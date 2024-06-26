#!/usr/bin/env php
<?php

/**

title=测试 cneModel->tryAllocate();
timeout=0
cid=1

- 空的数据
 - 第data条的total属性 @0
 - 第data条的allocated属性 @0
 - 第data条的failed属性 @0
- 正常范围的数据
 - 第data条的total属性 @1
 - 第data条的allocated属性 @1
 - 第data条的failed属性 @0
- 超出范围的数据属性code @41010

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/cne.unittest.class.php';

$cneModel = new cneTest();

$resources = array();
r($cneModel->tryAllocateTest($resources)) && p('data:total,allocated,failed') && e('0,0,0'); // 空的数据

$resources[] = array('cpu' => 0.2, 'memory' => 268435456);
r($cneModel->tryAllocateTest($resources)) && p('data:total,allocated,failed') && e('1,1,0'); // 正常范围的数据

$resources[] = array('cpu' => 100, 'memory' => 268435456);
r($cneModel->tryAllocateTest($resources)) && p('code') && e('41010'); // 超出范围的数据