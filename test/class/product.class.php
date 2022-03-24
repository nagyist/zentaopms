<?php
class productTest
{

    /**
     * __construct
     *
     * @param  mixed  $user
     * @access public
     * @return void
     */
    public function __construct($user)
    {
        global $tester;
        su($user);
        $this->objectModel = $tester->loadModel('product');
         $tester->app->loadClass('dao');
    }

    /**
     * Test create a product.
     *
     * @param  array  $param
     * @access public
     * @return object
     */
    public function createObject($param = array())
    {
        global $createFields;
        $whitelist = array();
        $createFields = array('program' => 1, 'line' => 0, 'lineName' => '', 'newLine' => 0, 'name' => '', 'code' => '', 'PO' => 'admin', 'QD' => '', 'RD' => '',
            'reviewer' => '', 'type' => 'normal', 'status' => 'normal', 'desc' => '', 'acl' => 'open', 'uid' => '');
        foreach($createFields as $field => $defaultValue) $_POST[$field] = $defaultValue;
        foreach($param as $key => $value) $_POST[$key] = $value;

        $objectID = $this->objectModel->create();
        unset($_POST);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            $object = $this->objectModel->getByID($objectID);
            return $object;
        }
    }

    /**
     * Test get the latest project of the product.
     *
     * @param  mixed  $productID
     * @access public
     * @return object
     */
    public function testGetLatestProject($productID)
    {
        $project = $this->objectModel->getLatestProject($productID);
        if($project == false) return '没有数据';
        return $project;
    }

    /**
     * Test get all products.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getAllProducts($programID)
    {
        return $this->objectModel->getList($programID);
    }

    /**
     * Test get all products count.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getAllProductsCount($programID)
    {
        return count($this->getAllProducts($programID));
    }

    /**
     * Test get noclosed products.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getNoclosedProducts($programID)
    {
        return $this->objectModel->getList($programID, 'noclosed');
    }

    /**
     * Test get noclosed products count.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getNoclosedProductsCount($programID)
    {
        return count($this->getNoclosedProducts($programID));
    }

    /**
     * Test get closed products.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getClosedProducts($programID)
    {
        return $this->objectModel->getList($programID, 'closed');
    }

    /**
     * Test get closed products count.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getClosedProductsCount($programID)
    {
        return count($this->getClosedProducts($programID));
    }

    /**
     * Test get involved products.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getInvolvedProducts($programID)
    {
        return $this->objectModel->getList($programID, 'involved');
    }

    /**
     * Test get involved products count.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getInvolvedProductsCount($programID)
    {
        return count($this->getInvolvedProducts($programID));
    }

    /**
     * Test get products by line.
     *
     * @param  int    $programID
     * @param  int    $line
     * @access public
     * @return array
     */
    public function getProductsByLine($programID, $line = 0)
    {
        return $this->objectModel->getList($programID, 'all', 0, $line);
    }

    /**
     * Test get products count by line.
     *
     * @param  int    $programID
     * @param  int    $line
     * @access public
     * @return int
     */
    public function countProductsByLine($programID, $line = 0)
    {
        return count($this->getProductsByLine($programID, $line));
    }

    /**
     * Test get product list.
     *
     * @param  int    $programID
     * @param  string $status
     * @param  int    $line
     * @access public
     * @return array
     */
    public function getProductList($programID, $status = 'all', $line = 0)
    {
        return $this->objectModel->getList($programID, $status, 0, $line);
    }

    /**
     * Test get product count.
     *
     * @param  int    $programID
     * @param  string $status
     * @param  int    $line
     * @access public
     * @return int
     */
    public function getProductCount($programID, $status = 'all', $line = 0)
    {
        return count($this->getProductList($programID, $status, $line));
    }

    /**
     * Test get product pairs.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getProductPairs($programID)
    {
        $pairs = $this->objectModel->getPairs('', $programID);
        if($pairs == array()) return '没有数据';
        return $pairs;
    }

    /**
     * Test get product pairs count.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getProductPairsCount($programID)
    {
        $pairsCount = count($this->getProductPairs($programID));
        if($pairsCount == '没有数据') return '0';
        return $pairsCount;
    }

    /**
     * Test get all product pairs.
     *
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function getAllPairs($programID)
    {
        $pairs = $this->objectModel->getPairs('all', $programID);
        if($pairs == array()) return '没有数据';
        return $pairs;
    }

    /**
     * Test get all product count.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getAllPairsCount($programID)
    {
        $pairsCount = count($this->getAllPairs($programID));
        if($pairsCount == '没有数据') return '0';
        return $pairsCount;
    }

    /**
     * Test get noclosed product pairs.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getNoclosedPairs($programID)
    {
        $pairs = $this->objectModel->getPairs('noclosed', $programID);
        if($pairs == array()) return '没有数据';
        return $pairs;
    }
    /**
     * Test get noclosed pairs count.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getNoclosedPairsCount($programID)
    {
        $pairsCount = count($this->getNoclosedPairs($programID));
        if($pairsCount == '没有数据') return '0';
        return $pairsCount;
    }

    /**
     * Test get product pairs by order.
     *
     * @param  int    $programID
     * @param  string $orderBy
     * @param  string $mode
     * @access public
     * @return array
     */
    public function getProductPairsByOrder($programID, $orderBy = 'id_desc', $mode = '')
    {
        $this->objectModel->orderBy = $orderBy;
        $pairs = $this->objectModel->getPairs($mode, $programID);
        return checkOrder($pairs, $orderBy);
    }

    /**
     * Test get all projects by product.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getAllProjectsByProduct($productID)
    {
        $projects = $this->objectModel->getProjectListByProduct($productID, 'all');
        if($projects == array()) return '没有数据';
        return $projects;
    }

    /**
     * Test get projects by status.
     *
     * @param  int    $productID
     * @param  string $status
     * @access public
     * @return array
     */
    public function getProjectsByStatus($productID, $status)
    {
        $projects = $this->objectModel->getProjectListByProduct($productID, $browseType);
        if($projects == array()) return '没有数据';
        return $projects;
    }

    /**
     * Test get project pairs by product id.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getProjectPairsByProductID($productID)
    {
        $projects = $this->objectModel->getProjectPairsByProduct($productID, 0, 0);
        if($projects == array()) return '没有数据';
        return $projects;
    }

    /**
     * Test get append project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getAppendProject($projectID)
    {
        $project = $this->objectModel->getProjectPairsByProduct(10086, 0, $projectID);
        if($project == array()) return '没有数据';
        return $project;
    }

    /**
     * Test judge an action is clickable or not.
     *
     * @param  int    $productID
     * @param  string $status
     * @access public
     * @return string
     */
    public function testIsClickable($productID, $status)
    {
        $product = $this->objectModel->getById($productID);
        $isClick = $this->objectModel->isClickable($product, $status);
        return $isClick == false ? 'false' : 'true';
    }

    /**
     * Test update a product.
     *
     * @param  string $module
     * @param  int    $objectID
     * @param  array  $param
     * @access public
     * @return array
     */
    public function updateObject($module, $objectID, $param = array())
    {
        global $tester;
        $objectModel = $tester->loadModel($module);

        $object = $objectModel->getById($objectID);
        foreach($object as $field => $value)
        {
            if(in_array($field, array_keys($param)))
            {
                $_POST[$field] = $param[$field];
            }
            else
            {
                $_POST[$field] = $value;
            }
        }

        $change = $objectModel->update($objectID);
        if($change == array()) $change = '没有数据更新';
        unset($_POST);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $change;
        }
    }

    /**
     * Test check privilege.
     *
     * @param  int    $productID
     * @access public
     * @return int
     */
    public function checkPrivTest($productID)
    {
        $object = $this->objectModel->checkPriv($productID);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $object ? 1 : 2;
        }
    }

    /**
     * Test get product by id.
     *
     * @param  int    $productID
     * @access public
     * @return object
     */
    public function getByIdTest($productID)
    {
        $object = $this->objectModel->getById($productID);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $object;
        }
    }

    /**
     * Test get product by id list.
     *
     * @param  array  $productIDList
     * @access public
     * @return array
     */
    public function getByIdListTest($productIDList)
    {
        $objects = $this->objectModel->getByIdList($productIDList);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $objects;
        }
    }

    /**
     * Test get product pairs by project.
     *
     * @param  int    $productID
     * @param  string $status
     * @access public
     * @return array
     */
    public function getProductPairsByProjectTest($projectID, $status = 'all')
    {
        $objects = $this->objectModel->getProductPairsByProject($projectID, $status);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $objects;
        }
    }

    /**
     * Test get product pairs by project model.
     *
     * @param  string $model
     * @access public
     * @return int
     */
    public function getPairsByProjectModelTest($model)
    {
        $objects = $this->objectModel->getPairsByProjectModel($model);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return count($objects);
        }
    }

    /**
     * Test get products by project.
     *
     * @param  int    $projectID
     * @param  string $status
     * @access public
     * @return array
     */
    public function getProductsTest($projectID, $status)
    {
        $objects = $this->objectModel->getProducts($projectID, $status);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $objects;
        }
    }

    /**
     * Test get product id by project.
     *
     * @param  int    $projectID
     * @access public
     * @return int
     */
    public function getProductIDByProjectTest($projectID)
    {
        $object = $this->objectModel->getProductIDByProject($projectID);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $object;
        }
    }

    /**
     * Test get ordered products.
     *
     * @param  string $status
     * @access public
     * @return int
     */
    public function getOrderedProductsTest($status)
    {
        $objects = $this->objectModel->getOrderedProducts($status);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return count($objects);
        }
    }

    /**
     * Test get Multi-branch product pairs.
     *
     * @param  int    $programID
     * @access public
     * @return array
     */
    public function getMultiBranchPairsTest($programID)
    {
        $objects = $this->objectModel->getMultiBranchPairs($programID);

        $title  = '';
        foreach($objects as $object) $title .=  ',' . $object;

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $title;
        }
    }

    /**
     * Test batch update products.
     *
     * @param  array  $param
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchUpdateTest($param, $productID)
    {
        $batchUpdateFields['productIDList'] = array('1' => '1', '2' => '2', '3' => '3');
        $batchUpdateFields['programs']      = array('1' => '', '2' => '1', '3' => '2');
        $batchUpdateFields['names']         = array('1' => '正常产品1', '2' => '正常产品2', '3' => '正常产品3');
        $batchUpdateFields['lines']         = array('1' => '0', '2' => '1', '3' => '2');
        $batchUpdateFields['POs']           = array('1' => 'po1', '2' => 'po2', '3' => 'po3');
        $batchUpdateFields['QDs']           = array('1' => 'test1', '2' => 'test2', '3' => 'test3');
        $batchUpdateFields['RDs']           = array('1' => 'dev1', '2' => 'dev2', '3' => 'dev3');
        $batchUpdateFields['types']         = array('1' => 'normal', '2' => 'normal', '3' => 'normal');
        $batchUpdateFields['statuses']      = array('1' => 'normal', '2' => 'normal', '3' => 'normal');
        $batchUpdateFields['descs']         = array('1' => '&lt;div&gt; &lt;p&gt;&lt;h1&gt;一、禅道项目管理软件是做什么的？&lt;/h1&gt; 禅道由 青岛易软天创网络科技有限公司开发，国产开源项目管理软件。它集产品管理、项目管理、质量管理、文档管理、组织管理和事务管理于一体，是一款专业的研发项目管理软件，完整覆盖了研发项目管理的核心流程。禅道管理思想注重实效，功能完备丰富，操作简洁高效，界面美观大方，搜索功能强大，统计报表丰富多样，软件架构合理，扩展灵活，有完善的API可以调用。禅道，专注研发项目管理 &lt;/p&gt; &lt;p&gt;我是数字符号23@#$%#^$ &lt;/p&gt; &lt;p&gt;我是英文dashcuscbrewg &lt;/p&gt; &lt;/div&gt;', '2' => '&lt;div&gt; &lt;p&gt;&lt;h1&gt;一、禅道项目管理软件是做什么的？&lt;/h1&gt; 禅道由 青岛易软天创网络科技有限公司开发，国产开源项目管理软件。它集产品管理、项目管理、质量管理、文档管理、组织管理和事务管理于一体，是一款专业的研发项目管理软件，完整覆盖了研发项目管理的核心流程。禅道管理思想注重实效，功能完备丰富，操作简洁高效，界面美观大方，搜索功能强大，统计报表丰富多样，软件架构合理，扩展灵活，有完善的API可以调用。禅道，专注研发项目管理 &lt;/p&gt; &lt;p&gt;我是数字符号23@#$%#^$ &lt;/p&gt; &lt;p&gt;我是英文dashcuscbrewg &lt;/p&gt; &lt;/div&gt;', '3' => 'i&lt;div&gt; &lt;p&gt;&lt;h1&gt;一、禅道项目管理软件是做什么的？&lt;/h1&gt; 禅道由 青岛易软天创网络科技有限公司开发，国产开源项目管理软件。它集产品管理、项目管理、质量管理、文档管理、组织管理和事务管理于一体，是一款专业的研发项目管理软件，完整覆盖了研发项目管理的核心流程。禅道管理思想注重实效，功能完备丰富，操作简洁高效，界面美观大方，搜索功能强大，统计报表丰富多样，软件架构合理，扩展灵活，有完善的API可以调用。禅道，专注研发项目管理 &lt;/p&gt; &lt;p&gt;我是数字符号23@#$%#^$ &lt;/p&gt; &lt;p&gt;我是英文dashcuscbrewg &lt;/p&gt; &lt;/div&gt;');
        $batchUpdateFields['acls']          = array('1' => 'open', '2' => 'open', '3' => 'open');

        foreach($batchUpdateFields as $field => $defaultValue) $_POST[$field] = $defaultValue;

        foreach($param as $key => $value) $_POST[$key] = $value;

        $changes = $this->objectModel->batchUpdate();
        unset($_POST);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $changes[$productID];
        }
    }

    /**
     * Test close a product.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function closeTest($productID)
    {
        $changes = $this->objectModel->close($productID);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $changes;
        }
    }

    /**
     * Test manage line.
     *
     * @param  array  $param
     * @access public
     * @return void
     */
    public function manageLineTest($param, $moduleID = 0)
    {
        foreach($param as $key => $value) $_POST[$key] = $value;

        $this->objectModel->manageLine();
        unset($_POST);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            global $tester;

            $moduleID = $moduleID ? $moduleID : $tester->dao->select('id')->from(TABLE_MODULE)->orderby('id desc')->fetch()->id;
            $object   = $tester->dao->select('*')->from(TABLE_MODULE)->where('id')->eq($moduleID)->fetch();

            return $object;
        }
    }

    /**
     * Test get stories.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $browseType
     * @param  int    $queryID
     * @param  int    $moduleID
     * @access public
     * @return array
     */
    public function getStoriesTest($productID, $branch, $browseType, $queryID, $moduleID)
    {
        $objects = $this->objectModel->getStories($productID, $branch, $browseType, $queryID, $moduleID);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return count($objects);
        }
    }

    /**
     * Test batch get story stage.
     *
     * @param  array  $stories
     * @access public
     * @return array
     */
    public function batchGetStoryStageTest($stories)
    {
        $objects = $this->objectModel->batchGetStoryStage($stories);
        a($objects);

        if(dao::isError())
        {
            return dao::getError();
        }
        else
        {
            return $objects;
        }
    }
}
