<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_Controller_Admin extends Controller {

	/**
	 * @var  string  page template
	 */
	public $template = 'template';

	/**
	 * @var  string  page content
	 */
	public $content;
	public $sidebar_title;
	public $sidebar_content;

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
		Theme::$name = 'admin';
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
			$this->template = View::factory($this->template);
		}

		View::bind_global('page_title', $this->title);
		View::bind_global('page_content', $this->content);

		View::bind_global('sidebar_title', $this->sidebar_title);
		View::bind_global('sidebar_content', $this->sidebar_content);

		// Add jQuery scripts
		Assets::add_script(Theme::uri('scripts/jquery.js'), -100);
		Assets::add_script(Theme::uri('scripts/jquery-ui.js'), -100);
		Assets::add_script(Theme::uri('scripts/jquery.form.js'), -100);

		// Add Stylsheets
		Assets::add_stylesheet(Theme::uri('stylesheets/base.css'), -100);
		Assets::add_stylesheet(Theme::uri('stylesheets/style.css'), -100);
		Assets::add_stylesheet(Theme::uri('stylesheets/admin.css'), -100);
		Assets::add_stylesheet(Theme::uri('stylesheets/jquery-ui.css'), -100);

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
