<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Controller_Admin_Site extends S7N_Controller_Admin {

	public function before()
	{
		parent::before();

		Assets::add_script(Theme::uri('scripts/jquery.tree.js'));
		Assets::add_script(Theme::uri('scripts/jquery.tree.contextmenu.js'));
		Assets::add_script(Theme::uri('scripts/site_structure.js'));
	}

	public function action_index()
	{
		if (Request::$is_ajax)
		{
			$this->content = Sprig::factory('route')->json();
		}
		else
		{
			$this->sidebar_title = new View('site/sidebar_title');
			$this->sidebar_content = new View('site/index_sidebar');

			$this->content = new View('site/index_content');
		}
	}

	public function action_update($id)
	{
		$route = Sprig::factory('route', array('id' => $id))->load();

		switch ($route->type) {
			case 'static':
				$this->content = new View('page/update', array('route' => $route, 'page' => $route->page));
				break;

			case 'module':
				$this->content = new View('module/update', array('route' => $route));
				break;

			case 'redirect':
				$this->content = new View('redirect/update', array('route' => $route));
				break;
		}
	}

	public function action_update_tree()
	{
		$tree = json_decode($_POST['tree'], TRUE);

		$mptt = $this->calculate_mptt($tree);

		foreach($mptt as $node)
		{
			$route = Sprig::factory('route', array('id' => $node['id']))->load();
			$route->lft = $node['lft'];
			$route->rgt = $node['rgt'];
			$route->lvl = $node['lvl'];
			$route->update();
		}

		$this->content = 'done';
	}

	private function calculate_mptt($tree, $level = 0)
	{
		static $mptt = array();
		static $counter = 0;

		foreach ($tree as $item => $children)
		{
			$id = empty($item) ? 0 : substr($item, 5);

			$left = ++$counter;

			if ( ! empty($children))
				$this->calculate_mptt($children, $level+1);

			$right = ++$counter;

			$mptt[] = array(
				'id' => $id,
				'lvl' => $level,
				'lft' => $left,
				'rgt' => $right
			);
		}

		return $mptt;
	}

}