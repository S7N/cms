<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Menu {

	public static function main($with_root = FALSE)
	{
		$template = 'menu/without_root';

		if ($with_root)
		{
			$template = 'menu/all';
		}

		return new View($template, array('menu' => Sprig::factory('menu')->load_all()));
	}

	public static function submenu()
	{
		return new View('menu/all', array('menu' => Sprig::factory('menu')->submenu()));
	}

	public static function first_level()
	{
		return new View('menu/first_level', array('menu' => Sprig::factory('menu')->first_level()));
	}

}