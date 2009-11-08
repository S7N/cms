<?php defined('SYSPATH') or die('No direct script access.') ?>

<ul>
<?php

if (count($menu) > 0)
{
	$level = $menu->current()->lvl;

	foreach ($menu as $item)
	{
		$active = ($item->route->uri() === Request::instance()->uri()) ? 'active' : '';

		if ($item->has_children())
		{
			if ($level > $item->lvl)
			{
				echo str_repeat("</ul></li>\n",($level - $item->lvl));
				echo '<li class="'.$active.'">'.Html::anchor($item->route->uri(), $item->title)."\n";
				echo '<ul>'."\n";
			}
			else
			{
				echo '<li class="'.$active.'">'.Html::anchor($item->route->uri(), $item->title)."\n";
				echo '<ul>'."\n";
			}
		}
		elseif ($level > $item->lvl)
		{
			echo str_repeat("</ul></li>\n",($level - $item->lvl));
			echo'<li class="'.$active.'">'.Html::anchor($item->route->uri(), $item->title).'</li>'."\n";
		}
		else
		{
			echo '<li class="'.$active.'">'.Html::anchor($item->route->uri(), $item->title).'</li>'."\n";
		}

		$level = $item->lvl;
	}

	echo str_repeat("</ul></li>\n", $level);
}
?>
</ul>