<?php
declare(strict_types=1);
/**
 * The activate view file of task module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */
namespace zin;

$buildModule = $app->rawModule == 'projectrelease' ? 'projectrelease' : 'release';

jsVar('initLink', $link);
jsVar('type', $type);
$canBeChanged = common::canBeChanged($buildModule, $release);
$menus        = $this->release->buildOperateViewMenu($release);
detailHeader
(
    to::title(entityLabel(set(array('entityID' => $release->id, 'level' => 1, 'text' => $release->name)))),
    !empty($menus) ? to::suffix(btnGroup(set::items($menus))) : null
);

jsVar('orderBy', $orderBy);
jsVar('releaseID', $release->id);
jsVar('sortLink', helper::createLink($buildModule, 'view', "releaseID={$release->id}&type={type}&link={$link}&param={$param}&orderBy={orderBy}"));

/* Table data and setting for finished stories tab. */
jsVar('summary', $summary);
jsVar('storyCases', $storyCases);
jsVar('confirmunlinkstory', $lang->release->confirmUnlinkStory);
jsVar('checkedSummary', str_replace('%storyCommon%', $lang->SRCommon, $lang->product->checkedSummary));
jsVar('unlinkstoryurl', helper::createLink($buildModule, 'unlinkStory', "releaseID={$release->id}&storyID=%s"));
$storyTableData = initTableData($stories, $config->release->dtable->story->fieldList, $this->release);

$canBatchUnlinkStory = $canBeChanged && common::hasPriv($buildModule, 'batchUnlinkStory');
$canBatchCloseStory  = $canBeChanged && common::hasPriv('story', 'batchClose');

$storyFootToolbar = array();
if($canBatchUnlinkStory) $storyFootToolbar['items'][] = array('className' => 'btn primary size-sm batch-btn', 'text' => $lang->release->batchUnlink, 'data-type' => 'story', 'data-url' => createLink($buildModule, 'batchUnlinkStory', "release={$release->id}"));
if($canBatchCloseStory)  $storyFootToolbar['items'][] = array('className' => 'btn primary size-sm batch-btn', 'text' => $lang->story->batchClose,    'data-type' => 'story', 'data-url' => createLink('story', 'batchClose', "productID={$release->product}"));

/* Table data and setting for resolved bugs tab. */
jsVar('confirmunlinkbug', $lang->release->confirmUnlinkBug);
jsVar('unlinkbugurl', helper::createLink($buildModule, 'unlinkBug', "releaseID={$release->id}&bugID=%s"));

$config->release->dtable->bug->fieldList['resolvedBuild']['map'] = $builds;
$bugTableData = initTableData($bugs, $config->release->dtable->bug->fieldList, $this->release);

$canBatchUnlinkBug = $canBeChanged && common::hasPriv($buildModule, 'batchUnlinkBug');
$canBatchCloseBug  = $canBeChanged && common::hasPriv('bug', 'batchClose');

$bugFootToolbar = array();
if($canBatchUnlinkBug) $bugFootToolbar['items'][] = array('className' => 'btn primary size-sm batch-btn', 'text' => $lang->release->batchUnlink, 'data-type' => 'bug', 'data-url' => createLink($buildModule, 'batchUnlinkBug', "release={$release->id}"));
if($canBatchCloseBug)  $bugFootToolbar['items'][] = array('className' => 'btn primary size-sm batch-btn', 'text' => $lang->bug->batchClose,      'data-type' => 'bug', 'data-url' => createLink('bug', 'batchClose', "productID={$release->product}"));

/* Table data and setting for left bugs tab. */
jsVar('confirmunlinkleftbug', $lang->release->confirmUnlinkBug);
jsVar('unlinkleftbugurl', helper::createLink($buildModule, 'unlinkBug', "releaseID={$release->id}&bugID=%s&type=leftBug"));

$config->release->dtable->leftBug->fieldList['resolvedBuild']['map'] = $builds;
$leftBugTableData = initTableData($leftBugs, $config->release->dtable->leftBug->fieldList, $this->release);

$leftBugFootToolbar = array();
if($canBatchUnlinkBug) $leftBugFootToolbar['items'][] = array('className' => 'btn primary size-sm batch-btn', 'text' => $lang->release->batchUnlink, 'data-type' => 'bug', 'data-url' => createLink($buildModule, 'batchUnlinkBug', "release={$release->id}&type=leftBug"));

/* Process release info data. */
$releaseBuild = array();
foreach($release->builds as $build) $releaseBuild[] = $build->name;

$releaseBranch = array();
if($product->type != 'normal')
{
    foreach($release->branches as $branchID) $releaseBranch[] = zget($branches, $branchID, '');
}

/* Right menus, export and link. */
$exportBtn = null;
if(common::hasPriv($buildModule, 'export') && ($summary || count($bugs) || count($leftBugs)))
{
    $exportBtn = btn(set(array(
        'text'        => $lang->release->export,
        'icon'        => 'export',
        'url'         => createLink($buildModule, 'export', "releaseID={$release->id}"),
        'class'       => 'ghost',
        'data-size'   => 'sm',
        'data-toggle' => 'modal',
    )));
}

