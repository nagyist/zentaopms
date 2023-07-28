#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('project')->config('project_close', $useCommon = true, $levels = 4)->gen(10);
zdTable('project')->config('execution', $useCommon = true, $levels = 4)->gen(1000, false);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_annual_created_execution
cid=1
pid=1

*/

r(count($calc->getResult())) && p('') && e('10');                        // 测试年度新增执行分组数。
r($calc->getResult(array('year' => '2017'))) && p('0:value') && e('30'); // 测试2017年新增执行数
r($calc->getResult(array('year' => '2019'))) && p('0:value') && e('6');  // 测试2019年新增执行数
r($calc->getResult(array('year' => '2023'))) && p('0:value') && e('0');  // 测试2023年新增执行数
