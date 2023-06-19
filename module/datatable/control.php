<?php
/**
 * The view file of datatable module of ZenTaoPMS.
 *
 * @copyright   Copyright 2014-2014 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Hao sun <sunhao@cnezsoft.com>
 * @package     datatable
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class datatable extends control
{
    /**
     * Construct function, set menu.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set display.
     *
     * @param  string $datatableId
     * @param  string $moduleName
     * @param  string $methodName
     * @param  string $currentModule
     * @param  string $currentMethod
     * @param  string $branchType
     * @access public
     * @return void
     */
    public function ajaxDisplay(string $datatableId, string $moduleName, string $methodName, string $currentModule, string $currentMethod, string $branchType)
    {
        if($branchType) $this->lang->datatable->showBranch = sprintf($this->lang->datatable->showBranch, isset($this->lang->datatable->$branchType) ? $this->lang->datatable->$branchType : $this->lang->datatable->branch);

        $this->app->loadConfig($currentModule);

        $this->view->datatableId   = $datatableId;
        $this->view->moduleName    = $moduleName;
        $this->view->methodName    = $methodName;
        $this->view->currentModule = $currentModule;
        $this->view->currentMethod = $currentMethod;
        $this->view->isShowBranch  = $branchType && $branchType != 'normal';
        $this->view->showBranch    = isset($this->config->$currentModule->$currentMethod->showBranch) ? $this->config->$currentModule->$currentMethod->showBranch : 1;
        $this->render();
    }

    /**
     * Save config
     *
     * @access public
     * @return void
     */
    public function ajaxSave()
    {
        if(!empty($_POST))
        {
            $account = $this->app->user->account;
            if($account == 'guest') return $this->send(array('result' => 'fail', 'target' => $target, 'message' => 'guest.'));

            $this->loadModel('setting')->setItem($account . '.' . $this->post->currentModule . '.' . $this->post->currentMethod . '.showModule', $this->post->value);
            if($this->post->allModule !== false)  $this->setting->setItem("$account.execution.task.allModule", $this->post->allModule);
            if($this->post->showBranch !== false) $this->setting->setItem($account . '.' . $this->post->currentModule . '.' . $this->post->currentMethod . '.showBranch', $this->post->showBranch);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => 'dao error.'));
            return $this->send(array('result' => 'success', 'closeModal' => true, 'load' => true));
        }
    }

    /**
     * Ajax save fields.
     *
     * @param  string $module
     * @param  string $method
     * @access public
     * @return void
     */
    public function ajaxSaveFields(string $module, string $method)
    {
        if(!empty($_POST))
        {
            $account = $this->app->user->account;
            if($account == 'guest') return $this->send(array('result' => 'fail', 'message' => 'guest.'));

            $rawModule = zget($this->config->datatable->moduleAlias, "$module-$method", $module);
            $fieldList = $this->datatable->getFieldList($rawModule, $method);
            $fieldList = $this->datatable->formatFields($module, $fieldList, false);
            $fields    = json_decode($this->post->fields);
            foreach($fields as $index => $field)
            {
                $id = $field->id;
                if(!isset($fieldList[$id])) continue;

                $fieldList[$id]['order'] = $field->order;
                $fieldList[$id]['width'] = $field->width;
                $fieldList[$id]['show']  = $field->show ? true : false;
            }

            $name  = 'datatable.' . $module . ucfirst($method) . '.cols';
            $value = json_encode($fieldList);
            $this->loadModel('setting')->setItem($account . '.' . $name, $value);
            if($this->post->global) $this->setting->setItem('system.' . $name, $value);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => 'dao error.'));
            return $this->send(array('result' => 'success', 'closeModal' => true, 'load' => true));
        }
    }

    /**
     * custom fields.
     *
     * @param  string $module
     * @param  string $method
     * @param  string $extra
     * @access public
     * @return void
     */
    public function ajaxCustom($module, $method, $extra = '')
    {
        $moduleName = $module;
        $target     = $module . ucfirst($method);

        $this->view->module = $module;

        if($module == 'testtask')
        {
            $this->loadModel('testcase');
            $this->app->loadConfig('testtask');
            $this->config->testcase->dtable->defaultField = $this->config->testtask->dtable->defaultField;
            $this->config->testcase->dtable->fieldList['actions']['width'] = '100';
            $this->config->testcase->dtable->fieldList['status']['width']  = '90';
        }
        if($module == 'testcase')
        {
            $this->loadModel('testcase');
            unset($this->config->testcase->dtable->fieldList['assignedTo']);
        }

        $module  = zget($this->config->datatable->moduleAlias, "$module-$method", $module);
        $setting = '';
        if(isset($this->config->datatable->$target->cols)) $setting = $this->config->datatable->$target->cols;

        if(empty($setting))
        {
            $cols = $this->datatable->getFieldList($module, $method);
            $cols = $this->datatable->formatFields($module, $cols, false);
        }
        else
        {
            $cols = json_decode($setting, true);
        }

        usort($cols, array('datatableModel', 'sortCols'));

        if($module == 'story' && $extra != 'requirement') unset($cols['SRS']);

        if($extra == 'requirement')
        {
            unset($cols['plan']);
            unset($cols['stage']);
            unset($cols['taskCount']);
            unset($cols['bugCount']);
            unset($cols['caseCount']);
            unset($cols['URS']);

            $cols['title']['title'] = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->title);
        }

        if($moduleName == 'project' and $method == 'bug')
        {
            $project = $this->loadModel('project')->getByID($this->session->project);

            if(!$project->multiple) unset($cols['execution']);
            if(!$project->hasProduct and $project->model != 'scrum') unset($cols['plan']);
            if(!$project->hasProduct) unset($cols['branch']);
        }

        if($moduleName == 'execution' and $method == 'bug')
        {
            $execution = $this->loadModel('execution')->getByID($this->session->execution);
            $project   = $this->loadModel('project')->getByID($execution->project);
            if(!$project->hasProduct and $project->model != 'scrum') unset($cols['plan']);
            if(!$project->hasProduct) unset($cols['branch']);
        }

        if($moduleName == 'execution' and $method == 'story')
        {
            $execution = $this->loadModel('execution')->getByID($this->session->execution);
            if(!$execution->hasProduct and !$execution->multiple) unset($cols['plan']);
            if(!$execution->hasProduct) unset($cols['branch']);
        }
        if($extra == 'unsetStory' and isset($cols['story'])) unset($cols['story']);

        $this->view->method = $method;
        $this->view->cols   = $cols;
        $this->display();
    }

    /**
     * Ajax reset cols
     *
     * @param  string $module
     * @param  string $method
     * @param  string $confirm
     * @access public
     * @return void
     */
    public function ajaxReset($module, $method, $system = 0, $confirm = 'no')
    {
        if($confirm == 'no') return print(js::confirm($this->lang->datatable->confirmReset, inlink('ajaxReset', "module=$module&method=$method&system=$system&confirm=yes")));

        $account = $this->app->user->account;
        $target  = $module . ucfirst($method);
        $mode    = isset($this->config->datatable->$target->mode) ? $this->config->datatable->$target->mode : 'table';
        $key     = $mode == 'datatable' ? 'cols' : 'tablecols';

        $this->loadModel('setting')->deleteItems("owner=$account&module=datatable&section=$target&key=$key");
        if($system) $this->setting->deleteItems("owner=system&module=datatable&section=$target&key=$key");
        return print(js::reload('parent'));
    }
}
