<?php defined('SYSPATH') or die('No direct script access.') ?>

<ul>
<?php
if (count($menu) > 0)
{
	foreach ($menu as $item)
	{
		$active = ($item->uri() === Request::instance()->uri()) ? 'active' : '';

		echo '<li class="'.$active.'">'.Html::anchor($item->uri(), $item->title)."</li>\n";
	}
}
?>
</ul>