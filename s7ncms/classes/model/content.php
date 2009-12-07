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
			'id' => new Sprig_Field_Auto,
			'title' => new Sprig_Field_Char(array(
				'label' => __('Title')
			)),
			'data' => new Sprig_Field_Text(array(
				'label' => __('Content')
			)),
			'menu_title' => new Sprig_Field_Char,
			'language' => new Sprig_Field_Char, // TODO: Sprig_Field_Language
			'slug' => new Sprig_Field_Char,
			'type' => new Sprig_Field_Char, // TODO: Sprig_Field_Type
			'page' => new Sprig_Field_BelongsTo(array(
				'model' => 'Page'
			)),
			'created_on' => new Sprig_Field_Timestamp(array(
				'auto_now_create' => TRUE,
				'empty' => TRUE
			)),
			'updated_on' => new Sprig_Field_Timestamp(array(
				'auto_now_update' => TRUE,
				'empty' => TRUE
			)),
			'created_by' => new Sprig_Field_Integer,
			'updated_by' => new Sprig_Field_Integer,
			'hide_menu' => new Sprig_Field_Boolean,
			'keywords' => new Sprig_Field_Char(array(
				'label' => __('Keywords'),
				'empty' => TRUE
			)),
		);
	}

}