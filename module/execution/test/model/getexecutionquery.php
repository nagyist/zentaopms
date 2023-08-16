#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/execution.class.php';
zdTable('user')->gen(5);
su('admin');

$userquery = zdTable('userquery');
$userquery->id->range(1);
$userquery->account->range('admin');
$userquery->module->range('execution');
$userquery->title->range('搜索进行中的迭代');
$userquery->sql->range("`(( 1   AND `name`  LIKE '迭代' ) AND ( 1  AND `status` = 'doing'  ))`");
$userquery->form->range('``');
$userquery->gen(1);

/**

title=测试 executionModel->getExecutionQuery();
timeout=0
cid=1

*/

$queryIdList = array(0, 1);
$executionTester = new executionTest();
r($executionTester->getExecutionQueryTest($queryIdList[0])) && p() && e("(( 1 ) AND ( 1  AND t1.`status` = 'doing'))");                                   // 没有queryID时，获取查询执行的SQL
r($executionTester->getExecutionQueryTest($queryIdList[1])) && p() && e("(( 1   AND t1.`name`  LIKE '迭代' ) AND ( 1  AND t1.`status` = 'doing'  ))");  // 有queryID时，获取查询执行的SQL
