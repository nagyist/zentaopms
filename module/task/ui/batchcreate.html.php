<?php
declare(strict_types=1);
/**
 * The batchCreate view file of task module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */
namespace zin;
/* ====== Preparing and processing page data ====== */
jsVar('executionID', $execution->id);
jsVar('storyTasks', $storyTasks);

/* zin: Set variables to define picker options for form. */
$storyItem         = '';
$previewItem       = '';
$copyStoryItem     = '';
$storyEstimateItem = '';
$storyDescItem     = '';
$storyPriItem      = '';
if(!$hideStory)
{
    $storyItem = formBatchItem(
        set::name('story'),
        set::label($lang->task->story),
        set::control('select'),
        set::items($stories),
        set::width('240px'),
        set::ditto(true),
    );

    $previewItem = formBatchItem(
        set::name('preview'),
        set::label(''),
        set('labelClass', 'hidden'),
        set::width('40px'),
        set::control('hidden'),
        btn
        (
            set('type', 'btn'),
            set('icon', 'eye'),
            set('class', 'ghost'),
            set('hint', $lang->preview),
            set('tagName', 'a'),
            set('url', '#'),
            set('data-toggle', 'modal'),
        )
    );

    $copyStoryItem = formBatchItem(
        set::name('copyStory'),
        set::label(''),
        set('labelClass', 'hidden'),
        set::width('40px'),
        set::control('hidden'),
        btn
        (
            set('type', 'btn'),
            set('icon', 'arrow-right'),
            set('class', 'ghost'),
            set('hint', $lang->task->copyStoryTitle),
        )
    );

    $storyEstimateItem = formBatchItem(
        set::name('storyEstimate'),
        set::label(''),
        set('labelClass', 'hidden'),
        set::control('hidden'),
    );

    $storyDescItem = formBatchItem(
        set::name('storyDesc'),
        set::label(''),
        set('labelClass', 'hidden'),
        set::control('hidden'),
    );

    $storyPriItem = formBatchItem(
        set::name('storyPri'),
        set::label(''),
        set('labelClass', 'hidden'),
        set::control('hidden'),
    );
}

/* Field of region and lane. */
$regionItem = '';
$laneItem   = '';
if($execution->type == 'kanban')
{
    $regionItem = formBatchItem(
        set::name('region'),
        set::label($lang->kanbancard->region),
        set::control('select'),
        set::value($regionID),
        set::items($regionPairs),
        set::width('160px'),
        set::ditto(true),
        set::required(true),
    );
    $laneItem = formBatchItem(
        set::name('lane'),
        set::label($lang->kanbancard->lane),
        set::control('select'),
        set::value($laneID),
        set::items($lanePairs),
        set::width('160px'),
        set::ditto(true),
        set::required(true),
    );
}


/* ====== Define the page structure with zin widgets ====== */

formBatchPanel
(
    set::title($lang->task->batchCreate),
    set::pasteField('name'),
    formBatchItem
    (
        set::name('id'),
        set::label($lang->idAB),
        set::control('index'),
        set::width('32px'),
    ),
    formBatchItem
    (
        set::name('module'),
        set::label($lang->task->module),
        set::control('select'),
        set::value($moduleID),
        set::items($modules),
        set::width('200px'),
        set::ditto(true),
    ),
    $storyItem,
    $previewItem,
    $copyStoryItem,
    $storyEstimateItem,
    $storyDescItem,
    $storyPriItem,
    formBatchItem
    (
        set::name('name'),
        set::label($lang->task->name),
        set::width('240px'),
    ),
    $regionItem,
    $laneItem,
    formBatchItem
    (
        set::name('type'),
        set::label($lang->task->type),
        set::control('select'),
        set::items($lang->task->typeList),
        set::width('160px'),
        set::ditto(true),
    ),
    formBatchItem
    (
        set::name('assignedTo'),
        set::label($lang->task->assignedTo),
        set::control('select'),
        set::items($members),
        set::width('128px'),
        set::ditto(true),
    ),
    formBatchItem
    (
        set::name('pri'),
        set::label($lang->task->pri),
        set::control('select'),
        set::value(3),
        set::items($lang->task->priList),
        set::width('80px'),
    ),
    formBatchItem
    (
        set::name('estimate'),
        set::label($lang->task->estimateAB),
        set::width('64px'),
        set::control
        (
            array(
                'type' => 'inputControl',
                'suffix' => $lang->task->suffixHour,
                'suffixWidth' => 20
            )
        )
    ),
    formBatchItem
    (
        set::name('estStarted'),
        set::label($lang->task->estStarted),
        set::control('date'),
        set::width('128px'),
        set::ditto(true),
    ),
    formBatchItem
    (
        set::name('deadline'),
        set::label($lang->task->deadline),
        set::control('date'),
        set::width('128px'),
        set::ditto(true),
    ),
    formBatchItem
    (
        set::name('desc'),
        set::label($lang->task->desc),
        set::control('textarea'),
        set::width('240px'),
    ),
    to::headingActions
    (
        checkbox
        (
            set::id('zeroTaskStory'),
            set::text($lang->story->zeroTask),
            on::change('toggleZeroTaskStory'),
        ),
    ),
    on::change('[data-name="module"]', 'setStories'),
    on::change('[data-name="story"]', 'setStoryRelated'),
    on::click('[data-name="copyStory"]', 'copyStoryTitle'),
    on::change('[data-name="region"]', 'loadLanes'),
);

/* ====== Render page ====== */
render();
