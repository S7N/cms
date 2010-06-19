<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Content extends Sprig {

	protected $_table = 'contents';

	public function create()
	{
		$page = Sprig::factory('page');
		$root = $page->root(1);

		if ($root->loaded())
		{
			$page->insert_as_last_child($root);
		}
		else
		{
			$page->insert_as_new_root(1);
		}

		try
		{
			// don't do: $title = $this->title; ... it will load an existing entry from database!
			$title = Arr::get($this->_changed, 'title', NULL);

			$this->menu_title = $title;
			$this->language = 'de-de';
			$this->slug = URL::title($title);
			$this->created_by = 1;
			$this->updated_by = 1;
			$this->hide_menu = FALSE;
			$this->keywords = '';
			$this->page = $page;

			parent::create();
		}
		catch (Exception $e)
		{
			$page->delete();

			throw $e;
		}

		return $this;
	}

	public function values(array $values)
	{
		if ( ! isset($values['hide_menu']))
		{
			$values['hide_menu'] = 0;
		}

		return parent::values($values);
	}

	public function update()
	{
		parent::update();
	}

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'title' => new Sprig_Field_Char(array(
				'label' => __('Title'),
				'filters' => array('Security::purify' => NULL)
			)),
			'data' => new Sprig_Field_Text(array(
				'label' => __('Content'),
				'filters' => array('Security::purify' => NULL)
			)),
			'menu_title' => new Sprig_Field_Char(array(
				'label' => __('Menu Title'),
				'filters' => array('Security::purify' => NULL)
			)),
			'language' => new Sprig_Field_Char(array( // TODO: Sprig_Field_Language
				'default' => 'de-de'
			)),
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
			'hide_menu' => new Sprig_Field_Boolean(array(
				'label' => __('Hide from menu')
			)),
			'keywords' => new Sprig_Field_Char(array(
				'label' => __('Keywords'),
				'empty' => TRUE,
				'null' => TRUE
			)),
		);
	}

}