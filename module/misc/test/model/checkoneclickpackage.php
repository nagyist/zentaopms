#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';

/**

title=测试 miscModel->checkOneClickPackage();
timeout=0
cid=1

- 调用方法返回值 @0

*/
global $tester;
$tester->loadModel('misc');

r($tester->misc->checkOneClickPackage()) && p() && e('0'); //调用方法返回值
?>
