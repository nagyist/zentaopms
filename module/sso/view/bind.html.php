<?php
/**
 * The bind view file of sso module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     sso
 * @version     $Id$
 * @link        https://www.zentao.net
 */
?>
<?php include '../../common/view/header.lite.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<?php if(!empty($config->safe->mode)) $lang->user->placeholder->password1 = $lang->user->placeholder->passwordStrength[$config->safe->mode]?>
<?php js::set('holders', $lang->user->placeholder);?>
<div id='mainContent' class='main-content'>
  <div class='center-block mw-700px'>
    <div class='main-header'><h2><?php echo $lang->sso->bind?></h2></div>
    <form method='post' target='hiddenwin' id='bindForm'>
      <table class='table table-form'>
        <tr>
          <th class='w-100px'><?php echo $lang->sso->bindType?></th>
          <td><?php echo html::radio('bindType', $lang->sso->bindTypeList, 'bind')?></td>
          <td></td>
        </tr>
        <tr class='params bind'>
          <th><?php echo $lang->sso->bindUser?></th>
          <td><?php echo html::select('bindUser', $users, $data->account, "class='form-control chosen'")?></td>
        </tr>
        <tr class='params bind'>
          <th><?php echo $lang->user->password?></th>
          <td><?php echo html::password('bindPassword', '', "class='form-control'")?></td>
        </tr>
        <tr class='params add hide'>
          <th><?php echo $lang->user->account?></th>
          <td><?php echo html::input('account', $data->account, "class='form-control'")?></td>
        </tr>
        <tr class='params add hide'>
          <th><?php echo $lang->user->password?></th>
          <td>
            <span class='input-group'>
              <?php echo html::password('password1', '', "class='form-control' onmouseup='checkPassword(this.value)' onkeyup='checkPassword(this.value)'");?>
              <span class='input-group-addon' id='passwordStrength'></span>
            </span>
          </td>
        </tr>
        <tr class='params add hide'>
          <th><?php echo $lang->user->password2?></th>
          <td><?php echo html::password('password2', '', "class='form-control'")?></td>
        </tr>
        <tr class='params add hide'>
          <th><?php echo $lang->user->realname?></th>
          <td><?php echo html::input('realname', $data->realname, "class='form-control'")?></td>
        </tr>
        <tr class='params add hide'>
          <th><?php echo $lang->user->gender?></th>
          <td><?php echo html::radio('gender', $lang->user->genderList, $data->gender)?></td>
        </tr>
        <tr class='params add hide'>
          <th><?php echo $lang->user->email?></th>
          <td><?php echo html::input('email', $data->email, "class='form-control'")?></td>
        </tr>
        <tr class='params add hide'>
          <th></th>
          <td colspan='2'><span style='color:red'><?php echo $lang->sso->bindNotice?></span></td>
        </tr>
        <tr>
          <th></th>
          <td><?php echo html::submitButton()?></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<script>
$(function()
{
    $('#bindForm input[id^="bindType"]').change(function()
    {
        $('#bindForm table tr.params').addClass('hide');
        $('#bindForm table tr.' + $(this).val()).removeClass('hide');
    })
})

function checkPassword(password)
{
    $('#passwordStrength').html(password == '' ? '' : passwordStrengthList[computePasswordStrength(password)]);
    $('#passwordStrength').css('display', password == '' ? 'none' : 'table-cell');
}
</script>
<?php js::set('passwordStrengthList', $lang->user->passwordStrengthList)?>
<?php include '../../common/view/footer.lite.html.php';?>
