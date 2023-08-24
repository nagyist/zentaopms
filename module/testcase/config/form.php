<?php
declare(strict_types=1);
global $lang, $app;

$config->testcase->form = new stdclass();

$config->testcase->form->create = array();
$config->testcase->form->create['product']        = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->create['branch']         = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->create['module']         = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->create['type']           = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->create['stage']          = array('required' => false, 'type' => 'array',  'default' => array(''), 'filter' => 'join');
$config->testcase->form->create['story']          = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->create['scene']          = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->create['title']          = array('required' => true,  'type' => 'string', 'filter' => 'trim');
$config->testcase->form->create['color']          = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->create['pri']            = array('required' => false, 'type' => 'int',    'default' => 3);
$config->testcase->form->create['precondition']   = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->create['steps']          = array('required' => false, 'type' => 'array',  'default' => array(''));
$config->testcase->form->create['expects']        = array('required' => false, 'type' => 'array',  'default' => array(''));
$config->testcase->form->create['stepType']       = array('required' => false, 'type' => 'array',  'default' => array(''));
$config->testcase->form->create['keywords']       = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->create['status']         = array('required' => false, 'type' => 'string', 'default' => 'wait');
$config->testcase->form->create['version']        = array('required' => false, 'type' => 'int',    'default' => 1);
$config->testcase->form->create['openedBy']       = array('required' => false, 'type' => 'string', 'default' => isset($app->user->account) ? $app->user->account : '');
$config->testcase->form->create['openedDate']     = array('required' => false, 'type' => 'date',   'default' => helper::now());
$config->testcase->form->create['auto']           = array('required' => false, 'type' => 'string', 'default' => 'no');
$config->testcase->form->create['script']         = array('required' => false, 'type' => 'string', 'default' => '');

$config->testcase->form->batchCreate = common::formConfig('testcase', 'batchCreate');
$config->testcase->form->batchCreate['branch']       = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->batchCreate['module']       = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->batchCreate['scene']        = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->batchCreate['story']        = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->batchCreate['title']        = array('required' => true,  'type' => 'string', 'default' => '', 'base' => true);
$config->testcase->form->batchCreate['type']         = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->batchCreate['pri']          = array('required' => false, 'type' => 'int',    'default' => 0);
$config->testcase->form->batchCreate['precondition'] = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->batchCreate['keywords']     = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->batchCreate['stage']        = array('required' => false, 'type' => 'string', 'default' => '');
$config->testcase->form->batchCreate['review']       = array('required' => false, 'type' => 'int',    'default' => 0);
