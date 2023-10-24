<?php
declare(strict_types = 1);
/**
 * The preview view file of pivot module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Gang Liu <liugang@easycorp.ltd>
 * @package     pivot
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('groupID', isset($group->id) ? $group->id : 0);
jsVar('charts', $charts);
jsVar('noChartSelected', $lang->chart->noChartSelected);

featureBar
(
    set::current($group->id),
    set::linkParams("dimension={$dimension}&group={key}")
);

render();                  ;
