<?php
global $lang;
$config->stage->create = new stdclass();
$config->stage->edit   = new stdclass();
if(isset($config->setPercent) && $config->setPercent == 1)
{
    $config->stage->create->requiredFields = 'name,percent,type';
    $config->stage->edit->requiredFields   = 'name,percent,type';
}
else
{
    $config->stage->create->requiredFields = 'name,type';
    $config->stage->edit->requiredFields   = 'name,type';
}

$config->stage->actionList['edit']['icon']        = 'edit';
$config->stage->actionList['edit']['hint']        = $lang->stage->edit;
$config->stage->actionList['edit']['url']         = helper::createLink('stage', 'edit', 'stageID={id}', '', true);
$config->stage->actionList['edit']['data-toggle'] = 'modal';

$config->stage->actionList['delete']['icon'] = 'trash';
$config->stage->actionList['delete']['hint'] = $lang->stage->delete;
$config->stage->actionList['delete']['url']  = 'javascript:confirmDelete("{id}")';
