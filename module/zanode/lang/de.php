<?php
$lang->zanode->common          = 'ZenAgent Node';
$lang->zanode->browse          = 'ZenAgent Node List';
$lang->zanode->create          = 'Create ZenAgent Node';
$lang->zanode->editAction      = 'Edit ZenAgent Node';
$lang->zanode->view            = 'View ZenAgent Node';
$lang->zanode->initTitle       = 'Init ZenAgent Node';
$lang->zanode->suspend         = 'Suspend ZenAgent Node';
$lang->zanode->destroy         = 'Destroy ZenAgent Node';
$lang->zanode->boot            = 'Start ZenAgent Node';
$lang->zanode->reboot          = 'Restart ZenAgent Node';
$lang->zanode->shutdown        = 'Shutdown ZenAgent Node';
$lang->zanode->resume          = 'Resume ZenAgent Node';
$lang->zanode->getVNC          = 'Remote management';
$lang->zanode->all             = 'All';
$lang->zanode->byQuery         = 'Search';
$lang->zanode->osName          = 'System';
$lang->zanode->image           = 'VM Image';
$lang->zanode->imageName       = 'Image Name';
$lang->zanode->name            = 'Name';
$lang->zanode->start           = 'Start After Created';
$lang->zanode->hostName        = 'Host Name';
$lang->zanode->host            = $lang->zanode->hostName;
$lang->zanode->extranet        = 'IP/Domain';
$lang->zanode->osArch          = 'Arch';
$lang->zanode->cpuCores        = 'CPU Cores';
$lang->zanode->memory          = 'Memory Size';
$lang->zanode->desc            = 'Description';
$lang->zanode->diskSize        = 'Disk Size';
$lang->zanode->status          = 'Status';
$lang->zanode->mac             = 'MAC';
$lang->zanode->vnc             = 'VNC Port';
$lang->zanode->destroyAt       = 'Destroy Time';
$lang->zanode->creater         = 'Creator';
$lang->zanode->createdDate     = 'Create Date';
$lang->zanode->confirmDelete   = "Are you sure about destroying the ZenAgent Node？";
$lang->zanode->confirmBoot     = "Are you sure to start the ZenAgent Node？";
$lang->zanode->confirmReboot   = "Are you sure to restart the ZenAgent Node？";
$lang->zanode->confirmShutdown = "Are you sure to shutdown the ZenAgent Node？";
$lang->zanode->confirmSuspend  = "Are you sure to suspend the ZenAgent Node？";
$lang->zanode->confirmResume   = "Are you sure to resume the ZenAgent Node？";
$lang->zanode->actionSuccess   = 'Success';
$lang->zanode->deleted         = "Deleted";
$lang->zanode->scriptPath      = "Script path";
$lang->zanode->shell           = "Shell";
$lang->zanode->automation      = "Automation";
$lang->zanode->install         = "Install";
$lang->zanode->copy            = 'Click to copy';
$lang->zanode->copied          = 'Copy successful';

$lang->automation = new stdClass();
$lang->automation->scriptPath = $lang->zanode->scriptPath;
$lang->automation->node       = $lang->zanode->common;

$lang->zanode->notFoundAgent  = 'No Agent service is found';
$lang->zanode->createVmFail   = 'Failed to create a ZenAgent Node';
$lang->zanode->noVncPort      = 'Failed to get vnc port';
$lang->zanode->nameValid      = "Name must be letters, numbers,'-'，'_'，'.', And cannot begin with a symbol";
$lang->zanode->empty          = 'ZenAgent Node not found.';
$lang->zanode->runCaseConfirm = 'The system detects the presence of an automation script. Whether to execute?';

$lang->zanode->createImage        = 'Create Image';
$lang->zanode->createImaging      = 'Creating';
$lang->zanode->createImageNotice  = 'The system will be created based on the current node，This process requires the ZenAgent Node to be shut down. Do you want to continue?';
$lang->zanode->createImageSuccess = 'Successed, You can use this image to create node.';
$lang->zanode->createImageFail    = 'Failed to create';
$lang->zanode->createImageButton  = 'Create image';

$lang->zanode->imageNameEmpty = 'Name can not be empty.';

$lang->zanode->runTimeout = 'Network connection timeout, please check the host and execution node status.';

$lang->zanode->apiError['-10100'] = 'ZenAgent Node not found.';

$lang->zanode->publicList[0] = 'Private';
$lang->zanode->publicList[1] = 'Public';

$lang->zanode->statusList['created']      = 'Created';
$lang->zanode->statusList['launch']       = 'Launch';
$lang->zanode->statusList['ready']        = 'Ready';
$lang->zanode->statusList['running']      = 'Running';
$lang->zanode->statusList['suspend']      = 'Suspend';
$lang->zanode->statusList['offline']      = 'Offline';
$lang->zanode->statusList['destroy']      = 'Destroyed';
$lang->zanode->statusList['shutoff']      = 'Shutoff';
$lang->zanode->statusList['shutdown']     = 'shutdown';
$lang->zanode->statusList['destroy_fail'] = 'Destroy Fail';
$lang->zanode->statusList['wait']         = 'Waiting For Init';
$lang->zanode->statusList['online']       = 'Online';

$lang->zanode->initNotice = "Succeeded. Please initialize the execution node or return to the list.";
$lang->zanode->initButton = "Initialize";

$lang->zanode->init = new stdclass;
$lang->zanode->init->statusTitle   = "Service Status";
$lang->zanode->init->checkStatus   = "Check Service Status";
$lang->zanode->init->not_install   = "Not installed";
$lang->zanode->init->not_available = "Installed, Not Started";
$lang->zanode->init->ready         = "Ready";
$lang->zanode->init->next          = "Next";
$lang->zanode->init->button        = "Go To Settings";

$lang->zanode->init->initSuccessNoticeTitle = "The initialization is successful, you can perform automated test settings or go to node browse.";
$lang->zanode->init->initFailNoticeTitle = "Initialization failed, check the init script execution log and try the following two solutions:";
$lang->zanode->init->initFailNoticeDesc  = "1. Re-execute the script <br/>2. Review the initialization FAQ";
$lang->zanode->init->serviceStatus = [
    "ZenAgent" => 'not_install',
    "ZTF"      => 'not_install',
];
$lang->zanode->init->title          = "Initialize Node";
$lang->zanode->init->descTitle      = "Follow these steps to complete the initialization on the node:";
$lang->zanode->init->initDesc       = "Execute the init script on the node: %s %s   <br>- Click check service status button.";
