<?php
/**
 * The control file of entry module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2017 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     entry
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class entry extends control
{
    /**
     * 接入禅道列表页面。
     * Browse entries.
     *
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function browse(string $orderBy = 'id_desc', int $recTotal = 0, int $recPerPage = 10, int $pageID = 1)
    {
        $pager = $this->app->loadClass('pager', true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $this->view->title   = $this->lang->entry->common . $this->lang->colon . $this->lang->entry->list;
        $this->view->entries = $this->entry->getList($orderBy, $pager);
        $this->view->orderBy = $orderBy;
        $this->view->pager   = $pager;
        $this->display();
    }

    /**
     * Create an entry.
     *
     * @access public
     * @return void
     */
    public function create()
    {
        if($_POST)
        {
            $entry = form::data($this->config->entry->form->create)
                ->setIF($this->post->allIP === 'on', 'ip', '*')
                ->setIF($this->post->freePasswd == '1', 'account', '')
                ->add('createdBy', $this->app->user->account)
                ->add('createdDate', helper::now())
                ->remove('allIP')
                ->get();

            $id = $this->entry->create($entry);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('entry', $id, 'created');
            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $id));
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse')));
        }

        $this->view->title      = $this->lang->entry->common . $this->lang->colon . $this->lang->entry->create;
        $this->view->users      = $this->loadModel('user')->getPairs('nodeleted|noclosed');
        $this->display();
    }

    /**
     * Edit an entry.
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function edit($id)
    {
        if($_POST)
        {
            $entry = form::data($this->config->entry->form->create)
                ->setIF($this->post->allIP === 'on', 'ip', '*')
                ->setIF($this->post->freePasswd == '1', 'account', '')
                ->add('createdBy', $this->app->user->account)
                ->add('createdDate', helper::now())
                ->remove('allIP')
                ->get();

            $changes = $this->entry->update($id, $entry);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($changes)
            {
                $actionID = $this->loadModel('action')->create('entry', $id, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse')));
        }

        $entry = $this->entry->getById($id);
        $this->view->title      = $this->lang->entry->edit . $this->lang->colon . $entry->name;
        $this->view->users      = $this->loadModel('user')->getPairs('nodeleted|noclosed');
        $this->view->entry      = $entry;
        $this->display();
    }

    /**
     * Delete an entry.
     *
     * @param  int    $id
     * @access public
     * @return void
     */
    public function delete($id)
    {
        $this->entry->delete(TABLE_ENTRY, $id);
        if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

        return $this->send(array('result' => 'success', 'load' => true));
    }

    /**
     * Browse logs of an entry.
     *
     * @param  int    $id
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function log($id, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $entry = $this->entry->getByID($id);
        $this->view->title      = $this->lang->entry->log . $this->lang->colon . $entry->name;
        $this->view->logs       = $this->entry->getLogs($id, $orderBy, $pager);
        $this->view->entry      = $entry;
        $this->view->orderBy    = $orderBy;
        $this->view->pager      = $pager;
        $this->display();
    }
}
