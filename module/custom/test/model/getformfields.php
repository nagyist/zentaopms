#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/custom.unittest.class.php';

zenData('lang')->loadYaml('lang')->gen(5);
zenData('user')->gen(5);
su('admin');

/**

title=测试 customModel->getFormFields();
timeout=1
cid=1

*/

$moduleName = array('product', 'story', 'productplan', 'release', 'execution', 'task', 'build', 'bug', 'testcase', 'testsuite', 'testreport', 'caselib', 'testtask', 'doc', 'user');
$method     = array('', 'create', 'edit', 'change', 'close', 'review', 'start', 'finish', 'activate', 'resolve', 'importUnit');

$customTester = new customTest();
r($customTester->getFormFieldsTest($moduleName[0], $method[0]))   && p()                           && e('0');                                              // 测试moduleName值为product，method为空
r($customTester->getFormFieldsTest($moduleName[0], $method[1]))   && p('PO,QD,RD,type')            && e('产品负责人,测试负责人,发布负责人,产品类型');      // 测试moduleName值为product，method为create
r($customTester->getFormFieldsTest($moduleName[0], $method[2]))   && p('PO,QD,RD,type,status')     && e('产品负责人,测试负责人,发布负责人,产品类型,状态'); // 测试moduleName值为product，method为edit
r($customTester->getFormFieldsTest($moduleName[1], $method[0]))   && p()                           && e('0');                                              // 测试moduleName值为story，method为空
r($customTester->getFormFieldsTest($moduleName[1], $method[1]))   && p('module,plan,source,pri')   && e('所属模块,所属计划,来源,优先级');                  // 测试moduleName值为story，method为create
r($customTester->getFormFieldsTest($moduleName[1], $method[3]))   && p('comment')                  && e('备注');                                           // 测试moduleName值为story，method为change
r($customTester->getFormFieldsTest($moduleName[1], $method[4]))   && p('comment')                  && e('备注');                                           // 测试moduleName值为story，method为close
r($customTester->getFormFieldsTest($moduleName[1], $method[5]))   && p('reviewedDate,comment')     && e('评审时间,备注');                                  // 测试moduleName值为story，method为review
r($customTester->getFormFieldsTest($moduleName[2], $method[0]))   && p('begin,end,desc')           && e('开始日期,结束日期,描述');                         // 测试moduleName值为productplan，method为空
r($customTester->getFormFieldsTest($moduleName[2], $method[1]))   && p('begin,end,desc')           && e('开始日期,结束日期,描述');                         // 测试moduleName值为productplan，method为create
r($customTester->getFormFieldsTest($moduleName[2], $method[2]))   && p('begin,end,desc')           && e('开始日期,结束日期,描述');                         // 测试moduleName值为productplan，method为edit
r($customTester->getFormFieldsTest($moduleName[3], $method[0]))   && p()                           && e('0');                                              // 测试moduleName值为release，method为空
r($customTester->getFormFieldsTest($moduleName[3], $method[1]))   && p('desc')                     && e('描述');                                           // 测试moduleName值为release，method为create
r($customTester->getFormFieldsTest($moduleName[3], $method[2]))   && p('desc')                     && e('描述');                                           // 测试moduleName值为release，method为edit
r($customTester->getFormFieldsTest($moduleName[4], $method[0]))   && p()                           && e('0');                                              // 测试moduleName值为execution，method为空
r($customTester->getFormFieldsTest($moduleName[4], $method[1]))   && p('days,desc')                && e('可用工作日,执行描述');                            // 测试moduleName值为execution，method为create
r($customTester->getFormFieldsTest($moduleName[4], $method[2]))   && p('days,desc,PO,PM')          && e('可用工作日,执行描述,产品负责人,执行负责人');      // 测试moduleName值为execution，method为edit
r($customTester->getFormFieldsTest($moduleName[4], $method[4]))   && p()                           && e('0');                                              // 测试moduleName值为execution，method为close
r($customTester->getFormFieldsTest($moduleName[4], $method[6]))   && p()                           && e('0');                                              // 测试moduleName值为execution，method为start
r($customTester->getFormFieldsTest($moduleName[5], $method[0]))   && p()                           && e('0');                                              // 测试moduleName值为task，method为空
r($customTester->getFormFieldsTest($moduleName[5], $method[1]))   && p('pri,estimate,desc')        && e('优先级,最初预计,任务描述');                       // 测试moduleName值为task，method为create
r($customTester->getFormFieldsTest($moduleName[5], $method[2]))   && p('pri,estimate,estStarted')  && e('优先级,最初预计,预计开始');                       // 测试moduleName值为task，method为edit
r($customTester->getFormFieldsTest($moduleName[5], $method[7]))   && p('comment')                  && e('备注');                                           // 测试moduleName值为task，method为finish
r($customTester->getFormFieldsTest($moduleName[5], $method[8]))   && p('assignedTo,comment')       && e('指派给,备注');                                    // 测试moduleName值为task，method为activate
r($customTester->getFormFieldsTest($moduleName[6], $method[0]))   && p('scmPath,filePath,desc')    && e('源代码地址,下载地址,描述');                       // 测试moduleName值为build，method为空
r($customTester->getFormFieldsTest($moduleName[6], $method[1]))   && p('scmPath,filePath,desc')    && e('源代码地址,下载地址,描述');                       // 测试moduleName值为build，method为create
r($customTester->getFormFieldsTest($moduleName[6], $method[2]))   && p('scmPath,filePath,desc')    && e('源代码地址,下载地址,描述');                       // 测试moduleName值为build，method为edit
r($customTester->getFormFieldsTest($moduleName[7], $method[0]))   && p()                           && e('0');                                              // 测试moduleName值为bug，method为空
r($customTester->getFormFieldsTest($moduleName[7], $method[1]))   && p('module,project,deadline')  && e('所属模块,所属项目,截止日期');                     // 测试moduleName值为bug，method为create
r($customTester->getFormFieldsTest($moduleName[7], $method[2]))   && p('plan,type,browser,pri')    && e('所属计划,Bug类型,浏览器,优先级');                 // 测试moduleName值为bug，method为edit
r($customTester->getFormFieldsTest($moduleName[7], $method[9]))   && p('resolvedBuild,comment')    && e('解决版本,备注');                                  // 测试moduleName值为bug，method为resolve
r($customTester->getFormFieldsTest($moduleName[8], $method[0]))   && p()                           && e('0');                                              // 测试moduleName值为testcase，method为空
r($customTester->getFormFieldsTest($moduleName[8], $method[1]))   && p('stage,pri')                && e('适用阶段,优先级');                                // 测试moduleName值为testcase，method为create
r($customTester->getFormFieldsTest($moduleName[8], $method[2]))   && p('stage,pri')                && e('适用阶段,优先级');                                // 测试moduleName值为testcase，method为edit
r($customTester->getFormFieldsTest($moduleName[9], $method[0]))   && p('desc')                     && e('描述');                                           // 测试moduleName值为testsuite，method为空
r($customTester->getFormFieldsTest($moduleName[9], $method[1]))   && p('desc')                     && e('描述');                                           // 测试moduleName值为testsuite，method为create
r($customTester->getFormFieldsTest($moduleName[9], $method[2]))   && p('desc')                     && e('描述');                                           // 测试moduleName值为testsuite，method为edit
r($customTester->getFormFieldsTest($moduleName[10], $method[0]))  && p('begin,end,members,report') && e('开始时间,结束时间,参与人员,总结');                // 测试moduleName值为testreport，method为空
r($customTester->getFormFieldsTest($moduleName[10], $method[1]))  && p('begin,end,members,report') && e('开始时间,结束时间,参与人员,总结');                // 测试moduleName值为testreport，method为create
r($customTester->getFormFieldsTest($moduleName[10], $method[2]))  && p('begin,end,members,report') && e('开始时间,结束时间,参与人员,总结');                // 测试moduleName值为testreport，method为edit
r($customTester->getFormFieldsTest($moduleName[11], $method[0]))  && p('desc')                     && e('描述');                                           // 测试moduleName值为caselib，method为空
r($customTester->getFormFieldsTest($moduleName[11], $method[1]))  && p('desc')                     && e('描述');                                           // 测试moduleName值为caselib，method为create
r($customTester->getFormFieldsTest($moduleName[11], $method[2]))  && p('desc')                     && e('描述');                                           // 测试moduleName值为caselib，method为edit
r($customTester->getFormFieldsTest($moduleName[12], $method[0]))  && p('owner,pri,desc')           && e('负责人,优先级,描述');                             // 测试moduleName值为caselib，method为空
r($customTester->getFormFieldsTest($moduleName[12], $method[1]))  && p('owner,pri,desc')           && e('负责人,优先级,描述');                             // 测试moduleName值为testtask，method为create
r($customTester->getFormFieldsTest($moduleName[12], $method[2]))  && p('owner,pri,desc')           && e('负责人,优先级,描述');                             // 测试moduleName值为testtask，method为edit
r($customTester->getFormFieldsTest($moduleName[12], $method[10])) && p('owner,pri,desc')           && e('负责人,优先级,描述');                             // 测试moduleName值为testtask，method为importUnit
r($customTester->getFormFieldsTest($moduleName[13], $method[0]))  && p('keywords,content')         && e('关键字,文档正文');                                // 测试moduleName值为doc，method为空
r($customTester->getFormFieldsTest($moduleName[13], $method[1]))  && p('keywords,content')         && e('关键字,文档正文');                                // 测试moduleName值为doc，method为create
r($customTester->getFormFieldsTest($moduleName[13], $method[2]))  && p('keywords,content')         && e('关键字,文档正文');                                // 测试moduleName值为doc，method为edit
r($customTester->getFormFieldsTest($moduleName[14], $method[0]))  && p()                           && e('0');                                              // 测试moduleName值为user，method为空
r($customTester->getFormFieldsTest($moduleName[14], $method[1]))  && p('dept,role,email,commiter') && e('部门,职位,邮箱,源代码帐号');                      // 测试moduleName值为user，method为create
r($customTester->getFormFieldsTest($moduleName[14], $method[2]))  && p('skype,qq,mobile,phone')    && e('Skype,QQ,手机,电话');                             // 测试moduleName值为user，method为edit
