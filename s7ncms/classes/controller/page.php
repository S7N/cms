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
		$page = $route->page; // TODO Sprig should always return a Model_Page object here!

		if ($page instanceof Model_Page AND $page->loaded())
		{
			switch ($page->type)
			{
				case 'module':
					$this->content =  Request::factory($page->content->data .'/'. implode('/', $route->arguments))->execute()->response;
					break;

				case 'static':
					if (count($route->arguments) > 0)
					{
						exit('too many arguments'); // TODO 404
					}
					else
					{
						$this->title = $page->content->title;
						$this->content = View::factory('page/content', array('data' => $page->content->data));
					}
					break;

				default:
					exit('invalid page type:' . $page->type);
			}
		}
		else
		{
			exit('page not found: ' .$permalink);
		}
	}

}
