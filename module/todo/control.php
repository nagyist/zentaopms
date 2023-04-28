<?php
declare(strict_types=1);

/**
 * The control file of example module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Lanzongjun <lanzongjun@easycorp.ltd>
 * @package     todo
 * @link        https://www.zentao.net
 */
class todo extends control
{
    /**
     * Construct function, load model of task, bug, my.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->app->loadClass('date');
        $this->loadModel('task');
        $this->loadModel('bug');
        $this->app->loadLang('my');
    }

    /**
     * 创建待办
     * Create a todo.
     *
     * @param  string  $date
     * @param  string  $from todo|feedback|block
     * @access public
     * @return int|void
     */
    public function create(string $date = 'today', string $from = 'todo')
    {
        if($date == 'today') $date = date::today();

        if(!empty($_POST))
        {
            $formData = form::data($this->config->todo->create->form);
            $todo     = $this->todoZen->beforeCreate($formData);

            $todoID = $this->todo->create($todo);
            if($todoID === false) return print(js::error(dao::getError()));

            $todo->id = $todoID;
            $this->todoZen->afterCreate($todo);

            if(!empty($_POST['objectID'])) return $this->send(array('result' => 'success'));

            if($from == 'block')
            {
                $todo = $this->todo->getById($todoID);
                $todo->begin = date::formatTime($todo->begin);
                return $this->send(array('result' => 'success', 'id' => $todoID, 'name' => $todo->name, 'pri' => $todo->pri, 'priName' => $this->lang->todo->priList[$todo->pri], 'time' => date(DT_DATE4, strtotime($todo->date)) . ' ' . $todo->begin));
            }

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $todoID));
            if($this->viewType == 'xhtml') return print(js::locate($this->createLink('todo', 'view', "todoID=$todoID", 'html'), 'parent'));
            if(isonlybody()) return print(js::closeModal('parent.parent'));
            return print(js::locate($this->createLink('my', 'todo', "type=all&userID=&status=all&orderBy=id_desc"), 'parent'));
        }

        unset($this->lang->todo->typeList['cycle']);

        $this->view->title = $this->lang->todo->common . $this->lang->colon . $this->lang->todo->create;
        $this->view->date  = date('Y-m-d', strtotime($date));
        $this->view->times = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->time  = date::now();
        $this->view->users = $this->loadModel('user')->getPairs('noclosed|nodeleted|noempty');
        $this->display();
    }

    /**
     * Batch create todo
     *
     * @param  string $date
     * @access public
     * @return void
     */
    public function batchCreate($date = 'today')
    {
        if($date == 'today') $date = date(DT_DATE1, time());
        if(!empty($_POST))
        {
            $todoIDList = $this->todo->batchCreate();
            if(dao::isError()) return print(js::error(dao::getError()));

            /* Locate the browser. */
            $date = str_replace('-', '', $this->post->date);
            if($date == '')
            {
                $date = 'future';
            }
            else if($date == date('Ymd'))
            {
                $date= 'today';
            }

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'idList' => $todoIDList));
            if(isonlybody()) return print(js::reload('parent.parent'));
            return print(js::locate($this->createLink('my', 'todo', "type=$date"), 'parent'));
        }

        unset($this->lang->todo->typeList['cycle']);

        /* Set Custom*/
        foreach(explode(',', $this->config->todo->list->customBatchCreateFields) as $field) $customFields[$field] = $this->lang->todo->$field;

        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->todo->custom->batchCreateFields;

        $this->view->title      = $this->lang->todo->common . $this->lang->colon . $this->lang->todo->batchCreate;
        $this->view->position[] = $this->lang->todo->common;
        $this->view->position[] = $this->lang->todo->batchCreate;
        $this->view->date       = (int)$date == 0 ? $date : date('Y-m-d', strtotime($date));
        $this->view->times      = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->time       = date::now();
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|nodeleted|noempty');

        $this->display();
    }

    /**
     * 编辑待办数据
     * Edit a todo.
     *
     * @param  string $todoID
     * @access public
     * @return void
     */
    public function edit(string $todoID)
    {
        if(!empty($_POST))
        {
            $formData = form::data($this->config->todo->edit->form);
            $todoID   = (int)$todoID;

            $todo = $this->todoZen->beforeEdit($todoID, $formData);
            if(dao::isError())
            {
                if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'fail', 'message' => dao::getError()));
                return print(js::error(dao::getError()));
            }

            $changes = $this->todo->update($todoID, $todo);
            if(dao::isError())
            {
                if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'fail', 'message' => dao::getError()));
                return print(js::error(dao::getError()));
            }

            $this->todoZen->afterEdit($todoID, $changes);

            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success'));
            return print(js::locate($this->session->todoList, 'parent.parent'));
        }

        /* Judge a private todo or not, If private, die. */
        $todo = $this->todo->getById($todoID);
        if($todo->private and $this->app->user->account != $todo->account) return print('private');

        unset($this->lang->todo->typeList['cycle']);

        $todo->date = date("Y-m-d", strtotime($todo->date));
        $this->view->title      = $this->lang->todo->common . $this->lang->colon . $this->lang->todo->edit;
        $this->view->times      = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->todo       = $todo;
        $this->view->users      = $this->loadModel('user')->getPairs('noclosed|nodeleted|noempty');

        $this->display();
    }

    /**
     * Batch edit todo.
     *
     * @param  string $from example:myTodo, todoBatchEdit.
     * @param  string $type
     * @param  int    $userID
     * @param  string $status
     * @access public
     * @return void
     */
    public function batchEdit($from = '', $type = 'today', $userID = '', $status = 'all')
    {
        /* Get form data for my-todo. */
        if($from == 'myTodo')
        {
            /* Initialize vars. */
            $editedTodos = array();
            $todoIDList  = array();
            $columns     = 7;

            if($userID == '') $userID = $this->app->user->id;
            $user    = $this->loadModel('user')->getById($userID, 'id');
            $account = $user->account;

            $reviews = array();
            if($this->config->edition == 'max') $reviews = $this->loadModel('review')->getUserReviewPairs($account);
            $allTodos = $this->todo->getList($type, $account, $status);
            if($this->post->todoIDList) $todoIDList = $this->post->todoIDList;

            /* Initialize todos whose need to edited. */
            foreach($allTodos as $todo)
            {
                if(in_array($todo->id, $todoIDList))
                {
                    $editedTodos[$todo->id] = $todo;
                    if($todo->type != 'custom')
                    {
                        if(!isset($objectIDList[$todo->type])) $objectIDList[$todo->type] = array();
                        $objectIDList[$todo->type][$todo->objectID] = $todo->objectID;
                    }
                }
            }

            $bugs   = $this->bug->getUserBugPairs($account, true, 0, '', '', isset($objectIDList['bug']) ? $objectIDList['bug'] : '');
            $tasks  = $this->task->getUserTaskPairs($account, 'wait,doing', '', isset($objectIDList['task']) ? $objectIDList['task'] : '');
            $storys = $this->loadModel('story')->getUserStoryPairs($account, 10, 'story', '', isset($objectIDList['story']) ? $objectIDList['story'] : '');
            if($this->config->edition != 'open') $this->view->feedbacks = $this->loadModel('feedback')->getUserFeedbackPairs($account, '', isset($objectIDList['feedback']) ? $objectIDList['feedback'] : '');
            if($this->config->edition == 'max')
            {
                $issues        = $this->loadModel('issue')->getUserIssuePairs($account);
                $risks         = $this->loadmodel('risk')->getUserRiskPairs($account);
                $opportunities = $this->loadmodel('opportunity')->getUserOpportunityPairs($account);
            }
            $testtasks = $this->loadModel('testtask')->getUserTestTaskPairs($account);

            /* Judge whether the edited todos is too large. */
            $countInputVars  = count($editedTodos) * $columns;
            $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);

            unset($this->lang->todo->typeList['cycle']);
            /* Set Custom*/
            foreach(explode(',', $this->config->todo->list->customBatchEditFields) as $field) $customFields[$field] = $this->lang->todo->$field;
            $this->view->customFields = $customFields;
            $this->view->showFields   = $this->config->todo->custom->batchEditFields;

            /* Assign. */
            $title      = $this->lang->todo->common . $this->lang->colon . $this->lang->todo->batchEdit;
            $position[] = html::a($this->createLink('my', 'todo'), $this->lang->my->todo);
            $position[] = $this->lang->todo->common;
            $position[] = $this->lang->todo->batchEdit;

            if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);
            $this->view->bugs        = $bugs;
            $this->view->tasks       = $tasks;
            $this->view->storys      = $storys;
            if($this->config->edition == 'max')
            {
                $this->view->issues        = $issues;
                $this->view->risks         = $risks;
                $this->view->opportunities = $opportunities;
            }
            $this->view->reviews     = $reviews;
            $this->view->testtasks   = $testtasks;
            $this->view->editedTodos = $editedTodos;
            $this->view->times       = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
            $this->view->time        = date::now();
            $this->view->title       = $title;
            $this->view->position    = $position;
            $this->view->users       = $this->loadModel('user')->getPairs('noclosed|nodeleted|noempty');

            $this->display();
        }
        /* Get form data from todo-batchEdit. */
        elseif($from == 'todoBatchEdit')
        {
            $allChanges = $this->todo->batchUpdate();
            foreach($allChanges as $todoID => $changes)
            {
                if(empty($changes)) continue;

                $actionID = $this->loadModel('action')->create('todo', $todoID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }

            return print(js::locate($this->session->todoList, 'parent'));
        }
    }

    /**
     * 开启一个待办事项
     * Start a todo.
     *
     * @param  string $todoID
     * @access public
     * @return void
     */
    public function start(string $todoID)
    {
        $todoID = (int)$todoID;
        $todo   = $this->todo->getById($todoID);

        if($todo->status == 'wait') $this->todo->start($todoID);
        if(in_array($todo->type, array('bug', 'task', 'story'))) return $this->todoZen->printConfirm($todo);
        if(isonlybody()) return print(js::reload('parent.parent'));

        echo js::reload('parent');
    }

    /**
     * 激活待办事项
     * Activated todo.
     *
     * @param  string $todoID
     * @access public
     * @return void
     */
    public function activate(string $todoID)
    {
        $todoID = (int)$todoID;
        $todo   = $this->todo->getById($todoID);

        if($todo->status == 'done' or $todo->status == 'closed') $this->todo->activate($todoID);
        if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success'));
        if(isonlybody()) return print(js::reload('parent.parent'));

        echo js::reload('parent');
    }

    /**
     * Closed todo.
     *
     * @param  $todoID
     *
     * @access public
     * @return void
     */
    public function close($todoID)
    {
        $todo = $this->todo->getById($todoID);
        if($todo->status == 'done') $this->todo->close($todoID);
        if(isonlybody()) return print(js::reload('parent.parent'));
        echo js::reload('parent');
    }

    /**
     * Assign.
     *
     * @param $todoID
     *
     * @access public
     * @return void
     */
    public function assignTo($todoID)
    {
        if(!empty($_POST))
        {
            if(empty($_POST['assignedTo'])) return print(js::error($this->lang->todo->noAssignedTo));
            $this->todo->assignTo($todoID);
            if(dao::isError()) return print(js::error(dao::getError()));
            return print(js::reload('parent.parent'));
        }

        $this->view->todo    = $this->todo->getById($todoID);
        $this->view->members = $this->loadModel('user')->getPairs('noclosed|noempty|nodeleted');
        $this->view->times   = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->actions = $this->loadModel('action')->getList('todo', $todoID);
        $this->view->users   = $this->user->getPairs('noletter');
        $this->view->time    = date::now();
        $this->display();
    }

    /**
     * 获取待办的信息.
     * Get info of todo .
     *
     * @param string $todoID
     * @param string $from   my|company
     *
     * @access public
     * @return void
     */
    public function view(string $todoID,string $from = 'company')
    {
        $todo = $this->todo->getById((int)$todoID, true);

        if(!$todo)
        {
            if((defined('RUN_MODE') && RUN_MODE == 'api') or $this->app->viewType == 'json') return $this->send(array('status' => 'fail', 'message' => '404 Not found'));
            return print(js::error((string)$this->lang->notFound) . (string)js::locate('back'));
        }

        $account = $this->app->user->account;
        if($todo->private and $todo->account != $account) return print(js::error((string)$this->lang->todo->thisIsPrivate) . (string)js::locate('back'));

        /* Save the session. */
        if(!isonlybody())
        {
            $url = $this->app->getURI(true);
            $this->session->set('bugList',      $url, 'qa');
            $this->session->set('taskList',     $url, 'execution');
            $this->session->set('storyList',    $url, 'product');
            $this->session->set('testtaskList', $url, 'qa');
        }

        /* Fix bug #936. */
        if($account != $todo->account and $account != $todo->assignedTo and !common::hasPriv('my', 'team'))
        {
            $this->locate($this->createLink('user', 'deny', "module=my&method=team"));
        }

        $projects = $this->todoZen->getProjectPairsByModel((string)$todo->type);
        if(!isset($this->session->project)) $this->session->set('project', (int)key($projects));

        $this->view->title           = $account == $todo->account ? "{$this->lang->todo->common} #$todo->id $todo->name" : $this->lang->todo->common;
        $this->view->position[]      = $this->lang->todo->view;
        $this->view->todo            = $todo;
        $this->view->times           = date::buildTimeList((int)$this->config->todo->times->begin, (int)$this->config->todo->times->end, 5);
        $this->view->from            = $from;
        $this->view->projects        = $projects;

        $this->todoZen->buildAssignToTodo((object)$todo, (int)$this->session->project);
        $this->display();
    }

    /**
     * Delete a todo.
     *
     * @param  int    $todoID
     * @param  string $confirm yes|no
     * @access public
     * @return void
     */
    public function delete($todoID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->todo->confirmDelete, $this->createLink('todo', 'delete', "todoID=$todoID&confirm=yes")));
        }
        else
        {
            $this->todo->delete(TABLE_TODO, $todoID);

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                return $this->send($response);
            }

            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success'));
            if(isonlybody()) return print(js::reload('parent.parent'));

            $browseLink = $this->session->todoList ? $this->session->todoList : $this->createLink('my', 'todo');
            return print(js::locate($browseLink, 'parent'));
        }
    }

    /**
     * Finish a todo.
     *
     * @param  int    $todoID
     * @access public
     * @return void
     */
    public function finish($todoID)
    {
        $todo = $this->todo->getById($todoID);
        if($todo->status != 'done' && $todo->status != 'closed') $this->todo->finish($todoID);
        if(in_array($todo->type, array('bug', 'task', 'story')))
        {
            $confirmNote = 'confirm' . ucfirst($todo->type);
            $okTarget    = isonlybody() ? 'parent' : 'window.parent.$.apps.open';
            $confirmURL  = $this->createLink($todo->type, 'view', "id=$todo->objectID");
            if($todo->type == 'bug')   $app = 'qa';
            if($todo->type == 'task')  $app = 'execution';
            if($todo->type == 'story') $app = 'product';
            $cancelURL   = $this->server->HTTP_REFERER;
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success', 'message' => sprintf($this->lang->todo->$confirmNote, $todo->objectID), 'locate' => $confirmURL));
            return print(strpos($cancelURL, 'calendar') ? json_encode(array(sprintf($this->lang->todo->$confirmNote, $todo->objectID), $confirmURL)) : js::confirm(sprintf($this->lang->todo->$confirmNote, $todo->objectID), $confirmURL, $cancelURL, $okTarget, 'parent', $app));
        }
        if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success'));
        if(isonlybody()) return print(js::reload('parent.parent'));
        echo js::reload('parent');
    }

    /**
     * Batch finish todos.
     *
     * @access public
     * @return void
     */
    public function batchFinish()
    {
        if(!empty($_POST['todoIDList']))
        {
            foreach($_POST['todoIDList'] as $todoID)
            {
                $todo = $this->todo->getById($todoID);
                if($todo->status != 'done' && $todo->status != 'closed') $this->todo->finish($todoID);
            }
            return print(js::reload('parent'));
        }
    }

    /**
     * Batch close todos. The status of todo which need to close should be done.
     *
     * @access public
     * @return void
     */
    public function batchClose(): void
    {
        $waitIdList = array();
        $todoIdlist = form::data($this->config->todo->batchClose->form)->get('todoIDList');
        foreach($todoIdlist as $todoID)
        {
            $todoID = (int) $todoID;
            $todo   = $this->todo->getById($todoID);
            if($todo->status == 'done') $this->todo->close($todoID);
            if($todo->status != 'done' and $todo->status != 'closed') $waitIdList[] = $todoID;
        }
        if(!empty($waitIdList)) echo js::alert(sprintf($this->lang->todo->unfinishedTodo, implode(',', $waitIdList)));

        echo js::reload('parent');
    }

    /**
     * 修改选中待办的日期。
     * Import selected todoes to today.
     *
     * @param  string $todoID
     * @access public
     * @return void
     */
    public function import2Today(string $todoID = '')
    {
        $todoIDList = $_POST ? $this->post->todoIDList : array($todoID);
        $date   = !empty($_POST['date']) ? $_POST['date'] : date::today();
        if(!$date || !$todoIDList) $this->locate((string)$this->session->todoList);

        $this->todo->editDate((array)$todoIDList, (string)$date);
        $this->locate((string)$this->session->todoList);
    }

    /**
     * Get data to export
     *
     * @param  int    $userID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function export($userID, $orderBy)
    {
        if($_POST)
        {
            $user    = $this->loadModel('user')->getById($userID, 'id');
            $account = $user->account;

            $todoLang   = $this->lang->todo;
            $todoConfig = $this->config->todo;

            /* Create field lists. */
            $fields = explode(',', $todoConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($todoLang->$fieldName) ? $todoLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }
            unset($fields['objectID']);
            unset($fields['private']);

            /* Get bugs. */
            $todos = $this->dao->select('*')->from(TABLE_TODO)->where($this->session->todoReportCondition)
                ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                ->orderBy($orderBy)->fetchAll('id');

            /* Get users, bugs, tasks and times. */
            $users     = $this->loadModel('user')->getPairs('noletter');
            $bugs      = $this->loadModel('bug')->getUserBugPairs($account);
            $stories   = $this->loadModel('story')->getUserStoryPairs($account, 100, 'story');
            $tasks     = $this->loadModel('task')->getUserTaskPairs($account);
            if($this->config->edition == 'max')
            {
                $issues        = $this->loadModel('issue')->getUserIssuePairs($account);
                $risks         = $this->loadModel('risk')->getUserRiskPairs($account);
                $opportunities = $this->loadModel('opportunity')->getUserOpportunityPairs($account);
            }
            $testTasks = $this->loadModel('testtask')->getUserTesttaskPairs($account);
            if(isset($this->config->qcVersion)) $reviews = $this->loadModel('review')->getUserReviewPairs($account, 0, 'wait');
            $times = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);

            foreach($todos as $todo)
            {
                /* fill some field with useful value. */
                $todo->begin = $todo->begin == '2400' ? '' : (isset($times[$todo->begin]) ? $times[$todo->begin] : $todo->begin);
                $todo->end   = $todo->end   == '2400' ? '' : (isset($times[$todo->end])   ? $times[$todo->end] : $todo->end);

                $type = $todo->type;
                if(isset($users[$todo->account])) $todo->account = $users[$todo->account];
                if($type == 'bug')                $todo->name    = isset($bugs[$todo->objectID])    ? $bugs[$todo->objectID] . "(#$todo->objectID)" : '';
                if($type == 'story')              $todo->name    = isset($stories[$todo->objectID]) ? $stories[$todo->objectID] . "(#$todo->objectID)" : '';
                if($type == 'task')               $todo->name    = isset($tasks[$todo->objectID])   ? $tasks[$todo->objectID] . "(#$todo->objectID)" : '';

                if($this->config->edition == 'max')
                {
                    if($type == 'issue') $todo->name = isset($issues[$todo->objectID]) ? $issues[$todo->objectID] . "(#$todo->objectID)" : '';
                    if($type == 'risk')  $todo->name = isset($risks[$todo->objectID])  ? $risks[$todo->objectID] . "(#$todo->objectID)" : '';
                    if($type == 'opportunity')  $todo->name = isset($opportunities[$todo->objectID])  ? $opportunities[$todo->objectID] . "(#$todo->objectID)" : '';
                }
                if($type == 'testtask')           $todo->name    = isset($testTasks[$todo->objectID]) ? $testTasks[$todo->objectID] . "(#$todo->objectID)" : '';
                if($type == 'review' && isset($this->config->qcVersion)) $todo->name = isset($reviews[$todo->objectID]) ? $reviews[$todo->objectID] . "(#$todo->objectID)" : '';

                if(isset($todoLang->typeList[$type]))           $todo->type    = $todoLang->typeList[$type];
                if(isset($todoLang->priList[$todo->pri]))       $todo->pri     = $todoLang->priList[$todo->pri];
                if(isset($todoLang->statusList[$todo->status])) $todo->status  = $todoLang->statusList[$todo->status];
                if($todo->private == 1)                         $todo->desc    = $this->lang->todo->thisIsPrivate;

                /* drop some field that is not needed. */
                unset($todo->objectID);
                unset($todo->private);
            }
            if($this->config->edition != 'open') list($fields, $todos) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $todos);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $todos);
            $this->post->set('kind', 'todo');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $this->view->fileName = $this->app->user->account . ' - ' . $this->lang->todo->common;
        $this->display();
    }

    /**
     * AJAX: get actions of a todo. for web app.
     *
     * @param  int    $todoID
     * @access public
     * @return void
     */
    public function ajaxGetDetail($todoID)
    {
        $this->view->actions = $this->loadModel('action')->getList('todo', $todoID);
        $this->display();
    }

    /**
     * AJAX: get program id.
     *
     * @param  int     $objectID
     * @param  string  $objectType
     * @access public
     * @return void
     */
    public function ajaxGetProgramID($objectID, $objectType)
    {
        $table = $objectType == 'project' ? TABLE_PROJECT : TABLE_PRODUCT;
        $field = $objectType == 'project' ? 'parent' : 'program';
        echo $this->dao->select($field)->from($table)->where('id')->eq($objectID)->fetch($field);
    }

    /**
     * AJAX: get execution pairs.
     *
     * @param  int     $projectID
     * @access public
     * @return void
     */
    public function ajaxGetExecutionPairs($projectID)
    {
        $this->session->set('project', $projectID);

        $project    = $this->loadModel('project')->getByID($projectID);
        $executions = $this->loadModel('execution')->getByProject($projectID, 'undone');
        foreach($executions as $id => $execution) $executions[$id] = $execution->name;

        echo html::select('execution', $executions, '', "class='form-control chosen'");
        echo "<script>toggleExecution({$project->multiple});</script>";
    }

    /**
     * AJAX: get product pairs.
     *
     * @param  int     $projectID
     * @access public
     * @return void
     */
    public function ajaxGetProductPairs($projectID)
    {
        $this->session->set('project', $projectID);

        $products = $this->loadModel('product')->getProductPairsByProject($projectID);
        echo html::select('bugProduct', $products, '', "class='form-control chosen'");
    }

    /**
     * Create cycle.
     *
     * @access public
     * @return void
     */
    public function createCycle()
    {
        $todoList = $this->dao->select('*')->from(TABLE_TODO)->where('cycle')->eq(1)->andWhere('deleted')->eq(0)->fetchAll('id');
        $this->todo->createByCycle($todoList);
    }
}
