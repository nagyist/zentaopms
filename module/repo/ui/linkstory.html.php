<?php
declare(strict_types=1);
/**
 * The linkstory file of testreport module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuting Wang <wangyuting@easycorp.ltd>
 * @package     testreport
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('orderBy',  $orderBy);
jsVar('sortLink', createLink('repo', 'linkStory', "repoID=$repoID&revision=$revision&browseType=$browseType&param=$param&orderBy={orderBy}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"));

detailHeader
(
    to::prefix(''),
    to::title
    (
        $lang->repo->linkStory,
    )
);

$footToolbar = array('items' => array
(
    array('text' => $lang->repo->linkStory, 'className' => 'batch-btn ajax-btn', 'data-url' => helper::createLink('repo', 'linkStory', "repoID=$repoID&revision=$revision&browseType=$browseType&param=$param&orderBy=$orderBy"))
), 'btnProps' => array('size' => 'sm', 'btnType' => 'secondary', 'data-type' => 'stories'));

div(setID('searchFormPanel'), searchToggle(set::open(true), set::module('story')));

div
(
    set('class', 'repo-linkstory-title'),
    icon('unlink'),
    span
    (
        set('class', 'font-semibold ml-2'),
        $lang->productplan->unlinkedStories . "({$pager->recTotal})"
    )
);
$config->repo->storyDtable->fieldList['module']['map'] = $modules;
$config->repo->storyDtable->fieldList['title']['width'] = '100';
$allStories = initTableData($allStories, $config->repo->storyDtable->fieldList);
$data = array_values($allStories);
dtable
(
    set::userMap($users),
    set::data($data),
    set::cols($config->repo->storyDtable->fieldList),
    set::checkable(true),
    set::footToolbar($footToolbar),
    set::sortLink(jsRaw('createSortLink')),
    set::footPager(usePager(array('linkCreator' => createLink('repo', 'linkStory', "repoID=$repoID&revision=$revision&browseType=$browseType&param=$param&orderBy={$orderBy}&recTotal={recTotal}&recPerPage={recPerPage}&page={page}")))),
);

render();
