<?php
/**
 * The control file of upgrade module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     upgrade
 * @version     $Id: control.php 5119 2013-07-12 08:06:42Z wyd621@gmail.com $
 * @link        https://www.zentao.net
 */
class upgrade extends control
{
    /**
     * The index page.
     *
     * @access public
     * @return void
     */
    public function index()
    {
        /* 如果没有升级入口文件，跳转到应用的首页。*/
        /* Locate to index page of my module, if upgrade.php does not exist. */
        $upgradeFile = $this->app->wwwRoot . 'upgrade.php';
        if(!file_exists($upgradeFile)) $this->locate($this->createLink('my', 'index'));

        /* 删除临时 model 文件。*/
        /* Delete tmp mode files. */
        $this->upgrade->deleteTmpModel();

        if(version_compare($this->config->installedVersion, '6.4', '<=')) $this->locate(inlink('license'));
        $this->locate(inlink('backup'));
    }

    /**
     * 授权协议页面。
     * Check agree license.
     *
     * @access public
     * @return void
     */
    public function license()
    {
        if($this->get->agree == true) $this->locate(inlink('backup'));

        $clientLang = $this->app->getClientLang();
        $licenseCN  = file_get_contents($this->app->getBasePath() . 'doc/LICENSE.CN');
        $licenseEN  = file_get_contents($this->app->getBasePath() . 'doc/LICENSE.EN');

        $license = $licenseEN . $licenseCN;
        if($clientLang == 'zh-cn' || $clientLang == 'zh-tw') $license = $licenseCN . $licenseEN;

        $this->view->title   = $this->lang->upgrade->common;
        $this->view->license = $license;
        $this->display();
    }

    /**
     * 提示备份数据库。
     * Prompt to backup database.
     *
     * @access public
     * @return void
     */
    public function backup()
    {
        $this->session->set('upgrading', true);

        $this->view->title = $this->lang->upgrade->common;
        $this->display();
    }

    /**
     * 选择升级前的禅道版本。
     * Select the version of old zentao.
     *
     * @access public
     * @return void
     */
    public function selectVersion()
    {
        $version = str_replace(array(' ', '.'), array('', '_'), $this->config->installedVersion);
        $version = strtolower($version);

        /* 处理迅捷版的版本。*/
        /* Process the lite version. */
        if($this->config->visions == ',lite,')
        {
            $installedVersion = str_replace('.', '_', $this->config->installedVersion);
            $version = array_search($installedVersion, $this->config->upgrade->liteVersion);

            foreach($this->lang->upgrade->fromVersions as $key => $value)
            {
                if(strpos($key, 'lite') === false) unset($this->lang->upgrade->fromVersions[$key]);
            }

            $this->config->version = ($this->config->edition == 'biz' ? 'LiteVIP' : 'Lite') . $this->config->liteVersion;
        }

        if($_POST) $this->locate(inlink('confirm', "fromVersion={$this->post->fromVersion}"));

        $this->view->title   = $this->lang->upgrade->common . $this->lang->colon . $this->lang->upgrade->selectVersion;
        $this->view->version = $version;
        $this->display();
    }

    /**
     * 确认要执行的SQL语句。
     * Confirm the upgrade sql.
     *
     * @param  string  $fromVersion
     * @access public
     * @return void
     */
    public function confirm(string $fromVersion = '')
    {
        if(file_exists($this->app->getTmpRoot() . 'upgradeSqlLines')) @unlink($this->app->getTmpRoot() . 'upgradeSqlLines');

        $this->view->fromVersion = $fromVersion;

        if(strpos($fromVersion, 'lite') !== false) $fromVersion = $this->config->upgrade->liteVersion[$fromVersion];
        if(strpos($fromVersion, 'ipd') !== false)  $fromVersion = $this->config->upgrade->ipdVersion[$fromVersion];

        if($_POST) $this->locate(inlink('execute', "fromVersion={$fromVersion}"));

        $confirmSql = $this->upgrade->getConfirm($fromVersion);

        /* When sql is empty then skip it. */
        if(empty($confirmSql)) $this->locate(inlink('execute', "fromVersion={$fromVersion}"));

        $this->session->set('step', '');
        $this->view->title    = $this->lang->upgrade->confirm;
        $this->view->confirm  = $confirmSql;
        $this->view->writable = is_writable($this->app->getTmpRoot()) ? true : false;

        $this->display();
    }

