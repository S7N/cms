<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Test extends S7N_Controller_Template {

	public function action_index()
	{
		$post = array('title' => 'Mein Titel', 'uri' => 'mein-titel', 'data' => 'meine data');

		$root = Sprig::factory('route')->root(1);

		$content = Sprig::factory('content');
		$content->values($post);
		$content->create();

		$page = Sprig::factory('page');
		$page->content = $content;
		$page->create();

		$route = Sprig::factory('route');
		$route->values($post);
		$route->type = 'static';
		$route->page = $page;
		$route->insert_as_last_child($root);
	}

}