<?php
declare(strict_types=1);
/**
* The preference view file of block module of ZenTaoPMS.
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      Yuting Wang <wangyuting@easycorp.ltd>
* @package     block
* @link        https://www.zentao.net
*/

namespace zin;

/**
 * 展示个性化设置页面。
 * Print preference page.
 */
function printPreference(array $URSRList)
{
    global $lang, $config;

    $imageList = array();
    $imageList['program-browse']  = 'list';
    $imageList['program-project'] = 'list-recent';
    $imageList['program-kanban']  = 'kanban';

    $imageList['product-index']     = 'panel-recent-browse';
    $imageList['product-all']       = 'list';
    $imageList['product-dashboard'] = 'panel';
    $imageList['product-browse']    = 'list-recent';
    $imageList['product-kanban']    = 'kanban';

    $imageList['project-browse']    = 'list';
    $imageList['project-execution'] = 'list-recent';
    $imageList['project-index']     = 'panel-recent-browse';
    $imageList['project-kanban']    = 'kanban';

    $imageList['execution-all']             = 'list';
    $imageList['execution-task']            = 'list-recent';
    $imageList['execution-executionkanban'] = 'kanban';

    $URSRItems = array();
    foreach($URSRList as $URSRKey => $URSRValue)
    {
        $contentHtml = "<div class=''><span class='label light-pale circle mr-4'>{$URSRKey}</span>{$URSRValue}</div>";
        $URSRItems[] = array('value' => $URSRKey, 'text' => $URSRValue, 'content' => array('html' => $contentHtml, 'class' => 'flex w-full border p-4 preference-box'));
    }

    foreach(array('program', 'product', 'project', 'execution') as $objectType)
    {
        $itemsName    = $objectType . 'Items';
        $linkListName = $objectType . 'LinkList';

        $$itemsName = array();
        foreach($lang->my->$linkListName as $value => $label)
        {
            list($title, $desc) = explode('/', $label);

            $contentHtml    = "<div class='basis-20'><img src='theme/default/images/guide/{$imageList[$value]}.png' /></div><div class='pl-2 col justify-around'><div class='text-black'>{$title}</div><div class='text-gray text-sm'>{$desc}</div></div>";
            ${$itemsName}[] = array('value' => $value, 'text' => $title, 'content' => array('html' => $contentHtml, 'class' => 'flex w-full border no-wrap pl-1 py-4 preference-box'));
        }
    }

    return div
    (
        set('class', 'preference-block pt-6 px-6'),
        form
        (
            set::url(helper::createLink('my', 'preference', "showTip=false")),
            set::labelWidth('8rem'),
            set::actions(array('submit')),
            formGroup
            (
                set::label($lang->my->storyConcept),
                picker
                (
                    set('menu', array('class' => 'menu picker-menu-list no-nested-items menu-nested flex flex-wrap content-between ursr-menu preference-menu')),
                    set::name('URSR'),
                    set::required(true),
                    set::items($URSRItems),
                    set::value($config->URSR)
                )
            ),
            in_array($config->systemMode, array('ALM', 'PLM')) ? formGroup
            (
                set::label($lang->my->programLink),
                picker
                (
                    set('menu', array('class' => 'preference-menu')),
                    set::name('programLink'),
                    set::required(true),
                    set::items($programItems),
                    set::value($config->programLink)
                )
            ) : null,
            formGroup
            (
                set::label($lang->my->productLink),
                picker
                (
                    set('menu', array('class' => 'preference-menu')),
                    set::name('productLink'),
                    set::required(true),
                    set::items($productItems),
                    set::value($config->productLink)
                )
            ),
            $config->vision != 'or' ? formGroup
            (
                set::label($lang->my->projectLink),
                picker
                (
                    set('menu', array('class' => 'preference-menu')),
                    set::name('projectLink'),
                    set::required(true),
                    set::items($projectItems),
                    set::value($config->projectLink)
                )

            ) : null,
            $config->vision != 'or' ? formGroup
            (
                set::label($lang->my->executionLink),
                picker
                (
                    set('menu', array('class' => 'preference-menu')),
                    set::name('executionLink'),
                    set::required(true),
                    set::items($executionItems),
                    set::value($config->executionLink)
                )
            ) : null
        )
    );
}
