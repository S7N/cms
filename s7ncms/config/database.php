<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

if (file_exists(DOCROOT.'config/database.php'))
{
	return require_once(DOCROOT.'config/database.php');
}
else
{
	// TODO kann man hier schon url::base() benutzen?
	header('Location: install.php');
	exit;
}