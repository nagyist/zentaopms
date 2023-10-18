<?php
declare(strict_types=1);
/**
 * The browse view file of zahost module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Zeng Gang<zenggang@easycorp.ltd>
 * @package     zahost
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('orderBy', $orderBy);
jsVar('sortLink', $sortLink);
jsVar('webRoot', getWebRoot());
jsVar('undeletedNotice', $lang->zahost->undeletedNotice);
jsVar('uninitNotice', $lang->zahost->uninitNotice);

$createItem = array('text' => $lang->zahost->create, 'url' => createLink('zahost', 'create'), 'icon' => 'plus', 'class' => 'btn primary');

foreach($hostList as $host)
{
    $host->memory   .= $lang->zahost->unitList['GB'];
    $host->diskSize .= $lang->zahost->unitList['GB'];

    $host->canDelete = !empty($nodeList[$host->hostID]) ? false : true;
}

$hostList = initTableData($hostList, $config->zahost->dtable->fieldList, $this->zahost);

\zin\featureBar
(
    li(searchToggle(set::open($browseType == 'bySearch'))),
    a
    (
        setClass('btn btn-link'),
        set::id('helpTab'),
        icon('help'),
        $lang->help,
        on::click('goHelp')
    )
);

toolBar
(
    hasPriv('zahost', 'create') ? item(set($createItem)) : null,
);

dtable
(
    set::cols($config->zahost->dtable->fieldList),
    set::data($hostList),
    set::onRenderCell(jsRaw('window.renderList')),
    set::afterRender(jsRaw('window.afterRender')),
    set::sortLink(jsRaw('createSortLink')),
    set::footPager(usePager()),
);

render();