    /**
     * 执行升级的 SQL。
     * Execute the upgrading sql.
     *
     * @param  string $fromVersion
     * @access public
     * @return void
     */
    public function execute(string $fromVersion = '')
    {
        session_write_close();
        $this->session->set('step', '');

        $this->view->title       = $this->lang->upgrade->result;
        $this->view->fromVersion = $fromVersion;

        /* 手动删除无法自动删除的文件。*/
        /* Remove files that can not be deleted automatically. */
        $result = $this->upgrade->deleteFiles();
        if($result)
        {
            $this->view->result = 'fail';
            $this->view->errors = $result;

            return $this->display();
        }

        $rawFromVersion = isset($_POST['fromVersion']) ? $this->post->fromVersion : $fromVersion;
        if(strpos($fromVersion, 'lite') !== false) $rawFromVersion = $this->config->upgrade->liteVersion[$fromVersion];
        if(strpos($fromVersion, 'ipd') !== false)  $rawFromVersion = $this->config->upgrade->ipdVersion[$fromVersion];

        $installedVersion = $this->loadModel('setting')->getItem('owner=system&module=common&section=global&key=version');

        if($this->config->version != $installedVersion) $this->upgrade->execute($rawFromVersion);

        if($this->upgrade->isError())
        {
            $this->view->result = 'sqlFail';
            $this->view->errors = $this->upgrade->getError();
            $this->display();
        }

        $this->upgradeZen->afterExec($fromVersion, $rawFromVersion);
    }

    /**
     * 引导升级到 18 版本。
     * Guide to 18 version.
     *
     * @param  string $fromVersion
     * @param  string $mode
     * @access public
     * @return void
     */
    public function to18Guide(string $fromVersion, string $mode = '')
    {
        if($_POST || $mode)
        {
            if($this->post->mode) $mode = $this->post->mode;

            if($this->config->edition == 'ipd') $mode = 'PLM';
            $this->loadModel('setting')->setItem('system.common.global.mode', $mode);
            $this->loadModel('custom')->disableFeaturesByMode($mode);

            /* 更新迭代的概念。*/
            /* Update sprint concept. */
            $this->upgradeZen->setSprintConcept();

            if($mode == 'light') $this->upgradeZen->setDefaultProgram();

            $this->locate(inlink('selectMergeMode', "fromVersion={$fromVersion}&mode={$mode}"));
        }

        $this->app->loadLang('install');

        list($disabledFeatures, $enabledScrumFeatures, $disabledScrumFeatures) = $this->loadModel('custom')->computeFeatures();

        $this->view->title                 = $this->lang->custom->selectUsage;
        $this->view->edition               = $this->config->edition;
        $this->view->disabledFeatures      = $disabledFeatures;
        $this->view->enabledScrumFeatures  = $enabledScrumFeatures;
        $this->view->disabledScrumFeatures = $disabledScrumFeatures;
        $this->display();
    }

