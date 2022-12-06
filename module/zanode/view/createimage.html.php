<?php
/**
 * The close file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      chunsheng wang <chunsheng@cnezsoft.com>
 * @package     task
 * @version     $Id: cancel.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php if($task):?>
<?php js::set('taskID', $task->task);?>
<?php js::set('nodeID', $node->id);?>
<style>.body-modal #mainContent{width:90%}
</style>
<?php endif;?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span><?php echo $lang->zanode->createImage;?></span>
      </h2>
    </div>
    <?php if($task):?>
    <h5 class='text-center'><?php echo $lang->zanode->createImaging;?></h5>
    <div class="progress progress-striped">
      <div class="progress-bar progress-bar-success rate" role="progressbar" aria-valuenow="<?php echo $task->rate;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $task->rate;?>%">
      </div>
    </div>
    <?php $link = helper::createLink('zanode', 'create');?>
    <h6 class='hide text-center success'><?php echo sprintf($lang->zanode->createImageSuccess, $link);?></h6>
    <h6 class='hide text-center fail'><?php echo $lang->zanode->createImageFail;?></h6>
    <?php else:?>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->zanode->imageName;?></th>
          <td><?php echo html::input('name', '', "class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->zanode->desc;?></th>
          <td><?php echo html::textarea('desc', '', "rows='6' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='2' class='text-center'>
            <?php echo html::submitButton('', "onclick='if(confirm(\"{$lang->zanode->createImageNotice}\")==false) return false;'");?>
          </td>
        </tr>
      </table>
    </form>
    <?php endif;?>
  </div>
</div>
<?php if($task):?>
<script>
var setProgress = self.setInterval("getTaskProgress()",1500);

function getTaskProgress()
{
    var url = createLink('zanode', 'ajaxGetTaskStatus', 'nodeID=' + nodeID + '&taskID=' + taskID + '&type=exportVm');
    $.get(url, function(data)
    {
        var rate   = data.rate;
        var status = data.status;

        if(rate == 1 || status == 'completed') rate = 1;

        $('.rate').css('width', rate*100 + '%');
        if(rate == 1 || (status != 'inprogress' && status != 'created'))
        {
            updateStatus(data);
            clearInterval(setProgress);
        }
    }, 'json');
}

function updateStatus(data)
{
    var url      = createLink('zanode', 'ajaxUpdateImage', 'taskID=' + taskID)
    var postData = {"status":data.status, "path":data.path}

    $.post(url, postData, function(result)
    {
        if(data.status == 'completed') $('.success').removeClass('hide');
        else $('.fail').removeClass('hide');
    }, 'json');
}
</script>
<?php endif;;?>
<?php include '../../common/view/footer.html.php';?>
