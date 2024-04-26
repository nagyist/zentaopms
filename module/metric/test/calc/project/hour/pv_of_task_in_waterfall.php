#!/usr/bin/env php
<?php

/**

title=pv_of_task_in_waterfall
timeout=0
cid=1

- 测试分组数。 @5
- 测试项目7。第0条的value属性 @59

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('project')->loadYaml('waterfall', true, 4)->gen(10);
zendata('project')->loadYaml('stage', true, 4)->gen(40, false);
zendata('task')->loadYaml('task_waterfall', true, 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('5'); // 测试分组数。

r($calc->getResult(array('project' => '7'))) && p('0:value') && e('59'); // 测试项目7。