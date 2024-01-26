#!/usr/bin/env php
<?php

/**

title=count_of_daily_review_feedback_in_user
timeout=0
cid=1

- 测试分组数。 @130
- 测试用户admin @4
- 测试用户user @24
- 测试用户dev @34
- 测试用户pm @22
- 测试不存在的用户 @0

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('product')->config('product_shadow', true, 4)->gen(10);
zdTable('feedback')->config('feedback', true, 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('224'); // 测试分组数。

r(count($calc->getResult(array('user' => 'admin')))) && p('') && e('10');  // 测试用户admin
r(count($calc->getResult(array('user' => 'user'))))  && p('') && e('42'); // 测试用户user
r(count($calc->getResult(array('user' => 'dev'))))   && p('') && e('64'); // 测试用户dev
r(count($calc->getResult(array('user' => 'pm'))))    && p('') && e('48'); // 测试用户pm
r($calc->getResult(array('user' => 'notexist')))     && p('') && e('0');  // 测试不存在的用户
