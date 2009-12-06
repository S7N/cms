<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Menu extends Sprig_MPTT {

	protected $_table = 'routes';
	protected $_sorting = array('lft' => 'ASC');

	public function submenu(Route_Model $route = NULL)
	{
		$return = NULL;

		if ($route === NULL)
		{
			$route = Sprig::factory('route')->permalink(Request::instance()->uri());
		}

		$parents = $route->parents();
		$count = count($parents);

		if ($route->lvl === 1)
		{
			$return = $route->descendants();
		}
		elseif ($count > 0)
		{
			foreach ($parents as $parent)
			{
				if ($parent->lvl == 1)
				{
					$return = $parent->descendants();
				}
			}
		}

		return $return;
	}

	public function load_all()
	{
		return $this->load(NULL, FALSE);
	}

	public function first_level()
	{
		return $this->load(DB::select()->where('lvl', '=', 1), FALSE);
	}

	public function uri()
	{
		return Sprig::factory('route', array('id' => $this->id))->load()->uri();
	}

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto(array(
				'rules'  => array(
					'regex' => array('/.+/')
				),
			)),
			'lft' => new Sprig_Field_MPTT_Left(array(
				'rules'  => array(
					'regex' => array('/.+/')
				),
			)),
			'rgt' => new Sprig_Field_MPTT_Right(array(
				'rules'  => array(
					'regex' => array('/.+/')
				),
			)),
			'lvl' => new Sprig_Field_MPTT_Level,
			'scope' => new Sprig_Field_MPTT_Scope,
			'page' => new Sprig_Field_BelongsTo(array(
				'model' => 'Page',
				'empty'  => TRUE
			)),
			'type' => new Sprig_Field_Char,
			'uri' => new Sprig_Field_Char,
			'title' => new Sprig_Field_Char(array(
				'empty'  => TRUE,
				'rules' => array(
					'regex' => array('/./')
				)
			)),
			'target' => new Sprig_Field_Text(array(
				'empty'  => TRUE
			))
		);
	}

}