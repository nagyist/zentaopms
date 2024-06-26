<?php
class treeTest
{
    public function __construct()
    {
        global $config, $tester;

        $config->webRoot     = '';
        $config->requestType = 'PATH_INFO';
        $this->objectModel   = $tester->loadModel('tree');
    }

    /**
     * Test get module by id.
     *
     * @param  int    $moduleID
     * @access public
     * @return object
     */
    public function getByIDTest($moduleID)
    {
        $object = $this->objectModel->getByID($moduleID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * get all module pairs with path.
     *
     * @param  string $type
     * @access public
     * @return int
     */
    public function getAllModulePairsTest($type = 'task')
    {
        $objects = $this->objectModel->getAllModulePairs($type);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test build the sql query.
     *
     * @param  int    $rootID
     * @param  string $type
     * @param  int    $startModule
     * @param  string $branch
     * @access public
     * @return string
     */
    public function buildMenuQueryTest($rootID, $type, $startModule = 0, $branch = 'all')
    {
        $string = $this->objectModel->buildMenuQuery($rootID, $type, $startModule, $branch);

        if(dao::isError()) return dao::getError();

        return $string;
    }

    /**
     * Test create an option menu in html.
     *
     * @param  int          $rootID
     * @param  string       $type
     * @param  int          $startModule
     * @param  string|array $branch
     * @access public
     * @return array
     */
    public function getOptionMenuTest(int $rootID, string $type = 'story', int $startModule = 0, string|array $branch = 'all'): array
    {
        $objects = $this->objectModel->getOptionMenu($rootID, $type, $startModule, $branch);

        if(dao::isError()) return dao::getError();

        return $objects;
    }

    /**
     * getModulePairsTest
     *
     * @param  int    $rootID
     * @param  string $viewType
     * @param  string $showModule
     * @param  string $extra
     * @access public
     * @return int
     */
    public function getModulePairsTest($rootID, $viewType = 'story', $showModule = 'end', $extra = '')
    {
        $objects = $this->objectModel->getModulePairs($rootID, $viewType, $showModule, $extra);

        if(dao::isError()) return dao::getError();

        return count($objects);
    }

    /**
     * Test create an option menu of task in html.
     *
     * @param  int    $rootID
     * @param  int    $startModule
     * @param  string $extra
     * @access public
     * @return string
     */
    public function getTaskOptionMenuTest($rootID, $startModule = 0, $extra = '')
    {
        $objects = $this->objectModel->getTaskOptionMenu($rootID, $startModule, $extra);

        if(dao::isError()) return dao::getError();

        $names = '';
        foreach($objects as $object) $names .= ',' . $object;
        return $names;
    }

    /**
     * Test build tree array.
     *
     * @param  array  & $&treeMenu
     * @param  array  $modules
     * @param  int    $moduleID
     * @param  string $moduleName
     * @access public
     * @return array
     */
    public function buildTreeArrayTest(array & $treeMenu, array $modules, object $module, string $moduleName = '/'): array
    {
        $this->objectModel->buildTreeArray($treeMenu, $modules, $module, $moduleName);

        if(dao::isError()) return dao::getError();

        return $treeMenu;
    }

    /**
     * Test get full task tree.
     *
     * @param  int    $rootID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function getTaskStructureTest($rootID, $productID = 0)
    {
        $objects = $this->objectModel->getTaskStructure($rootID, $productID = 0);

        if(dao::isError()) return dao::getError();

        $child = '';
        foreach($objects as $object)
        {
            if(is_array($object)) $child .= isset($object['children']) ? $object['name'] . ':' . count($object['children']) . ';' : $object['name'] . ':0';
            if(is_object($object)) $child .= isset($object->children) ? "$object->name:" . count($object->children) . ';' : "$object->name:0";
        }
        return $child;
    }

    /**
     * Test get tree structure.
     *
     * @param  int    $rootID
     * @param  string $type
     * @access public
     * @return string
     */
    public function getTreeStructureTest($rootID, $type)
    {
        $objects = $this->objectModel->getTreeStructure($rootID, $type);

        if(dao::isError()) return dao::getError();

        $child = '';
        foreach($objects as $object) $child .= isset($object->children) ? "$object->id:" . count($object->children) . ';' : "$object->id:0;";
        return $child;
    }

    /**
     * Test get tree menu.
     *
     * @param  int    $rootID
     * @param  string $type
     * @access public
     * @return string
     */
    public function getTreeMenuTest($rootID, $type)
    {
        $objects = $this->objectModel->getTreeMenu($rootID, $type, 0, array('treeModel', 'createStoryLink'));

        if(dao::isError()) return dao::getError();

        $modules = array();
        foreach($objects as $object) $modules[] = $object->id;

        return implode('|', $modules);
    }

    /**
     * Test get task tree menu.
     *
     * @param  int    $rootID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function getTaskTreeMenuTest($rootID, $productID = 0)
    {
        $objects = $this->objectModel->getTaskTreeMenu($rootID, $productID, 0, array('treeModel', 'createTaskLink'));

        if(dao::isError()) return dao::getError();

        $modules = array();
        foreach($objects as $object) $modules[] = $object->id;

        return implode('|', $modules);
    }

    /**
     * Test get bug tree menu.
     *
     * @param  int    $rootID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function getBugTreeMenuTest($rootID, $productID = 0)
    {
        global $app;
        $app->tab = 'project';

        $objects = $this->objectModel->getBugTreeMenu($rootID, $productID, 0, array('treeModel', 'createBugLink'));

        if(dao::isError()) return dao::getError();

        $modules = array();
        foreach($objects as $object) $modules[] = $object->name;

        return implode('|', $modules);
    }

    /**
     * Test get case tree menu.
     *
     * @param  int    $rootID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function getCaseTreeMenuTest($rootID, $productID = 0)
    {
        $objects = $this->objectModel->getCaseTreeMenu($rootID, $productID, 0, array('treeModel', 'createCaseLink'));

        if(dao::isError()) return dao::getError();

        $modules = array();
        foreach($objects as $object) $modules[] = $object->name;

        return implode('|', $modules);
    }

    /**
     * Test get project story tree menu.
     *
     * @param  int    $rootID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function getProjectStoryTreeMenuTest($rootID)
    {
        $objects = $this->objectModel->getProjectStoryTreeMenu($rootID, 0, array('treeModel', 'createStoryLink'));

        if(dao::isError()) return dao::getError();

        $modules = array();
        foreach($objects as $object) $modules[] = $object->id;

        return implode('|', $modules);
    }

    /**
     * Test get host tree menu.
     *
     * @access public
     * @return string
     */
    public function getHostTreeMenuTest()
    {
        $objects = $this->objectModel->getHostTreeMenu();

        if(dao::isError()) return dao::getError();

        $modules = array();
        foreach($objects as $object) $modules[] = $object->id;

        return implode('|', $modules);
    }

    /**
     * Test get execution modules.
     *
     * @param  int    $executionID
     * @param  bool   $parent
     * @param  string $linkObject
     * @access public
     * @return string
     */
    public function getTaskTreeModulesTest(int $executionID, bool $parent = false, string $linkObject = 'story', array $extra = array()): string
    {
        $this->objectModel->app->tab = 'execution';
        $objects = $this->objectModel->getTaskTreeModules($executionID, $parent, $linkObject, $extra);

        if(dao::isError()) return dao::getError();
        return implode(',', $objects);
    }

    /**
     * Test create story link.
     *
     * @param  int    $moduleID
     * @param  string $parent
     * @param  array  $extra
     * @access public
     * @return string
     */
    public function createStoryLinkTest($moduleID, $parent = '0', $extra = array())
    {
        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createStoryLink($type, $module, $parent, $extra);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Test create task link.
     *
     * @param  int    $moduleID
     * @access public
     * @return string
     */
    public function createTaskLinkTest($moduleID)
    {
        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createTaskLink($type, $module);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Test create requirment link.
     *
     * @param  int    $moduleID
     * @param  string $parent
     * @param  array  $extra
     * @access public
     * @return string
     */
    public function createRequirementLinkTest($moduleID, $parent = '0', $extra = array())
    {
        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createRequirementLink($type, $module, $parent, $extra);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Test reate link of a bug.
     *
     * @param  int    $moduleID
     * @param  string $tab
     * @param  arraty $extra
     * @access public
     * @return string
     */
    public function createBugLinkTest($moduleID, $tab = 'qa', $extra = array())
    {
        global $app;
        $app->tab = $tab;

        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createBugLink($type, $module, '0', $extra);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Create case link.
     *
     * @param  int    $moduleID
     * @param  array  $extra
     * @access public
     * @return string
     */
    public function createCaseLinkTest($moduleID, $tab = 'qa', $extra = array('branchID' => 0))
    {
        global $app;
        $app->tab = $tab;

        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createCaseLink($type, $module, '0', $extra);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Test create test task link.
     *
     * @param  int    $moduleID
     * @param  int    $extra
     * @access public
     * @return string
     */
    public function createTestTaskLinkTest($moduleID, $extra)
    {
        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createTestTaskLink($type, $module, '0', $extra);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Test create case lib link.
     *
     * @param  int    $moduleID
     * @access public
     * @return string
     */
    public function createCaseLibLinkTest($moduleID)
    {
        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createCaseLibLink($type, $module);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Test create feedback link.
     *
     * @param  int    $moduleID
     * @access public
     * @return string
     */
    public function createFeedbackLinkTest($moduleID)
    {
        global $app;
        $app->methodName = 'browse';

        $type   = '';
        $module = $this->objectModel->getByID($moduleID);

        $link = $this->objectModel->createFeedbackLink($type, $module);

        if(dao::isError()) return dao::getError();

        return $link;
    }

    /**
     * Test get sons of a module.
     *
     * @param  int    $rootID
     * @param  int    $moduleID
     * @param  string $type
     * @param  int    $branch
     * @access public
     * @return string
     */
    public function getSonsTest($rootID, $moduleID, $type = 'root', $branch = 0)
    {
        $objects = $this->objectModel->getSons($rootID, $moduleID, $type, $branch);

        if(dao::isError()) return dao::getError();

        $ids = '';
        foreach($objects as $object) $ids .= ',' . $object->id;
        return $ids;
    }

    /**
     * Test get sons of a task module.
     *
     * @param  int    $rootID
     * @param  int    $productID
     * @param  int    $moduleID
     * @access public
     * @return string
     */
    public function getTaskSonsTest($rootID, $productID, $moduleID)
    {
        $objects = $this->objectModel->getTaskSons($rootID, $productID, $moduleID);

        if(dao::isError()) return dao::getError();

        $ids = '';
        foreach($objects as $object) $ids .= ',' . $object->id;
        return $ids;
    }

    /**
     * Test get id list of a module's childs.
     *
     * @param  int    $moduleID
     * @access public
     * @return string
     */
    public function getAllChildIdTest($moduleID)
    {
        $objects = $this->objectModel->getAllChildId($moduleID);

        if(dao::isError()) return dao::getError();

        $ids = '';
        foreach($objects as $objectID) $ids .= ',' . $objectID;
        return $ids;
    }

    /**
     * Test get parents of a module.
     *
     * @param  int    $moduleID
     * @param  bool   $queryAll
     * @access public
     * @return string
     */
    public function getParentsTest($moduleID, $queryAll = false)
    {
        $objects = $this->objectModel->getParents($moduleID, $queryAll);

        if(dao::isError()) return dao::getError();

        $ids = '';
        foreach($objects as $object) $ids .= ',' . $object->id;
        return $ids;
    }

    /**
     * Test get product by moduleID.
     *
     * @param  int $moduleID
     * @access public
     * @return object
     */
    public function getProductTest($moduleID)
    {
        $object = $this->objectModel->getProduct($moduleID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test get the module that its type == 'story'.
     *
     * @param  int    $moduleID
     * @access public
     * @return int
     */
    public function getStoryModuleTest($moduleID)
    {
        $object = $this->objectModel->getStoryModule($moduleID);

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * Test update modules' order.
     *
     * @param  array  $orders
     * @access public
     * @return string
     */
    public function updateOrderTest($orders)
    {
        $objects = $this->objectModel->updateOrder($orders);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_MODULE)->where('id')->in(array_keys($orders))->orderBy('`order` asc')->fetchAll();
        $ids = '';
        foreach($objects as $object) $ids .= ',' . $object->id;
        return $ids;
    }

    /**
     * Test manage childs of a module.
     *
     * @param  int    $rootID
     * @param  string $type
     * @param  array  $param
     * @access public
     * @return string
     */
    public function manageChildTest($rootID, $type, $param)
    {
        $_POST['allProduct']    = 100;
        $_POST['productModule'] = 0;

        foreach($param as $key => $value) $_POST[$key] = $value;

        $this->objectModel->manageChild($rootID, $type);

        unset($_POST);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('*')->from(TABLE_MODULE)->where('parent')->eq($param['parentModuleID'])->andWhere('deleted')->eq(0)->fetchAll();

        $names = '';
        foreach($objects as $object) $names .= ',' . $object->name;
        return $names;
    }

    /**
     * Test update a module.
     *
     * @param  int    $moduleID
     * @param  array  $param
     * @access public
     * @return object
     */
    public function updateTest($moduleID, $param)
    {
        $fields = array('root', 'branch', 'parent', 'name', 'short');
        $module = $this->objectModel->getByID($moduleID);
        foreach($fields as $field)
        {
            if(isset($param[$field]))
            {
                $_POST[$field] = $param[$field];
            }
            else
            {
                $_POST[$field] = $module->$field;
            }
        }

        $this->objectModel->update($moduleID);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getByID($moduleID);
        return $object;
    }

    /**
     * Test change root.
     *
     * @param  int    $moduleID
     * @param  int    $oldRoot
     * @param  int    $newRoot
     * @param  string $type
     * @access public
     * @return int
     */
    public function changeRootTest($moduleID, $oldRoot, $newRoot, $type)
    {
        $this->objectModel->changeRoot($moduleID, $oldRoot, $newRoot, $type);

        if(dao::isError()) return dao::getError();

        global $tester;
        $table = array('case' => TABLE_CASE, 'story' => TABLE_STORY, 'bug' => TABLE_BUG);
        $objects = $tester->dao->select('*')->from($table[$type])->where('product')->eq($newRoot)->andWhere('deleted')->eq(0)->fetchAll();
        return count($objects);
    }

    /**
     * Test remove a module.
     *
     * @param  int    $moduleID
     * @access public
     * @return object
     */
    public function removeTest($moduleID, $null = null)
    {
        $this->objectModel->remove($moduleID, $null);

        if(dao::isError()) return dao::getError();

        $object = $this->objectModel->getByID($moduleID);
        return $object;
    }

    /**
     * Test fix the path, grade fields according to the id and parent fields.
     *
     * @param  int    $root
     * @param  string $type
     * @access public
     * @return object
     */
    public function fixModulePathTest($root, $type)
    {
        $this->objectModel->fixModulePath($root, $type);

        if(dao::isError()) return dao::getError();

        global $tester;
        $objects = $tester->dao->select('id,path')->from(TABLE_MODULE)->where('root')->eq($root)->andWhere('type')->eq($type)->andWhere('deleted')->eq(0)->fetchPairs('id');
        foreach($objects as $id => $object) $objects[$id] = str_replace(',', '+', trim($object, ','));
        return $objects;
    }

    /**
     * Test check unique module name.
     *
     * @param  object $module
     * @param  array  $modules
     * @param  array  $branches
     * @access public
     * @return string
     */
    public function checkUniqueTest($module, $modules = array(), $branches = array())
    {
        global $tester;

        $repeatName = $this->objectModel->checkUnique($module, $modules, $branches);

        if(dao::isError()) return dao::getError();

        return $repeatName;
    }

    /**
     * Test check merge module version.
     *
     * @param  int    $rootID
     * @param  string $viewType
     * @access public
     * @return int
     */
    public function isMergeModuleTest($rootID, $viewType)
    {
        $object = $this->objectModel->isMergeModule($rootID, $viewType);

        if(dao::isError()) return dao::getError();

        return $object ? 1 : 2;
    }

    /**
     * Test get full trees.
     *
     * @param  int    $rootID
     * @param  string $viewType
     * @param  string $branchID
     * @param  int    $currentModuleID
     * @access public
     * @return string
     */
    public function getProductStructureTest($rootID, $viewType, $branchID = 'all', $currentModuleID = 0)
    {
        $objects = $this->objectModel->getProductStructure($rootID, $viewType, $branchID, $currentModuleID);

        if(dao::isError()) return dao::getError();

        $child = '';
        foreach($objects as $object) $child .= isset($object->children) ? "$object->id:" . count($object->children) . ';' : "$object->id:0;";
        return $child;
    }

    /**
     * Test get full task tree.
     *
     * @param  int    $root
     * @param  string $viewType
     * @param  array $keepModules
     * @access public
     * @return string
     */
    public function getDataStructureTest($root, $viewType, $keepModules = array())
    {
        global $tester;

        $stmt = $tester->dbh->query($this->objectModel->buildMenuQuery($root, $viewType));

        $objects = $this->objectModel->getDataStructure($stmt, $viewType, $root, $keepModules);

        if(dao::isError()) return dao::getError();

        $child = '';
        foreach($objects as $object) $child .= isset($object->children) ? "$object->id:" . count($object->children) . ';' : "$object->id:0;";
        return $child;
    }

    /**
     * Test get  all doc structure.
     *
     * @access public
     * @return string
     */
    public function getDocStructureTest()
    {
        $objects = $this->objectModel->getDocStructure();

        if(dao::isError()) return dao::getError();

        $child = '';
        foreach($objects as $object) $child .= isset($object[0]->children) ? $object[0]->id . ':' . count($object[0]->children) . ';' : "$object[0]->id:0;";
        return $child;
    }

    /**
     * Create module.
     *
     * @param  array  $postData
     * @access public
     * @return object|bool
     */
    public function createModuleTest($postData)
    {
        global $tester;

        unset($_POST);
        foreach($postData as $key => $value) $_POST[$key] = $value;
        $object = $this->objectModel->createModule();

        if(dao::isError())
        {
            $error = dao::getError();
            return $error[0];
        }
        return $object;
    }
}
