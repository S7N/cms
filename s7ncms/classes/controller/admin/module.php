<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Module extends S7N_Controller_Admin {

	public function action_create()
	{
		$response = array('errors' => FALSE, 'callback' => 'create');
		$route = Sprig::factory('route');

		if ($_POST)
		{
			try
			{
				if ($post = $route->check($_POST))
				{
					$root = Sprig::factory('route')->root(1);

					if ($root->loaded())
					{
						$route->values($post);
						$route->type = 'module';
						$route->insert_as_last_child($root);
					}
					else
					{
						$route->values($post);
						$route->type = 'module';
						$route->insert_as_new_root(1);
					}

					$response = json_encode(array_merge($response, array(
						'id' => 'item_'.$route->id,
						'parent' => 'item_'.$root->id,
						'type' => $route->type,
						'title' => $route->title()
					)));
				}
			}
			catch (Validate_Exception $e)
			{
				$response = json_encode(array('errors' => $e->array->errors('validate')));
			}
		}
		else
		{
			$response = new View('module/create', array('route' => $route));
		}

		echo $response;
	}

	public function action_update($id)
	{
		$response = array('errors' => FALSE, 'callback' => 'update');

		if ($_POST)
		{
			$route = Sprig::factory('route', array('id' => $id))->load();

			try
			{
				if ($post = $route->check($_POST))
				{
					$route->values($post);
					$route->update();
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

}