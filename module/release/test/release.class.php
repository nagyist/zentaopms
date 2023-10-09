<?php
class releaseTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('release');
    }

    /**
     * function getByIDTest by release
     *
     * @param  string $releaseID
     * @param  bool   $setImgSize
     * @access public
     * @return array
     */
    public function getByIDTest($releaseID, $setImgSize = false)
    {
        $objects = $this->objectModel->getByID($releaseID, $setImgSize);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * function getListTest by release
     *
     * @param  string $productID
     * @param  string $branch
     * @param  string $type
     * @access public
     * @return array
     */

    public function getListTest($productID, $branch = 'all', $type = 'all', $orderBy = 't1.date_desc', $pager = null)
    {
        $objects = $this->objectModel->getList($productID, $branch, $type, $orderBy, $pager);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * function getLastTest by release
     *
     * @param  string $productID
     * @param  string $branch
     * @access public
     * @return array
     */
    public function getLastTest($productID, $branch = 0)
    {
        $objects = $this->objectModel->getLast($productID, $branch);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * 创建一个发布。
     * Create a release.
     *
     * @param  array  $data
     * @param  bool   $isSync
     * @access public
     * @return array
     */

    public function createTest(array $data, bool $isSync)
    {
        $date = date('Y-m-d');
        $createFields = array('name' => '','marker' => '1', 'build' => '', 'date' => $date, 'desc' => '', 'mailto' => '', 'stories' => '', 'bugs' => '', 'createdBy' => 'admin', 'createdDate' => helper::now());

        $release = new stdclass();
        foreach($createFields as $field => $defaultValue) $release->$field = $defaultValue;
        foreach($data as $key => $value) $release->$key = $value;

        $objectID = $this->objectModel->create($release, $isSync);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getByID($objectID);
        return $object;
    }
    /**
     * Update a release.
     *
     * @param  int    $releaseID
     * @access public
     * @return array
     */

    public function updateTest($releaseID, $param = array())
    {
        $date   = date('Y-m-d');
        $labels = array();
        $files  = array();
        $mailto = array();

        $createFields = array('name' => '','marker' => '1', 'build' => '', 'date' => $date, 'desc' => '', 'mailto' => $mailto , 'labels' => $labels, 'files' => $files);

        foreach($createFields as $field => $defaultValue) $_POST[$field] = $defaultValue;
        foreach($param as $key => $value) $_POST[$key] = $value;
        $this->objectModel->update($releaseID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);
        return $objects;
    }

    /**
     * Get notify persons.
     *
     * @param  string $notfiyList
     * @param  int    $productID
     * @param  int    $buildID
     * @param  int    $releaseID
     * @access public
     * @return array
     */

    public function getNotifyPersonsTest($releaseID)
    {
        $release = $this->objectModel->getById($releaseID);
        $objects = $this->objectModel->getNotifyPersons($release);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * Link stories
     *
     * @param  int    $releaseID
     * @access public
     * @return array
     */

    public function linkStoryTest($releaseID, $stories)
    {
        $_POST['stories'] = $stories;
        $this->objectModel->linkStory($releaseID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);
        return $objects;
    }

    /**
     * Unlink story
     *
     * @param  int    $releaseID
     * @param  int    $storyID
     * @access public
     * @return array
     */
    public function unlinkStoryTest($releaseID, $storyID)
    {
        $_POST['stories'] = $storyID;
        $this->objectModel->linkStory($releaseID);
        unset($_POST);

        $this->objectModel->unlinkStory($releaseID, $storyID[0]);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);
        return $objects;
    }

    /**
     * Batch unlink story.
     *
     * @param  int    $releaseID
     * @access public
     * @return array
     */

    public function batchUnlinkStoryTest($releaseID, $stories)
    {
        $_POST['stories'] = $stories;
        $this->objectModel->linkStory($releaseID);
        unset($_POST);

        $_POST['storyIdList'] = $stories;
        $this->objectModel->batchUnlinkStory($releaseID);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);

        return $objects;
    }

    /**
     * Link bugs.
     *
     * @param  int    $releaseID
     * @param  string $type
     * @param  string $bugs
     * @access public
     * @return array
     */

    public function linkBugTest($releaseID, $type = 'bug', $bugs = '')
    {
        $_POST['bugs'] = $bugs;
        $this->objectModel->linkBug($releaseID, $type);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);
        return $objects;
    }

    /**
     * Unlink bug.
     *
     * @param  int    $releaseID
     * @param  int    $bugID
     * @param  string $type
     * @access public
     * @return void
     */

    public function unlinkBugTest($releaseID, $bugID, $type = 'bug')
    {
        $_POST['bugs'] = $bugID;
        $this->objectModel->linkBug($releaseID);
        unset($_POST);

        $this->objectModel->unlinkBug($releaseID, $bugID[0], $type);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);
        return $objects;
    }

    /**
     * Batch unlink bug.
     *
     * @param  int    $releaseID
     * @param  string $type
     * @param  string $bugs
     * @access public
     * @return array
     */
    public function batchUnlinkBugTest($releaseID, $type = 'bug', $bugs = '')
    {
        $_POST['bugs'] = $bugs;
        $this->objectModel->linkBug($releaseID);
        unset($_POST);

        $_POST['unlinkBugs'] = $bugs;
        $this->objectModel->batchUnlinkBug($releaseID, $type);
        unset($_POST);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);
        return $objects;
    }

    /**
     * Change status.
     *
     * @param  int    $releaseID
     * @param  string $status
     * @access public
     * @return bool
     */

    public function changeStatusTest($releaseID, $status)
    {
        $this->objectModel->changeStatus($releaseID, $status);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($releaseID);
        return $objects;
    }

    /**
     * Get toList and ccList.
     *
     * @param  object    $release
     * @access public
     * @return bool|array
     */
    public function getToAndCcListTest($releaseID)
    {
        $release = $this->objectModel->getByID($releaseID);

        $objects = $this->objectModel->getToAndCcList($release);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * 处理待创建的发布字段。
     * Process release fields for create.
     *
     * @param  int         $releaseID
     * @param  bool        $isSync
     * @access public
     * @return object|false
     */
    public function processReleaseForCreateTest(int $releaseID, bool $isSync): object|false
    {
        $release = $this->objectModel->getByID($releaseID);
        if(!$release) return false;

        return $this->objectModel->processReleaseForCreate($release, $isSync);
    }
}
