<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Blog extends S7N_Controller_Template {

	public function action_list($slug)
	{
		$this->title .= ' - Weblog';
		$this->content = '<h2>Blog</h2>this is my blog: '.$slug;
	}

	public function action_tag($tag)
	{
		$this->title .= ' - Weblog - Tag: '.$tag;
		$this->content = '<h2>Blog</h2>this is my blog with tag '.$tag;
	}

	public function action_paginate($page)
	{
		$this->title .= ' - Weblog - Page: '.$page;
		$this->content = '<h2>Blog</h2>this is my blog with page number '.$page;
	}

}
