#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/execution.unittest.class.php';

$execution = zenData('project');
$execution->id->range('1-5');
$execution->name->range('项目1,项目2,迭代1,迭代2,迭代3');
$execution->type->range('project{2},sprint{3}');
$execution->status->range('doing{3},closed,doing');
$execution->parent->range('0,0,1,1,2');
$execution->project->range('0,0,1,1,2');
$execution->grade->range('2{2},1{3}');
$execution->path->range('1,2,`1,3`,`1,4`,`2,5`')->prefix(',')->postfix(',');
$execution->begin->range('20230102 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->end->range('20230212 000000:0')->type('timestamp')->format('YY/MM/DD');
$execution->gen(5);

zenData('user')->gen(5);
zenData('team')->gen(0);
zenData('product')->gen(0);
zenData('userview')->gen(0);
su('admin');

/**

title=测试executionModel->updateUserView();
timeout=0
cid=1

*/

$execution = new executionTest();
r($execution->updateUserViewTest(5)) && p() && e('~f:5~'); // 默认情况下的用户是否有执行的可视权限
