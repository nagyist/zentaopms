<?php
class testreportTest
{
    public function __construct()
    {
        global $tester;
        global $app;
        $this->objectModel = $tester->loadModel('testreport');
        $this->testtask    = $tester->loadModel('testtask');
        $this->build       = $tester->loadModel('build');
        $app->loadLang('bug');
    }

    /**
     * 测试创建一个测试报告。
     * Test create a test report.
     *
     * @param  array        $param
     * @access public
     * @return array|object
     */
    public function createTest(array $param): array|object
    {
        $begin_date = date('Y-m-d');
        $end_date   = date('Y-m-d',strtotime("+7 day"));
        $builds     = '11';

        $testreport = new stdclass();
        $createFields = array('begin' => $begin_date, 'end' => $end_date, 'product' => '1', 'execution' => '101', 'tasks' => '1', 'objectID' => '1', 'objectType' => 'testtask', 'owner' => '', 'title' => '', 'report' => '', 'builds' => $builds, 'cases' => '');
        foreach($createFields as $field => $defaultValue) $testreport->{$field} = $defaultValue;
        foreach($param as $key => $value) $testreport->{$key} = $value;

        $reportID = $this->objectModel->create($testreport);
        if(dao::isError()) return dao::getError();
        $object = $this->objectModel->getByID($reportID);
        return $object;
    }

    /**
     * 测试更新一个测试报告。
     * Test update a test report.
     *
     * @param  int    $reportID
     * @param  array  $param
     * @access public
     * @return array|object
     */
    public function updateTest(int $reportID, array $param): array|object
    {
        $report = $oldReport = $this->objectModel->getByID($reportID);

        $begin_date = $report->begin;
        $end_date   = $report->end;
        $builds     = $report->builds;

        $createFields = array('begin' => $begin_date, 'end' => $end_date, 'product' => $report->product, 'execution' => $report->execution, 'tasks' => $report->tasks, 'objectID' => $report->objectID, 'objectType' => $report->objectType, 'owner' => '', 'title' => '', 'report' => '', 'builds' => $builds, 'cases' => '');
        foreach($createFields as $field => $defaultValue) $report->{$field} = $defaultValue;
        foreach($param as $key => $value) $report->{$key} = $value;

        unset($report->files);
        unset($oldReport->files);
        $this->objectModel->update($report, $oldReport);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getByID($reportID);
        return $object;
    }

    /**
     * 测试通过 id 获取测试报告。
     * Test get report by id.
     *
     * @param  int          $reportID
     * @access public
     * @return array|object|false
     */

