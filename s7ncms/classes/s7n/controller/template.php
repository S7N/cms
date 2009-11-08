<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Controller_Template extends Controller {

	/**
	 * @var  string  page template
	 */
	public $template = 'template';

	/**
	 * @var  string  page content
	 */
	public $title = 'S7Ncms';

	/**
	 * @var  string  page content
	 */
	public $content;

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;

	/**
	 * Loads the template View object.
	 *
	 * @return  void
	 */
	public function before()
	{
		// TODO load theme from database
		Theme::$name = 'default';
		Kohana::modules(array_merge(Kohana::modules(), array (
			'themes' => THEMESPATH.Theme::$name
		)));

		if (Request::$is_ajax OR $this->request !== Request::instance())
		{
			$this->auto_render = FALSE;
		}

		if ($this->auto_render === TRUE)
		{
			// Load the template
			$this->template = View::factory($this->template)
				->bind('page_content', $this->content);

			View::bind_global('page_title', $this->title);
		}
	}

	/**
	 * Assigns the template as the request response.
	 *
	 * @param   string   request method
	 * @return  void
	 */
	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			// Assign the template as the request response and render it
			$this->request->response = $this->template;
		}
		else
		{
			$this->request->response = $this->content;
		}
	}

}
