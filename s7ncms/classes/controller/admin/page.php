<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Page extends S7N_Controller_Admin {

	public function before()
	{
		parent::before();

		$this->title = 'Page';
	}

	public function action_index()
	{
		$this->title .= ' - List All';
		$this->content = View::factory('page/index')->bind('pages', $pages);

		$pages = Sprig::factory('page')->load(NULL, FALSE);
	}

	public function action_create()
	{
		$this->title .= ' - Create';
		$this->content = View::factory('page/create')->bind('page', $content);

		$content = Sprig::factory('content');

		if ($_POST)
		{
			if ($post = $content->check($_POST))
			{
				$content->values($post);
				$content->create();

				$page = Sprig::factory('page');
				$page->content = $content;
				$page->type = 'static';
				$page->create();

				Request::instance()->redirect('admin/page/update/'. $page->id);
			}
		}
	}

	public function action_update($id)
	{
		$this->title .= ' - Update';
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

				Request::instance()->redirect('admin/page/update/'. $id);
			}
		}
	}

	public function action_delete($id)
	{
		Sprig::factory('page', array('id' => $id))->load()->delete();

		Request::instance()->redirect('admin/page');
	}

}