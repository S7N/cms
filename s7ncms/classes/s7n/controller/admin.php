<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

abstract class S7N_Controller_Admin extends Kohana_Controller_Template {

	public function before() {

		// load admin theme
		Kohana::modules(array_merge(Kohana::modules(), array (
			'themes' => THEMESPATH.'admin'
		)));

		parent::before();
	}

}
