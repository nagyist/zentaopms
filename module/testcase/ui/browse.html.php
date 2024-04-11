<?php
declare(strict_types=1);
/**
 * The browse view file of testcase module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuting Wang <wangyuting@easycorp.ltd>
 * @package     testcase
 * @link        https://www.zentao.net
 */
namespace zin;

include 'header.html.php';

jsVar('confirmBatchDeleteSceneCase', $lang->testcase->confirmBatchDeleteSceneCase);
jsVar('dragModalMessage', $lang->testcase->dragModalMessage);

$topSceneCount = count(array_filter(array_map(function($case){return $case->isScene && $case->grade == 1;}, $cases)));

$canBatchRun                = hasPriv('testtask', 'batchRun') && !$isOnlyScene;
$canBatchEdit               = hasPriv('testcase', 'batchEdit') && !$isOnlyScene;
$canBatchReview             = hasPriv('testcase', 'batchReview') && !$isOnlyScene && ($config->testcase->needReview || !empty($config->testcase->forceReview));
$canBatchDelete             = hasPriv('testcase', 'batchDelete') && !$isOnlyScene;
$canBatchChangeType         = hasPriv('testcase', 'batchChangeType') && !$isOnlyScene;
$canBatchConfirmStoryChange = hasPriv('testcase', 'batchConfirmStoryChange') && !$isOnlyScene;
$canBatchChangeBranch       = hasPriv('testcase', 'batchChangeBranch') && !$isOnlyScene && isset($product->type) && $product->type != 'normal';
$canBatchChangeModule       = hasPriv('testcase', 'batchChangeModule') && !empty($productID) && ((isset($product->type) && $product->type == 'normal') || $branch !== 'all');
$canBatchChangeScene        = hasPriv('testcase', 'batchChangeScene') && !$isOnlyScene;
$canImportToLib             = hasPriv('testcase', 'importToLib') && !$isOnlyScene;
$canGroupBatch              = ($canBatchRun || $canBatchEdit || $canBatchReview || $canBatchDelete || $canBatchChangeType || $canBatchConfirmStoryChange);
$canBatchAction             = ($canGroupBatch || $canBatchChangeBranch || $canBatchChangeModule || $canBatchChangeScene || $canImportToLib);

$productCount  = count(array_unique(array_map(function($case){return $case->product;}, $cases)));
$caseProductID = $productCount > 1 ? 0 : $productID;

$navActions = array();
if($canBatchReview || $canBatchDelete || $canBatchChangeType || $canBatchConfirmStoryChange)
{
    if($canBatchReview)
    {
        $reviewItems = array();
        foreach($lang->testcase->reviewResultList as $key => $result)
        {
            if($key == '') continue;
            $reviewItems[] = array('text' => $result, 'innerClass' => 'batch-btn ajax-btn not-open-url', 'data-url' => $this->createLink('testcase', 'batchReview', "result=$key"));
        }
        $navActions[] = array('text' => $lang->testcase->review, 'class' => 'not-hide-menu', 'items' => $reviewItems);
    }
    if($canBatchDelete) $navActions[] = array('text' => $lang->delete, 'innerClass' => 'batch-btn ajax-btn not-open-url batch-delete-btn', 'data-url' => helper::createLink('testcase', 'batchDelete', "productID=$productID"));
    if($canBatchChangeType)
    {
        $typeItems = array();
        foreach($lang->testcase->typeList as $key => $type)
        {
            if(!$key || $key == 'unit') continue;
            $typeItems[] = array('text' => $type, 'innerClass' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('testcase', 'batchChangeType', "type={$key}"));
        }
        $navActions[] = array('text' => $lang->testcase->type, 'class' => 'not-hide-menu', 'items' => $typeItems);
    }
    if($canBatchConfirmStoryChange) $navActions[] = array('text' => $lang->testcase->confirmStoryChange, 'innerClass' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('testcase', 'batchConfirmStoryChange', "productID=$productID"));
}

if($canBatchChangeModule)
{
    $moduleItems = array();
    foreach($modules as $changeModuleID => $module) $moduleItems[] = array('text' => $module, 'innerClass' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('testcase', 'batchChangeModule', "moduleID={$changeModuleID}"));
}

if($canBatchChangeBranch)
{
    $branchItems = array();
    foreach($branchTagOption as $branchTagID => $branchName) $branchItems[] = array('text' => $branchName, 'innerClass' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('testcase', 'batchChangeBranch', "branchID=$branchTagID"));
}

if($canBatchChangeScene)
{
    $sceneItems = array();
    foreach($iscenes as $sceneID => $scene) $sceneItems[] = array('text' => $scene, 'innerClass' => 'batch-btn ajax-btn not-open-url', 'data-url' => helper::createLink('testcase', 'batchChangeScene', "sceneId=$sceneID"));
}

