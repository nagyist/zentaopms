<?php
declare(strict_types=1);
/**
 * Create view of program plan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      chen.tao <chentao@easycorp.ltd>
 * @package     programplan
 * @link        https://www.zentao.net
 */

namespace zin;

include($this->app->getModuleRoot() . 'ai/ui/inputinject.html.php');

$fields = $this->config->programplan->form->create;

/* Generate title that is tailored to specific situation. */
$title = $lang->programplan->create;
if($planID) $title = $programPlan->name . $lang->project->stage . '（' . $programPlan->begin . $lang->project->to . $programPlan->end . '）';

/* Generate product list dropdown menu while stage by product. */
$fnGenerateStageByProductList = function() use ($productID, $productList, $project)
{
    if(empty($productList) || $project->stageBy != 'product') return null;

    $defaultName = $productID != 0 ? zget($productList,$productID) : current($productList);

    $items = array();
    foreach($productList as $key => $product)
    {
        $items[] = array('text' => $product, 'active' => $productID == $key, 'url' => createLink('programplan', 'create', "projectID=$project->id&productID=$key"));
    }

    return dropdown
    (
        $defaultName,
        span(setClass('caret')),
        set::items($items)
    );
};

/* Generate checkboxes for sub-stage management. */
$fnGenerateSubPlanManageFields = function() use ($lang, $planID, $project, $executionType)
{
    if(empty($planID) || !in_array($project->model, array('waterfallplus', 'ipd'))) return div();

    $typeList = $project->model == 'ipd' ? $lang->stage->ipdTypeList : $lang->programplan->typeList;

    $items = array();
    if(count($typeList) > 1)
    {
        foreach($typeList as $key => $value)
        {
            $items[] = div(setClass('px-1'), checkbox
            (
                set::type('radio'),
                set::name('executionType'),
                set::text($value),
                set::value($key),
                on::change('window.onChangeExecutionType'),
                set::checked($key == $executionType)
            ));
        }
    }
    else
    {
        $items[] = div(setClass('px-1'), zget($typeList, $executionType));
    }

    /* Append method tip. */
    $items[] = icon(
                'help',
                setID('methodTip'),
                setClass('ml-2 text-gray'),
                setData(array('toggle' => 'tooltip', 'title' => $lang->programplan->methodTip, 'placement' => 'right', 'type' => 'white', 'class-name' => 'text-gray border border-light')),
            );

    $items[] = tooltip(
        set::_to('#methodTip'),
        set::title($lang->programplan->methodTip),
        set::placement('right'),
        set::type('white'),
        setClass('text-darker border border-light')
    );

    return div
    (
        setClass('flex w-1/2 items-center'),
        div(setClass('font-bold'), $lang->programplan->subPlanManage . ':'),
        $items
    );
};

/* Generate form fields. */
$fnGenerateFields = function() use ($config, $lang, $requiredFields, $showFields, $fields, $PMUsers, $enableOptionalAttr, $programPlan, $planID, $executionType, $project)
{
    $items   = array();
    $items[] = array('name' => 'id', 'label' => $lang->idAB, 'control' => 'index', 'width' => '32px');

    $fields['attribute']['required'] = $fields['acl']['required'] = true;
    if(isset($requiredFields['code'])) $fields['code']['required'] = true;

    $renderFields = implode(',', array_keys($requiredFields));
    $renderFields = ",$renderFields,$showFields,";

    foreach($fields as $name => $field)
    {
        $field['name'] = $name;
        if(!empty($field['default'])) $field['value'] = $field['default'];

        /* Convert 'options' to 'items'. */
        if(!empty($field['options'])) $field['items'] = $field['options'];
        unset($field['options']);

        /* Assgn item data to PM field. */
        if($name == 'PM') $field['items'] = $PMUsers;

        /* Set hidden attribute. */
        if(!str_contains($renderFields, ",$name,")) $field['hidden'] = true;

        /* Sub-stage. */
        if($name == 'attribute' && !$enableOptionalAttr)
        {
            $field['disabled'] = true;
            $field['value']    = $programPlan->attribute;
        }

        if($name == 'acl' && $planID)
        {
            $field['disabled'] = true;
            $field['value']    = empty($programPlan) ? 'open' : $programPlan->acl;
        }

        /* Field for agileplus. */
        if($name == 'type' && !empty($planID) && in_array($project->model, array('waterfallplus', 'ipd')))
        {
            $field['hidden'] = $executionType == 'stage';
            $field['items']  = $lang->execution->typeList;
        }
        if($name == 'milestone') $field['width'] = '100px';

        $items[] = $field;
    }

    return $items;
};

/* Generate default rendering data. */
$fnGenerateDefaultData = function() use ($config, $plans, $planID, $stages, $executionType)
{
    $items = array();

    /* Created a new project witho no stages. */
    if(empty($plans) && $planID == 0)
    {
        foreach($stages as $stage)
        {
            $item = new stdClass();

            $item->name      = $stage->name;
            $item->code      = isset($stage->code) ? $stage->code : '';
            $item->percent   = $stage->percent;
            $item->attribute = $stage->type;
            $item->acl       = 'open';
            $item->milestone = 0;

            $items[] = $item;
        }

        return $items;
    }

    /* Create stages for exist project. */
    foreach($plans as $plan)
    {
        $item = new stdClass();

        $item->disabled     = !isset($plan->setMilestone);
        $item->id           = $plan->id;
        $item->type         = $plan->type;
        $item->name         = $plan->name;
        $item->code         = $plan->code;
        $item->PM           = $plan->PM;
        $item->percent      = $plan->percent;
        $item->attribute    = $plan->attribute;
        $item->acl          = $plan->acl;
        $item->milestone    = $plan->milestone;
        $item->begin        = $plan->begin;
        $item->end          = $plan->end;
        $item->realBegan    = $plan->realBegan;
        $item->realEnd      = $plan->realEnd;
        $item->desc         = $plan->desc;
        $item->setMilestone = isset($plan->setMilestone) ? $plan->setMilestone : false;
        $item->order        = $plan->order;
        if(in_array($config->edition, array('max', 'ipd')) && $executionType == 'stage')
        {
            $item->output = empty($plan->output) ? 0 : explode(',', $plan->output);
        }

        $items[] = $item;
    }

    return $items;
};

/* ZIN: layout. */
jsVar('projectID', $project->id);
jsVar('productID', $productID);
jsVar('planID',    $planID);
jsVar('type',      $executionType);

featureBar(li
(
    setClass('nav-item'),
    a
    (
        setClass('active'),
        $title
    ),
    $fnGenerateStageByProductList()
));

toolbar
(
    backBtn(set::icon('back'), setClass('primary'), $lang->goback),
);

formBatchPanel
(
    setID('dataform'),
    set::onRenderRow(jsRaw('window.onRenderRow')),
    to::headingActions(array($fnGenerateSubPlanManageFields())),
    set::customFields(array('list' => $customFields, 'show' => explode(',', $showFields), 'key' => 'createFields')),
    set::items($fnGenerateFields()),
    set::data($fnGenerateDefaultData())
);
