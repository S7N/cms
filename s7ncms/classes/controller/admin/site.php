<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Site extends S7N_Controller_Admin {

	public function before()
	{
		parent::before();

		Assets::add_script(Theme::uri('scripts/jquery.tree.js'));
		Assets::add_script(Theme::uri('scripts/jquery.tree.contextmenu.js'));
		Assets::add_script(Theme::uri('scripts/site_structure.js'));
	}

	public function action_index()
	{
		if (Request::$is_ajax)
		{
			$this->content = Sprig::factory('route')->json();
		}
		else
		{
			$this->content = View::factory('site/index');
		}
	}

	public function action_create($name = NULL)
	{
		if (Request::$is_ajax)
		{
			if ($_POST)
			{
				$route = Sprig::factory('route');
				try
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

						if ($route->type == 'static')
						{
							// create new page
							$content = Sprig::factory('content');
							$content->title = 'New Page';
							$content->create();

							$page = Sprig::factory('page');
							$page->content = $content;
							$page->create();

							$route->page = $page;
							$route->update();

							$this->content = json_encode(array(
								'location' => url::site('admin/page/update/'.$page->id)
							));

						}
						else
						{
							$this->content = json_encode(array(
								'id' => 'item_'.$route->id,
								'parent' => 'item_'.$root->id,
								'type' => $route->type,
								'title' => $route->title()
							));
						}


					}
				}
				catch (Validate_Exception $e)
				{
					// TODO
					$this->content = Kohana::debug($e->array->errors());
				}
			}
			else
			{
				switch ($name)
				{
					case 'static':
						$this->content = new View('site/form/static');
						break;

					case 'module':
						$this->content = new View('site/form/module');
						break;

					case 'redirect':
						$this->content = new View('site/form/redirect');
						break;
					default:
						$this->content = new View('site/create_dialog');
						break;
				}
			}
		}
	}

	public function action_update($id)
	{
		$route = Sprig::factory('route', array('id' => $id))->load();
		$view = NULL;
		if ($_POST)
		{
			try
			{
				if ($post = $route->check($_POST))
				{
					$route->values($post);
					$route->update();

					$this->content = 'done';
				}
			}
			catch (Exception $e)
			{
				$this->content = Kohana::debug($e->array->errors());
			}
		}
		else
		{
			$this->content = new View('site/form/update_'.$route->type, array('route' => $route));
		}
	}

	public function action_update_tree()
	{
		$tree = json_decode($_POST['tree'], TRUE);

		$mptt = $this->calculate_mptt($tree);

		foreach($mptt as $node)
		{
			$route = Sprig::factory('route', array('id' => $node['id']))->load();
			$route->lft = $node['lft'];
			$route->rgt = $node['rgt'];
			$route->lvl = $node['lvl'];
			$route->update();
		}

		$this->content = 'done';
	}

	public function action_delete($id)
	{
		Sprig::factory('route', array('id' => $id))->load()->delete();
		$this->content = 'done';
	}

	public function action_get_page_update_location()
	{
		$this->content = url::site('admin/page/update/'.Sprig::factory('route', array('id' => $_GET['id']))->load()->page->id);
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