<?php
declare(strict_types=1);

namespace zin;
class productsBox extends wg
{
    /**
     * Define widget properties.
     *
     * @var    array
     * @access protected
     */
    protected static array $defineProps = array(
        'productItems?: array',       // 产品列表。
        'branchGroups?: array',       // 产品分支分组列表。
        'planGroups?: array',         // 产品计划分组列表。
        'linkedProducts?: array',     // 关联的产品。
        'linkedBranches?: array',     // 关联的分支。
        'currentProduct?: int',       // 来源产品ID。
        'currentPlan?: int',          // 来源计划。
        'productPlans?: array',       // 同来源计划所属产品的计划列表。
        'project?: object',           // 关联的项目。
        'isStage?: bool',             // 是否是阶段类型。
    );

    public static function getPageCSS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public static function getPageJS(): string|false
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    protected function build(): wg
    {
        global $lang, $config;
        list($productItems, $branchGroups, $planGroups, $productPlans) = $this->prop(array('productItems', 'branchGroups', 'planGroups', 'productPlans'));
        list($linkedProducts, $linkedBranches, $currentProduct, $currentPlan) = $this->prop(array('linkedProducts', 'linkedBranches', 'currentProduct', 'currentPlan'));
        list($project, $isStage) = $this->prop(array('project', 'isStage'));

        $productsBox = array();
        if(!empty($project->hasProduct) && $linkedProducts)
        {
            $productsBox = $this->buildLinkedProducts($linkedProducts);
        }
        elseif(!empty($project) && empty($project->hasProduct) && !in_array($project->model, array('waterfall', 'kanban', 'waterfallplus')))
        {
            $productsBox = $this->buildOnlyLinkPlans($productItems);
        }
        else
        {
            $productsBox = $this->initProductsBox();
        }

        return div
        (
            setClass('productsBox'),
            on::click('.productsBox .addLine', 'addNewLine'),
            on::click('.productsBox .removeLine', 'removeLine'),
            on::change('.productsBox [name^=products]', 'loadBranches'),
            jsVar('multiBranchProducts', data('multiBranchProducts')),
            jsVar('productID', $project->id),
            $productsBox
        );
    }

    protected function initProductsBox(): array
    {
        global $lang;
        list($productItems, $project, $isStage) = $this->prop(array('productItems', 'project', 'isStage'));

        $productsBox   = array();
        $hidden        = empty($project->hasProduct) ? 'hidden' : '';
        $productsBox[] = div
        (
            set::className("productBox flex $hidden"),
            formGroup
            (
                set::width('1/2'),
                setClass('linkProduct'),
                set::required($project && in_array($project->model, array('waterfall', 'waterfallplus'))),
                set::label($lang->project->manageProducts),
                picker
                (
                    set::name('products[0]'),
                    set::items($productItems),
                    empty($project->hasProduct) ? set::value(current(array_keys($productItems))) : null,
                )
            ),
            formGroup
            (
                set::width('1/4'),
                setClass('hidden linkBranch ml-px'),
                set::label(''),
                inputGroup
                (
                    setClass('branchBox'),
                    picker
                    (
                        set::name('branch[0][]'),
                        set::items(array()),
                        set::multiple(true),
                        on::change("branchChange")
                    )
                )
            ),
            formGroup
            (
                set::width('1/2'),
                set::label($lang->project->associatePlan),
                set::className('planBox'),
                inputGroup
                (
                    set::id("plan0"),
                    picker
                    (
                        set::name('plans[0][]'),
                        set::items($productPlans),
                        set::multiple(true)
                    )
                ),
            ),
            $isStage && $project->stageBy == 'product' ? null : div
            (
                setClass('pl-2 flex self-center line-btn c-actions'),
                btn
                (
                    setClass('btn btn-link text-gray addLine'),
                    icon('plus')
                ),
                btn
                (
                    setClass('btn btn-link text-gray removeLine'),
                    setClass('hidden'),
                    icon('trash')
                )
            )
        );

        return $productsBox;
    }

