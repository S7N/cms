<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Theme {

	public static $name = 'default';

	public static function uri($path = '')
	{
		return 'themes/' . Theme::$name . '/' . $path;
	}

}