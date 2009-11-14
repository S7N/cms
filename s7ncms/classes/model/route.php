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
			'page' => new Sprig_Field_BelongsTo(array(
				'model' => 'Page',
				'empty'  => TRUE
			)),
			'type' => new Sprig_Field_Char,
			'uri' => new Sprig_Field_Char,
			'title' => new Sprig_Field_Char(array(
				'empty'  => TRUE
			)),
			'target' => new Sprig_Field_Text(array(
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

	public function uri()
	{
		$uri = array();
		foreach ($this->parents() as $parent)
		{
			if ($parent->lvl > 0)
			{
				$uri[] = $parent->uri;
			}
		}

		if ($this->lvl > 0)
		{
			$uri[] = $this->uri;
		}

		return empty($uri) ? '/' : implode('/', $uri);

	}

	public function title()
	{
		$title = 'Untitled ID: '.$this->id;

		if ($this->type === 'static')
		{
			$title = __('Page: :page', array(':page' => $this->page->content->title));
		}
		elseif ($this->type === 'redirect')
		{
			$title = __('Redirect to: :target', array(':target' => $this->target));
		}
		elseif ($this->type === 'module')
		{
			$title = __('Module: :module', array(':module' => $this->target));
		}

		return $title;
	}

	public function json()
	{
		$routes = $this->load(NULL, FALSE);
		$stack = array(array());
		$last_node_level = 0;
		$last_node = NULL;

		foreach ($routes as $route)
		{
			$has_children = ($route->lft+1 < $route->rgt);

			if ($last_node_level == $route->lvl) {
				array_push($stack[count($stack)-1], array(
					'data' => $route->title(),
					'children' => array(),
					'attributes' => array('id' => 'item_'.$route->id, 'rel' => $route->type),
					'state' => $has_children ? 'open' : ''
				));

                $last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
                $last_node_level = $route->lvl;
			}
			elseif ($last_node_level < $route->lvl)
			{
				$stack[] = & $last_node;

				array_push($stack[count($stack)-1], array(
					'data' => $route->title(),
					'children' => array(),
					'attributes' => array('id' => 'item_'.$route->id, 'rel' => $route->type),
					'state' => $has_children ? 'open' : ''
				));

				$last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
				$last_node_level = $route->lvl;
			}
			elseif ($last_node_level > $route->lvl)
			{
				for ($i=0; $i < ($last_node_level-$route->lvl); $i++) {
					array_pop($stack);
				}

				array_push($stack[count($stack)-1], array(
					'data' => $route->title(),
					'children' => array(),
					'attributes' => array('id' => 'item_'.$route->id, 'rel' => $route->type),
					'state' => $has_children ? 'open' : ''
				));

				$last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
				$last_node_level = $route->lvl;
			}
		}

		return json_encode($stack[0]);
	}

	public function delete(Database_Query_Builder_Delete $query = NULL)
	{
		foreach ($this->descendants(TRUE) as $descendant)
		{
			$descendant->page->delete();
		}

		parent::delete($query);
	}

}