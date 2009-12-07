<?php defined('SYSPATH') or die('No direct script access.') ?>

<ul>
<?php
if ($menu !== NULL AND count($menu) > 0)
{
	foreach ($menu as $item)
	{
		$active = (rldecode($item->uri()) === Request::instance()->uri()) ? 'active' : '';

		echo '<li class="'.$active.'">'.Html::anchor($item->uri(), $item->content->menu_title)."</li>\n";
	}
}
?>
</ul>