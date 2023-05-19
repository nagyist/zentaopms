<?php
$now = helper::now();

$config->task->form = new stdclass();
$config->task->form->team = new stdclass();
$config->task->form->testTask = new stdclass();

global $app;
$config->task->form->create = array();
$config->task->form->create['execution']    = array('type' => 'int', 'required' => true);
$config->task->form->create['type']         = array('type' => 'string', 'required' => true, 'default' => '');
$config->task->form->create['module']       = array('type' => 'int', 'required' => false);
$config->task->form->create['story']        = array('type' => 'int', 'required' => false);
$config->task->form->create['mode']         = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->create['color']        = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->create['name']         = array('type' => 'string', 'required' => true, 'default' => '');
$config->task->form->create['pri']          = array('type' => 'int', 'required' => false, 'default' => $config->task->default->pri);
$config->task->form->create['estimate']     = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->create['desc']         = array('type' => 'string', 'required' => false, 'control' => 'editor');
$config->task->form->create['estStarted']   = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->create['deadline']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->create['vision']       = array('type' => 'string', 'required' => false, 'default' => $config->vision);
$config->task->form->create['status']       = array('type' => 'string', 'required' => false, 'default' => 'wait');
$config->task->form->create['openedBy']     = array('type' => 'string', 'required' => false, 'default' => $app->user->account);
$config->task->form->create['openedDate']   = array('type' => 'string', 'required' => false, 'default' => $now);
$config->task->form->create['mailto']       = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->create['version']      = array('type' => 'int', 'required' => false, 'default' => 1);
$config->task->form->create['uid']          = array('type' => 'int', 'required' => false, 'default' => 0);
$config->task->form->create['mailto']       = array('type' => 'array', 'required' => false, 'default' => '', 'filter' => 'join');

$config->task->form->assign = array();
$config->task->form->assign['assignedTo']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->assign['left']           = array('type' => 'float', 'required' => true);
$config->task->form->assign['lastEditedDate'] = array('type' => 'string', 'required' => false, 'default' => $now);
$config->task->form->assign['assignedDate']   = array('type' => 'string', 'required' => false, 'default' => $now);

$config->task->form->cancel = array();
$config->task->form->cancel['status']  = array('type' => 'string', 'required' => false, 'default' => 'cancel');
$config->task->form->cancel['comment'] = array('type' => 'string', 'required' => false, 'default' => '');

$config->task->form->manageTeam = array();
$config->task->form->manageTeam['status']         = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->manageTeam['estimate']       = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->manageTeam['left']           = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->manageTeam['consumed']       = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->manageTeam['lastEditedDate'] = array('type' => 'string', 'required' => false, 'default' => $now);
$config->task->form->manageTeam['assignedDate']   = array('type' => 'string', 'required' => false, 'default' => $now);

$config->task->form->edit = array();
$config->task->form->edit['name']           = array('type' => 'string', 'required' => true);
$config->task->form->edit['color']          = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['desc']           = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['execution']      = array('type' => 'int', 'required' => true);
$config->task->form->edit['story']          = array('type' => 'int', 'required' => false, 'default' => 0);
$config->task->form->edit['module']         = array('type' => 'int', 'required' => false, 'default' => 0);
$config->task->form->edit['parent']         = array('type' => 'int', 'required' => false, 'default' => 0);
$config->task->form->edit['mailto']         = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->edit['mode']           = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['assignedTo']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['type']           = array('type' => 'string', 'required' => true);
$config->task->form->edit['status']         = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['pri']            = array('type' => 'int', 'required' => false, 'default' => 0);
$config->task->form->edit['estStarted']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['deadline']       = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['estimate']       = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->edit['left']           = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->edit['consumed']       = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->edit['realStarted']    = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['finishedBy']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['finishedDate']   = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['canceledBy']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['canceledDate']   = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['closedBy']       = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['closedReason']   = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['closedDate']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->edit['lastEditedBy']   = array('type' => 'string', 'required' => false, 'default' => $app->user->account);
$config->task->form->edit['lastEditedDate'] = array('type' => 'string', 'required' => false, 'default' => $now);

$config->task->form->team->create = array();
$config->task->form->team->create['team']         = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->team->create['teamSource']   = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->team->create['teamEstimate'] = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->team->create['teamConsumed'] = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->team->create['teamLeft']     = array('type' => 'array', 'required' => false, 'default' => array());

