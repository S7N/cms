<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Route extends S7N_Controller_Admin {

	public function before()
	{
		parent::before();

		$this->title = 'Route';
	}

	public function action_index()
	{
		$this->title .= ' - List All';
		$this->content = View::factory('route/index')->bind('routes', $routes);

		$routes = Sprig::factory('route')->load(NULL, FALSE);
	}

	public function action_create()
	{
		$this->title .= ' - Create';
		$this->content = View::factory('route/create')
			->bind('route', $route)
			->bind('pages', $pages);

		$pages = Sprig::factory('page')->load(NULL, FALSE);
		$route = Sprig::factory('route');

		if ($_POST)
		{
			if ($post = $route->check($_POST))
			{
				$root = Sprig::factory('route')->root(1);

				if ($root->loaded())
				{
					$route->values($post);
					$route->insert_as_last_child($root);
				}
				else
				{
					$route->values($post);
					$route->insert_as_new_root(1);
				}

				Request::instance()->redirect('admin/route/update/'.$route->id);
			}
		}
	}

	public function action_update($id)
	{
		$this->title .= ' - Update';
		$this->content = View::factory('route/update')
			->bind('route', $route)
			->bind('pages', $pages);

		$pages = Sprig::factory('page')->load(NULL, FALSE);
		$route = Sprig::factory('route', array('id' => $id))->load();

		if ($_POST)
		{
			if ($post = $route->check($_POST))
			{
				$route->values($post);
				$route->update();

				Request::instance()->redirect('admin/route/update/'.$route->id);
			}
		}
	}

	public function action_delete($id)
	{
		Sprig::factory('route', array('id' => $id))->load()->delete();

		Request::instance()->redirect('admin/route');
	}

}
