<?php
namespace zin;

$mainBlocks = array();
foreach($longBlocks as $index => $block)
{
    $mainBlocks[] = 
    div
    (
        set('class', "panel rounded shadow ring-0 canvas {$block->block}" . (isset($block->params->color) ? 'panel-' . $block->params->color : '')),
        set('data-id', $block->id),
        set('data-name', $block->title),
        set('data-order', $block->order),
        set('data-url', $block->blockLink),
        div
        (
            set('class', 'panel-heading'),
            div
            ( 
                set('class', 'panel-title'),
                $block->title
            ),
            nav
            ( 
                set('class', 'panel-actions nav nav-default'),
                dropdown
                (
                    icon
                    (
                        set('class', 'icon icon-ellipsis-v')
                    ),
                    set::items
                    ([
                        ['text' => $lang->block->refresh, 'url' => '', 'class' => 'refresh-panel'],
                        ['text' => $lang->edit, 'url' => $this->createLink("block", "admin", "id=$block->id&module=$module"), 'class' => 'refresh-panel'],
                        ['text' => $lang->block->hidden, 'url' => '', 'class' => 'refresh-panel'],
                        ['text' => $lang->block->createBlock, 'url' => $this->createLink("block", "admin", "id=0&module=$module"), 'data-toggle' => 'modal'],
                        ['text' => $lang->block->reset, 'url' => '', 'class' => 'refresh-panel'],
                    ]), 
                )
            )
        ),
        div
        (
            set('class', 'panel-body scrollbar-hover')
        )
    );
}

$sideBlocks = array();
foreach($shortBlocks as $index => $block)
{
    $sideBlocks[] = 
    div
    (
        set('class', "panel rounded shadow ring-0 canvas {$block->block}" . (isset($block->params->color) ? 'panel-' . $block->params->color : '')),
        set('data-id', $block->id),
        set('data-name', $block->title),
        set('data-order', $block->order),
        set('data-url', $block->blockLink),
        div
        (
            set('class', 'panel-heading'),
            div
            ( 
                set('class', 'panel-title'),
                $block->title
            ),
            nav
            ( 
                set('class', 'panel-actions nav nav-default'),
                dropdown
                (
                    icon
                    (
                        set('class', 'icon icon-ellipsis-v')
                    ),
                    set::items
                    ([
                        ['text' => $lang->block->refresh, 'url' => '', 'class' => 'refresh-panel'],
                        ['text' => $lang->edit, 'url' => $this->createLink("block", "admin", "id=$block->id&module=$module"), 'class' => 'refresh-panel'],
                        ['text' => $lang->block->hidden, 'url' => '', 'class' => 'refresh-panel'],
                        ['text' => $lang->block->createBlock, 'url' => $this->createLink("block", "admin", "id=0&module=$module"), 'data-toggle' => 'modal'],
                        ['text' => $lang->block->reset, 'url' => '', 'class' => 'refresh-panel'],
                    ]), 
                )
            )
        ),
        div
        (
            set('class', 'panel-body scrollbar-hover')
        )
    );
}

div
(
    set('class', 'dashboard'),
    set('id', 'dashboard'),
    div
    (
        set('class', 'row'),
        div
        (
            set('class', 'col-main'),
            $mainBlocks
        ),
        div
        (
            set('class', 'col-side'),
            $sideBlocks
        )
    )
);

render();
