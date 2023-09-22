#!/usr/bin/env php
<?php

use function zin\wg;

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/action.class.php';
su('admin');

zdTable('action')->config('action')->gen(20);
zdTable('userquery')->config('userquery')->gen(2);
zdTable('story')->gen(10);
zdTable('pipeline')->gen(5);

/**

title=测试 actionModel->getTrashesBySearch();
cid=1
pid=1

搜索objectType all,      type all,    queryID myQueryID, orderBy id_desc的回收站信息 << 0
搜索objectType story,    type all,    queryID myQueryID, orderBy id_desc的回收站信息 << 1,1,用户需求1;9,9,用户需求9
搜索objectType all,      type hidden, queryID myQueryID, orderBy id_desc的回收站信息 << 2,2,软件需求2;10,10,软件需求10
搜索objectType all,      type all,    queryID 1,         orderBy id_desc的回收站信息 << 1,1,用户需求1
搜索objectType pipeline, type all,    queryID myQueryID, orderBy id_desc的回收站信息 << 1,gitlab,1;3,jenkins,3

*/

$objectTypeList = array('all', 'story', 'pipeline');
$typeList       = array('all', 'hidden');
$queryIdList    = array('myQueryID', 1);
$orderBy        = array('id_desc', 'id_asc');
$pager          = null;

$action = new actionTest();

r($action->getTrashesBySearchTest($objectTypeList[0], $typeList[0], $queryIdList[0], $orderBy[0], $pager)) && p()                                                     && e('0');                              // 搜索objectType all,      type all,    queryID myQueryID, orderBy id_desc的回收站信息
r($action->getTrashesBySearchTest($objectTypeList[1], $typeList[0], $queryIdList[0], $orderBy[0], $pager)) && p('1:id,objectID,objectName;9:id,objectID,objectName')  && e('1,1,用户需求1;9,9,用户需求9');    // 搜索objectType story,    type all,    queryID myQueryID, orderBy id_desc的回收站信息
r($action->getTrashesBySearchTest($objectTypeList[1], $typeList[1], $queryIdList[0], $orderBy[0], $pager)) && p('2:id,objectID,objectName;10:id,objectID,objectName') && e('2,2,软件需求2;10,10,软件需求10'); // 搜索objectType all,      type hidden, queryID myQueryID, orderBy id_desc的回收站信息
r($action->getTrashesBySearchTest($objectTypeList[1], $typeList[0], $queryIdList[1], $orderBy[0], $pager)) && p('1:id,objectID,objectName')                           && e('1,1,用户需求1');                  // 搜索objectType all,      type all,    queryID 1,         orderBy id_desc的回收站信息
r($action->getTrashesBySearchTest($objectTypeList[2], $typeList[0], $queryIdList[0], $orderBy[0], $pager)) && p('1:id,objectType,objectID')                           && e('1,gitlab,1');                     // 搜索objectType pipeline, type all,    queryID myQueryID, orderBy id_desc的回收站信息
