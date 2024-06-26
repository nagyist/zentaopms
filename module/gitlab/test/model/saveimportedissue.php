#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/gitlab.unittest.class.php';
su('admin');

/**

title=测试gitlabModel->saveImportedIssue();
timeout=0
cid=1

- 保存导入任务issue。第labels条的0属性 @zentao_task/18
- 保存导入bug issue。第labels条的0属性 @zentao_bug/9

*/

zenData('task')->gen(20);
zenData('bug')->gen(10);
zenData('pipeline')->gen(5);
zenData('relation')->loadYaml('relation')->gen(4);

$gitlab  = new gitlabTest();

$gitlabID   = 1;
$projectID  = 2;
$issueID    = 4;
$objectType = 'task';
$objectID   = 18;
r($gitlab->saveImportedIssueTest($gitlabID, $projectID, $objectType, $objectID, $issueID)) && p('labels:0') && e('zentao_task/18'); // 保存导入任务issue。

$issueID    = 3;
$objectType = 'bug';
$objectID   = 9;
r($gitlab->saveImportedIssueTest($gitlabID, $projectID, $objectType, $objectID, $issueID)) && p('labels:0') && e('zentao_bug/9'); // 保存导入bug issue。