$config->task->form->team->edit = $config->task->form->team->create;
$config->task->form->team->edit['deleteFiles']  = array('type' => 'array', 'required' => false, 'default' => array());

$config->task->form->batchEdit = array();
$config->task->form->batchEdit['taskIDList']    = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['modules']       = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['names']         = array('type' => 'array', 'required' => true, 'default' => array());
$config->task->form->batchEdit['colors']        = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['types']         = array('type' => 'array', 'required' => true, 'default' => array());
$config->task->form->batchEdit['statuses']      = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['assignedTos']   = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['estimates']     = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['estStarteds']   = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['consumeds']     = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['lefts']         = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['finishedBys']   = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['canceledBys']   = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['closedBys']     = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['closedReasons'] = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['deadlines']     = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchEdit['pris']          = array('type' => 'array', 'required' => false, 'default' => array());

$config->task->form->batchCreate = array();
$config->task->form->batchCreate['module']        = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['parent']        = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['story']         = array('type' => 'array', 'required' => true,  'default' => array());
$config->task->form->batchCreate['storyEstimate'] = array('type' => 'array', 'required' => true,  'default' => array());
$config->task->form->batchCreate['storyDesc']     = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['storyPri']      = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['name']          = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['color']         = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['type']          = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['assignedTo']    = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['estimate']      = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['estStarted']    = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['deadline']      = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['desc']          = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->batchCreate['pri']           = array('type' => 'array', 'required' => false, 'default' => array());

$config->task->form->pause = array();
$config->task->form->pause['lastEditedBy']   = array('type' => 'string', 'required' => false, 'default' => $app->user->account);
$config->task->form->pause['lastEditedDate'] = array('type' => 'string', 'required' => false, 'default' => $now);
$config->task->form->pause['status']         = array('type' => 'string', 'required' => false, 'default' => 'pause');

$config->task->form->activate = array();
$config->task->form->activate['mode']       = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->activate['left']       = array('type' => 'float', 'required' => true, 'default' => 0);
$config->task->form->activate['assignedTo'] = array('type' => 'string', 'required' => true);

$config->task->form->start = array();
$config->task->form->start['status']         = array('type' => 'string', 'required' => false, 'default' => 'doing');
$config->task->form->start['consumed']       = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->start['left']           = array('type' => 'float', 'required' => false, 'default' => 0);
$config->task->form->start['assignedTo']     = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->start['comment']        = array('type' => 'string', 'required' => false, 'control' => 'editor');
$config->task->form->start['realStarted']    = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->start['lastEditedBy']   = array('type' => 'string', 'required' => false, 'default' => $app->user->account);
$config->task->form->start['lastEditedDate'] = array('type' => 'string', 'required' => false, 'default' => $now);

$config->task->form->finish = array();
$config->task->form->finish['realStarted']     = array('type' => 'string', 'required' => true);
$config->task->form->finish['left']            = array('type' => 'float',  'required' => false, 'default' => 0);
$config->task->form->finish['consumed']        = array('type' => 'float',  'required' => false, 'default' => 0);
$config->task->form->finish['currentConsumed'] = array('type' => 'float',  'required' => false, 'default' => 0);
$config->task->form->finish['assignedTo']      = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->finish['comment']         = array('type' => 'string', 'required' => false, 'default' => '');
$config->task->form->finish['status']          = array('type' => 'string', 'required' => false, 'default' => 'done');
$config->task->form->finish['finishedDate']    = array('type' => 'string', 'required' => false, 'default' => $now);
$config->task->form->finish['lastEditedDate']  = array('type' => 'string', 'required' => false, 'default' => $now);
$config->task->form->finish['assignedDate']    = array('type' => 'string', 'required' => false, 'default' => $now);
$config->task->form->finish['finishedBy']      = array('type' => 'string', 'required' => false, 'default' => $app->user->account);
$config->task->form->finish['lastEditedBy']    = array('type' => 'string', 'required' => false, 'default' => $app->user->account);

$config->task->form->testTask->create = array();
$config->task->form->testTask->create['testStory']       = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->testTask->create['testEstStarted']  = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->testTask->create['testDeadline']    = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->testTask->create['testAssignedTo']  = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->testTask->create['testPri']         = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->testTask->create['testEstimate']    = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->testTask->create['estStartedDitto'] = array('type' => 'array', 'required' => false, 'default' => array());
$config->task->form->testTask->create['deadlineDitto']   = array('type' => 'array', 'required' => false, 'default' => array());
