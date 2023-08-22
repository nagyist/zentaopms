<?php
declare(strict_types=1);
/**
 * The view file of metric module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Xin Zhou<zhouxin@easycorp.ltd>
 * @package     metric
 * @link        http://www.zentao.net
 */
namespace zin;

/**
 * Build content of table data.
 *
 * @param  array  $items
 * @access public
 * @return array
 */
$buildItems = function($items): array
{
    $itemList = array();
    foreach($items as $item)
    {
        $itemList[] = item
        (
            set::name($item['name']),
            !empty($item['href']) ? a
            (
                set::href($item['href']),
                !empty($item['attr']) && is_array($item['attr']) ? set($item['attr']) : null,
                $item['text']
            ) : $item['text'],
            set::collapse(!empty($item['text'])),
        );
    }

    return $itemList;
};

detailHeader
(
    to::title
    (
        entityLabel
        (
            set::entityID($metric->id),
            set::level(),
            set::text($metric->name)
        )
    ),
    to::prefix
    (
        backBtn
        (
            set::icon('back'),
            set::class('ghost text-white'),
            $lang->goback
        )
    )
);

detailBody
(
    sectionList
    (
        section
        (
            set::title($lang->metric->desc),
            set::content($metric->desc),
            set::useHtml(true)
        ),
        section
        (
            set::title($lang->metric->formula),
            set::content($metric->definition),
            set::useHtml(true)
        )
    ),
    history(),
    detailSide
    (
        tabs
        (
            set::collapse(true),
            tabPane
            (
                set::key('legendBasicInfo'),
                set::title($lang->metric->legendBasicInfo),
                set::active(true),
                tableData
                (
                    $buildItems($legendBasic)
                )
            ),
            tabPane
            (
                set::key('legendCreateInfo'),
                set::title($lang->metric->legendCreateInfo),
                tableData
                (
                    $buildItems($createEditInfo)
                )
            )
        ),
    )
);

render();
