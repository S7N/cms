<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Controller_Admin extends Controller {

	public $title;
	public $content;

	/**
	 * @var  string  page template
	 */
	public $template = 'template';

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
		// load admin theme
		Kohana::modules(array_merge(Kohana::modules(), array (
			'themes' => THEMESPATH.'admin'
		)));

		if ($this->auto_render === TRUE)
		{
			// Load the template
			// Load the template
			$this->template = View::factory($this->template)
				->bind('title', $this->title)
				->bind('content', $this->content);
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
	}

}
