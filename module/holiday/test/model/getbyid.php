#!/usr/bin/env php
<?php
/**

title=测试 holidayModel->getById();
cid=1

- 查询id为1的holiday
 - 属性name @这是一个节假日1
 - 属性type @holiday
 - 属性desc @这个是节假日的描述1
- 查询id为5的holiday
 - 属性name @这是一个节假日5
 - 属性type @holiday
 - 属性desc @这个是节假日的描述5
- 查询id为10的holiday
 - 属性name @这是一个节假日10
 - 属性type @working
 - 属性desc @这个是节假日的描述10
- 查询不存在的holiday @Object not found

*/
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/holiday.unittest.class.php';

zenData('holiday')->gen(10);
zenData('user')->gen(1);

su('admin');

$holidayIDList = array(1, 5, 10, 1001);

$holiday = new holidayTest();

r($holiday->getByIdTest($holidayIDList[0])) && p('name,type,desc') && e('这是一个节假日1,holiday,这个是节假日的描述1');   //查询id为1的holiday
r($holiday->getByIdTest($holidayIDList[1])) && p('name,type,desc') && e('这是一个节假日5,holiday,这个是节假日的描述5');   //查询id为5的holiday
r($holiday->getByIdTest($holidayIDList[2])) && p('name,type,desc') && e('这是一个节假日10,working,这个是节假日的描述10'); //查询id为10的holiday
r($holiday->getByIdTest($holidayIDList[3])) && p()                 && e('Object not found');                              //查询不存在的holiday
