<?php
$lang->gitlab = new stdclass;
$lang->gitlab->common               = 'GitLab';
$lang->gitlab->browse               = '浏览GitLab';
$lang->gitlab->create               = '添加GitLab';
$lang->gitlab->edit                 = '编辑GitLab';
$lang->gitlab->view                 = '查看GitLab';
$lang->gitlab->bindUser             = '绑定用户';
$lang->gitlab->webhook              = 'webhook';
$lang->gitlab->bindProduct          = '关联产品';
$lang->gitlab->importIssue          = '关联issue';
$lang->gitlab->delete               = '删除GitLab';
$lang->gitlab->confirmDelete        = '确认删除该GitLab吗？';
$lang->gitlab->gitlabAccount        = 'GitLab用户';
$lang->gitlab->zentaoAccount        = '禅道用户';
$lang->gitlab->bindingStatus        = '绑定状态';
$lang->gitlab->binded               = '已绑定';
$lang->gitlab->serverFail           = '连接GitLab服务器异常，请检查GitLab服务器。';
$lang->gitlab->lastUpdate           = '最后更新';
$lang->gitlab->confirmAddWebhook    = '您确定创建Webhook吗？';
$lang->gitlab->failCreateWebhook    = 'Webhook创建失败，请查看日志';
$lang->gitlab->manageProjectMembers = '项目成员管理';

$lang->gitlab->browseAction  = 'GitLab列表';
$lang->gitlab->deleteAction  = '删除GitLab';
$lang->gitlab->gitlabProject = "{$lang->gitlab->common}项目";
$lang->gitlab->browseProject = "{$lang->gitlab->common}项目列表";
$lang->gitlab->browseUser    = "用户列表";
$lang->gitlab->browseGroup   = "群组列表";
$lang->gitlab->gitlabIssue   = "{$lang->gitlab->common} issue";
$lang->gitlab->zentaoProduct = '禅道产品';
$lang->gitlab->objectType    = '类型'; // task, bug, story

$lang->gitlab->id             = 'ID';
$lang->gitlab->name           = "{$lang->gitlab->common}名称";
$lang->gitlab->url            = '服务地址';
$lang->gitlab->token          = 'Token';
$lang->gitlab->defaultProject = '默认项目';
$lang->gitlab->private        = 'MD5验证';

$lang->gitlab->lblCreate  = '添加GitLab服务器';
$lang->gitlab->desc       = '描述';
$lang->gitlab->tokenFirst = 'Token不为空时，优先使用Token。';
$lang->gitlab->tips       = '使用密码时，请在GitLab全局安全设置中禁用"防止跨站点请求伪造"选项。';

$lang->gitlab->placeholder = new stdclass;
$lang->gitlab->placeholder->name  = '';
$lang->gitlab->placeholder->url   = "请填写Gitlab Server首页的访问地址，如：https://gitlab.zentao.net。";
$lang->gitlab->placeholder->token = "请填写具有admin权限账户的access token";

$lang->gitlab->noImportableIssues = "目前没有可供导入的issue。";
$lang->gitlab->tokenError         = "当前token非管理员权限。";
$lang->gitlab->tokenLimit         = "GitLab Token权限不足。请更换为有管理员权限的GitLab Token。";
$lang->gitlab->hostError          = "无效的GitLab服务地址。";
$lang->gitlab->bindUserError      = "不能重复绑定用户 %s";
$lang->gitlab->importIssueError   = "未选择该issue所属的执行。";
$lang->gitlab->importIssueWarn    = "存在导入失败的issue，可再次尝试导入。";

$lang->gitlab->accessLevels[10] = '客人';
$lang->gitlab->accessLevels[20] = '报告者';
$lang->gitlab->accessLevels[30] = '开发者';
$lang->gitlab->accessLevels[40] = '维护者';
$lang->gitlab->accessLevels[50] = '所有者';

$lang->gitlab->project = new stdclass;
$lang->gitlab->project->id                         = "项目ID";
$lang->gitlab->project->name                       = "项目名称";
$lang->gitlab->project->create                     = "添加{$lang->gitlab->gitlabProject}";
$lang->gitlab->project->edit                       = "编辑{$lang->gitlab->gitlabProject}";
$lang->gitlab->project->url                        = "项目 URL";
$lang->gitlab->project->path                       = "项目标识串";
$lang->gitlab->project->description                = "项目描述";
$lang->gitlab->project->visibility                 = "可见性级别";
$lang->gitlab->project->visibilityList['private']  = "私有(项目访问必须明确授予每个用户。 如果此项目是在一个群组中，群组成员将会获得访问权限)";
$lang->gitlab->project->visibilityList['internal'] = "内部(除外部用户外，任何登录用户均可访问该项目)";
$lang->gitlab->project->visibilityList['public']   = "公开(该项目允许任何人访问)";
$lang->gitlab->project->star                       = "星标";
$lang->gitlab->project->fork                       = "派生";
$lang->gitlab->project->mergeRequests              = "合并请求";
$lang->gitlab->project->issues                     = "议题";
$lang->gitlab->project->tagList                    = "主题";
$lang->gitlab->project->tagListTips                = "用逗号分隔主题。";
$lang->gitlab->project->emptyError                 = "项目名称或标识符不能为空";
$lang->gitlab->project->confirmDelete              = '确认删除该GitLab项目吗？';
$lang->gitlab->project->notbindedError             = '还没绑定Gitlab用户，无法修改权限！';

