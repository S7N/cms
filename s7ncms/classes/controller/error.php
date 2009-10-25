<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Error extends S7N_Controller_Template {

	public function action_403()
	{
		$this->title = 'Forbidden';
	}

	public function action_404()
	{
		$this->title = 'Not Found';
	}

	public function action_500()
	{
		$this->title = 'Internal Server Error';
	}

}