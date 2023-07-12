<?php
namespace zin;

jsVar('model', $model);
jsVar('longTime', $lang->project->longTime);
jsVar('weekend', $config->execution->weekend);
jsVar('beginLetterParent', $lang->project->beginLetterParent);
jsVar('endGreaterParent', $lang->project->endGreaterParent);
jsVar('ignore', $lang->project->ignore);
jsVar('multiBranchProducts', $multiBranchProducts);
jsVar('errorSameProducts', $lang->project->errorSameProducts);
jsVar('copyProjectID', $copyProjectID);
jsVar('nameTips', $lang->project->copyProject->nameTips);
jsVar('codeTips', $lang->project->copyProject->codeTips);
jsVar('endTips', $lang->project->copyProject->endTips);
jsVar('daysTips', $lang->project->copyProject->daysTips);
jsVar('programTip', $lang->program->tips);

$projectModelItems = array();
foreach($lang->project->modelList as $key => $text)
{
    if(empty($key)) continue;

    $projectModelItems[] = array
    (
        'active'    => ($key == $model),
        'url'       => $this->createLink("project", "create", "model=$key&programID=$programID&copyProjectID=0&extra=productID=$productID,branchID=$branchID"),
        'text'      => $text,
        'data-type' => 'ajax'
    );
}

$productsBox = array();
if(!empty($products))
{
    $i = 0;
    foreach($products as $product)
    {
        $hasBranch = $product->type != 'normal' && isset($branchGroups[$product->id]);
        $branches  = isset($branchGroups[$product->id]) ? $branchGroups[$product->id] : array();
        $plans     = isset($copyProject->productPlans[$product->id]) ? $copyProject->productPlans[$product->id] : array();
        $productsBox[] = formRow
        (
            setClass('productBox'),
            formGroup
            (
                set::width($hasBranch ? '1/4' : '1/2'),
                set('id', 'linkProduct'),
                set::required(true),
                $i == 0 ? set::label($lang->project->manageProducts) : set::label(''),
                inputGroup
                (
                    div
                    (
                        setClass('grow'),
                        select
                        (
                            set::name("products[$i]"),
                            set::value($product->id),
                            set::items($allProducts),
                            set::last($product->id),
                            set::required(true),
                            on::change('productChange')
                        )
                    ),
                )
            ),
            formGroup
            (
                set::width('1/4'),
                setClass('ml-px'),
                $hasBranch ? null : setClass('hidden'),
                inputGroup
                (
                    $lang->product->branchName['branch'],
                    select
                    (
                        set::name("branch[$i][]"),
                        set::items($branches),
                        set::value(implode(',', $product->branches)),
                        on::change('branchChange')
                    )
                ),
            ),
            formGroup
            (
                set::width('1/2'),
                inputGroup
                (
                    set::id("plan{$i}"),
                    $lang->project->associatePlan,
                    select
                    (
                        set::name("plans[$product->id][]"),
                        set::items($plans),
                        set::value($product->plans)
                    )
                ),
                div
                (
                    setClass('pl-2 flex self-center line-btn'),
                    btn
                    (
                        setClass('btn ghost addLine'),
                        on::click('addNewLine'),
                        icon('plus')
                    ),
                    btn
                    (
                        setClass('btn ghost removeLine'),
                        icon('trash'),
                        on::click('removeLine'),
                        $i == 0 ? set::disabled(true) : null
                    ),
                )
            ),
        );

        $i ++;
    }
}

