<?php
declare(strict_types=1);
class bugTao extends bugModel
{
    /**
     * 获取Bug的基础数据。
     * Fetch base info of a bug.
     *
     * @param  int          $bugID
     * @access protected
     * @return object|false
     */
    protected function fetchBaseInfo(int $bugID): object|false
    {
        return $this->dao->select('*')->from(TABLE_BUG)->where('id')->eq($bugID)->fetch();
    }

    /**
     * Get bug details, including all contents of the TABLE_BUG, execution name, associated story name, associated story status, associated story version, associated task name, and associated plan name.
     * 获取bug的详情，包含bug表的所有内容、所属执行名称、关联需求名称、关联需求状态、关联需求版本、关联任务名称、关联计划名称
     *
     * @param  int          $bugID
     * @access protected
     * @return object|false
     */
    protected function fetchBugInfo(int $bugID): object|false
    {
        return $this->dao->select('t1.*, t2.name AS executionName, t3.title AS storyTitle, t3.status AS storyStatus, t3.version AS latestStoryVersion, t4.name AS taskName, t5.title AS planName')
            ->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_STORY)->alias('t3')->on('t1.story = t3.id')
            ->leftJoin(TABLE_TASK)->alias('t4')->on('t1.task = t4.id')
            ->leftJoin(TABLE_PRODUCTPLAN)->alias('t5')->on('t1.plan = t5.id')
            ->where('t1.id')->eq((int)$bugID)->fetch();
    }

    /**
     * Get bug list by browse type.
     * 通过浏览类型获取 bug 列表。
     *
     * @param  string     $browseType
     * @param  array      $productIdList
     * @param  int        $projectID
     * @param  int[]      $executionIdList
     * @param  int|string $branch
     * @param  array      $moduleIdList
     * @param  int        $queryID
     * @param  string     $orderBy
     * @param  object     $pager
     * @access protected
     * @return array
     */
    protected function getListByBrowseType(string $browseType, array $productIdList, int $projectID, array $executionIdList, int|string $branch, array $moduleIdList, int $queryID, string $orderBy, object $pager = null): array
    {
        $browseType = strtolower($browseType);

        if($browseType == 'bysearch')    return $this->getBySearch($productIdList, $branch, $projectID, $queryID, $orderBy, '', $pager, $projectID);
        if($browseType == 'needconfirm') return $this->getNeedConfirmList($productIdList, $projectID, $executionIdList, $branch, $moduleIdList, $orderBy, $pager);

        $lastEditedDate = '';
        if($browseType == 'longlifebugs') $lastEditedDate = date(DT_DATE1, time() - $this->config->bug->longlife * 24 * 3600);

        $bugIdListAssignedByMe = array();
        if($browseType == 'assignedbyme') $bugIdListAssignedByMe = $this->dao->select('objectID')->from(TABLE_ACTION)->where('objectType')->eq('bug')->andWhere('action')->eq('assigned')->andWhere('actor')->eq($this->app->user->account)->fetchPairs();

        $bugList = $this->dao->select("*, IF(`pri` = 0, {$this->config->maxPriValue}, `pri`) AS priOrder, IF(`severity` = 0, {$this->config->maxPriValue}, `severity`) AS severityOrder")->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->andWhere('product')->in($productIdList)
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->beginIF($this->app->tab !== 'qa')->andWhere('execution')->in($executionIdList)->fi()
            ->beginIF($branch !== 'all')->andWhere('branch')->in($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('module')->in($moduleIdList)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('project')->in('0,' . $this->app->user->view->projects)->fi()

            ->beginIF($browseType == 'assigntome')->andWhere('assignedTo')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'openedbyme')->andWhere('openedBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'resolvedbyme')->andWhere('resolvedBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'assigntonull')->andWhere('assignedTo')->eq('')->fi()
            ->beginIF($browseType == 'unconfirmed')->andWhere('confirmed')->eq(0)->fi()
            ->beginIF($browseType == 'unclosed')->andWhere('status')->ne('closed')->fi()
            ->beginIF($browseType == 'unresolved')->andWhere('status')->eq('active')->fi()
            ->beginIF($browseType == 'toclosed')->andWhere('status')->eq('resolved')->fi()
            ->beginIF($browseType == 'postponedbugs')->andWhere('resolution')->eq('postponed')->fi()
            ->beginIF($browseType == 'review')->andWhere("FIND_IN_SET('{$this->app->user->account}', reviewers)")->fi()

            ->beginIF($browseType == 'longlifebugs')
            ->andWhere('lastEditedDate')->lt($lastEditedDate)
            ->andWhere('openedDate')->lt($lastEditedDate)
            ->andWhere('status')->ne('closed')
            ->fi()

            ->beginIF($browseType == 'overduebugs')
            ->andWhere('status')->eq('active')
            ->andWhere('deadline')->lt(helper::today())
            ->fi()

            ->beginIF($browseType == 'assignedbyme')
            ->andWhere('status')->ne('closed')
            ->andWhere('id')->in($bugIdListAssignedByMe)
            ->fi()

            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugList;
    }

    /**
     * 搜索 bug。
     * Search bugs.
     *
     * @param  array      $productIdList
     * @param  int|string $branch
     * @param  int        $projectID
     * @param  int        $queryID
     * @param  string     $excludeBugs
     * @param  string     $orderBy
     * @param  object     $pager
     * @access public
     * @return array
     */
    protected function getBySearch(array $productIdList, int|string $branch = 0, int $projectID = 0, int $queryID = 0, string $excludeBugs = '', string $orderBy = '', object $pager = null): array
    {
        $bugQuery = $this->processSearchQuery($queryID, $productIdList, (string)$branch);

        return $this->dao->select("*, IF(`pri` = 0, {$this->config->maxPriValue}, `pri`) AS priOrder, IF(`severity` = 0, {$this->config->maxPriValue}, `severity`) AS severityOrder")->from(TABLE_BUG)
            ->where($bugQuery)
            ->andWhere('deleted')->eq('0')
            ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()

            ->beginIF(!$this->app->user->admin)
            ->andWhere('project')->in('0,' . $this->app->user->view->projects)
            ->andWhere('execution')->in('0,' . $this->app->user->view->sprints)
            ->fi()

            ->beginIF($projectID)
            ->andWhere('project', true)->eq($projectID)
            ->orWhere('project')->eq(0)
            ->andWhere('openedBuild')->eq('trunk')
            ->markRight(1)
            ->fi()

            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * 获取需要确认需求变动的 bug 列表。
     * Get bug list that related story need to be confirmed.
     *
     * @param  array      $productIdList
     * @param  int        $projectID
     * @param  int[]      $executionIdList
     * @param  int|string $branch
     * @param  array      $moduleIdList
     * @param  string     $orderBy
     * @param  object     $pager
     * @access protected
     * @return array
     */
    private function getNeedConfirmList(array $productIdList, int $projectID, array $executionIdList, int|string $branch, array $moduleIdList, string $orderBy, object $pager = null): array
    {
        return $this->dao->select("t1.*, t2.title AS storyTitle, IF(t1.`pri` = 0, {$this->config->maxPriValue}, t1.`pri`) AS priOrder, IF(t1.`severity` = 0, {$this->config->maxPriValue}, t1.`severity`) AS severityOrder")->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t2.status')->eq('active')
            ->andWhere('t2.version > t1.storyVersion')
            ->andWhere('t1.product')->in($productIdList)
            ->beginIF($projectID)->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF($this->app->tab !== 'qa')->andWhere('t1.execution')->in($executionIdList)->fi()
            ->beginIF($branch !== 'all')->andWhere('t1.branch')->in($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t1.module')->in($moduleIdList)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t1.project')->in('0,' . $this->app->user->view->projects)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get cases created by bug.
     * 获取bug建的用例。
     *
     * @param  int       $bugID
     * @access protected
     * @return array
     */
    protected function getCasesFromBug(int $bugID): array
    {
        return $this->dao->select('id, title')->from(TABLE_CASE)->where('`fromBug`')->eq($bugID)->fetchPairs();
    }

    /**
     * Get an array of id and title pairs by buglist.
     * 传入一个buglist，获得bug的id和title键值对数组。
     *
     * @param  string|array $bugList
     * @access protected
     * @return array
     */
    protected function getBugPairsByList(string|array $bugList): array
    {
        return $this->dao->select('id,title')->from(TABLE_BUG)->where('id')->in($bugList)->fetchPairs();
    }

    /**
     * Get object title/name base on the params.
     * 根据传入的参数，获取对象名称。
     *
     * @param  int       $objectID
     * @param  string    $table
     * @param  string    $field
     * @access protected
     * @return string
     */
    protected function getNameFromTable(int $objectID, string $table, string $field): string
    {
        return $this->dao->findById($objectID)->from($table)->fields($field)->fetch($field);
    }

    /**
     * Call checkDelayBug in foreach to check if the bug is delay.
     * 循环调用checkDelayBug，检查bug是否延期
     *
     * @param  array     $bugs
     * @access protected
     * @return object[]
     */
    protected function batchAppendDelayedDays(array $bugs): array
    {
        foreach($bugs as $bug) $this->appendDelayedDays($bug);

        return $bugs;
    }

    /**
     * If the bug is delayed, add the bug->delay field to show the delay time (day).
     * 添加bug->delay字段，内容为延期的时长（天），不延期则为0
     *
     * @param  object    $bug
     * @access protected
     * @return object
     */
    protected function appendDelayedDays(object $bug): object
    {
        if(helper::isZeroDate($bug->deadline)) return $bug;

        $delay = 0;
        if($bug->resolvedDate and !helper::isZeroDate($bug->resolvedDate))
        {
            $delay = helper::diffDate(substr($bug->resolvedDate, 0, 10), $bug->deadline);
        }
        elseif($bug->status == 'active')
        {
            $delay = helper::diffDate(helper::today(), $bug->deadline);
        }

        if($delay > 0) $bug->delay = $delay;

        return $bug;
    }

    /**
     * 处理搜索的查询语句。
     * Process search query.
     *
     * @param  int        $queryID
     * @param  array      $productIdList
     * @param  int|string $branch
     * @access protected
     * @return string
     */
    private function processSearchQuery(int $queryID, array $productIdList, string $branch): string
    {
        /* 设置 bug 查询的 session。*/
        /* Set the session of bug query. */
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);

            if($query)
            {
                $this->session->set('bugQuery', $query->sql);
                $this->session->set('bugForm', $query->form);
            }
        }
        if($this->session->bugQuery === false) $this->session->set('bugQuery', ' 1 = 1');

        $bugQuery = $this->getBugQuery($this->session->bugQuery);

        /* 在 bug 的查询中加上产品的限制。*/
        /* Append product condition in bug query. */
        if(strpos($bugQuery, '`product`') !== false)
        {
            $productParis  = $this->loadModel('product')->getPairs('', 0, '', 'all');
            $productIdList = array_keys($productParis);
        }
        $productIdList = implode(',', $productIdList);
        $bugQuery .= ' AND `product` IN (' . $productIdList . ')';

        /* 如果查询中没有分支条件，获取主干和当前分支下的 bug。*/
        /* If there is no branch condition in query, append it that is main and current . */
        $branch = trim($branch, ',');
        if(strpos($branch, ',') !== false) $branch = str_replace(',', "','", $branch);
        if($branch !== 'all' && strpos($bugQuery, '`branch` =') === false) $bugQuery .= " AND `branch` in('0','$branch')";

        /* 将所有分支条件替换成 1。*/
        /* Replace the condition of all branch to 1. */
        $allBranch = "`branch` = 'all'";
        if(strpos($bugQuery, $allBranch) !== false) $bugQuery = str_replace($allBranch, '1', $bugQuery);

        return $bugQuery;
    }

    /**
     * 更新bug根据id。
     * Update bug by id.
     *
     * @param  int       $bugID
     * @param  object    $bug
     * @access protected
     * @return bool
     */
    protected function updateByID(int $bugID, object $bug): bool
    {
        if(!isset($bug->lastEditedBy))   $bug->lastEditedBy = $this->app->user->account;
        if(!isset($bug->lastEditedDate)) $bug->lastEditedDate = helper::now();

        $this->dao->update(TABLE_BUG)->data($bug)->autoCheck()->where('id')->eq($bugID)->exec();
        return !dao::isError();
    }
}
