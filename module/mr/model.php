<?php
declare(strict_types=1);
/**
 * The model file of mr module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      dingguodong <dingguodong@easycorp.ltd>
 * @package     mr
 * @link        https://www.zentao.net
 */
class mrModel extends model
{
    /**
     * 获取合并请求列表.
     * Get MR list of gitlab project.
     *
     * @param  string $mode
     * @param  string $param
     * @param  string $orderBy
     * @param  array  $filterProjects
     * @param  int    $repoID
     * @param  int    $objectID
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getList(string $mode = 'all', string $param = 'all', string $orderBy = 'id_desc', array $filterProjects = array(), int $repoID = 0, int $objectID = 0, object $pager = null): array
    {
        $filterProjectSql = '';
        if(!$this->app->user->admin && !empty($filterProjects))
        {
            foreach($filterProjects as $hostID => $projectID)
            {
                $filterProjectSql .= "(hostID = {$hostID} and sourceProject = {$projectID}) or ";
            }

            if($filterProjectSql) $filterProjectSql = '(' . substr($filterProjectSql, 0, -3) . ')'; // Remove last or.
        }

        return $this->dao->select('*')->from(TABLE_MR)
            ->where('deleted')->eq('0')
            ->beginIF($mode == 'status' && $param != 'all')->andWhere('status')->eq($param)->fi()
            ->beginIF($mode == 'assignee' && $param != 'all')->andWhere('assignee')->eq($param)->fi()
            ->beginIF($mode == 'creator' && $param != 'all')->andWhere('createdBy')->eq($param)->fi()
            ->beginIF($filterProjectSql)->andWhere($filterProjectSql)->fi()
            ->beginIF($repoID)->andWhere('repoID')->eq($repoID)->fi()
            ->beginIF($objectID)->andWhere('executionID')->eq($objectID)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 根据代码库ID获取合并请求列表.
     * Get MR list by repoID.
     *
     * @access public
     * @return array
     */
    public function getPairs(int $repoID): array
    {
        return $this->dao->select('id,title')
            ->from(TABLE_MR)
            ->where('deleted')->eq('0')
            ->andWhere('repoID')->eq($repoID)
            ->orderBy('id')
            ->fetchPairs('id', 'title');
    }

    /**
     * 获取Gitea服务器的项目.
     * Get gitea projects.
     *
     * @param  int    $hostID
     * @access public
     * @return array
     */
    public function getGiteaProjects(int $hostID = 0): array
    {
        $projects = $this->loadModel('gitea')->apiGetProjects($hostID);
        return array($hostID => helper::arrayColumn($projects, null, 'full_name'));
    }

    /**
     * 获取Gogs服务器的项目.
     * Get gogs projects.
     *
     * @param  int    $hostID
     * @access public
     * @return array
     */
    public function getGogsProjects(int $hostID = 0): array
    {
        $projects = $this->loadModel('gogs')->apiGetProjects($hostID);
        return array($hostID => helper::arrayColumn($projects, null, 'full_name'));
    }

    /**
     * 获取Gitlab服务器的项目.
     * Get gitlab projects.
     *
     * @param  int    $hostID
     * @param  array  $projectIdList
     * @access public
     * @return array
     */
    public function getGitlabProjects(int $hostID = 0, array $projectIdList = array()): array
    {
        $gitlabUsers = $this->loadModel('pipeline')->getProviderPairsByAccount('gitlab');
        if(!$this->app->user->admin && !isset($gitlabUsers[$hostID])) return array();

        $minProject = $maxProject = 0;
        /* Mysql string to int. */
        $MR = $this->dao->select('min(sourceProject + 0) as minSource, MAX(sourceProject + 0) as maxSource,MIN(targetProject) as minTarget,MAX(targetProject) as maxTarget')->from(TABLE_MR)
            ->where('deleted')->eq('0')
            ->andWhere('hostID')->eq($hostID)
            ->fetch();
        if($MR)
        {
            $minProject = min($MR->minSource, $MR->minTarget);
            $maxProject = max($MR->maxSource, $MR->maxTarget);
        }

        /* If not an administrator, need to obtain group member information. */
        $groupIDList = array(0 => 0);
        $this->loadModel('gitlab');
        if(!$this->app->user->admin)
        {
            $groups = $this->gitlab->apiGetGroups($hostID, 'name_asc', 'reporter');
            foreach($groups as $group) $groupIDList[] = $group->id;
        }

        $allProjectPairs = array();
        $allProjects     = $this->gitlab->apiGetProjects($hostID, 'false', (int)$minProject, (int)$maxProject);
        foreach($allProjects as $project)
        {
            if($projectIdList && !in_array($project->id, $projectIdList)) continue;
            if(!$this->gitlab->checkUserAccess($hostID, 0, $project, $groupIDList, 'reporter')) continue;

            $project->isDeveloper = $this->gitlab->checkUserAccess($hostID, 0, $project, $groupIDList, 'developer');
            $allProjectPairs[$hostID][$project->id] = $project;
        }

        return $allProjectPairs;
    }

    /**
     * 创建本地合并请求。
     * Create a local merge request.
     *
     * @param  object $MR
     * @access public
     * @return bool
     */
    public function createMR(object $MR): bool
    {
        /* Exec Job */
        if(isset($MR->jobID) && $MR->jobID)
        {
            $pipeline = $this->loadModel('job')->exec($MR->jobID);
            if(!empty($pipeline->queue))
            {
                $compile = $this->loadModel('compile')->getByQueue((int)$pipeline->queue);
                $MR->compileID     = $compile->id;
                $MR->compileStatus = $compile->status;
            }
        }

        return $this->mrTao->insertMr($MR);
    }

