#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/calc.unittest.class.php';

zenData('user')->loadYaml('user', true)->gen(30);
zenData('product')->loadYaml('product', true)->gen(10);
zenData('productplan')->loadYaml('productplan', true)->gen(50);
zenData('feedback')->loadYaml('feedback_create', true)->gen(50);

$metric = new metricTest();

/**

title=getMetricByCode
timeout=0
cid=1

- 测试按产品统计年度关闭反馈数第0条的value属性 @4

*/

global $config;
$config->vision = 'rnd';

$options = array('product' => '9', 'year' => date('Y'));
r($metric->getMetricByCode('count_of_annual_closed_feedback_in_product', $options)) && p('0:value') && e('4');  // 测试按产品统计年度关闭反馈数