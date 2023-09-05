<?php
/**
 * The model file of case module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     case
 * @version     $Id: model.php 5108 2013-07-12 01:59:04Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class testcaseModel extends model
{
    /**
     * Set menu.
     *
     * @param  array  $products
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $moduleID
     * @param  int    $suiteID
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function setMenu($products, $productID, $branch = 0, $moduleID = 0, $suiteID = 0, $orderBy = 'id_desc')
    {
        $this->loadModel('qa')->setMenu($products, $productID, $branch, $moduleID, 'case');
    }

    /**
     * 创建一个用例。
     * Create a case.
     *
     * @param  object $case
     * @access public
     * @return bool|int
     */
    public function create(object $case): bool|int
    {
        if(empty($case->product)) $this->config->testcase->create->requiredFields = str_replace('story', '', $this->config->testcase->create->requiredFields);

        /* 插入测试用例。 */
        /* Insert testcase. */
        $this->dao->insert(TABLE_CASE)->data($case, 'steps,expects,files,labels,stepType,needReview,scriptFile,scriptName')
            ->autoCheck()
            ->batchCheck($this->config->testcase->create->requiredFields, 'notempty')
            ->checkFlow()
            ->exec();
        if(dao::isError()) return false;

        $caseID = $this->dao->lastInsertID();

        /* 记录动态。*/
        /* Record log. */
        $this->loadModel('action');
        $this->action->create('case', $caseID, 'Opened');
        if($case->status == 'wait') $this->action->create('case', $caseID, 'submitReview');

        /* 存储上传的文件。 */
        /* Save upload files. */
        $this->config->dangers = '';
        $this->loadModel('file')->saveUpload('testcase', $caseID, 'autoscript', 'script', 'scriptName');
        $this->loadModel('file')->saveUpload('testcase', $caseID);

        $this->loadModel('score')->create('testcase', 'create', $caseID);

        /* 插入用例步骤。 */
        /* Insert testcase steps. */
        $this->testcaseTao->insertSteps($caseID, $case->steps, $case->expects, $case->stepType);

        if(dao::isError()) return false;
        return $caseID;
    }

    /**
     * 获取模块的用例。
     * Get cases of modules.
     *
     * @param  int         $productID
     * @param  int|string  $branch
     * @param  int|array   $moduleIdList
     * @param  string      $browseType
     * @param  string      $auto   no|unit
     * @param  string      $caseType
     * @param  string      $orderBy
     * @param  object      $pager
     * @access public
     * @return array
     */
    public function getModuleCases(int $productID, int|string $branch = 0, int|array $moduleIdList = 0, string $browseType = '', string $auto = 'no', string $caseType = '', string $orderBy = 'id_desc', object $pager = null): array
    {
        $stmt = $this->dao->select('t1.*, t2.title as storyTitle, t2.deleted as storyDeleted')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id');

        if($this->app->tab == 'project') $stmt = $stmt->leftJoin(TABLE_PROJECTCASE)->alias('t3')->on('t1.id=t3.case');

        return $stmt ->where('t1.product')->eq((int)$productID)
            ->beginIF($this->app->tab == 'project')->andWhere('t3.project')->eq($this->session->project)->fi()
            ->beginIF($branch !== 'all')->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t1.module')->in($moduleIdList)->fi()
            ->beginIF($browseType == 'all')->andWhere('t1.scene')->eq(0)->fi()
            ->beginIF($browseType == 'wait')->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($auto == 'auto' || $auto == 'unit')->andWhere('t1.auto')->eq($auto)->fi()
            ->beginIF($auto != 'auto' && $auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->beginIF($caseType)->andWhere('t1.type')->eq($caseType)->fi()
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get project cases of a module.
     *
     * @param  int        $productID
     * @param  int|string $branch
     * @param  int        $moduleIdList
     * @param  string     $browseType
     * @param  string     $auto   no|unit
     * @param  string     $caseType
     * @param  string     $orderBy
     * @param  object     $pager
     * @access public
     * @return array
     */
    public function getModuleProjectCases($productID, $branch = 0, $moduleIdList = 0, $browseType = '', $auto = 'no', $caseType = '', $orderBy = 'id_desc', $pager = null)
    {
        $executions = $this->loadModel('execution')->getIdList($this->session->project);
        array_push($executions, $this->session->project);

        return $this->dao->select('distinct t1.*, t2.*, t4.title as storyTitle')->from(TABLE_PROJECTCASE)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
            ->leftJoin(TABLE_PROJECTSTORY)->alias('t3')->on('t3.story=t2.story')
            ->leftJoin(TABLE_STORY)->alias('t4')->on('t3.story=t4.id')
            ->where('t1.project')->in($executions)
            ->beginIF(!empty($productID))->andWhere('t2.product')->eq((int)$productID)->fi()
            ->beginIF(!empty($productID) and $branch !== 'all')->andWhere('t2.branch')->eq($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t2.module')->in($moduleIdList)->fi()
            ->beginIF($browseType == 'all')->andWhere('t2.scene')->eq(0)->fi()
            ->beginIF($browseType == 'wait')->andWhere('t2.status')->eq($browseType)->fi()
            ->beginIF($auto == 'auto' || $auto == 'unit')->andWhere('t1.auto')->eq($auto)->fi()
            ->beginIF($auto != 'auto' && $auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->beginIF($caseType)->andWhere('t2.type')->eq($caseType)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager, 't1.`case`')
            ->fetchAll('id');
    }

    /**
     * Get project cases.
     *
     * @param  int    $projectID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $browseType
     * @access public
     * @return array
     */
    public function getProjectCases($projectID, $orderBy = 'id_desc', $pager = null, $browseType = '')
    {
        return $this->dao->select('distinct t1.*, t2.*')->from(TABLE_PROJECTCASE)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
            ->where('t1.project')->eq((int)$projectID)
            ->beginIF($browseType != 'all')->andWhere('t2.status')->eq($browseType)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get execution cases.
     *
     * @param  int    $executionID
     * @param  int    $productID
     * @param  int    $branchID
     * @param  int    $moduleID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $browseType   all|wait|needconfirm
     * @access public
     * @return array
     */
    public function getExecutionCases($executionID, $productID = 0, $branchID = 0, $moduleID = 0, $orderBy = 'id_desc', $pager = null, $browseType = '')
    {
        if($browseType == 'needconfirm')
        {
            return $this->dao->select('distinct t1.*, t2.*')->from(TABLE_PROJECTCASE)->alias('t1')
                ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
                ->leftJoin(TABLE_STORY)->alias('t3')->on('t2.story = t3.id')
                ->leftJoin(TABLE_MODULE)->alias('t4')->on('t2.module=t4.id')
                ->where('t1.project')->eq((int)$executionID)
                ->beginIF(!empty($productID))->andWhere('t1.product')->eq($productID)->fi()
                ->beginIF(!empty($moduleID))->andWhere('t4.path')->like("%,$moduleID,%")->fi()
                ->beginIF(!empty($productID) and $branchID !== 'all')->andWhere('t2.branch')->eq($branchID)->fi()
                ->andWhere('t2.deleted')->eq('0')
                ->andWhere('t3.version > t2.storyVersion')
                ->andWhere("t3.status")->eq('active')
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

        return $this->dao->select('distinct t1.*, t2.*')->from(TABLE_PROJECTCASE)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
            ->leftJoin(TABLE_MODULE)->alias('t3')->on('t2.module=t3.id')
            ->where('t1.project')->eq((int)$executionID)
            ->beginIF($browseType != 'all' and $browseType != 'byModule')->andWhere('t2.status')->eq($browseType)->fi()
            ->beginIF(!empty($productID))->andWhere('t1.product')->eq($productID)->fi()
            ->beginIF(!empty($moduleID))->andWhere('t3.path')->like("%,$moduleID,%")->fi()
            ->beginIF(!empty($productID) and $branchID !== 'all')->andWhere('t2.branch')->eq($branchID)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get cases by suite.
     *
     * @param  int         $productID
     * @param  int|string  $branch
     * @param  int         $suiteID
     * @param  array       $moduleIdList
     * @param  string      $orderBy
     * @param  object      $pager
     * @param  string      $auto    no|unit
     * @access public
     * @return array
     */
    public function getBySuite($productID, $branch = 0, $suiteID = 0, $moduleIdList = 0, $orderBy = 'id_desc', $pager = null, $auto = 'no')
    {
        return $this->dao->select('t1.*, t2.title as storyTitle, t3.version as version')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
            ->leftJoin(TABLE_SUITECASE)->alias('t3')->on('t1.id=t3.case')
            ->where('t1.product')->eq((int)$productID)
            ->beginIF($this->app->tab == 'project')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->andWhere('t3.suite')->eq((int)$suiteID)
            ->beginIF($branch !== 'all')->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t1.module')->in($moduleIdList)->fi()
            ->beginIF($auto == 'auto' || $auto == 'unit')->andWhere('t1.auto')->eq($auto)->fi()
            ->beginIF($auto != 'auto' && $auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)->page($pager)->fetchAll('id');
    }

    /**
     * Get cases by type.
     *
     * @param  int         $productID
     * @param  int|string  $branch
     * @param  int         $suiteID
     * @param  array       $moduleIdList
     * @param  string      $orderBy
     * @param  object      $pager
     * @param  string      $auto    no|unit
     * @access public
     * @return array
     */
    public function getByType($productID, $branch = 0, $type = '', $moduleIdList = 0, $orderBy = 'id_desc', $pager = null, $auto = 'no')
    {
        return $this->dao->select('t1.*, t2.title as storyTitle')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story=t2.id')
            ->where('t1.product')->eq((int)$productID)
            ->beginIF($this->app->tab == 'project')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->beginIF($type)->andWhere('t1.type')->eq($type)->fi()
            ->beginIF($branch !== 'all')->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($moduleIdList)->andWhere('t1.module')->in($moduleIdList)->fi()
            ->beginIF($auto == 'unit')->andWhere('t1.auto')->eq('unit')->fi()
            ->beginIF($auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get case info by ID.
     *
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return object|bool
     */
    public function getById(int $caseID, int $version = 0): object|bool
    {
        $case = $this->dao->findById($caseID)->from(TABLE_CASE)->fetch();
        if(!$case) return false;

        foreach($case as $key => $value) if(strpos($key, 'Date') !== false and $value && !(int)substr($value, 0, 4)) $case->$key = '';

        /* Get project and execution. */
        if($this->app->tab == 'project')
        {
            $case->project = $this->session->project;
        }
        elseif($this->app->tab == 'execution')
        {
            $case->execution = $this->session->execution;
            $case->project   = $this->dao->select('project')->from(TABLE_PROJECT)->where('id')->eq($case->execution)->fetch('project');
        }
        else
        {
            $objects = $this->dao->select('t1.project as objectID, t2.type')->from(TABLE_PROJECTCASE)->alias('t1')
                ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.project=t2.id')
                ->where('t1.case')->eq($caseID)
                ->fetchPairs();
            foreach($objects as $objectID => $objectType)
            {
                if($objectType == 'project') $case->project = $objectID;
                if(in_array($objectType, array('sprint', 'stage', 'kanban'))) $case->execution = $objectID;
            }
        }

        /* Set related variables. */
        $toBugs       = $this->dao->select('id, title, severity, openedDate')->from(TABLE_BUG)->where('`case`')->eq($caseID)->fetchAll();
        $case->toBugs = array();
        foreach($toBugs as $toBug) $case->toBugs[$toBug->id] = $toBug;
        if($case->story)
        {
            $story = $this->dao->findById($case->story)->from(TABLE_STORY)->fields('title, status, version')->fetch();
            $case->storyTitle         = $story->title;
            $case->storyStatus        = $story->status;
            $case->latestStoryVersion = $story->version;
        }
        if($case->fromBug) $case->fromBugData = $this->dao->findById($case->fromBug)->from(TABLE_BUG)->fields('title, severity, openedDate')->fetch();
        if($case->linkCase || $case->fromCaseID) $case->linkCaseTitles = $this->dao->select('id,title')->from(TABLE_CASE)->where('id')->in($case->linkCase)->orWhere('id')->eq($case->fromCaseID)->fetchPairs();

        $case->currentVersion = $version ? $version : $case->version;
        $case->files          = $this->loadModel('file')->getByObject('testcase', $caseID);
        $case->steps          = $this->testcaseTao->getSteps($caseID, $case->currentVersion);
        return $case;
    }

    /**
     * Get cases by id list and query string.
     *
     * @param  array  $caseIdList
     * @param  string $query
     * @access public
     * @return array
     */
    public function getByList(array $caseIdList, string $query = ''): array
    {
        if(!$caseIdList) return array();

        return $this->dao->select('*')->from(TABLE_CASE)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($caseIdList)
            ->beginIF($query)->andWhere($query)->fi()
            ->fetchAll('id');
    }

    /**
     * Get test cases.
     *
     * @param  int        $productID
     * @param  int|string $branch
     * @param  string     $browseType
     * @param  int        $queryID
     * @param  int        $moduleID
     * @param  string     $caseType
     * @param  string     $sort
     * @param  object     $pager
     * @param  string     $auto   no|unit
     * @access public
     * @return array
     */
    public function getTestCases($productID, $branch, $browseType, $queryID, $moduleID, $caseType = '', $sort = 'id_desc', $pager = null, $auto = 'no')
    {
        /* Set modules and browse type. */
        $modules    = $moduleID ? $this->loadModel('tree')->getAllChildId($moduleID) : '0';
        $browseType = ($browseType == 'bymodule' and $this->session->caseBrowseType and $this->session->caseBrowseType != 'bysearch') ? $this->session->caseBrowseType : $browseType;
        $group      = $this->lang->navGroup->testcase;
        $auto       = $this->cookie->onlyAutoCase ? 'auto' : $auto;

        /* By module or all cases. */
        $cases = array();
        if($browseType == 'bymodule' or $browseType == 'all' or $browseType == 'wait')
        {
            if($this->app->tab == 'project')
            {
                $cases = $this->getModuleProjectCases($productID, $branch, $modules, $browseType, $auto, $caseType, $sort, $pager);
            }
            else
            {
                $cases = $this->getModuleCases($productID, $branch, $modules, $browseType, $auto, $caseType, $sort, $pager);
            }
        }
        /* Cases need confirmed. */
        elseif($browseType == 'needconfirm')
        {
            $cases = $this->dao->select('distinct t1.*, t2.title AS storyTitle')->from(TABLE_CASE)->alias('t1')
                ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
                ->leftJoin(TABLE_PROJECTCASE)->alias('t3')->on('t1.id = t3.case')
                ->where("t2.status = 'active'")
                ->andWhere('t1.deleted')->eq(0)
                ->andWhere('t2.version > t1.storyVersion')
                ->beginIF(!empty($productID))->andWhere('t1.product')->eq($productID)->fi()
                ->beginIF($this->app->tab == 'project')->andWhere('t3.project')->eq($this->session->project)->fi()
                ->beginIF($branch !== 'all' and !empty($productID))->andWhere('t1.branch')->eq($branch)->fi()
                ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
                ->beginIF($auto == 'auto' || $auto == 'unit')->andWhere('t1.auto')->eq($auto)->fi()
                ->beginIF($auto != 'auto' && $auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
                ->beginIF($caseType)->andWhere('t1.type')->eq($caseType)->fi()
                ->orderBy($sort)
                ->page($pager, 't1.id')
                ->fetchAll();
        }
        elseif($browseType == 'bysuite')
        {
            $cases = $this->getBySuite($productID, $branch, $queryID, $modules, $sort, $pager, $auto);
        }
        /* By search. */
        elseif($browseType == 'bysearch')
        {
            $cases = $this->getBySearch($productID, $queryID, $sort, $pager, $branch, $auto);
        }

        return $cases;
    }

    /**
     * Get cases by search.
     *
     * @param  int         $productID
     * @param  int         $queryID
     * @param  string      $orderBy
     * @param  object      $pager
     * @param  int|string  $branch
     * @param  string      $auto   no|unit
     * @access public
     * @return array
     */
    public function getBySearch($productID, $queryID, $orderBy, $pager = null, $branch = 0, $auto = 'no')
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('testcaseQuery', $query->sql);
                $this->session->set('testcaseForm', $query->form);
            }
            else
            {
                $this->session->set('testcaseQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->testcaseQuery == false) $this->session->set('testcaseQuery', ' 1 = 1');
        }

        $queryProductID = $productID;
        $allProduct     = "`product` = 'all'";
        $caseQuery      = '(' . $this->session->testcaseQuery;
        if(strpos($this->session->testcaseQuery, $allProduct) !== false)
        {
            $products  = $this->app->user->view->products;
            $caseQuery = str_replace($allProduct, '1', $caseQuery);
            $caseQuery = $caseQuery . ' AND `product` ' . helper::dbIN($products);
            $queryProductID = 'all';
        }

        $allBranch = "`branch` = 'all'";
        if($branch !== 'all' and strpos($caseQuery, '`branch` =') === false) $caseQuery .= " AND `branch` in('$branch')";
        if(strpos($caseQuery, $allBranch) !== false) $caseQuery = str_replace($allBranch, '1', $caseQuery);
        $caseQuery .= ')';
        $caseQuery  = str_replace('`version`', 't1.`version`', $caseQuery);

        if($this->app->tab == 'project') $caseQuery = str_replace('`product`', 't2.`product`', $caseQuery);

        /* Search criteria under compatible project. */
        $sql = $this->dao->select('*')->from(TABLE_CASE)->alias('t1');
        if($this->app->tab == 'project') $sql->leftJoin(TABLE_PROJECTCASE)->alias('t2')->on('t1.id=t2.case');
        $cases = $sql
            ->where($caseQuery)
            ->beginIF($this->app->tab == 'project' and $this->config->systemMode == 'new')->andWhere('t2.project')->eq($this->session->project)->fi()
            ->beginIF($this->app->tab == 'project' and !empty($productID) and $queryProductID != 'all')->andWhere('t2.product')->eq($productID)->fi()
            ->beginIF($this->app->tab != 'project' and !empty($productID) and $queryProductID != 'all')->andWhere('t1.product')->eq($productID)->fi()
            ->beginIF($auto == 'auto' || $auto == 'unit')->andWhere('t1.auto')->eq($auto)->fi()
            ->beginIF($auto != 'auto' && $auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll('id');

        return $cases;
    }

    /**
     * Get cases by assignedTo.
     *
     * @param  string $account
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto  no|unit|skip
     * @access public
     * @return array
     */
    public function getByAssignedTo($account, $orderBy = 'id_desc', $pager = null, $auto = 'no')
    {
        return $this->dao->select('t1.id as run, t1.task,t1.case,t1.version,t1.assignedTo,t1.lastRunner,t1.lastRunDate,t1.lastRunResult,t1.status as lastRunStatus,t2.id as id,t2.project,t2.pri,t2.title,t2.type,t2.openedBy,t2.color,t2.product,t2.branch,t2.module,t2.status,t2.story,t2.storyVersion,t3.name as taskName')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
            ->leftJoin(TABLE_TESTTASK)->alias('t3')->on('t1.task = t3.id')
            ->where('t1.assignedTo')->eq($account)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t3.status')->ne('done')
            ->beginIF(strpos($auto, 'skip') === false and $auto != 'unit')->andWhere('t2.auto')->ne('unit')->fi()
            ->beginIF($auto == 'unit')->andWhere('t2.auto')->eq('unit')->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll(strpos($auto, 'run') !== false? 'run' : 'id');
    }

    /**
     * Get cases by openedBy
     *
     * @param  string $account
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto   no|unit|skip
     * @access public
     * @return array
     */
    public function getByOpenedBy($account, $orderBy = 'id_desc', $pager = null, $auto = 'no')
    {
        return $this->dao->findByOpenedBy($account)->from(TABLE_CASE)
            ->beginIF($auto != 'skip')->andWhere('product')->ne(0)->fi()
            ->andWhere('deleted')->eq(0)
            ->beginIF($auto != 'skip' and $auto != 'unit')->andWhere('auto')->ne('unit')->fi()
            ->beginIF($auto == 'unit')->andWhere('auto')->eq('unit')->fi()
            ->orderBy($orderBy)->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get cases by type
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $type    all|needconfirm
     * @param  string $status  all|normal|blocked|investigate
     * @param  int    $moduleID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $auto    no|unit|skip
     * @access public
     * @return array
     */
    public function getByStatus($productID = 0, $branch = 0, $type = 'all', $status = 'all', $moduleID = 0, $orderBy = 'id_desc', $pager = null, $auto = 'no')
    {
        $modules = $moduleID ? $this->loadModel('tree')->getAllChildId($moduleID) : '0';

        $cases = $this->dao->select('t1.*, t2.title as storyTitle')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->beginIF($productID)->where('t1.product')->eq((int) $productID)->fi()
            ->beginIF($productID == 0)->where('t1.product')->in($this->app->user->view->products)->fi()
            ->beginIF($branch)->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
            ->beginIF($type == 'needconfirm')->andWhere("t2.status = 'active'")->andWhere('t2.version > t1.storyVersion')->fi()
            ->beginIF($status != 'all')->andWhere('t1.status')->eq($status)->fi()
            ->beginIF($auto == 'unit')->andWhere('t1.auto')->eq('unit')->fi()
            ->beginIF($auto != 'unit')->andWhere('t1.auto')->ne('unit')->fi()
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)->page($pager)
            ->fetchAll('id');
        return $this->appendData($cases);
    }

    /**
     * Get cases by product id.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getByProduct($productID)
    {
        return $this->dao->select('*')->from(TABLE_CASE)
            ->where('deleted')->eq(0)
            ->andWhere('product')->eq($productID)
            ->fetchAll('id');
    }

    /**
     * Get case pairs by product id and branch.
     *
     * @param int        $productID
     * @param int|string $branch
     * @access public
     * @return void
     */
    public function getPairsByProduct($productID, $branch = 0)
    {
        return $this->dao->select("id, concat_ws(':', id, title) as title")->from(TABLE_CASE)
            ->where('deleted')->eq(0)
            ->andWhere('product')->eq($productID)
            ->beginIF($branch)->andWhere('branch')->in($branch)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Get cases of a story.
     *
     * @param  int    $storyID
     * @access public
     * @return array
     */
    public function getStoryCases($storyID)
    {
        return $this->dao->select('id, project, title, pri, type, status, lastRunner, lastRunDate, lastRunResult')
            ->from(TABLE_CASE)
            ->where('story')->eq((int)$storyID)
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * Get counts of some stories' cases.
     *
     * @param  array  $stories
     * @access public
     * @return int
     */
    public function getStoryCaseCounts($stories)
    {
        if(empty($stories)) return array();
        $caseCounts = $this->dao->select('story, COUNT(*) AS cases')
            ->from(TABLE_CASE)
            ->where('story')->in($stories)
            ->andWhere('deleted')->eq(0)
            ->groupBy('story')
            ->fetchPairs();
        foreach($stories as $storyID) if(!isset($caseCounts[$storyID])) $caseCounts[$storyID] = 0;
        return $caseCounts;
    }

    /**
     * 获取导出的用例。
     * Get cases to export.
     *
     * @param  string   $exportType
     * @param  int      $taskID
     * @param  string   $orderBy
     * @param  int|bool $limit
     * @access public
     * @return array
     */
    public function getCasesToExport(string $exportType, int $taskID, string $orderBy, int|bool $limit): array
    {
        if(strpos($orderBy, 'case') !== false)
        {
            list($field, $sort) = explode('_', $orderBy);
            $orderBy = '`' . $field . '`_' . $sort;
        }

        if($this->session->testcaseOnlyCondition)
        {
            $caseIdList = array();
            if($taskID) $caseIdList = $this->dao->select('`case`')->from(TABLE_TESTRUN)->where('task')->eq($taskID)->fetchPairs();

            return $this->dao->select('*')->from(TABLE_CASE)->where($this->session->testcaseQueryCondition)
                ->beginIF($taskID)->andWhere('id')->in($caseIdList)->fi()
                ->beginIF($exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                ->orderBy($orderBy)
                ->beginIF($limit)->limit($limit)->fi()
                ->fetchAll('id');
        }

        $cases   = array();
        $orderBy = " ORDER BY " . str_replace(array('|', '^A', '_'), ' ', $orderBy);
        $stmt    = $this->dao->query($this->session->testcaseQueryCondition . $orderBy . ($limit ? ' LIMIT ' . $limit : ''));
        while($row = $stmt->fetch())
        {
            $caseID = isset($row->case) ? $row->case : $row->id;

            if($exportType == 'selected' && strpos(",{$this->cookie->checkedItem},", ",$caseID,") === false) continue;

            $row->id        = $caseID;
            $cases[$caseID] = $row;
        }

        return $cases;
    }

    /**
     * 获取导出的用例的结果。
     * Get case results for export.
     *
     * @param  array  $caseIdList
     * @param  int    $taskID
     * @access public
     * @return array
     */
    public function getCaseResultsForExport(array $caseIdList, int $taskID = 0): array
    {
        $stmt = $this->dao->select('t1.*')->from(TABLE_TESTRESULT)->alias('t1')
            ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.run = t2.id')
            ->where('t1.`case`')->in($caseIdList)
            ->beginIF($taskID)->andWhere('t2.task')->eq($taskID)->fi()
            ->orderBy('id_desc')
            ->query();

        $results = array();
        while($result = $stmt->fetch())
        {
            if(!isset($results[$result->case])) $results[$result->case] = unserialize($result->stepResults);
        }

        return $results;
    }

    /**
     * 更新用例。
     * Update a case.
     *
     * @param  object $case
     * @param  object $oldCase
     * @param  array  $testtasks
     * @access public
     * @return bool|array
     */
    public function update(object $case, object $oldCase, array $testtasks = array()): bool|array
    {
        /* Remove the require field named story when the case is a lib case.*/
        $requiredFields = $this->config->testcase->edit->requiredFields;
        if($oldCase->lib != 0) $requiredFields = str_replace(',story,', ',', ",$requiredFields,");

        $this->dao->update(TABLE_CASE)->data($case, 'deleteFiles,uid,stepChanged,comment,steps,expects,stepType,linkBug')
            ->autoCheck()
            ->batchCheck($requiredFields, 'notempty')
            ->checkFlow()
            ->where('id')->eq((int)$case->id)
            ->exec();

        if(dao::isError()) return false;

        $this->testcaseTao->updateCase2Project($oldCase, $case);

        if($case->stepChanged) $this->testcaseTao->updateStep($case, $oldCase);

        if($oldCase->lib && empty($oldCase->product))
        {
            $fromcaseVersion = $this->dao->select('fromCaseVersion')->from(TABLE_CASE)->where('fromCaseID')->eq($case->id)->fetch('fromCaseVersion');
            $fromcaseVersion = (int)$fromcaseVersion + 1;
            $this->dao->update(TABLE_CASE)->set('`fromCaseVersion`')->eq($fromcaseVersion)->where('`fromCaseID`')->eq($case->id)->exec();
        }

        if(isset($oldCase->toBugs) && isset($case->linkBug)) $this->testcaseTao->linkBugs($oldCase->id, array_keys($oldCase->toBugs), $case);

        if($case->branch && !empty($testtasks)) $this->testcaseTao->unlinkCaseFromTesttask($oldCase->id, $testtasks);

        $this->loadModel('file')->processFile4Object('testcase', $oldCase, $case);

        /* Join the steps to diff. */
        if($case->stepChanged && $case->steps)
        {
            $oldCase->steps = $this->joinStep($oldCase->steps);
            $case->steps    = $this->joinStep($this->getByID($oldCase->id, $case->version)->steps);
        }
        else
        {
            unset($oldCase->steps);
            unset($case->steps);
        }

        return common::createChanges($oldCase, $case);
    }

    /**
     * 评审用例。
     * Review case.
     *
     * @param  object $case
     * @param  object $oldCase
     * @access public
     * @return bool
     */
    public function review(object $case, object $oldCase): bool
    {
        $this->dao->update(TABLE_CASE)->data($case, 'result,comment')->autoCheck()->checkFlow()->where('id')->eq($oldCase->id)->exec();
        if(dao::isError()) return false;

        $changes = common::createChanges($oldCase, $case);
        if(!empty($changes))
        {
            $actionID = $this->loadModel('action')->create('case', $caseID, 'Reviewed', $case->comment, ucfirst($case->result));
            $this->action->logHistory($actionID, $changes);
        }
        return true;
    }

    /**
     * Batch review cases.
     *
     * @param  array  $caseIdList
     * @param  string $result
     * @access public
     * @return bool
     */
    public function batchReview(array $caseIdList, string $result): bool
    {
        $caseIdList = array_filter($caseIdList);
        if(!$caseIdList) return false;

        $oldCases = $this->getByList($caseIdList, "status = 'wait'");
        if(!$oldCases) return false;

        $now  = helper::now();
        $case = new stdClass();
        $case->reviewedBy     = $this->app->user->account;
        $case->reviewedDate   = substr($now, 0, 10);
        $case->lastEditedBy   = $this->app->user->account;
        $case->lastEditedDate = $now;
        if($result == 'pass') $case->status = 'normal';

        $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->where('id')->in(array_keys($oldCases))->exec();
        if(dao::isError()) return false;

        $this->loadModel('action');

        foreach($oldCases as $oldCase)
        {
            $changes = common::createChanges($oldCase, $case);
            if($changes)
            {
                $actionID = $this->action->create('case', $oldCase->id, 'Reviewed', '', ucfirst($result));
                $this->action->logHistory($actionID, $changes);
            }
        }

        return !dao::isError();
    }

    /**
     * 获取可关联的用例。
     * Get cases to link.
     *
     * @param  int    $caseID
     * @param  string $browseType
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getCases2Link(int $caseID, string $browseType = 'bySearch', int $queryID = 0): array
    {
        if($browseType != 'bySearch') return array();

        $case       = $this->getByID($caseID);
        $cases2Link = $this->getBySearch($case->product, $queryID, 'id', null, $case->branch);
        foreach($cases2Link as $key => $case2Link)
        {
            if($case2Link->id == $caseID) unset($cases2Link[$key]);
            if(in_array($case2Link->id, explode(',', $case->linkCase))) unset($cases2Link[$key]);
        }
        return $cases2Link;
    }

    /**
     * 获取可关联的 bug。
     * Get bugs to link.
     *
     * @param  int    $caseID
     * @param  string $browseType
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getBugs2Link(int $caseID, string $browseType = 'bySearch', int $queryID = 0): array
    {
        if($browseType != 'bySearch') return array();

        $case      = $this->getByID($caseID);
        $bugs2Link = $this->loadModel('bug')->getBySearch('bug', $case->product, (string)$case->branch, 0, 0, $queryID, '', 'id');
        foreach($bugs2Link as $key => $bug2Link)
        {
            if($bug2Link->case != 0) unset($bugs2Link[$key]);
        }
        return $bugs2Link;
    }

    /**
     * Batch delete cases and scenes.
     *
     * @param  array  $caseIdList
     * @param  array  $sceneIdList
     * @access public
     * @return bool
     */
    public function batchDelete(array $caseIdList, array $sceneIdList): bool
    {
        $caseIdList  = array_filter($caseIdList);
        $sceneIdList = array_filter($sceneIdList);
        if(!$caseIdList && !$sceneIdList) return false;

        $this->loadModel('action');

        if($caseIdList)
        {
            $this->dao->update(TABLE_CASE)->set('deleted')->eq('1')->where('id')->in($caseIdList)->exec();
            foreach($caseIdList as $caseID) $this->action->create('case', $caseID, 'deleted', '', $extra = ACTIONMODEL::CAN_UNDELETED);
        }

        if($sceneIdList)
        {
            $this->dao->update(TABLE_SCENE)->set('deleted')->eq('1')->where('id')->in($sceneIdList)->exec();
            foreach($sceneIdList as $sceneID) $this->action->create('scene', $sceneID, 'deleted', '', $extra = ACTIONMODEL::CAN_UNDELETED);
        }

        return !dao::isError();
    }

    /**
     * Batch change branch of cases and scenes.
     *
     * @param  array  $caseIdList
     * @param  array  $sceneIdList
     * @param  int    $branchID
     * @access public
     * @return bool
     */
    public function batchChangeBranch(array $caseIdList, array $sceneIdList, int $branchID): bool
    {
        if($branchID < 0 || $branchID > 16777215) return false; // The branch column's data type is mediumint unsigned and its range is 0-16777215.

        $caseIdList  = array_filter($caseIdList);
        $sceneIdList = array_filter($sceneIdList);
        if(!$caseIdList && !$sceneIdList) return false;

        if($caseIdList)  $this->batchChangeCaseBranch($caseIdList, $branchID);
        if($sceneIdList) $this->batchChangeSceneBranch($sceneIdList, $branchID);

        return !dao::isError();
    }

    /**
     * Batch change branch of cases.
     *
     * @param  array  $caseIdList
     * @param  int    $branchID
     * @access public
     * @return bool
     */
    public function batchChangeCaseBranch(array $caseIdList, int $branchID): bool
    {
        if(!$caseIdList) return false;
        if($branchID < 0 || $branchID > 16777215) return false; // The branch column's data type is mediumint unsigned and its range is 0-16777215.

        $oldCases = $this->getByList($caseIdList, "branch != '{$branchID}'");
        if(!$oldCases) return false;

        $case = new stdclass();
        $case->branch         = $branchID;
        $case->lastEditedBy   = $this->app->user->account;
        $case->lastEditedDate = helper::now();

        $this->dao->update(TABLE_CASE)->data($case)->where('id')->in(array_keys($oldCases))->exec();
        if(dao::isError()) return false;

        $this->loadModel('action');

        foreach($oldCases as $oldCase)
        {
            $changes = common::createChanges($oldCase, $case);
            if($changes)
            {
                $actionID = $this->action->create('case', $oldCase->id, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        return !dao::isError();
    }

    /**
     * Batch change branch of scenes.
     *
     * @param  array  $sceneIdList
     * @param  int    $branchID
     * @access public
     * @return bool
     */
    public function batchChangeSceneBranch(array $sceneIdList, int $branchID): bool
    {
        if(!$sceneIdList) return false;
        if($branchID < 0 || $branchID > 16777215) return false; // The branch column's data type is mediumint unsigned and its range is 0-16777215.

        $oldScenes = $this->getScenesByList($sceneIdList, "branch != '{$branchID}'");
        if(!$oldScenes) return false;

        $scene = new stdclass();
        $scene->branch         = $branchID;
        $scene->lastEditedBy   = $this->app->user->account;
        $scene->lastEditedDate = helper::now();

        $this->dao->update(TABLE_SCENE)->data($scene)->where('id')->in(array_keys($oldScenes))->exec();
        if(dao::isError()) return false;

        $this->loadModel('action');

        foreach($oldScenes as $oldScene)
        {
            $changes = common::createChanges($oldScene, $scene);
            if($changes)
            {
                $actionID = $this->action->create('scene', $oldScene->id, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        return !dao::isError();
    }

    /**
     * Batch change module of cases and scenes.
     *
     * @param  array  $caseIdList
     * @param  array  $sceneIdList
     * @param  int    $moduleID
     * @access public
     * @return bool
     */
    public function batchChangeModule(array $caseIdList, array $sceneIdList, int $moduleID): bool
    {
        if($moduleID < 0 || $moduleID > 16777215) return false; // The module column's data type is mediumint unsigned and its range is 0-16777215.

        $caseIdList  = array_filter($caseIdList);
        $sceneIdList = array_filter($sceneIdList);
        if(!$caseIdList && !$sceneIdList) return false;

        if($caseIdList)  $this->batchChangeCaseModule($caseIdList, $moduleID);
        if($sceneIdList) $this->batchChangeSceneModule($sceneIdList, $moduleID);

        return !dao::isError();
    }

    /**
     * Batch change module of cases.
     *
     * @param  array  $caseIdList
     * @param  int    $moduleID
     * @access public
     * @return bool
     */
    public function batchChangeCaseModule(array $caseIdList, int $moduleID): bool
    {
        if(!$caseIdList) return false;
        if($moduleID < 0 || $moduleID > 16777215) return false; // The module column's data type is mediumint unsigned and its range is 0-16777215.

        $oldCases = $this->getByList($caseIdList, "module != '{$moduleID}'");
        if(!$oldCases) return false;

        $case = new stdclass();
        $case->module         = $moduleID;
        $case->lastEditedBy   = $this->app->user->account;
        $case->lastEditedDate = helper::now();

        $this->dao->update(TABLE_CASE)->data($case)->where('id')->in(array_keys($oldCases))->exec();
        if(dao::isError()) return false;

        $this->loadModel('action');

        foreach($oldCases as $oldCase)
        {
            $changes = common::createChanges($oldCase, $case);
            if($changes)
            {
                $actionID = $this->action->create('case', $oldCase->id, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        return !dao::isError();
    }

    /**
     * Batch change module of scenes.
     *
     * @param  array  $sceneIdList
     * @param  int    $moduleID
     * @access public
     * @return bool
     */
    public function batchChangeSceneModule(array $sceneIdList, int $moduleID): bool
    {
        if(!$sceneIdList) return false;
        if($moduleID < 0 || $moduleID > 16777215) return false; // The module column's data type is mediumint unsigned and its range is 0-16777215.

        $oldScenes = $this->getScenesByList($sceneIdList, "module != '{$moduleID}'");
        if(!$oldScenes) return false;

        $scene = new stdclass();
        $scene->module         = $moduleID;
        $scene->lastEditedBy   = $this->app->user->account;
        $scene->lastEditedDate = helper::now();

        $this->dao->update(TABLE_SCENE)->data($scene)->where('id')->in(array_keys($oldScenes))->exec();
        if(dao::isError()) return false;

        $this->loadModel('action');

        foreach($oldScenes as $oldScene)
        {
            $changes = common::createChanges($oldScene, $scene);
            if($changes)
            {
                $actionID = $this->action->create('scene', $oldScene->id, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        return !dao::isError();
    }

    /**
     * Batch change type of cases.
     *
     * @param  array  $caseIdList
     * @param  string $type
     * @access public
     * @return bool
     */
    public function batchChangeType(array $caseIdList, string $type): bool
    {
        if(!$type) return false;

        $caseIdList = array_filter($caseIdList);
        if(!$caseIdList) return false;

        $oldCases = $this->getByList($caseIdList, "type != '{$type}'");
        if(!$oldCases) return false;

        $case = new stdClass();
        $case->type           = $type;
        $case->lastEditedBy   = $this->app->user->account;
        $case->lastEditedDate = helper::now();

        $this->dao->update(TABLE_CASE)->data($case)->autoCheck()->where('id')->in(array_keys($oldCases))->exec();
        if(dao::isError()) return false;

        $this->loadModel('action');

        foreach($oldCases as $oldCase)
        {
            $changes = common::createChanges($oldCase, $case);
            if($changes)
            {
                $actionID = $this->action->create('case', $oldCase->id, 'Edited', '', ucfirst($type));
                $this->action->logHistory($actionID, $changes);
            }
        }

        return !dao::isError();
    }

    /**
     * Batch confirm story change of cases.
     *
     * @param  array  $caseIdList
     * @access public
     * @return bool
     */
    public function batchConfirmStoryChange(array $caseIdList): bool
    {
        $caseIdList = array_filter($caseIdList);
        if(!$caseIdList) return false;

        $cases = $this->getByList($caseIdList);
        if(!$cases) return false;

        $storyIdList = array_unique(array_filter(array_map(function($case){return $case->story;}, $cases)));
        if(!$storyIdList) return false;

        $stories = $this->dao->select('id, version')->from(TABLE_STORY)->where('id')->in($storyIdList)->fetchPairs();

        $this->loadModel('action');

        foreach($cases as $case)
        {
            $storyVersion = zget($stories, $case->story, 0);
            if(!$storyVersion) continue;

            $this->dao->update(TABLE_CASE)->set('storyVersion')->eq($storyVersion)->where('id')->eq($case->id)->exec();
            $this->action->create('case', $case->id, 'confirmed', '', $storyVersion);
        }

        return !dao::isError();
    }

    /**
     * Join steps to a string, thus can diff them.
     *
     * @param  array   $steps
     * @access public
     * @return string
     */
    public function joinStep($steps)
    {
        $return = '';
        if(empty($steps)) return $return;
        foreach($steps as $step) $return .= $step->desc . ' EXPECT:' . $step->expect . "\n";
        return $return;
    }

    /**
     * Create case steps from a bug's step.
     *
     * @param  string    $steps
     * @access public
     * @return array
     */
    function createStepsFromBug($steps)
    {
        $steps        = strip_tags($steps);
        $caseSteps    = array((object)array('desc' => $steps, 'expect' => ''));   // the default steps before parse.
        $lblStep      = strip_tags($this->lang->bug->tplStep);
        $lblResult    = strip_tags($this->lang->bug->tplResult);
        $lblExpect    = strip_tags($this->lang->bug->tplExpect);
        $lblStepPos   = strpos($steps, $lblStep);
        $lblResultPos = strpos($steps, $lblResult);
        $lblExpectPos = strpos($steps, $lblExpect);

        if($lblStepPos === false or $lblResultPos === false or $lblExpectPos === false) return $caseSteps;

        $caseSteps  = substr($steps, $lblStepPos + strlen($lblStep), $lblResultPos - strlen($lblStep) - $lblStepPos);
        $caseExpect = substr($steps, $lblExpectPos + strlen($lblExpect));
        $caseSteps  = trim($caseSteps);
        $caseExpect = trim($caseExpect);

        $caseSteps = explode("\n", trim($caseSteps));
        $stepCount = count($caseSteps);
        foreach($caseSteps as $key => $caseStep)
        {
            $expect = $key + 1 == $stepCount ? $caseExpect : '';
            $caseSteps[$key] = (object)array('desc' => trim($caseStep), 'expect' => $expect, 'type' => 'item');
        }
        return $caseSteps;
    }

    /**
     * Adjust the action is clickable.
     *
     * @param  object $case
     * @param  string $action
     * @access public
     * @return bool
     */
    public static function isClickable(object $case, string $action): bool
    {
        $canBeChanged = common::canBeChanged('case', $case);
        if(!$canBeChanged) return false;

        global $config;

        $action = strtolower($action);

        if($action == 'confirmchange')      return $case->caseStatus != 'wait' && $case->version < $case->caseVersion;
        if($action == 'confirmstorychange') return $case->needconfirm || $case->browseType == 'needconfirm';
        if($action == 'createbug')          return !empty($case->caseFails) && $case->caseFails > 0;
        if($action == 'review')             return ($config->testcase->needReview || !empty($config->testcase->forceReview)) && (isset($case->caseStatus) ? $case->caseStatus == 'wait' : $case->status == 'wait');
        if($action == 'showscript')         return $case->auto == 'auto';

        return true;
    }

    /**
     * Get fields for import.
     *
     * @access public
     * @return array
     */
    public function getImportFields($productID = 0)
    {
        $product = $this->loadModel('product')->getById($productID);
        if($product && $product->type != 'normal') $this->lang->testcase->branch = $this->lang->product->branchName[$product->type];

        $caseLang   = $this->lang->testcase;
        $caseConfig = $this->config->testcase;
        $fields     = explode(',', $caseConfig->exportFields);
        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($caseLang->$fieldName) ? $caseLang->$fieldName : $fieldName;
            unset($fields[$key]);
        }

        return $fields;
    }

    /**
     * Import case from Lib.
     *
     * @param  int    $productID
     * @param  int    $libID
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function importFromLib($productID, $libID, $branch)
    {
        $data = fixer::input('post')->get();

        $prevModule = 0;
        $prevBranch = 0;
        foreach($data->module as $i => $module)
        {
            if($module != 'ditto') $prevModule = $module;
            if($module == 'ditto') $data->module[$i] = $prevModule;
        }

        $caseModules = array();
        $this->loadModel('testsuite');
        if(isset($data->branch))
        {
            foreach($data->branch as $i => $branch)
            {
                if($branch != 'ditto') $prevBranch = $branch;
                if($branch == 'ditto') $data->branch[$i] = $prevBranch;
                if(!isset($caseModules[$data->branch[$i]])) $caseModules[$data->branch[$i]] = $this->testsuite->getCanImportModules($productID, $libID,  $data->branch[$i]);
            }
        }
        else
        {
            $caseModules[$branch] = $this->loadModel('testsuite')->getCanImportModules($productID, $libID,  $branch);
        }

        $libCases = $this->dao->select('*')->from(TABLE_CASE)->where('deleted')->eq(0)->andWhere('id')->in($data->caseIdList)->fetchAll('id');
        $libSteps = $this->dao->select('*')->from(TABLE_CASESTEP)->where('`case`')->in($data->caseIdList)->orderBy('id')->fetchGroup('case');
        $libFiles = $this->dao->select('*')->from(TABLE_FILE)->where('objectID')->in($data->caseIdList)->andWhere('objectType')->eq('testcase')->fetchGroup('objectID', 'id');
        $imported = '';
        foreach($libCases as $libCaseID => $case)
        {
            $case->fromCaseID      = $case->id;
            $case->fromCaseVersion = (int)$case->version;
            $case->product         = $productID;
            if(isset($data->module[$case->id])) $case->module = $data->module[$case->id];
            if(isset($data->branch[$case->id])) $case->branch = $data->branch[$case->id];
            unset($case->id);

            $branch = isset($case->branch) ? $case->branch : 0;
            if(empty($caseModules[$branch][$case->fromCaseID][$case->module]))
            {
                $imported .= "$case->fromCaseID,";
                continue;
            }

            $this->dao->insert(TABLE_CASE)->data($case)->autoCheck()->exec();

            if(!dao::isError())
            {
                $caseID = $this->dao->lastInsertID();
                if(isset($libSteps[$libCaseID]))
                {
                    foreach($libSteps[$libCaseID] as $step)
                    {
                        $step->case = $caseID;
                        unset($step->id);
                        $this->dao->insert(TABLE_CASESTEP)->data($step)->exec();
                    }
                }

                /* If under the project module, the cases is imported need linking to the project. */
                if($this->app->tab == 'project')
                {
                    $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($this->session->project)->orderBy('order_desc')->limit(1)->fetch('order');

                    $this->dao->insert(TABLE_PROJECTCASE)
                        ->set('project')->eq($this->session->project)
                        ->set('product')->eq($case->product)
                        ->set('case')->eq($caseID)
                        ->set('version')->eq($case->version)
                        ->set('order')->eq(++ $lastOrder)
                        ->exec();
                }

                /* Fix bug #1518. */
                $oldFiles = zget($libFiles, $libCaseID, array());
                foreach($oldFiles as $fileID => $file)
                {
                    $file->objectID  = $caseID;
                    $file->addedBy   = $this->app->user->account;
                    $file->addedDate = helper::now();
                    $file->downloads = 0;
                    unset($file->id);
                    $this->dao->insert(TABLE_FILE)->data($file)->exec();
                }
                $this->loadModel('action')->create('case', $caseID, 'fromlib', '', $case->lib);
            }
        }

        if(!empty($imported)) return $imported;

        return !dao::isError();
    }

    /**
     * Import cases to lib.
     *
     * @param  int    $caseIdList
     * @access public
     * @return void
     */
    public function importToLib($caseIdList = 0)
    {
        if(empty($caseIdList)) $caseIdList = $this->post->caseIdList;
        $caseIdList = explode(',' , $caseIdList);
        $libID      = $this->post->lib;

        if(empty($libID)) return dao::$errors[] = sprintf($this->lang->error->notempty, $this->lang->testcase->caselib);

        $this->loadModel('action');
        $cases          = $this->dao->select('*')->from(TABLE_CASE)->where('deleted')->eq(0)->andWhere('id')->in($caseIdList)->fetchAll('id');
        $caseSteps      = $this->dao->select('*')->from(TABLE_CASESTEP)->where('`case`')->in($caseIdList)->orderBy('id')->fetchGroup('case');
        $caseFiles      = $this->dao->select('*')->from(TABLE_FILE)->where('objectID')->in($caseIdList)->andWhere('objectType')->eq('testcase')->fetchGroup('objectID', 'id');
        $libCases       = $this->loadModel('caselib')->getLibCases($libID, 'all');
        $libFiles       = $this->dao->select('*')->from(TABLE_FILE)->where('objectID')->in(array_keys($libCases))->andWhere('objectType')->eq('testcase')->fetchGroup('objectID', 'id');
        $libCases       = $this->dao->select('*')->from(TABLE_CASE)->where('lib')->eq($libID)->andWhere('product')->eq(0)->andWhere('deleted')->eq('0')->fetchGroup('fromCaseID', 'id');
        $maxOrder       = $this->dao->select('max(`order`) as maxOrder')->from(TABLE_CASE)->where('deleted')->eq(0)->fetch('maxOrder');
        $maxModuleOrder = $this->dao->select('max(`order`) as maxOrder')->from(TABLE_MODULE)->where('deleted')->eq(0)->fetch('maxOrder');
        foreach($cases as $caseID => $case)
        {
            $libCase = new stdclass();
            $libCase->lib             = $libID;
            $libCase->title           = $case->title;
            $libCase->precondition    = $case->precondition;
            $libCase->keywords        = $case->keywords;
            $libCase->pri             = $case->pri;
            $libCase->type            = $case->type;
            $libCase->stage           = $case->stage;
            $libCase->status          = $case->status;
            $libCase->fromCaseID      = $case->id;
            $libCase->fromCaseVersion = $case->version;
            $libCase->order           = ++ $maxOrder;
            $libCase->module          = empty($case->module) ? 0 : $this->importCaseRelatedModules($libID, $case->module, $maxModuleOrder);

            if(empty($libCases[$caseID]))
            {
                $libCase->openedBy   = $this->app->user->account;
                $libCase->openedDate = helper::now();
                $this->dao->insert(TABLE_CASE)->data($libCase)->autoCheck()->exec();
                if(!dao::isError()) $libCaseID = $this->dao->lastInsertID();
                $this->action->create('case', $libCaseID, 'tolib', '', $caseID);
            }
            else
            {
                $libCaseList = array_keys($libCases[$caseID]);
                $libCaseID   = $libCaseList[0];

                $libCase->lastEditedBy   = $this->app->user->account;
                $libCase->lastEditedDate = helper::now();
                $libCase->version        = (int)$libCases[$caseID][$libCaseID]->version + 1;
                $this->dao->update(TABLE_CASE)->data($libCase)->autoCheck()->where('id')->eq((int)$libCaseID)->exec();

                $this->action->create('case', $libCaseID, 'updatetolib', '', $caseID);

                $this->dao->delete()->from(TABLE_CASESTEP)->where('`case`')->eq($libCaseID)->exec();

                $removeFiles = zget($libFiles, $libCaseID, array());
                $this->dao->delete()->from(TABLE_FILE)->where('`objectID`')->eq($libCaseID)->andWhere('objectType')->eq('testcase')->exec();
                foreach($removeFiles as $fileID => $file)
                {
                    $filePath = pathinfo($file->pathname, PATHINFO_BASENAME);
                    $datePath = substr($file->pathname, 0, 6);
                    $filePath = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . "{$datePath}/" . $filePath;
                    unlink($filePath);
                }
            }

            if(!dao::isError())
            {
                if(isset($caseSteps[$caseID]))
                {
                    foreach($caseSteps[$caseID] as $index => $step)
                    {
                        if($step->version != $case->version) continue;
                        $oldStepID     = $step->id;
                        $step->case    = $libCaseID;
                        $step->version = zget($libCase, 'version', '0');
                        unset($step->id);

                        $this->dao->insert(TABLE_CASESTEP)->data($step)->exec();
                    }
                }

                $oldFiles = zget($caseFiles, $caseID, array());
                foreach($oldFiles as $fileID => $file)
                {
                    $originName = pathinfo($file->pathname, PATHINFO_FILENAME);
                    $datePath   = substr($file->pathname, 0, 6);
                    $originFile = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . "{$datePath}/" . $originName;

                    $copyName = $originName . 'copy' . $libCaseID;
                    $copyFile = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . "{$datePath}/" .  $copyName;
                    copy($originFile, $copyFile);

                    $newFileName    = $file->pathname;
                    $newFileName    = str_replace('.', "copy$libCaseID.", $newFileName);
                    $file->pathname = $newFileName;

                    $file->objectID  = $libCaseID;
                    $file->addedBy   = $this->app->user->account;
                    $file->addedDate = helper::now();
                    $file->downloads = 0;
                    unset($file->id);
                    $this->dao->insert(TABLE_FILE)->data($file)->exec();
                }
            }
        }
    }

    /**
     * Import case related modules.
     *
     * @param  int    $libID
     * @param  int    $oldModuleID
     * @param  int    $maxOrder
     * @access public
     * @return void
     */
    public function importCaseRelatedModules($libID, $oldModuleID = 0, $maxOrder = 0)
    {
        $moduleID = $this->checkModuleImported($libID, $oldModuleID);
        if($moduleID) return $moduleID;

        $oldModule = $this->dao->select('name, parent, grade, `order`, short')->from(TABLE_MODULE)->where('id')->eq($oldModuleID)->fetch();

        $oldModule->root   = $libID;
        $oldModule->from   = $oldModuleID;
        $oldModule->type   = 'caselib';
        if(!empty($maxOrder)) $oldModule->order = $maxOrder + $oldModule->order;
        $this->dao->insert(TABLE_MODULE)->data($oldModule)->autoCheck()->exec();

        if(!dao::isError())
        {
            $newModuleID = $this->dao->lastInsertID();

            if($oldModule->parent)
            {
                $parentModuleID = $this->importCaseRelatedModules($libID, $oldModule->parent, !empty($maxOrder) ? $maxOrder : 0);
                $parentModule   = $this->dao->select('id, path')->from(TABLE_MODULE)->where('id')->eq($parentModuleID)->fetch();
                $parent         = $parentModule->id;
                $path           = $parentModule->path . "$newModuleID,";
            }
            else
            {
                $path   = ",$newModuleID,";
                $parent = 0;
            }

            $this->dao->update(TABLE_MODULE)->set('parent')->eq($parent)->set('path')->eq($path)->where('id')->eq($newModuleID)->exec();

            return $newModuleID;
        }
    }

    /**
     * Adjust module is can import.
     *
     * @param  int    $libID
     * @param  int    $oldModule
     * @access public
     * @return int
     */
    public function checkModuleImported($libID, $oldModule = 0)
    {
        $module = $this->dao->select('id')->from(TABLE_MODULE)
            ->where('root')->eq($libID)
            ->andWhere('`from`')->eq($oldModule)
            ->andWhere('type')->eq('caselib')
            ->andWhere('deleted')->eq(0)
            ->fetch();

        if(!$module) return '';

        return $module->id;
    }

    /**
     * Build search form.
     *
     * @param  int    $productID
     * @param  array  $products
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  string $projectID
     * @access public
     * @return void
     */
    public function buildSearchForm($productID, $products, $queryID, $actionURL, $projectID = 0, $moduleID = 0, $branch = 0)
    {
        $productList = array();
        if($this->app->tab == 'project' and empty($productID))
        {
            $productList = $products;
        }
        else
        {
            $productList = array('all' => $this->lang->all);
            if(isset($products[$productID])) $productList = array($productID => $products[$productID]) + $productList;
        }
        $this->config->testcase->search['params']['product']['values'] = array('') + $productList;

        $module = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, $branch);
        $scene  = $this->getSceneMenu($productID, $moduleID, $viewType = 'case', $startSceneID = 0, $branch, 0, true);
        if(!$productID)
        {
            $module = array();
            foreach($products as $id => $name) $module += $this->loadModel('tree')->getOptionMenu($id, 'case', 0);
        }
        $this->config->testcase->search['params']['module']['values'] = $module;
        $this->config->testcase->search['params']['scene']['values']  = $scene;

        $this->config->testcase->search['params']['lib']['values'] = $this->loadModel('caselib')->getLibraries();

        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->testcase->search['fields']['branch']);
            unset($this->config->testcase->search['params']['branch']);
        }
        else
        {
            $this->app->loadLang('branch');
            $product = $this->loadModel('product')->getByID($productID);
            $this->config->testcase->search['fields']['branch'] = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);
            $this->config->testcase->search['params']['branch']['values'] = array('' => '', '0' => $this->lang->branch->main) + $this->loadModel('branch')->getPairs($productID, '', $projectID) + array('all' => $this->lang->branch->all);
        }
        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);
        $this->config->testcase->search['actionURL'] = $actionURL;
        $this->config->testcase->search['queryID']   = $queryID;
        $this->config->testcase->search['module']    = $this->app->rawModule;

        $this->loadModel('search')->setSearchParams($this->config->testcase->search);
    }

    /**
     * Print cell data
     *
     * @param  object $col
     * @param  object $case
     * @param  array  $users
     * @param  array  $branches
     * @access public
     * @return void
     */
    public function printCell($col, $case, $users, $branches, $modulePairs = array(), $browseType = '', $mode = 'datatable', $isCase = 1)
    {
        /* Check the product is closed. */
        $canBeChanged = common::canBeChanged('case', $case);

        $canBatchRun                = common::hasPriv('testtask', 'batchRun');
        $canBatchEdit               = common::hasPriv('testcase', 'batchEdit');
        $canBatchDelete             = common::hasPriv('testcase', 'batchDelete');
        $canBatchChangeType         = common::hasPriv('testcase', 'batchChangeType');
        $canBatchConfirmStoryChange = common::hasPriv('testcase', 'batchConfirmStoryChange');
        $canBatchChangeModule       = common::hasPriv('testcase', 'batchChangeModule');

        $canBatchAction             = ($canBatchRun or $canBatchEdit or $canBatchDelete or $canBatchChangeType or $canBatchConfirmStoryChange or $canBatchChangeModule);

        $canView    = common::hasPriv('testcase', 'view');
        $caseLink   = helper::createLink('testcase', 'view', "caseID=$case->id&version=$case->version");
        $account    = $this->app->user->account;
        $fromCaseID = $case->fromCaseID;
        $id = $col->id;
        if($col->show)
        {
            $class = $id == 'title' ? 'c-name' : 'c-' . $id;
            $title = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$case->title}'";
            }
            if($id == 'status')
            {
                $class .= $case->status;
                $title  = "title='" . $this->processStatus('testcase', $case) . "'";
            }
            if(strpos(',bugs,results,stepNumber,', ",$id,") !== false) $title = "title='{$case->$id}'";
            if($id == 'actions') $class .= ' c-actions';
            if($id == 'lastRunResult') $class .= " {$case->lastRunResult}";
            if(strpos(',stage,precondition,keywords,story,', ",{$id},") !== false) $class .= ' text-ellipsis';

            if($id == 'title')
            {
                if($isCase == 2)
                {
                    echo "<td class='c-name table-nest-title text-left sort-handler has-prefix has-suffix' {$title}><span class='table-nest-icon icon '></span>";
                }
                else
                {
                    echo "<td class='c-name table-nest-title text-left sort-handler has-prefix has-suffix' {$title}><span class='table-nest-icon icon icon-test'></span>";
                }
            }
            else
            {
                echo "<td class='{$class}' {$title}>";
            }
            if($this->config->edition != 'open') $this->loadModel('flow')->printFlowCell('testcase', $case, $id);
            switch($id)
            {
            case 'id':
                $showid = "";
                if($isCase == 2)
                {
                    $showid = substr($case->id,1);
                    $showid = preg_replace('/^0+/', '', $showid);
                }
                else
                {
                    $showid = $case->id;
                }
                if($canBatchAction)
                {
                    $disabled = $canBeChanged ? '' : 'disabled';
                    if($isCase == 1){
                        echo html::checkbox('caseIDList', array($case->id => ''), '', $disabled) . html::a(helper::createLink('testcase', 'view', "caseID=$case->id"), sprintf('%03d', $showid), '', "data-app='{$this->app->tab}'");
                    }
                    else
                    {
                        echo html::checkbox('caseIDList', array($case->id => ''), '', $disabled) .  sprintf('%03d', $showid);
                    }
                }
                else
                {
                    printf('%03d', $showid);
                }
                break;
            case 'pri':
                if($isCase != 2)
                {
                    echo "<span class='label-pri label-pri-" . $case->pri . "' title='" . zget($this->lang->testcase->priList, $case->pri, $case->pri) . "'>";
                    echo zget($this->lang->testcase->priList, $case->pri, $case->pri);
                    echo "</span>";
                }
                break;
            case 'title':
                if($isCase == 1)
                {
                    $autoIcon = $case->auto == 'auto' ? " <i class='icon icon-draft-edit'></i>" : '';
                    if($modulePairs and $case->module and isset($modulePairs[$case->module])) echo "<span class='label label-gray label-badge'>{$modulePairs[$case->module]}</span> ";
                    echo $canView ? html::a($caseLink, $case->title, null, "style='color: $case->color' data-app='{$this->app->tab}'")
                        : "<span style='color: $case->color'>$case->title</span>";

                    $fromLink = ($fromCaseID and $canView) ? helper::createLink('testcase', 'view', "caseID=$fromCaseID") : '#';
                    $title    = $fromCaseID ? "[<i class='icon icon-share' title='{$this->lang->testcase->fromCaselib}'></i>#$fromCaseID]$autoIcon" : $autoIcon;
                    if($case->auto == 'auto') echo html::a($fromLink, $title, '', "data-app='{$this->app->tab}'");
                }
                else
                {
                    echo $case->title;
                }
                break;
            case 'branch':
                echo $branches[$case->branch];
                break;
            case 'type':
                echo $this->lang->testcase->typeList[$case->type];
                break;
            case 'stage':
                $stages = '';
                foreach(explode(',', trim($case->stage, ',')) as $stage) $stages .= $this->lang->testcase->stageList[$stage] . ',';
                $stages = trim($stages, ',');
                echo "<span title='$stages'>$stages</span>";
                break;
            case 'status':
                if($case->needconfirm)
                {
                    print("<span class='status-story status-changed' title='{$this->lang->story->changed}'>{$this->lang->story->changed}</span>");
                }
                elseif(isset($case->fromCaseVersion) and $case->fromCaseVersion > $case->version and !$case->needconfirm)
                {
                    print("<span class='status-story status-changed' title='{$this->lang->testcase->changed}'>{$this->lang->testcase->changed}</span>");
                }
                else
                {
                    print("<span class='status-testcase status-{$case->status}'>" . $this->processStatus('testcase', $case) . "</span>");
                }
                break;
            case 'story':
                static $stories = array();
                if(empty($stories)) $stories = $this->dao->select('id,title')->from(TABLE_STORY)->where('deleted')->eq('0')->andWhere('product')->eq($case->product)->fetchPairs('id', 'title');
                if($case->story and isset($stories[$case->story])) echo html::a(helper::createLink('story', 'view', "storyID=$case->story"), $stories[$case->story]);
                break;
            case 'precondition':
                echo $case->precondition;
                break;
            case 'keywords':
                echo $case->keywords;
                break;
            case 'version':
                if($isCase == 1) echo $case->version;
                break;
            case 'openedBy':
                echo zget($users, $case->openedBy);
                break;
            case 'openedDate':
                echo substr($case->openedDate, 5, 11);
                break;
            case 'reviewedBy':
                echo zget($users, $case->reviewedBy);
                break;
            case 'reviewedDate':
                 echo helper::isZeroDate($case->reviewedDate) ? '' : substr($case->reviewedDate, 5, 11);
                break;
            case 'lastEditedBy':
                echo zget($users, $case->lastEditedBy);
                break;
            case 'lastEditedDate':
                 echo helper::isZeroDate($case->lastEditedDate) ? '' : substr($case->lastEditedDate, 5, 11);
                break;
            case 'lastRunner':
                echo zget($users, $case->lastRunner);
                break;
            case 'lastRunDate':
                if(!helper::isZeroDate($case->lastRunDate)) echo substr($case->lastRunDate, 5, 11);
                break;
            case 'lastRunResult':
                if ($isCase == 1) {
                    $class = 'result-' . $case->lastRunResult;
                    $lastRunResultText = $case->lastRunResult ? zget($this->lang->testcase->resultList, $case->lastRunResult, $case->lastRunResult) : $this->lang->testcase->unexecuted;
                    echo "<span class='$class'>" . $lastRunResultText . "</span>";
                }
                break;
            case 'bugs':
                if ($isCase == 1) echo (common::hasPriv('testcase', 'bugs') and $case->bugs) ? html::a(helper::createLink('testcase', 'bugs', "runID=0&caseID={$case->id}"), $case->bugs, '', "class='iframe'") : $case->bugs;
                break;
            case 'results':
                if ($isCase == 1) echo (common::hasPriv('testtask', 'results') and $case->results) ? html::a(helper::createLink('testtask', 'results', "runID=0&caseID={$case->id}"), $case->results, '', "class='iframe'") : $case->results;
                break;
            case 'stepNumber':
                if ($isCase == 1) echo $case->stepNumber;
                break;
            case 'actions':
                if ($isCase == 1)
                {
                    $case->browseType = $browseType;
                    echo $this->buildOperateMenu($case, 'browse');
                    break;
                }
                else
                {
                    echo $this->buildOperateBrowseSceneMenu($case);
                }
            }
            echo '</td>';
        }
    }

    /**
     * 追加 bug 和用例执行结果信息
     * Append bugs and results.
     *
     * @param  array  $cases
     * @param  string $type
     * @param  array  $caseIdList
     * @access public
     * @return void
     */
    public function appendData(array $cases, string $type = 'case', array $caseIdList = array()): array
    {
        if(empty($caseIdList)) $caseIdList = array_keys($cases);

        /* 查询用例的 bugs 和结果。 */
        /* Get bugs and results. */
        $queryField = $type == 'case' ? '`case`' : '`result`';
        $caseBugs   = $this->dao->select('count(*) as count, `case`')->from(TABLE_BUG)->where($queryField)->in($caseIdList)->andWhere('deleted')->eq(0)->groupBy('`case`')->fetchPairs('case', 'count');
        $results    = $this->dao->select('count(*) as count, `case`')->from(TABLE_TESTRESULT)->where('`case`')->in($caseIdList)->groupBy('`case`')->fetchPairs('case', 'count');

        /* 查询用例的失败结果。 */
        /* Get result fails of the the testcases. */
        if($type != 'case') $queryField = '`run`';
        $caseFails  = $this->dao->select('count(*) as count, `case`')->from(TABLE_TESTRESULT)
            ->where('caseResult')->eq('fail')
            ->andWhere($queryField)->in($caseIdList)
            ->groupBy('`case`')
            ->fetchPairs('case','count');

        /* 查询用例的步骤。 */
        /* Get the the testcase steps. */
        $queryTable = $type == 'case' ? TABLE_CASE : TABLE_TESTRUN;
        $queryOn    = $type == 'case' ? 't1.`case`=t2.`id`' : 't1.`case`=t2.`case`';
        $queryField = $type == 'case' ? 't1.`case`' : 't2.`id`';
        $steps = $this->dao->select('count(distinct t1.id) as count, t1.`case`')->from(TABLE_CASESTEP)->alias('t1')
            ->leftJoin($queryTable)->alias('t2')->on($queryOn)
            ->where($queryField)->in($caseIdList)
            ->andWhere('t1.type')->ne('group')
            ->andWhere('t1.version=t2.version')
            ->groupBy('t1.`case`')
            ->fetchPairs('case', 'count');

        /* 设置测试用例的 bugs 执行结果和步骤。 */
        /* Set related bugs, results and steps of the testcases. */
        foreach($cases as $key => $case)
        {
            $caseID = $type == 'case' ? $case->id : $case->case;
            $case->bugs       = isset($caseBugs[$caseID])  ? $caseBugs[$caseID]   : 0;
            $case->results    = isset($results[$caseID])   ? $results[$caseID]    : 0;
            $case->caseFails  = isset($caseFails[$caseID]) ? $caseFails[$caseID]  : 0;
            $case->stepNumber = isset($steps[$caseID])     ? $steps[$caseID]      : 0;
        }
        return $cases;
    }

    /**
     * Check whether force not review.
     *
     * @access public
     * @return bool
     */
    public function forceNotReview()
    {
        if(empty($this->config->testcase->needReview))
        {
            if(!isset($this->config->testcase->forceReview)) return true;
            if(strpos(",{$this->config->testcase->forceReview},", ",{$this->app->user->account},") === false) return true;
        }
        if($this->config->testcase->needReview && isset($this->config->testcase->forceNotReview) && strpos(",{$this->config->testcase->forceNotReview},", ",{$this->app->user->account},") !== false) return true;

        return false;
    }

    /**
     * Summary cases
     *
     * @param  array    $cases
     * @access public
     * @return string
     */
    public function summary($cases)
    {
        $executed = 0;
        foreach($cases as $case)
        {
            if($case->lastRunResult != '') $executed ++;
        }

        return sprintf($this->lang->testcase->summary, count($cases), $executed);
    }

    /**
     * Sync case to project.
     *
     * @param  object $case
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function syncCase2Project($case, $caseID)
    {
        $projects = array();
        if(!empty($case->story))
        {
            $projects = $this->dao->select('project')->from(TABLE_PROJECTSTORY)->where('story')->eq($case->story)->fetchPairs();
        }
        elseif($this->app->tab == 'project' and empty($case->story))
        {
            $projects = array($this->session->project);
        }
        elseif($this->app->tab == 'execution' and empty($case->story))
        {
            $projects = array($this->session->execution);
        }
        if(empty($projects)) return;

        $this->loadModel('action');
        $objectInfo = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->in($projects)->fetchAll('id');

        foreach($projects as $projectID)
        {
            $lastOrder = (int)$this->dao->select('*')->from(TABLE_PROJECTCASE)->where('project')->eq($projectID)->orderBy('order_desc')->limit(1)->fetch('order');
            $data = new stdclass();
            $data->project = $projectID;
            $data->product = $case->product;
            $data->case    = $caseID;
            $data->version = 1;
            $data->order   = ++ $lastOrder;
            $this->dao->insert(TABLE_PROJECTCASE)->data($data)->exec();

            $object     = $objectInfo[$projectID];
            $objectType = $object->type;
            if($objectType == 'project') $this->action->create('case', $caseID, 'linked2project', '', $projectID);
            if(in_array($objectType, array('sprint', 'stage')) and $object->multiple) $this->action->create('case', $caseID, 'linked2execution', '', $projectID);
        }
    }

    /**
     * processDatas
     *
     * @param  array  $datas
     * @access public
     * @return void
     */
    public function processDatas($datas)
    {
        if(isset($datas->datas)) $datas = $datas->datas;
        $columnKey  = array();
        $caseData   = array();
        $stepData   = array();
        $stepVars   = 0;

        foreach($datas as $row => $cellValue)
        {
            foreach($cellValue as $field => $value)
            {
                if($field != 'stepDesc' and $field != 'stepExpect') continue;
                if($field == 'stepDesc' or $field == 'stepExpect')
                {
                    $steps = $value;
                    if(strpos($value, "\n"))
                    {
                        $steps = explode("\n", $value);
                    }
                    elseif(strpos($value, "\r"))
                    {
                        $steps = explode("\r", $value);
                    }
                    if(is_string($steps)) $steps = explode("\n", $steps);

                    $stepKey  = str_replace('step', '', strtolower($field));

                    $caseStep = array();
                    foreach($steps as $step)
                    {
                        $trimedStep = trim($step);
                        if(empty($trimedStep)) continue;
                        if(preg_match('/^(([0-9]+)\.[0-9]+)([.、]{1})/U', $step, $out) and ($field == 'stepDesc' or ($field == 'stepExpect' and isset($stepData[$row]['desc'][$out[1]]))))
                        {
                            $num     = $out[1];
                            $parent  = $out[2];
                            $sign    = $out[3];
                            $signbit = $sign == '.' ? 1 : 3;
                            $step    = trim(substr($step, strlen($num) + $signbit));
                            if(!empty($step)) $caseStep[$num]['content'] = $step;
                            $caseStep[$num]['type']    = 'item';
                            $caseStep[$parent]['type'] = 'group';
                        }
                        elseif(preg_match('/^([0-9]+)([.、]{1})/U', $step, $out) and ($field == 'stepDesc' or ($field == 'stepExpect' and isset($stepData[$row]['desc'][$out[1]]))))
                        {
                            $num     = $out[1];
                            $sign    = $out[2];
                            $signbit = $sign == '.' ? 1 : 3;
                            $step    = trim(substr($step, strpos($step, $sign) + $signbit));
                            if(!empty($step)) $caseStep[$num]['content'] = $step;
                            $caseStep[$num]['type'] = 'step';
                        }
                        elseif(isset($num))
                        {
                            if(!isset($caseStep[$num]['content'])) $caseStep[$num]['content'] = '';
                            if(!isset($caseStep[$num]['type']))    $caseStep[$num]['type']    = 'step';
                            $caseStep[$num]['content'] .= "\n" . $step;
                        }
                        else
                        {
                            if($field == 'stepDesc')
                            {
                                $num = 1;
                                $caseStep[$num]['content'] = $step;
                                $caseStep[$num]['type']    = 'step';
                            }
                            if($field == 'stepExpect' and isset($stepData[$row]['desc']))
                            {
                                end($stepData[$row]['desc']);
                                $num = key($stepData[$row]['desc']); $caseStep[$num]['content'] = $step;
                            }
                        }
                    }

                    unset($num);
                    unset($sign);
                    $stepVars += count($caseStep, COUNT_RECURSIVE) - count($caseStep);
                    $stepData[$row][$stepKey] = $caseStep;
                }

            }
        }
        return $stepData;
    }

    /**
     * Get modules for datatable.
     *
     * @param int $productID
     * @access public
     * @return void
     */
    public function getDatatableModules($productID)
    {
        $branches = $this->loadModel('branch')->getPairs($productID);
        $modules  = $this->loadModel('tree')->getOptionMenu($productID, 'case', '');
        if(count($branches) <= 1) return $modules;

        foreach($branches as $branchID => $branchName) $modules += $this->tree->getOptionMenu($productID, 'case', 0, $branchID);
        return $modules;
    }

    /**
     * Batch change scene.
     *
     * @param  array $caseIdList
     * @param  int   $sceneID
     * @access public
     * @return bool
     */
    public function batchChangeScene(array $caseIdList, int $sceneID): bool
    {
        if($sceneID < 0 || $sceneID > 16777215) return false; // The scene column's data type is mediumint unsigned and its range is 0-16777215.

        $caseIdList = array_filter($caseIdList);
        if(!$caseIdList) return false;

        $oldCases = $this->getByList($caseIdList, "scene != '{$sceneID}'");
        if(!$oldCases) return false;

        $case = new stdclass();
        $case->scene          = $sceneID;
        $case->lastEditedBy   = $this->app->user->account;
        $case->lastEditedDate = helper::now();

        $this->dao->update(TABLE_CASE)->data($case)->where('id')->in(array_keys($oldCases))->exec();
        if(dao::isError()) return false;

        $this->loadModel('action');

        foreach($oldCases as $oldCase)
        {
            $changes = common::createChanges($oldCase, $case);
            if($changes)
            {
                $actionID = $this->action->create('case', $oldCase->id, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        return !dao::isError();
    }

    /**
     * Build menu query.
     *
     * @param  int    $rootID
     * @param  int    $moduleID
     * @param  string $type
     * @param  int    $startScene
     * @param  string $branch
     * @access public
     * @return object
     */
    public function buildMenuQuery($rootID, $moduleID, $type, $startScene = 0, $branch = 'all')
    {
        /* Set the start module. */
        $startScenePath = '';
        if($startScene > 0)
        {
            $startScene = $this->dao->findById((int)$startScene)->from(VIEW_SCENECASE)->fetch();
            if($startScene) $startScenePath = $startScene->path . '%';
        }

        return $this->dao->select('*')->from(VIEW_SCENECASE)
            ->where('deleted')->eq(0)
            ->beginIF($rootID)->andWhere('product')->eq((int)$rootID)->fi()
            ->beginIF(intval($moduleID) > 0)->andWhere('module')->eq((int)$moduleID)->fi()
            ->beginIF($startScenePath)->andWhere('path')->like($startScenePath)->fi()
            ->beginIF($branch !== 'all' and $branch !== '' and $branch !== false)->andWhere('branch')->eq((int)$branch)->fi()
            ->andWhere('isCase')->eq(2)
            ->orderBy('grade desc, sort')
            ->get();
    }

    /**
     * Build operate browse scene menu.
     *
     * @param  object $scene
     * @access public
     * @return string
     */
    public function buildOperateBrowseSceneMenu($scene)
    {
        $canBeChanged = common::canBeChanged('case', $scene);
        if(!$canBeChanged) return '';

        $params = "sceneID=$scene->id";

        /* Generate params for editing scene. */
        $editParams = $params;
        if($this->app->tab == 'project')   $editParams .= "&projectID={$this->session->project}";
        if($this->app->tab == 'execution') $editParams .= "&executionID={$this->session->execution}";

        $menu  = $this->buildMenu('testcase', 'editScene',   $editParams, $scene, 'browse', 'edit',  '',          '', '', '', $this->lang->testcase->editScene);
        $menu .= $this->buildMenu('testcase', 'deleteScene', $params,     $scene, 'browse', 'trash', 'hiddenwin', '', '', '', $this->lang->testcase->deleteScene);

        return $menu;
    }

    /**
     * Search form add scene.
     *
     * @param  int    $productID
     * @param  array  $products
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  int    $projectID
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function buildSearchFormAddScene($productID, $products, $queryID, $actionURL, $projectID = 0,$moduleID = 0)
    {
        $product = ($this->app->tab == 'project' and empty($productID)) ? $products : array($productID => $products[$productID]) + array('all' => $this->lang->testcase->allProduct);
        $this->config->testcase->search['params']['product']['values'] = $product;

        $module = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0);
        $scene  = $this->getSceneMenu($productID, $moduleID, $viewType = 'case', $startSceneID = 0,  0);

        if(!$productID)
        {
            $module = array();
            foreach($products as $id => $product) $module += $this->loadModel('tree')->getOptionMenu($id, 'case', 0);
        }

        $this->config->testcase->search['params']['module']['values'] = $module;
        $this->config->testcase->search['params']['parent']['values'] = $scene;
        $this->config->testcase->search['params']['lib']['values']    = $this->loadModel('caselib')->getLibraries();

        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->testcase->search['fields']['branch']);
            unset($this->config->testcase->search['params']['branch']);
        }
        else
        {
            $productInfo = $this->loadModel('product')->getByID($productID);

            $this->config->testcase->search['fields']['branch']           = sprintf($this->lang->product->branch, $this->lang->product->branchName[$productInfo->type]);
            $this->config->testcase->search['params']['branch']['values'] = array('' => '', '0' => $this->lang->branch->main) + $this->loadModel('branch')->getPairs($productID, '', $projectID) + array('all' => $this->lang->branch->all);
        }

        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);
        $this->config->testcase->search['actionURL'] = $actionURL;
        $this->config->testcase->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->testcase->search);
    }

    /**
     * Build tree array.
     *
     * @param  array  $treeMenu
     * @param  array  $scenes
     * @param  object $scene
     * @param  string $sceneName
     * @access public
     * @return void
     */
    public function buildTreeArray(& $treeMenu, $scenes, $scene, $sceneName = '/')
    {
        $parentScenes = explode(',', $scene->path);
        foreach($parentScenes as $parentSceneID)
        {
            if(empty($parentSceneID)) continue;
            if(empty($scenes[$parentSceneID])) continue;

            $sceneName .= $scenes[$parentSceneID]->title . '/';
        }

        $sceneName  = rtrim($sceneName, '/');
        $sceneName .= "|$scene->id\n";

        if(isset($treeMenu[$scene->id]) and !empty($treeMenu[$scene->id]))
        {
            if(isset($treeMenu[$scene->parent]))
            {
                $treeMenu[$scene->parent] .= $sceneName;
            }
            else
            {
                $treeMenu[$scene->parent] = $sceneName;
            }
            $treeMenu[$scene->parent] .= $treeMenu[$scene->id];
        }
        else
        {
            if(isset($treeMenu[$scene->parent]) and !empty($treeMenu[$scene->parent]))
            {
                $treeMenu[$scene->parent] .= $sceneName;
            }
            else
            {
                $treeMenu[$scene->parent] = $sceneName;
            }
        }
    }

    /**
     * 根据 ID 获取一个场景。
     * Get a scene by id.
     *
     * @param  int    $sceneID
     * @access public
     * @return object
     */
    public function getSceneByID(int $sceneID): object|bool
    {
        return $this->dao->select('*')->from(TABLE_SCENE)->where('id')->eq($sceneID)->fetch();
    }

    /**
     * Create a scene.
     *
     * @access public
     * @return bool
     */
    public function createScene(object $scene): bool
    {
        $product = zget($scene, 'product', 0);
        $this->dao->insert(TABLE_SCENE)->data($scene)
            ->autoCheck()
            ->batchCheck($this->config->testcase->createscene->requiredFields, 'notempty')
            ->checkIF($product, 'title', 'unique', "product = {$product}")
            ->checkFlow()
            ->exec();
        if(dao::isError()) return false;

        $sceneID = $this->dao->lastInsertID();

        $scene->sort  = $sceneID;
        $scene->path  = ',' . $sceneID . ',';
        $scene->grade = 1;

        if(!empty($scene->parent))
        {
            $parent = $this->getSceneByID($scene->parent);
            if($parent)
            {
                $scene->path    = $parent->path . $sceneID . ',';
                $scene->grade   = ++$parent->grade;
                $scene->product = $parent->product;
                $scene->branch  = $parent->branch;
                $scene->module  = $parent->module;
            }
            else
            {
                $scene->parent = 0;
            }
        }

        $this->dao->update(TABLE_SCENE)->data($scene)->where('id')->eq($sceneID)->exec();
        if(dao::isError()) return false;

        $this->loadModel('action')->create('scene', $sceneID, 'Opened');

        return !dao::isError();
    }

    /**
     * Get all children id.
     *
     * @param  int $sceneID
     * @access public
     * @return object
     */
    public function getAllChildId($sceneID)
    {
        if($sceneID == 0) return array();

        $scene = $this->dao->findById((int)$sceneID)->from(VIEW_SCENECASE)->fetch();
        if(empty($scene)) return array();

        return $this->dao->select('id')->from(VIEW_SCENECASE)
            ->where('path')->like($scene->path . '%')
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();
    }

    /**
     * Get scenes by id list and query string.
     *
     * @param  array  $sceneIdList
     * @param  string $query
     * @access public
     * @return array
     */
    public function getScenesByList(array $sceneIdList, string $query = ''): array
    {
        if(!$sceneIdList) return array();

        return $this->dao->select('*')->from(TABLE_SCENE)
            ->where('deleted')->eq('0')
            ->andWhere('id')->in($sceneIdList)
            ->beginIF($query)->andWhere($query)->fi()
            ->fetchAll('id');
    }

    /**
     * Get scene list include sub scenes and cases.
     *
     * @param  int    $productID
     * @param  string $branch
     * @param  int    $moduleID
     * @param  string $caseType
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getSceneGroups(int $productID, string $branch = '', string $browseType = '', int $moduleID = 0, string $caseType = '', string $orderBy = 'id_desc', object $pager = null): array
    {
        $modules = $moduleID ? $this->loadModel('tree')->getAllChildId($moduleID) : '0';
        $scenes = $this->dao->select('*')->from(TABLE_SCENE)
            ->where('deleted')->eq('0')
            ->andWhere('product')->eq($productID)
            ->beginIF($branch !== 'all')->andWhere('branch')->eq($branch)->fi()
            ->beginIF($modules)->andWhere('module')->in($modules)->fi()
            ->orderBy('grade_desc, sort_asc')
            ->fetchAll('id');

        $pager->recTotal = 0;

        if(!$scenes) return array();

        $cases = array();
        if($scenes && $browseType != 'onlyscene')
        {
            $stmt = $this->dao->select('t1.*')->from(TABLE_CASE)->alias('t1');

            if($this->app->tab == 'project') $stmt = $stmt->leftJoin(TABLE_PROJECTCASE)->alias('t2')->on('t1.id=t2.case');

            $caseList = $stmt->where('t1.deleted')->eq('0')
                ->andWhere('t1.scene')->ne(0)
                ->andWhere('t1.product')->eq($productID)
                ->beginIF($this->app->tab == 'project')->andWhere('t2.project')->eq($this->session->project)->fi()
                ->beginIF($branch !== 'all')->andWhere('t1.branch')->eq($branch)->fi()
                ->beginIF($modules)->andWhere('t1.module')->in($modules)->fi()
                ->beginIF($this->cookie->onlyAutoCase)->andWhere('t1.auto')->eq('auto')->fi()
                ->beginIF(!$this->cookie->onlyAutoCase)->andWhere('t1.auto')->ne('unit')->fi()
                ->beginIF($caseType)->andWhere('t1.type')->eq($caseType)->fi()
                ->orderBy($orderBy)
                ->fetchAll('id');

            $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);
            $caseList = $this->loadModel('story')->checkNeedConfirm($caseList);
            $caseList = $this->appendData($caseList);
            foreach($caseList as $case) $cases[$case->scene][$case->id] = $case;
        }

        $this->dao->setTable(TABLE_CASE);
        $fieldTypes = $this->dao->getFieldsType();

        foreach($scenes as $id => $scene)
        {
            /* Set default value for the fields exist in TABLE_CASE but not in TABLE_SCENE. */
            foreach($fieldTypes as $field => $type)
            {
                if(isset($scene->$field)) continue;
                $scene->$field = $type['rule'] == 'int' ? '0' : '';
            }

            $scene->caseID     = $scene->id;
            $scene->bugs       = 0;
            $scene->results    = 0;
            $scene->caseFails  = 0;
            $scene->stepNumber = 0;
            $scene->isScene    = true;

            if(isset($cases[$id]))
            {
                foreach($cases[$id] as $case)
                {
                    $case->caseID  = $case->id;
                    $case->id      = 'case_' . $case->id;
                    $case->parent  = $id;
                    $case->grade   = $scene->grade + 1;
                    $case->path    = $scene->path . $case->id . ',';
                    $case->isScene = false;

                    $scene->cases[$case->id] = $case;
                }
            }

            if(!isset($scenes[$scene->parent])) continue;

            $parent = $scenes[$scene->parent];
            $parent->children[$id] = $scene;

            unset($scenes[$id]);
        }

        $pager->recTotal  = count($scenes);
        $pager->pageTotal = ceil($pager->recTotal / $pager->recPerPage);

        return array_slice($scenes, $pager->recPerPage * ($pager->pageID - 1), $pager->recPerPage);
    }

    /**
     * Get scene menu.
     *
     * @param  int    $rootID
     * @param  int    $moduleID
     * @param  string $type
     * @param  int    $startScene
     * @param  int    $branch
     * @param  int    $currentScene
     * @param  bool   $emptyMenu
     * @access public
     * @return array
     */
    public function getSceneMenu($rootID, $moduleID, $type = '', $startScene = 0, $branch = 0, $currentScene = 0, $emptyMenu = false)
    {
        if(empty($branch)) $branch = 0;

        /* If type of $branch is array, get scenes of these branches. */
        if(is_array($branch))
        {
            $scenes = array();
            foreach($branch as $branchID) $scenes[$branchID] = $this->getOptionMenu($rootID,$moduleID, $type, $startScene, $branchID,$currentScene);

            return $scenes;
        }

        if($type == 'line') $rootID = 0;

        $branches = array($branch => '');
        if($branch != 'all' and strpos('story|bug|case', $type) !== false)
        {
            $product = $this->loadModel('product')->getById($rootID);
            if($product and $product->type != 'normal')
            {
                $branchPairs = $this->loadModel('branch')->getPairs($rootID, 'all');
                $branches    = array($branch => $branchPairs[$branch]);
            }
            elseif($product and $product->type == 'normal')
            {
                $branches = array(0 => '');
            }
        }

        $treeMenu = array();
        foreach($branches as $branchID => $branch)
        {
            $scenes = array();
            $stmt   = $this->dbh->query($this->buildMenuQuery($rootID, $moduleID, $type, $startScene, $branchID));
            while($scene = $stmt->fetch())
            {
                if ($scene->id != $currentScene) $scenes[$scene->id] = $scene;
            }

            foreach($scenes as $scene)
            {
                $branchName = (!empty($product) and $product->type != 'normal' and $scene->branch === BRANCH_MAIN) ? $this->lang->branch->main : $branch;

                $this->buildTreeArray($treeMenu, $scenes, $scene, (empty($branchName)) ? '/' : "/$branchName/");
            }
        }

        ksort($treeMenu);
        $topMenu = @array_shift($treeMenu);
        $topMenu = explode("\n", trim((string)$topMenu));
        $lastMenu[] = '/';
        foreach($topMenu as $menu)
        {
            if(!strpos($menu, '|')) continue;
            list($label, $sceneID) = explode('|', $menu);
            $lastMenu[$sceneID]    = $label;
        }

        /* Attach empty option. */
        if($emptyMenu) $lastMenu['null'] = $this->lang->null;

        return $lastMenu;
    }

    /**
     * Get scene name.
     *
     * @param  array $moduleIdList
     * @param  bool  $allPath
     * @param  bool  $branchPath
     * @access public
     * @return array
     */
    public function getScenesName($moduleIdList, $allPath = true, $branchPath = false)
    {
        if(!$allPath) return $this->dao->select('id, title')->from(VIEW_SCENECASE)->where('id')->in($moduleIdList)->andWhere('deleted')->eq(0)->fetchPairs('id', 'title');

        $modules    = $this->dao->select('id, title, path, branch')->from(VIEW_SCENECASE)->where('id')->in($moduleIdList)->andWhere('deleted')->eq(0)->fetchAll('path');
        $allModules = $this->dao->select('id, title')->from(VIEW_SCENECASE)->where('id')->in(join(array_keys($modules)))->andWhere('deleted')->eq(0)->fetchPairs('id', 'title');

        $branchIDList = array();
        $modulePairs  = array();
        foreach($modules as $module)
        {
            $paths = explode(',', trim($module->path, ','));
            $moduleName = '';
            foreach($paths as $path) $moduleName .= '/' . $allModules[$path];
            $modulePairs[$module->id] = $moduleName;

            if($module->branch) $branchIDList[$module->branch] = $module->branch;
        }

        if(!$branchPath) return $modulePairs;

        $branchs  = $this->dao->select('id, title')->from(VIEW_SCENECASE)->where('id')->in($branchIDList)->andWhere('deleted')->eq(0)->fetchALL('id');
        foreach($modules as $module)
        {
            if(isset($modulePairs[$module->id]))
            {
                $branchName = isset($branchs[$module->branch]) ? '/' . $branchs[$module->branch]->name : '';
                $modulePairs[$module->id] = $branchName . $modulePairs[$module->id];
            }
        }

        return $modulePairs;
    }

    /**
     * Substr string.
     *
     * @param  string $text
     * @param  int    $length
     * @access public
     * @return string
     */
    public function istrcut($text, $length)
    {
        return (mb_strlen($text, 'utf8') > $length) ? mb_substr($text, 0, $length, 'utf8').'...' : $text;
    }

    /**
     * Print table head.
     *
     * @param  object $col
     * @param  string $orderBy
     * @param  string $vars
     * @param  bool   $checkBox
     * @access public
     * @return string
     */
    public function printHead($col, $orderBy, $vars, $checkBox = true)
    {
        $id = $col->id;
        if($col->show)
        {
            $fixed = $col->fixed == 'no' ? 'true' : 'false';
            $width = is_numeric($col->width) ? "{$col->width}px" : $col->width;
            $title = isset($col->title) ? "title='$col->title'" : '';
            $title = (isset($col->name) and $col->name) ? "title='$col->name'" : $title;
            if($id == 'id' and (int)$width < 90) $width = '120px';

            $align = $id == 'actions' ? 'text-center' : '';
            $align = in_array($id, array('budget', 'teamCount', 'estimate', 'consume', 'consumed', 'left')) ? 'text-right' : $align;

            $style  = '';
            $data   = '';
            $data  .= "data-width='$width'";
            $style .= "width:$width;";

            if(isset($col->minWidth))
            {
                $data  .= "data-minWidth='{$col->minWidth}px'";
                $style .= "min-width:{$col->minWidth}px;";
            }

            if(isset($col->maxWidth))
            {
                $data  .= "data-maxWidth='{$col->maxWidth}px'";
                $style .= "max-width:{$col->maxWidth}px;";
            }

            if(isset($col->pri)) $data .= "data-pri='{$col->pri}'";
            if($col->title == $this->lang->testcase->title)
            {
                echo "<th data-flex='$fixed' $data style='$style' class='c-$id $align' title='".$this->lang->testcase->generalTitle."'>";
            }
            else
            {
                echo "<th data-flex='$fixed' $data style='$style' class='c-$id $align' $title>";
            }

            if($id == 'actions')
            {
                echo $this->lang->actions;
            }
            else
            {
                if($id == 'id' && $checkBox) echo "<div class='checkbox-primary check-all' title='".$this->lang->selectAll."'><label></label></div>";
                if($col->title == $this->lang->testcase->title)
                {
                    echo $this->lang->testcase->generalTitle;
                }
                else
                {
                    echo $col->title;
                }
            }

            echo '</th>';
        }
    }

    /**
     * Update scene.
     *
     * @param  int $sceneID
     * @access public
     * @return string
     */
    public function updateScene($sceneID)
    {
        /* Get original data. */
        $scene = $this->dao->findById((int)$sceneID)->from(VIEW_SCENECASE)->fetch();
        $now   = helper::now();

        /* Collect changed data. */
        $scenePost = fixer::input('post')
            ->add('id', $sceneID - CHANGEVALUE)
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->add('lastEditedDate', $now)
            ->cleanInt('product,module')
            ->get();

        /* Update scene with changed data. */
        $this->dao->update(TABLE_SCENE)->data($scenePost)
            ->batchCheck($this->config->testcase->createscene->requiredFields, 'notempty')
            ->where('id')->eq((int)$sceneID - CHANGEVALUE)
            ->checkFlow()
            ->exec();

        /* Verify database error. */
        if($this->dao->isError()) return;

        $sceneNew    = $this->dao->findById((int)$sceneID - CHANGEVALUE)->from(TABLE_SCENE)->fetch();
        $childPath   = "";
        $grade       = "";
        $viewSceneID = $sceneID;

        /* Not change key fields. */
        if($scene->parent == $sceneNew->parent and $scene->product == $sceneNew->product and $scene->branch == $sceneNew->branch and $scene->module == $sceneNew->module) return array('status' => 'updated', 'id' => $sceneID);;

        if($sceneNew->parent)
        {
            /* Update product, module, branch, path and grade field with parent scene. */
            $resultScene = $this->dao->findById((int)$sceneNew->parent - CHANGEVALUE)->from(TABLE_SCENE)->fetch();
            $childPath   = $resultScene->path . $viewSceneID . ',';
            $grade       = $resultScene->grade + 1;
            $this->dao->update(TABLE_SCENE)
                ->set('path')->eq($childPath)
                ->set('grade')->eq($grade)
                ->set('product')->eq($resultScene->product)
                ->set('module')->eq($resultScene->module)
                ->set('branch')->eq($resultScene->branch)
                ->where('id')->eq($sceneID - CHANGEVALUE)
                ->exec();
        }
        else
        {
            /* Update path and grade field without parent scene. */
            $childPath = ",$viewSceneID,";
            $grade     = 1;
            $this->dao->update(TABLE_SCENE)
                ->set('path')->eq($childPath)
                ->set('grade')->eq($grade)
                ->where('id')->eq($sceneID - CHANGEVALUE)
                ->exec();
        }

        /* Get children scenes and cases. */
        $children = $this->dao->select('*')->from(VIEW_SCENECASE)
            ->where('deleted')->eq(0)
            ->andWhere('(path')->like($scene->path.'%')
            ->orWhere('path')->like(",$sceneID,%")
            ->markRight(1)
            ->orderBy('grade asc')
            ->fetchAll('id');

        foreach($children as $id => $childScene)
        {
            if(!$id or $id == $sceneID or !$childScene->parent) continue;

            /* Get grade of child with parent scene. */
            $parentScene = $this->dao->findById($childScene->parent - CHANGEVALUE)->from(TABLE_SCENE)->fetch();

            /* Update sub-scene. */
            if($childScene->isCase == 2)
            {
                /* The grade of child node must be greater than parent node. */
                if($childScene->grade <= $scene->grade) continue;

                $viewID    = $id;
                $childPath = $parentScene->path . $viewID . ',';
                $grade     = $parentScene->grade + 1;

                $this->dao->update(TABLE_SCENE)
                    ->set('path')->eq($childPath)
                    ->set('grade')->eq($grade)
                    ->set('product')->eq($parentScene->product)
                    ->set('module')->eq($parentScene->module)
                    ->set('branch')->eq($parentScene->branch)
                    ->set('lastEditedBy')->eq($this->app->user->account)
                    ->set('lastEditedDate')->eq($now)
                    ->where('id')->eq($id - CHANGEVALUE)
                    ->exec();

                continue;
            }

            /* Update case. */
            $this->dao->update(TABLE_CASE)
                ->set('product')->eq($parentScene->product)
                ->set('module')->eq($parentScene->module)
                ->set('branch')->eq($parentScene->branch)
                ->set('lastEditedBy')->eq($this->app->user->account)
                ->set('lastEditedDate')->eq($now)
                ->where('id')->eq($id)
                ->exec();
        }

        return array('status' => 'updated', 'id' => $sceneID);
    }

    /**
     * Get xmind file content.
     *
     * @param  string $fileName
     * @access public
     * @return string
     */
    public function getXmindImport($fileName)
    {
        $xmlNode  = simplexml_load_file($fileName);
        $testData = $this->xmlToArray($xmlNode);

        return json_encode($testData);
    }

    /**
     * Save xmind file content to database.
     *
     * @access public
     * @return array
     */
    public function saveXmindImport()
    {
        $this->dao->begin();

        $sceneIds  = array();
        $sceneList = $this->post->sceneList;
        foreach($sceneList as $scene)
        {
            $tmpId  = $scene["tmpId"];
            $tmpPId = $scene["tmpPId"];

            $result = $this->saveScene($scene,$sceneIds);
            /* Rollback. */
            if($result["result"] == "fail")
            {
                $this->dao->rollBack();
                return $result;
            }

            $sceneIds[$tmpId] = array("id"=>$result['sceneID'], "tmpPId"=>$tmpPId);
        }

        $testcaseList = $this->post->testcaseList;
        foreach($testcaseList as $testcase)
        {
            $tmpId  = $testcase["tmpId"];
            $tmpPId = $testcase["tmpPId"];

            $result = $this->saveTestcase($testcase,$sceneIds);
            if($result["result"] == "fail")
            {
                $this->dao->rollBack();
                return $result;
            }

            $sceneIds[$tmpId] = array("id"=>$result['testcaseID'], "tmpPId"=>$tmpPId);
        }

        $this->dao->commit();

        return array("result"=>"success","message"=>1);
    }

    /**
     * Save test case.
     *
     * @param  array $testcaseData
     * @param  array $sceneIds
     * @access public
     * @return array
     */
    public function saveTestcase($testcaseData, $sceneIds)
    {
        $tmpPId = $testcaseData["tmpPId"];
        $scene  = 0;

        if(isset($sceneIds[$tmpPId]))
        {
            $pScene = $sceneIds[$tmpPId];
            $scene  = $pScene["id"] + CHANGEVALUE;
        }

        $id         = isset($testcaseData["id"]) ? $testcaseData["id"] : -1;
        $module     = $testcaseData["module"];
        $product    = $testcaseData["product"];
        $branch     = $testcaseData["branch"];
        $title      = $testcaseData["name"];
        $pri        = $testcaseData["pri"];
        $now        = helper::now();
        $testcaseID = -1;
        $version    = 1;

        if(!isset($testcaseData["id"]))
        {
            $testcase             = new stdclass();
            $testcase->scene      = $scene;
            $testcase->module     = $module;
            $testcase->product    = $product;
            $testcase->branch     = $branch;
            $testcase->title      = $title;
            $testcase->pri        = $pri;
            $testcase->type       = "feature";
            $testcase->status     = "normal";
            $testcase->version    = $version;
            $testcase->openedBy   = $this->app->user->account;
            $testcase->openedDate = $now;

            $this->dao->insert(TABLE_CASE)->data($testcase)->autoCheck()->exec();
            $testcaseID = $this->dao->lastInsertID();

            $order = new stdclass();
            $order->sort = $testcaseID;
            $this->dao->update(TABLE_CASE)->data($order)->where('id')->eq((int)$testcaseID)->exec();
        }
        else
        {
            $oldCase = $this->dao->select('version,id')->from(TABLE_CASE)->where('id')->eq((int)$id)->fetch();

            if(isset($oldCase->id))
            {
                if(!isset($oldCase->version)) return array('result' => 'fail', 'message' => 'not exist testcase');

                $version  = $oldCase->version + 1;

                $testcase                 = new stdclass();
                $testcase->id             = $id;
                $testcase->scene          = $scene;
                $testcase->module         = $module;
                $testcase->product        = $product;
                $testcase->branch         = $branch;
                $testcase->title          = $title;
                $testcase->pri            = $pri;
                $testcase->version        = $version;
                $testcase->lastEditedBy   = $this->app->user->account;
                $testcase->lastEditedDate = $now;

                $testcaseID = $id;
                $this->dao->update(TABLE_CASE)->data($testcase)->where('id')->eq((int)$id)->exec();
            }
            else
            {
                $testcase             = new stdclass();
                $testcase->scene      = $scene;
                $testcase->module     = $module;
                $testcase->product    = $product;
                $testcase->branch     = $branch;
                $testcase->title      = $title;
                $testcase->pri        = $pri;
                $testcase->type       = "feature";
                $testcase->status     = "normal";
                $testcase->version    = $version;
                $testcase->openedBy   = $this->app->user->account;
                $testcase->openedDate = $now;

                $this->dao->insert(TABLE_CASE)->data($testcase)->autoCheck()->exec();
                $testcaseID = $this->dao->lastInsertID();

                $order       = new stdclass();
                $order->sort = $testcaseID;
                $this->dao->update(TABLE_CASE)->data($order)->where('id')->eq((int)$testcaseID)->exec();
            }
        }

        if($this->dao->isError())
        {
            return array('result' => 'fail', 'message' => $this->dao->getError(true));
        }

        $stepList = isset($testcaseData["stepList"]) ? $testcaseData["stepList"] : array();
        if(isset($stepList))
        {
            foreach($stepList as $step)
            {
                $tmpPId = $step["tmpPId"];
                $pObj   = isset($sceneIds[$tmpPId]) ? $sceneIds[$tmpPId] : array();

                $parent = 0;
                if(isset($sceneIds[$tmpPId])) $parent = $pObj["id"];

                $case   = $testcaseID;
                $type   = $step["type"];
                $desc   = $step["desc"];
                $expect = isset($step["expect"]) ? $step["expect"] : '';

                $casestep            = new stdclass();
                $casestep->case      = $case;
                $casestep->version   = $version;
                $casestep->type      = $type;
                $casestep->parent    = $parent;
                $casestep->desc      = $desc;
                $casestep->expect    = $expect;

                $this->dao->insert(TABLE_CASESTEP)->data($casestep)->autoCheck()->exec();
                $casestepID = $this->dao->lastInsertID();

                if($this->dao->isError()) return array('result' => 'fail', 'message' => $this->dao->getError(true));

                $sceneIds[$step["tmpId"]] = array("id"=>$casestepID, "tmpPId"=>$tmpPId);
            }
        }

        return array('result' => 'success', 'message' => 1,'testcaseID'=>$testcaseID);
    }

    /**
     * Save scene.
     *
     * @param  array $sceneData
     * @param  array $sceneIds
     * @access public
     * @return array
     */
    public function saveScene($sceneData, $sceneIds)
    {
        $id      = isset($sceneData["id"]) ? $sceneData["id"] : -1;
        $name    = $sceneData["name"];
        $module  = isset($sceneData["module"]) ? $sceneData["module"] : 0;
        $product = $sceneData["product"];
        $branch  = $sceneData["branch"];
        $now     = helper::now();
        $sceneID = -1;

        if(!isset($sceneData["id"]))
        {
            $scene             = new stdclass();
            $scene->title      = $name;
            $scene->module     = $module;
            $scene->product    = $product;
            $scene->branch     = $branch;
            $scene->openedBy   = $this->app->user->account;
            $scene->openedDate = $now;

            $this->dao->insert(TABLE_SCENE)->data($scene)->autoCheck()->exec();
            $sceneID = $this->dao->lastInsertID();

            $order       = new stdclass();
            $order->sort = ($sceneID + CHANGEVALUE);

            $this->dao->update(TABLE_SCENE)->data($order)->where('id')->eq((int)$sceneID)->exec();
        }
        else
        {
            $scene                 = new stdclass();
            $scene->title          = $name;
            $scene->module         = $module;
            $scene->product        = $product;
            $scene->branch         = $branch;
            $scene->lastEditedBy   = $this->app->user->account;
            $scene->lastEditedDate = $now;

            $sceneID      = $id;
            $affectedRows = $this->dao->update(TABLE_SCENE)->data($scene)->where('id')->eq((int)$id)->exec();
            if(empty($affectedRows)) return array('result' => 'fail', 'message' => sprintf($this->lang->testcase->errorSceneNotExist, $id));
        }

        if($this->dao->isError()) return array('result' => 'fail', 'message' => $this->dao->getError(true));

        $tmpPId = $sceneData["tmpPId"];
        $pScene = isset($sceneIds[$tmpPId]) ? $sceneIds[$tmpPId] : array();
        $parent = 0;
        $grade  = 1;
        $path   = ",".($sceneID + CHANGEVALUE).",";

        if(isset($sceneIds[$tmpPId]))
        {
            $parent      = $pScene["id"];
            $parentScene = $this->dao->findById((int)$parent)->from(TABLE_SCENE)->fetch();
            $path        = $parentScene->path . ($sceneID + CHANGEVALUE).",";
            $grade       = $parentScene->grade + 1;
        }

        if($parent != 0) $parent = $parent + CHANGEVALUE;

        $this->dao->update(TABLE_SCENE)
            ->set('parent')->eq($parent)
            ->set('path')->eq($path)
            ->set('grade')->eq($grade)
            ->where('id')->eq($sceneID)
            ->limit(1)
            ->exec();

        if($this->dao->isError()) return array('result' => 'fail', 'message' => $this->dao->getError(true));

        return array('result' => 'success', 'message' => 1,"sceneID"=>$sceneID);
    }

    /**
     * Get export data.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @param  int $branch
     * @access public
     * @return array
     */
    public function getXmindExport($productID, $moduleID, $branch)
    {
        $caseList   = $this->getCaseByProductAndModule($productID, $moduleID);
        $stepList   = $this->getStepByProductAndModule($productID, $moduleID);
        $sceneInfo  = $this->getSceneByProductAndModule($productID, $moduleID);
        $moduleList = $this->getModuleByProductAndModel($productID, $moduleID, $branch);

        $config = $this->getXmindConfig();

        return array(
                'caseList'  =>$caseList,
                'stepList'  =>$stepList,
                'sceneMaps' =>$sceneInfo['sceneMaps'],
                'topScenes' =>$sceneInfo['topScenes'],
                'moduleList'=>$moduleList,
                'config'    =>$config
            );
    }

    /**
     * Get module by product.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @param  int $branch
     * @access public
     * @return array
     */
    function getModuleByProductAndModel($productID, $moduleID, $branch)
    {
        $moduleList = array();

        if($moduleID > 0)
        {
            $module = $this->loadModel('tree')->getByID($moduleID);

            $moduleList[$module->id] = $module->name;
        }
        else
        {
            $moduleList = $this->loadModel('tree')->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, ($branch === 'all' or !isset($branches[$branch])) ? 0 : $branch);

            unset($moduleList['0']);
        }

        return $moduleList;
    }

    /**
     * Get case by product and module.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @access public
     * @return array
     */
    function getCaseByProductAndModule($productID, $moduleID)
    {
        $fields = "t2.id as productID,"
            . "t2.`name` as productName,"
            . "t3.id as moduleID,"
            . "t3.`name` as moduleName,"
            . "t4.id as sceneID,"
            . "t4.title as sceneName,"
            . "t1.id as testcaseID,"
            . "t1.title as `name`,"
            . "t1.pri";

        $caseList = $this->dao->select($fields)->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->leftJoin(TABLE_MODULE)->alias('t3')->on('t1.module = t3.id')
            ->leftJoin(TABLE_SCENE)->alias('t4')->on('t1.scene = t4.id+' . CHANGEVALUE)
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.product')->eq($productID)
            ->beginIF($moduleID > 0)->andWhere('t1.module')->eq($moduleID)->fi()
            ->fetchAll();

        return $caseList;
    }

    /**
     * Get step by product and module.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @access public
     * @return array
     */
    function getStepByProductAndModule($productID, $moduleID)
    {
        $fields = "t1.id as testcaseID,"
            . "t2.id as stepID,"
            . "t2.type,"
            . "t2.parent as parentID,"
            . "t2.`desc`,"
            . "t2.expect";

        $stepList = $this->dao->select($fields)->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_CASESTEP)->alias('t2')->on('t1.id = t2.`case` and t1.version = t2.version')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.product')->eq($productID)
            ->andWhere('t2.id')->gt('0')
            ->beginIF($moduleID > 0)->andWhere('t1.module')->eq($moduleID)->fi()
            ->fetchAll();

        return $stepList;
    }

    /**
     * Get scene by product and module.
     *
     * @param  int $productID
     * @param  int $moduleID
     * @access public
     * @return array
     */
    function getSceneByProductAndModule($productID, $moduleID)
    {
        $sceneList = $this->dao->select('id as sceneID, title as sceneName, path, parent as parentID, product as productID, module as moduleID')
            ->from(TABLE_SCENE)
            ->where('deleted')->eq(0)
            ->andWhere('product')->eq($productID)
            ->beginIF($moduleID > 0)->andWhere('module')->eq($moduleID)->fi()
            ->fetchAll();

        $sceneMaps = array();
        $topScenes = array();
        foreach($sceneList as $one)
        {
            if($one->parentID == 0) $topScenes[] = $one;

            $sceneMaps[$one->sceneID] = $one;
        }

        return array('sceneMaps'=>$sceneMaps,'topScenes'=>$topScenes);
    }

    /**
     * Check config.
     *
     * @param  string $str
     * @access public
     * @return bool
     */
    function checkConfigValue($str)
    {
        return preg_match("/^[a-zA-Z]{1,10}$/",$str);
    }

    /**
     * Save xmind config.
     *
     * @access public
     * @return array
     */
    function saveXmindConfig()
    {
        $configList = array();

        $module = $this->post->module;
        if(isset($module) && !empty($module))
        {
            if(!$this->checkConfigValue($module)) return array('result' => 'fail', 'message' => '模块特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'module','value'=>$module);
        }

        $scene = $this->post->scene;
        if(isset($scene) && !empty($scene))
        {
            if(!$this->checkConfigValue($scene)) return array('result' => 'fail', 'message' => '场景特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'scene','value'=>$scene);
        }

        $case = $this->post->case;
        if(isset($case) && !empty($case))
        {
            if(!$this->checkConfigValue($case)) return array('result' => 'fail', 'message' => '测试用例特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'case','value'=>$case);
        }

        $pri = $this->post->pri;
        if(isset($pri) && !empty($pri))
        {
            if(!$this->checkConfigValue($pri)) return array('result' => 'fail', 'message' => '优先级特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'pri','value'=>$pri);
        }

        $group = $this->post->group;
        if(isset($group) && !empty($group))
        {
            if(!$this->checkConfigValue($group)) return array('result' => 'fail', 'message' => '步骤分组特征字符串只能是1-10个字母');
            $configList[] = array('key'=>'group','value'=>$group);
        }

        $map = array();
        $map[strtolower($module)] = true;
        $map[strtolower($scene)]  = true;
        $map[strtolower($case)]   = true;
        $map[strtolower($pri)]    = true;
        $map[strtolower($group)]  = true;

        if(count($map) < 5) return array('result' => 'fail', 'message' => '特征字符串不能重复');

        $this->dao->begin();

        $this->dao->delete()->from(TABLE_CONFIG)
            ->where('owner')->eq($this->app->user->account)
            ->andWhere('module')->eq('testcase')
            ->andWhere('section')->eq('xmind')
            ->exec();

        foreach($configList as $one)
        {
            $config = new stdclass();

            $config->module  = 'testcase';
            $config->section = 'xmind';
            $config->key     = $one['key'];
            $config->value   = $one['value'];
            $config->owner   = $this->app->user->account;

            $this->dao->insert(TABLE_CONFIG)->data($config)->autoCheck()->exec();

            if($this->dao->isError())
            {
                $this->dao->rollBack();
                return array('result' => 'fail', 'message' => $this->dao->getError(true));
            }
        }

        $this->dao->commit();

        return array("result" => "success", "message" => 1);
    }

    /**
     * Get xmind config.
     *
     * @access public
     * @return array
     */
    function getXmindConfig()
    {
        $configItems = $this->dao->select("`key`,value")->from(TABLE_CONFIG)
            ->where('owner')->eq($this->app->user->account)
            ->andWhere('module')->eq('testcase')
            ->andWhere('section')->eq('xmind')
            ->fetchAll();

        $config = array();
        foreach($configItems as $item) $config[$item -> key] = $item -> value;

        if(!isset($config['module'])) $config['module'] = 'M';
        if(!isset($config['scene']))  $config['scene']  = 'S';
        if(!isset($config['case']))   $config['case']   = 'C';
        if(!isset($config['pri']))    $config['pri']    = 'P';
        if(!isset($config['group']))  $config['group']  = 'G';

        return $config;
    }

    /**
     * Convert xml to array.
     *
     * @param  object $xml
     * @param  array  $options
     * @access public
     * @return array
     */
    function xmlToArray($xml, $options = array())
    {
        $defaults = array(
            'namespaceRecursive' => false, // Get XML doc namespaces recursively
            'removeNamespace'    => true, // Remove namespace from resulting keys
            'namespaceSeparator' => ':', // Change separator to something other than a colon
            'attributePrefix'    => '', // Distinguish between attributes and nodes with the same name
            'alwaysArray'        => array(), // Array of XML tag names which should always become arrays
            'autoArray'          => true, // Create arrays for tags which appear more than once
            'textContent'        => 'text', // Key used for the text content of elements
            'autoText'           => true, // Skip textContent key if node has no attributes or child nodes
            'keySearch'          => false, // (Optional) search and replace on tag and attribute names
            'keyReplace'         => false, // (Optional) replace values for above search values
        );

        $options        = array_merge($defaults, $options);
        $namespaces     = $xml->getDocNamespaces($options['namespaceRecursive']);
        $namespaces[''] = null; // Add empty base namespace

        /* Get attributes from all namespaces. */
        $attributesArray = array();
        foreach($namespaces as $prefix => $namespace)
        {
            if($options['removeNamespace']) $prefix = '';

            foreach($xml->attributes($namespace) as $attributeName => $attribute)
            {
                // (Optional) replace characters in attribute name
                if($options['keySearch']) $attributeName = str_replace($options['keySearch'], $options['keyReplace'], $attributeName);

                $attributeKey = $options['attributePrefix'] . ($prefix ? $prefix . $options['namespaceSeparator'] : '') . $attributeName;
                $attributesArray[$attributeKey] = (string) $attribute;
            }
        }

        // Get child nodes from all namespaces
        $tagsArray = array();
        foreach($namespaces as $prefix => $namespace)
        {
            if($options['removeNamespace']) $prefix = '';

            foreach($xml->children($namespace) as $childXml)
            {
                // Recurse into child nodes
                $childArray      = $this->xmlToArray($childXml, $options);
                $childTagName    = key($childArray);
                $childProperties = current($childArray);

                // Replace characters in tag name
                if($options['keySearch']) $childTagName = str_replace($options['keySearch'], $options['keyReplace'], $childTagName);

                // Add namespace prefix, if any
                if($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

                if(!isset($tagsArray[$childTagName]))
                {
                    // Only entry with this key
                    // Test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] = in_array($childTagName, $options['alwaysArray'], true) || !$options['autoArray'] ? array($childProperties) : $childProperties;
                }
                elseif(is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName]) === range(0, count($tagsArray[$childTagName]) - 1))
                {
                    // Key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                }
                else
                {
                    // Key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        // Get text content of node
        $textContentArray = array();
        $plainText = trim((string) $xml);
        if($plainText !== '') $textContentArray[$options['textContent']] = $plainText;

        // Stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || $plainText === '' ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        // Return node as array
        return array($xml->getName() => $propertiesArray);
    }

    /**
     * Append case fails.
     *
     * @param  object $case
     * @param  string $from
     * @param  int    $taskID
     * @access public
     * @return object
     */
    public function appendCaseFails(object $case, string $from, int $taskID): object
    {
        $caseFails = $this->dao->select('COUNT(*) AS count')->from(TABLE_TESTRESULT)
            ->where('caseResult')->eq('fail')
            ->andwhere('`case`')->eq($case->id)
            ->beginIF($from == 'testtask')->andwhere('`run`')->eq($taskID)->fi()
            ->fetch('count');
        $case->caseFails = $caseFails;
        return $case;
    }

    /**
     * 添加步骤。
     * Append steps.
     *
     * @param  array  $steps
     * @param  int    $count
     * @access public
     * @return array
     */
    public function appendSteps(array $steps, int $count = 0): array
    {
        if($count == 0) $count = $this->config->testcase->defaultSteps;
        if(count($steps) < $count)
        {
            $step = new stdclass();
            $step->step   = '';
            $step->desc   = '';
            $step->expect = '';
            $step->type   = 'step';
            for($i = count($steps) + 1; $i <= $count; $i ++)
            {
                $data = clone $step;
                $data->name    = (string)$i;
                $steps[] = $data;
            }
        }
        return $steps;
    }
}
