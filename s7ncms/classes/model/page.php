<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Page extends Sprig_MPTT {

	protected $_table = 'pages';
	protected $_sorting = array('lft' => 'ASC');
	public $arguments = array();

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'lft' => new Sprig_Field_MPTT_Left,
			'rgt' => new Sprig_Field_MPTT_Right,
			'lvl' => new Sprig_Field_MPTT_Level,
			'language' => new Sprig_Field_MPTT_Scope,
			'content' => new Sprig_Field_HasOne(array(
				'model' => 'Content'
			)),
		);
	}

	public function permalink($permalink)
	{
		$current_page = Sprig::factory('page');

		if (empty($permalink))
		{
			$current_page = $current_page->root(1);
		}
		else
		{
			$segments = explode('/', $permalink);
			$children = Sprig::factory('page')->root(1)->children();

			for ($i = 0; $i < count($segments); $i++)
			{
				foreach ($children as $child)
				{
					$child->content->load();

					if ($child->content->slug === $segments[$i])
					{
						$current_page = $child;

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

			// if we've found a page, how many arguments are left?
			if (count($segments) > $current_page->lvl)
			{
				$current_page->arguments = array_slice($segments, -(count($segments)-$current_page->lvl));
			}
		}

		return $current_page;
	}

	public function uri()
	{
		$uri = array();
		foreach ($this->parents() as $parent)
		{
			if ($parent->lvl > 0)
			{
				$uri[] = urlencode($parent->content->slug);
			}
		}

		if ($this->lvl > 0)
		{
			$uri[] = urlencode($this->content->slug);
		}

		return empty($uri) ? '/' : implode('/', $uri);

	}

	public function title()
	{
		$title = 'Untitled ID: '.$this->id;

		if ($this->content->type === 'static')
		{
			$title = $this->content->menu_title;
		}
		elseif ($this->content->type === 'redirect')
		{
			$title = $this->content->menu_title;
		}
		elseif ($this->content->type === 'module')
		{
			$title = $this->content->menu_title;
		}

		return $title;
	}

	public function json()
	{
		$pages = $this->load(NULL, FALSE);
		$stack = array(array());
		$last_node_level = 0;
		$last_node = NULL;

		foreach ($pages as $page)
		{
			$has_children = ($page->lft+1 < $page->rgt);

			if ($last_node_level == $page->lvl)
			{
				array_push($stack[count($stack)-1], array(
					'data' => $page->title(),
					'children' => array(),
					'attributes' => array('id' => 'item_'.$page->id, 'rel' => $page->content->type),
					'state' => $has_children ? 'open' : ''
				));

                $last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
                $last_node_level = $page->lvl;
			}
			elseif ($last_node_level < $page->lvl)
			{
				$stack[] = & $last_node;

				array_push($stack[count($stack)-1], array(
					'data' => $page->title(),
					'children' => array(),
					'attributes' => array('id' => 'item_'.$page->id, 'rel' => $page->content->type),
					'state' => $has_children ? 'open' : ''
				));

				$last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
				$last_node_level = $page->lvl;
			}
			elseif ($last_node_level > $page->lvl)
			{
				for ($i=0; $i < ($last_node_level-$page->lvl); $i++)
				{
					array_pop($stack);
				}

				array_push($stack[count($stack)-1], array(
					'data' => $page->title(),
					'children' => array(),
					'attributes' => array('id' => 'item_'.$page->id, 'rel' => $page->content->type),
					'state' => $has_children ? 'open' : ''
				));

				$last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
				$last_node_level = $page->lvl;
			}
		}

		return json_encode($stack[0]);
	}

	public function delete(Database_Query_Builder_Delete $query = NULL)
	{
		foreach ($this->descendants(TRUE) as $page)
		{
			$page->content->delete();
		}

		parent::delete($query);
	}

}