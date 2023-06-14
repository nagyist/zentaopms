<?php
declare(strict_types=1);
/**
 * The create view file of build module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     build
 * @link        https://www.zentao.net
 */
namespace zin;

/* zin: Set variables to define picker options for form. */
jsVar('multipleSelect', $lang->build->placeholder->multipleSelect);
jsVar('projectID', $projectID);
jsVar('executionID', $executionID);
jsVar('productGroups', $productGroups);

$integratedRow = '';
if($app->tab == 'project' && !empty($multipleProject))
{
    $integratedRow = formRow(
        formGroup
        (
            set::width('1/2'),
            set::label($lang->build->integrated),
            radioList
            (
                set::name('isIntegrated'),
                set::items($lang->build->isIntegrated),
                set::value('no'),
                set::inline(true)
            ),
        ),
    );
}

$executionRow = '';
if(!empty($multipleProject))
{
    $executionRow = formRow(
        formGroup
        (
            set::width('1/2'),
            set::name('execution'),
            set::label($lang->executionCommon),
            set::value($executionID),
            set::items($executions),
            set::required(true),
            on::change('loadProducts')
        ),
    );
}

$productRow = '';
if(!$hidden)
{
    $productBox = '';
    if(!empty($products) || !$executionID)
    {
        $productBox = formGroup(
            set::width('1/2'),
            set::id('productBox'),
            set::name('product'),
            set::label($lang->build->product),
            set::value(empty($product) ? '' : $product->id),
            set::items($products),
            set::required(true),
            on::change('loadBranches')
        );
    }
    else
    {
        $productBox = formGroup(
            set::width('1/2'),
            set::id('productBox'),
            set::label($lang->build->product),
            setClass('items-center'),
            html(sprintf($lang->build->noProduct, helper::createLink('execution', 'manageproducts', "executionID={$executionID}&from=buildCreate", '', true), $app->tab)),
        );
    }
    $productRow = formRow(
        $productBox,
    );
}

if(empty($product)) $product = new stdclass();
$productType     = zget($product, 'type', 'normal');
$productBranches = zget($product, 'branches', array());

formPanel
(
    set::title($lang->build->create),
    $integratedRow,
    $executionRow,
    $productRow,
    formRow
    (
        setClass(!empty($product) && $product->type != 'normal' ? '' : 'hidden'),
        formGroup
        (
            set::width('1/2'),
            set::name('branch[]'),
            set::label($lang->product->branchName[$productType]),
            set::value(key($productBranches)),
            set::items($branches),
        )
    ),
    formRow
    (
        setClass('hidden'),
        formGroup
        (
            set::width('1/2'),
            set::name('builds[]'),
            set::label($lang->build->builds),
            set::items(array()),
        ),
        formGroup
        (
            set::width('1/2'),
            setClass('pl-4 items-center'),
            icon('help')
        ),
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::name('name'),
            set::label($lang->build->nameAB),
        ),
        $lastBuild ? formGroup
        (
            set::width('1/2'),
            setClass('items-center'),
            div
            (
                set::id('lastBuildBox'),
                setClass('text-gray'),
                div
                (
                    setClass('help-block'),
                    html('&nbsp;' . $lang->build->last . ': <a class="code label light rounded-full" id="lastBuildBtn">' . $lastBuild->name . '</a>'),
                ),
            )
        ) : '',
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::name('builder'),
            set::label($lang->build->builder),
            set::value($app->user->account),
            set::items($users),
        ),
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::name('date'),
            set::label($lang->build->date),
            set::control('date'),
            set::value(helper::today()),
        ),
    ),
    formRow
    (
        formGroup
        (
            set::name('scmPath'),
            set::label($lang->build->scmPath),
            set::placeholder($lang->build->placeholder->scmPath),
        ),
    ),
    formRow
    (
        formGroup
        (
            set::name('filePath'),
            set::label($lang->build->filePath),
            set::placeholder($lang->build->placeholder->filePath),
        ),
    ),
    formRow
    (
        formGroup
        (
            set::name('files[]'),
            set::label($lang->build->files),
            set::control('file')
        ),
    ),
    formRow
    (
        formGroup
        (
            set::label($lang->build->desc),
            editor
            (
                set::name('desc'),
                set::rows('10'),
            )
        ),
        formGroup
        (
            setClass('hidden'),
            set::name('project'),
            set::value($projectID),
        )
    )
);

/* ====== Render page ====== */
render();
