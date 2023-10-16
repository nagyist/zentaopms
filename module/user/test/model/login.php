#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/user.class.php';
su('admin');

$user = zdTable('user');
$user->gen(50);

/**

title=测试 userModel->login();
cid=1
pid=1

使用admin登录，返回此用户为超级管理员 >> 1
使用user3登录，返回权限列表的数量 >> 6
使用空账号登录，返回权限列表数量为0 >> 0

*/

$userClass = new userTest();

$admin = new stdclass();
$admin->id      = 1;
$admin->account = 'admin';
$admin          = $userClass->loginTest($admin);

$normalAccount = new stdclass();
$normalAccount->id      = 4;
$normalAccount->account = 'user3';
$normalAccount          = $userClass->loginTest($normalAccount);

r($admin)                        && p('admin') && e('1'); //使用admin登录，返回此用户为超级管理员
r(count($normalAccount->rights)) && p()        && e('6'); //使用admin登录，返回权限列表的数量
