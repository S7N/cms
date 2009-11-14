<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Page extends S7N_Controller_Admin {

	public function action_create()
	{
		$response = array('errors' => FALSE, 'callback' => 'create');

		if ($_POST)
		{
			$route = Sprig::factory('route');

			try
			{
				if ($post_route = $route->check($_POST) AND
					$post_content = $route->page->content->check($_POST))
				{
					$root = Sprig::factory('route')->root(1);

					if ($root->loaded())
					{
						$route->values($post_route);
						$route->type = 'static';
						$route->insert_as_last_child($root);
					}
					else
					{
						$route->values($post_route);
						$route->type = 'static';
						$route->insert_as_new_root(1);
					}

					// create new page
					$content = Sprig::factory('content');
					$content->values($post_content);
					$content->create();

					$page = Sprig::factory('page');
					$page->content = $content;
					$page->create();

					$route->page = $page;
					$route->update();

					$response = json_encode(array_merge($response, array(
						'id' => 'item_'.$route->id,
						'parent' => 'item_'.$root->id,
						'type' => $route->type,
						'title' => $route->title()
					)));
				}
			}
			catch (Exception $e)
			{
				$response = json_encode(array('errors' => $e->array->errors('validate')));
			}
		}
		else
		{
			$response = new View('page/create', array('route' => Sprig::factory('route'), 'page' => Sprig::factory('page')));
		}

		$this->content = $response;
	}

	public function action_update($id)
	{
		$response = array('errors' => FALSE, 'callback' => 'update');

		if ($_POST)
		{
			$route = Sprig::factory('route', array('id' => $id))->load();

			try
			{
				$content = $route->page->content->load();

				if ($post_route = $route->check($_POST) AND $post = $content->check($_POST))
				{
					$route->values($post_route);
					$route->update();

					$content->values($post);
					$content->update();
				}

				$response = array_merge($response, array(
					'id' => 'item_'.$route->id,
					'title' => $route->title()
				));
			}
			catch (Validate_Exception $e)
			{
				$response = array('errors' => $e->array->errors('validate'));
			}
		}

		$this->content = json_encode($response);
	}

	/*public function action_update($id)
	{
		$this->content = View::factory('page/update')
			->bind('page', $page)
			->bind('errors', $errors);

		$page = Sprig::factory('page', array('id' => $id))->load();

		if ($_POST)
		{
			if ($post = $page->content->check($_POST))
			{
				$page->content->values($post);
				$page->content->update();

				Session::instance()->set('notice', __('Pages was updated successfully'));

				Request::instance()->redirect('admin/site');
			}
			else
			{
				Session::instance()->set('error', __('Error occured'));
			}
		}
	}*/

}