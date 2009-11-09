<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Test extends S7N_Controller_Template {

	public function action_index()
	{
		$route = Sprig::factory('route')->permalink('muh');
		$this->content = Kohana::debug($route->page->load());
	}

}