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

		$this->title = 'Menu';
	}

	public function action_index()
	{
		$this->title .= ' - List All';
		$this->content = View::factory('menu/index')->bind('menuitems', $menuitems);

		$menuitems = Sprig::factory('menu')->load(NULL, FALSE);
	}

	public function action_create()
	{
		$this->title .= ' - Create';
		$this->content = View::factory('menu/create')
			->bind('routes', $routes)
			->bind('menuitem', $menuitem);

		$routes = Sprig::factory('route')->load(NULL, FALSE);
		$menuitem = Sprig::factory('menu');

		if ($_POST)
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

				Request::instance()->redirect('admin/menu/update/'. $menuitem->id);
			}
		}
	}

	public function action_update($id)
	{
		$this->title .= ' - Update';
		$this->content = View::factory('menu/update')
			->bind('routes', $routes)
			->bind('menuitem', $menuitem)
			->bind('errors', $errors);

		$routes = Sprig::factory('route')->load(NULL, FALSE);
		$menuitem = Sprig::factory('menu', array('id' => $id))->load();

		if ($_POST)
		{
			if ($post = $menuitem->check($_POST))
			{
				$menuitem->values($post);
				$menuitem->update();

				Request::instance()->redirect('admin/menu/update/'. $id);
			}
		}
	}

	public function action_delete($id)
	{
		Sprig::factory('menu', array('id' => $id))->load()->delete();

		Request::instance()->redirect('admin/menu');
	}

}