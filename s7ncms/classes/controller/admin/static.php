<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Static extends S7N_Controller_Admin {

	public function action_create()
	{
		$response = array('errors' => FALSE, 'callback' => 'create');
		$page = Sprig::factory('page');
		$content = Sprig::factory('content');

		if ($_POST)
		{
			try
			{
				$content->type = 'static';
				$content->values($_POST)->create();

				$response = json_encode(array_merge($response, array(
					'id' => 'item_'.$content->page->id,
					'parent' => 'item_'.$content->page->root(1)->id,
					'type' => $content->type,
					'title' => $content->page->title()
				)));
			}
			catch (Validate_Exception $e)
			{
				$response = json_encode(array('errors' => $e->array->errors('validate')));
			}
			catch (Kohana_Exception $e)
			{
				$response = json_encode(array($e->getMessage()));
			}
		}
		else
		{
			$response = new View('static/create', array('page' => $page));
		}

		$this->content = $response;
	}

	public function action_update($id)
	{
		$response = array('errors' => FALSE, 'callback' => 'update');
		$page = Sprig::factory('page', array('id' => $id))->load();
		$content = $page->content->load();

		if ($_POST)
		{
			try
			{
				$content->values($_POST)->update();

				$response = json_encode(array_merge($response, array(
					'id' => 'item_'.$content->page->id,
					'title' => $content->page->title()
				)));
			}
			catch (Validate_Exception $e)
			{
				$response = json_encode(array('errors' => $e->array->errors('validate')));
			}
			catch (Kohana_Exception $e)
			{
				$response = json_encode(array($e->getMessage()));
			}
		}
		else
		{
			$response = new View('static/update', array('page' => $page));
		}

		$this->content = $response;
	}

}