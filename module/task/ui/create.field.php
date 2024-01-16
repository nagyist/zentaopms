<?php
namespace zin;
global $lang,$config;

$fields = defineFieldList('task.create', 'task');

$nameWidth = 'w-1/2';
if(empty(data('features.story'))) $nameWidth .= ' full:w-full';
if(data('execution.type') == 'kanban') $nameWidth .= ' lite:w-full';
$fields->field('name')->className($nameWidth);

$buildStoryBox = function($props)
{
    if(!empty(data('execution.hasProduct')))
    {
        $executionID      = data('execution.id');
        $storyEmptyPreTip = span
            (
                setClass('input-control-prefix'),
                span(data('lang.task.noticeLinkStory')),
                a
                (
                    set::href(createLink('execution', 'linkStory', "executionID={$executionID}")),
                    setClass('text-primary'),
                    isInModal() ? setData('toggle', 'modal') : null,
                    data('lang.execution.linkStory')
                )
            );
    }
    else
    {
        $storyEmptyPreTip = span
            (
                setClass('input-control-prefix'),
                span(data('lang.task.noticeLinkStoryNoProduct'))
            );
    }

    return div
        (
            inputGroup
            (
                setClass('setStoryBox'),
                setClass(empty(data('stories')) ? 'hidden' : ''),
                picker
                (
                    set::name('story'),
                    set::value(data('task.story')),
                    set::items(data('stories'))
                ),
                btn
                (
                    setID('preview'),
                    setClass('hidden'),
                    setData('toggle', 'modal'),
                    setData('url', '#'),
                    setData('size', 'lg'),
                    icon('eye text-gray')
                )
            ),
            inputGroup
            (
                setClass('empty-story-tip input-control has-prefix has-suffix'),
                setClass(empty(data('stories')) ? '' : 'hidden'),
                $storyEmptyPreTip,
                input
                (
                    set::name(''),
                    set('readonly'),
                    set('onfocus', 'this.blur()')
                ),
            )
        );
};

$fields->field('storyBox')
    ->label($lang->task->story)
    ->checkbox(array('text' => $lang->task->syncStory, 'name' => 'copyButton'))
    ->hidden(!data('features.story'))
    ->foldable(data('features.story'))
    ->control($buildStoryBox);

if(!isAjaxRequest('modal'))
{
    $fields->field('after')
        ->label($lang->task->afterSubmit)
        ->width('full')
        ->control(array('type' => 'radioList', 'inline' => true))
        ->value(data('task.id') ? 'continueAdding' : 'toTaskList')
        ->items(empty(data('features.story')) ? array('toTaskList' => $lang->task->afterChoices['toTaskList']) : $config->task->afterOptions);
}

$fields->field('storyEstimate')
    ->hidden()
    ->control('input');

$fields->field('storyDesc')
    ->hidden()
    ->control('input');

$fields->field('storyPri')
    ->hidden()
    ->control('input')
    ->value('0');

$fields->field('taskName')
    ->hidden()
    ->control('input');

$fields->field('taskEstimate')
    ->hidden()
    ->control('input');
