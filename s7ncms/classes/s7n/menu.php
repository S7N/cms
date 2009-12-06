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

		return new View($template, array('menu' => Sprig::factory('route')->load(NULL, FALSE)));
	}

	public static function submenu()
	{
		$menu = NULL;

		$route = Sprig::factory('route')->permalink(Request::instance()->uri());

		$parents = $route->parents();
		$count = count($parents);

		if ($route->lvl === 1)
		{
			$menu = $route->descendants();
		}
		elseif ($count > 0)
		{
			foreach ($parents as $parent)
			{
				if ($parent->lvl == 1)
				{
					$menu = $parent->descendants();
				}
			}
		}

		return new View('menu/all', array('menu' => $menu));
	}

	public static function first_level()
	{
		return new View('menu/first_level', array('menu' => Sprig::factory('route')->load(DB::select()->where('lvl', '=', 1), FALSE)));
	}

}