$footToolbar = $canBatchAction ? array('items' => array
(
    $canGroupBatch ? array('type' => 'btn-group', 'items' => array
    (
        $canBatchRun ? array('text' => $lang->testtask->runCase, 'className' => 'batch-btn secondary not-open-url', 'data-url' => helper::createLink('testtask', 'batchRun', "productID=$productID&orderBy=$orderBy&from=testcase")) : null,
        $canBatchEdit ? array('text' => $lang->edit, 'className' => 'batch-btn secondary not-open-url', 'data-url' => helper::createLink('testcase', 'batchEdit', "productID=$caseProductID&branch=$branch")) : null,
        !empty($navActions) ? array('caret' => 'up', 'className' => 'secondary', 'items' => $navActions, 'data-placement' => 'top-start') : null,
    )) : null,
    $canBatchChangeBranch ? array('caret' => 'up', 'text' => $lang->product->branchName[$product->type], 'type' => 'dropdown', 'items' => $branchItems, 'data-placement' => 'top-start') : null,
    $canBatchChangeModule ? array('caret' => 'up', 'text' => $lang->testcase->moduleAB, 'type' => 'dropdown', 'items' => $moduleItems, 'data-placement' => 'top-start') : null,
    $canBatchChangeScene ? array('caret' => 'up', 'text' => $lang->testcase->scene, 'type' => 'dropdown', 'items' => $sceneItems, 'data-placement' => 'top-start') : null,
    $canImportToLib ? array('text' => $lang->testcase->importToLib, 'data-toggle' => 'modal', 'data-target' => '#importToLib', 'data-size' => 'sm') : null,
), 'btnProps' => array('size' => 'sm', 'btnType' => 'secondary')) : null;

$footToolbar['items'] = $canBatchAction ? array_values(array_filter($footToolbar['items'])) : array();

$cols = $isOnlyScene ? $this->config->scene->dtable->fieldList : $this->loadModel('datatable')->getSetting('testcase');
if(!empty($cols['actions']['list']))
{
    $executionID = ($app->tab == 'project' || $app->tab == 'execution') ? $this->session->{$app->tab} : '0';
    foreach($cols['actions']['list'] as $method => $methodParams)
    {
        if(!isset($methodParams['url'])) continue;

        $cols['actions']['list'][$method]['url'] = str_replace('%executionID%', (string)$executionID, $methodParams['url']);
    }
}

if(isset($cols['title']))  $cols['title']['nestedToggle'] = $topSceneCount > 0;
if(isset($cols['branch'])) $cols['branch']['map']         = $branchTagOption;
if(isset($cols['story']))  $cols['story']['map']          = $stories;
if(isset($cols['scene']))  $cols['scene']['map']          = $iscenes;
if(isset($cols['status'])) $cols['status']['statusMap']['changed'] = $lang->story->changed;

foreach($cases as $case)
{
    $case->lastRunDate = formatTime($case->lastRunDate);

    $actionType = $case->isScene ? 'scene' : 'testcase';
    $cols['actions']['menu'] = $config->$actionType->menu;
    if($actionType == 'testcase' && !$this->config->testcase->needReview) unset($cols['actions']['menu'][1][0]);
    if($actionType == 'scene') $case->bugs = $case->results = $case->stepNumber = $case->version = '';
    if(!empty($case->needconfirm)) $case->status = 'changed';

    $case->browseType = $browseType;
    initTableData(array($case), $cols, $this->testcase);
}

$linkParams = '';
foreach($app->rawParams as $key => $value) $linkParams = $key != 'orderBy' ? "{$linkParams}&{$key}={$value}" : "{$linkParams}&orderBy={name}_{sortType}";

div(
    on::click('[data-col="actions"] .ztf-case', 'window.checkZtf'),
    dtable
    (
        set::plugins(array('sortable')),
        set::sortable(strpos($orderBy, 'sort_asc') !== false),
        set::onSortEnd(strpos($orderBy, 'sort_asc') !== false ? jsRaw('window.onSortEnd') : null),
        set::canSortTo(strpos($orderBy, 'sort_asc') !== false ? jsRaw('window.canSortTo') : null),
        set::customCols(!$isOnlyScene),
        set::userMap($users),
        set::cols($cols),
        set::nested(true),
        set::data(array_values($cases)),
        set::onRenderCell(jsRaw('window.onRenderCell')),
        set::checkable($canBatchAction),
        set::checkInfo(jsRaw('function(checks){return window.setStatistics(this, checks);}')),
        set::orderBy($orderBy),
        set::sortLink(createLink($app->rawModule, $app->rawMethod, $linkParams)),
        set::nested(true),
        set::footToolbar($footToolbar),
        set::footPager(usePager()),
        set::emptyTip($lang->testcase->noCase),
        set::createTip($lang->testcase->create),
        set::createLink($canModify && hasPriv('testcase', 'create') ? createLink('testcase', 'create', 'productID=' . zget($product, 'id', 0) . "&branch={$branch}&moduleID={$moduleID}" . ($app->tab == 'project' ? "&from=project&param={$projectID}" : '')) : ''),
        set::customData(array('isOnlyScene' => $isOnlyScene, 'pageSummary' => $summary, 'modules' => $modulePairs))
    )
);

modal
(
    on::click('button[type="submit"]', 'getCheckedCaseIdList'),
    setID('importToLib'),
    set::modalProps(array('title' => $lang->testcase->importToLib)),
    formPanel
    (
        set::url(createLink('testcase', 'importToLib')),
        set::actions(array('submit')),
        set::submitBtnText($lang->testcase->import),
        formRow
        (
            formGroup
            (
                set::label($lang->testcase->selectLibAB),
                set::name('lib'),
                set::items($libraries),
                set::value(''),
                set::required(true)
            )
        ),
        formRow
        (
            setClass('hidden'),
            formGroup
            (
                set::name('caseIdList'),
                set::value('')
            )
        )
    )
);

render();
