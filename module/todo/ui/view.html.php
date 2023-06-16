<?php
declare(strict_types=1);
/**
 * The view view file of todo module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     todo
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('selectProduct',   $lang->todo->selectProduct);
jsVar('selectExecution', $lang->execution->selectExecution);
jsVar('todoID',          $todo->id);

$isInModal = isAjaxRequest('modal');

detailHeader
(
    $isInModal ? to::prefix('') : '',
    to::title
    (
        entityLabel
        (
            set::entityID(17),
            set::level(1),
            set::text($todo->name)
        )
    ),
);

$fromItem     = '';
$fromItemData = '';
if(in_array($todo->type, array('story', 'task', 'bug')) && !empty($todo->object))
{
    $app->loadLang($todo->type);
    $objectData = array();
    if($todo->type == 'story')
    {
        $objectData[] = item
        (
            set::title($lang->story->legendSpec),
            empty($todo->object->spec) ? $lang->noData : html($todo->object->spec)
        );
        $objectData[] = item
        (
            set::title($lang->story->legendVerify),
            empty($todo->object->verify) ? $lang->noData : html($todo->object->verify)
        );
    }
    if($todo->type == 'task')
    {
        $objectData[] = item
        (
            set::title($lang->task->legendDesc),
            empty($todo->object->desc) ? $lang->noData : html($todo->object->desc)
        );
    }
    if($todo->type == 'bug')
    {
        $objectData[] = item
        (
            set::title($lang->bug->legendSteps),
            empty($todo->object->steps) ? $lang->noData : html($todo->object->steps)
        );
    }
    $fromItemData = section
    (
        set::title(zget($lang->todo->fromList, $todo->type)),
        sectionCard
        (
            entityLabel
            (
                set::entityID($todo->objectID),
                set::text($todo->name),
            ),
            $objectData,
        ),
    );
    $fromItem = item
    (
        set::name(zget($lang->todo->fromList, $todo->type)),
        a
        (
            set::href(createLink($todo->type, 'view', "id={$todo->objectID}", '', false)),
            set('data-toggle', 'modal'),
            set('data-data-type', 'html'),
            set('data-type', 'ajax'),
            $todo->name,
        ),
    );
}

$actionList = array();
if($todo->status == 'wait' && common::hasPriv('todo', 'start'))
{
    $actionList[] = array
    (
        'icon' => 'play',
        'url'  => createLink('todo', 'start', "todoID={$todo->id}"),
        'text' => $lang->todo->abbr->start
    );
}
if(($todo->status == 'done' || $todo->status == 'closed') && common::hasPriv('todo', 'activate'))
{
    $actionList[] = array
    (
        'icon' => 'magic',
        'url'  => createLink('todo', 'activate', "todoID={$todo->id}"),
        'text' => $lang->activate
    );
}
if($todo->status == 'done' && common::hasPriv('todo', 'close'))
{
    $actionList[] = array
    (
        'icon' => 'off',
        'url'  => createLink('todo', 'close', "todoID={$todo->id}"),
        'text' => $lang->close
    );
}
if(common::hasPriv('todo', 'edit'))
{
    $actionList[] = array
    (
        'icon' => 'edit',
        'url'  => createLink('todo', 'edit', "todoID={$todo->id}"),
        'text' => $lang->edit
    );
}
if(common::hasPriv('todo', 'delete'))
{
    $actionList[] = array
    (
        'icon' => 'trash',
        'url'  => createLink('todo', 'delete', "todoID={$todo->id}"),
        'text' => $lang->delete
    );
}
if($todo->status != 'done' && $todo->status != 'closed')
{
    $actionList[] = array
    (
        'icon' => 'checked',
        'url'  => createLink('todo', 'finish', "todoID={$todo->id}"),
        'text' => $lang->todo->abbr->finish
    );
    $createStoryPriv = common::hasPriv('story', 'create');
    $createTaskPriv  = common::hasPriv('task', 'create');
    $createBugPriv   = common::hasPriv('bug', 'create');
    $printBtn        = $config->vision == 'lite' && empty($projects);
    if($printBtn && ($createStoryPriv || $createTaskPriv || $createBugPriv))
    {
        $actionList[] = array
        (
            'url'            => '#navActions',
            'text'           => $lang->more,
            'data-toggle'    => 'dropdown',
            'data-placement' => 'top-end',
        );
    }
    $storyTarget = $createStoryPriv && $config->vision == 'lite' ? '#projectModal' : '#productModal';
    menu
    (
        set::id('navActions'),
        setClass('menu dropdown-menu'),
        set::items(array
        (
            array('text' => $lang->todo->reasonList['story'],  'id' => 'toStoryLink', 'data-url' => '###', 'data-toggle' => 'modal', 'data-target' => $storyTarget, 'data-backdrop' => false, 'data-moveable' => true, 'data-position' => 'center'),
            array('text' => $lang->todo->reasonList['task'],  'id' => 'toTaskLink', 'data-url' => '###', 'data-toggle' => 'modal', 'data-target' => '#executionModal', 'data-backdrop' => false, 'data-moveable' => true, 'data-position' => 'center'),
            array('text' => $lang->todo->reasonList['bug'],  'id' => 'toBugLink', 'data-url' => '###', 'data-toggle' => 'modal', 'data-target' => '#projectProductModal', 'data-backdrop' => false, 'data-moveable' => true, 'data-position' => 'center'),
        ))
    );
    modal
    (
        setID('productModal'),
        set::modalProps(array('title' => $lang->product->select)),
        empty($products) ? div
        (
            setClass('text-center', 'pb-8'),
            span($lang->product->noProduct),
            btn
            (
                $lang->product->create,
                set
                (
                    array(
                        'url'   => createLink('product', 'create'),
                        'id'    => 'createProduct',
                        'class' => 'secondary-pale',
                        'icon'  => 'plus'
                    )
                )
            )
        ) : form
        (
            formGroup
            (
                inputGroup
                (
                    select
                    (
                        on::change('getProgramByProduct(this)'),
                        set
                        (
                            array(
                                'id'       => 'product',
                                'class'    => 'form-control',
                                'name'     => 'product',
                                'items'    => $products,
                                'required' => true
                            )
                        )
                    ),
                    input
                    (
                        set::type('hidden'),
                        set::name('productProgram'),
                        set::value(0)
                    ),
                    btn
                    (
                        on::click('toStory'),
                        set
                        (
                            array(
                                'id'    => 'toStoryButton',
                                'class' => 'primary',
                                'text'  => $lang->todo->reasonList['story']
                            )
                        )
                    )
                )
            ),
            set::actions(array()),
            setClass('pb-6')
        ),
    );
    modal
    (
        setID('executionModal'),
        set::modalProps(array('title' => $lang->execution->selectExecution)),
        form
        (
            setClass('text-center', 'pb-4'),
            set::actions(array()),
            formGroup
            (
                set::label($lang->todo->project),
                select
                (
                    on::change('getExecutionByProject(this)'),
                    set
                    (
                        array(
                            'id'       => 'project',
                            'name'     => 'project',
                            'items'    => $projects,
                            'required' => true
                        )
                    )
                )
            ),
            formGroup
            (
                set::label($lang->todo->execution),
                select
                (
                    set
                    (
                        array(
                            'id'       => 'execution',
                            'name'     => 'execution',
                            'items'    => $executions,
                            'required' => true
                        )
                    )
                )
            ),
            btn
            (
                $lang->todo->reasonList['task'],
                on::click('toTask'),
                set
                (
                    array(
                        'id'    => 'toTaskButton',
                        'class' => array('primary', 'text-center')
                    )
                )
            )
        )
    );

    modal
    (
        setID('projectProductModal'),
        set::modalProps(array('title' => $lang->product->select)),
        form
        (
            setClass('text-center', 'pb-4'),
            set::actions(array()),
            formGroup
            (
                set::label($lang->todo->project),
                select
                (
                    on::change('getProductByProject(this)'),
                    set
                    (
                        array(
                            'id'       => 'bugProject',
                            'name'     => 'bugProject',
                            'items'    => $projects,
                            'required' => true
                        )
                    )
                )
            ),
            formGroup
            (
                set::label($lang->todo->product),
                select
                (
                    set
                    (
                        array(
                            'id'       => 'bugProduct',
                            'name'     => 'bugProduct',
                            'items'    => $projectProducts,
                            'required' => true
                        )
                    )
                )
            ),
            btn
            (
                $lang->todo->reasonList['bug'],
                on::click('toBug'),
                set
                (
                    array(
                        'id'    => 'toBugButton',
                        'class' => array('primary', 'text-center'),
                        // 'data-backdrop' => false,
                        'data-toggle' => 'modal',
                        // 'data-type' => 'html',
                    )
                )
            )
        )
    );
}

detailBody
(
    sectionList
    (
        section
        (
            set::title($lang->todo->desc),
            set::content(nl2br($todo->desc)),
            set::useHtml(true),
        ),
        $fromItemData,
        history(),
        center
        (
            floatToolbar
            (
                set::main($actionList),
            ),
        ),
    ),
    detailSide
    (
        tabs
        (
            tabPane
            (
                set::key('legendBasic'),
                set::title($lang->todo->legendBasic),
                set::active(true),
                tableData
                (
                    item
                    (
                        set::name($lang->todo->pri),
                        priLabel(zget($lang->todo->priList, $todo->pri)),
                    ),
                    item
                    (
                        set::name($lang->todo->status),
                        zget($lang->todo->statusList, $todo->status),
                    ),
                    item
                    (
                        set::name($lang->todo->type),
                        zget($lang->todo->typeList, $todo->type),
                    ),
                    $fromItem,
                    item
                    (
                        set::name($lang->todo->account),
                        zget($users, $todo->account),
                    ),
                    item
                    (
                        set::name($lang->todo->date),
                        $todo->date,
                    ),
                    item
                    (
                        set::name($lang->todo->beginAndEnd),
                        isset($times[$todo->begin]) ? $times[$todo->begin] : '',
                        isset($times[$todo->end]) ?  ' ~ ' . $times[$todo->end] : '',
                    ),
                    item
                    (
                        set::name($lang->todo->assignTo),
                        zget($users, $todo->assignedTo),
                    ),
                    item
                    (
                        set::name($lang->todo->assignedDate),
                        $todo->assignedDate,
                    ),
                ),
            ),
        ),
    ),
);

render($isInModal ? 'modalDialog' : 'page');

