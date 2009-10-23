<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Createnodes extends Controller {

	public function action_index()
	{
		try
		{
			$startseite = Sprig::factory('content');
			$startseite->title = 'Startseite';
			$startseite->data = 'Ich bin die Startseite';
			$startseite->create();

			$startseite_p = Sprig::factory('page');
			$startseite_p->type = 'static';
			$startseite_p->content = $startseite;
			$startseite_p->create();

			$startseite_route = Sprig::factory('route')->root(1);
			$startseite_route->page = $startseite_p;
			$startseite_route->uri = '';
			$startseite_route->insert_as_new_root(1);
			/////
			$produkte = Sprig::factory('content');
			$produkte->title = 'Produkte';
			$produkte->data = 'Hier gibts meine Produkte';
			$produkte->create();

			$produkte_p = Sprig::factory('page');
			$produkte_p->type = 'static';
			$produkte_p->content = $produkte;
			$produkte_p->create();

			$produkte_route = Sprig::factory('route');
			$produkte_route->page = $produkte_p;
			$produkte_route->uri = 'produkte';
			$produkte_route->insert_as_last_child($startseite_route);
			/////
			$s7ncms = Sprig::factory('content');
			$s7ncms->title = 'S7Ncms';
			$s7ncms->data = 'Das ist S7Ncms';
			$s7ncms->create();

			$s7ncms_p = Sprig::factory('page');
			$s7ncms_p->type = 'static';
			$s7ncms_p->content = $s7ncms;
			$s7ncms_p->create();

			$s7ncms_route = Sprig::factory('route');
			$s7ncms_route->page = $s7ncms_p;
			$s7ncms_route->uri = 's7ncms';
			$s7ncms_route->insert_as_last_child($produkte_route);
			//////
			$s7ncmsdoku = Sprig::factory('content');
			$s7ncmsdoku->title = 'S7Ndoku';
			$s7ncmsdoku->data = 'Das ist die Doku';
			$s7ncmsdoku->create();

			$s7ncmsdoku_p = Sprig::factory('page');
			$s7ncmsdoku_p->type = 'static';
			$s7ncmsdoku_p->content = $s7ncmsdoku;
			$s7ncmsdoku_p->create();

			$s7ncmsdoku_route = Sprig::factory('route');
			$s7ncmsdoku_route->page = $s7ncmsdoku_p;
			$s7ncmsdoku_route->uri = 's7ndoku';
			$s7ncmsdoku_route->insert_as_last_child($produkte_route);
			/////
			$something = Sprig::factory('content');
			$something->title = 'Something';
			$something->data = 'Das ist Irgendetwas';
			$something->create();

			$something_p = Sprig::factory('page');
			$something_p->type = 'static';
			$something_p->content = $something;
			$something_p->create();

			$something_route = Sprig::factory('route');
			$something_route->page = $something_p;
			$something_route->uri = 'something';
			$something_route->insert_as_last_child($produkte_route);
			/////
			$blog = Sprig::factory('content');
			$blog->title = 'Blog';
			$blog->data = 'test'; // module/controller name
			$blog->create();

			$blog_p = Sprig::factory('page');
			$blog_p->type = 'module';
			$blog_p->content = $blog;
			$blog_p->create();

			$blog_route = Sprig::factory('route');
			$blog_route->page = $blog_p;
			$blog_route->uri = 'blog';
			$blog_route->insert_as_last_child($startseite_route);

		}
		catch (Validate_Exception $e)
		{
			echo Kohana::debug($e->array->errors());
		}

		echo 'done';
	}
}