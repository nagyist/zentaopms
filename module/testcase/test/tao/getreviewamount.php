#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/testcase.unittest.class.php';

zenData('case')->gen(30);
zenData('user')->gen(1);

su('admin');

/**

title=测试 testcaseTao->getReviewAmount();
cid=1
pid=1

*/

$testcase = new testcaseTest();

r($testcase->getReviewAmountTest()) && p() && e('8'); // 测试查询待评审的用例数量
