<?php
declare(strict_types=1);
/**
 * The edit view file of testcase module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     testcase
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('tab', $this->app->tab);
jsVar('isLibCase', $isLibCase);
jsVar('caseBranch', $case->branch);

set::title($lang->testcase->edit);

$rootID   = $isCaseLib ? $libID : $productID;
$viewType = $isCaseLib ? 'caselib' : 'product';
$createModuleLink = createLink('tree', 'browse', "rootID={$rootID}&view={$viewType}&currentModuleID=0&branch={$case->branch}");

if($case->type != 'unit') unset($lang->testcase->typeList['unit']);

$linkCaseItems = array();
if(isset($case->linkCaseTitles))
{
    foreach($case->linkCaseTitles as $linkCaseID => $linkCaseTitle)
    {
        $linkCaseItems[] = array('text' => "#{$linkCaseID} {$linkCaseTitle}", 'value' => $linkCaseID, 'checked' => true);
    }
}

$linkBugItems = array();
if(isset($case->toBugs))
{
    foreach($case->toBugs as $bugID => $bug)
    {
        $linkBugItems[] = array('text' => "#{$bugID} {$bug->title}", 'value' => $bugID, 'checked' => true);
    }
}

detailHeader
(
    to::prefix(null),
    to::title
    (
        entityLabel
        (
            set::entityID($case->id),
            set::level(1),
            set::text($case->title),
            set::reverse(true),
        )
    ),
);

detailBody
(
    set::isForm(true),
    on::change('#lib', 'loadLibModules'),
    on::change('#product', 'loadProductRelated()'),
    on::change('#module', 'loadModuleRelated'),
    on::change('#branch', 'loadBranchRelated'),
    on::click('.refresh', $isCaseLib ? 'loadLibModules' : 'loadProductModules'),
    sectionList
    (
        section
        (
            set::title($lang->testcase->title),
            formGroup
            (
                set::name('title'),
                set::value($case->title),
                set::placeholder($lang->case->title),
            )
        ),
        section
        (
            set::title($lang->testcase->scene),
            formGroup
            (
                set::id('sceneIdBox'),
                select
                (
                    set::name('scene'),
                    set::items($sceneOptionMenu),
                    set::value($currentSceneID),
                )
            )
        ),
        section
        (
            set::title($lang->testcase->precondition),
            editor
            (
                set::name('precondition'),
                set::value($case->precondition),
                set::rows(2)
            )
        ),
        section
        (
            set::title($lang->testcase->steps),
        ),
        section
        (
            set::title($lang->files),
            $case->files ? fileList
            (
                set::files($case->files),
                set::fieldset(false),
                set::showEdit(true),
                set::showDelete(true),
            ) : null,
            formGroup
            (
                set::name('files[]'),
                set::control('file'),
            )
        ),
        section
        (
            set::title($lang->testcase->legendComment),
            editor
            (
                set::name('comment'),
                set::rows(5)
            )
        )
    ),
    history
    (
        set::actions($actions),
        set::users($users),
        set::methodName($methodName),
    ),
    detailSide
    (
        tableData
        (
            set::title($lang->testcase->legendBasicInfo),
            $isLibCase ? item
            (
                set::name($lang->testcase->lib),
                select
                (
                    set::name('lib'),
                    set::items($libraries),
                    set::value($libID),
                )
            ) : item
            (
                set::name($lang->testcase->product),
                set::hidden($product->shadow),
                inputGroup
                (
                    select
                    (
                        set::name('product'),
                        set::items($products),
                        set::value($productID),
                    ),
                    isset($product->type) && $product->type != 'normal' ? select
                    (
                        set::name('branch'),
                        set::items($branchTagOption),
                        set::value($case->branch),
                    ) : null,
                )
            ),
            item
            (
                set::name($lang->testcase->module),
                inputGroup
                (
                    set::id('moduleIdBox'),
                    select
                    (
                        set::name('module'),
                        set::items($moduleOptionMenu),
                        set::value($currentModuleID),
                    ),
                    count($moduleOptionMenu) == 1 ? span
                    (
                        set::class('input-group-btn flex'),
                        a
                        (
                            set::class('btn'),
                            set::url($createModuleLink),
                            set('data-toggle', 'modal'),
                            $lang->tree->manage,
                        ),
                        btn 
                        (
                            set::class('refresh'),
                            set::icon($lang->refreshIcon),
                        )
                    ) : null,
                ),
            ),
            !$isLibCase ? item
            (
                set::name($lang->testcase->story),
                div
                (
                    set::id('storyIdBox'),
                    select
                    (
                        set::name('story'),
                        set::items($stories),
                        set::value($case->story),
                    )
                )
            ) : null,
            item
            (
                set::name($lang->testcase->type),
                inputGroup
                (
                    select
                    (
                        set::name('type'),
                        set::items($lang->testcase->typeList),
                        set::value($case->type),
                        set::required(true)
                    ),
                    span
                    (
                        set::class('input-group-addon'),
                        control
                        (
                            set::type('checkbox'),
                            set::name('auto'),
                            set::text($lang->testcase->automated),
                            set::value($case->auto)
                        )
                    )
                )
            ),
            item
            (
                set::class('autoScript'),
                set::hidden(true),
                set::name($lang->testcase->autoScript),
            ),
            item
            (
                set::name($lang->testcase->stage),
                select
                (
                    set::name('stage[]'),
                    set::items($lang->testcase->stageList),
                    set::value($case->stage),
                    set::multiple(true)
                )
            ),
            item
            (
                set::name($lang->testcase->pri),
                select
                (
                    set::name('pri'),
                    set::items($lang->testcase->priList),
                    set::value($case->pri),
                )
            ),
            item
            (
                set::name($lang->testcase->status),
                !$forceNotReview && $case->status == 'wait' ? $lang->testcase->statusList[$case->status] : select
                (
                    set::name('status'),
                    set::items($lang->testcase->statusList),
                    set::value($case->status),
                )
            ),
            item
            (
                set::name($lang->testcase->keywords),
                input
                (
                    set::name('keywords'),
                    set::value($case->keywords)
                )
            ),
            ($isLibCase && hasPriv('testcase', 'linkCases')) ? item
            (
                set::name($lang->testcase->linkCase),
                a
                (
                    set::href(createLink('testcase', 'linkCases', "caseID={$case->id}")),
                    set('data-toggle', 'modal'),
                    $lang->testcase->linkCases,
                )
            ) : null,
            ($isLibCase && hasPriv('testcase', 'linkCases')) ? item
            (
                set::hidden(!isset($case->linkCaseTitles)),
                control
                (
                    set::type('checkList'),
                    set::name('linkCase[]'),
                    set::value(array_keys($case->linkCaseTitles)),
                    set::items($linkCaseItems),
                ),
                span
                (
                    set::id('linkCaseBox'),
                )
            ) : null,
            ($isLibCase && hasPriv('testcase', 'linkBugs')) ? item
            (
                set::name($lang->testcase->linkBug),
                a
                (
                    set::href(createLink('testcase', 'linkBugs', "caseID={$case->id}")),
                    set('data-toggle', 'modal'),
                    $lang->testcase->linkBugs,
                )
            ) : null,
            ($isLibCase && hasPriv('testcase', 'linkBugs')) ? item
            (
                set::hidden(!isset($case->toBugs)),
                control
                (
                    set::type('checkList'),
                    set::name('linkBug[]'),
                    set::value(array_keys($case->toBugs)),
                    set::items($linkBugItems),
                ),
                span
                (
                    set::id('linkBugBox'),
                )
            ) : null,
        ),
        tableData
        (
            set::title($lang->testcase->legendOpenAndEdit),
            item
            (
                set::name($lang->testcase->openedBy),
                zget($users, $case->openedBy) . $lang->at . $case->openedDate,
            ),
            item
            (
                set::name($lang->testcase->lblLastEdited),
                $case->lastEditedBy ?  zget($users, $case->lastEditedBy) . $lang->at . $case->lastEditedDate : null,
            )
        )
    )
);

render();

