#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/user.class.php';
zdTable('user')->gen(10);
su('admin');

$userContactList = zdTable('usercontact');
$userContactList->gen(100);

/**

title=测试 userModel::getListByAccount();
cid=1
pid=1

获取admin的联系人列表 >> 0
获取dev2的联系人列表 >> 3
获取空用户的联系人列表 >> 0

*/

$user = new userTest();
$adminContactList = $user->getListByAccountTest('admin');
$test2ContactList = $user->getListByAccountTest('dev2');
$emptyContactList = $user->getListByAccountTest('');

r(count($adminContactList)) && p() && e('0'); // 获取admin的联系人列表
r(count($test2ContactList)) && p() && e('3'); // 获取dev2的联系人列表
r(count($emptyContactList)) && p() && e('0'); // 获取空用户的联系人列表
