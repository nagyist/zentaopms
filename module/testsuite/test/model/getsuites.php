#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/testsuite.class.php';

zdTable('testsuite')->config('testsuite')->gen(4);
zdTable('user')->gen(2);

su('admin');

/**

title=测试 testsuiteModel->getSuites();
cid=1
pid=1

测试productID值为1,orderBy为id_desc           >> 这是测试套件名称2;这是测试套件名称1
测试productID值为1,orderBy为id_asc            >> 这是测试套件名称1;这是测试套件名称2
测试productID值为1,orderBy为name_desc,id_desc >> 这是测试套件名称2;这是测试套件名称1
测试productID值为1,orderBy为name_asc,id_desc  >> 这是测试套件名称1;这是测试套件名称2

切换dev1用户

测试productID值为1,orderBy为id_desc           >> 0
测试productID值为1,orderBy为id_asc            >> ~~
测试productID值为1,orderBy为name_desc,id_desc >> ~~
测试productID值为1,orderBy为name_asc,id_desc  >> 这是测试套件名称1;这是测试套件名称3

*/
$productID = array(1, 0);
$orderBy   = array('id_desc', 'id_asc', 'name_desc,id_desc', 'name_asc,id_desc');

$testsuite = new testsuiteTest();

r($testsuite->getSuitesTest($productID[0], $orderBy[0], null, '')) && p('3:name;1:name;2') && e('这是测试套件名称3;这是测试套件名称1;~~');  //测试productID值为1,orderBy为id_desc
r($testsuite->getSuitesTest($productID[0], $orderBy[1], null, '')) && p('1:name;3:name;2') && e('这是测试套件名称1;这是测试套件名称3;~~');  //测试productID值为1,orderBy为id_asc
r($testsuite->getSuitesTest($productID[0], $orderBy[2], null, '')) && p('1:name;3:name;2') && e('这是测试套件名称1;这是测试套件名称3;~~');  //测试productID值为1,orderBy为name_desc,id_desc
r($testsuite->getSuitesTest($productID[0], $orderBy[3], null, '')) && p('3:name;1:name;2') && e('这是测试套件名称3;这是测试套件名称1;~~');  //测试productID值为1,orderBy为name_asc,id_desc

su('user1');

r($testsuite->getSuitesTest($productID[1], $orderBy[0], null, '')) && p()                && e('0');                                    //测试productID值为1,orderBy为id_desc
r($testsuite->getSuitesTest($productID[0], $orderBy[0], null, '')) && p('2')             && e('~~');                                   //测试productID值为1,orderBy为id_desc
r($testsuite->getSuitesTest($productID[0], $orderBy[1], null, '')) && p('2')             && e('~~');                                   //测试productID值为1,orderBy为id_asc
r($testsuite->getSuitesTest($productID[0], $orderBy[2], null, '')) && p('1:name;3:name') && e('这是测试套件名称1;这是测试套件名称3');  //测试productID值为1,orderBy为name_desc,id_desc
