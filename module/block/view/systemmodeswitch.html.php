<style>
.block-guide .tab-pane .mode-switch .dataTitle {padding: 14px 20px;}
.block-guide .tab-pane .mode-switch .mode-block {background: #E6F0FF; margin-left: 10px; cursor: pointer;}
.block-guide .tab-pane .mode-switch .mode-block:nth-child(2) {margin-left: 8%;}
.block-guide .tab-pane .mode-switch .mode-block:hover, .block-guide .tab-pane .mode-switch .mode-block.active {box-shadow: 0 0 0 2px #2E7FFF;}
.block-guide .tab-pane .mode-switch .mode-desc {padding: 10px; font-size: 12px; color: #5E626D;}
</style>
<?php $usedMode = zget($this->config->global, 'mode', 'light');?>
<?php js::set('usedMode', $usedMode);?>
<?php js::set('hasProgram', !empty($programs));?>
<?php js::set('changeModeTips', sprintf($lang->custom->changeModeTips, $lang->custom->modeList[$usedMode == 'light' ? 'ALM' : 'light']));?>
<div class='table-row mode-switch'>
  <div class="col-4">
    <div class="col dataTitle"><?php echo $lang->block->customModeTip->common;?></div>
    <div class='col pull-left col-md-12'>
      <?php foreach($lang->block->customModes as $mode => $modeName):?>
      <div class="pull-left col-md-5 mode-block<?php if($usedMode == $mode) echo ' active';?>" data-mode='<?php echo $mode;?>'>
        <div><?php echo html::image($config->webRoot . "theme/default/images/guide/{$mode}.png");?></div>
        <div class='mode-desc'>
          <h4><?php echo $modeName;?></h4>
          <?php echo $lang->block->customModeTip->$mode;?>
        </div>
      </div>
      <?php endforeach;?>
    </div>
  </div>
</div>

<div class='modal fade' id='selectProgramModal'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>× </span><span class='sr-only'><?php echo $this->lang->close;?></span></button>
        <h4 class='modal-title'><?php echo $lang->custom->selectDefaultProgram;?></h4>
      </div>
      <div class='modal-body'>
        <div class='alert alert-primary'>
          <p class='text-info'><?php echo $lang->custom->selectProgramTips;?></p>
        </div>
        <table class='table table-form'>
          <tr>
            <th><?php echo $lang->custom->defaultProgram;?></th>
            <td><?php echo html::select('defaultProgram', $programs, $programID, "class='form-control chosen'");?></td>
          </tr>
        </table>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary btn-wide btn-save'><?php echo $lang->save;?></button>
      </div>
    </div>
  </div>
</div>

<script>
$(function()
{
    var selectedMode = usedMode;

    /**
     * Switch system mode.
     *
     * @access public
     * @return void
     */
    function switchMode()
    {
        parent.location.reload();
        return;

        if(selectedMode == usedMode) return;

        var postData = {mode: selectedMode};
        if(selectedMode == 'light' && hasProgram) postData.program = $('#defaultProgram').val();
        $.post(createLink('custom', 'mode'), postData, function(result)
        {
            if(result.result == 'success') parent.location.reload();
        });
    }

    var $nav = $('#<?php echo "tab3{$blockNavId}ContentsystemMode";?>');
    $nav.on('click', '.mode-block', function()
    {
        selectedMode = $(this).data('mode');
        if(selectedMode == usedMode) return;

        var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop || $('#guideBody').offset().top;
        localStorage.setItem('systemModePosition', scrollTop);

        if(selectedMode == 'light' && hasProgram)
        {
            $('#selectProgramModal').modal('show');
        }
        else
        {
            bootbox.confirm(changeModeTips, function(result)
            {
                if(result) switchMode();
            });
        }
    }).on('click', '#selectProgramModal .btn-save', switchMode);
});
</script>
