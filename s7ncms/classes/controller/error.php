<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Error extends S7N_Controller_Template {

	public function action_index($id)
	{
		$this->content = new View('error', array('error' => new View('error/'.$id)));
	}

}