$lang->gitlab->user = new stdclass;
$lang->gitlab->user->id             = "用户ID";
$lang->gitlab->user->name           = "名称";
$lang->gitlab->user->username       = "用户名";
$lang->gitlab->user->email          = "电子邮件";
$lang->gitlab->user->password       = "密码";
$lang->gitlab->user->passwordRepeat = "重复密码";
$lang->gitlab->user->projectsLimit  = "限制项目";
$lang->gitlab->user->canCreateGroup = "可创建组";
$lang->gitlab->user->external       = "外部人员";
$lang->gitlab->user->externalTip    = "除非明确授予访问权限，否则外部用户无法查看内部或私有项目。另外，外部用户无法创建项目，群组或个人代码片段。";
$lang->gitlab->user->bind           = "绑定禅道用户";
$lang->gitlab->user->avatar         = "头像";
$lang->gitlab->user->skype          = "Skype";
$lang->gitlab->user->linkedin       = "Linkedin";
$lang->gitlab->user->twitter        = "Twitter";
$lang->gitlab->user->websiteUrl     = "网站地址";
$lang->gitlab->user->note           = "备注";
$lang->gitlab->user->createOn       = "创建于";
$lang->gitlab->user->lastActivity   = "上次活动";
$lang->gitlab->user->create         = "添加{$lang->gitlab->gitlabAccount}";
$lang->gitlab->user->edit           = "编辑{$lang->gitlab->gitlabAccount}";
$lang->gitlab->user->emptyError     = "不能为空";
$lang->gitlab->user->passwordError  = "二次密码不一致！";
$lang->gitlab->user->bindError      = "该用户已经被绑定！";
$lang->gitlab->user->confirmDelete  = '确认删除该GitLab用户吗？';

$lang->gitlab->group = new stdclass;
$lang->gitlab->group->id                                      = "群组ID";
$lang->gitlab->group->name                                    = "群组名称";
$lang->gitlab->group->path                                    = "群组URL";
$lang->gitlab->group->pathTip                                 = "更改群组URL可能会有意想不到的副作用。";
$lang->gitlab->group->description                             = "群组描述";
$lang->gitlab->group->avatar                                  = "群组头像";
$lang->gitlab->group->avatarTip                               = '文件最大支持200k.';
$lang->gitlab->group->visibility                              = "可见性级别";
$lang->gitlab->group->visibilityList['private']               = "私有(群组及其项目只能由成员查看)";
$lang->gitlab->group->visibilityList['internal']              = "内部(除外部用户外，任何登录用户均可查看该组和任何内部项目)";
$lang->gitlab->group->visibilityList['public']                = "公开(群组和任何公共项目可以在没有任何身份验证的情况下查看)";
$lang->gitlab->group->requestAccessEnabledTip                 = "允许用户请求访问(如果可见性是公开或内部的)";
$lang->gitlab->group->lfsEnabled                              = '大文件存储';
$lang->gitlab->group->lfsEnabledTip                           = "允许该组内的项目使用 Git LFS(可以在每个项目中覆盖此设置)";
$lang->gitlab->group->projectCreationLevel                    = "允许创建项目";
$lang->gitlab->group->projectCreationLevelList['noone']       = "禁止";
$lang->gitlab->group->projectCreationLevelList['maintainer']  = "维护者";
$lang->gitlab->group->projectCreationLevelList['developer']   = "开发者 + 维护者";
$lang->gitlab->group->subgroupCreationLevel                   = "允许创建子群组";
$lang->gitlab->group->subgroupCreationLevelList['owner']      = "所有者";
$lang->gitlab->group->subgroupCreationLevelList['maintainer'] = "维护者";
$lang->gitlab->group->create                                  = "添加群组";
$lang->gitlab->group->edit                                    = "编辑群组";
$lang->gitlab->group->createOn                                = "创建于";
$lang->gitlab->group->members                                 = "群组成员";
$lang->gitlab->group->confirmDelete                           = '确认删除该GitLab群组吗？';
$lang->gitlab->group->emptyError                              = "不能为空";
$lang->gitlab->group->manageMembers                           = '群组成员管理';
$lang->gitlab->group->memberName                              = '账号';
$lang->gitlab->group->memberAccessLevel                       = '角色权限';
$lang->gitlab->group->memberExpiresAt                         = '过期时间';
$lang->gitlab->group->repeatError                             = "群组成员不能重复添加";