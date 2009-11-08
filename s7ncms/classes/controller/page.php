<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Page extends S7N_Controller_Template {

	public function action_index($permalink)
	{
		$route = Sprig::factory('route')->permalink($permalink);

		if ($route->loaded())
		{
			switch ($route->type)
			{
				case 'redirect':
					Request::instance()->redirect($route->target);
					break;

				case 'module':
					$this->content = Request::factory('module_'.$route->target .'/'. implode('/', $route->arguments))->execute()->response;
					break;

				case 'static':
					if (count($route->arguments) > 0 OR ! $route->page instanceof Model_Page)
					{
						throw new S7N_Exception_404;
					}
					else
					{
						$this->content = View::factory('page/content', array('title' => $route->page->content->title, 'data' => $route->page->content->data));
					}
					break;

				default:
					throw new S7N_Exception_404;
			}
		}
		else
		{
			throw new S7N_Exception_404;
		}
	}

}