    protected function buildOnlyLinkPlans(array $productItems): array
    {
        global $lang;
        list($currentPlan, $productPlans) = $this->prop(array('currentPlan', 'productPlans'));

        $planProductID = current(array_keys($productItems));
        $productsBox   = array();
        $productsBox[] = div
        (
            set::className('productBox'),
            formGroup
            (
                set::width('1/2'),
                set::label($lang->execution->linkPlan),
                set::className('planBox'),
                picker
                (
                    set::name("plans[{$planProductID}][]"),
                    set::items(!empty($productPlans) ? $productPlans : array()),
                    set::value($currentPlan),
                    set::multiple(true),
                    formHidden('products[]', $planProductID),
                    formHidden('branch[0][0]', 0)
                )
            )
        );

        return $productsBox;
    }

    protected function buildLinkedProducts(array $linkedProducts): array
    {
        if(empty($linkedProducts)) return array();

        global $lang;
        list($productItems, $branchGroups, $planGroups, $productPlans) = $this->prop(array('productItems', 'branchGroups', 'planGroups', 'productPlans'));
        list($linkedBranches, $currentProduct, $currentPlan) = $this->prop(array('linkedBranches', 'currentProduct', 'currentPlan'));
        list($project, $isStage) = $this->prop(array('project', 'isStage'));

        $linkedProductsBox = array();
        foreach(array_values($linkedProducts) as $i => $product)
        {
            $hasBranch = $product->type != 'normal' && isset($branchGroups[$product->id]);
            $branches  = isset($branchGroups[$product->id]) ? $branchGroups[$product->id] : array();

            $branchIdList = '';
            if(isset($product->branches)) $branchIdList = implode(',', $product->branches);
            if(!empty($linkedBranches[$product->id]))  $branchIdList = $linkedBranches[$product->id];

            if(empty($currentProduct) || ($currentProduct != $product->id))
            {
                $plans  = isset($planGroups[$product->id]) ? $planGroups[$product->id] : array();
                $planID = isset($product->plans) ? implode(',', $product->plans) : '';
            }
            else
            {
                $plans  = !empty($productPlans) ? $productPlans : array();
                $planID = isset($currentPlan) && isset($productPlans[$currentPlan]) ? $currentPlan : '';
            }

            $linkedProductsBox[] = div
            (
                set::className('productBox flex'),
                formGroup
                (
                    set::width($hasBranch ? '1/4' : '1/2'),
                    setClass('linkProduct'),
                    set::required($project && in_array($project->model, array('waterfall', 'waterfallplus'))),
                    $i == 0 ? set::label($lang->project->manageProducts) : null,
                    inputGroup
                    (
                        div
                        (
                            setClass('grow'),
                            picker
                            (
                                set::name("products[$i]"),
                                set::value($product->id),
                                set::items($productItems),
                                set::last($product->id),
                                set::disabled($isStage && $project->stageBy == 'project'),
                                $isStage && $project->stageBy == 'project' ? formHidden("products[$i]", $product->id) : null
                            )
                        )
                    )
                ),
                formGroup
                (
                    set::width('1/4'),
                    setClass('ml-px linkBranch'),
                    set::label(''),
                    $hasBranch ? null : setClass('hidden'),
                    inputGroup
                    (
                        setClass('branchBox'),
                        picker
                        (
                            set::name("branch[$i][]"),
                            set::items($branches),
                            set::value(is_array($branchIdList) ? implode(',', $branchIdList) : $branchIdList),
                            set::disabled($isStage && $project->stageBy == 'project'),
                            set::multiple(true),
                            on::change("branchChange")
                        )
                    )
                ),
                formGroup
                (
                    set::width('1/2'),
                    $i == 0 ? set::label($lang->project->associatePlan) : null,
                    set::className('planBox'),
                    set::id("plan{$i}"),
                    inputGroup
                    (
                        set::id("plan{$i}"),
                        picker
                        (
                            set::name("plans[$product->id][]"),
                            set::items($plans),
                            set::value($planID),
                            set::multiple(true)
                        )
                    )
                ),
                $isStage && $project->stageBy == 'project' ? null : div
                (
                    setClass('pl-2 flex self-center line-btn c-actions'),
                    btn
                    (
                        setClass('btn btn-link text-gray addLine'),
                        icon('plus')
                    ),
                    btn
                    (
                        setClass('btn btn-link text-gray removeLine'),
                        setClass($i == 0 ? 'hidden' : ''),
                        icon('trash')
                    )
                )
            );
        }

        return $linkedProductsBox;
    }
}