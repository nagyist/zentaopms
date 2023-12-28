<?php
declare(strict_types=1);
/**
 * The edit view file of testtask module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     testtask
 * @link        https://www.zentao.net
 */
namespace zin;

formPanel
(
    on::change('#execution', 'loadExecutionRelated'),
    input
    (
        set::width('1/2'),
        set::className('hidden'),
        set::name('product'),
        set::value($productID)
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->execution),
        set::name('execution'),
        set::value($task->execution),
        set::className(((!empty($project) && !$project->multiple) || ($app->tab == 'execution' && $task->execution)) ? 'hidden' : ''),
        set::control('picker'),
        set::items($executions)
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->build),
        set::required(true),
        set::name('build'),
        set::value($task->build),
        set::control('picker'),
        set::items($builds),
        on::change('setExecutionByBuild')
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->type),
        set::name('type[]'),
        set::value($task->type),
        set::control('picker'),
        set::items($lang->testtask->typeList),
        set::multiple(true)
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->owner),
        set::name('owner'),
        set::value($task->owner),
        set::control('picker'),
        set::items($users)
    ),
    formgroup
    (
        set::width('1/2'),
        set::label($lang->testtask->members),
        picker
        (
            setid('members'),
            set::name('members[]'),
            set::items($users),
            set::value($task->members),
            set::multiple(true)
        )
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->begin),
        set::required(true),
        inputGroup
        (
            datePicker
            (
                set::id('beginDate'),
                set::name('begin'),
                set::required(true),
                set::value($task->begin),
                on::change('suitEndDate')
            ),
            $lang->testtask->end,
            datePicker
            (
                set::id('endDate'),
                set::name('end'),
                set::required(true),
                set::value($task->end)
            )
        )
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->status),
        set::name('status'),
        set::required(true),
        set::value($task->status),
        set::control('picker'),
        set::items($lang->testtask->statusList)
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->testtask->testreport),
        set::name('testreport'),
        set::value($task->testreport),
        set::control('picker'),
        set::items($testreports)
    ),
    formGroup
    (
        set::label($lang->testtask->name),
        set::required(true),
        inputGroup
        (
            input
            (
                set::name('name'),
                set::required(true),
                set::value($task->name)
            ),
            $lang->testtask->pri,
            priPicker
            (
                zui::width('80px'),
                set::name('pri'),
                set::items($lang->testtask->priList),
                set::value($task->pri)
            )
        )
    ),
    formGroup
    (
        set::label($lang->testtask->desc),
        set::required(strpos(",{$this->config->testtask->edit->requiredFields},", ",desc,") !== false),
        editor
        (
            set::name('desc'),
            html($task->desc)
        )
    ),
    formGroup
    (
        set::label($lang->comment),
        editor(set::name('comment'))
    ),
    formGroup
    (
        set::label($lang->testtask->files),
        upload()
    ),
    formGroup
    (
        set::label($lang->testtask->mailto),
        mailto(set::items($users), set::value($task->mailto))
    )
);

render();
