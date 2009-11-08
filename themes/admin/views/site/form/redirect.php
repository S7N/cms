<?php defined('SYSPATH') or die('No direct script access.') ?>
<p>
	<?php echo Form::label('uri', 'URI') . Form::input('uri', NULL, array('id' => 'uri')) ?><br />
	<?php echo Form::label('target', 'Target') . Form::input('target', NULL, array('id' => 'target')) ?>
</p>