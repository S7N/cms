<?php defined('SYSPATH') or die('No direct script access.') ?>
<p>
	<?php echo Form::label('uri', 'URI') . Form::input('uri', $route->uri, array('id' => 'uri')) ?><br />
	<?php echo Form::label('target', 'Target') . Form::input('target', $route->target, array('id' => 'target')) ?>
</p>