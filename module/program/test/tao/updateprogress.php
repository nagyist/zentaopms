#!/usr/bin/env php
<?php
/**

title=测试 programTao::updateProgress();
timeout=0
cid=1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/program.unittest.class.php';

zenData('project')->loadYaml('program')->gen(20);
zenData('user')->gen(5);
su('admin');

$programTester = new programTest();
r($programTester->updateProgressTest()) && p('1:progress') && e('0.00'); // 获取系统中所有项目集的进度
