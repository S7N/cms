<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Exception_403 extends Kohana_Exception {

	public function __construct($message = '403 Forbidden', array $variables = NULL, $code = 0)
	{
		parent::__construct($message, $variables, $code);
	}

}
