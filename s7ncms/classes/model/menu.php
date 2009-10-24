<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Menu extends Sprig_MPTT {

	protected $_table = 'menu';
	protected $_sorting = array('lft' => 'ASC');

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'lft' => new Sprig_Field_MPTT_Left,
			'rgt' => new Sprig_Field_MPTT_Right,
			'lvl' => new Sprig_Field_MPTT_Level,
			'scope' => new Sprig_Field_MPTT_Scope,
			'route' => new Sprig_Field_HasOne(array(
				'model' => 'Route'
			)),
			'title' => new Sprig_Field_Char
		);
	}

}