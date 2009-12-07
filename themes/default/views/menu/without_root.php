<?php defined('SYSPATH') or die('No direct script access.') ?>

<ul>
<?php

if ($menu !== NULL AND count($menu) > 0)
{
	$menu->next();
	$level = $init_lvl = $menu->current()->lvl;

	foreach ($menu as $item)
	{
		if ($item->lvl === 0)
		{
			continue;
		}

		$active = (urldecode($item->uri()) === Request::instance()->uri()) ? 'active' : '';

		if ($item->has_children())
		{
			if ($level > $item->lvl)
			{
				echo str_repeat("</ul></li>\n",($level - $item->lvl));
				echo '<li class="'.$active.'">'.Html::anchor($item->uri(), $item->content->menu_title, array('class' => $active))."\n";
				echo '<ul>'."\n";
			}
			else
			{
				echo '<li class="'.$active.'">'.Html::anchor($item->uri(), $item->content->menu_title, array('class' => $active))."\n";
				echo '<ul>'."\n";
			}
		}
		elseif ($level > $item->lvl)
		{
			echo str_repeat("</ul></li>\n",($level - $item->lvl));
			echo'<li class="'.$active.'">'.Html::anchor($item->uri(), $item->content->menu_title, array('class' => $active)).'</li>'."\n";
		}
		else
		{
			echo '<li class="'.$active.'">'.Html::anchor($item->uri(), $item->content->menu_title, array('class' => $active)).'</li>'."\n";
		}

		$level = $item->lvl;
	}
}

echo str_repeat("</ul></li>\n", ($level-$init_lvl));
?>
</ul>