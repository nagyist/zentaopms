#!/usr/bin/env php
<?php
/**

title=测试 chartModel::getTables();
timeout=0
cid=1

- 测试普通SQL语句获取表是否正确第tables条的0属性 @zt_story
- 测试普通SQL语句获取字段是否正确第fields条的*属性 @*
- 测试普通SQL语句获取sql是否正确属性sql @select * from zt_story;
- 测试普通SQL语句获取表是否正确第tables条的0属性 @zt_story
- 测试普通SQL语句获取查询字段是否正确
 - 第fields条的id属性 @id
 - 第fields条的name属性 @name
- 测试普通SQL语句获取sql是否正确属性sql @select id,name from zt_story;
- 测试联表SQL语句获取表是否正确
 - 第tables条的0属性 @zt_story
 - 第tables条的1属性 @zt_product
- 测试联表SQL语句获取字段是否正确
 - 第fields条的id属性 @t1.id
 - 第fields条的name属性 @t2.name
- 测试联表SQL语句获取sql是否正确属性sql @select t1.id,t2.name from zt_story as t1 left join zt_product as t2 on t1.product = t2.id;
- 测试联表SQL语句获取重定义字段是否正确
 - 第tables条的0属性 @zt_story
 - 第tables条的1属性 @zt_product
- 测试联表SQL语句获取重定义字段是否正确
 - 第fields条的storyID属性 @t1.id
 - 第fields条的productName属性 @t2.name
- 测试联表SQL语句获取sql是否正确属性sql @select t1.id as storyID,t2.name as productName from zt_story as t1 left join zt_product as t2 on t1.product = t2.id;
- 测试sql中有where获取表是否正确第tables条的0属性 @zt_story
- 测试sql中有where获取字段是否正确第fields条的*属性 @*
- 测试sql中有where获取sql是否正确属性sql @select * from zt_story where id = 1;
- 测试sql中有limit获取表是否正确第tables条的0属性 @zt_story
- 测试sql中有limit获取字段是否正确第fields条的*属性 @*
- 测试sql中有limit获取sql是否正确属性sql @select * from zt_story limit 10;
- 测试sql中有group by获取表是否正确第tables条的0属性 @zt_story
- 测试sql中有group by获取字段是否正确第fields条的*属性 @*
- 测试sql中有group by获取sql是否正确属性sql @select * from zt_story group by product;
- 测试异常sql语句 @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';

zenData('user')->gen(5);
su('admin');

global $tester;
$chart = $tester->loadModel('chart');

$sql = array();
$sql[0] = 'select * from zt_story;';
$sql[1] = 'select id,name from zt_story;';
$sql[2] = 'select t1.id,t2.name from zt_story as t1 left join zt_product as t2 on t1.product = t2.id;';
$sql[3] = 'select t1.id as storyID,t2.name as productName from zt_story as t1 left join zt_product as t2 on t1.product = t2.id;';
$sql[4] = 'select * from zt_story where id = 1;';
$sql[5] = 'select * from zt_story limit 10;';
$sql[6] = 'select * from zt_story group by product;';
$sql[7] = 'select * where table;';

r($chart->getTables($sql[0])) && p('tables:0') && e('zt_story'); //测试普通SQL语句获取表是否正确
r($chart->getTables($sql[0])) && p('fields:*') && e('*');        //测试普通SQL语句获取字段是否正确
r($chart->getTables($sql[0])) && p('sql')      && e('select * from zt_story;'); //测试普通SQL语句获取sql是否正确

r($chart->getTables($sql[1])) && p('tables:0')       && e('zt_story'); //测试普通SQL语句获取表是否正确
r($chart->getTables($sql[1])) && p('fields:id,name') && e('id,name');  //测试普通SQL语句获取查询字段是否正确
r($chart->getTables($sql[1])) && p('sql', '-')       && e('select id,name from zt_story;'); //测试普通SQL语句获取sql是否正确

r($chart->getTables($sql[2])) && p('tables:0,1')     && e('zt_story,zt_product'); //测试联表SQL语句获取表是否正确
r($chart->getTables($sql[2])) && p('fields:id,name') && e('t1.id,t2.name');       //测试联表SQL语句获取字段是否正确
r($chart->getTables($sql[2])) && p('sql', '-')       && e('select t1.id,t2.name from zt_story as t1 left join zt_product as t2 on t1.product = t2.id;'); //测试联表SQL语句获取sql是否正确

r($chart->getTables($sql[3])) && p('tables:0,1')                 && e('zt_story,zt_product'); //测试联表SQL语句获取重定义字段是否正确
r($chart->getTables($sql[3])) && p('fields:storyID,productName') && e('t1.id,t2.name');       //测试联表SQL语句获取重定义字段是否正确
r($chart->getTables($sql[3])) && p('sql', '-')                   && e('select t1.id as storyID,t2.name as productName from zt_story as t1 left join zt_product as t2 on t1.product = t2.id;'); //测试联表SQL语句获取sql是否正确

r($chart->getTables($sql[4])) && p('tables:0') && e('zt_story'); //测试sql中有where获取表是否正确
r($chart->getTables($sql[4])) && p('fields:*') && e('*');        //测试sql中有where获取字段是否正确
r($chart->getTables($sql[4])) && p('sql')      && e('select * from zt_story where id = 1;'); //测试sql中有where获取sql是否正确

r($chart->getTables($sql[5])) && p('tables:0') && e('zt_story'); //测试sql中有limit获取表是否正确
r($chart->getTables($sql[5])) && p('fields:*') && e('*');        //测试sql中有limit获取字段是否正确
r($chart->getTables($sql[5])) && p('sql')      && e('select * from zt_story limit 10;'); //测试sql中有limit获取sql是否正确

r($chart->getTables($sql[6])) && p('tables:0') && e('zt_story'); //测试sql中有group by获取表是否正确
r($chart->getTables($sql[6])) && p('fields:*') && e('*');        //测试sql中有group by获取字段是否正确
r($chart->getTables($sql[6])) && p('sql')      && e('select * from zt_story group by product;'); //测试sql中有group by获取sql是否正确

r($chart->getTables($sql[7])) && p('') && e('0'); //测试异常sql语句
