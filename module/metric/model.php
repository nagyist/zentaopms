<?php
/**
 * The model file of metric module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      zhouxin <zhouxin@easycorp.ltd>
 * @package     metric
 * @version     $Id: model.php 5145 2013-07-15 06:47:26Z zhouxin@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class metricModel extends model
{
    /**
     * 用对象数据创建度量。
     * Create a metric.
     *
     * @param  object  $metric
     * @param  string  $back
     * @access public
     * @return int|false
     */
    public function create($metric)
    {
        $this->dao->insert(TABLE_METRIC)->data($metric)
            ->autoCheck()
            ->checkIF(!empty($metric->name), 'name', 'unique', "`deleted` = '0'")
            ->checkIF(!empty($metric->code), 'code', 'unique', "`deleted` = '0'")
            ->exec();
        if(dao::isError()) return false;
        $metricID = $this->dao->lastInsertID();

        $this->loadModel('action')->create('metric', $metricID, 'opened');

        return $metricID;
    }

    /**
     * 获取度量项数据列表。
     * Get metric data list.
     *
     * @param  string $type
     * @param  int    $queryID
     * @param  string $sort
     * @param  object $pager
     * @access public
     * @return array|false
     */
    public function getList($scope, $param, $type = '', $queryID = 0, $sort = '', $pager = null)
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('metricQuery', $query->sql);
                $this->session->set('metricForm', $query->form);
            }
            else
            {
                $this->session->set('metricQuery', ' 1 = 1');
            }
        }

        $object = null;
        $purpose = null;
        if($type == 'byTree')
        {
            $object_purpose = explode('_', $param);
            $object = $object_purpose[0];
            if(count($object_purpose) == 2) $purpose = $object_purpose[1];
        }

        $metrics = $this->metricTao->fetchMetrics($scope, $object, $purpose, $this->session->metricQuery, $sort, $pager);

        return $metrics;
    }

    /**
     * 获取模块树数据。
     * Get module tree data.
     *
     * @param string  $scope
     * @access public
     * @return void
     */
    public function getModuleTreeList($scope)
    {
        return $this->metricTao->fetchModules($scope);
    }

    /**
     * 根据代号获取度量项信息。
     * Get metric info by code.
     *
     * @param  string       $code
     * @param  string|array $fieldList
     * @access public
     * @return object|false
     */
    public function getByCode(string $code, string|array $fieldList = '*')
    {
        if(is_array($fieldList)) $fieldList = implode(',', $fieldList);
        return $this->dao->select($fieldList)->from(TABLE_METRIC)->where('code')->eq($code)->fetch();
    }

    /**
     * 根据ID获取度量项信息。
     * Get metric info by id.
     *
     * @param  int          $metricID
     * @param  string|array $fieldList
     * @access public
     * @return object|false
     */
    public function getByID(int $metricID, string|array $fieldList = '*')
    {
        if(is_array($fieldList)) $fieldList = implode(',', $fieldList);
        return $this->dao->select($fieldList)->from(TABLE_METRIC)->where('id')->eq($metricID)->fetch();
    }

    /**
     * 获取度量项数据源句柄。
     * Get data source statement of calculator.
     *
     * @param  object $calculator
     * @access public
     * @return PDOStatement|string
     */
    public function getDataStatement($calculator, $returnType = 'statement')
    {
        if(!empty($calculator->dataset))
        {
            include_once $this->metricTao->getDatasetPath();

            $dataset    = new dataset($this->dao);
            $dataSource = $calculator->dataset;
            $fieldList  = implode(',', $calculator->fieldList);

            $statement = $dataset->$dataSource($fieldList);
            $sql       = $dataset->dao->get();
        }
        else
        {
            $calculator->setDAO($this->dao);

            $statement = $calculator->getStatement();
            $sql       = $calculator->dao->get();
        }

        return $returnType == 'sql' ? $sql : $statement;
    }

    /**
     * 根据代号获取计算实时度量项的结果。
     * Get result of calculate metric by code.
     *
     * @param  string $code
     * @param  array  $options e.g. array('product' => '1,2,3', 'year' => '2023')
     * @access public
     * @return array
     */
    public function getResultByCode($code, $options = array())
    {
        $metric = $this->dao->select('id,code,scope,purpose')->from(TABLE_METRIC)->where('code')->eq($code)->fetch();
        if(!$metric) return false;

        $calcPath = $this->metricTao->getCalcRoot() . $metric->scope . DS . $metric->purpose . DS . $metric->code . '.php';
        if(!is_file($calcPath)) return false;

        include_once $this->metricTao->getBaseCalcPath();
        include_once $calcPath;
        $calculator = new $metric->code;

        $statement = $this->getDataStatement($calculator);
        $rows = $statement->fetchAll();

        foreach($rows as $row) $calculator->calculate($row);
        return $calculator->getResult($options);
    }

    /**
     * 根据代号列表批量获取度量项的结果。
     * Get result of calculate metric by code list.
     *
     * @param  array $codes   e.g. array('code1', 'code2')
     * @param  array $options e.g. array('product' => '1,2,3', 'year' => '2023')
     * @access public
     * @return array
     */
    public function getResultByCodes($codes, $options = array())
    {
        $results = array();
        foreach($codes as $code)
        {
            $result = $this->getResultByCode($code, $options);
            if($result) $results[$code] = $result;
        }

        return $results;
    }

    /**
     * 获取可计算的度量项列表。
     * Get executable metric list.
     *
     * @access public
     * @return array
     */
    public function getExecutableMetric()
    {
        $currentWeek = date('w');
        $currentDay  = date('d');
        $now         = date('H:i');

        $metricList = $this->dao->select('id,code,time')
            ->from(TABLE_METRIC)
            ->where('deleted')->eq('0')
            ->fetchAll();

        $excutableMetrics = array();
        foreach($metricList as $metric)
        {
            $excutableMetrics[$metric->id] = $metric->code;
        }
        return $excutableMetrics;
    }

    /**
     * insertmetricLib
     *
     * @param  int    $records
     * @access public
     * @return void
     */
    public function insertmetricLib($records)
    {
        $this->dao->begin();
        foreach($records as $record)
        {
            $this->dao->insert(TABLE_METRICLIB)
                ->data($record)
                ->exec();
        }
        $this->dao->commit();

        return dao::isError();
    }

    /**
     * 获取可计算的度量项对象列表。
     * Get executable calculator list.
     *
     * @access public
     * @return array
     */
    public function getExecutableCalcList()
    {
        $funcRoot = $this->metricTao->getCalcRoot();

        $fileList = array();
        foreach($this->config->metric->scopeList as $scope)
        {
            foreach($this->config->metric->purposeList as $purpose)
            {
                $pattern = $funcRoot . $scope . DS . $purpose . DS . '*.php';
                $matchedFiles = glob($pattern);
                if($matchedFiles !== false) $fileList = array_merge($fileList, $matchedFiles);
            }
        }

        $calcList = array();
        $excutableMetric = $this->getExecutableMetric();
        foreach($fileList as $file)
        {
            $code = rtrim(basename($file), '.php');
            if(!in_array($code, $excutableMetric)) continue;
            $id = array_search($code, $excutableMetric);

            $calc = new stdclass();
            $calc->code = $code;
            $calc->file = $file;
            $calcList[$id] = $calc;
        }

        return $calcList;
    }

    /**
     * 获取度量项计算实例列表。
     * Get calculator instance list.
     *
     * @access public
     * @return array
     */
    public function getCalcInstanceList()
    {
        $calcList = $this->getExecutableCalcList();

        include $this->metricTao->getBaseCalcPath();
        $calcInstances = array();
        foreach($calcList as $id => $calc)
        {
            $file      = $calc->file;
            $className = $calc->code;

            require_once $file;
            $metricInstance = new $className;
            $metricInstance->id = $id;

            $calcInstances[$className] = $metricInstance;
        }

        return $calcInstances;
    }

    /**
     * 获取通用数据集对象。
     * Get instance of data set object.
     *
     * @access public
     * @return dataset
     */
    public function getDataset()
    {
        $datasetPath = $this->metricTao->getDatasetPath();
        include_once $datasetPath;
        return new dataset($this->dao);
    }

    /**
     * 对度量项按照通用数据集进行归类，没有数据集不做归类。
     * Classify calculator instance list by its data set.
     *
     * @param  array  $calcList
     * @access public
     * @return array
     */
    public function classifyCalc($calcList)
    {
        $datasetCalcGroup = array();
        $otherCalcList    = array();
        foreach($calcList as $code => $calc)
        {
            if(empty($calc->dataset))
            {
                $otherCalcList[$code] = $calc;
                continue;
            }

            $dataset = $calc->dataset;
            if(!isset($datasetCalcGroup[$dataset])) $datasetCalcGroup[$dataset] = array();
            $datasetCalcGroup[$dataset][$code] = $calc;
        }

        $classifiedCalcGroup = array();
        foreach($datasetCalcGroup as $dataset => $calcList) $classifiedCalcGroup[] = (object)array('dataset' => $dataset, 'calcList' => $calcList);

        foreach($otherCalcList as $code => $calc) $classifiedCalcGroup[] = (object)array('dataset' => '', 'calcList' => array($code => $calc));
        return $classifiedCalcGroup;
    }

    /**
     * 对度量项的字段列表取并集。
     * Unite field list of each calculator.
     *
     * @param  array  $calcList
     * @access public
     * @return string
     */
    public function uniteFieldList($calcList)
    {
        $fieldList = array();
        foreach($calcList as $calcInstance) $fieldList  = array_merge($fieldList, $calcInstance->fieldList);
        return implode(',', array_unique($fieldList));
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->metric->browse->search['actionURL'] = $actionURL;
        $this->config->metric->browse->search['queryID']   = $queryID;
        $this->config->metric->browse->search['params']['dept']['values']    = $this->loadModel('dept')->getOptionMenu();
        $this->config->metric->browse->search['params']['visions']['values'] = $this->loadModel('user')->getVisionList();

        $this->loadModel('search')->setSearchParams($this->config->metric->browse->search);
    }

    /**
     * 获取范围的对象列表。
     * Get object pairs by scope.
     *
     * @param  string    $scope
     * @access protected
     * @return array
     */
    protected function getPairsByScope($scope)
    {
        if($scope == 'global') return array();

        $objectPairs = array();
        switch($scope)
        {
            case 'dept':
                $objectPairs = $this->loadModel('dept')->getDeptPairs();
                break;
            case 'user':
                $objectPairs = $this->loadModel('user')->getPairs('noletter');
                break;
            default:
                $objectPairs = $this->loadModel($scope)->getPairs();
                break;
        }

        return $objectPairs;
    }

    /**
     * 获取度量项的日期字符串。
     * Build date cell.
     *
     * @param  object    $row
     * @access protected
     * @return string
     */
    protected function buildDateCell($row)
    {
        extract((array)$row);

        if(isset($year, $month, $day))
        {
            return "{$year}-{$month}-{$day}";
        }
        elseif(isset($year, $week))
        {
            return sprintf($this->lang->metric->weekCell, $year, $month);
        }
        elseif(isset($year, $month))
        {
            return $year . $this->lang->year . $month . $this->lang->month;
        }
        elseif(isset($year))
        {
            return $year . $this->lang->year;
        }

        return false;
    }

    /**
     * 检查度量项计算文件是否存在。
     * Check if the calculator file exists or not.
     *
     * @param  object    $row
     * @access protected
     * @return bool
     */
    protected function checkCalcExists($metric)
    {
        $calcName = $this->metricTao->getCustomCalcRoot() . $metric->code . '.php';
        return file_exists($calcName);
    }

    /**
     * 检查度量项计算文件是否定义了必要的类。
     * Check whether the necessary class exist in the file.
     *
     * @param  object    $row
     * @access protected
     * @return bool
     */
    protected function checkCalcClass($metric)
    {
        return class_exists($metric->code);
    }

    /**
     * 检查度量项计算文件中是否编写了必要的方法。
     * Check whether the necessary methods exist in the file.
     *
     * @param  object    $row
     * @access protected
     * @return bool
     */
    protected function checkCalcMethods($metric)
    {
        $methodNameList = $this->metricTao->getMethodNameList($metric->code);
        foreach($this->config->metric->$necessaryMethodList as $method)
        {
            if(!in_array($method, $methodNameList)) return false;
        }

        return true;
    }

    /**
     * 没有度量的显示范围不做显示。
     * Unset scope item that have no metric.
     *
     * @access protected
     * @return void
     */
    public function processScopeList()
    {
        foreach($this->lang->metric->scopeList as $scope => $name)
        {
            $metrics = $this->metricTao->fetchMetrics($scope, '', '', '', '', null);
            if(empty($metrics)) unset($this->lang->metric->scopeList[$scope]);
        }
    }

    /**
     * 根据后台配置的估算单位对列表赋值。
     * Assign unitList['measure'] by custom hourPoint.
     *
     * @access protected
     * @return void
     */
    public function processUnitList()
    {
        $this->app->loadLang('custom');
        $key = zget($this->config->custom, 'hourPoint', '0');

        $this->lang->metric->unitList['measure'] = $this->lang->custom->conceptOptions->hourPoint[$key];
    }
}
