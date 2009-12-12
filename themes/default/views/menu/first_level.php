<?php defined('SYSPATH') or die('No direct script access.') ?>

<ul>
<?php

if ($menu !== NULL AND count($menu) > 0)
{
	foreach ($menu as $item)
	{
		if ($item->content->hide_menu)
		{
			continue;
		}

		$active = (rldecode($item->uri()) === Request::instance()->uri()) ? 'active' : '';
		$anchor = Html::anchor($item->uri(), $item->content->menu_title, array('class' => $active));

		echo '<li class="'.$active.'">'.$anchor."</li>\n";
	}
}
?>
</ul>