    /**
     * 归并项目集。
     * Merge program.
     *
     * @param  string $type
     * @param  int    $programID
     * @param  string $projectType project|execution
     * @access public
     * @return void
     */
    public function mergeProgram(string $type = 'productline', int $programID = 0, string $projectType = 'project')
    {
        set_time_limit(0);

        $this->session->set('upgrading', true);
        $this->app->loadLang('program');
        $this->app->loadLang('project');
        $this->app->loadLang('product');
        $this->app->loadConfig('execution');

        if($_POST)
        {
            $projectType = isset($_POST['projectType']) ? $_POST['projectType'] : 'project';
            if($type == 'productline')
            {
                $linkedProducts = array();
                $linkedSprints  = array();
                $unlinkSprints  = array();
                $sprintProducts = array();
                $singleProducts = array();

                /* Compute checked products and sprints, unchecked products and sprints. */
                foreach($_POST['products'] as $lineID => $products)
                {
                    foreach($products as $productID)
                    {
                        $linkedProducts[$productID] = $productID;

                        if(isset($_POST['sprints'][$lineID][$productID]))
                        {
                            foreach($_POST['sprints'][$lineID][$productID] as $sprintID)
                            {
                                $linkedSprints[$sprintID]  = $sprintID;
                                $sprintProducts[$sprintID] = $productID;
                                unset($_POST['sprintIdList'][$lineID][$productID][$sprintID]);
                            }
                            $unlinkSprints[$productID] = $this->post->sprintIdList[$lineID][$productID];
                        }
                    }
                }

                /* Create Program. */
                $result = $this->upgrade->createProgram($linkedProducts, $linkedSprints);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
                if(isset($result['result']) && $result['result'] == 'fail') return $this->send($result);

                list($programID, $projectList, $lineID) = $this->upgrade->createProgram($linkedProducts, $linkedSprints);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

                /* Process merged products and projects. */
                if($projectType == 'execution')
                {
                    /* Use historical projects as execution upgrades. */
                    $this->upgrade->processMergedData($programID, $projectList, $lineID, $linkedProducts, $linkedSprints);
                }
                else
                {
                    /* Use historical projects as project upgrades. */
                    $singleProducts = array_diff($linkedProducts, $sprintProducts);
                    foreach($linkedSprints as $sprint)
                    {
                        $this->upgrade->processMergedData($programID, zget($projectList, $sprint, array()), $lineID, array($sprintProducts[$sprint] => $sprintProducts[$sprint]), array($sprint => $sprint));
                    }
                }

                /* When upgrading historical data as a project, handle products that are not linked with the project. */
                if(!empty($singleProducts)) $this->upgrade->computeProductAcl($singleProducts, $programID, $lineID);

                /* Process unlinked sprint and product. */
                foreach($linkedProducts as $productID => $product)
                {
                    if((isset($unlinkSprints[$productID]) and empty($unlinkSprints[$productID])) || !isset($unlinkSprints[$productID])) $this->dao->update(TABLE_PRODUCT)->set('line')->eq($lineID)->where('id')->eq($productID)->exec();
                }
            }
            elseif($type == 'product')
            {
                $linkedProducts = array();
                $linkedSprints  = array();
                $unlinkSprints  = array();
                $sprintProducts = array();
                $singleProducts = array();
                foreach($_POST['products'] as $productID)
                {
                    $linkedProducts[$productID] = $productID;

                    if(isset($_POST['sprints'][$productID]))
                    {
                        foreach($_POST['sprints'][$productID] as $sprintID)
                        {
                            $linkedSprints[$sprintID]  = $sprintID;
                            $sprintProducts[$sprintID] = $productID;
                            unset($_POST['sprintIdList'][$productID][$sprintID]);
                        }
                        $unlinkSprints += $this->post->sprintIdList[$productID];
                    }
                }

                /* Create Program. */
                list($programID, $projectList, $lineID) = $this->upgrade->createProgram($linkedProducts, $linkedSprints);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

                /* Process productline. */
                $this->dao->delete()->from(TABLE_MODULE)->where('`root`')->eq(0)->andWhere('`type`')->eq('line')->exec();

                /* Process merged products and projects. */
                if($projectType == 'execution')
                {
                    /* Use historical projects as execution upgrades. */
                    $this->upgrade->processMergedData($programID, $projectList, $lineID, $linkedProducts, $linkedSprints);
                }
                else
                {
                    /* Use historical projects as project upgrades. */
                    $singleProducts = array_diff($linkedProducts, $sprintProducts);
                    foreach($linkedSprints as $sprint)
                    {
                        $this->upgrade->processMergedData($programID, $projectList[$sprint], $lineID, array($sprintProducts[$sprint] => $sprintProducts[$sprint]), array($sprint => $sprint));
                    }
                }

                /* When upgrading historical data as a project, handle products that are not linked with the project. */
                if(!empty($singleProducts)) $this->upgrade->computeProductAcl($singleProducts, $programID, $lineID);
            }
            elseif($type == 'sprint')
            {
                $linkedSprints = $this->post->sprints;

                /* Create Program. */
                list($programID, $projectList, $lineID) = $this->upgrade->createProgram(array(), $linkedSprints);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

                if($projectType == 'execution')
                {
                    /* Use historical projects as execution upgrades. */
                    $this->upgrade->processMergedData($programID, $projectList, $lineID, array(), $linkedSprints);
                }
                else
                {
                    /* Use historical projects as project upgrades. */
                    foreach($linkedSprints as $sprint)
                    {
                        $this->upgrade->processMergedData($programID, $projectList[$sprint], $lineID, array(), array($sprint => $sprint));
                    }
                }
            }
            elseif($type == 'moreLink')
            {
                $linkedSprints = $this->post->sprints;

                /* Create Program. */
                list($programID, $projectList, $lineID) = $this->upgrade->createProgram(array(), $linkedSprints);
                if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

                if($projectType == 'execution')
                {
                    /* Use historical projects as execution upgrades. */
                    $this->upgrade->processMergedData($programID, $projectList, $lineID, array(), $linkedSprints);
                }
                else
                {
                    /* Use historical projects as project upgrades. */
                    foreach($linkedSprints as $sprint)
                    {
                        $this->upgrade->processMergedData($programID, $projectList[$sprint], $lineID, array(), array($sprint => $sprint));
                    }
                }

                /* If is more-link sprints, and as project upgrade, set old relation into new project. */
                $projectProducts = $this->dao->select('product,project,branch,plan')->from(TABLE_PROJECTPRODUCT)->where('project')->in($linkedSprints)->fetchAll();

                foreach($projectProducts as $projectProduct)
                {
                    $data = new stdclass();
                    $data->project = $projectType == 'execution' ? $projectList : $projectList[$projectProduct->project];
                    $data->product = $projectProduct->product;
                    $data->plan    = $projectProduct->plan;
                    $data->branch  = $projectProduct->branch;

                    $this->dao->replace(TABLE_PROJECTPRODUCT)->data($data)->exec();
                }
            }

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'load' => $this->createLink('upgrade', 'mergeProgram', "type={$type}&programID={$programID}&projectType={$projectType}")));
        }

        /* Get no merged product and project count. */
        $noMergedProductCount = $this->dao->select('count(*) AS count')->from(TABLE_PRODUCT)->where('program')->eq(0)->andWhere('vision')->eq('rnd')->fetch('count');
        $noMergedSprintCount  = $this->dao->select('count(*) AS count')->from(TABLE_PROJECT)->where('vision')->eq('rnd')->andWhere('project')->eq(0)->andWhere('type')->eq('sprint')->andWhere('deleted')->eq(0)->fetch('count');

        /* When all products and projects merged then finish and locate afterExec page. */
        if(empty($noMergedProductCount) && empty($noMergedSprintCount)) $this->upgradeZen->upgradeAfterMerged();

        $this->view->noMergedProductCount = $noMergedProductCount;
        $this->view->noMergedSprintCount  = $noMergedSprintCount;

        $systemMode = $this->loadModel('setting')->getItem('owner=system&module=common&section=global&key=mode');

        $this->loadModel('project');
        $this->view->type = $type;

        /* 获取产品线下的产品和项目。*/
        /* Get products and projects group by product line. */
        if($type == 'productline') $this->upgradeZen->assignProductsAndProjectsGroupByProductline($projectType);

        /* 获取产品下的项目。*/
        /* Get projects group by product. */
        if($type == 'product') $this->upgradeZen->assignProjectsGroupByProduct($projectType);

        /* Get no merged projects that is not linked product. */
        if($type == 'sprint')
        {
            $this->upgradeZen->assignSprintsWithoutProduct();
            if(!$programID && $systemMode == 'light') $programID = $this->loadModel('setting')->getItem('owner=system&module=common&section=global&key=defaultProgram');
        }

        /* Get no merged projects that link more then two products. */
        if($type == 'moreLink')
        {
            $noMergedSprints = $this->dao->select('*')->from(TABLE_PROJECT)
                ->where('project')->eq(0)
                ->andWhere('vision')->eq('rnd')
                ->andWhere('type')->eq('sprint')
                ->andWhere('deleted')->eq(0)
                ->orderBy('id_desc')
                ->fetchAll('id');

            $projectProducts = $this->dao->select('*')->from(TABLE_PROJECTPRODUCT)->where('project')->in(array_keys($noMergedSprints))->fetchGroup('project', 'product');

            $productPairs = array();
            foreach($projectProducts as $sprintID => $products)
            {
                foreach($products as $productID => $data) $productPairs[$productID] = $productID;
            }

            $projects = $this->dao->select('t1.*,t2.product as productID')->from(TABLE_PROJECT)->alias('t1')
                ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id=t2.project')
                ->where('t2.product')->in($productPairs)
                ->andWhere('t1.vision')->eq('rnd')
                ->andWhere('t1.type')->eq('project')
                ->fetchAll('productID');

            foreach($noMergedSprints as $sprintID => $sprint)
            {
                $products = zget($projectProducts, $sprintID, array());
                foreach($products as $productID => $data)
                {
                    $project = zget($projects, $productID, '');
                    if($project) $sprint->projects[$project->id] = $project->name;
                }

                if(!isset($sprint->projects)) $sprint->projects = $this->dao->select('id,name')->from(TABLE_PROJECT)->where('type')->eq('project')->andWhere('vision')->eq('rnd')->fetchPairs();
            }

            $this->view->noMergedSprints = $noMergedSprints;
        }

        $programs = $this->loadModel('program')->getPairs(true, 'id_asc');
        $currentProgramID = $programID ? $programID : key($programs);

        $this->view->title       = $this->lang->upgrade->mergeProgram;
        $this->view->programs    = $programs;
        $this->view->programID   = $programID;
        $this->view->projects    = $this->upgrade->getProjectPairsByProgram($currentProgramID);
        $this->view->lines       = $currentProgramID ? $this->loadModel('product')->getLinePairs($currentProgramID) : array();
        $this->view->users       = $this->loadModel('user')->getPairs('noclosed|noempty');
        $this->view->groups      = $this->loadModel('group')->getPairs();
        $this->view->systemMode  = $systemMode;
        $this->view->projectType = $projectType;
        $this->display();
    }

    /**
     * 选择数据归并的方式。
     * Select the merge mode when upgrading to zentaopms 18.0.
     *
     * @param  string  $fromVersion
     * @param  string  $mode        light | ALM
     * @access public
     * @return void
     */
    public function selectMergeMode(string $fromVersion, string $mode = 'light')
    {
        if($_POST)
        {
            $mergeMode = $this->post->projectType;
            if($mergeMode == 'manually') $this->locate(inlink('mergeProgram'));

            if($mode == 'light') $programID = $this->loadModel('setting')->getItem('owner=system&module=common&section=global&key=defaultProgram');
            if($mode == 'ALM')   $programID = $this->loadModel('program')->createDefaultProgram();

            if($mergeMode == 'project')   $this->upgrade->upgradeInProjectMode($programID);
            if($mergeMode == 'execution') $this->upgrade->upgradeInExecutionMode($programID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->upgrade->computeObjectMembers();
            $this->upgrade->initUserView();
            $this->upgrade->setDefaultPriv();
            $this->dao->update(TABLE_CONFIG)->set('value')->eq('0_0')->where('`key`')->eq('productProject')->exec();

            $hourPoint = $this->loadModel('setting')->getItem('owner=system&module=custom&key=hourPoint');
            if(empty($hourPoint)) $this->setting->setItem('system.custom.hourPoint', 0);

            $sprints = $this->dao->select('id')->from(TABLE_PROJECT)->where('type')->eq('sprint')->fetchAll('id');
            $this->dao->update(TABLE_ACTION)->set('objectType')->eq('execution')->where('objectID')->in(array_keys($sprints))->andWhere('objectType')->eq('project')->exec();

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->locate(inlink('afterExec', "fromVersion={$fromVersion}"));
        }
        $this->view->title       = $this->lang->upgrade->selectMergeMode;
        $this->view->fromVersion = $fromVersion;
        $this->view->systemMode  = $mode;
        $this->display();
    }

    /**
     * Rename object in upgrade.
     *
     * @param  string $type  project|product|execution
     * @param  string $duplicateList
     * @access public
     * @return void
     */
    public function renameObject($type = 'project', $duplicateList = '')
    {
        $this->app->loadLang($type);
        if($_POST)
        {
            foreach($this->post->project as $projectID => $projectName)
            {
                if(!$projectName) continue;
                $this->dao->update(TABLE_PROJECT)->set('name')->eq($projectName)->where('id')->eq($projectID)->exec();
            }

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'load' => true));
        }

        $objectGroup = array();
        if($type == 'project' or $type == 'execution')
        {
            $objectGroup = $this->dao->select('id,name')->from(TABLE_PROJECT)
                ->where('id')->in($duplicateList)
                ->orderBy('name')
                ->fetchAll();
        }

        $this->view->type        = $type;
        $this->view->objectGroup = $objectGroup;
        $this->display();
    }

    /**
     * Merge Repos.
     *
     * @access public
     * @return void
     */
    public function mergeRepo()
    {
        if($_POST)
        {
            $this->upgrade->mergeRepo();
            return $this->send(array('result' => 'success', 'load' => inlink('mergeRepo')));
        }

        $repoes   = $this->dao->select('id, name')->from(TABLE_REPO)->where('deleted')->eq(0)->andWhere('product')->eq('')->fetchPairs();
        $products = $this->dao->select('id, name')->from(TABLE_PRODUCT)->where('deleted')->eq(0)->fetchPairs();
        if(empty($repoes) || empty($products))
        {
            $this->dao->delete()->from(TABLE_BLOCK)->exec();
            $this->dao->delete()->from(TABLE_CONFIG)->where('`key`')->eq('blockInited')->exec();
            $this->loadModel('setting')->deleteItems('owner=system&module=common&section=global&key=upgradeStep');
            $this->locate(inlink('afterExec', 'fromVersion=&processed=no'));
        }

        $this->view->title    = $this->lang->upgrade->mergeRepo;
        $this->view->repoes   = $repoes;
        $this->view->products = $products;
        $this->view->programs = $this->dao->select('id, name')->from(TABLE_PROGRAM)->where('deleted')->eq(0)->andWhere('type')->eq('program')->fetchPairs();

        $this->display();
    }

    /**
     * Ajax get progress.
     *
     * @param  int    $offset
     * @access public
     * @return void
     */
    public function ajaxGetProgress($offset = 0)
    {
        $tmpProgressFile = $this->app->getTmpRoot() . 'upgradeSqlLines';
        $upgradeLogFile  = $this->upgrade->getLogFile();

        $progress = 1;
        if(file_exists($tmpProgressFile) && $offset != 0)
        {
            $sqlLines = file_get_contents($tmpProgressFile);
            if(empty($sqlLines)) $progress = $this->session->upgradeProgress ? $this->session->upgradeProgress : 1;
            if($sqlLines == 'completed') $progress = 100;

            $sqlLines = explode('-', $sqlLines);
            $progress = round((int)$sqlLines[1] / (int)$sqlLines[0] * 100);
            if($progress > 95) $progress = 100;
            $this->session->set('upgradeProgress', $progress);
        }

        $log  = !file_exists($upgradeLogFile) ? '' : file_get_contents($upgradeLogFile, false, null, $offset);
        $size = 10 * 1024;
        if(!empty($log) && mb_strlen($log) > $size)
        {
            $left     = mb_substr($log, $size);
            $log      = mb_substr($log, 0, $size);
            $position = strpos($left, "\n");
            if($position !== false) $log .= substr($left, 0, $position + 1);
        }
        $log = trim($log);
        return print(json_encode(array('log' => str_replace("\n", "<br />", $log) . ($log ? '<br />' : ''), 'progress' => $progress, 'offset' => $offset + strlen($log))));
    }

    /**
     * Ajax get fix consistency logs.
     *
     * @param  int    $offset
     * @access public
     * @return void
     */
    public function ajaxGetFixLogs($offset = 0)
    {
        $logFile  = $this->upgrade->getConsistencyLogFile();
        $lines    = !file_exists($logFile) ? array() : file($logFile);

        $log = array_slice($lines, $offset);
        $finished = ($log and end($log) == 'Finished') ? true : false;

        return print(json_encode(array('log' => implode("<br />", $log) . "<br />", 'finished' => $finished, 'offset' => count($lines))));
    }

    /**
     * Ajax fix for consistency.
     *
     * @param  string $version
     * @access public
     * @return void
     */
    public function ajaxFixConsistency($version)
    {
        set_time_limit(0);
        session_write_close();
        $this->upgrade->fixConsistency($version);
    }

    /**
     * Get the project of the program it belongs to.
     *
     * @param  int   $programID
     * @access public
     * @return void
     */
    public function ajaxGetProjectPairsByProgram($programID = 0)
    {
        $projects = $this->upgrade->getProjectPairsByProgram($programID);

        $result = array();
        foreach($projects as $projectID => $projectName)
        {
            $result[] = array('text' => $projectName, 'value' => $projectID);
        }
        return $this->send(array('result' => 'success', 'projects' => $result));
    }

    /**
     * Get the lines of the program it belongs to.
     *
     * @param  int   $programID
     * @access public
     * @return void
     */
    public function ajaxGetLinesPairsByProgram($programID = 0)
    {
        $lines = $this->loadModel('product')->getLinePairs((int)$programID);

        $result = array();
        foreach($lines as $lineID => $lineName)
        {
            $result[] = array('text' => $lineName, 'value' => $lineID);
        }

        return $this->send(array('result' => 'success', 'lines' => $result));
    }

    /**
     * After execute.
     *
     * @param  string $fromVersion
     * @param  string $processed
     * @param  string $skipMoveFile
     * @access public
     * @return void
     */
    public function afterExec($fromVersion, $processed = 'no', $skipMoveFile = 'no')
    {
        $alterSQL = $this->upgrade->checkConsistency($this->config->version);
        if(!empty($alterSQL))
        {
            $logFile  = $this->upgrade->getConsistencyLogFile();
            $hasError = $this->upgrade->hasConsistencyError();
            if(file_exists($logFile)) unlink($logFile);

            $this->view->title    = $this->lang->upgrade->consistency;
            $this->view->hasError = $hasError;
            $this->view->alterSQL = $alterSQL;
            $this->view->version  = $this->config->version;
            return $this->display('upgrade', 'consistency');
        }

        $extFiles = $this->upgrade->getExtFiles();
        if(!empty($extFiles) and $skipMoveFile == 'no') $this->locate(inlink('moveExtFiles', "fromVersion={$fromVersion}"));

        $response = $this->upgrade->removeEncryptedDir();
        if($response['result'] == 'fail')
        {
            $this->view->title  = $this->lang->upgrade->common;
            $this->view->errors = $response['command'];
            $this->view->result = 'fail';

            return $this->display('upgrade', 'execute');
        }

        unset($_SESSION['user']);

        if($processed == 'no')
        {
            $this->app->loadLang('install');
            $this->view->title      = $this->lang->upgrade->result;

            $needProcess = $this->upgrade->checkProcess();
            $this->view->needProcess = $needProcess;
            $this->view->fromVersion = $fromVersion;
            $this->display();
        }
        if(empty($needProcess) or $processed == 'yes')
        {
            $this->loadModel('setting')->updateVersion($this->config->version);

            $zfile = $this->app->loadClass('zfile');
            $zfile->removeDir($this->app->getTmpRoot() . 'model/');

            $installFile = $this->app->getAppRoot() . 'www/install.php';
            $upgradeFile = $this->app->getAppRoot() . 'www/upgrade.php';
            if(file_exists($installFile)) @unlink($installFile);
            if(file_exists($upgradeFile)) @unlink($upgradeFile);
            unset($_SESSION['upgrading']);
        }
    }

    /**
     * 数据库一致性检查。
     * Check database consistency.
     *
     * @param  bool   $netConnect
     * @access public
     * @return void
     */
    public function consistency(bool $netConnect = true)
    {
        $logFile  = $this->upgrade->getConsistencyLogFile();
        $hasError = $this->upgrade->hasConsistencyError();
        if(file_exists($logFile)) unlink($logFile);

        $alterSQL = $this->upgrade->checkConsistency();
        if(empty($alterSQL))
        {
            /* 能访问禅道官网插件接口跳转到检查插件页面，否则跳转到选择版本页面。*/
            /* If you can access the ZenTao official website extension interface, locate to the check extension page, otherwise locate to the version selection page. */
            if(!$netConnect) $this->locate(inlink('selectVersion'));
            $this->locate(inlink('checkExtension'));
        }

        $this->view->title    = $this->lang->upgrade->consistency;
        $this->view->hasError = $hasError;
        $this->view->alterSQL = $alterSQL;
        $this->view->version  = $this->config->installedVersion;
        $this->display();
    }

    /**
     * Check extension.
     *
     * @access public
     * @return void
     */
    public function checkExtension()
    {
        $this->loadModel('extension');
        $extensions = $this->extension->getLocalExtensions('installed');
        if(empty($extensions)) $this->locate(inlink('selectVersion'));

        $versions = array();
        foreach($extensions as $code => $extension) $versions[$code] = $extension->version;

        $incompatibleExts = $this->extension->checkIncompatible($versions);
        $extensionsName   = array();
        if(empty($incompatibleExts)) $this->locate(inlink('selectVersion'));

        $removeCommands = array();
        foreach($incompatibleExts as $extension)
        {
            $this->extension->updateExtension(array('code' => $extension, 'status' => 'deactivated'));
            $removeCommands[$extension] = $this->extension->removePackage($extension);
            $extensionsName[$extension] = $extensions[$extension]->name;
        }

        $this->view->title          = $this->lang->upgrade->checkExtension;
        $this->view->extensionsName = $extensionsName;
        $this->view->removeCommands = $removeCommands;
        $this->display();
    }

    /**
     * Ajax update file.
     *
     * @param  string $type
     * @param  int    $lastID
     * @access public
     * @return void
     */
    public function ajaxUpdateFile($type = '', $lastID = 0)
    {
        set_time_limit(0);
        $this->app->loadLang('search');
        $result = $this->upgrade->updateFileObjectID($type, $lastID);
        $response = array();
        if($result['type'] == 'finish')
        {
            $response['result']  = 'finished';
            $response['type']    = $type;
            $response['count']   = $result['count'];
            $response['message'] = $this->lang->search->buildSuccessfully;
        }
        else
        {
            $response['result']   = 'continue';
            $response['next']     = inlink('ajaxUpdateFile', "type={$result['type']}&lastID={$result['lastID']}");
            $response['count']    = $result['count'];
            $response['type']     = $type;
            $response['nextType'] = $result['type'];
            $response['message']  = zget($this->lang->searchObjects, $result['type']) . " <span class='{$result['type']}-num'>0</span>";
        }
        echo json_encode($response);
    }

    /**
     * Ajax get product name.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function ajaxGetProductName($productID)
    {
        echo $this->dao->findByID($productID)->from(TABLE_PRODUCT)->fetch('name');
    }

    /**
     * Ajax get program status.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function ajaxGetProgramStatus($programID)
    {
        echo $this->dao->select('status')->from(TABLE_PROGRAM)->where('id')->eq($programID)->fetch('status');
    }

    /**
     * 迁移扩展文件。
     * Move Extent files.
     *
     * @param  string $fromVersion
     * @access public
     * @return void
     */
    public function moveExtFiles(string $fromVersion)
    {
        $command = '';
        $result  = 'success';
        if(strtolower($this->server->request_method) == 'post')
        {
            if(!empty($_POST['files']))
            {
                $response = $this->upgrade->moveExtFiles();
                $result   = $response['result'];
                if($result == 'fail') $command = $response['command'];
            }

            if($result == 'success') $this->locate(inlink('afterExec', "fromVersion={$fromVersion}&processed=no&skipMoveFile=yes"));
        }

        $this->view->title       = $this->lang->upgrade->common;
        $this->view->files       = $this->upgrade->getExtFiles();
        $this->view->result      = $result;
        $this->view->command     = $command;
        $this->view->fromVersion = $fromVersion;

        $this->display();
    }

    /**
     * Process old metrics in order to easy of test.
     *
     * @param  bool $isDelete
     * @access public
     * @return void
     */
    public function processOldMetrics($isDelete = false)
    {
        if($isDelete)
        {
            $this->dao->delete()->from(TABLE_METRIC)->where('fromID')->ne(0)->exec();
        }
        else
        {
            $this->upgrade->processOldMetrics();
        }
        echo 'ok';
    }

    public function processHistoryDataForMetric()
    {
        $this->upgrade->processHistoryDataForMetric();
        echo 'ok';
    }
}
