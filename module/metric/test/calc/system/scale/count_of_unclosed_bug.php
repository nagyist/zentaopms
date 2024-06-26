#!/usr/bin/env php
<?php

/**

title=count_of_unclosed_bug
timeout=0
cid=1

- 测试356条数据Bug数。第0条的value属性 @64
- 测试652条数据Bug数。第0条的value属性 @112
- 测试1265条数据Bug数。第0条的value属性 @220

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('product')->loadYaml('product', true, 4)->gen(10);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

zendata('bug')->loadYaml('bug_resolution_status', true, 4)->gen(356, true, false);
$calc = $metric->calcMetric(__FILE__);
r($calc->getResult()) && p('0:value') && e('64'); // 测试356条数据Bug数。

zendata('bug')->loadYaml('bug_resolution_status', true, 4)->gen(652, true, false);
$calc = $metric->calcMetric(__FILE__);
r($calc->getResult()) && p('0:value') && e('112'); // 测试652条数据Bug数。

zendata('bug')->loadYaml('bug_resolution_status', true, 4)->gen(1265, true, false);
$calc = $metric->calcMetric(__FILE__);
r($calc->getResult()) && p('0:value') && e('220'); // 测试1265条数据Bug数。