$linkStoryBtn = $linkBugBtn = $linkLeftBtn = null;
if($canBeChanged)
{
    if(common::hasPriv($buildModule, 'linkStory'))
    {
        $linkStoryBtn = btn(set(array(
            'text'     => $lang->release->linkStory,
            'icon'     => 'link',
            'data-url' => createLink($buildModule, 'linkStory', "releaseID={$release->id}&browseType=story"),
            'class'    => 'link',
            'type'     => 'primary',
            'onclick'  => 'showLink(this)',
        )));
    }

    if(common::hasPriv($buildModule, 'linkBug'))
    {
        $linkBugBtn = btn(set(array(
            'text'     => $lang->release->linkBug,
            'icon'     => 'bug',
            'data-url' => createLink($buildModule, 'linkBug', "releaseID={$release->id}&browseType=bug"),
            'class'    => 'link',
            'type'     => 'primary',
            'onclick'  => 'showLink(this)',
        )));

        $linkLeftBtn = btn(set(array(
            'text'     => $lang->release->linkBug,
            'icon'     => 'bug',
            'data-url' => createLink($buildModule, 'linkBug', "releaseID={$release->id}&browseType=leftBug&param=0&type=leftBug"),
            'class'    => 'link',
            'type'     => 'primary',
            'onclick'  => 'showLink(this)',
        )));
    }
}

detailBody
(
    sectionList(
        tabs
        (
            set::className('w-full'),

            /* Linked story table. */
            tabPane
            (
                to::prefix(icon('lightbulb')),
                set::key('finishedStory'),
                set::title($lang->release->stories),
                set::active($type == 'story'),
                div
                (
                    setClass('tabnActions'),
                    $exportBtn,
                    $linkStoryBtn,
                ),
                dtable
                (
                    set::userMap($users),
                    set::cols(array_values($config->release->dtable->story->fieldList)),
                    set::data($storyTableData),
                    set::checkable($canBatchUnlinkStory || $canBatchCloseStory),
                    set::sortLink(jsRaw('window.createSortLink')),
                    set::footToolbar($storyFootToolbar),
                    set::footPager(
                        usePager('storyPager'),
                        set::recPerPage($storyPager->recPerPage),
                        set::recTotal($storyPager->recTotal),
                        set::linkCreator(helper::createLink($buildModule, 'view', "releaseID={$release->id}&type=story&link={$link}&param={$param}&orderBy={$orderBy}&recTotal={$storyPager->recTotal}&recPerPage={recPerPage}&page={page}"))
                    ),
                    set::checkInfo(jsRaw('function(checkedIDList){return window.setStoryStatistics(this, checkedIDList);}'))
                )
            ),

            /* Resolved bug table. */
            tabPane
            (
                to::prefix(icon('bug')),
                set::key('resolvedBug'),
                set::title($lang->release->bugs),
                set::active($type == 'bug'),
                div
                (
                    setClass('tabnActions'),
                    $exportBtn,
                    $linkBugBtn,
                ),
                dtable
                (
                    set::userMap($users),
                    set::cols(array_values($config->release->dtable->bug->fieldList)),
                    set::data($bugTableData),
                    set::checkable($canBatchUnlinkBug || $canBatchCloseBug),
                    set::sortLink(jsRaw('window.createSortLink')),
                    set::footToolbar($bugFootToolbar),
                    set::footPager(
                        usePager('bugPager'),
                        set::recPerPage($bugPager->recPerPage),
                        set::recTotal($bugPager->recTotal),
                        set::linkCreator(helper::createLink($buildModule, 'view', "releaseID={$release->id}&type=bug&link={$link}&param={$param}&orderBy={$orderBy}&recTotal={$bugPager->recTotal}&recPerPage={recPerPage}&page={page}"))
                    ),
                )
            ),

            /* Left bug table. */
            tabPane
            (
                to::prefix(icon('bug')),
                set::key('leftBug'),
                set::title($lang->release->generatedBugs),
                set::active($type == 'leftBug'),
                div
                (
                    setClass('tabnActions'),
                    $exportBtn,
                    $linkLeftBtn,
                ),
                dtable
                (
                    set::userMap($users),
                    set::cols(array_values($config->release->dtable->leftBug->fieldList)),
                    set::data($leftBugTableData),
                    set::checkable($canBatchUnlinkBug || $canBatchCloseBug),
                    set::sortLink(jsRaw('window.createSortLink')),
                    set::footToolbar($leftBugFootToolbar),
                    set::footPager(
                        usePager('leftBugPager'),
                        set::recPerPage($leftBugPager->recPerPage),
                        set::recTotal($leftBugPager->recTotal),
                        set::linkCreator(helper::createLink($buildModule, 'view', "releaseID={$release->id}&type=leftBug&link={$link}&param={$param}&orderBy={$orderBy}&recTotal={$leftBugPager->recTotal}&recPerPage={recPerPage}&page={page}"))
                    ),
                )
            ),

            /* Basic info block. */
            tabPane
            (
                to::prefix(icon('flag')),
                set::key('releaseInfo'),
                set::title($lang->release->basicInfo),
                div
                (
                    setClass('tabnActions'),
                    $exportBtn,
                ),
                div(
                    section(
                        set::title($lang->release->basicInfo),
                        tableData
                        (
                            item
                            (
                                set::name($lang->release->product),
                                $release->productName
                            ),
                            item
                            (
                                set::name($lang->release->name),
                                $release->name
                            ),
                            item
                            (
                                set::name($lang->release->includedBuild),
                                implode($lang->comma, $releaseBuild)
                            ),
                            !empty($releaseBranch) ? item
                            (
                                set::name($lang->release->branch),
                                implode($lang->comma, $releaseBranch)
                            ) : null,
                            item
                            (
                                set::name($lang->release->status),
                                $this->processStatus('release', $release)
                            ),
                            item
                            (
                                set::name($lang->release->date),
                                $release->date
                            ),
                            item
                            (
                                set::name($lang->release->desc),
                                $release->desc
                            ),
                        )
                    ),
                    fileList
                    (
                        set::files($release->files),
                    ),
                    h::hr(set::className('mt-6')),
                    history()
                )
            )
        )
    )
);

render();
