<?php
$config->account->form = new stdclass();
$config->account->form->create = array();
$config->account->form->create['name']        = array('type' => 'string', 'required' => true,  'default' => '', 'filter' => 'trim');
$config->account->form->create['provider']    = array('type' => 'string', 'required' => true,  'default' => '');
$config->account->form->create['adminURI']    = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->create['account']     = array('type' => 'string', 'required' => true,  'default' => '', 'filter' => 'trim');
$config->account->form->create['password']    = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->create['email']       = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->create['mobile']      = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->create['type']        = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->create['status']      = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->create['extra']       = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->create['createdDate'] = array('type' => 'string', 'required' => false, 'default' => helper::now());

$config->account->form->edit = array();
$config->account->form->edit['name']       = array('type' => 'string', 'required' => true,  'default' => '', 'filter' => 'trim');
$config->account->form->edit['provider']   = array('type' => 'string', 'required' => true,  'default' => '');
$config->account->form->edit['adminURI']   = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->edit['account']    = array('type' => 'string', 'required' => true,  'default' => '', 'filter' => 'trim');
$config->account->form->edit['password']   = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->edit['email']      = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->edit['mobile']     = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->edit['type']       = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->edit['status']     = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->edit['extra']      = array('type' => 'string', 'required' => false, 'default' => '', 'filter' => 'trim');
$config->account->form->edit['editedDate'] = array('type' => 'string', 'required' => false, 'default' => helper::now());
