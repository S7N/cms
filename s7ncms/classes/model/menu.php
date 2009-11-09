<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Menu extends Sprig_MPTT {

	protected $_table = 'menu';
	protected $_sorting = array('lft' => 'ASC');

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'lft' => new Sprig_Field_MPTT_Left,
			'rgt' => new Sprig_Field_MPTT_Right,
			'lvl' => new Sprig_Field_MPTT_Level,
			'scope' => new Sprig_Field_MPTT_Scope,
			'route' => new Sprig_Field_BelongsTo(array(
				'model' => 'Route'
			)),
			'title' => new Sprig_Field_Char
		);
	}

	public function json()
	{
		$items = $this->load(NULL, FALSE);
		$stack = array(array());
		$last_node_level = 0;
		$last_node = NULL;

		foreach ($items as $item)
		{
			$has_children = ($item->lft+1 < $item->rgt);

			if ($last_node_level == $item->lvl) {
				array_push($stack[count($stack)-1], array(
					'data' => $item->title,
					'children' => array(),
					'attributes' => array('id' => 'item_'.$item->id, 'rel' => 'menu'),
					'state' => $has_children ? 'open' : ''
				));

                $last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
                $last_node_level = $item->lvl;
			}
			elseif ($last_node_level < $item->lvl)
			{
				$stack[] = & $last_node;

				array_push($stack[count($stack)-1], array(
					'data' => $item->title,
					'children' => array(),
					'attributes' => array('id' => 'item_'.$item->id, 'rel' => 'menu'),
					'state' => $has_children ? 'open' : ''
				));

				$last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
				$last_node_level = $item->lvl;
			}
			elseif ($last_node_level > $item->lvl)
			{
				for ($i=0; $i < ($last_node_level-$item->lvl); $i++) {
					array_pop($stack);
				}

				array_push($stack[count($stack)-1], array(
					'data' => $item->title,
					'children' => array(),
					'attributes' => array('id' => 'item_'.$item->id, 'rel' => 'menu'),
					'state' => $has_children ? 'open' : ''
				));

				$last_node = & $stack[count($stack)-1][count($stack[count($stack)-1])-1]['children'];
				$last_node_level = $item->lvl;
			}
		}

		return json_encode($stack[0]);
	}

}