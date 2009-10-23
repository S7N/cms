<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Controller_Template extends Kohana_Controller_Template {

	public function before() {

		// load admin theme
		// TODO load theme from database
		Kohana::modules(array_merge(Kohana::modules(), array (
			'themes' => THEMESPATH.'default'
		)));

		parent::before();
	}

}