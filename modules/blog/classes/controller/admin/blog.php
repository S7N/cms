<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Blog extends S7N_Controller_Admin {

	public function action_index()
	{
		$this->title = 'Blog';
		$this->content = 'Hello';
	}
}