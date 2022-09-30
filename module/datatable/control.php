<?php
/**
 * The view file of datatable module of ZenTaoPMS.
 *
 * @copyright   Copyright 2014-2014 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
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

            $name = 'datatable.' . $this->post->target . '.' . $this->post->name;
            $this->loadModel('setting')->setItem($account . '.' . $name, $this->post->value);
            if($this->post->allModule !== false) $this->setting->setItem("$account.execution.task.allModule", $this->post->allModule);
            if($this->post->showBranch !== false) $this->setting->setItem($account . '.' . $this->post->currentModule . '.' . $this->post->currentMethod . '.showBranch', $this->post->showBranch);
            if($this->post->global) $this->setting->setItem('system.' . $name, $this->post->value);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => 'dao error.'));
            return $this->send(array('result' => 'success'));
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
        $target = $module . ucfirst($method);
        $mode   = isset($this->config->datatable->$target->mode) ? $this->config->datatable->$target->mode : 'table';
        $key    = $mode == 'datatable' ? 'cols' : 'tablecols';

        if($module == 'testtask')
        {
            $this->loadModel('testcase');
            $this->app->loadConfig('testtask');
            $this->config->testcase->datatable->defaultField = $this->config->testtask->datatable->defaultField;
            $this->config->testcase->datatable->fieldList['actions']['width'] = '100';
            $this->config->testcase->datatable->fieldList['status']['width']  = '90';
        }
        if($module == 'testcase')
        {
            $this->loadModel('testcase');
            unset($this->config->testcase->datatable->fieldList['assignedTo']);
        }

        $this->view->module = $module;
        $this->view->method = $method;
        $this->view->mode   = $mode;

        $module  = zget($this->config->datatable->moduleAlias, "$module-$method", $module);
        $setting = '';
        if(isset($this->config->datatable->$target->$key)) $setting = $this->config->datatable->$target->$key;
        if(empty($setting))
        {
            $this->loadModel($module);
            $setting = json_encode($this->config->$module->datatable->defaultField);
        }

        $cols = $this->datatable->getFieldList($module);
        if($extra == 'requirement')
        {
            unset($cols['plan']);
            unset($cols['stage']);
            unset($cols['taskCount']);
            unset($cols['bugCount']);
            unset($cols['caseCount']);
            $cols['title']['title'] = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->title);
        }

        $this->view->cols    = $cols;
        $this->view->setting = $setting;
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
