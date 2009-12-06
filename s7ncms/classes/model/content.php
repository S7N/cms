<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Content extends Sprig {

	protected $_table = 'contents';

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto(array(
				'rules'  => array(
					'regex' => array('/.+/')
				),
			)),
			'title' => new Sprig_Field_Char(array(
				'label' => __('Title')
			)),
			'data' => new Sprig_Field_Text(array(
				'label' => __('Content')
			)),
		);
	}

}