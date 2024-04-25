<?php
declare(strict_types=1);
/**
 * The link view file of mr module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Zeng Gang<zenggang@easycorp.ltd>
 * @package     mr
 * @link        https://www.zentao.net
 */
namespace zin;

$module = $app->tab == 'devops' ? 'repo' : $app->tab;
dropmenu
(
    set::module($module),
    set::tab($module),
    set::url(createLink($module, 'ajaxGetDropMenu', "objectID=$objectID&module={$app->rawModule}&method={$app->rawMethod}"))
);

jsVar('appTab', $app->tab);

foreach($bugs as $bug)
{
    $repo      = zget($repos, $bug->repo, $repo);
    $objectID  = $app->tab == 'execution' ? $bug->execution : 0;

    $bug->entry     = $this->repo->decodePath($bug->entry);
    $bug->revisionA = $repo->SCM != 'Subversion' ? strtr($bug->v2, '*', '-') : $bug->v2;

    $lines     = trim($bug->lines, ',');
    $fileEntry = $this->repo->encodePath("{$bug->entry}#{$bug->lines}");
    if(empty($bug->v1))
    {
        $bug->v2   = $repo->SCM != 'Subversion' ? strtr($bug->v2, '*', '-') : $bug->v2;
        $revision  = $repo->SCM != 'Subversion' ? $this->repo->getGitRevisionName($bug->v2, (int)zget($historys, $bug->v2)) : $bug->v2;
        $bug->link = $this->repo->createLink('view', "repoID=$repoID&objectID={$objectID}&entry={$fileEntry}&revision={$bug->v2}");
    }
    else
    {
        $revision  = $repo->SCM != 'Subversion' ? substr($bug->v1, 0, 10) : $bug->v1;
        $revision .= ' : ';
        $revision .= $repo->SCM != 'Subversion' ? substr($bug->v2, 0, 10) : $bug->v2;
        if($repo->SCM != 'Subversion') $revision .= ' (' . zget($historys, $bug->v1) . ' : ' . zget($historys, $bug->v2) . ')';
        $bug->link = $this->repo->createLink('diff', "repoID=$repoID&objectID={$objectID}&entry={$fileEntry}&oldRevision={$bug->v1}&newRevision={$bug->v2}");
    }

}
$bugs = initTableData($bugs, $config->repo->reviewDtable->fieldList);

$repoData = array(array(
     'text'     => $lang->all,
     'data-app' => $app->tab,
     'url'      => createLink('repo', 'review', "repoID=0&browseType=all&objectID={$objectID}"),
     'active'   => $allRepo
 ));

 foreach($repos as $repo)
 {
     if(!in_array($repo->SCM, $this->config->repo->gitServiceTypeList)) continue;

     $repoData[] = array(
         'text'     => $repo->name,
         'data-app' => $app->tab,
         'url'      => createLink('repo', 'review', "repoID={$repo->id}&browseType={$browseType}&objectID={$objectID}"),
         'active'   =>  !$allRepo && $repo->id == $repoID
     );
 }

\zin\featureBar
(
    set::current($browseType),
    set::linkParams("repoID={$repoID}&browseType={key}&objectID={$objectID}"),
    count($repoPairs) > 1 && $objectID ? to::leading
    (
        dropdown(
            to('trigger', btn(setClass('ghost'), $allRepo ? $lang->all : zget($repoPairs, $repoID, $lang->all))),
            set::items($repoData),
            set::placement('bottom-end')
        )
    ) : null
);

dtable
(
    set::userMap($users),
    set::cols($config->repo->reviewDtable->fieldList),
    set::data($bugs),
    set::sortLink(createLink('repo', 'review', "repoID=$repoID&browseType=$browseType&objectID={$objectID}&orderBy={name}_{sortType}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}")),
    set::orderBy($orderBy),
    set::onRenderCell(jsRaw('window.renderRepobugList')),
    set::footPager(usePager())
);
