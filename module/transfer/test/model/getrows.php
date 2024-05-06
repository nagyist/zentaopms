#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/transfer.unittest.class.php';
zenData('story')->gen(20);
$file = zenData('file');
$file->objectType->range('task');
$file->gen(10);

$task = zenData('task');
$task->project->range('11');
$task->parent->range('0{3},3{7}');
$task->mode->range('linear{1},multi{1},0{8}');
$task->execution->range('101');
$task->gen(10);

su('admin');

/**

title=测试 transfer->getRows();
timeout=0
cid=1

- 测试导出多人并行任务第0条的name属性 @[多人] 开发任务11
- 测试导出多人串行任务第1条的name属性 @[多人] 开发任务12
- 测试导出正常任务第2条的name属性 @开发任务13
- 测试导出子任务第3条的name属性 @>开发任务14

*/
$transfer = new transferTest();

//a($transfer->getRowsTest('task'));die;
/* 测试导出任务。*/
r($transfer->getRowsTest('task')) && p('0:name') && e('[多人] 开发任务11'); // 测试导出多人并行任务
r($transfer->getRowsTest('task')) && p('1:name') && e('[多人] 开发任务12'); // 测试导出多人串行任务
r($transfer->getRowsTest('task')) && p('2:name') && e('开发任务13');        // 测试导出正常任务
r($transfer->getRowsTest('task')) && p('3:name') && e('>开发任务14');       // 测试导出子任务
r(isset($transfer->getRowsTest('task')[8]->files)) && p('') && e('1');      // 测试导出附件是否存在

$task = new stdclass();
$task->name = '追加任务1';
$_POST['rows']['2'] = $task;
r($transfer->getRowsTest('task')) && p('1:name') && e('[多人] 追加任务1');  // 测试当有数据需要单独处理时
