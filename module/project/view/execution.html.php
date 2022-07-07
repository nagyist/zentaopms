<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/sortable.html.php';?>
<?php js::set('projectID', $projectID);?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <div class='btn-group'>
      <?php $viewName = $productID != 0 ? zget($productList,$productID) : $lang->product->allProduct;?>
      <a href='javascript:;' class='btn btn-link btn-limit' data-toggle='dropdown'><span class='text' title='<?php echo $viewName;?>'><?php echo $viewName;?></span> <span class='caret'></span></a>
      <ul class='dropdown-menu' style='max-height:240px; max-width: 300px; overflow-y:auto'>
        <?php
          $class = '';
          if($productID == 0) $class = 'class="active"';
          echo "<li $class>" . html::a($this->createLink('project', 'execution', "status=$status&projectID=$projectID&orderby=$orderBy"), $lang->product->allProduct) . "</li>";
          foreach($productList as $key => $product)
          {
              $class = $productID == $key ? 'class="active"' : '';
              echo "<li $class>" . html::a($this->createLink('project', 'execution', "status=$status&projectID=$projectID&orderby=$orderBy&productID=$key"), $product) . "</li>";
          }
        ?>
      </ul>
    </div>
    <?php foreach($lang->execution->featureBar['all'] as $key => $label):?>
    <?php $label = "<span class='text'>$label</span>";?>
    <?php if($status == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
    <?php echo html::a($this->createLink('project', 'execution', "status=$key&projectID=$projectID&orderBy=$orderBy&productID=$productID"), $label, '', "class='btn btn-link' id='{$key}Tab'");?>
    <?php endforeach;?>
  </div>
  <div class='btn-toolbar pull-right'>
    <?php common::printLink('execution', 'export', "status=$status&productID=$productID&orderBy=$orderBy&from=project", "<i class='icon-export muted'> </i> " . $lang->export, '', "class='btn btn-link export'")?>
     <?php if(common::hasPriv('programplan', 'create') and $isStage):?>
     <?php echo html::a($this->createLink('programplan', 'create', "projectID=$projectID&productID=$productID"), "<i class='icon icon-plus'></i> " . $lang->programplan->create, '', "class='btn btn-primary'");?>
    <?php else: ?>
    <?php if(common::hasPriv('execution', 'create')) echo html::a($this->createLink('execution', 'create', "projectID=$projectID"), "<i class='icon icon-sm icon-plus'></i> " . $lang->execution->create, '', "class='btn btn-primary create-execution-btn' data-app='execution' onclick='$(this).removeAttr(\"data-toggle\")'");?>
    <?php endif;?>
  </div>
</div>
<div id='mainContent' class="main-row fade">
  <?php if(empty($executionStats)):?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->execution->noExecution;?></span>
      <?php if($status == "all"):?>
        <?php if(common::hasPriv('programplan', 'create') and $isStage):?>
        <?php echo html::a($this->createLink('programplan', 'create', "projectID=$projectID&productID=$productID"), "<i class='icon icon-plus'></i> " . $lang->programplan->create, '', "class='btn btn-info'");?>
        <?php else: ?>
          <?php if(common::hasPriv('execution', 'create')):?>
          <?php echo html::a($this->createLink('execution', 'create', "projectID=$projectID"), "<i class='icon icon-plus'></i> " . $lang->execution->create, '', "class='btn btn-info' data-app='execution'");?>
          <?php endif;?>
        <?php endif;?>
      <?php endif;?>
    </p>
  </div>
  <?php else:?>
  <?php $canBatchEdit = common::hasPriv('execution', 'batchEdit'); ?>
  <form class='main-table' id='executionsForm' method='post' action='<?php echo inLink('batchEdit');?>' data-ride='table'>
    <div class="table-header fixed-right">
      <table class="table table-from has-sort-head table-fixed table-nested" id="executionList">
        <thead>
          <tr>
            <th class='table-nest-title'>
              <a class='table-nest-toggle table-nest-toggle-global' data-expand-text='<?php echo $lang->expand; ?>' data-   collapse-text='<?php echo $lang->collapse;?>'></a>
              <?php echo $lang->idAB;?>
            </th>
            <th class='c-progress'><?php echo $lang->programplan->name;?></th>
            <th class='c-progress'><?php echo $lang->execution->owner;?></th>
            <th class='c-progress'><?php echo $lang->programplan->status;?></th>
            <th class='c-progress'><?php echo $lang->project->progress;?></th>
            <th class='c-progress'><?php echo $lang->programplan->begin;?></th>
            <th class='c-progress'><?php echo $lang->programplan->end;?></th>
            <th class='c-progress'><?php echo $lang->task->estimateAB;?></th>
            <th class='c-progress'><?php echo $lang->task->consumedAB;?></th>
            <th class='c-progress'><?php echo $lang->task->leftAB;?> </th>
            <th class='c-progress'><?php echo $lang->execution->burn;?> </th>
            <th class='text-center c-actions-6'><?php echo $lang->actions;?></th> 
          </tr>
        </thead>
        <tbody id="executionTableList">
          <?php foreach($executionStats as $stage):?>
          <tr>
            <td><?php echo $stage->id;?></td>
            <td><?php echo $stage->name;?></td>
            <td><?php echo $stage->id;?></td>
            <td><?php echo $stage->name;?></td>
            <td><?php echo $stage->id;?></td>
            <td><?php echo $stage->name;?></td>
            <td><?php echo $stage->id;?></td>
            <td><?php echo $stage->name;?></td>
            <td><?php echo $stage->id;?></td>
            <td><?php echo $stage->name;?></td>
            <td><?php echo $stage->name;?></td>
            <td><?php echo $stage->name;?></td>
          </tr>

          <?php if(!empty($stage->children)):?>
          <?php foreach($stage as $childStage):?>
          <tr>
            <td><?php echo $childStage->id;?></td>
            <td><?php echo $childStage->name;?></td>
            <td><?php echo $childStage->id;?></td>
            <td><?php echo $childStage->name;?></td>
            <td><?php echo $childStage->id;?></td>
            <td><?php echo $childStage->name;?></td>
            <td><?php echo $childStage->id;?></td>
            <td><?php echo $childStage->name;?></td>
            <td><?php echo $childStage->id;?></td>
            <td><?php echo $childStage->name;?></td>
            <td><?php echo $childStage->name;?></td>
            <td><?php echo $childStage->name;?></td>
          </tr>

          <?php if(!empty($childStage->tasks)):?>
          <?php foreach($childStage->tasks as $task):?>
          <tr>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->name;?></td>
          </tr>
          <?php endforeach;?>
          <?php endif;?>

          <?php endforeach;?>
          <?php endif;?>

          <?php if(!empty($stage->tasks)):?>
          <?php foreach($stage->tasks as $task):?>
          <tr>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->id;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->name;?></td>
            <td><?php echo $task->name;?></td>
          </tr>
          <?php endforeach;?>
          <?php endif;?>

          <?php endforeach;?>
        </tbody>
      </table>
      <nav class="btn-toolbar pull-right setting"></nav>
    </div>
  </form>
  <?php endif;?>
</div>
<?php js::set('status', $status)?>
<script>
$("#<?php echo $status;?>Tab").addClass('btn-active-text');
</script>
<?php include '../../common/view/footer.html.php';?>
