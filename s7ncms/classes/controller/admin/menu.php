<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Menu extends S7N_Controller_Admin {

	public function before()
	{
		parent::before();


		Assets::add_script(Theme::uri('scripts/jquery.tree.js'));
		Assets::add_script(Theme::uri('scripts/jquery.tree.contextmenu.js'));
		Assets::add_script(Theme::uri('scripts/menu.js'));
	}

	public function action_index()
	{
		if (Request::$is_ajax)
		{
			$this->content = Sprig::factory('menu')->json();
		}
		else
		{
			$this->content = View::factory('menu/index');
		}
	}

	public function action_create()
	{
		if (Request::$is_ajax)
		{
			if ($_POST)
			{
				$menuitem = Sprig::factory('menu');

				try
				{
					if ($post = $menuitem->check($_POST))
					{
						$root = Sprig::factory('menu')->root(1);

						if ($root->loaded())
						{
							$menuitem->values($post);
							$menuitem->insert_as_last_child($root);
						}
						else
						{
							$menuitem->values($post);
							$menuitem->insert_as_new_root(1);
						}

						$this->content = json_encode(array(
							'id' => 'item_'.$menuitem->id,
							'parent' => 'item_'.$root->id,
							'title' => $menuitem->title
						));
					}
				}
				catch (Exception $e)
				{
					// TODO
					$this->content = Kohana::debug($e->array->errors());
				}
			}
			else
			{
				$this->content = new View('menu/create', array('routes' => Sprig::factory('route')->load(NULL, FALSE)));
			}
		}
	}

	public function action_update($id = NULL)
	{
		// update MPTT
		if ($id === NULL)
		{
			$tree = json_decode($_POST['tree'], TRUE);

			$mptt = $this->calculate_mptt($tree);

			foreach($mptt as $node)
			{
				$item = Sprig::factory('menu', array('id' => $node['id']))->load();
				$item->lft = $node['lft'];
				$item->rgt = $node['rgt'];
				$item->lvl = $node['lvl'];
				$item->update();
			}

			$this->content = 'done';
		}
		// update menu item
		else
		{
			$menuitem = Sprig::factory('menu', array('id' => $id))->load();

			if ($_POST)
			{
				try
				{
					if ($post = $menuitem->check($_POST))
					{
						$menuitem->values($post);
						$menuitem->update();

						$this->content = 'done';
					}
				}
				catch (Exception $e)
				{
					$view = Kohana::debug($e->array->errors());
				}
			}
			else
			{
				$routes = Sprig::factory('route')->load(NULL, FALSE);
				$this->content = new View('menu/update', array('routes' => $routes, 'menuitem' => $menuitem));
			}
		}
	}

	public function action_delete($id)
	{
		Sprig::factory('menu', array('id' => $id))->load()->delete();

		$this->content = 'deleted';
	}

	private function calculate_mptt($tree, $level = 0)
	{
		static $mptt = array();
		static $counter = 0;

		foreach ($tree as $item => $children)
		{
			$id = empty($item) ? 0 : substr($item, 5);

			$left = ++$counter;

			if ( ! empty($children))
				$this->calculate_mptt($children, $level+1);

			$right = ++$counter;

			$mptt[] = array(
				'id' => $id,
				'lvl' => $level,
				'lft' => $left,
				'rgt' => $right
			);
		}

		return $mptt;
	}

}