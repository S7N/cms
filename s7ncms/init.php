<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

// admin route
Route::set('admin', 'admin(/<controller>(/<action>(/<id>)))', array('id' => '.+'))
	->defaults(array(
		'controller' => 'dashboard',
		'action' => 'index',
		'directory' => 'admin'
));

// error route
Route::set('error', 'error/<action>', array('action' => '403|404|500'))
	->defaults(array(
		'controller' => 'error'
));

// XXX internal route for creating nodes to test mptt :-)
Route::set('createnodes', 'createnodes')
	->defaults(array(
		'controller' => 'createnodes',
		'action' => 'index'
));

// load core modules
Kohana::modules(array_merge(Kohana::modules(), array (
	'database'   => COREPATH.'modules/database',
	'sprig'      => COREPATH.'modules/sprig',
	'sprig-mptt' => COREPATH.'modules/sprig-mptt',
)));

// enable database config
Kohana::$config->attach(new Kohana_Config_Database);

// set the default language
// TODO set from config/uri
I18n::$lang = 'de-de';

// load user modules
// TODO load theme here
$modules = array();
foreach (Sprig::factory('module')->load(NULL, FALSE) as $module)
{
	if ($module->enabled)
	{
		$modules[$module->name] = MODPATH.$module->name;
	}
}

if (count($modules) > 0)
{
	Kohana::modules(array_merge(Kohana::modules(), $modules));
}

// default route
Route::set('default', '(<permalink>)', array('permalink' => '.+'))
	->defaults(array(
		'controller' => 'page',
		'action' => 'index',
		'permalink' => '',
));