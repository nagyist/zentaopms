<?php
    $title          = $datas->title;
    $requiredFields = $datas->requiredFields;
    $allCount       = $datas->allCount;
    $allPager       = $datas->allPager;
    $pagerID        = $datas->pagerID;
    $isEndPage      = $datas->isEndPage;
    $maxImport      = $datas->maxImport;
    $dataInsert     = $datas->dataInsert;
    $fields         = $datas->fields;
    $suhosinInfo    = $datas->suhosinInfo;
    $model          = $datas->model;
    $datas          = $datas->datas;
?>
<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
<style>
th {width:150px;}
.c-pri, .c-severity, .c-deadline, .c-openedBuild{width:100px;}
.c-steps {width:150px;}

</style>
<?php if(!empty($suhosinInfo)):?>
<div class='alert alert-info'><?php echo $suhosinInfo?></div>
<?php elseif(empty($maxImport) and $allCount > $this->config->file->maxImport):?>
<div id="mainContent" class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->port->import;?></h2>
  </div>
  <p><?php echo sprintf($lang->file->importSummary, $allCount, html::input('maxImport', $config->file->maxImport, "style='width:50px'"), ceil($allCount / $config->file->maxImport));?></p>
  <p><?php echo html::commonButton($lang->import, "id='import'", 'btn btn-primary');?></p>
</div>
<script>
$(function()
{
    $('#maxImport').keyup(function()
    {
        if(parseInt($('#maxImport').val())) $('#times').html(Math.ceil(parseInt($('#allCount').html()) / parseInt($('#maxImport').val())));
    });

    $('#import').click(function()
    {
        $.cookie('maxImport', $('#maxImport').val())
        location.href = "<?php echo $this->app->getURI()?>";
    })
});
</script>
<?php else:?>
<?php js::set('requiredFields', $requiredFields);?>
<?php js::set('page', 'showImport');?>
<div id="mainContent" class="main-content">
  <div class="main-header clearfix">
    <h2><?php echo $lang->port->import;?></h2>
  </div>
  <form class='main-form' target='hiddenwin' method='post'>
    <table class='table table-form' id='showData'>
      <thead>
        <tr>
          <th class='w-70px'> <?php echo $lang->port->id?></th>
          <?php foreach($fields as $key => $value):?>
          <?php if($value['control'] != 'hidden'):?>
          <th class='c-<?php echo $key?>'  id='<?php echo $key;?>'>  <?php echo $value['title'];?></th>
          <?php endif;?>
          <?php endforeach;?>
        </tr>
      </thead>
      <tbody>
        <?php
        $insert  = true;
        $addID   = 1;
        $colspan = 1;
        ?>
        <?php foreach($datas as $key => $object):?>
        <tr class='text-top'>
          <td>
            <?php
            if(!empty($object->id))
            {
                echo $object->id . html::hidden("id[$key]", $object->id);
                $insert = false;
            }
            else
            {
                echo $addID++ . " <sub style='vertical-align:sub;color:gray'>{$lang->port->new}</sub>";
            }
            ?>
          </td>

          <?php foreach($fields as $field => $value):?>

          <?php if($value['control'] != 'hidden') $colspan ++?>

          <?php if($value['control'] == 'select'):?>
          <?php $selected = !empty($object->$field) ? $object->$field : '';$list = array();?>
          <?php if(!empty($value['values'][$selected])) $list = array($selected => $value['values'][$selected]);?>
          <?php if(!empty($value['values'][$selected]) and isset($value['values'][0])) $list = array_slice($value['values'], 0, 1);?>
          <td><?php echo html::select("{$field}[$key]", $list, $selected, "class='form-control picker-select' data-field='{$field}' data-key='$key'")?></td>

          <?php elseif($value['control'] == 'multiple'):?>
          <td><?php echo html::select("{$field}[$key][]", $value['values'], !empty($object->$field) ? $object->$field : '', "class='form-control picker-select' data-field='{$field}' data-key='$key' multiple")?></td>

          <?php elseif($value['control'] == 'date'):?>
          <?php $dateMatch = $this->config->port->dateMatch;?>
          <?php if(!preg_match($dateMatch, $object->$field)) $object->$field = ''; ?>
          <td><?php echo html::input("{$field}[$key]", !empty($object->$field) ? $object->$field : '', "class='form-control form-date autocomplete='off'")?></td>

          <?php elseif($value['control'] == 'datetime'):?>
          <td><?php echo html::input("{$field}[$key]", !empty($object->$field) ? $object->$field : '', "class='form-control form-datetime autocomplete='off'")?></td>

          <?php elseif($value['control'] == 'hidden'):?>
          <?php echo html::hidden("{$field}[$key]", $object->$field)?>

          <?php elseif($value['control'] == 'textarea'):?>
          <td><?php echo html::textarea("{$field}[$key]", $object->$field, "class='form-control' cols='50' rows='1'")?></td>

          <?php else:?>
          <td><?php echo html::input("{$field}[$key]", !empty($object->$field) ? $object->$field : '', "class='form-control autocomplete='off'")?></td>
          <?php endif;?>

          <?php endforeach;?>
        </tr>
        <?php endforeach;?>

      </tbody>
      <tfoot>
        <tr>
          <td colspan='<?php echo $colspan;?>' class='text-center form-actions'>
            <?php
            $submitText = $isEndPage ? $this->lang->save : $this->lang->file->saveAndNext;
            $backLink   = isset($backLink) ? $backLink : "javascript:history.back(-1)";
            if(!$insert and $dataInsert === '')
            {
                echo "<button type='button' data-toggle='modal' data-target='#importNoticeModal' class='btn btn-primary btn-wide'>{$submitText}</button>";
            }
            else
            {
                echo html::submitButton($submitText);
                if($dataInsert !== '') echo html::hidden('insert', $dataInsert);
            }
            echo html::hidden('isEndPage', $isEndPage ? 1 : 0);
            echo html::hidden('pagerID', $pagerID);
            echo html::a("$backLink", $lang->goback, '', "class='btn btn-back btn-wide'");
            echo ' &nbsp; ' . sprintf($lang->file->importPager, $allCount, $pagerID, $allPager);
            ?>
          </td>
        </tr>
      </tfoot>
    </table>
    <?php if(!$insert and $dataInsert === '') include $app->getModuleRoot() . 'common/view/noticeimport.html.php';?>
  </form>
</div>
<?php endif;?>
<script>
$.ajaxSetup({async: false});

$('#showData').on('mouseenter', '.picker', function(e){
    var myPicker = $(this);
    var field    = myPicker.prev().attr('data-field');
    var id       = myPicker.prev().attr('id');
    var index    = myPicker.prev().attr('data-key');
    var value    = myPicker.prev().val();

    if($('#' + id).attr('isInit')) return;
    $.get(createLink('port', 'ajaxGetOptions', 'model=<?php echo $model;?>&field=' + field + '&value=' + value + '&index=' + index), function(data)
    {
        $('#' + id).parent().html(data);
        $('#' + id).picker({chosenMode: true});
        $('#' + id).attr('isInit', true);
    });
})

$(function()
{
    $.fixedTableHead('#showData');
    $("#showData th").each(function()
    {
        if(requiredFields.indexOf(this.id) !== -1) $("#" + this.id).addClass('required');
    });
});
</script>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
