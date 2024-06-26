#!/usr/bin/env php
<?php

/**

title=测试 storyModel->getByAssignedby();
cid=0

- 查看产品 1 由我指派的需求 @0
- 查看产品 2 由我指派的需求 @0
- 查看产品 3 由我指派的需求 @0
- 查看产品 4 由我指派的需求 @0
- 查看产品 5 由我指派的需求 @0

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/story.unittest.class.php';
su('admin');

$productIDList = array(1, 2, 3, 4 ,5);
$branch        = 'all';

$story = new storyTest();

r($story->getByAssignedByTest($productIDList[0], $branch)) && p() && e('0'); // 查看产品 1 由我指派的需求
r($story->getByAssignedByTest($productIDList[1], $branch)) && p() && e('0'); // 查看产品 2 由我指派的需求
r($story->getByAssignedByTest($productIDList[2], $branch)) && p() && e('0'); // 查看产品 3 由我指派的需求
r($story->getByAssignedByTest($productIDList[3], $branch)) && p() && e('0'); // 查看产品 4 由我指派的需求
r($story->getByAssignedByTest($productIDList[4], $branch)) && p() && e('0'); // 查看产品 5 由我指派的需求
