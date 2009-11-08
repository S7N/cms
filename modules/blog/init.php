<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

Route::set('module_blog', 'module_blog(/<args>)', array('args' => '.+'))
	->defaults(array(
		'controller' => 'blog',
		'action' => 'index',
		'args' => NULL
	));