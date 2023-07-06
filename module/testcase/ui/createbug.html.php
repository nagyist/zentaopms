<?php
declare(strict_types=1);
/**
 * The create bug view file of testcase module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     testcase
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('resultsLink', createLink('testtask', 'results', "runID={$runID}&caseID={$caseID}&version={$version}") . '#casesResults');

modalHeader
(
    set::title($lang->testtask->createBug),
    set::entityText($case->title),
    set::entityID($case->id),
);

div
(
    setClass('main'),
    set::id('resultsContainer'),
    div
    (
        set::id('casesResults'),
    ),
);

render('modalDialog');
