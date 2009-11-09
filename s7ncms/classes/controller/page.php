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
					$this->show_page($route->page, $route->arguments);
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

	private function show_page($page, array $arguments)
	{
		if (count($arguments) > 0)
		{
			throw new S7N_Exception_404;
		}

		$this->content = View::factory('page/content')
			->bind('title', $title)
			->bind('data', $content);

		$title = $page->content->title;
		$content = $page->content->data;

		$this->title .= ' - '.$title;
	}

}
