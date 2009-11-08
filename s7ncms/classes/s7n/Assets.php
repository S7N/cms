<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Assets {

	protected static $scripts = array();
	protected static $stylesheets = array();

	public static function add_script($name, $weight = 0)
	{
		self::$scripts[$weight][] = $name;
	}

	public static function add_stylesheet($name, $weight = 0)
	{
		self::$stylesheets[$weight][] = $name;
	}

	public static function show()
	{
		krsort(self::$scripts);
		krsort(self::$stylesheets);

		$return = NULL;

		foreach (self::$stylesheets as $stylesheets)
		{
			foreach ($stylesheets as $style)
			{
				$return .= Html::style($style);
			}
		}

		foreach (self::$scripts as $scripts)
		{
			foreach ($scripts as $script)
			{
				$return .= Html::script($script);
			}
		}

		return $return;
	}

}