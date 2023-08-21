<?php
namespace zin;

$parentID = $program->parent;
$currency = $parentID ? $parentProgram->budgetUnit : $config->project->defaultCurrency;
$aclList  = $parentID ? $lang->program->subAclList : $lang->program->aclList;

jsVar('longTime', $lang->project->longTime);
jsVar('weekend', $config->execution->weekend);
jsVar('page', 'edit');
jsVar('parentBudget', $lang->program->parentBudget);
jsVar('beginLessThanParent', $lang->program->beginLessThanParent);
jsVar('endGreatThanParent', $lang->program->endGreatThanParent);
jsVar('ignore', $lang->program->ignore);
jsVar('currencySymbol', $lang->project->currencySymbol);
jsVar('budgetOverrun', $lang->project->budgetOverrun);

set::title($parentID ? $lang->program->children : $lang->program->create);

formPanel
(
    to::heading
    (
        div
        (
            setClass('panel-title text-lg'),
            $title
        )
    ),
    on::change('#parent', 'onParentChange'),
    on::change('#budget', 'budgetOverrunTips'),
    on::change('#future', 'onFutureChange'),
    on::change('#acl',    'onAclChange'),
    formGroup
    (
        set::width('1/2'),
        set::name('parent'),
        set::label($lang->program->parent),
        set::value($parentID),
        set::items($parents),
    ),
    formGroup
    (
        set::width('1/2'),
        set::name('name'),
        set::strong(true),
        set::value($program->name),
        set::label($lang->program->name)
    ),
    formGroup
    (
        set::width('1/4'),
        set::name('PM'),
        set::label($lang->program->PM),
        set::value($program->PM),
        set::items($pmUsers)
    ),
    formRow
    (
        set::id('budgetRow'),
        formGroup
        (
            set::width('1/4'),
            set::name('budget'),
            set::label($lang->project->budget),
            $program->budget ? set::value($program->budget) : null,
            $program->budget == 0 ? set::disabled(true) : null,
            set::control(array
            (
                'type'        => 'inputControl',
                'prefix'      => zget($lang->project->currencySymbol, $currency),
                'prefixWidth' => 'icon',
                'suffix'      => $lang->project->tenThousandYuan,
                'suffixWidth' => 60,
            )),
        ),
        formGroup
        (
            set::width('1/4'),
            set::name('future'),
            set::class('items-center'),
            $program->budget == 0 ? set::value('1') : null,
            set::control(array('type' => 'checkList', 'inline' => true)),
            set::items(array('1' => $lang->project->future)),
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->project->dateRange),
            set::required(true),
            inputGroup
            (
                set::seg(true),
                input
                (
                    set::type('date'),
                    set::name('begin'),
                    set::id('begin'),
                    set::value(date('Y-m-d')),
                    set::placeholder($lang->project->begin),
                    set::required(true),
                    on::change('computeWorkDays')
                ),
                $lang->project->to,
                input
                (
                    set::type('date'),
                    set::name('end'),
                    set::id('end'),
                    set::placeholder($lang->project->end),
                    set::required(true),
                    $program->end == LONG_TIME ? set::disabled(true) : null,
                    on::change('computeWorkDays')
                ),
            )
        ),
        formGroup
        (
            set::name('delta'),
            set::class('pl-4 items-center'),
            set::control(array('type' => 'radioList', 'inline' => true, 'rootClass' => 'ml-4', 'items' => $lang->program->endList)),
            $program->end == LONG_TIME ? set::value(999) : null,
            on::change('setDate'),
        ),
    ),
    formGroup
    (
        set::name('desc'),
        set::label($lang->program->desc),
        set::control('editor')
    ),
    formGroup
    (
        set::name('acl'),
        set::label($lang->program->acl),
        set::value('open'),
        set::items($aclList),
        set::control('radioList'),
    ),
    formRow
    (
        set::id('whitelistRow'),
        setClass('hidden'),
        formGroup
        (
            set::width('3/4'),
            set::name('whitelist'),
            set::label($lang->whitelist),
            set::control('select')
        )
    )
);

render();