$currency   = $parentProgram ? $parentProgram->budgetUnit : $config->project->defaultCurrency;
$labelClass = $config->project->labelClass[$model];
formPanel
(
    to::heading(div
    (
        setClass('panel-title text-lg'),
        $title,
        dropdown
        (
            btn
            (
                set::id('project-model'),
                setClass("$labelClass h-5 px-2"),
                zget($lang->project->modelList, $model, '')
            ),
            set::trigger('click'),
            set::placement('bottom'),
            set::menu(array('style' => array('color' => 'var(--color-fore)'))),
            set::items($projectModelItems)
        )
    )),
    to::headingActions
    (
        div
        (
            setClass('flex mr-5'),
            icon('cog-outline')
        ),
        btn
        (
            setClass('primary-pale'),
            set::icon('copy'),
            set::url('#copyProjectModal'),
            set('data-destoryOnHide', true),
            set('data-toggle', 'modal'),
            $lang->project->copy
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::name('parent'),
            set::label($lang->project->parent),
            set::items($programList),
            set::value($programID),
            on::change('setParentProgram')
        ),
        formGroup
        (
            set::width('1/2'),
            div
            (
                setClass('pl-2 flex self-center'),
                setStyle(['color' => 'var(--form-label-color)']),
                icon
                (
                    'help',
                    set('data-toggle', 'tooltip'),
                    set('id', 'programHover'),
                )
            )
        ),
        formGroup
        (
            set::name('model'),
            set::value($model),
            set::control('hidden'),
        )
    ),
    formGroup
    (
        set::width('1/2'),
        set::name('name'),
        set::label($lang->project->name),
        set::value($copyProjectID ? $copyProject->name : ''),
        set::strong(true),
    ),
    (!isset($config->setCode) or $config->setCode == 1) ? formGroup
    (
        set::width('1/2'),
        set::name('code'),
        set::label($lang->project->code),
        set::value($copyProjectID ? $copyProject->code : ''),
        set::strong(true),
    ) : null,
    (in_array($model, array('scrum', 'kanban'))) ? formGroup
    (
        set::width('1/2'),
        set::name('multiple'),
        set::label($lang->project->multiple),
        set::control(array('type' => 'radioList', 'inline' => true)),
        set::items($lang->project->multipleList),
        set::disabled($copyProjectID),
        set::value('1'),
        on::change('toggleMultiple'),
        $copyProjectID ? formHidden('multiple', $copyProject->multiple) : null,
    ) : null,
    formGroup
    (
        set::width('1/2'),
        set::label($lang->project->type),
        inputGroup
        (
            set::seg(true),
            btn
            (
                setClass('primary-pale project-type-1'),
                on::click('changeType(1)'),
                set::disabled($copyProjectID),
                $lang->project->projectTypeList[1]
            ),
            btn(
                setClass('project-type-0'),
                on::click('changeType(0)'),
                set::disabled($copyProjectID),
                $lang->project->projectTypeList[0]
            )
        ),
        formHidden('hasProduct', $copyProjectID ? $copyProject->hasProduct : 1)
    ),
    formGroup
    (
        set::width('1/4'),
        set::name('PM'),
        set::label($lang->project->PM),
        set::items($pmUsers)
    ),
    formRow
    (
        formGroup
        (
            set::width('1/4'),
            set::name('budget'),
            set::label($lang->project->budget),
            set::control(array
            (
                'type'        => 'inputControl',
                'prefix'      => zget($lang->project->currencySymbol, $currency),
                'prefixWidth' => 'icon',
                'suffix'      => $lang->project->tenThousandYuan,
                'suffixWidth' => 60,
            )),
            $parentProgram ? null : formHidden('budgetUnit', $config->project->defaultCurrency)
        ),
        formGroup
        (
            set::width('1/4'),
            set::name('future'),
            setClass('items-center'),
            set::control(array('type' => 'checkList', 'inline' => true)),
            set::items(array('1' => $lang->project->future)),
            on::change('toggleBudget')
        )
    ),
    formRow
    (
        formGroup
        (
            set::width('1/2'),
            set::label($lang->project->planDate),
            set::required(true),
            inputGroup
            (
                input
                (
                    set::name('begin'),
                    set::type('date'),
                    set('id', 'begin'),
                    set::value(date('Y-m-d')),
                    set::placeholder($lang->project->begin),
                    set::required(true),
                    on::change('computeWorkDays')
                ),
                $lang->project->to,
                input
                (
                    set::name('end'),
                    set('id', 'end'),
                    set::type('date'),
                    set::placeholder($lang->project->end),
                    set::required(true),
                    on::change('computeWorkDays')
                ),
            )
        ),
        formGroup
        (
            set::width('1/2'),
            setClass('items-center'),
            radioList
            (
                on::change('setDate'),
                set::name('delta'),
                set::inline(true),
                set::items($lang->project->endList)
            )
        ),
    ),
    formGroup
    (
        set::label($lang->project->days),
        set::width('1/4'),
        inputGroup
        (
            setClass('has-suffix'),
            input
            (
                set::name('days'),
                set::required(true),
            ),
            div
            (
                setClass('input-control-suffix z-50'),
                $lang->project->day
            )
        )
    ),
    $products ? $productsBox :
    formRow
    (
        setClass('productBox'),
        formGroup
        (
            set::width('1/2'),
            set('id', 'linkProduct'),
            set::label($lang->project->manageProducts),
            set::required(true),
            inputGroup
            (
                div
                (
                    setClass('grow'),
                    picker
                    (
                        set::name('products[0]'),
                        set::items($allProducts),
                        on::change('productChange')
                    )
                ),
                div
                (
                    setClass('flex items-center pl-2'),
                    checkbox
                    (
                        set::name('newProduct'),
                        set::text($lang->project->newProduct),
                        on::change('addProduct')
                    )
                )
            )
        ),
        formGroup
        (
            set::width('1/4'),
            setClass('hidden'),
            inputGroup
            (
                $lang->product->branchName['branch'],
                picker
                (
                    set::name("branch[0][]"),
                    on::change('branchChange')
                )
            ),
        ),
        formGroup
        (
            set::width('1/2'),
            inputGroup
            (
                set::id("plan0"),
                $lang->project->associatePlan,
                picker
                (
                    set::name('plans[0][]'),
                    set::items(null),
                )
            ),
            div
            (
                setClass('pl-2 flex self-center line-btn'),
                btn
                (
                    setClass('btn ghost addLine'),
                    on::click('addNewLine'),
                    icon('plus')
                ),
                btn
                (
                    setClass('btn ghost removeLine'),
                    icon('trash'),
                    on::click('removeLine'),
                    $i == 0 ? set::disabled(true) : null
                ),
            )
        ),
    ),
    formRow
    (
        set::id('addProductBox'),
        setClass('hidden'),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->project->addProduct),
            set::required(true),
            inputGroup
            (
                div
                (
                    setClass('grow'),
                    input(set::name('productName')),
                ),
                div
                (
                    setClass('flex items-center pl-2'),
                    checkbox
                    (
                        set::name('newProduct'),
                        set::text($lang->project->newProduct),
                        on::change('addProduct')
                    )
                )
            )
        )
    ),
    ($model == 'waterfall' || $model == 'waterfallplus') ? formRow
    (
        setClass('stageBy hidden'),
        formGroup
        (
            set::width('1/2'),
            set::label($lang->project->stageBy),
            inputGroup
            (
                set::seg(true),
                btn(
                    setClass('primary-pale project-stageBy-0'),
                    on::click('changeStageBy(0)'),
                    set::disabled($copyProjectID),
                    $lang->project->stageByList[0]
                ),
                btn
                (
                    setClass('project-stageBy-1'),
                    on::click('changeStageBy(1)'),
                    set::disabled($copyProjectID),
                    $lang->project->stageByList[1]
                ),
            ),
            formHidden('stageBy', $copyProjectID ? $copyProject->stageBy : '0')
        )
    ) : null,
    formGroup
    (
        set::name('desc'),
        set::label($lang->project->desc),
        set::control('editor'),
        set::placeholder($lang->project->editorPlaceholder)
    ),
    /* TODO printExtendFields() */
    formRow
    (
        set::id('aclList'),
        formGroup
        (
            set::width('1/2'),
            set::name('acl'),
            set::label($lang->project->acl),
            set::control('radioList'),
            $programID ? set::items($lang->project->subAclList) : set::items($lang->project->aclList),
            set::value($copyProjectID ? $copyProject->acl : 'private'),
        )
    ),
    /* TODO add events */
    formGroup
    (
        set::width('1/2'),
        set::label($lang->whitelist),
        picker
        (
            set::name('whitelist[]'),
            set::items($users),
            set::multiple(true)
        )
    ),
    formGroup
    (
        set::width('1/2'),
        set::name('auth'),
        set::label($lang->project->auth),
        set::control('radioList'),
        set::items($lang->project->authList),
        set::value($copyProjectID ? $copyProject->auth : 'extend')
    ),
);

$copyProjectsBox = array();
foreach($copyProjects as $id => $name)
{
    $copyProjectsBox[] = btn(
        setClass('project-block justify-start'),
        setClass($copyProjectID == $id ? 'primary-outline' : ''),
        set('data-id', $id),
        set('data-pinyin', zget($copyPinyinList, $name, '')),
        icon
        (
            setClass('text-gray'),
            $lang->icons['project']
        ),
        span($name),
    );
}

modalTrigger
(
    modal
    (
        set::id('copyProjectModal'),
        to::header
        (
            span
            (
                h4
                (
                    set::class('copy-title'),
                    $lang->project->copyTitle
                )
            ),
            input
            (
                set::name('projectName'),
                set::placeholder($lang->project->searchByName),
            ),
        ),
        div
        (
            set::id('copyProjects'),
            setClass('flex items-center flex-wrap'),
            $copyProjectsBox
        )
    )
);

render();
