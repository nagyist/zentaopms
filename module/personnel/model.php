<?php
/**
 * The model file of personnel of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     personnel
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class personnelModel extends model
{
    /**
     * Access to program set input staff.
     *
     * @param  int       $programID
     * @param  int       $deptID
     * @param  string    $browseType
     * @param  string    $orderBy
     * @param  int       $queryID
     * @param  object    $pager
     * @access public
     * @return array
     */
    public function getAccessiblePersonnel($programID = 0, $deptID = 0, $browseType = 'all', $queryID = 0, $pager)
    {
        $accessibleQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('accessibleQuery', $query->sql);
                $this->session->set('accessibleForm', $query->form);
            }
            if($this->session->accessibleQuery == false) $this->session->set('accessibleQuery', ' 1=1');
            $accessibleQuery = $this->session->accessibleQuery;
        }

        /* Determine who can be accessed based on access control. */
        $program = $this->loadModel('program')->getPGMByID($programID);
        $personnelList = array();
        if($program->acl != 'open')
        {
            $personnelList = $this->dao->select('t2.id,t2.dept,t2.account,t2.role,t2.realname,t2.gender')->from(TABLE_USERVIEW)->alias('t1')
                ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account=t2.account')
                ->where("CONCAT(',', t1.programs, ',')")->like("%,$programID,%")
                ->beginIF($deptID > 0)->andWhere('t2.dept')->eq($deptID)->fi()
                ->beginIF($browseType == 'bysearch')->andWhere($accessibleQuery)->fi()
                ->page($pager)
                ->fetchAll();
        }

        return $personnelList;
    }

    /**
     * Access to program set input staff.
     *
     * @param  int       $programID
     * @access public
     * @return array
     */
    public function getInputPersonnel($programID = 0)
    {
        $personnelList = array();

        /* Get all projects under the current program. */
        $projects = $this->dao->select('id,model,type,parent,path,name')->from(TABLE_PROJECT)
            ->where('type')->eq('project')
            ->andWhere('path')->like("%,$programID,%")
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchAll('id');
        if(empty($projects)) return $personnelList;

        $accountPairs = $this->getInvolvedProjects($projects);
        if(empty($accountPairs)) return $personnelList;

        $executionPairs   = $this->getInvolvedExecutions($projects);
        $taskInput        = $this->getProjectTaskInput($projects, $accountPairs);
        $bugAndStoryInput = $this->getBugAndStoryInput($accountPairs, $programID);
        if(isset($this->config->maxVersion))
        {
            $issueInput       = $this->getIssueInput($accountPairs, $projects);
            $riskInput        = $this->getRiskInput($accountPairs, $projects);
        }
        $userPairs        = $this->loadModel('user')->getListByAccounts(array_keys($accountPairs), 'account');
        foreach($userPairs as $user) $user->role = zget($this->lang->user->roleList, $user->role, $user->role);

        foreach($accountPairs as $account => $projects)
        {
            $personnelList[$account]['realname']   = $userPairs[$account]->realname;
            $personnelList[$account]['account']    = $account;
            $personnelList[$account]['role']       = $userPairs[$account]->role;
            $personnelList[$account]['projects']   = $projects;
            $personnelList[$account]['executions'] = zget($executionPairs, $account, 0);

            $personnelList[$account] += $taskInput[$account];
            $personnelList[$account] += $bugAndStoryInput[$account];
            if(isset($this->config->maxVersion))
            {
                $personnelList[$account] += $issueInput[$account];
                $personnelList[$account] += $riskInput[$account];
            }
        }

        return $personnelList;
    }

    /**
     * Get user project risk input.
     *
     * @param  array     $accounts
     * @param  object    $projects
     * @access public
     * @return array
     */
    public function getRiskInput($accounts, $projects)
    {
        $risks = $this->dao->select('id,createdBy,resolvedBy,status,assignedTo')->from(TABLE_RISK)
            ->where('PRJ')->in(array_keys($projects))
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');

        /* Initialization personnel risks. */
        $putInto = array();
        foreach($accounts as $account => $project)
        {
            $putInto[$account]['createdRisk']  = 0;
            $putInto[$account]['resolvedRisk'] = 0;
            $putInto[$account]['pendingRisk']  = 0;
        }

        foreach($risks as $risk)
        {
            if($risk->createdBy && isset($putInto[$risk->createdBy])) $putInto[$risk->createdBy]['createdRisk'] += 1;
            if($risk->resolvedBy && isset($putInto[$risk->resolvedBy])) $putInto[$risk->resolvedBy]['resolvedRisk'] += 1;
            if($risk->assignedTo && $risk->status == 'active' && isset($putInto[$risk->assignedTo])) $putInto[$risk->assignedTo]['pendingRisk'] += 1;
        }

        return $putInto;
    }

    /**
     * Get user project issue input.
     *
     * @param  array     $accounts
     * @param  object    $projects
     * @access public
     * @return array
     */
    public function getIssueInput($accounts, $projects)
    {
        $issues = $this->dao->select('id,createdBy,resolvedBy,status,assignedTo')->from(TABLE_ISSUE)
            ->where('PRJ')->in(array_keys($projects))
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');

        /* Initialization personnel issues. */
        $putInto = array();
        foreach($accounts as $account => $project)
        {
            $putInto[$account]['createdIssue']  = 0;
            $putInto[$account]['resolvedIssue'] = 0;
            $putInto[$account]['pendingIssue']  = 0;
        }

        foreach($issues as $issue)
        {
            if($issue->createdBy && isset($putInto[$issue->createdBy])) $putInto[$issue->createdBy]['createdIssue'] += 1;
            if($issue->resolvedBy && isset($putInto[$issue->resolvedBy])) $putInto[$issue->resolvedBy]['resolvedIssue'] += 1;
            if($issue->assignedTo && in_array($issue->status, array('unconfirmed', 'confirmed', 'active')) && isset($putInto[$issue->assignedTo])) $putInto[$issue->assignedTo]['pendingIssue'] += 1;
        }

        return $putInto;
    }

    /**
     * Get user bug and story input.
     *
     * @param  array     $accounts
     * @param  int       $programID
     * @access public
     * @return array
     */
    public function getBugAndStoryInput($accounts, $programID)
    {
        $productPairs = $this->loadModel('product')->getPairs('', $programID);
        $productKeys  = array_keys($productPairs);

        $bugs = $this->dao->select('id,status,openedBy,assignedTo,resolvedBy')->from(TABLE_BUG)
            ->where('product')->in($productKeys)
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');

        $requirement = $this->dao->select('openedBy, count(id) as number')->from(TABLE_STORY)
            ->where('product')->in($productKeys)
            ->andWhere('openedBy')->in(array_keys($accounts))
            ->andWhere('type')->eq('requirement')
            ->andWhere('deleted')->eq(0)
            ->groupBy('openedBy')
            ->fetchPairs('openedBy');

        $story = $this->dao->select('openedBy, count(id) as number')->from(TABLE_STORY)
            ->where('product')->in($productKeys)
            ->andWhere('openedBy')->in(array_keys($accounts))
            ->andWhere('type')->eq('story')
            ->andWhere('deleted')->eq(0)
            ->groupBy('openedBy')
            ->fetchPairs('openedBy');

        /* Initialize bugs and requirements related to personnel. */
        $putInto = array();
        foreach($accounts as $account => $project)
        {
            $putInto[$account]['createdBug']  = 0;
            $putInto[$account]['resolvedBug'] = 0;
            $putInto[$account]['pendingBug']  = 0;
            $putInto[$account]['UR']          = 0;
            $putInto[$account]['SR']          = 0;
        }

        foreach($requirement as $account => $number) $putInto[$account]['UR'] = $number;
        foreach($story as $account => $number)       $putInto[$account]['SR'] = $number;

        foreach($bugs as $bug)
        {
            if($bug->openedBy && isset($putInto[$bug->openedBy])) $putInto[$bug->openedBy]['createdBug'] += 1;
            if($bug->resolvedBy && isset($putInto[$bug->resolvedBy])) $putInto[$bug->resolvedBy]['resolvedBug'] += 1;
            if($bug->assignedTo && $bug->status == 'active' && isset($putInto[$bug->assignedTo])) $putInto[$bug->assignedTo]['pendingBug'] += 1;
        }
        return $putInto;
    }

    /**
     * Get the project member accounts and the number of participating projects.
     *
     * @param  object    $projects
     * @access public
     * @return array
     */
    public function getInvolvedProjects($projects)
    {
        return $this->dao->select('account, count(root) as projects')->from(TABLE_TEAM)
            ->where('root')->in(array_keys($projects))
            ->andWhere('type')->eq('project')
            ->groupBy('account')
            ->fetchPairs('account');
    }

    /**
     * Gets the iteration or phase under the project.
     *
     * @param  object    $projects
     * @access public
     * @return array
     */
    public function getInvolvedExecutions($projects)
    {
        $executions = $this->dao->select('id')->from(TABLE_PROJECT)
            ->where('type')->in('stage,sprint')
            ->andWhere('project')->in(array_keys($projects))
            ->andWhere('deleted')->eq('0')
            ->fetchPairs('id');

        return $this->dao->select('account, count(root) as executions')->from(TABLE_TEAM)
            ->where('root')->in(array_keys($executions))
            ->andWhere('type')->in('stage,sprint')
            ->groupBy('account')
            ->fetchPairs('account');
    }

    /**
     * Get project task inputs.
     *
     * @param  object    $projects
     * @param  array     $accounts
     * @access public
     * @return array
     */
    public function getProjectTaskInput($projects, $accounts)
    {
        $tasks = $this->dao->select('id,status,openedBy,finishedBy,assignedTo,PRJ')->from(TABLE_TASK)
          ->where('PRJ')->in(array_keys($projects))
          ->andWhere('deleted')->eq('0')
          ->fetchAll('id');

        /* Initialize personnel related tasks. */
        $putInto = array();
        foreach($accounts as $account => $project)
        {
            $putInto[$account]['createdTask']  = 0;
            $putInto[$account]['finishedTask'] = 0;
            $putInto[$account]['pendingTask']  = 0;
            $putInto[$account]['consumedTask'] = 0;
            $putInto[$account]['leftTask']     = 0;
        }

        /* Number of tasks per person. */
        $userTasks = array();
        foreach($tasks as $task)
        {
            if($task->openedBy && isset($putInto[$task->openedBy]))
            {
                $putInto[$task->openedBy]['createdTask'] += 1;
                $userTasks[$task->openedBy][$task->id]    = $task->id;
            }

            if($task->finishedBy && isset($putInto[$task->finishedBy]))
            {
                $putInto[$task->finishedBy]['finishedTask'] += 1;
                $userTasks[$task->finishedBy][$task->id]     = $task->id;
            }

            if($task->assignedTo && $task->status == 'wait' && isset($putInto[$task->assignedTo]))
            {
                $putInto[$task->assignedTo]['pendingTask'] += 1;
                $userTasks[$task->assignedTo][$task->id]    = $task->id;
            }
        }

        /* The number of hours per person. */
        $userHours = array();
        if(isset($this->config->qcVersion) || isset($this->config->proVersion) || isset($this->config->bizVersion))
        {
            $userHours = $this->getUserEffortHours($userTasks);
        }
        else
        {
            $userHours = $this->getUserHours($userTasks);
        }

        foreach($userHours as $account => $hours)
        {
            $putInto[$account]['leftTask']     = $hours->left;
            $putInto[$account]['consumedTask'] = $hours->consumed;
        }

        return $putInto;
    }

    /**
     * Get user hours.
     *
     * @param  object    $userTasks
     * @access public
     * @return object
     */
    public function getUserEffortHours($userTasks)
    {
        $accounts   = array();
        $taskIDList = array();
        foreach($userTasks as $account => $taskID)
        {
            $accounts[] = $account;
            $taskIDList = array_merge($taskIDList, $taskID);
        }

        $userHours = $this->dao->select('account, sum(`left`) as `left`, sum(consumed) as consumed')->from(TABLE_EFFORT)
            ->where('account')->in($accounts)
            ->andWhere('objectType')->eq('task')
            ->andWhere('objectID')->in($taskIDList)
            ->groupBy('account')
            ->fetchAll('account');
        return $userHours;
    }

    /**
     * Get user hours.
     *
     * @param  object    $userTasks
     * @access public
     * @return object
     */
    public function getUserHours($userTasks)
    {
        $accounts   = array();
        $taskIDList = array();
        foreach($userTasks as $account => $taskID)
        {
            $accounts[] = $account;
            $taskIDList = array_merge($taskIDList, $taskID);
        }

        $userHours = $this->dao->select('account, sum(`left`) as `left`, sum(consumed) as consumed')->from(TABLE_TASKESTIMATE)
            ->where('account')->in($accounts)
            ->andWhere('task')->in($taskIDList)
            ->groupBy('account')
            ->fetchAll('account');
        return $userHours;
    }

    /**
     * Access to data on stages and sprints.
     *
     * @param  object    $projects
     * @access public
     * @return array
     */
    public function getSprintAndStage($projects)
    {
        $teams = $this->dao->select('t1.id,t1.root,t1.type,t1.role,t1.account,t2.realname')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account=t2.account')
            ->where('t1.root')->in($rootIDList)
            ->andWhere('t1.type')->in('stage,sprint')
            ->fetchGroup('root', 'id');
    }

    /**
     * Access to program set input staff.
     *
     * @param  int       $objectID
     * @param  string    $objectType  program|project|product|sprint
     * @param  string    $orderBy
     * @param  object    $pager
     * @access public
     * @return array
     */
    public function getWhitelist($objectID = 0, $objectType = '', $orderBy = 'id_desc', $pager = '')
    {
        return $this->dao->select('t1.id,t1.account,t2.realname,t2.dept,t2.role,t2.phone,t2.qq,t2.weixin,t2.email')->from(TABLE_ACL)->alias('t1')
            ->leftjoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
            ->where('t1.objectID')->eq($objectID)
            ->andWhere('t1.type')->eq('whitelist')
            ->andWhere('t1.objectType')->eq($objectType)
            ->orderBy($orderBy)
            ->beginIF(!empty($pager))->page($pager)->fi()
            ->fetchAll();
    }

    /**
     * Get whitelisted accounts.
     *
     * @param  int    $objectID
     * @param  string $objectType
     * @access public
     * @return array
     */
    public function getWhitelistAccount($objectID = 0, $objectType = '')
    {
        return $this->dao->select('account')->from(TABLE_ACL)->where('objectID')->eq($objectID)->andWhere('objectType')->eq($objectType)->fetchPairs('account');
    }

    /**
     * Adding users to access control lists.
     *
     * @param  array   $users
     * @param  string  $objectType  program|project|product|sprint
     * @param  int     $objectID
     * @param  string  $type    whitelist|blacklist
     * @param  string  $source  upgrade|add|sync
     * @access public
     * @return void
     */
    public function updateWhitelist($users = array(), $objectType = '', $objectID = 0, $type = 'whitelist', $source = 'add')
    {
        $oldWhitelist = $this->dao->select('account,objectType,objectID,type,source')->from(TABLE_ACL)->where('objectID')->eq($objectID)->andWhere('objectType')->eq($objectType)->fetchAll('account');
        $this->dao->delete()->from(TABLE_ACL)->where('objectID')->eq($objectID)->andWhere('objectType')->eq($objectType)->exec();

        $users = array_filter($users);
        $users = array_unique($users);
        $accounts = array();
        foreach($users as $account)
        {
            if(isset($oldWhitelist[$account]))
            {
                $this->dao->insert(TABLE_ACL)->data($oldWhitelist[$account])->exec();
                $accounts[$account] = $account;
                continue;
            }

            $acl             = new stdClass();
            $acl->account    = $account;
            $acl->objectType = $objectType;
            $acl->objectID   = $objectID;
            $acl->type       = $type;
            $acl->source     = $source;
            $this->dao->insert(TABLE_ACL)->data($acl)->autoCheck()->exec();
            if(!dao::isError()) $this->loadModel('user')->updateUserView($acl->objectID, $acl->objectType, $acl->account);
            $accounts[$account] = $account;
        }

        $whitelist = ',' . implode(',', $accounts);
        $objectTable = $objectType == 'product' ? TABLE_PRODUCT : TABLE_PROJECT;
        $this->dao->update($objectTable)->set('whitelist')->eq($whitelist)->where('id')->eq($objectID)->exec();

        $deletedAccounts = array();
        foreach($oldWhitelist as $account => $whitelist)
        {
            if(!isset($accounts[$account])) $deletedAccounts[] = $account;
        }

        /* Synchronization of people from the product whitelist to the program set. */
        if($objectType == 'product')
        {
            $product = $this->loadModel('product')->getById($objectID);
            if(empty($product)) return false;

            $programWhitelist = $this->getWhitelistAccount($product->program, 'program');
            $newWhitelist     = array_merge($programWhitelist, $accounts);
            $source           = $source == 'upgrade' ? 'upgrade' : 'sync';
            $this->updateWhitelist($newWhitelist, 'program', $product->program, 'whitelist', $source);

            /* Removal of persons from centralized program whitelisting. */
            foreach($deletedAccounts as $account) $this->deleteProgramWhitelist($objectID, $account);
        }

        /* Synchronization of people from the sprint white list to the project. */
        if($objectType == 'sprint')
        {
            $sprint = $this->dao->select('id,project')->from(TABLE_PROJECT)->where('id')->eq($objectID)->fetch();
            if(empty($sprint)) return false;

            $projectWhitelist = $this->getWhitelistAccount($sprint->project, 'project');
            $newWhitelist     = array_merge($projectWhitelist, $accounts);
            $source           = $source == 'upgrade' ? 'upgrade' : 'sync';
            $this->updateWhitelist($newWhitelist, 'project', $sprint->project, 'whitelist', $source);

            /* Removal of whitelisted persons from projects. */
            foreach($deletedAccounts as $account) $this->deleteProjectWhitelist($objectID, $account);
        }
    }

    /**
     * Adding users to access control lists.
     *
     * @param  string  $objectType  program|project|product|sprint
     * @param  int     $objectID
     * @access public
     * @return void
     */
    public function addWhitelist($objectType = '', $objectID = 0)
    {
        $users = $this->post->accounts;
        $this->updateWhitelist($users, $objectType, $objectID);
    }

    /**
     * Determine whether the user exists in the white list of multiple products.
     *
     * @param  int     $objectID
     * @param  string  $account
     * @access public
     * @return void
     */
    public function deleteProgramWhitelist($objectID = 0, $account = '')
    {
        $program = $this->dao->select('id,program,whitelist')->from(TABLE_PRODUCT)->where('id')->eq($objectID)->fetch();
        if(empty($program)) return false;
        $programID = $program->program;
        $products  = $this->dao->select('id')->from(TABLE_PRODUCT)->where('program')->eq($programID)->andWhere('deleted')->eq('0')->fetchPairs('id');
        $whitelist = $this->dao->select('*')->from(TABLE_ACL)->where('objectID')->in($products)->andWhere('account')->eq($account)->andWhere('objectType')->eq('product')->fetch();

        /* Determine if the user exists in other products in the program set. */
        if(empty($whitelist))
        {
            $newWhitelist = str_replace(',' . $account, '', $program->whitelist);
            $this->dao->update(TABLE_PROGRAM)->set('whitelist')->eq($newWhitelist)->where('id')->eq($programID)->exec();
            $this->dao->delete()->from(TABLE_ACL)->where('objectID')->eq($programID)->andWhere('account')->eq($account)->andWhere('objectType')->eq('program')->exec();
        }
    }

    /**
     * Determine if the user is on a whitelist for multiple sprints
     *
     * @param  int     $objectID
     * @param  string  $account
     * @access public
     * @return void
     */
    public function deleteProjectWhitelist($objectID = 0, $account = '')
    {
        $project = $this->dao->select('id,project,whitelist')->from(TABLE_PROJECT)->where('id')->eq($objectID)->fetch();
        if(empty($project)) return false;
        $projectID = $project->project;
        $sprints   = $this->dao->select('id')->from(TABLE_PROJECT)->where('project')->eq($projectID)->andWhere('deleted')->eq('0')->fetchPairs('id');
        $whitelist = $this->dao->select('*')->from(TABLE_ACL)->where('objectID')->in($sprints)->andWhere('account')->eq($account)->andWhere('objectType')->eq('sprint')->fetch();

        /* Determine if the user exists in other sprints in the project set. */
        if(empty($whitelist))
        {
            $newWhitelist = str_replace(',' . $account, '', $project->whitelist);
            $this->dao->update(TABLE_PROJECT)->set('whitelist')->eq($newWhitelist)->where('id')->eq($projectID)->exec();
            $this->dao->delete()->from(TABLE_ACL)->where('objectID')->eq($projectID)->andWhere('account')->eq($account)->andWhere('objectType')->eq('project')->exec();
        }
    }

    /**
     * Create access links by department.
     *
     * @param  object  $dept
     * @param  int     $programID
     * @access public
     * @return string
     */
    public function createMemberLink($dept = 0, $programID = 0)
    {
        return html::a(helper::createLink('personnel', 'accessible', "program={$programID}&deptID={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID = 0, $actionURL = '')
    {
        $this->config->personnel->accessible->search['actionURL'] = $actionURL;
        $this->config->personnel->accessible->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->personnel->accessible->search);
    }
}
