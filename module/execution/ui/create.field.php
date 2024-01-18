<?php
namespace zin;
global $lang, $config;

$fields  = defineFieldList('execution.create');
$project = data('project');
$from    = data('from');
$isStage = data('isStage');
$showExecutionExec = ($from == 'execution' || $from == 'doc');

$fields->field('project')
    ->required()
    ->control('picker')
    ->label($lang->execution->projectName)
    ->items(data('allProjects'))
    ->value(data('projectID'));

if(!empty($project->model) && $project->model == 'agileplus')
{
    unset($lang->execution->typeList['stage'], $lang->execution->typeList['']);
    $fields->field('method')
        ->required()
        ->name('type')
        ->labelHint($lang->execution->agileplusMethodTip)
        ->items($lang->execution->typeList)
        ->value(data('execution.type'));
}

$fields->field('name')
    ->wrapBefore(true)
    ->required()
    ->label($showExecutionExec ? $lang->execution->execName : $lang->execution->name)
    ->value(data('execution.name'));

$fields->field('code')
    ->required()
    ->label($showExecutionExec ? $lang->execution->execCode : $lang->execution->code)
    ->value(data('execution.code'));

$fields->field('type')
    ->required()
    ->label($showExecutionExec ? $lang->execution->execType : $lang->execution->type)
    ->name($isStage ? 'attribute' : 'lifetime')
    ->items($isStage ? $lang->stage->typeList : $lang->execution->lifeTimeList);

$plan = data('plan');
$fields->field('dateRange')
    ->required()
    ->control('inputGroup')
    ->items(false)
    ->itemBegin('begin')->control('datePicker')->id('begin')->value(empty($plan->begin) ? date('Y-m-d') : $plan->begin)->placeholder($lang->execution->begin)->itemEnd()
    ->item($lang->project->to)
    ->itemBegin('end')->control('datePicker')->id('end')->value(empty($plan->end) ? '' : $plan->end)->placeholder($lang->execution->end)->itemEnd();

$fields->field('days')
    ->width('1/4')
    ->required(strpos(",{$config->execution->create->requiredFields},", ",days,") !== false)
    ->label($lang->execution->days . sprintf($lang->execution->unitTemplate, $lang->execution->day))
    ->items(false)
    ->value(!empty($plan->begin) ? (helper::workDays($plan->begin, $plan->end) + 1) : '');

$fields->field('percent')
    ->width('1/4')
    ->label($lang->stage->percent . sprintf($lang->execuition->unitTemplate, '%'))
    ->required(true);

$fields->field('productsBox')
    ->width('full')
    ->control(array
    (
        'control'        => 'productsBox',
        'productItems'   => data('allProducts'),
        'branchGroups'   => data('branchGroups'),
        'planGroups'     => data('productPlans'),
        'linkedProducts' => data('products'),
        'linkedBranches' => data('linkedBranches'),
        'currentProduct' => data('productID'),
        'currentPlan'    => data('planID'),
        'productPlans'   => data('productPlan'),
        'project'        => data('project'),
        'isStage'        => data('isStage'),
    ));

$fields->field('desc')
    ->width('full')
    ->label($showExecutionExec ? $lang->execution->execDesc : $lang->execution->desc)
    ->control('editor');

$fields->field('PO')->foldable()->items(data('poUsers'))->value(data('copyExecution.PO'));
$fields->field('QD')->foldable()->items(data('qdUsers'))->value(data('copyExecution.QD'));
$fields->field('PM')->foldable()->items(data('pmUsers'))->value(data('copyExecution.PM'));
$fields->field('RD')->foldable()->items(data('rdUsers'))->value(data('copyExecution.RD'));

$fields->field('teamName')
    ->foldable()
    ->width('full')
    ->name('team')
    ->checkbox(array('text' => $lang->execution->copyTeam, 'id' => 'copyTeam'))
    ->value(data('execution.team'));

$fields->field('teams')
    ->foldable()
    ->hidden(true)
    ->label($lang->execution->copyTeam)
    ->name('teams')
    ->items(data('teams'))
    ->set('data-placeholder', $lang->execution->copyTeamTip);

$fields->field('teamMembers')
    ->width('full')
    ->foldable()
    ->label($lang->execution->team)
    ->name('teamMembers[]')
    ->items(data('users'))
    ->multiple();

$fields->field('acl')
    ->foldable()
    ->width('full')
    ->control(array('control' => 'aclBox', 'aclItems' => $lang->execution->aclList, 'aclValue' => data('execution.acl'), 'whitelistLabel' => $lang->whitelist));