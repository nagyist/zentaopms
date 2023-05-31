<?php
class storyTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('story');
         su('admin');
    }

    /**
     * Test get by id.
     *
     * @param  int    $storyID
     * @param  int    $version
     * @access public
     * @return void
     */
    public function getByIdTest($storyID, $version = 0)
    {
        $story = $this->objectModel->getById($storyID, $version);

        if(dao::isError()) return dao::getError();

        return $story;
    }

    /**
     * Test get by list.
     *
     * @param  int    $storyIdList
     * @param  string $type
     * @access public
     * @return void
     */
    public function getByListTest($storyIdList = 0, $type = 'story')
    {
        $stories = $this->objectModel->getByList($storyIdList, $type);

        if(dao::isError()) return dao::getError();

        return $stories;
    }

    /**
     * Test get test stories.
     *
     * @param  array  $storyIdList
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function getTestStoriesTest($storyIdList, $executionID)
    {
        $objects = $this->objectModel->getTestStories($storyIdList, $executionID);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Test get story specs.
     *
     * @param  array $storyIdList
     * @access public
     * @return void
     */
    public function getStorySpecsTest($storyIdList)
    {
        $objects = $this->objectModel->getStorySpecs($storyIdList);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Test get affected scope.
     *
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function getAffectedScopeTest($storyID)
    {
        global $tester;
        $story = $tester->loadModel('story')->getById($storyID);
        $scope = $this->objectModel->getAffectedScope($story);

        if(dao::isError()) return dao::getError();

        return $scope;
    }

    /**
     * Test get requierements.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function getRequierementsTest($productID)
    {
        $requirements = $this->objectModel->getRequierements($productID);

        if(dao::isError()) return dao::getError();

        return $requirements;
    }

    /**
     * Test create story.
     *
     * @param  int    $executionID
     * @param  int    $bugID
     * @param  string $from
     * @param  string $extra
     * @param  string $params
     * @access public
     * @return void
     */
    public function createTest($executionID = 0, $bugID = 0, $from = '', $extra = '', $params = '')
    {
        $_POST  = $params;
        $result = $this->objectModel->create($executionID, $bugID, $from, $extra);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        global $tester;
        $storyID = $result['id'];
        return $tester->loadModel('story')->getById($storyID);
    }

    /**
     * Test create story from gitlab issue.
     *
     * @param  int    $story
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function createStoryFromGitlabIssueTest($story, $executionID)
    {
        $storyID = $this->objectModel->createStoryFromGitlabIssue($story, $executionID);

        if(dao::isError()) return dao::getError();

        global $tester;
        return $tester->loadModel('story')->getById($storyID);
    }

    /**
     * Test batch create stories.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $type
     * @param  array  $params
     * @access public
     * @return void
     */
    public function batchCreateTest($productID = 0, $branch = 0, $type = 'story', $params = '')
    {
        $_POST   = $params;
        $results = $this->objectModel->batchCreate($productID, $branch, $type);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        foreach($results as $result) $storyIdList[] = $result->storyID;

        global $tester;
        $stories = $tester->loadModel('story')->getByList($storyIdList);
        return $stories;
    }

    /**
     * Test change story.
     *
     * @param  int    $storyID
     * @param  array  $params
     * @access public
     * @return void
     */
    public function changeTest($storyID, $params)
    {
        $_POST = $params;
        $this->objectModel->change($storyID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        global $tester;
        return $tester->loadModel('story')->getById($storyID);
    }

    /**
     * Test update story.
     *
     * @param  int    $storyID
     * @param  array  $params
     * @access public
     * @return void
     */
    public function updateTest($storyID, $params)
    {
        $_POST = $params;
        $this->objectModel->update($storyID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        return $this->objectModel->getById($storyID);
    }

    /**
     * Test update story order of plan.
     *
     * @param  int    $storyID
     * @param  string $planIDList
     * @param  string $oldPlanIDList
     * @access public
     * @return void
     */
    public function updateStoryOrderOfPlanTest($storyID, $planIDList = '', $oldPlanIDList = '')
    {
        $this->objectModel->updateStoryOrderOfPlan($storyID, $planIDList, $oldPlanIDList);

        if(dao::isError()) return dao::getError();

        global $tester;
        return $tester->dao->select('*')->from(TABLE_PLANSTORY)->where('plan')->in($planIDList)->fetchAll();
    }

    /**
     * Test compute estimate.
     *
     * @param  int    $storyID
     * @access public
     * @return array
     */
    public function computeEstimateTest(int $storyID): array
    {
        $oldStory = $this->objectModel->getByID($storyID);
        if(empty($oldStory)) return array();

        $this->objectModel->computeEstimate($storyID);

        if(dao::isError()) return dao::getError();

        $newStory = $this->objectModel->getByID($storyID);
        return array('old' => $oldStory->estimate, 'new' => $newStory->estimate);
    }

    /**
     * Test batch update stories.
     *
     * @access public
     * @return void
     */
    public function batchUpdateTest($params)
    {
        $_POST      = $params;
        $allStories = $this->objectModel->batchUpdate();
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($allStories);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test review story.
     *
     * @param  int    $storyID
     * @param  array  $params
     * @access public
     * @return void
     */
    public function reviewTest($storyID, $params)
    {
        $_POST = $params;
        $changes = $this->objectModel->review($storyID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        return $this->objectModel->getByID($storyID);
    }

    /**
     * Test batch review.
     *
     * @param  int    $storyIdList
     * @param  int    $result
     * @param  int    $reason
     * @access public
     * @return void
     */
    public function batchReviewTest($storyIdList, $result, $reason)
    {
        $actions = $this->objectModel->batchReview($storyIdList, $result, $reason);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($actions);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test story subdivide.
     *
     * @param  int    $storyID
     * @param  array  $stories
     * @access public
     * @return object|array
     */
    public function subdivideTest(int $storyID, array $stories, string $type): object|array
    {
        $this->objectModel->dao->delete()->from(TABLE_RELATION)->exec();
        $this->objectModel->subdivide($storyID, $stories);

        if(dao::isError()) return dao::getError();

        global $tester;
        if($type == 'requirement')
        {
            return $tester->dao->select('*')->from(TABLE_RELATION)->fetchAll();
        }
        else
        {
            return $this->objectModel->getById($storyID);
        }
    }

    /**
     * Test close a story.
     *
     * @param  int    $storyID
     * @param  array  $params
     * @access public
     * @return void
     */
    public function closeTest($storyID, $params)
    {
        $_POST   = $params;
        $changes = $this->objectModel->close($storyID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        return $changes;
    }

    /**
     * Test batch close story.
     *
     * @param  array $params
     * @access public
     * @return void
     */
    public function batchCloseTest($params)
    {
        $_POST   = $params;
        $changes = $this->objectModel->batchClose();
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($changes);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test batch change story module.
     *
     * @param  int    $storyIdList
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function batchChangeModuleTest($storyIdList, $moduleID)
    {
        $changes = $this->objectModel->batchChangeModule($storyIdList, $moduleID);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($changes);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test batch change story plan.
     *
     * @param  array $storyIdList
     * @param  int   $planID
     * @param  int   $oldPlanID
     * @access public
     * @return void
     */
    public function batchChangePlanTest($storyIdList, $planID, $oldPlanID = 0)
    {
        $changes = $this->objectModel->batchChangePlan($storyIdList, $planID, $oldPlanID);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($changes);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test batch change story branch.
     *
     * @param  array  $storyIdList
     * @param  int    $branchID
     * @param  string $confirm
     * @param  array  $plans
     * @access public
     * @return void
     */
    public function batchChangeBranchTest($storyIdList, $branchID, $confirm = '', $plans = array())
    {
        $changes = $this->objectModel->batchChangeBranch($storyIdList, $branchID, $confirm, $plans);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($changes);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test batch change stage.
     *
     * @param  array  $storyIdList
     * @param  string $stage
     * @access public
     * @return void
     */
    public function batchChangeStageTest($storyIdList, $stage)
    {
        $changes = $this->objectModel->batchChangeStage($storyIdList, $stage);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($changes);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test story batch to task.
     *
     * @param  int    $executionID
     * @param  int    $projectID
     * @param  array  $params
     * @access public
     * @return void
     */
    public function batchToTaskTest($executionID, $projectID = 0, $params = '')
    {
        $_POST      = $params;
        $taskIdList = $this->objectModel->batchToTask($executionID, $projectID);
        unset($params);

        if(dao::isError()) return dao::getError();

        global $tester;
        return $tester->loadModel('task')->getByIdList($taskIdList);
    }

    /**
     * Test assign a story.
     *
     * @param  int    $storyID
     * @param  string $assignedTo
     * @access public
     * @return void
     */
    public function assignTest($storyID, $assignedTo)
    {
        $_POST['assignedTo'] = $assignedTo;
        $this->objectModel->assign($storyID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        return $this->objectModel->getById($storyID);
    }

    /**
     * Test batch assign story.
     *
     * @param  array  $params
     * @access public
     * @return void
     */
    public function batchAssignToTest($params)
    {
        $_POST   = $params;
        $changes = $this->objectModel->batchAssignTo();
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $storyIdList = array_keys($changes);
        return $this->objectModel->getByList($storyIdList);
    }

    /**
     * Test get stories by assignedBy.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return string
     */
    public function getByAssignedByTest($productID, $branch)
    {
        global $tester;
        $stories = $this->objectModel->getByAssignedBy($productID, $branch, $modules = array(), $account = $tester->app->user->account, $type = 'story', $orderBy = '', $pager = null);

        $title = '';
        foreach($stories as $story)
        {
            $title .= ',' . $story->title;
        }
        $title = trim($title, ',');
        $title = str_replace("'", '', $title);

        if(dao::isError()) return dao::getError();

        return $title;
    }

    /**
     * 测试 fetchProjectStories 方法。
     * Test fetchProjectStories method.
     *
     * @param  int         $productID
     * @param  int         $projectID
     * @param  string      $type
     * @param  string      $branch
     * @param  object|null $pager
     * @access public
     * @return array
     */
    public function fetchProjectStoriesTest(int $productID, int $projectID, string $type = 'all', string $branch = '', object|null $pager = null): array
    {
        $unclosedStatus = $this->objectModel->lang->story->statusList;
        unset($unclosedStatus['closed']);

        $storyIdList = array('1,2,3,4,5,6,7');
        $storyDAO    = $this->objectModel->dao->select("DISTINCT t2.*")->from(TABLE_PROJECTSTORY)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t2.product = t3.id')
            ->where('t1.project')->eq($projectID)
            ->andWhere('t2.type')->eq('story')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0);

        return $this->objectModel->fetchProjectStories($storyDAO, $productID, $type, $branch, $storyIdList, 't2.id_desc', $pager);
    }

    /**
     * 测试 fetchExecutionStories 方法。
     * Test fetchExecutionStories method.
     *
     * @param  int         $executionID
     * @param  int         $product
     * @param  object|null $pager
     * @access public
     * @return array
     */
    public function fetchExecutionStoriesTest(int $executionID, int $productID, object|null $pager = null): array
    {
        $unclosedStatus = $this->objectModel->lang->story->statusList;
        unset($unclosedStatus['closed']);

        $storyDAO = $this->objectModel->dao->select("DISTINCT t2.*")->from(TABLE_PROJECTSTORY)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t2.product = t3.id')
            ->where('t1.project')->eq($executionID)
            ->andWhere('t2.type')->eq('story')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0);

        return $this->objectModel->fetchExecutionStories($storyDAO, $productID, 't2.id_desc', $pager);
    }

    /**
     * 测试 getExecutionStoriesBySearch。
     * Test getExecutionStoriesBySearch method.
     *
     * @param  int         $executionID
     * @param  int         $queryID
     * @param  int         $productID
     * @param  array       $excludeStories
     * @param  object|null $pager
     * @access public
     * @return int
     */
    public function getExecutionStoriesBySearchTest(int $executionID, int $queryID, int $productID, array $excludeStories = array(), object|null $pager = null): int
    {
        $stories = $this->objectModel->getExecutionStoriesBySearch($executionID, $queryID, $productID, 't2.id_desc', 'story', $excludeStories, $pager);
        return count($stories);
    }

    /**
     * 测试 updateTwins 方法。
     * Test updateTwins method.
     *
     * @param  array  $storyIdList
     * @access public
     * @return array
     */
    public function updateTwinsTest(array $storyIdList): array
    {
        $this->objectModel->updateTwins($storyIdList);

        if(empty($storyIdList)) return array();
        if($storyIdList)
        {
            $twins = $this->objectModel->dao->select('id,twins')->from(TABLE_STORY)->where('id')->in($storyIdList)->fetchPairs('id', 'twins');
            return array_map(function($item){return str_replace(',', ':', $item);}, $twins);
        }
    }

    /**
     * 测试 finishTodoWhenToStory 方法。
     * Test finishTodoWhenToStory.
     *
     * @param  int    $todoID
     * @param  int    $storyID
     * @access public
     * @return string
     */
    public function finishTodoWhenToStoryTest(int $todoID, int $storyID): string
    {
        $this->objectModel->finishTodoWhenToStory($todoID, $storyID);

        if(empty($todoID) or empty($storyID)) return '';
        return $this->objectModel->dao->select('id,status')->from(TABLE_TODO)->where('id')->eq($todoID)->fetch('status');
    }

    /**
     * 测试 closeBugWhenToStory 方法。
     * Test closeBugWhenToStory.
     *
     * @param  int    $bugID
     * @param  int    $storyID
     * @access public
     * @return array
     */
    public function closeBugWhenToStoryTest(int $bugID, int $storyID): array
    {
        $this->objectModel->closeBugWhenToStory($bugID, $storyID);

        if(empty($bugID) or empty($storyID)) return array();
        $bug = (array)$this->objectModel->dao->select('*')->from(TABLE_BUG)->where('id')->eq($bugID)->fetch();
        $bug['files'] = $this->objectModel->dao->select('*')->from(TABLE_FILE)->where('objectType')->eq('story')->andWhere('objectID')->eq($storyID)->fetchAll('id');
        return $bug;
    }

    /**
     * 测试 linkToExecutionForCreate 方法。
     * Test linkToExecutionForCreate method.
     *
     * @param  int    $executionID
     * @param  int    $storyID
     * @param  string $extra
     * @access public
     * @return array
     */
    public function linkToExecutionForCreateTest(int $executionID, int $storyID, string $extra = ''): array
    {
        $this->objectModel->dao->delete()->from(TABLE_PROJECTSTORY)->exec();
        $this->objectModel->dao->delete()->from(TABLE_ACTION)->exec();
        $story = $this->objectModel->dao->select('*')->from(TABLE_STORY)->where('id')->eq($storyID)->fetch();
        if(empty($story)) $story = new stdclass();

        $this->objectModel->linkToExecutionForCreate($executionID, $storyID, $story, $extra);
        return array_filter((array)$this->objectModel->dao->select('*')->from(TABLE_ACTION)->orderBy('id_desc')->limit(1)->fetch());
    }

    /**
     * 测试 doCreateURRelations 方法。
     * Test doCreateURRelations method.
     *
     * @param  int    $storyID
     * @param  array  $URList
     * @access public
     * @return array
     */
    public function doCreateURRelationsTest(int $storyID, array $URList): array
    {
        $this->objectModel->dao->delete()->from(TABLE_RELATION)->exec();
        $this->objectModel->doCreateURRelations($storyID, $URList);

        if(dao::isError()) return dao::getError();
        return $this->objectModel->dao->select('*')->from(TABLE_RELATION)->fetchAll();
    }

    /**
     * 测试 doCreateReviewer 方法。
     * 测u8 doCreateReviewer method.
     *
     * @param  int    $storyID
     * @param  array  $reviewer
     * @access public
     * @return array
     */
    public function doCreateReviewerTest(int $storyID, array $reviewer): array
    {
        $this->objectModel->dao->delete()->from(TABLE_STORYREVIEW)->exec();
        $this->objectModel->doCreateReviewer($storyID, $reviewer);

        if(dao::isError()) return dao::getError();
        return $this->objectModel->dao->select('*')->from(TABLE_STORYREVIEW)->fetchAll();
    }

    /**
     * 测试 doCreateSpec 方法。
     * Test doCreateSpec method.
     *
     * @param  int    $storyID
     * @param  object $story
     * @param  array  $files
     * @access public
     * @return array
     */
    public function doCreateSpecTest(int $storyID, object $story, array $files = array()): array
    {
        $this->objectModel->dao->delete()->from(TABLE_STORYSPEC)->exec();
        $this->objectModel->doCreateSpec($storyID, $story, $files);

        if(dao::isError()) return dao::getError();
        return $this->objectModel->dao->select('*')->from(TABLE_STORYSPEC)->fetchAll();
    }
}
