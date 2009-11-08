<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Page extends S7N_Controller_Admin {

	public function action_update($id)
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
	}

}