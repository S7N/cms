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
		$content = Sprig::factory('content');
		$page = Sprig::factory('page');

		if ($_POST)
		{
			try
			{
				if ($post_content = $content->check($_POST))
				{
					$root = Sprig::factory('page')->root(1);

					if ($root->loaded())
					{
						$page->insert_as_last_child($root);
					}
					else
					{
						$page->insert_as_new_root(1);
					}

					$content->values($post_content);
					$content->type = 'static';
					$content->menu_title = $post_content['title'];
					$content->language = 'de-de';
					$content->slug = URL::title($post_content['title']);
					$content->created_by = 1;
					$content->updated_by = 1;
					$content->hide_menu = FALSE;
					$content->keywords = '';
					$content->page = $page;
					$content->create();

					$response = json_encode(array_merge($response, array(
						'id' => 'item_'.$page->id,
						'parent' => 'item_'.$root->id,
						'type' => $page->content->type,
						'title' => $page->content->title
					)));
				}
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
			$response = new View('page/create', array('page' => $page));
		}

		$this->content = $response;
	}

	public function action_update($id)
	{
		$response = array('errors' => FALSE, 'callback' => 'update');
		$page = Sprig::factory('page', array('id' => $id))->load();

		if ($_POST)
		{
			try
			{
				$content = $page->content->load();

				if ($post = $content->check($_POST))
				{
					$content->values($post);
					$content->update();
				}

				$response = array_merge($response, array(
					'id' => 'item_'.$page->id,
					'title' => $page->title()
				));
			}
			catch (Validate_Exception $e)
			{
				$response = array('errors' => $e->array->errors('validate'));
			}
			catch (Kohana_Exception $e)
			{
				$response = json_encode(array($e->getMessage()));
			}
		}

		$this->content = json_encode($response);
	}

	public function action_delete($id)
	{
		$route = Sprig::factory('page', array('id' => $id))->load()->delete();

		$this->content = 'done';
	}

}