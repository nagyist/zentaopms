#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';

zdTable('case')->config('case')->gen(5);
zdTable('testrun')->gen(5);

/**

title=测试 testtaskModel->getRunById();
cid=1
pid=1

*/

global $tester;

$testtask = $tester->loadModel('testtask');

r($testtask->getRunById(0)) && p() && e(0); // 查询测试执行 1 的信息。
r($testtask->getRunById(6)) && p() && e(0); // 查询测试执行 6 的信息。

$run = $testtask->getRunById(1);
r($run) && p('id,task,version') && e('1,1,1');       // 查询测试执行 1 的信息。
r($run->case) && p('id,title')  && e('1,测试用例1'); // 查询测试执行 1 对应的用例信息。

$run = $testtask->getRunById(2);
r($run) && p('id,task,version') && e('2,1,1');       // 查询测试执行 2 的信息。
r($run->case) && p('id,title')  && e('2,测试用例2'); // 查询测试执行 2 对应的用例信息。

$run = $testtask->getRunById(3);
r($run) && p('id,task,version') && e('3,1,1');       // 查询测试执行 3 的信息。
r($run->case) && p('id,title')  && e('3,测试用例3'); // 查询测试执行 3 对应的用例信息。

$run = $testtask->getRunById(4);
r($run) && p('id,task,version') && e('4,1,1');       // 查询测试执行 4 的信息。
r($run->case) && p('id,title')  && e('4,测试用例4'); // 查询测试执行 4 对应的用例信息。

$run = $testtask->getRunById(5);
r($run) && p('id,task,version') && e('5,2,1');       // 查询测试执行 5 的信息。
r($run->case) && p('id,title')  && e('5,测试用例5'); // 查询测试执行 5 对应的用例信息。
