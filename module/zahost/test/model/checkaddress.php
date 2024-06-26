#!/usr/bin/env php
<?php

/**

title=测试 zahostModel->checkAddress();
timeout=0
cid=1

- 测试正常域名得到的结果 @1
- 测试正常 IP 得到的结果 @1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zahost.unittest.class.php';
su('admin');

$addressList = array('a', 'www.baidu.com', '10.0.0.133');

$zahost = new zahostTest();
r($zahost->checkAddress($addressList[1])) && p('') && e('1'); //测试正常域名得到的结果
r($zahost->checkAddress($addressList[2])) && p('') && e('1'); //测试正常 IP 得到的结果