<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Route extends Sprig_MPTT {

	protected $_table = 'routes';
	protected $_sorting = array('lft' => 'ASC');
	public $arguments = array();

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'lft' => new Sprig_Field_MPTT_Left,
			'rgt' => new Sprig_Field_MPTT_Right,
			'lvl' => new Sprig_Field_MPTT_Level,
			'scope' => new Sprig_Field_MPTT_Scope,
			'page' => new Sprig_Field_HasOne(array(
				'model' => 'Page'
			)),
			'uri' => new Sprig_Field_Char(array(
				'empty'  => TRUE
			))
		);
	}

	public function permalink($permalink)
	{
		$current_route = Sprig::factory('route');

		if (empty($permalink))
		{
			$current_route = $current_route->root(1);
		}
		else
		{
			$segments = explode('/', $permalink);
			$children = Sprig::factory('route')->root(1)->children();

			for ($i = 0; $i < count($segments); $i++)
			{
				foreach ($children as $child)
				{
					if ($child->uri === $segments[$i])
					{
						$current_route = $child;

						if ($child->has_children())
						{
							$children = $child->children();
							continue 2;
						}
						else
						{
							break 2;
						}
					}
				}
			}

			// if we've found a route, how many arguments are left?
			if (count($segments) > $current_route->lvl)
			{
				$current_route->arguments = array_slice($segments, -(count($segments)-$current_route->lvl));
			}
		}

		return $current_route;
	}

}