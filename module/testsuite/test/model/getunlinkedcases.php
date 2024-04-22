#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/testsuite.unittest.class.php';
su('admin');

zenData('suitecase')->gen(0);
zenData('case')->gen(2);
zenData('testsuite')->gen(2);
zenData('userquery')->loadYaml('userquery')->gen(1);
/**

title=测试 testsuiteModel->getUnlinkedCases();
cid=1
pid=1

测试suiteID值为1,param值为0 >> 2,这个是测试用例2;1,这个是测试用例1
测试suiteID值为1,param值为1 >> 1,这个是测试用例1
测试suiteID值为2,param值为0 >> 2,这个是测试用例2;1,这个是测试用例1
测试suiteID值为2,param值为1 >> 1,这个是测试用例1

*/
$suiteID = array(1, 2);
$param = array(0, 1);

$testsuite = new testsuiteTest();

r($testsuite->getUnlinkedCasesTest($suiteID[0], 'all',      $param[0])) && p('0:id,title;1:id,title') && e('2,这个是测试用例2;1,这个是测试用例1');  //测试suiteID值为1,param值为0
r($testsuite->getUnlinkedCasesTest($suiteID[1], 'all',      $param[0])) && p('0:id,title;1:id,title') && e('2,这个是测试用例2;1,这个是测试用例1');  //测试suiteID值为2,param值为0
r($testsuite->getUnlinkedCasesTest($suiteID[1], 'bySearch', $param[1])) && p('0:id,title')            && e('1,这个是测试用例1');                    //测试suiteID值为2,param值为1
r($testsuite->getUnlinkedCasesTest($suiteID[0], 'bySearch', $param[1])) && p('0:id,title')            && e('1,这个是测试用例1');                    //测试suiteID值为1,param值为1
