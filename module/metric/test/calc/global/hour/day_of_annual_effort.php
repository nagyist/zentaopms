#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('effort')->config('effort', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=day_of_annual_effort
cid=1
pid=1

*/

r(count($calc->getResult())) && p('') && e('11'); // 测试分组数。

r($calc->getResult(array('year' => '2014'))) && p('0:value') && e('16.2');  // 测试2014年。
r($calc->getResult(array('year' => '2016'))) && p('0:value') && e('10.2');  // 测试2016年。
