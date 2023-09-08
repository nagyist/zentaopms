#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/execution.class.php';
zdTable('user')->gen(5);
su('admin');

zdTable('project')->config('execution')->gen(30);

/**

title=测试executionModel->batchProcessName();
timeout=0
cid=1

*/

$projectIdList = array(11, 60, 100);
$countList     = array(0, 1);

$executionTester = new executionTest();
r($executionTester->batchProcessNameTest($projectIdList[0], $countList[0])) && p('2:project,name') && e('11,迭代7');   // 测试处理敏捷项目下的执行名称
r($executionTester->batchProcessNameTest($projectIdList[1], $countList[0])) && p('2:project,name') && e('60,阶段12');  // 测试处理瀑布项目下的执行名称
r($executionTester->batchProcessNameTest($projectIdList[2], $countList[0])) && p('2:project,name') && e('100,看板30'); // 测试处理看板项目下的执行名称
r($executionTester->batchProcessNameTest($projectIdList[0], $countList[1])) && p()                 && e('5');          // 测试获取敏捷项目下的执行数量
r($executionTester->batchProcessNameTest($projectIdList[1], $countList[1])) && p()                 && e('12');         // 测试获取瀑布项目下的执行数量
r($executionTester->batchProcessNameTest($projectIdList[2], $countList[1])) && p()                 && e('3');          // 测试获取看板项目下的执行数量