    /**
     * 创建合并请求。
     * Create MR function.
     *
     * @param  object $MR
     * @access public
     * @return array
     */
    public function create(object $MR): array
    {
        $result = $this->checkSameOpened($MR->hostID, $MR->sourceProject, $MR->sourceBranch, $MR->targetProject, $MR->targetBranch);
        if($result['result'] == 'fail') return $result;

        $this->createMR($MR);
        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        $MRID = $this->dao->lastInsertID();
        $this->loadModel('action')->create('mr', $MRID, 'opened');

        $rawMR = $this->apiCreateMR($MR->hostID, $MR->sourceProject, $MR);

        /**
         * Another open merge request already exists for this source branch.
         * The type of variable `$rawMR->message` is array.
         */
        if(isset($rawMR->message) and !isset($rawMR->iid))
        {
            $this->dao->delete()->from(TABLE_MR)->where('id')->eq($MRID)->exec();

            $errorMessage = $this->convertApiError($rawMR->message);
            return array('result' => 'fail', 'message' => sprintf($this->lang->mr->apiError->createMR, $errorMessage));
        }

        /* Create MR failed. */
        if(!isset($rawMR->iid))
        {
            $this->dao->delete()->from(TABLE_MR)->where('id')->eq($MRID)->exec();
            return array('result' => 'fail', 'message' => $this->lang->mr->createFailedFromAPI);
        }

        /* Create a todo item for this MR. */
        if(empty($MR->jobID)) $this->apiCreateMRTodo($MR->hostID, $MR->targetProject, $rawMR->iid);

        $newMR = new stdclass;
        $newMR->mriid       = $rawMR->iid;
        $newMR->status      = $rawMR->state;
        $newMR->mergeStatus = $rawMR->merge_status;

        /* Update MR in Zentao database. */
        $this->dao->update(TABLE_MR)->data($newMR)->where('id')->eq($MRID)->autoCheck()->exec();

        /* Link stories,bugs and tasks. */
        $MR->id    = $MRID;
        $MR->mriid = $newMR->mriid;
        $this->linkObjects($MR);

        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());
        $linkParams = $this->app->tab == 'execution' ? "repoID=0&mode=status&param=opened&objectID={$MR->executionID}" : "repoID={$MR->repoID}";
        return array('result' => 'success', 'message' => $this->lang->saveSuccess, 'load' => helper::createLink('mr', 'browse', $linkParams));
    }

    /**
     * 创建合并请求后的操作。
     * Create MR after operation.
     *
     * @param  int    $MRID
     * @param  object $MR
     * @access public
     * @return bool
     */
    public function afterApiCreate(int $MRID, object $MR): bool
    {
        if($MR->hasNoConflict == '0' && $MR->mergeStatus == 'can_be_merged' && $MR->jobID)
        {
            $pipeline = $this->loadModel('job')->exec($MR->jobID, array('sourceBranch' => $MR->sourceBranch, 'targetBranch' => $MR->targetBranch));
            if(!$pipeline) return false;

            $newMR = new stdClass();
            if(!empty($pipeline->queue))
            {
                $compile = $this->loadModel('compile')->getByQueue((int)$pipeline->queue);
                $newMR->compileID     = $compile->id;
                $newMR->compileStatus = $compile->status;
                if($newMR->compileStatus == 'failure') $newMR->status = 'closed';
            }
            else
            {
                $newMR->compileStatus = $pipeline->status;
                if($newMR->compileStatus == 'create_fail') $newMR->status = 'closed';
            }

            /* Update MR in Zentao database. */
            $this->dao->update(TABLE_MR)->data($newMR)->where('id')->eq($MRID)->autoCheck()->exec();
            if(dao::isError()) return false;
        }
        return true;
    }

    /**
     * 通过API创建合并请求。
     * Create MR function by api.
     *
     * @access public
     * @return bool|int
     */
    public function apiCreate(): int|false
    {
        $postData = fixer::input('post')->get();
        $repo     = $this->dao->findByID($postData->repoID)->from(TABLE_REPO)->fetch();
        if(empty($repo))
        {
            dao::$errors[] = 'No matched gitlab.';
            return false;
        }

        /* Process and insert mr data. */
        $MR = new stdClass();
        $MR->hostID         = (int)$repo->serviceHost;
        $MR->sourceProject  = $repo->serviceProject;
        $MR->sourceBranch   = $postData->sourceBranch;
        $MR->targetProject  = $repo->serviceProject;
        $MR->targetBranch   = $postData->targetBranch;
        $MR->diffs          = $postData->diffs;
        $MR->title          = $this->lang->mr->common . ' ' . $postData->sourceBranch . $this->lang->mr->to . $postData->targetBranch ;
        $MR->repoID         = $repo->id;
        $MR->jobID          = $postData->jobID;
        $MR->status         = 'opened';
        $MR->synced         = '0';
        $MR->needCI         = '1';
        $MR->hasNoConflict  = $postData->mergeStatus ? '0' : '1';
        $MR->mergeStatus    = $postData->mergeStatus ? 'can_be_merged' : 'cannot_be_merged';
        $MR->createdBy      = $this->app->user->account;
        $MR->createdDate    = date('Y-m-d H:i:s');

        if($MR->sourceProject == $MR->targetProject && $MR->sourceBranch == $MR->targetBranch)
        {
            dao::$errors[] = $this->lang->mr->errorLang[1];
            return false;
        }

        $result = $this->checkSameOpened($MR->hostID, (string)$MR->sourceProject, $MR->sourceBranch, (string)$MR->targetProject, $MR->targetBranch);
        if($result['result'] == 'fail')
        {
            dao::$errors[] = $result['message'];
            return false;
        }

        $this->mrTao->insertMr($MR);
        if(dao::isError()) return false;

        $MRID = $this->dao->lastInsertID();
        $this->loadModel('action')->create('mr', $MRID, 'opened');

        /* Exec Job */
        $this->afterApiCreate($MRID, $MR);
        return $MRID;
    }

    /**
     * 更新合并请求。
     * Edit MR function.
     *
     * @param  int    $MRID
     * @param  object $MR
     * @access public
     * @return array
     */
    public function update(int $MRID, object $MR): array
    {
        $oldMR = $this->fetchByID($MRID);
        if(!$oldMR) return array('result' => 'fail', 'message' => $this->lang->mr->notFound);

        $this->dao->update(TABLE_MR)->data($MR)->checkIF($MR->needCI, 'jobID',  'notempty');
        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        /* Exec Job */
        if(isset($MR->jobID) && $MR->jobID)
        {
            $pipeline = $this->loadModel('job')->exec($MR->jobID);
            if(!empty($pipeline->queue))
            {
                $compile = $this->loadModel('compile')->getByQueue($pipeline->queue);
                $MR->compileID     = $compile->id;
                $MR->compileStatus = $compile->status;
            }
        }

        /* Known issue: `reviewer_ids` takes no effect. */
        $rawMR = $this->apiUpdateMR($oldMR, $MR);
        if(!isset($rawMR->id) and isset($rawMR->message))
        {
            $errorMessage = $this->convertApiError($rawMR->message);
            return array('result' => 'fail', 'message' => $errorMessage);
        }

        /* Update MR in Zentao database. */
        $this->dao->update(TABLE_MR)->data($MR, $this->config->mr->edit->skippedFields)
            ->where('id')->eq($MRID)
            ->batchCheck($this->config->mr->edit->requiredFields, 'notempty')
            ->autoCheck()
            ->exec();
        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        $MR = $this->fetchByID($MRID);
        $this->linkObjects($MR);

        $actionID = $this->loadModel('action')->create('mr', $MRID, 'edited');
        $changes  = common::createChanges($oldMR, $MR);
        if(!empty($changes)) $this->action->logHistory($actionID, $changes);
        $this->createMRLinkedAction($MRID, 'editmr', $MR->editedDate);

        if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

        $linkParams = $this->app->tab == 'execution' ? "repoID=0&mode=status&param=opened&objectID={$MR->executionID}" : "repoID={$MR->repoID}";
        return array('result' => 'success', 'message' => $this->lang->saveSuccess, 'load' => helper::createLink('mr', 'browse', $linkParams));
    }

    /**
     * 更新合并请求关联信息。
     * Update MR linked info.
     *
     * @param  int    $MRID
     * @param  string $action  createmr|editmr|removemr
     * @param  string $actionDate
     * @access public
     * @return bool
     */
    public function createMRLinkedAction(int $MRID, string $action, string $actionDate = ''): bool
    {
        if(empty($actionDate)) $actionDate = helper::now();

        $MRAction = $actionDate . '::' . $this->app->user->account . '::' . helper::createLink('mr', 'view', "mr={$MRID}");

        $this->loadModel('action');
        foreach(array('story', 'task', 'bug') as $objectType)
        {
            $linkedObjects = $this->mrTao->getLinkedObjectPairs($MRID, $objectType);
            foreach($linkedObjects as $objectID) $this->action->create($objectType, $objectID, $action, '', $MRAction);
        }
        return !dao::isError();
    }

    /**
     * 通过API同步合并请求。
     * Sync MR from GitLab API to Zentao database.
     *
     * @param  object  $MR
     * @access public
     * @return object|false
     */
    public function apiSyncMR(object $MR): object|false
    {
        $rawMR = $this->apiGetSingleMR($MR->hostID, $MR->targetProject, $MR->mriid);
        /* Sync MR in ZenTao database whatever status of MR in GitLab. */
        if(isset($rawMR->iid))
        {
            $newMR    = new stdclass();
            $gitUsers = $this->loadModel('pipeline')->getUserBindedPairs($MR->hostID, $rawMR->gitService, 'openID,account');
            foreach($this->config->mr->maps->sync as $syncField => $config)
            {
                $value = '';
                list($field, $optionType, $options) = explode('|', $config);

                if($optionType == 'field') $value = $rawMR->$field;
                if($optionType == 'userPairs')
                {
                    $gitUserID = '';
                    if(isset($rawMR->$field))
                    {
                        $values = $rawMR->$field;
                        if(isset($values[0])) $gitUserID = $values[0]->$options;
                    }
                    $value = zget($gitUsers, $gitUserID, '');
                }

                if($value) $newMR->$syncField = $value;
            }

            /* For compatibility with PHP 5.4 . */
            $condition = (array)$newMR;
            if(empty($condition)) return false;

            /* Update compile status of current MR object */
            if(isset($MR->needCI) && $MR->needCI == '1')
            {
                $compile = $this->loadModel('compile')->getByID($MR->id);
                $newMR->compileStatus = $compile ? $compile->status : 'failed';
            }

            /* Update MR in Zentao database. */
            $newMR->editedBy   = $this->app->user->account;
            $newMR->editedDate = helper::now();
            $this->dao->update(TABLE_MR)->data($newMR)
                ->where('id')->eq($MR->id)
                ->exec();
        }
        return $this->fetchByID($MR->id);
    }

    /**
     * 通过API批量同步合并请求。
     * Batch Sync GitLab MR Database.
     *
     * @param  array  $MRList
     * @access public
     * @return array
     */
    public function batchSyncMR(array $MRList): array
    {
        if(empty($MRList)) return array();

        foreach($MRList as $key => $MR)
        {
            if($MR->status != 'opened') continue;
            $MRList[$key] = $this->apiSyncMR($MR);
        }

        return $MRList;
    }

    /**
     * 创建远程合并请求。
     * Create MR by API.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  object $MR
     * @access public
     * @return object|null
     */
    public function apiCreateMR(int $hostID, string $projectID, object $MR): object|null
    {
        $host = $this->loadModel('pipeline')->getByID($hostID);
        if(!$host) return null;

        if($MR->assignee) $assignee = $this->pipeline->getOpenIdByAccount($hostID, $host->type, $MR->assignee);

        $MRObject = new stdclass();
        $MRObject->title = $MR->title;
        if($host->type == 'gitlab')
        {
            $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests");

            $MRObject->target_project_id    = $MR->targetProject;
            $MRObject->source_branch        = $MR->sourceBranch;
            $MRObject->target_branch        = $MR->targetBranch;
            $MRObject->description          = $MR->description;
            $MRObject->remove_source_branch = $MR->removeSourceBranch == '1' ? true : false;
            $MRObject->squash               = $MR->squash == '1' ? 1 : 0;
            if(!empty($assignee)) $MRObject->assignee_ids = $assignee;
            return json_decode(commonModel::http($url, $MRObject));
        }
        elseif(in_array($host->type, array('gitea', 'gogs')))
        {
            $url = sprintf($this->loadModel($host->type)->getApiRoot($hostID), "/repos/$projectID/pulls");

            $MRObject->head = $MR->sourceBranch;
            $MRObject->base = $MR->targetBranch;
            $MRObject->body = $MR->description;
            if(!empty($assignee)) $MRObject->assignee = $assignee;

            $mergeResult = json_decode(commonModel::http($url, $MRObject));
            if(isset($mergeResult->number)) $mergeResult->iid = $mergeResult->number;
            if(isset($mergeResult->mergeable))
            {
                if($mergeResult->mergeable) $mergeResult->merge_status = 'can_be_merged';
                if(!$mergeResult->mergeable) $mergeResult->merge_status = 'cannot_be_merged';
            }
            if(isset($mergeResult->state) and $mergeResult->state == 'open') $mergeResult->state = 'opened';
            if(isset($mergeResult->merged) and $mergeResult->merged) $mergeResult->state = 'merged';
            return $mergeResult;
        }
    }

    /**
     * 通过API查看是否有相同的合并请求。
     * Get same opened mr by api.
     *
     * @param  int    $hostID
     * @param  string $sourceProject
     * @param  string $sourceBranch
     * @param  string $targetProject
     * @param  string $targetBranch
     * @access public
     * @return object|null
     */
    public function apiGetSameOpened(int $hostID, string $sourceProject, string $sourceBranch, string $targetProject, string $targetBranch): object|null
    {
        if(empty($hostID) || empty($sourceProject) || empty($sourceBranch) ||  empty($targetProject) || empty($targetBranch)) return null;

        $url      = sprintf($this->loadModel('gitlab')->getApiRoot((int)$hostID), "/projects/{$sourceProject}/merge_requests") . "&state=opened&source_branch={$sourceBranch}&target_branch={$targetBranch}";
        $response = json_decode(commonModel::http($url));
        if($response)
        {
            foreach($response as $MR)
            {
                if(empty($MR->source_project_id) || empty($MR->target_project_id)) return null;
                if($MR->source_project_id == $sourceProject && $MR->target_project_id == $targetProject) return $MR;
            }
        }
        return null;
    }

    /**
     * 通过API获取单个合并请求。
     * Get single MR by API.
     *
     * @param  int    $hostID
     * @param  string $projectID  targetProject
     * @param  int    $MRID
     * @access public
     * @return object|null
     */
    public function apiGetSingleMR(int $hostID, string $projectID, int $MRID): object|null
    {
        $host = $this->loadModel('pipeline')->getByID($hostID);
        if(!$host) return null;

        if($host->type == 'gitlab')
        {
            $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID, false), "/projects/$projectID/merge_requests/$MRID");
            $MR  = json_decode(commonModel::http($url));
        }
        else
        {
            $url = sprintf($this->loadModel($host->type)->getApiRoot($hostID), "/repos/$projectID/pulls/$MRID");
            $MR  = json_decode(commonModel::http($url));
            if(!$MR || isset($MR->message) || isset($MR->errors)) return null;

            if(isset($MR->url) || isset($MR->html_url))
            {
                $diff = $this->apiGetDiffs($hostID, $projectID, $MRID);

                $MR->web_url = $host->type == 'gitea' ? $MR->url : $MR->html_url;
                $MR->iid     = $host->type == 'gitea' ? $MR->number : $MR->id;
                $MR->state   = $MR->state == 'open' ? 'opened' : $MR->state;
                if($MR->merged) $MR->state = 'merged';

                $MR->merge_status      = $MR->mergeable ? 'can_be_merged' : 'cannot_be_merged';
                $MR->changes_count     = empty($diff) ? 0 : 1;
                $MR->description       = $MR->body;
                $MR->target_branch     = $host->type == 'gitea' ? $MR->base->ref : $MR->base_branch;
                $MR->source_branch     = $host->type == 'gitea' ? $MR->head->ref : $MR->head_branch;
                $MR->source_project_id = $projectID;
                $MR->target_project_id = $projectID;
                $MR->has_conflicts     = empty($diff) ? true : false;
            }
        }

        if($MR) $MR->gitService = $host->type;
        return $MR;
    }

    /**
     * 通过API获取合并请求的提交信息。
     * Get MR commits by API.
     *
     * @param  int    $hostID
     * @param  string $projectID  targetProject
     * @param  int    $MRID
     * @access public
     * @return array|null
     */
    public function apiGetMRCommits(int $hostID, string $projectID, int $MRID): array|null
    {
        $host = $this->loadModel('pipeline')->getByID($hostID);
        if($host->type == 'gitlab')
        {
            $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests/$MRID/commits");
        }
        else
        {
            $url = sprintf($this->loadModel($host->type)->getApiRoot($hostID), "/repos/$projectID/pulls/$MRID/commits");
        }

        return (array)json_decode(commonModel::http($url));
    }

    /**
     * 通过API更新合并请求。
     * Update MR by API.
     *
     * @param  object $oldMR
     * @param  object $MR
     * @access public
     * @return object|null
     */
    public function apiUpdateMR(object $oldMR, object $MR): object|null
    {
        $host = $this->loadModel('pipeline')->getByID($oldMR->hostID);
        if(!$host) return null;

        if(!empty($MR->assignee)) $assignee = $this->pipeline->getOpenIdByAccount($host->id, $host->type, $MR->assignee);

        $MRObject = new stdclass();
        $MRObject->title = $MR->title;
        if($host->type == 'gitlab')
        {
            if(isset($MR->targetBranch))         $MRObject->target_branch        = zget($MR, 'targetBranch', $oldMR->targetBranch);
            if(isset($MR->description))          $MRObject->description          = $MR->description;
            if(isset($MR->remove_source_branch)) $MRObject->remove_source_branch = $MR->removeSourceBranch == '1' ? true : false;
            if(isset($MR->squash))               $MRObject->squash               = $MR->squash == '1' ? 1 : 0;
            if(!empty($assignee))                $MRObject->assignee_ids         = $assignee;

            $url = sprintf($this->loadModel('gitlab')->getApiRoot($host->id), "/projects/{$oldMR->sourceProject}/merge_requests/{$oldMR->id}");
            return json_decode(commonModel::http($url, $MRObject, array(CURLOPT_CUSTOMREQUEST => 'PUT')));
        }
        else
        {
            if(isset($MR->targetBranch)) $MRObject->base     = zget($MR, 'targetBranch', $oldMR->targetBranch);
            if(isset($MR->description))  $MRObject->body     = $MR->description;
            if(!empty($assignee))        $MRObject->assignee = $assignee;

            $url = sprintf($this->loadModel($host->type)->getApiRoot($host->id), "/repos/{$oldMR->sourceProject}/pulls/{$oldMR->id}");
            $mergeResult = json_decode(commonModel::http($url, $MRObject, array(), array(), 'json', 'PATCH'));

            if(isset($mergeResult->number)) $mergeResult->iid = $host->type == 'gitea' ? $mergeResult->number : $mergeResult->id;
            if(isset($mergeResult->mergeable))
            {
                if($mergeResult->mergeable)  $mergeResult->merge_status = 'can_be_merged';
                if(!$mergeResult->mergeable) $mergeResult->merge_status = 'cannot_be_merged';
            }
            if(isset($mergeResult->state) && $mergeResult->state == 'open') $mergeResult->state = 'opened';
            if(isset($mergeResult->merged) && $mergeResult->merged)         $mergeResult->state = 'merged';
            return $mergeResult;
        }
    }

    /**
     * 通过API删除合并请求。
     * Delete MR by API.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  int    $MRID
     * @access public
     * @return object|null
     */
    public function apiDeleteMR(int $hostID, string $projectID, int $MRID): object|null
    {
        $host = $this->loadModel('pipeline')->getByID($hostID);
        if(!$host) return null;

        if($host->type == 'gitlab')
        {
            $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests/$MRID");
            return json_decode(commonModel::http($url, null, array(CURLOPT_CUSTOMREQUEST => 'DELETE')));
        }

        $rowMR = $this->apiGetSingleMR($hostID, $projectID, $MRID);
        if($rowMR->state == 'opened')
        {
            $url = sprintf($this->loadModel($host->type)->getApiRoot($hostID), "/repos/$projectID/pulls/$MRID");
            return json_decode(commonModel::http($url, array('state' => 'closed'), array(), array(), 'json', 'PATCH'));
        }

        return null;
    }

    /**
     * 通过API关闭合并请求。
     * Close MR by API.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  int    $MRID
     * @access public
     * @return object|null
     */
    public function apiCloseMR(int $hostID, string $projectID, int $MRID): object|null
    {
        $host = $this->loadModel('pipeline')->getByID($hostID);
        if(!$host) return null;

        if($host->type == 'gitlab')
        {
            $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests/$MRID") . '&state_event=close';
            return json_decode(commonModel::http($url, null, array(CURLOPT_CUSTOMREQUEST => 'PUT')));
        }
        else
        {
            $url = sprintf($this->loadModel($host->type)->getApiRoot($hostID), "/repos/$projectID/pulls/$MRID");
            return json_decode(commonModel::http($url, array('state' => 'closed'), array(), array(), 'json', 'PATCH'));
        }
    }

    /**
     * 通过API重新打开合并请求。
     * Reopen MR by API.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  int    $MRID
     * @access public
     * @return object|null
     */
    public function apiReopenMR(int $hostID, string $projectID, int $MRID): object|null
    {
        $host = $this->loadModel('pipeline')->getByID($hostID);
        if(!$host) return null;

        if($host->type == 'gitlab')
        {
            $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests/$MRID") . '&state_event=reopen';
            return json_decode(commonModel::http($url, null, array(CURLOPT_CUSTOMREQUEST => 'PUT')));
        }
        else
        {
            $url = sprintf($this->loadModel($host->type)->getApiRoot($hostID), "/repos/$projectID/pulls/$MRID");
            $MR  = json_decode(commonModel::http($url, array('state' => 'open'), array(), array(), 'json', 'PATCH'));
            $MR->iid   = $host->type == 'gitea' ? $MR->number : $MR->id;
            $MR->state = $MR->state == 'open' ? 'opened' : $MR->state;
            if($MR->merged) $MR->state = 'merged';

            $MR->merge_status      = $MR->mergeable ? 'can_be_merged' : 'cannot_be_merged';
            $MR->description       = $MR->body;
            $MR->target_branch     = $host->type == 'gitea' ? $MR->base->ref : $MR->base_branch;
            $MR->source_branch     = $host->type == 'gitea' ? $MR->head->ref : $MR->head_branch;
            $MR->source_project_id = $projectID;
            $MR->target_project_id = $projectID;

            return $MR;
        }
    }

    /**
     * 通过API接受合并请求。
     * Accept MR by API.
     *
     * @param  object $MR
     * @access public
     * @return object|null
     */
    public function apiAcceptMR(object $MR): object|null
    {
        $host = $this->loadModel('pipeline')->getByID($MR->hostID);
        if(!$host) return null;

        $apiRoot = $this->loadModel($host->type)->getApiRoot($MR->hostID);
        if($host->type == 'gitlab')
        {
            $approveUrl = sprintf($apiRoot, "/projects/$MR->targetProject/merge_requests/$MR->mriid/approved");
            commonModel::http($approveUrl, null, array(CURLOPT_CUSTOMREQUEST => 'POST'));

            $url = sprintf($apiRoot, "/projects/$MR->targetProject/merge_requests/$MR->mriid/merge");
            return json_decode(commonModel::http($url, null, array(CURLOPT_CUSTOMREQUEST => 'PUT')));
        }
        else
        {
            $url   = sprintf($apiRoot, "/repos/$MR->targetProject/pulls/$MR->mriid/merge");
            $merge = $MR->squash == '1' ? 'squash' : 'merge';
            $data  = array('Do' => $merge);
            if($MR->removeSourceBranch == '1') $data['delete_branch_after_merge'] = true;

            $rowMR = json_decode(commonModel::http($url, $data, array(), array(), 'json', 'POST'));
            if(!isset($rowMR->massage))
            {
                $rowMR = $this->apiGetSingleMR($MR->hostID, $MR->targetProject, $MR->mriid);
                if($data['delete_branch_after_merge'] == true) $this->loadModel('gogs')->apiDeleteBranch($MR->hostID, $MR->targetProject, $MR->sourceBranch);
            }

            return $rowMR;
        }
    }

    /**
     * 获取合并请求的对比信息。
     * Get MR diff versions by API.
     *
     * @param  object $MR
     * @param  string $encoding
     * @access public
     * @return array
     */
    public function getDiffs(object $MR, string $encoding = ''): array
    {
        if(!isset($MR->repoID)) return array();

        $repo = $this->loadModel('repo')->getByID($MR->repoID);
        if(!$repo) return array();

        $host = $this->loadModel('pipeline')->getByID($MR->hostID);
        $repo->gitService = $host->id;
        $repo->project    = $MR->targetProject;
        $repo->password   = $host->token;
        $repo->account    = '';
        $repo->encoding   = $encoding;

        $lines = array();
        if($host->type == 'gitlab')
        {
            $diffVersions = array();
            if($MR->synced) $diffVersions = $this->apiGetDiffVersions($MR->hostID, $MR->targetProject, $MR->mriid);

            foreach($diffVersions as $diffVersion)
            {
                $singleDiff = $this->apiGetSingleDiffVersion($MR->hostID, $MR->targetProject, $MR->mriid, $diffVersion->id);
                if($singleDiff->state == 'empty') continue;

                $diffs = $singleDiff->diffs;
                foreach($diffs as $diff)
                {
                    $lines[] = sprintf("diff --git a/%s b/%s", $diff->old_path, $diff->new_path);
                    $lines[] = sprintf("index %s ... %s %s ", $singleDiff->head_commit_sha, $singleDiff->base_commit_sha, $diff->b_mode);
                    $lines[] = sprintf("--a/%s", $diff->old_path);
                    $lines[] = sprintf("--b/%s", $diff->new_path);
                    $diffLines = explode("\n", $diff->diff);
                    foreach($diffLines as $diffLine) $lines[] = $diffLine;
                }
            }
        }
        else
        {
            $diffs = $this->apiGetDiffs($MR->hostID, $MR->targetProject, $MR->mriid);
            $lines = explode("\n", $diffs);
        }

        if(empty($MR->synced))
        {
            $diffs = preg_replace('/^\s*$\n?\r?/m', '', $MR->diffs);
            $lines = explode("\n", $diffs);
        }

        $scm = $this->app->loadClass('scm');
        $scm->setEngine($repo);
        return $scm->engine->parseDiff($lines);
    }

    /**
     * 通过API创建合并请求待办。
     * Create a todo item for merge request.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  int    $MRID
     * @access public
     * @return object|null
     */
    public function apiCreateMRTodo(int $hostID, string $projectID, int $MRID): object|null
    {
        $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests/$MRID/todo");
        return json_decode(commonModel::http($url, null, array(CURLOPT_CUSTOMREQUEST => 'POST')));
    }

    /**
     * 通过API获取合并请求的对比版本信息。
     * Get diff versions of MR from GitLab API.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  int    $MRID
     * @access public
     * @return array|null
     */
    public function apiGetDiffVersions(int $hostID, string $projectID, int $MRID): array|null
    {
        $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests/$MRID/versions");
        return json_decode(commonModel::http($url));
    }

    /**
     * 通过API获取合并请求的单个对比版本信息。
     * Get a single diff version of MR from GitLab API.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  int    $MRID
     * @param  int    $versionID
     * @access public
     * @return object|null
     */
    public function apiGetSingleDiffVersion(int $hostID, string $projectID, int $MRID, int $versionID): object|null
    {
        $url = sprintf($this->loadModel('gitlab')->getApiRoot($hostID), "/projects/$projectID/merge_requests/$MRID/versions/$versionID");
        return json_decode(commonModel::http($url));
    }

    /**
     * 通过Gitea API获取合并请求的对比信息。
     * Get diff of MR from Gitea API.
     *
     * @param  int    $hostID
     * @param  string $projectID
     * @param  int    $MRID
     * @access public
     * @return string
     */
    public function apiGetDiffs(int $hostID, string $projectID, int $MRID): string
    {
        $url = sprintf($this->loadModel('gitea')->getApiRoot($hostID), "/repos/$projectID/pulls/$MRID.diff");
        return commonModel::http($url);
    }

    /**
     * 审核合并请求。
     * Reject or Approve this MR.
     *
     * @param  object $MR
     * @param  string $action  approve|reject
     * @param  string $comment
     * @return array
     */
    public function approve(object $MR, string $action = 'approve', string $comment = ''): array
    {
        $actionID = $this->loadModel('action')->create('mr', $MR->id, $action);

        if(isset($MR->status) && $MR->status == 'opened')
        {
            $oldMR = clone $MR;
            $rawApprovalStatus = zget($MR, 'approvalStatus', '');
            if($action == 'reject'  && $rawApprovalStatus != 'rejected') $MR->approvalStatus = 'rejected';
            if($action == 'approve' && $rawApprovalStatus != 'approved') $MR->approvalStatus = 'approved';
            if(isset($MR->approvalStatus) && $rawApprovalStatus != $MR->approvalStatus)
            {
                $changes = common::createChanges($oldMR, $MR);
                $this->action->logHistory($actionID, $changes);

                unset($MR->editedDate);
                $MR->approver = $this->app->user->account;
                $this->dao->update(TABLE_MR)->data($MR)
                    ->where('id')->eq($MR->id)
                    ->exec();
                if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

                /* Save approval history into db. */
                $approval = new stdClass;
                $approval->date    = helper::now();
                $approval->mrID    = $MR->id;
                $approval->account = $MR->approver;
                $approval->action  = $action;
                $approval->comment = $comment;
                $this->dao->insert(TABLE_MRAPPROVAL)->data($approval, $this->config->mrapproval->create->skippedFields)
                    ->batchCheck($this->config->mrapproval->create->requiredFields, 'notempty')
                    ->autoCheck()
                    ->exec();
                if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());

                return array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true, 'load' => true);
            }
        }
        return array('result' => 'fail', 'message' => $this->lang->mr->repeatedOperation, 'load' => helper::createLink('mr', 'view', "mr={$MR->id}"));
    }

    /**
     * 关闭合并请求。
     * Close this MR.
     *
     * @param  object $MR
     * @access public
     * @return array
     */
    public function close(object $MR): array
    {
        $link = helper::createLink('mr', 'view', "mr={$MR->id}");
        if($MR->status == 'closed') return array('result' => 'fail', 'message' => $this->lang->mr->repeatedOperation, 'load' => $link);

        $actionID = $this->loadModel('action')->create('mr', $MR->id, 'closed');
        $rawMR    = $this->apiCloseMR($MR->hostID, $MR->targetProject, $MR->mriid);
        $changes  = common::createChanges($MR, $rawMR);
        $this->action->logHistory($actionID, $changes);

        if(isset($rawMR->state) && $rawMR->state == 'closed') return array('result' => 'success', 'message' => $this->lang->mr->closeSuccess, 'load' => $link);
        return array('result' => 'fail', 'message' => $this->lang->fail, 'load' => $link);
    }

    /**
     * 重新打开合并请求。
     * Reopen this MR.
     *
     * @param  object $MR
     * @access public
     * @return array
     */
    public function reopen(object $MR): array
    {
        $link = helper::createLink('mr', 'view', "mr={$MR->id}");
        if($MR->status == 'opened') return array('result' => 'fail', 'message' => $this->lang->mr->repeatedOperation, 'load' => $link);

        $actionID = $this->loadModel('action')->create('mr', $MR->id, 'reopen');
        $rawMR    = $this->apiReopenMR($MR->hostID, $MR->targetProject, $MR->mriid);
        $changes  = common::createChanges($MR, $rawMR);
        $this->action->logHistory($actionID, $changes);

        if(isset($rawMR->state) && $rawMR->state == 'opened') return array('result' => 'success', 'message' => $this->lang->mr->reopenSuccess, 'load' => $link);
        return array('result' => 'fail', 'message' => $this->lang->fail, 'load' => $link);
    }

    /**
     * 获取合并请求关联的对象。
     * Get mr link list.
     *
     * @param  int    $MRID
     * @param  int    $productID
     * @param  string $type       story|task|bug
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getLinkList(int $MRID, int $productID, string $type, string $orderBy = 'id_desc', object $pager = null): array
    {
        if(!isset($this->config->objectTables[$type])) return array();

        $orderBy = str_replace('name_', 'title_', $orderBy);
        if($type == 'task') $orderBy = str_replace('title_', 'name_', $orderBy);

        return $this->dao->select('t1.*')->from($this->config->objectTables[$type])->alias('t1')
            ->leftJoin(TABLE_RELATION)->alias('t2')->on('t1.id=t2.BID')
            ->where('t2.product')->eq($productID)
            ->andWhere('t2.relation')->eq('interrated')
            ->andWhere('t2.AType')->eq('mr')
            ->andWhere('t2.AID')->eq($MRID)
            ->andWhere('t2.BType')->eq($type)
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 根据对象信息获取合并请求列表。
     * Get linked MR pairs.
     *
     * @param  int    $objectID
     * @param  string $objectType
     * @access public
     * @return array
     */
    public function getLinkedMRPairs(int $objectID, string $objectType = 'story'): array
    {
        return $this->dao->select("t2.id,t2.title")->from(TABLE_RELATION)->alias('t1')
            ->leftJoin(TABLE_MR)->alias('t2')->on('t1.AID = t2.id')
            ->where('t1.AType')->eq('mr')
            ->andWhere('t1.BType')->eq($objectType)
            ->andWhere('t1.BID')->eq($objectID)
            ->andWhere('t2.id')->ne(0)
            ->fetchPairs();
    }

    /**
     * 合并请求关联对象。
     * Create an mr link.
     *
     * @param  int    $MRID
     * @param  int    $productID
     * @param  string $type       story|task|bug
     * @param  array  $objects
     * @access public
     * @return bool
     */
    public function link(int $MRID, int $productID, string $type, array $objects): bool
    {
        if(!isset($this->config->objectTables[$type])) return false;

        $MR = $this->fetchByID($MRID);
        if(!$MR) return false;

        /* Set link action text. */
        $user    = $this->loadModel('user')->getRealNameAndEmails($MR->createdBy);
        $comment = $MR->createdDate . '::' . zget($user, 'realname', '') . '::' . helper::createLink('mr', 'view', "mr={$MR->id}");

        $this->loadModel('action');
        foreach($objects as $objectID)
        {
            $relation = new stdclass();
            $relation->product  = $productID;
            $relation->AType    = 'mr';
            $relation->AID      = $MRID;
            $relation->relation = 'interrated';
            $relation->BType    = $type;
            $relation->BID      = $objectID;
            $this->dao->replace(TABLE_RELATION)->data($relation)->exec();

            $this->action->create($type, (int)$objectID, 'createmr', '', $comment);
        }

        return !dao::isError();
    }

    /**
     * 保存合并请求关联的对象。
     * Save linked objects.
     *
     * @param  object $MR
     * @access public
     * @return bool
     */
    public function linkObjects(object $MR): bool
    {
        /* Get commits by MR. */
        $commits = $this->apiGetMRCommits($MR->hostID, (string)$MR->targetProject, $MR->mriid);
        if(empty($commits)) return true;

        /* Init objects. */
        $objectList = array('story' => array(), 'bug' => array(), 'task' => array());
        $this->loadModel('repo');
        foreach($commits as $commit)
        {
            $objects = $this->repo->parseComment($commit->message);
            $objectList['story'] = array_merge($objectList['story'], $objects['stories']);
            $objectList['bug']   = array_merge($objectList['bug'],   $objects['bugs']);
            $objectList['task']  = array_merge($objectList['task'],  $objects['tasks']);
        }

        $users          = $this->loadModel('user')->getPairs('noletter');
        $MRCreateAction = $MR->createdDate . '::' . zget($users, $MR->createdBy) . '::' . helper::createLink('mr', 'view', "mr={$MR->id}");
        $product        = $this->getMRProduct($MR);

        $this->loadModel('action');
        foreach($objectList as $type => $objectIDs)
        {
            $relation           = new stdclass();
            $relation->product  = $product ? $product->id : 0;
            $relation->AType    = 'mr';
            $relation->AID      = $MR->id;
            $relation->relation = 'interrated';
            $relation->BType    = $type;
            foreach($objectIDs as $objectID)
            {
                $relation->BID = $objectID;
                $this->dao->replace(TABLE_RELATION)->data($relation)->exec();
                $this->action->create($type, (int)$objectID, 'createmr', '', $MRCreateAction);
            }
        }
        return !dao::isError();
    }

    /**
     * 解除合并请求关联的对象。
     * Unlink an mr link.
     *
     * @param  int    $MRID
     * @param  int    $productID
     * @param  string $type
     * @param  int    $objectID
     * @access public
     * @return bool
     */
    public function unlink(int $MRID, int $productID, string $type, int $objectID): bool
    {
        if(!isset($this->config->objectTables[$type])) return false;

        $this->dao->delete()->from(TABLE_RELATION)
            ->where('product')->eq($productID)
            ->andWhere('AType')->eq('mr')
            ->andWhere('AID')->eq($MRID)
            ->andWhere('BType')->eq($type)
            ->andWhere('BID')->eq($objectID)
            ->exec();

        $this->loadModel('action')->create($type, $objectID, 'deletemr', '', helper::createLink('mr', 'view', "mr={$MRID}"));
        return !dao::isError();
    }

    /**
     * 获取合并请求的产品。
     * Get mr product.
     *
     * @param  object $MR
     * @access public
     * @return object|false
     */
    public function getMRProduct(object $MR): object|false
    {
        $productID = $this->dao->select('product')->from(TABLE_REPO)->where('id')->eq($MR->repoID)->fetch('product');
        if(!$productID) return false;

        return $this->loadModel('product')->getById((int)$productID);
    }

    /**
     * 获取合并请求的收件人和抄送人。
     * Get toList and ccList.
     *
     * @param  object $MR
     * @access public
     * @return array
     */
    public function getToAndCcList(object $MR): array
    {
        return array($MR->createdBy, $MR->assignee);
    }

    /**
     * 将合并的操作记录到链接
     * Log merged action to links.
     *
     * @param  object $MR
     * @access public
     * @return bool
     */
    public function logMergedAction(object $MR): bool
    {
        $this->loadModel('action')->create('mr', $MR->id, 'mergedmr');

        $product = $this->getMRProduct($MR);
        foreach(array('story', 'bug', 'task') as $type)
        {
            $objects = $this->getLinkList($MR->id, $product ? $product->id : 0, $type);
            foreach($objects as $object)
            {
                $this->action->create($type, $object->id, 'mergedmr', '', helper::createLink('mr', 'view', "mr={$MR->id}"));
            }
        }

        $this->dao->update(TABLE_MR)->data(array('status' => 'merged'))->where('id')->eq($MR->id)->exec();
        return !dao::isError();
    }

    /**
     * 检查是否有相同的未关闭合并请求。
     * Check same opened mr for source branch.
     *
     * @param  int    $hostID
     * @param  string $sourceProject
     * @param  string $sourceBranch
     * @param  string $targetProject
     * @param  string $targetBranch
     * @access public
     * @return array
     */
    public function checkSameOpened(int $hostID, string $sourceProject, string $sourceBranch, string $targetProject, string $targetBranch): array
    {
        if($sourceProject == $targetProject && $sourceBranch == $targetBranch) return array('result' => 'fail', 'message' => $this->lang->mr->errorLang[1]);
        $dbOpenedID = $this->dao->select('id')->from(TABLE_MR)
            ->where('hostID')->eq($hostID)
            ->andWhere('sourceProject')->eq($sourceProject)
            ->andWhere('sourceBranch')->eq($sourceBranch)
            ->andWhere('targetProject')->eq($targetProject)
            ->andWhere('targetBranch')->eq($targetBranch)
            ->andWhere('status')->eq('opened')
            ->andWhere('deleted')->eq('0')
            ->fetch('id');
        if(!empty($dbOpenedID)) return array('result' => 'fail', 'message' => sprintf($this->lang->mr->hasSameOpenedMR, $dbOpenedID));

        $MR = $this->apiGetSameOpened($hostID, (string)$sourceProject, $sourceBranch, (string)$targetProject, $targetBranch);
        if($MR) return array('result' => 'fail', 'message' => sprintf($this->lang->mr->errorLang[2], $MR->iid));
        return array('result' => 'success');
    }

    /**
     * 解析API错误信息。
     * Convert API error.
     *
     * @param  array  $message
     * @access public
     * @return string
     */
    public function convertApiError(array|string $message): string
    {
        if(is_array($message)) $message = $message[0];
        if(!is_string($message)) return $message;

        foreach($this->lang->mr->apiErrorMap as $key => $errorMsg)
        {
            if(strpos($errorMsg, '/') === 0)
            {
                $result = preg_match($errorMsg, $message, $matches);
                if($result) $errorMessage = sprintf(zget($this->lang->mr->errorLang, $key), $matches[1]);
            }
            elseif($message == $errorMsg)
            {
                $errorMessage = zget($this->lang->mr->errorLang, $key, $message);
            }
            if(isset($errorMessage)) break;
        }
        return isset($errorMessage) ? $errorMessage : $message;
    }

    /**
     * 判断按钮是否可点击。
     * Adjust the action clickable.
     *
     * @param  object $MR
     * @param  string $action
     * @access public
     * @return bool
     */
    public static function isClickable(object $MR, string $action): bool
    {
        if($action == 'edit' and !$MR->synced) return false;
        if($action == 'edit')   return $MR->canEdit != 'disabled';
        if($action == 'delete') return $MR->canDelete != 'disabled';

        return true;
    }

    /**
     * 根据ID删除合并请求。
     * Delete MR by ID.
     *
     * @param  int    $MRID
     * @access public
     * @return bool
     */
    public function deleteByID(int $MRID): bool
    {
        $MR = $this->fetchByID($MRID);
        if(!$MR) return false;

        if($MR->synced)
        {
           $res = $this->apiDeleteMR($MR->hostID, $MR->targetProject, $MR->mriid);
           if(isset($res->message)) dao::$errors[] = $this->convertApiError($res->message);
           if(dao::isError()) return false;
        }

        $this->dao->delete()->from(TABLE_MR)->where('id')->eq($MRID)->exec();

        $this->loadModel('action')->create('mr', $MRID, 'deleted', '', $MR->title);
        $this->createMRLinkedAction($MRID, 'removemr');
        return !dao::isError();
    }
}
