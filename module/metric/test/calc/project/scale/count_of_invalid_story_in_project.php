#!/usr/bin/env php
<?php

/**

title=count_of_invalid_story_in_project
timeout=0
cid=1

- 测试分组数。 @6
- 测试。第0条的value属性 @8

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('product')->loadYaml('product', true, 4)->gen(10);
zendata('project')->loadYaml('project_close', true, 4)->gen(10);
zendata('story')->loadYaml('story_status_closedreason', true, 4)->gen(1000);
zendata('projectstory')->loadYaml('projectstory', true, 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('6'); // 测试分组数。

r($calc->getResult(array('project' => '4'))) && p('0:value') && e('8');  // 测试。