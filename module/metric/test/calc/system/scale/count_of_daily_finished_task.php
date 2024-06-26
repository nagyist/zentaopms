#!/usr/bin/env php
<?php

/**

title=count_of_daily_finished_task
timeout=0
cid=1

- 测试分组数。 @244
- 测试2020.09.05。第0条的value属性 @1
- 测试2020.09.15。第0条的value属性 @1
- 测试不存在。 @0

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('project')->loadYaml('project_close', true, 4)->gen(10);
zendata('project')->loadYaml('execution', true, 4)->gen(20, false);
zendata('task')->loadYaml('task', true, 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('244'); // 测试分组数。

r($calc->getResult(array('year' => '2020', 'month' => '09', 'day' => '05'))) && p('0:value') && e('1'); // 测试2020.09.05。
r($calc->getResult(array('year' => '2020', 'month' => '09', 'day' => '15'))) && p('0:value') && e('1'); // 测试2020.09.15。

r($calc->getResult(array('year' => '2021', 'month' => '04'))) && p('') && e('0'); // 测试不存在。