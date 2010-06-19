<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Security extends Kohana_Security {

	public static $htmlpurifier = NULL;

	public static function purify($dirty_html)
	{
		if (self::$htmlpurifier === NULL)
		{
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Cache.SerializerPath', CONFIGPATH.'cache');
			$config->set('HTML.SafeObject', TRUE);
			$config->set('HTML.SafeEmbed', TRUE);

			self::$htmlpurifier = new HTMLPurifier($config);
		}

		return self::$htmlpurifier->purify($dirty_html);
	}

}