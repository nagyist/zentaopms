<?php
/**
 * The model file of programplan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     programplan
 * @version     $Id: model.php 5079 2013-07-10 00:44:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class programplanModel extends model
{
    /**
     * Set menu.
     *
     * @param  int  $projectID
     * @param  int  $productID
     * @access public
     * @return bool
     */
    public function setMenu($projectID, $productID)
    {
        return true;
    }

    /**
     * Get plan by id.
     *
     * @param  int    $planID
     * @access public
     * @return object
     */
    public function getByID($planID)
    {
        $plan = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($planID)->fetch();

        return $this->processPlan($plan);
    }

    /**
     * Get plans list.
     *
     * @param  int     $executionID
     * @param  int     $productID
     * @param  string  $browseType all|parent
     * @param  string  $orderBy
     * @access public
     * @return array
     */
    public function getStage($executionID = 0, $productID = 0, $browseType = 'all', $orderBy = 'id_asc')
    {
        if(empty($executionID)) return array();

        $plans = $this->dao->select('t1.*')->from(TABLE_EXECUTION)->alias('t1')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id = t2.project')
            ->where('t1.type')->eq('stage')
            ->beginIF($productID)->andWhere('t2.product')->eq($productID)->fi()
            ->beginIF($browseType == 'all')->andWhere('t1.project')->eq($executionID)->fi()
            ->beginIF($browseType == 'parent')->andWhere('t1.parent')->eq($executionID)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t1.id')->in($this->app->user->view->sprints)->fi()
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)
            ->fetchAll('id');

        return $this->processPlans($plans);
    }

    /**
     * Get plans by idList.
     *
     * @param  array  $idList
     * @access public
     * @return array
     */
    public function getByList($idList = array())
    {
        $plans = $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('id')->in($idList)
            ->andWhere('type')->eq('project')
            ->fetchAll('id');

        return $this->processPlans($plans);
    }

    /**
     * Get plans.
     *
     * @param  int    $executionID
     * @param  int    $productID
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getPlans($executionID = 0, $productID = 0, $orderBy = 'id_asc')
    {
        $plans = $this->getStage($executionID, $productID, 'all', $orderBy);

        $parents  = array();
        $children = array();
        foreach($plans as $planID => $plan)
        {
            $plan->grade == 1 ? $parents[$planID] = $plan : $children[$plan->parent][] = $plan;
        }

        foreach($parents as $planID => $plan) $parents[$planID]->children = isset($children[$planID]) ? $children[$planID] : array();

        return $parents;
    }

    /**
     * Get pairs.
     *
     * @param  int    $executionID
     * @param  int    $productID
     * @param  string $type all|leaf
     * @access public
     * @return array
     */
    public function getPairs($executionID, $productID = 0, $type = 'all')
    {
        $plans = $this->getStage($executionID, $productID, 'all', $orderBy);

        $pairs = array(0 => '');

        if(strpos($type, 'leaf') !== false)
        {
            $parents = array();
            foreach($plans as $planID => $plan) $parents[$plan->parent] = true;
        }

        foreach($plans as $planID => $plan)
        {
            if(strpos($type, 'leaf') !== false and isset($parents[$plan->id])) continue;

            $paths = array_slice(explode(',', trim($plan->path, ',')), 1);
            $planName = '';
            foreach($paths as $path)
            {
                if(isset($plans[$path])) $planName .= '/' . $plans[$path]->name;
            }
            $pairs[$planID] = $planName;
        }

        return $pairs;
    }

    /**
     * Get gantt data.
     *
     * @param  int     $executionID
     * @param  int     $productID
     * @param  int     $baselineID
     * @param  string  $selectCustom
     * @param  bool    $returnJson
     * @access public
     * @return string
     */
    public function getDataForGantt($executionID, $productID, $baselineID = 0, $selectCustom = '', $returnJson = true)
    {
        $this->loadModel('stage');
        $this->loadModel('execution');

        $plans = $this->getStage($executionID, $productID, 'all', 'order');
        if($baselineID)
        {
            $baseline = $this->loadModel('cm')->getByID($baselineID);
            $oldData  = json_decode($baseline->data);
            $oldPlans = $oldData->stage;
            foreach($oldPlans as $id => $oldPlan)
            {
                if(!isset($plans[$id])) continue;
                $plans[$id]->version   = $oldPlan->version;
                $plans[$id]->name      = $oldPlan->name;
                $plans[$id]->milestone = $oldPlan->milestone;
                $plans[$id]->begin     = $oldPlan->begin;
                $plans[$id]->end       = $oldPlan->end;
            }
        }

        $today       = helper::today();
        $datas       = array();
        $planIdList  = array();
        $isMilestone = "<icon class='icon icon-flag icon-sm red'></icon> ";
        $stageIndex  = array();
        foreach($plans as $plan)
        {
            $planIdList[$plan->id] = $plan->id;

            $start     = helper::isZeroDate($plan->begin) ? '' : $plan->begin;
            $end       = helper::isZeroDate($plan->end)   ? '' : $plan->end;
            $realBegan = helper::isZeroDate($plan->realBegan) ? '' : $plan->realBegan;
            $realEnd   = helper::isZeroDate($plan->realEnd)   ? '' : $plan->realEnd;

            $data = new stdclass();
            $data->id            = $plan->id;
            $data->type          = 'plan';
            $data->text          = empty($plan->milestone) ? $plan->name : $plan->name . $isMilestone ;
            $data->name          = $plan->name;
            $data->percent       = $plan->percent;
            $data->attribute     = zget($this->lang->stage->typeList, $plan->attribute);
            $data->milestone     = zget($this->lang->programplan->milestoneList, $plan->milestone);
            $data->owner_id      = $plan->PM;
            $data->status        = $this->processStatus('execution', $plan);
            $data->begin         = $start;
            $data->deadline      = $end;
            $data->realBegan     = $realBegan ? substr($realBegan, 0, 10) : '';
            $data->realEnd       = $realEnd ? substr($realEnd, 0, 10) : '';;
            $data->parent        = $plan->grade == 1 ? 0 :$plan->parent;
            $data->open          = true;
            $data->start_date    = $realBegan ? $realBegan : $start;
            $data->endDate       = $realEnd ? $realEnd : $end;
            $data->duration      = 1;
            $data->color         = $this->lang->execution->gantt->stage->color;
            $data->progressColor = $this->lang->execution->gantt->stage->progressColor;
            $data->textColor     = $this->lang->execution->gantt->stage->textColor;
            $data->bar_height    = $this->lang->execution->gantt->bar_height;

            /* Determines if the object is delay. */
            $data->delay     = $this->lang->programplan->delayList[0];
            $data->delayDays = 0;
            if($today > $end)
            {
                $data->delay     = $this->lang->programplan->delayList[1];
                $data->delayDays = helper::diffDate($today, substr($end, 0, 10));
            }

            if($data->endDate > $data->start_date) $data->duration = helper::diffDate(substr($data->endDate, 0, 10), substr($data->start_date, 0, 10)) + 1;
            if($data->start_date) $data->start_date = date('d-m-Y', strtotime($data->start_date));
            if($data->start_date == '' or $data->endDate == '') $data->duration = 1;

            $datas['data'][$plan->id] = $data;
            $stageIndex[$plan->id]    = array('planID' => $plan->id, 'parent' => $plan->parent, 'progress' => array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalReal' => 0));
        }

        $taskPri  = "<span class='label-pri label-pri-%s' title='%s'>%s</span> ";

        /* Judge whether to display tasks under the stage. */
        $owner   = $this->app->user->account;
        $module  = 'programplan';
        $section = 'browse';
        $object  = 'stageCustom';

        if(empty($selectCustom)) $selectCustom = $this->loadModel('setting')->getItem("owner={$owner}&module={$module}&section={$section}&key={$object}");

        $tasks     = $this->dao->select('*')->from(TABLE_TASK)->where('deleted')->eq(0)->andWhere('execution')->in($planIdList)->orderBy('execution_asc, order_asc, id_desc')->fetchAll('id');
        $taskTeams = $this->dao->select('task,account')->from(TABLE_TASKTEAM)->where('task')->in(array_keys($tasks))->fetchGroup('task', 'account');
        $users     = $this->loadModel('user')->getPairs('noletter');

        if($baselineID)
        {
            $oldTasks = $oldData->task;
            foreach($oldTasks as $id => $oldTask)
            {
                if(!isset($tasks[$id])) continue;
                $tasks[$id]->version    = $oldTask->version;
                $tasks[$id]->name       = $oldTask->name;
                $tasks[$id]->estStarted = $oldTask->estStarted;
                $tasks[$id]->deadline   = $oldTask->deadline;
            }
        }

        foreach($tasks as $task)
        {
            $execution = zget($plans, $task->execution, array());
            $priIcon   = sprintf($taskPri, $task->pri, $task->pri, $task->pri);

            $estStart  = helper::isZeroDate($task->estStarted)  ? '' : $task->estStarted;
            $estEnd    = helper::isZeroDate($task->deadline)    ? '' : $task->deadline;
            $realBegan = helper::isZeroDate($task->realStarted) ? '' : $task->realStarted;
            $realEnd   = (in_array($task->status, array('done', 'closed')) and !helper::isZeroDate($task->finishedDate)) ? $task->finishedDate : '';

            $start = $realBegan ? $realBegan : $estStart;
            $end   = $realEnd   ? $realEnd   : $estEnd;
            if(empty($start) and $execution) $start = $execution->begin;
            if(empty($end)   and $execution) $end   = $execution->end;
            if($start > $end) $end = $start;

            $data = new stdclass();
            $data->id            = $task->execution . '-' . $task->id;
            $data->type          = 'task';
            $data->text          = $priIcon . $task->name;
            $data->percent       = '';
            $data->status        = $this->processStatus('task', $task);
            $data->owner_id      = $task->assignedTo;
            $data->attribute     = '';
            $data->milestone     = '';
            $data->begin         = $start;
            $data->deadline      = $end;
            $data->realBegan     = $realBegan ? substr($realBegan, 0, 10) : '';
            $data->realEnd       = $realEnd ? substr($realEnd, 0, 10) : '';
            $data->pri           = $task->pri;
            $data->parent        = $task->parent > 0 ? $task->execution . '-' . $task->parent : $task->execution;
            $data->open          = true;
            $progress            = $task->consumed ? round($task->consumed / ($task->left + $task->consumed), 3) : 0;
            $data->progress      = $progress;
            $data->taskProgress  = ($progress * 100) . '%';
            $data->start_date    = $start;
            $data->endDate       = $end;
            $data->duration      = 1;
            $data->estimate      = $task->estimate;
            $data->consumed      = $task->consumed;
            $data->color         = zget($this->lang->execution->gantt->color, $task->pri, $this->lang->execution->gantt->defaultColor);
            $data->progressColor = zget($this->lang->execution->gantt->progressColor, $task->pri, $this->lang->execution->gantt->defaultProgressColor);
            $data->textColor     = zget($this->lang->execution->gantt->textColor, $task->pri, $this->lang->execution->gantt->defaultTextColor);
            $data->bar_height    = $this->lang->execution->gantt->bar_height;

            /* Determines if the object is delay. */
            $data->delay     = $this->lang->programplan->delayList[0];
            $data->delayDays = 0;
            if($today > $end)
            {
                $data->delay     = $this->lang->programplan->delayList[1];
                $data->delayDays = helper::diffDate($today, substr($end, 0, 10));
            }

            /* If multi task then show the teams. */
            if($task->mode == 'multi' and !empty($taskTeams[$task->id]))
            {
                $teams     = array_keys($taskTeams[$task->id]);
                $assigneds = array();
                foreach($teams as $assignedTo) $assigneds[] = zget($users, $assignedTo);
                $data->owner_id = join(',', $assigneds);
            }

            if($data->endDate > $data->start_date) $data->duration = helper::diffDate(substr($data->endDate, 0, 10), substr($data->start_date, 0, 10)) + 1;
            if($data->start_date) $data->start_date = date('d-m-Y', strtotime($data->start_date));
            if($data->start_date == '' or $data->endDate == '') $data->duration = 1;

            if(strpos($selectCustom, 'task') !== false) $datas['data'][$data->id] = $data;
            foreach($stageIndex as $index => $stage)
            {
                if($stage['planID'] == $task->execution)
                {
                    $stageIndex[$index]['progress']['totalEstimate'] += $task->estimate;
                    $stageIndex[$index]['progress']['totalConsumed'] += $task->parent == '-1' ? 0 : $task->consumed;
                    $stageIndex[$index]['progress']['totalReal']     += ($task->left + $task->consumed);

                    $parent = $stage['parent'];
                    if(isset($stageIndex[$parent]))
                    {
                        $stageIndex[$parent]['progress']['totalEstimate'] += $task->estimate;
                        $stageIndex[$parent]['progress']['totalConsumed'] += $task->parent == '-1' ? 0 : $task->consumed;
                        $stageIndex[$parent]['progress']['totalReal']     += ($task->left + $task->consumed);
                    }
                }
            }
        }

        /* Calculate the progress of the phase. */
        foreach($stageIndex as $index => $stage)
        {
            $progress  = empty($stage['progress']['totalConsumed']) ? 0 : round($stage['progress']['totalConsumed'] / $stage['progress']['totalReal'], 3);
            $datas['data'][$index]->progress = $progress;

            $progress = ($progress * 100) . '%';
            $datas['data'][$index]->taskProgress = $progress;
            $datas['data'][$index]->estimate = $stage['progress']['totalEstimate'];
            $datas['data'][$index]->consumed = $stage['progress']['totalConsumed'];
        }

        $datas['links'] = array();
        if($this->config->edition != 'open')
        {
            $relations = $this->dao->select('*')->from(TABLE_RELATIONOFTASKS)->where('execution')->in($planIdList)->orderBy('task,pretask')->fetchAll();
            foreach($relations as $relation)
            {
                $link['source']   = $relation->execution . '-' . $relation->pretask;
                $link['target']   = $relation->execution . '-' . $relation->task;
                $link['type']     = $this->config->execution->gantt->linkType[$relation->condition][$relation->action];
                $datas['links'][] = $link;
            }
        }

        $datas['data'] = isset($datas['data']) ? array_values($datas['data']) : array();
        return $returnJson ? json_encode($datas) : $datas;
    }

    /**
     * Get gantt data group by assigned.
     *
     * @param  int     $executionID
     * @param  int     $productID
     * @param  int     $baselineID
     * @param  string  $selectCustom
     * @param  bool    $returnJson
     * @access public
     * @return string
     */
    public function getDataForGanttGroupByAssignedTo($executionID, $productID, $baselineID = 0, $selectCustom = '', $returnJson = true)
    {
        $plans = $this->getStage($executionID, $productID);

        $datas       = array();
        $planIdList  = array();
        $isMilestone = "<icon class='icon icon-flag icon-sm red'></icon> ";
        $stageIndex  = array();

        foreach($plans as $plan) $planIdList[$plan->id] = $plan->id;

        $taskPri  = "<span class='label-pri label-pri-%s' title='%s'>%s</span> ";

        /* Judge whether to display tasks under the stage. */
        $owner   = $this->app->user->account;
        $module  = 'programplan';
        $section = 'browse';
        $object  = 'stageCustom';
        if(empty($selectCustom)) $selectCustom = $this->loadModel('setting')->getItem("owner={$owner}&module={$module}&section={$section}&key={$object}");

        $tasksGroup = $this->dao->select('*')->from(TABLE_TASK)->where('deleted')->eq(0)->andWhere('execution')->in($planIdList)->fetchGroup('assignedTo','id');
        $users      = $this->loadModel('user')->getPairs('noletter');

        $tasksMap   = array();
        $multiTasks = array();
        foreach($tasksGroup as $group => $tasks)
        {
            foreach($tasks as $id => $task)
            {
                if($task->mode == 'multi') $multiTasks[$id] = $group;
                $tasksMap[$task->id] = $task;
            }
        }

        if($multiTasks)
        {
            $taskTeams = $this->dao->select('t1.*,t2.realname')->from(TABLE_TASKTEAM)->alias('t1')
                ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
                ->where('t1.task')->in(array_keys($multiTasks))
                ->orderBy('t1.order')
                ->fetchGroup('task', 'id');
            foreach($taskTeams as $taskID => $team)
            {
                $group = $multiTasks[$taskID];
                foreach($team as $member)
                {
                    $account = $member->account;
                    if($account == $group) continue;
                    if(!isset($taskGroups[$account])) $taskGroups[$account] = array();

                    $taskGroups[$account][$taskID] = clone $tasksMap[$taskID];
                    $taskGroups[$account][$taskID]->id         = $taskID . '_' . $account;
                    $taskGroups[$account][$taskID]->realID     = $taskID;
                    $taskGroups[$account][$taskID]->assignedTo = $account;
                    $taskGroups[$account][$taskID]->realname   = $member->realname;
                }
            }
        }

        $groupID = 0;
        foreach($tasksGroup as $group => $tasks)
        {
            $groupID --;
            $groupName = $group;
            $groupName = zget($users, $group);
            $dataGroup             = new stdclass();
            $dataGroup->id         = $groupID;
            $dataGroup->type       = 'group';
            $dataGroup->text       = $groupName;
            $dataGroup->percent    = '';
            $dataGroup->attribute  = '';
            $dataGroup->milestone  = '';
            $dataGroup->owner_id   = $group;
            $dataGroup->status     = '';
            $dataGroup->begin      = '';
            $dataGroup->deadline   = '';
            $dataGroup->realBegan  = '';
            $dataGroup->realEnd    = '';
            $dataGroup->parent     = 0;
            $dataGroup->open       = true;
            $dataGroup->progress   = '';
            $dataGroup->taskProgress  = '';
            $dataGroup->color         = $this->lang->execution->gantt->stage->color;
            $dataGroup->progressColor = $this->lang->execution->gantt->stage->progressColor;
            $dataGroup->textColor     = $this->lang->execution->gantt->stage->textColor;
            $dataGroup->bar_height    = $this->lang->execution->gantt->bar_height;

            $groupKey = $groupID . $group;
            $datas['data'][$groupKey] = $dataGroup;

            $realStartDate = array();
            $realEndDate   = array();
            $totalTask = count($tasks);
            foreach($tasks as $taskID => $task)
            {
                $execution = zget($plans, $task->execution, array());

                $priIcon   = sprintf($taskPri, $task->pri, $task->pri, $task->pri);

                $estStart  = helper::isZeroDate($task->estStarted)  ? '' : $task->estStarted;
                $estEnd    = helper::isZeroDate($task->deadline)    ? '' : $task->deadline;
                $realBegan = helper::isZeroDate($task->realStarted) ? '' : $task->realStarted;
                $realEnd   = (in_array($task->status, array('done', 'closed')) and !helper::isZeroDate($task->finishedDate)) ? $task->finishedDate : '';

                $start = $realBegan ? $realBegan : $estStart;
                $end   = $realEnd   ? $realEnd   : $estEnd;
                if(empty($start) and $execution) $start = $execution->begin;
                if(empty($end)   and $execution) $end   = $execution->end;
                if($start > $end) $end = $start;

                $data = new stdclass();
                $data->id           = $task->id;
                $data->type         = 'task';
                $data->text         = $priIcon . $task->name;
                $data->percent      = '';
                $data->status       = $this->processStatus('task', $task);
                $data->owner_id     = $task->assignedTo;
                $data->attribute    = '';
                $data->milestone    = '';
                $data->begin        = $start;
                $data->deadline     = $end;
                $data->realBegan    = $realBegan ? substr($realBegan, 0, 10) : '';
                $data->realEnd      = $realEnd ? substr($realEnd, 0, 10) : '';
                $data->pri          = $task->pri;
                $data->parent       = ($task->parent > 0 and $task->assignedTo != '' and !empty($tasksMap[$task->parent]->assignedTo)) ? $task->parent : $groupID;
                $data->open         = true;
                $progress           = $task->consumed ? round($task->consumed / ($task->left + $task->consumed), 3) : 0;
                $data->progress     = $progress;
                $data->taskProgress = ($progress * 100) . '%';
                $data->start_date   = $start;
                $data->endDate      = $end;
                $data->duration     = 1;
                $data->estimate     = $task->estimate;
                $data->consumed     = $task->consumed;
                $data->color         = zget($this->lang->execution->gantt->color, $task->pri, $this->lang->execution->gantt->defaultColor);
                $data->progressColor = zget($this->lang->execution->gantt->progressColor, $task->pri, $this->lang->execution->gantt->defaultProgressColor);
                $data->textColor     = zget($this->lang->execution->gantt->textColor, $task->pri, $this->lang->execution->gantt->defaultTextColor);
                $data->bar_height    = $this->lang->execution->gantt->bar_height;

                if($data->endDate > $data->start_date) $data->duration = helper::diffDate($data->endDate, $data->start_date) + 1;

                if($data->start_date) $data->start_date = date('d-m-Y', strtotime($data->start_date));
                if($data->start_date == '' or $data->endDate == '') $data->duration = 1;

                if(strpos($selectCustom, 'task') !== false) $datas['data'][$data->id] = $data;

                if(!empty($start)) $realStartDate[] = strtotime($start);
                if(!empty($end)) $realEndDate[] = strtotime($end);

                if(isset($stageIndex[$groupKey]['totalConsumed']))
                {
                    $stageIndex[$groupKey]['totalConsumed'] += $task->consumed;
                    $stageIndex[$groupKey]['totalReal']     += $task->left + $task->consumed;
                    $stageIndex[$groupKey]['totalEstimate'] += $task->estimate;
                }
                else
                {
                    $stageIndex[$groupKey]['totalConsumed'] = $task->consumed;
                    $stageIndex[$groupKey]['totalReal']     = $task->left + $task->consumed;
                    $stageIndex[$groupKey]['totalEstimate'] = $task->estimate;
                }
            }

            /* Calculate group realBegan and realEnd. */
            if(!empty($realStartDate)) $datas['data'][$groupKey]->realBegan = date('Y-m-d', min($realStartDate));
            if(!empty($realEndDate) and (count($realEndDate) == $totalTask)) $datas['data'][$groupKey]->realEnd = date('Y-m-d', max($realEndDate));
        }

        /* Calculate the progress of the phase. */
        foreach($stageIndex as $index => $stage)
        {
            $progress  = empty($stage['totalReal']) ? 0 : round($stage['totalConsumed'] / $stage['totalReal'], 3);
            $datas['data'][$index]->progress = $progress;

            $progress = ($progress * 100) . '%';
            $datas['data'][$index]->taskProgress = $progress;
            $datas['data'][$index]->estimate     = $stage['totalEstimate'];
            $datas['data'][$index]->consumed     = $stage['totalConsumed'];
        }

        $datas['links'] = array();
        if($this->config->edition != 'open')
        {
            $relations = $this->dao->select('*')->from(TABLE_RELATIONOFTASKS)->where('execution')->in($planIdList)->orderBy('task,pretask')->fetchAll();
            foreach($relations as $relation)
            {
                $link['source']   = $relation->execution . '-' . $relation->pretask;
                $link['target']   = $relation->execution . '-' . $relation->task;
                $link['type']     = $this->config->execution->gantt->linkType[$relation->condition][$relation->action];
                $datas['links'][] = $link;
            }
        }

        $datas['data'] = isset($datas['data']) ? array_values($datas['data']) : array();

        return $returnJson ? json_encode($datas) : $datas;
    }

    /**
     * Get total percent.
     *
     * @param  object  $stage
     * @param  object  $parent
     * @access public
     * @return int
     */
    public function getTotalPercent($stage, $parent = false)
    {
        /* When parent is equal to true, query the total workload of the subphase. */
        $executionID = $parent ? $stage->id : $stage->project;
        $plans = $this->getStage($executionID, $stage->product, 'parent');

        $totalPercent = 0;
        $stageID      = $stage->id;
        foreach($plans as $id => $stage)
        {
            if($id == $stageID) continue;
            $totalPercent += $stage->percent;
        }

        return $totalPercent;
    }

    /**
     * Process plans.
     *
     * @param  int    $plans
     * @access public
     * @return object
     */
    public function processPlans($plans)
    {
        foreach($plans as $planID => $plan) $plans[$planID] = $this->processPlan($plan);
        return $plans;
    }

    /**
     * Process plan.
     *
     * @param  int    $plan
     * @access public
     * @return object
     */
    public function processPlan($plan)
    {
        $plan->setMilestone = true;

        if($plan->parent)
        {
            $attribute = $this->dao->select('attribute')->from(TABLE_PROJECT)->where('id')->eq($plan->parent)->fetch('attribute');
            $plan->attribute = $attribute == 'develop' ? $attribute : $plan->attribute;
        }
        else
        {
            $milestones = $this->dao->select('count(*) AS count')->from(TABLE_PROJECT)
                ->where('parent')->eq($plan->id)
                ->andWhere('milestone')->eq(1)
                ->andWhere('deleted')->eq(0)
                ->fetch('count');
            if($milestones > 0)
            {
                $plan->milestone    = 0;
                $plan->setMilestone = false;
            }
        }

        $plan->begin     = $plan->begin == '0000-00-00' ? '' : $plan->begin;
        $plan->end       = $plan->end  == '0000-00-00' ? '' : $plan->end;
        $plan->realBegan = $plan->realBegan == '0000-00-00' ? '' : $plan->realBegan;
        $plan->realEnd   = $plan->realEnd == '0000-00-00' ? '' : $plan->realEnd;

        $plan->product     = $this->loadModel('product')->getProductIDByProject($plan->id);
        $plan->productName = $this->dao->findByID($plan->product)->from(TABLE_PRODUCT)->fetch('name');

        return $plan;
    }

    /**
     * Get duration.
     *
     * @param  int    $begin
     * @param  int    $end
     * @access public
     * @return int
     */
    public function getDuration($begin, $end)
    {
        $duration = $this->loadModel('holiday')->getActualWorkingDays($begin, $end);
        return count($duration);
    }

    /**
     * Create a plan.
     *
     * @param  int  $projectID
     * @param  int  $productID
     * @param  int  $parentID
     * @access public
     * @return bool
     */
    public function create($projectID = 0, $productID = 0, $parentID = 0)
    {
        $data = (array)fixer::input('post')->get();
        extract($data);

        /* Determine if a task has been created under the parent phase. */
        if(!$this->isCreateTask($parentID)) return dao::$errors['message'][] = $this->lang->programplan->error->createdTask;

        /* The child phase type setting is the same as the parent phase. */
        $parentAttribute = '';
        $parentPercent   = 0;
        if($parentID)
        {
            $parentStage     = $this->getByID($parentID);
            $parentAttribute = $parentStage->attribute;
            $parentPercent   = $parentStage->percent;
            $parentACL       = $parentStage->acl;
        }

        $names     = array_filter($names);
        $sameNames = array_diff_assoc($names, array_unique($names));

        $project   = $this->loadModel('project')->getByID($projectID);
        $setCode   = (!isset($this->config->setCode) or $this->config->setCode == 1) ? true : false;
        $sameCodes = $this->checkCodeUnique($codes, isset($planIDList) ? $planIDList : '');

        $datas = array();
        foreach($names as $key => $name)
        {
            if(empty($name)) continue;

            $plan = new stdclass();
            $plan->id         = isset($planIDList[$key]) ? $planIDList[$key] : '';
            $plan->type       = empty($type[$key]) ? 'stage' : $type[$key];
            $plan->project    = $projectID;
            $plan->parent     = $parentID ? $parentID : $projectID;
            $plan->name       = $names[$key];
            if($setCode) $plan->code = $codes[$key];
            $plan->percent    = $percents[$key];
            $plan->attribute  = (empty($parentID) or $parentAttribute == 'mix') ? $attributes[$key] : $parentAttribute;
            $plan->milestone  = $milestone[$key];
            $plan->begin      = empty($begin[$key]) ? '0000-00-00' : $begin[$key];
            $plan->end        = empty($end[$key]) ? '0000-00-00' : $end[$key];
            $plan->realBegan  = empty($realBegan[$key]) ? '0000-00-00' : $realBegan[$key];
            $plan->realEnd    = empty($realEnd[$key]) ? '0000-00-00' : $realEnd[$key];
            $plan->output     = empty($output[$key]) ? '' : implode(',', $output[$key]);
            $plan->acl        = empty($parentID) ? $acl[$key] : $parentACL;
            $plan->PM         = empty($PM[$key]) ? '' : $PM[$key];
            $plan->desc       = empty($desc[$key]) ? '' : $desc[$key];
            $plan->hasProduct = $project->hasProduct;

            $datas[] = $plan;
        }

        $totalPercent = 0;
        $totalDevType = 0;
        $milestone    = 0;
        foreach($datas as $index => $plan)
        {
            if(!empty($sameNames) and in_array($plan->name, $sameNames)) dao::$errors[$index]['name'] = empty($type) ? $this->lang->programplan->error->sameName : str_replace($this->lang->execution->stage, '', $this->lang->programplan->error->sameName);
            if($setCode and $sameCodes !== true and !empty($sameCodes) and in_array($plan->code, $sameCodes)) dao::$errors[$index]['code'] = sprintf($this->lang->error->repeat, $plan->type == 'stage' ? $this->lang->execution->code : $this->lang->code, $plan->code);

            if($plan->percent and !preg_match("/^[0-9]+(.[0-9]{1,3})?$/", $plan->percent))
            {
                dao::$errors[$index]['percent'] = $this->lang->programplan->error->percentNumber;
            }
            if(helper::isZeroDate($plan->begin))
            {
                dao::$errors[$index]['begin'] = $this->lang->programplan->emptyBegin;
            }
            if(!validater::checkDate($plan->begin) and empty(dao::$errors[$index]['begin']))
            {
                dao::$errors[$index]['begin'] = $this->lang->programplan->checkBegin;
            }
            if(helper::isZeroDate($plan->end))
            {
                dao::$errors[$index]['end'] = $this->lang->programplan->emptyEnd;
            }
            if(!validater::checkDate($plan->end) and empty(dao::$errors[$index]['end']))
            {
                dao::$errors[$index]['end'] = $this->lang->programplan->checkEnd;
            }
            if(!helper::isZeroDate($plan->end) and $plan->end < $plan->begin and empty(dao::$errors[$index]['begin']))
            {
                dao::$errors[$index]['end'] = $this->lang->programplan->error->planFinishSmall;
            }
            if(isset($parentStage) and ($plan->end > $parentStage->end || $plan->begin < $parentStage->begin))
            {
                if($plan->begin < $parentStage->begin and empty(dao::$errors[$index]['begin'])) dao::$errors[$index]['begin'] = $this->lang->programplan->error->parentDuration;
                if($plan->end > $parentStage->end and empty(dao::$errors[$index]['end']))       dao::$errors[$index]['end']   = $this->lang->programplan->error->parentDuration;
            }
            if($plan->begin < $project->begin and empty(dao::$errors[$index]['begin']))
            {
                dao::$errors[$index]['begin'] = sprintf($this->lang->programplan->errorBegin, $project->begin);
            }
            if(!helper::isZeroDate($plan->end) and $plan->end > $project->end and empty(dao::$errors[$index]['end']))
            {
                dao::$errors[$index]['end'] = sprintf($this->lang->programplan->errorEnd, $project->end);
            }

            if(helper::isZeroDate($plan->begin)) $plan->begin = '';
            if(helper::isZeroDate($plan->end))   $plan->end   = '';
            if($setCode and empty($plan->code))
            {
                dao::$errors[$index]['code'] = sprintf($this->lang->error->notempty, $plan->type == 'stage' ? $this->lang->execution->code : $this->lang->code);
            }
            foreach(explode(',', $this->config->programplan->create->requiredFields) as $field)
            {
                $field = trim($field);
                if($field and empty($plan->$field))
                {
                    dao::$errors[$index][$field] = sprintf($this->lang->error->notempty, $this->lang->programplan->$field);
                }
            }

            $plan->percent = (float)$plan->percent;
            $totalPercent += $plan->percent;

            if($plan->milestone) $milestone = 1;
        }

        if($totalPercent > 100) dao::$errors['percent'] = $this->lang->programplan->error->percentOver;
        if(dao::isError()) return false;

        $this->loadModel('action');
        $this->loadModel('user');
        $this->loadModel('execution');
        $this->app->loadLang('doc');
        $account = $this->app->user->account;
        $now     = helper::now();

        if(!isset($orders)) $orders = array();
        asort($orders);
        if(count($orders) < count($datas))
        {
            $orderIndex = empty($orders) ? 0 : count($orders);
            $lastID     = $this->dao->select('id')->from(TABLE_EXECUTION)->orderBy('id_desc')->fetch('id');
            for($i = $orderIndex; $i < count($datas); $i ++)
            {
                $lastID ++;
                $orders[$i] = $lastID * 5;
            }
        }

        foreach($datas as $data)
        {
            /* Set planDuration and realDuration. */
            if($this->config->edition == 'max')
            {
                $data->planDuration = $this->getDuration($data->begin, $data->end);
                $data->realDuration = $this->getDuration($data->realBegan, $data->realEnd);
            }

            $projectChanged = false;
            $data->days     = helper::diffDate($data->end, $data->begin) + 1;
            $data->order    = current($orders);

            if($data->id)
            {
                $stageID = $data->id;
                unset($data->id);

                $oldStage    = $this->getByID($stageID);
                $planChanged = ($oldStage->name != $data->name || $oldStage->milestone != $data->milestone || $oldStage->begin != $data->begin || $oldStage->end != $data->end);

                if($planChanged) $data->version = $oldStage->version + 1;
                $this->dao->update(TABLE_PROJECT)->data($data)
                    ->autoCheck()
                    ->batchCheck($this->config->programplan->edit->requiredFields, 'notempty')
                    ->checkIF($plan->percent != '', 'percent', 'float')
                    ->where('id')->eq($stageID)
                    ->exec();

                /* Add PM to stage teams and project teams. */
                if(!empty($data->PM))
                {
                    $team = $this->user->getTeamMemberPairs($stageID, 'execution');
                    if(isset($team[$data->PM])) continue;

                    $roles  = $this->user->getUserRoles($data->PM);
                    $member = new stdclass();
                    $member->root    = $stageID;
                    $member->account = $data->PM;
                    $member->role    = zget($roles, $data->PM, '');
                    $member->join    = $now;
                    $member->type    = 'execution';
                    $member->days    = $data->days;
                    $member->hours   = $this->config->execution->defaultWorkhours;
                    $this->dao->insert(TABLE_TEAM)->data($member)->exec();
                    $this->execution->addProjectMembers($data->project, array($data->PM => $member));
                }

                if($data->acl != 'open') $this->user->updateUserView($stageID, 'sprint');

                /* Record version change information. */
                if($planChanged)
                {
                    $spec = new stdclass();
                    $spec->project   = $stageID;
                    $spec->version   = $data->version;
                    $spec->name      = $data->name;
                    $spec->milestone = $data->milestone;
                    $spec->begin     = $data->begin;
                    $spec->end       = $data->end;
                    $this->dao->insert(TABLE_PROJECTSPEC)->data($spec)->exec();
                }

                $changes  = common::createChanges($oldStage, $data);
                $actionID = $this->action->create('execution', $stageID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            else
            {
                unset($data->id);
                $data->status        = 'wait';
                $data->division      = $project->division;
                $data->version       = 1;
                $data->parentVersion = $data->parent == 0 ? 0 : $this->dao->findByID($data->parent)->from(TABLE_PROJECT)->fetch('version');
                $data->team          = substr($data->name,0, 30);
                $data->openedBy      = $account;
                $data->openedDate    = $now;
                $data->openedVersion = $this->config->version;
                if(!isset($data->acl)) $data->acl = $this->dao->findByID($data->parent)->from(TABLE_PROJECT)->fetch('acl');
                $this->dao->insert(TABLE_PROJECT)->data($data)
                    ->autoCheck()
                    ->batchCheck($this->config->programplan->create->requiredFields, 'notempty')
                    ->checkIF($plan->percent != '', 'percent', 'float')
                    ->exec();

                if(!dao::isError())
                {
                    $stageID = $this->dao->lastInsertID();
                    if($data->type == 'kanban')
                    {
                        $execution = $this->execution->getByID($stageID);
                        $this->loadModel('kanban')->createRDKanban($execution);
                    }

                    if($data->acl != 'open') $this->user->updateUserView($stageID, 'sprint');

                    /* Create doc lib. */
                    $lib = new stdclass();
                    $lib->execution = $stageID;
                    $lib->name      = str_replace($this->lang->executionCommon, $this->lang->project->stage, $this->lang->doclib->main['execution']);
                    $lib->type      = 'execution';
                    $lib->main      = '1';
                    $lib->acl       = 'default';
                    $this->dao->insert(TABLE_DOCLIB)->data($lib)->exec();

                    /* Add creators and PM to stage teams and project teams. */
                    $teamMembers = array();
                    $members     = array($this->app->user->account, $data->PM);
                    $roles       = $this->user->getUserRoles(array_values($members));
                    $team        = $this->user->getTeamMemberPairs($stageID, 'execution');
                    foreach($members as $teamMember)
                    {
                        if(empty($teamMember) or isset($team[$teamMember]) or isset($teamMembers[$teamMember])) continue;

                        $member = new stdclass();
                        $member->root    = $stageID;
                        $member->account = $teamMember;
                        $member->role    = zget($roles, $teamMember, '');
                        $member->join    = $now;
                        $member->type    = 'execution';
                        $member->days    = $data->days;
                        $member->hours   = $this->config->execution->defaultWorkhours;
                        $this->dao->insert(TABLE_TEAM)->data($member)->exec();
                        $teamMembers[$teamMember] = $member;
                    }
                    $this->execution->addProjectMembers($data->project, $teamMembers);

                    $this->setTreePath($stageID);
                    if($data->acl != 'open') $this->user->updateUserView($stageID, 'sprint');

                    /* Record version change information. */
                    $spec = new stdclass();
                    $spec->project   = $stageID;
                    $spec->version   = $data->version;
                    $spec->name      = $data->name;
                    $spec->milestone = $data->milestone;
                    $spec->begin     = $data->begin;
                    $spec->end       = $data->end;
                    $this->dao->insert(TABLE_PROJECTSPEC)->data($spec)->exec();

                    if($project->hasProduct)
                    {
                        $this->action->create('execution', $stageID, 'opened', '', join(',', $_POST['products']));
                    }
                    else
                    {
                        $this->action->create('execution', $stageID, 'opened');
                    }

                    $this->computeProgress($stageID, 'create');
                }
            }

            $linkProducts = array();
            $linkBranches = array();
            $productList  = $this->loadModel('product')->getProducts($projectID);
            if($project->division)
            {
                $linkProducts = array(0 => $productID);
                $linkBranches = array(0 => $productList[$productID]->branches);
            }
            else
            {
                $linkProducts = array_keys($productList);
                foreach($linkProducts as $index => $productID) $linkBranches[$index] = $productList[$productID]->branches;
            }
            $this->post->set('products', $linkProducts);
            $this->post->set('branch', $linkBranches);
            $this->execution->updateProducts($stageID);

            /* If child plans has milestone, update parent plan set milestone eq 0 . */
            if($parentID and $milestone) $this->dao->update(TABLE_PROJECT)->set('milestone')->eq(0)->where('id')->eq($parentID)->exec();

            if(dao::isError()) return print(js::error(dao::getError()));

            next($orders);
        }
    }

    /**
     * Set stage tree path.
     *
     * @param  int    $planID
     * @access public
     * @return bool
     */
    public function setTreePath($planID)
    {
        $stage  = $this->dao->select('id,type,parent,path,grade')->from(TABLE_PROJECT)->where('id')->eq($planID)->fetch();
        $parent = $this->dao->select('id,type,parent,path,grade')->from(TABLE_PROJECT)->where('id')->eq($stage->parent)->fetch();

        if($parent->type == 'project')
        {
            $path['path']  =  ",{$parent->id},{$stage->id},";
            $path['grade'] = 1;
        }
        elseif($parent->type == 'stage')
        {
            $path['path']  = $parent->path . "{$stage->id},";
            $path['grade'] = $parent->grade + 1;
        }
        $this->dao->update(TABLE_PROJECT)->set('path')->eq($path['path'])->set('grade')->eq($path['grade'])->where('id')->eq($stage->id)->exec();
    }

    /**
     * Update a plan.
     *
     * @param  int    $planID
     * @param  int    $projectID
     * @access public
     * @return bool|array
     */
    public function update($planID = 0, $projectID = 0)
    {
        /* Get oldPlan and the data from the post. */
        $oldPlan = $this->getByID($planID);
        $plan    = fixer::input('post')
            ->setDefault('begin', '0000-00-00')
            ->setDefault('end', '0000-00-00')
            ->setDefault('realBegan', '0000-00-00')
            ->setDefault('realEnd', '0000-00-00')
            ->join('output', ',')
            ->get();

        /* Judgment of required items. */
        if($plan->begin == '0000-00-00') dao::$errors['begin'][] = sprintf($this->lang->error->notempty, $this->lang->programplan->begin);
        if($plan->end   == '0000-00-00') dao::$errors['end'][]   = sprintf($this->lang->error->notempty, $this->lang->programplan->end);
        if(dao::isError()) return false;

        if($plan->parent) $parentStage = $this->getByID($plan->parent);
        if(isset($parentStage) and ($plan->end > $parentStage->end || $plan->begin < $parentStage->begin))
        {
            dao::$errors['message'][] = $this->lang->programplan->error->parentDuration;
            return false;
        }

        if($projectID) $this->loadModel('execution')->checkBeginAndEndDate($projectID, $plan->begin, $plan->end);
        if(dao::isError()) return false;

        $setCode = (!isset($this->config->setCode) or $this->config->setCode == 1) ? true : false;
        if($setCode and empty($plan->code))
        {
            dao::$errors['code'][] = sprintf($this->lang->error->notempty, $this->lang->execution->code);
            return false;
        }

        $planChanged = ($oldPlan->name != $plan->name || $oldPlan->milestone != $plan->milestone || $oldPlan->begin != $plan->begin || $oldPlan->end != $plan->end);

        if($plan->parent > 0)
        {
            $plan->attribute = $parentStage->attribute == 'mix' ? $plan->attribute : $parentStage->attribute;
            $plan->acl       = $parentStage->acl;
            $parentPercent   = $parentStage->percent;

            $childrenTotalPercent = $this->getTotalPercent($parentStage, true);
            $childrenTotalPercent = $plan->parent == $oldPlan->parent ? ($childrenTotalPercent - $oldPlan->percent + $plan->percent) : ($childrenTotalPercent + $plan->percent);
            if($childrenTotalPercent > 100) return dao::$errors['percent'][] = $this->lang->programplan->error->percentOver;

            /* If child plan has milestone, update parent plan set milestone eq 0 . */
            if($plan->milestone and $parentStage->milestone) $this->dao->update(TABLE_PROJECT)->set('milestone')->eq(0)->where('id')->eq($oldPlan->parent)->exec();
        }
        else
        {
            /* Synchronously update sub-phase permissions. */
            $childrenIDList = $this->dao->select('id')->from(TABLE_PROJECT)->where('parent')->eq($oldPlan->id)->fetchAll('id');
            if(!empty($childrenIDList)) $this->dao->update(TABLE_PROJECT)->set('acl')->eq($plan->acl)->where('id')->in(array_keys($childrenIDList))->exec();

            /* The workload of the parent plan cannot exceed 100%. */
            $oldPlan->parent = $plan->parent;
            $totalPercent    = $this->getTotalPercent($oldPlan);
            $totalPercent    = $totalPercent + $plan->percent;
            if($totalPercent > 100) return dao::$errors['percent'][] = $this->lang->programplan->error->percentOver;
        }

        /* Set planDuration and realDuration. */
        if($this->config->edition == 'max')
        {
            $plan->planDuration = $this->getDuration($plan->begin, $plan->end);
            $plan->realDuration = $this->getDuration($plan->realBegan, $plan->realEnd);
        }

        if($planChanged)  $plan->version = $oldPlan->version + 1;
        if(empty($plan->parent)) $plan->parent = $projectID;

        $parentStage = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($plan->parent)->andWhere('type')->eq('stage')->fetch();

        /* Fix bug #22030. Reset field name for show dao error. */
        $this->lang->project->name = $this->lang->programplan->name;
        $this->lang->project->code = $this->lang->execution->code;

        $relatedExecutionsID = $this->loadModel('execution')->getRelatedExecutions($planID);
        $relatedExecutionsID = !empty($relatedExecutionsID) ? implode(',', array_keys($relatedExecutionsID)) : '';

        $this->dao->update(TABLE_PROJECT)->data($plan)
            ->autoCheck()
            ->batchCheck($this->config->programplan->edit->requiredFields, 'notempty')
            ->checkIF($plan->end != '0000-00-00', 'end', 'ge', $plan->begin)
            ->checkIF($plan->percent != false, 'percent', 'float')
            ->checkIF(!empty($plan->name), 'name', 'unique', "id in ('{$relatedExecutionsID}') and type in ('sprint','stage') and `project` = {$oldPlan->project} and `deleted` = '0'" . ($parentStage ? " and `parent` = {$oldPlan->parent}" : ''))
            ->checkIF(!empty($plan->code) and $setCode, 'code', 'unique', "id != $planID and type in ('sprint','stage','kanban') and `deleted` = '0'")
            ->where('id')->eq($planID)
            ->exec();

        if(dao::isError()) return false;
        $this->setTreePath($planID);
        $this->updateSubStageAttr($planID, $plan->attribute);
        if($plan->acl != 'open')
        {
            $planIdList = $this->dao->select('id')->from(TABLE_EXECUTION)->where('path')->like("%,$planID,%")->andWhere('type')->eq('stage')->fetchAll('id');
            $this->loadModel('user')->updateUserView(array_keys($planIdList), 'sprint');
        }

        if($planChanged)
        {
            $spec = new stdclass();
            $spec->project   = $planID;
            $spec->version   = $plan->version;
            $spec->name      = $plan->name;
            $spec->milestone = $plan->milestone;
            $spec->begin     = $plan->begin;
            $spec->end       = $plan->end;

            $this->dao->insert(TABLE_PROJECTSPEC)->data($spec)->exec();
        }

        return common::createChanges($oldPlan, $plan);
    }

    /**
     * Print cell.
     *
     * @param  int    $col
     * @param  int    $plan
     * @param  int    $users
     * @param  int    $projectID
     * @access public
     * @return string
     */
    public function printCell($col, $plan, $users, $projectID)
    {
        $id = $col->id;
        if($col->show)
        {
            $class  = 'c-' . $id;
            $title  = '';
            $idList = array('id','name','output','percent','attribute','version','begin','end','realBegan','realEnd', 'openedBy', 'openedDate');
            if(in_array($id,$idList))
            {
                $class .= ' text-left';
                $title  = "title='{$plan->$id}'";
                if($id == 'output') $class .= ' text-ellipsis';
                if(!empty($plan->children)) $class .= ' has-child';
            }
            else
            {
                $class .= ' text-center';
            }
            if($id == 'actions') $class .= ' c-actions';

            echo "<td class='{$class}' {$title}>";
            if($this->config->edition != 'open') $this->loadModel('flow')->printFlowCell('programplan', $plan, $id);
            switch($id)
            {
            case 'id':
                echo sprintf('%03d', $plan->id);
                break;
            case 'name':
                $milestoneFlag = $plan->milestone ? " <i class='icon icon-flag red' title={$this->lang->programplan->milestone}'></i>" : '';
                if($plan->grade > 1) echo '<span class="label label-badge label-light" title="' . $this->lang->programplan->children . '">' . $this->lang->programplan->childrenAB . '</span> ';
                echo $plan->name . $milestoneFlag;
                if(!empty($plan->children)) echo '<a class="plan-toggle" data-id="' . $plan->id . '"><i class="icon icon-angle-double-right"></i></a>';
                break;
            case 'percent':
                echo $plan->percent . '%';
                break;
            case 'attribute':
                echo zget($this->lang->stage->typeList, $plan->attribute, '');
                break;
            case 'begin':
                echo $plan->begin;
                break;
            case 'end':
                echo $plan->end;
                break;
            case 'realBegan':
                echo $plan->realBegan;
                break;
            case 'realEnd':
                echo $plan->realEnd;
                break;
            case 'output':
                echo $plan->output;
                break;
            case 'version':
                echo $plan->version;
                break;
            case 'editedBy':
                echo zget($users, $plan->editedBy);
                break;
            case 'editedDate':
                echo substr($plan->editedDate, 5, 11);
                break;
            case 'openedBy':
                echo zget($users, $plan->openedBy);
                break;
            case 'openedDate':
                echo substr($plan->openedDate, 5, 11);
                break;
            case 'actions':
                common::printIcon('execution', 'start', "executionID={$plan->id}", $plan, 'list', '', '', 'iframe', true);
                $class = !empty($plan->children) ? 'disabled' : '';
                common::printIcon('task', 'create', "executionID={$plan->id}", $plan, 'list', '', '', $class, false, "data-app='execution'");

                if($plan->grade == 1 && $this->isCreateTask($plan->id))
                {
                    common::printIcon('programplan', 'create', "program={$plan->parent}&productID=$plan->product&planID=$plan->id", $plan, 'list', 'split', '', '', '', '', $this->lang->programplan->createSubPlan);
                }
                else
                {
                    $disabled = ($plan->grade == 2) ? ' disabled' : '';
                    echo html::a('javascript:alert("' . $this->lang->programplan->error->createdTask . '");', '<i class="icon-programplan-create icon-split"></i>', '', 'class="btn ' . $disabled . '"');
                }

                common::printIcon('programplan', 'edit', "planID=$plan->id&projectID=$projectID", $plan, 'list', '', '', 'iframe', true);

                $disabled = !empty($plan->children) ? ' disabled' : '';
                if(common::hasPriv('execution', 'delete', $plan))
                {
                    common::printIcon('execution', 'delete', "planID=$plan->id&confirm=no", $plan, 'list', 'trash', 'hiddenwin' , $disabled, '', '', $this->lang->programplan->delete);
                }
                break;
            }
            echo '</td>';
        }
    }

    /**
     * Is create task.
     *
     * @param  int    $planID
     * @access public
     * @return bool
     */
    public function isCreateTask($planID)
    {
        if(empty($planID)) return true;

        $task = $this->dao->select('*')->from(TABLE_TASK)->where('execution')->eq($planID)->andWhere('deleted')->eq('0')->limit(1)->fetch();
        return empty($task) ? true : false;
    }

    /**
     * Get parent stage's children types.
     *
     * @param  int    $parentID
     * @access public
     * @return array
     */
    public function getParentChildrenTypes($parentID)
    {
        if(empty($parentID)) return true;

        return $this->dao->select('DISTINCT type')->from(TABLE_EXECUTION)->where('parent')->eq($parentID)->andWhere('deleted')->eq('0')->fetchPairs('type');
    }

    /**
     * Is clickable.
     *
     * @param  int    $plan
     * @param  int    $action
     * @static
     * @access public
     * @return bool
     */
    public static function isClickable($plan, $action)
    {
        $action = strtolower($action);

        if($action == 'create')
        {
            global $dao;
            $task = !empty($plan->id) ? $dao->select('*')->from(TABLE_TASK)->where('execution')->eq($plan->id)->andWhere('deleted')->eq('0')->limit(1)->fetch() : '';
            return empty($task) ? true : false;
        }

        return true;
    }

    /**
     * Check name unique.
     *
     * @param  array  $names
     * @access public
     * @return bool
     */
    public function checkNameUnique($names)
    {
        $names = array_filter($names);
        if(count(array_unique($names)) != count($names)) return false;
        return true;
    }

    /**
     * Check code unique.
     *
     * @param array $codes
     * @param array $planIDList
     * @access public
     * @return mix
     */
    public function checkCodeUnique($codes, $planIDList)
    {
        $codes = array_filter($codes);

        $sameCodes = $this->dao->select('code')->from(TABLE_EXECUTION)
            ->where('type')->in('sprint,stage,kanban')
            ->andWhere('deleted')->eq('0')
            ->andWhere('code')->in($codes)
            ->beginIF($planIDList)->andWhere('id')->notin($planIDList)->fi()
            ->fetchPairs('code');
        if(count(array_unique($codes)) != count($codes)) $sameCodes += array_diff_assoc($codes, array_unique($codes));
        return $sameCodes ? $sameCodes : true;
    }

    /**
     * Get the stage set to milestone.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getMilestones($projectID = 0)
    {
        $milestones = $this->dao->select('id, path')->from(TABLE_PROJECT)
            ->where('project')->eq($projectID)
            ->andWhere('type')->eq('stage')
            ->andWhere('milestone')->eq(1)
            ->andWhere('deleted')->eq(0)
            ->orderBy('begin_desc,path')
            ->fetchPairs();
        return $this->formatMilestones($milestones, $projectID);
    }

    /**
     * Get milestone by product.
     *
     * @param  int    $productID
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getMilestoneByProduct($productID, $projectID)
    {
        $milestones = $this->dao->select('t1.id, t1.path')->from(TABLE_PROJECT)->alias('t1')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id=t2.project')
            ->where('t2.product')->eq($productID)
            ->andWhere('t1.project')->eq($projectID)
            ->andWhere('t1.type')->eq('stage')
            ->andWhere('t1.milestone')->eq(1)
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('t1.begin asc,path')
            ->fetchPairs();
        return $this->formatMilestones($milestones, $projectID);
    }

    /**
     * Format milestones use '/'.
     *
     * @param  array  $milestones
     * @param  int    $projectID
     * @access public
     * @return array
     */
    private function formatMilestones($milestones, $projectID)
    {
        $allStages = $this->dao->select('id,name')->from(TABLE_EXECUTION)
            ->where('project')->eq($projectID)
            ->andWhere('type')->notin('program,project')
            ->fetchPairs();
        foreach($milestones as $id => $path)
        {
            $paths = explode(',', trim($path, ','));
            $stageName = '';
            foreach($paths as $stage)
            {
                if(isset($allStages[$stage])) $stageName .= '/' . $allStages[$stage];
            }
            $milestones[$id] = trim($stageName, '/');
        }

        return $milestones;
    }

    /**
     * Get parent stage list.
     *
     * @param  int    $executionID
     * @param  int    $planID
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getParentStageList($executionID, $planID, $productID)
    {
        $parentStage = $this->dao->select('t2.id, t2.name')->from(TABLE_PROJECTPRODUCT)
            ->alias('t1')->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.project')->eq($executionID)
            ->andWhere('t2.type')->eq('stage')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t2.path')->notlike("%,$planID,%")
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->sprints)->fi()
            ->orderBy('t2.id desc')
            ->fetchPairs();

        $plan = $this->getByID($planID);
        foreach($parentStage as $key => $stage)
        {
            $isCreate = $this->isCreateTask($key);
            if($isCreate === false and $key != $plan->parent) unset($parentStage[$key]);

            $parentTypes = $this->getParentChildrenTypes($key);
            if($plan->type == 'stage' and (isset($parentTypes['sprint']) or isset($parentTypes['kanban']))) unset($parentStage[$key]);
            if(($plan->type == 'sprint' or $plan->type == 'kanban') and isset($parentTypes['stage'])) unset($parentStage[$key]);
        }
        $parentStage[0] = $this->lang->programplan->emptyParent;
        ksort($parentStage);

        return $parentStage;
    }

    /**
     * Compute stage status.
     *
     * @param  int    $stage
     * @param  string $action
     * @param  bool   $isParent
     * @access public
     * @return void
     */
    public function computeProgress($stageID, $action = '', $isParent = false)
    {
        $stage = $this->loadModel('execution')->getByID($stageID);
        if(empty($stage) or empty($stage->path) or $stage->type != 'stage') return false;

        $this->loadModel('execution');
        $this->loadModel('action');
        $action       = strtolower($action);
        $parentIdList = explode(',', trim($stage->path, ','));
        $parentIdList = array_reverse($parentIdList);
        foreach($parentIdList as $id)
        {
            $parent = $this->execution->getByID($id);
            if($parent->type != 'stage' or (!$isParent and $id == $stageID)) continue;

            $statusCount = array();
            $children    = $this->execution->getChildExecutions($parent->id);
            $allChildren = $this->dao->select('id')->from(TABLE_EXECUTION)->where('deleted')->eq(0)->andWhere('path')->like("{$parent->path}%")->andWhere('id')->ne($id)->fetchPairs();
            $startTasks  = $this->dao->select('count(1) as count')->from(TABLE_TASK)->where('deleted')->eq(0)->andWhere('execution')->in($allChildren)->andWhere('consumed')->ne(0)->fetch('count');
            foreach($children as $childID => $childExecution) $statusCount[$childExecution->status] = empty($statusCount[$childExecution->status]) ? 1 : $statusCount[$childExecution->status] ++;

            if(isset($statusCount['wait']) and count($statusCount) == 1 and helper::isZeroDate($parent->realBegan) and $startTasks == 0)
            {
                if($parent->status != 'wait')
                {
                    $newParent = new stdclass();
                    $newParent->status         = 'wait';
                    $newParent->realBegan      = '';
                    $newParent->closedDate     = '';
                    $newParent->closedBy       = '';
                    $newParent->canceledDate   = '';
                    $newParent->canceledBy     = '';
                    $newParent->suspendedDate  = '';
                    $newParent->lastEditedBy   = $this->app->user->account;
                    $newParent->lastEditedDate = helper::now();
                    $parentAction = 'waitbychild';
                }
            }
            elseif(isset($statusCount['closed']) and count($statusCount) == 1)
            {
                if($parent->status != 'closed')
                {
                    $newParent = new stdclass();
                    $newParent->status         = 'closed';
                    $newParent->closedDate     = helper::now();
                    $newParent->closedBy       = $this->app->user->account;
                    $newParent->lastEditedBy   = $this->app->user->account;
                    $newParent->lastEditedDate = helper::now();
                    $parentAction = 'closedbychild';
                }
            }
            elseif(isset($statusCount['suspended']) and (count($statusCount) == 1 or (isset($statusCount['closed']) and count($statusCount) == 2)))
            {
                if($parent->status != 'suspended')
                {
                    $newParent = new stdclass();
                    $newParent->status         = 'suspended';
                    $newParent->suspendedDate  = helper::now();
                    $newParent->lastEditedBy   = $this->app->user->account;
                    $newParent->lastEditedDate = helper::now();
                    $parentAction = 'suspendedbychild';
                }
            }
            else
            {
                if($parent->status != 'doing')
                {
                    $newParent = new stdclass();
                    $newParent->status         = 'doing';
                    $newParent->realBegan      = helper::today();
                    $newParent->closedDate     = '';
                    $newParent->closedBy       = '';
                    $newParent->canceledDate   = '';
                    $newParent->canceledBy     = '';
                    $newParent->suspendedDate  = '';
                    $newParent->lastEditedBy   = $this->app->user->account;
                    $newParent->lastEditedDate = helper::now();
                    $parentAction = $parent->status == 'wait' ?'startbychildstart' : 'startbychild' . $action;
                }
            }

            if(isset($newParent))
            {
                $this->dao->update(TABLE_EXECUTION)->data($newParent)->where('id')->eq($id)->exec();
                $this->action->create('execution', $id, $parentAction, '', $parentAction);
            }
            unset($newParent, $parentAction);
        }
    }

    /**
     * Check if the stage is a leaf stage.
     *
     * @param  int    $planID
     * @access public
     * @return bool
     */
    public function checkLeafStage($planID)
    {
        $subStageList = $this->dao->select('id')->from(TABLE_EXECUTION)->where('parent')->eq($planID)->andWhere('deleted')->eq(0)->fetchAll('id');

        return $subStageList ? false : true;
    }

    /**
     * Change whether it is the top stage.
     *
     * @param  int    $planID
     * @access public
     * @return bool
     */
    public function checkTopStage($planID)
    {
        $parentID   = $this->dao->select('parent')->from(TABLE_EXECUTION)->where('id')->eq($planID)->fetch('parent');
        $parentType = $this->dao->select('type')->from(TABLE_EXECUTION)->where('id')->eq($parentID)->fetch('type');

        return $parentType == 'project';
    }

    /**
     * Update sub-stage attribute.
     *
     * @param  int    $planID
     * @param  string $attribute
     * @param  bool   $withDeleted
     * @access public
     * @return bool
     */
    public function updateSubStageAttr($planID, $attribute, $withDeleted = false)
    {
        if($attribute == 'mix') return true;

        $subStageList = $this->dao->select('id')->from(TABLE_EXECUTION)
            ->where('parent')->eq($planID)
            ->beginIF(!$withDeleted)->andWhere('deleted')->eq(0)->fi()
            ->fetchAll('id');
        $this->dao->update(TABLE_EXECUTION)->set('attribute')->eq($attribute)->where('id')->in(array_keys($subStageList))->exec();

        foreach($subStageList as $childID => $subStage)
        {
            $this->updateSubStageAttr($childID, $attribute, $withDeleted);
        }
    }
}
