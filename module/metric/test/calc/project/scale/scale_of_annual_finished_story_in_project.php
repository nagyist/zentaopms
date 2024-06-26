#!/usr/bin/env php
<?php

/**

title=scale_of_annual_finished_story_in_project
timeout=0
cid=1

- 测试分组数。 @18
- 测试项目2。第0条的value属性 @15

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('product')->loadYaml('product', true, 4)->gen(10);
zendata('project')->loadYaml('project_close', true, 4)->gen(10);
zendata('story')->loadYaml('story_status_closedreason', true, 4)->gen(1000);
zendata('projectstory')->loadYaml('projectstory', true, 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('18'); // 测试分组数。

r($calc->getResult(array('project' => '9', 'year' => '2018'))) && p('0:value') && e('15');  // 测试项目2。