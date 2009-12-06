<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

Route::set('module/blog/pagination', 'module/blog/page/<page>', array('page' => '[0-9]+'))
	->defaults(array(
		'controller' => 'blog',
		'action' => 'paginate',
	));

Route::set('module/blog/tags', 'module/blog/tag/<tag>', array('tag' => '.+'))
	->defaults(array(
		'controller' => 'blog',
		'action' => 'tag',
		'tag' => NULL,
	));

Route::set('module/blog', 'module/blog(/<slug>)', array('slug' => '.+'))
	->defaults(array(
		'controller' => 'blog',
		'action' => 'list',
		'slug' => NULL,
	));