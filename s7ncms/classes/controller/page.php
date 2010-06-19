<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Page extends S7N_Controller_Template {

	public function action_index($permalink)
	{
		$page = Sprig::factory('page')->permalink($permalink);

		if ($page->loaded())
		{
			//$page->content->load();

			if ( ! $page->content->loaded())
			{
				$page->content->load();
			}

			switch ($page->content->type)
			{
				case 'redirect':
					Request::instance()->redirect($page->content->data);
					break;

				case 'module':
					$this->content = Request::factory('module/'.$page->content->data .'/'. implode('/', $page->arguments))->execute()->response;
					break;

				case 'static':
					$this->show_page($page);
					break;

				default:
					// unknown content type
					throw new S7N_Exception_500;
			}
			/*}
			else
			{
				// where is our content? inconsistent database
				throw new S7N_Exception_500;
			}*/
		}
		else
		{
			// page not found
			throw new S7N_Exception_404;
		}
	}

	private function show_page($page)
	{
		// too many arguments
		if (count($page->arguments) > 0)
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
