#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('user')->config('user', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_annual_created_user
cid=1
pid=1

*/

r(count($calc->getResult())) && p('') && e('10');                        // 测试年度新增用户分组数。
r($calc->getResult(array('year' => '2017'))) && p('0:value') && e('36'); // 测试2017年新增用户数
r($calc->getResult(array('year' => '2018'))) && p('0:value') && e('28'); // 测试2018年新增用户数
r($calc->getResult(array('year' => '2023'))) && p('0:value') && e('0');  // 测试2023年新增用户数
