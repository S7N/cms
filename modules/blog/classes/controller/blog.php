<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Blog extends Controller {

	public function before()
	{
		parent::before();

		// TODO change action here
	}

	public function action_index($arg)
	{
		$this->request->response = Kohana::debug('Blog with args:', $arg);

		echo Kohana::debug($this->request);
	}
}