#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/program.unittest.class.php';
su('admin');

$program = zenData('project');
$program->id->range('1,2');
$program->name->range('父项目集1,父项目集2');
$program->type->range('program');
$program->budget->range('900000,899900');
$program->path->range('1,2')->prefix(',')->postfix(',');
$program->begin->range('20220112 000000:0')->type('timestamp')->format('YYYY-MM-DD');
$program->end->range('20220212 000000:0')->type('timestamp')->format('YYYY-MM-DD');
$program->gen(2);

/**

title=测试 programModel::getBudgetLeft();
cid=1
pid=1

查看项目集1的所有父项目集的预算剩余 >> 900000
查看项目集2的所有父项目集的预算剩余 >> 899900

*/

$programTester = new programTest();
$result1       = $programTester->getBudgetLeftTest(1);
$result2       = $programTester->getBudgetLeftTest(2);

r($result1) && p() && e('900000');  // 查看项目集1的所有父项目集的预算剩余
r($result2) && p() && e('899900');  // 查看项目集2的所有父项目集的预算剩余
