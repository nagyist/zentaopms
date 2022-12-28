<?php

/**
 * The view file of zanode module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Ke Zhao <zhaoke@cnezsoft.com>
 * @package     zanode
 * @version     $Id: view.html.php $
 * @link        http://www.zentao.net
 */
?>
<?php include $app->getModuleRoot() . 'common/view/header.html.php'; ?>
<?php include $app->getModuleRoot() . 'common/view/kindeditor.html.php'; ?>
<?php js::set('confirmDelete', $lang->zanode->confirmDelete) ?>
<?php js::set('confirmBoot', $lang->zanode->confirmBoot) ?>
<?php js::set('confirmReboot', $lang->zanode->confirmReboot) ?>
<?php js::set('confirmShutdown', $lang->zanode->confirmShutdown) ?>
<?php js::set('actionSuccess', $lang->zanode->actionSuccess) ?>
<?php js::set('vncLink', "http://$url/novnc?port=6080&path=websockify/?token=$token&password=pass") ?>
<?php $browseLink = $this->session->zanodeList ? $this->session->zanodeList : $this->createLink('zanode', 'browse', ""); ?>
<?php
$vars    = "id={$zanode->id}&orderBy=%s";
$account = strpos($zanode->osName, "windows") ? $config->zanode->defaultWinAccount : $config->zanode->defaultAccount;
?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php echo html::backButton('<i class="icon icon-back icon-sm"></i> ' . $lang->goback, "data-app='{$app->tab}'", 'btn btn-secondary'); ?>
    <div class='divider'></div>
    <div class='page-title'>
      <span class='label label-id'><?php echo $zanode->id; ?></span>
      <span class='text' title='<?php echo $zanode->name; ?>'><?php echo $zanode->name; ?></span>
      <?php if ($zanode->deleted) : ?>
        <span class='label label-danger'><?php echo $lang->zanode->deleted; ?></span>
      <?php endif; ?>
    </div>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class="col-8 main-col">
    <div class="cell">
      <div class="detail zanode-detail">
        <div class="detail-title"><?php echo $lang->zanode->view; ?></div>
        <div class="detail-content article-content">
          <div class="main-row zanode-mt-8">
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->osName; ?>:</div>
                <div class="col-8"><?php echo $zanode->osName; ?></div>
              </div>
            </div>
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->sshAddress; ?>:</div>
                <div class="col-8"><?php echo $account . '@' . $zanode->ip; ?></div>
              </div>
            </div>
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->cpuCores; ?>:</div>
                <div class="col-8"><?php echo $zanode->cpuCores; ?></div>
              </div>
            </div>
          </div>
          <div class="main-row zanode-mt-8">
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->status; ?>:</div>
                <div class="col-8"><?php echo zget($lang->zanode->statusList, $zanode->status); ?></div>
              </div>
            </div>
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->defaultUser; ?>:</div>
                <div class="col-8"><?php echo $account; ?></div>
              </div>
            </div>
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->memory; ?>:</div>
                <div class="col-8"><?php echo $zanode->memory; ?></div>
              </div>
            </div>
          </div>
          <div class="main-row main-row-last zanode-mt-8">
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->hostName; ?>:</div>
                <div class="col-8"><?php echo $zanode->hostName; ?></div>
              </div>
            </div>
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->defaultPwd; ?>:</div>
                <div class="col-8"><?php echo $config->zanode->defaultPwd; ?></div>
              </div>
            </div>
            <div class="col-4">
              <div class="main-row">
                <div class="col-3 text-right"><?php echo $lang->zanode->diskSize; ?>:</div>
                <div class="col-8"><?php echo $zanode->diskSize; ?></div>
              </div>
            </div>
            <div class="col-4"></div>
          </div>
        </div>
      </div>
      
      <?php
      $canBeChanged = common::canBeChanged('zanode', $zanode);
      if ($canBeChanged) $actionFormLink = $this->createLink('action', 'comment', "objectType=zanode&objectID=$zanode->id");
      ?>
    </div>
    <div class="cell">
      <div class="detail vnc-detail">
        <div class='vnc-mask'></div>
        <?php echo "<iframe width='100%' id='vncIframe' src='http://$url/novnc?port=6080&path=websockify/?token=$token&password=pass'></iframe>";?>
      </div>
    </div>
    <?php $this->printExtendFields($zanode, 'div', "position=left&inForm=0&inCell=1"); ?>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php echo html::backButton('<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', 'btn btn-secondary'); ?>
        <div class='divider'></div>
        <?php
        if (empty($zanode->deleted)) {
          if ($zanode->status == "running") {
            common::printLink('zanode', 'suspend', "id={$zanode->id}", "<i class='icon icon-pause'></i> " . $lang->zanode->suspend, '', "title='{$lang->zanode->suspend}' class='btn' target='hiddenwin' onclick='if(confirm(\"{$lang->zanode->confirmSuspend}\")==false) return false;'");
          } elseif ($zanode->status == "suspend") {
            common::printLink('zanode', 'resume', "id={$zanode->id}", "<i class='icon icon-restart'></i> " . $lang->zanode->resume, '', "title='{$lang->zanode->resume}' class='btn' target='hiddenwin' onclick='if(confirm(\"{$lang->zanode->confirmResume}\")==false) return false;'");
          }
          common::printLink('zanode', 'getVNC', "id={$zanode->id}", "<i class='icon icon-desktop'></i> " . $lang->zanode->getVNC, '_blank', "title='{$lang->zanode->getVNC}' class='btn " . (common::hasPriv('zanode', 'getVNC') && in_array($zanode->status, array('running', 'launch')) ? '' : 'disabled') . "'");
        }
        ?>
        <div class='divider'></div>
        <?php echo $this->zanode->buildOperateMenu($zanode, 'view'); ?>
      </div>
    </div>
  </div>
  <div class="col-4 side-col">
    <div class='cell'><?php include '../../common/view/action.html.php'; ?></div>
    <div class="cell">
      <div class="detail zanode-detail">
        <div class="detail-title">
          <?php echo $lang->zanode->init->statusTitle; ?>
          <span class="zanode-status-text"><i class="icon icon-restart"></i><?php echo $lang->zanode->init->checkStatus; ?></span>
        </div>
        <div class="detail-content">
          <div class="service-status">
            <span class='dot-symbol text-success'>●</span>
            <span>&nbsp;&nbsp;ZenAgent: &nbsp;
              <span class="zenagent-staus">已就绪</span>
            </span>
          </div>
          <div class="service-status">
            <span class='dot-symbol text-success'>●</span>
            <span>&nbsp;&nbsp;ZTF: &nbsp;
              <span class="zenagent-staus">已就绪</span>
            </span>
          </div>
          <div class="status-notice">
            <?php echo sprintf($lang->zanode->init->initSuccessNoticeTitle, "<a href=''>{$lang->zanode->manual}</a>", html::a(helper::createLink('testcase', 'automation', "", '', true), $lang->zanode->automation, '', "class='iframe' title='{$lang->zanode->automation}' data-width='50%'", '')); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail zanode-detail">
        <div class="detail-title"><?php echo $lang->zanode->desc; ?></div>
        <div class="detail-content article-content"><?php echo !empty($zanode->desc) ? htmlspecialchars_decode($zanode->desc) : $lang->noData; ?></div>
      </div>
    </div>

    <div id='mainActions' class='main-actions'>
      <?php common::printPreAndNext($browseLink); ?>
    </div>
    <?php include '../../common/view/footer.html.php'; ?>