    public function getByIdTest(int $reportID): array|object|false
    {
        $object = $this->objectModel->getById($reportID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * 测试获取测试报告列表。
     * Test get report list.
     *
     * @param  int    $objectID
     * @param  string $objectType
     * @param  int    $extra
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array|string
     */
    public function getListTest(int $objectID, string $objectType, int $extra = 0, string $orderBy = 'id_desc', object $pager = null): array|string
    {
        $objects = $this->objectModel->getList($objectID, $objectType, $extra, $orderBy, $pager);

        if(dao::isError()) return dao::getError();

        return implode(',', array_keys($objects));
    }

    /**
     * Get bug info and summary.
     *
     * @param  array  $tasksIDs
     * @param  array  $productIdList
     * @param  int    $reportID
     * @param  array  $buildIDs
     * @access public
     * @return array
     */
    public function getBug4ReportTest($taskIDs, $productIdList, $reportID, $buildIDs)
    {
        $tasks   = $taskIDs ? $this->testtask->getByList($taskIDs) : array();
        $builds  = $buildIDs ? $this->build->getByList($buildIDs) : array();
        $report  = $this->objectModel->getByID($reportID);

        $objects = $this->objectModel->getBug4Report($tasks, $productIdList, $report->begin, $report->end, $builds);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get task cases.
     *
     * @param  array  $taskID
     * @param  int    $reportID
     * @access public
     * @return array
     */

    public function getTaskCasesTest($taskID, $reportID, $idList = '', $pager = null)
    {
        $task   = $taskID ? $this->testtask->getByList($taskID) : array();
        $report  = $this->objectModel->getByID($reportID);

        $objects = $this->objectModel->getTaskCases($task, $report->begin, $report->end, $idList, $pager);

        if(dao::isError()) return dao::getError();

        return $objects;
    }
     /**
     * 测试获取测试报告的 caseID 列表。
     * Get caseID list.
     *
     * @param  int    $reportID
     * @access public
     * @return array
     */

    public function getCaseIdListTest($reportID): array
    {
        $objects = $this->objectModel->getCaseIdList($reportID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }
   /**
     * Get result summary.
     *
     * @param  int    $taskID
     * @param  int    $reportID
     * @access public
     * @return string
     */

    public function getResultSummaryTest($taskID, $reportID)
    {
        $tasks   = $taskID ? $this->testtask->getByList($taskID) : array();
        $report = $this->objectModel->getByID($reportID);
        $cases  = $this->objectModel->getTaskCases($tasks, $report->begin, $report->end);

        $objects = $this->objectModel->getResultSummary($tasks, $cases, $report->begin, $report->end);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Get per run result for testreport.
     *
     * @param  int     $taskID
     * @param  int     $reportID
     * @access public
     * @return string
     */

    public function getPerCaseResult4ReportTest($taskID, $reportID)
    {
        $tasks   = $taskID ? $this->testtask->getByList($taskID) : array();
        $report = $this->objectModel->getByID($reportID);

        $objects = $this->objectModel->getPerCaseResult4Report($tasks, $report->cases, $report->begin, $report->end);

        if(dao::isError()) return dao::getError();

        return $objects;
    }
    /**
     * Get per case runner for testreport.
     *
     * @param  int     $taskID
     * @param  int     $reportID
     * @access public
     * @return string
     */

    public function getPerCaseRunner4ReportTest($taskID, $reportID)
    {
        $tasks   = $taskID ? $this->testtask->getByList($taskID) : array();
        $report = $this->objectModel->getByID($reportID);
        $objects = $this->objectModel->getPerCaseRunner4Report($tasks, $report->cases, $report->begin, $report->end);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * 测试获取测试报告的 bugs。
     * Get bugs for test.
     *
     * @param  array              $buildIdList
     * @param  int                $product
     * @param  int                $taskID
     * @param  string             $type
     * @access public
     * @return array|string|false
     */

    public function getBugs4TestTest(string $buildIdList, int $productID, int $taskID, string $type = 'build'): array|string|false
    {
        $task    = $this->testtask->getByID($taskID);
        $builds  = $this->build->getByList($buildIdList);
        if(empty($builds)) $builds = false;
        $objects = $this->objectModel->getBugs4Test($builds, $productID, $task->begin, $task->end, $type);

        if(dao::isError()) return dao::getError();

        return implode(',', array_keys($objects));
    }
    /**
     * Get stories for test
     *
     * @param  array  $builds
     * @return void
     */
    public function getStories4TestTest($buildIdList)
    {
        $builds  = $this->build->getByList($buildIdList);
        $objects = $this->objectModel->getStories4Test($builds);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * 测试获取测试报告键对。
     * Test get pairs.
     *
     * @param  int          $productID
     * @param  int          $appendID
     * @access public
     * @return array|string
     */
    public function getPairsTest(int $productID = 0, int $appendID = 0): array|string
    {
        $objects = $this->objectModel->getPairs($productID, $appendID);
        if(dao::isError()) return dao::getError();
        return implode(',', array_keys($objects));
    }

    /**
     * 测试判断操作是否可以点击。
     * Test judge an action is clickable or not.
     *
     * @param  object     $report
     * @param  string     $action
     * @access public
     * @return int|array
     */
    public function isClickableTest(object $report, string $action): int|array
    {
        $isClickable = $this->objectModel->isClickable($report, $action);
        if(dao::isError()) return dao::getError();
        return $isClickable ? 1 : 0;
    }
}
