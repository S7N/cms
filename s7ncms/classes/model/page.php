<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2009, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class Model_Page extends Sprig {

	protected $_table = 'pages';

	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'content' => new Sprig_Field_BelongsTo(array(
				'model' => 'Content'
			)),
		);
	}

	public function delete(Database_Query_Builder_Delete $query = NULL)
	{
		$this->content->delete();

		parent::delete();
	}

}