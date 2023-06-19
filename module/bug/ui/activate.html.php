<?php
declare(strict_types=1);
/**
 * The activate view file of bug module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Gang Liu <liugang@easycorp.ltd>
 * @package     bug
 * @link        https://www.zentao.net
 */
namespace zin;

include 'common.html.php';

formPanel
(
    setCommonProps($bug) ,
    formGroup
    (
        set::width('1/3'),
        set::label($lang->bug->assignedTo),
        select
        (
            set::name('assignedTo'),
            set::value($bug->resolvedBy),
            set::items($users),
        )
    ),
    formGroup
    (
        set::width('1/2'),
        set::label($lang->bug->openedBuild),
        select
        (
            set::name('openedBuild[]'),
            set::value($bug->openedBuild),
            set::items($builds),
            set::multiple(true)
        ),
        input
        (
            set::name('status'),
            set::value('active'),
            set::class('hidden')
        )
    ),
    formGroup
    (
        set::label($lang->comment),
        editor
        (
            set::name('comment'),
            set::rows(6)
        )
    ),
    formGroup
    (
        set::label($lang->bug->files),
        fileinput
        (
            set::name('files[]')
        )
    )
);

history();

render('modalDialog');
