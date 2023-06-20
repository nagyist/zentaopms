<?php
declare(strict_types=1);
/**
* The UI file of stakeholder module of ZenTaoPMS.
*
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      chen.tao <chentao@easycorp.ltd>
* @package     stakeholder
* @link        https://www.zentao.net
*/

namespace zin;

detailHeader
(
    to::prefix
    (
        !isonlybody() ? backBtn($lang->goback, setClass('secondary'), set::icon('back'), set::back('stackholder-browse')) : null,
        div(setClass('nav-divider')),
        entityLabel
        (
            set::entityID($user->id),
            set::text($user->name),
            set::level(1)
        ),
        $user->deleted ? span(setClass('label danger circle'), $lang->stakeholder->deleted) : null
    ),
    hasPriv('stakeholder', 'create') ? to::suffix(btn
    (
        setClass('primary'),
        set::icon('plus'),
        set::url(createLink('stakeholder', 'create', "projectID=$user->objectID")),
        $lang->stakeholder->create
    )) : null
);

detailBody
(
    sectionList
    (
        section
        (
            set::title($lang->stakeholder->nature),
            set::content(!empty($user->nature) ? $user->nature : $lang->noDesc),
            set::useHtml(true)
        ),
        section
        (
            set::title($lang->stakeholder->analysis),
            set::content(!empty($user->analysis) ? $user->analysis : $lang->noDesc),
            set::useHtml(true)
        ),
        section
        (
            set::title($lang->stakeholder->strategy),
            set::content(!empty($user->strategy) ? $user->strategy : $lang->noDesc),
            set::useHtml(true)
        ),
    ),
    /* Expect content. */
    array_map(function($expect) use ($lang)
    {
        return sectionList
        (
            section
            (
                set::title($lang->stakeholder->expect . "($expect->createDate)"),
                set::content(!empty($expect->expect) ? $expect->expect : $lang->noDesc),
                set::useHtml(true)
            ),
            section
            (
                set::title($lang->stakeholder->progress . "($expect->createDate)"),
                set::content(!empty($expect->progress) ? $expect->progress : $lang->noDesc),
                set::useHtml(true)
            ),
        );
    }, $expects),
    /* Histories. */
    history(),
    /* Basic information of right side. */
    detailSide(tabs(tabPane
    (
        set::title($lang->stakeholder->basicInfo),
        set::active(true),
        tableData
        (
            item(set::name($lang->stakeholder->name),    $user->name),
            item(set::name($lang->stakeholder->type),    zget($lang->stakeholder->typeList, $user->type, '')),
            item(set::name($lang->stakeholder->company), empty($user->companyName) ? $lang->noData :$user->companyName),
            item(set::name($lang->stakeholder->phone),   empty($user->phone)       ? $lang->noData :$user->phone),
            item(set::name($lang->stakeholder->qq),      empty($user->qq)          ? $lang->noData :$user->qq),
            item(set::name($lang->stakeholder->weixin),  empty($user->weixin)      ? $lang->noData :$user->weixin),
            item(set::name($lang->stakeholder->email),   empty($user->email)       ? $lang->noData :$user->email),
            item(set::name($lang->stakeholder->isKey),   zget($lang->stakeholder->keyList, $user->key, '')),
            item(set::name($lang->stakeholder->from),    zget($lang->stakeholder->fromList, $user->from, '')),
        )
    )))
);

render();
