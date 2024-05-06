#!/usr/bin/env php
<?php

/**

title=scale_of_finished_story
timeout=0
cid=1

- 测试按全局统计的已完成研发需求规模数第0条的value属性 @30

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('story')->loadYaml('story_stage_closedreason', true, 4)->gen(140);
zendata('product')->loadYaml('product', true, 4)->gen(10);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r($calc->getResult()) && p('0:value') && e('30'); // 测试按全局统计的已完成研发需求规模数