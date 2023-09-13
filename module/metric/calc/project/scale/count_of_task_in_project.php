<?php
/**
 * 按项目统计的任务总数。
 * Count of task in project.
 *
 * 范围：project
 * 对象：task
 * 目的：scale
 * 度量名称：按项目统计的任务总数
 * 单位：个
 * 描述：按项目统计的任务总数是指整个项目当前存在的任务总量。该度量项可以用来跟踪任务的规模和复杂性，为资源分配和工作计划提供基础。较大的任务总数可能需要更多的资源和时间来完成，而较小的任务总数可能意味着项目负荷较轻或项目进展较好。
 * 定义：项目中所有的任务个数求和;过滤已删除的任务;过滤已删除执行的任务;过滤已删除的项目;
 * 度量库：
 * 收集方式：realtime
 *
 * @copyright Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @author    zhouixn <zhouxin@easycorp.ltd>
 * @package
 * @uses      func
 * @license   ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @Link      https://www.zentao.net
 */
class count_of_task_in_project extends baseCalc
{
    public $dataset = 'getTasks';

    public $fieldList = array('t1.project');

    public $result = array();

    public function calculate($row)
    {
        if(!isset($this->result[$row->project])) $this->result[$row->project] = 0;
        $this->result[$row->project] += 1;
    }

    public function getResult($options = array())
    {
        $records = $this->getRecords(array('project', 'value'));
        return $this->filterByOptions($records, $options);
    }
}
