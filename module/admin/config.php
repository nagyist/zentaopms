<?php
$config->admin->apiRoot = 'https://www.zentao.net';

$config->admin->log = new stdclass();
$config->admin->log->saveDays = 30;

if(!isset($config->safe))       $config->safe = new stdclass();
if(!isset($config->safe->weak)) $config->safe->weak = '123456,password,12345,12345678,qwerty,123456789,1234,1234567,abc123,111111,123123';

$config->admin->menuGroup['system']    = array('custom|mode', 'backup', 'cron', 'action|trash', 'admin|xuanxuan', 'setting|xuanxuan', 'admin|license', 'admin|checkweak', 'admin|resetpwdsetting', 'admin|safe', 'custom|timezone', 'search|buildindex', 'admin|tableengine', 'ldap', 'custom|libreoffice');
$config->admin->menuGroup['user']      = array('dept', 'company', 'user', 'group');
$config->admin->menuGroup['switch']    = array('admin|setmodule');
$config->admin->menuGroup['model']     = array('auditcl', 'stage', 'design', 'cmcl', 'reviewcl', 'custom|required', 'custom|set', 'custom|flow', 'custom|code', 'custom|estimate', 'subject', 'process');
$config->admin->menuGroup['feature']   = array('custom|set', 'custom|product', 'custom|execution', 'custom|required', 'custom|kanban', 'approvalflow', 'measurement', 'meetingroom', 'custom|browsestoryconcept', 'custom|kanban');
$config->admin->menuGroup['template']  = array('custom|set', 'baseline');
$config->admin->menuGroup['message']   = array('mail', 'webhook', 'sms', 'message');
$config->admin->menuGroup['dev']       = array('dev', 'entry');
$config->admin->menuGroup['extension'] = array('extension');
$config->admin->menuGroup['convert']   = array('convert');

$config->admin->menuModuleGroup['model']['custom|set']        = array('project', 'issue', 'risk', 'opportunity', 'nc');
$config->admin->menuModuleGroup['model']['custom|required']   = array('project', 'build');
$config->admin->menuModuleGroup['feature']['custom|set']      = array('todo', 'feedback', 'user', 'block', 'story', 'task', 'bug', 'testcase', 'testtask', 'feedback', 'user');
$config->admin->menuModuleGroup['feature']['custom|required'] = array('bug', 'doc', 'product', 'story', 'productplan', 'release', 'task', 'bug', 'testcase', 'testsuite', 'testtask', 'testreport', 'caselib', 'doc', 'feedback', 'user');
$config->admin->menuModuleGroup['template']['custom|set']     = array('baseline');

$config->admin->plugins[203] = new stdClass();
$config->admin->plugins[203]->name     = '人力资源日历';
$config->admin->plugins[203]->desc     = '禅道人力资源日历插件可以帮助项目管理者查看不同维度下的消耗工时、未完成工作量、并行工作量及负载率，同时组织维度待处理状态下实现了模拟负载的功能，帮助管理者详细了解成员的工作负载情况，更好的进行项目人力资源调度。';
$config->admin->plugins[203]->viewLink = 'https://www.zentao.net/extension-viewExt-203.html';

$config->admin->plugins[198] = new stdClass();
$config->admin->plugins[198]->name     = '需求池插件';
$config->admin->plugins[198]->desc     = '本插件为禅道需求池管理插件，包括：创建需求池 需求池权限管理 收集需求并向需求池录入需求 记录需求提出人信息 评审需求 拆分用户需求、研发需求 需求跟踪矩阵';
$config->admin->plugins[198]->viewLink = 'https://www.zentao.net/extension-viewExt-198.html';

$config->admin->plugins[27] = new stdClass();
$config->admin->plugins[27]->name     = 'Excel导出/导入';
$config->admin->plugins[27]->desc     = '安装该插件可以支持Excel导出和任务、需求、Bug、用例导入功能。更新提示：新增任务、需求、Bug的excel导入功能。';
$config->admin->plugins[27]->viewLink = 'https://www.zentao.net/extension-viewExt-27.html';

$config->admin->plugins[26] = new stdClass();
$config->admin->plugins[26]->name     = '甘特图';
$config->admin->plugins[26]->desc     = '禅道甘特图插件 运用此插件可以查看甘特图，维护任务关系。';
$config->admin->plugins[26]->viewLink = 'https://www.zentao.net/extension-viewExt-26.html';

$config->admin->plugins[30] = new stdClass();
$config->admin->plugins[30]->name     = '日志日历';
$config->admin->plugins[30]->desc     = '运用此插件可以实现工作日志的添加、编辑、查看、删除、导出等功能，方便用户管理工作日志。';
$config->admin->plugins[30]->viewLink = 'https://www.zentao.net/extension-viewExt-30.html';

$config->admin->plugins[194] = new stdClass();
$config->admin->plugins[194]->name     = '应用巡检报告';
$config->admin->plugins[194]->desc     = '每日生成公司级禅道应用巡检报告，促进项目管理持续改进。';
$config->admin->plugins[194]->viewLink = 'https://www.zentao.net/extension-viewExt-194.html';

$config->admin->apiRoot      = 'https://www.zentao.net/';
$config->admin->classURL     = 'https://www.zentao.net/publicclass.html';
$config->admin->extensionURL = 'https://www.zentao.net/extension-browse.html';
$config->admin->patchURL     = 'https://www.zentao.net/extension-browse-byModule-1218.html';
$config->admin->downloadURL  = 'https://www.zentao.net/download.html';
$config->admin->liteMenuList = array('system', 'user', 'feature', 'message', 'extension', 'dev');

$config->admin->helpURL['system']    = 'https://www.zentao.net/book/zentaopms/538.html';
$config->admin->helpURL['user']      = 'https://www.zentao.net/book/zentaopms/538.html';
$config->admin->helpURL['switch']    = 'https://www.zentao.net/book/zentaopms/538.html';
$config->admin->helpURL['model']     = 'https://www.zentao.net/book/zentaopms/533.html';
$config->admin->helpURL['feature']   = 'https://www.zentao.net/book/zentaopms/538.html';
$config->admin->helpURL['template']  = 'https://www.zentao.net/book/zentaopms/538.html';
$config->admin->helpURL['message']   = 'https://www.zentao.net/book/zentaopms/email-notification-541.html';
$config->admin->helpURL['extension'] = 'https://www.zentao.net/book/zentaopms/536.html';
$config->admin->helpURL['dev']       = 'https://www.zentao.net/book/zentaopms/537.html';
$config->admin->helpURL['convert']   = 'https://www.zentao.net/book/zentaopms/656.html';
