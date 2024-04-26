#!/usr/bin/env php
<?php

/**

title=count_of_user
timeout=0
cid=1

- 测试356条数据。第0条的value属性 @119
- 测试652条数据。第0条的value属性 @218

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

zendata('user')->loadYaml('user', true, 4)->gen(356, true, false);
$calc = $metric->calcMetric(__FILE__);
r($calc->getResult()) && p('0:value') && e('119'); // 测试356条数据。

zendata('user')->loadYaml('user', true, 4)->gen(652, true, false);
$calc = $metric->calcMetric(__FILE__);
r($calc->getResult()) && p('0:value') && e('218'); // 测试652条数据。