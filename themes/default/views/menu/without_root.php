<?php defined('SYSPATH') or die('No direct script access.') ?>

<ul>
<?php

$continue = 0;
$last_item_has_children = FALSE;
$last_item_is_child = FALSE;

if ($menu !== NULL AND count($menu) > 0)
{
	$menu->next();

	if ($menu->current() !== FALSE)
	{
		$level = $init_lvl = $menu->current()->lvl;

		foreach ($menu as $item)
		{
			if ($continue > 0)
			{
				$continue--;
				continue;
			}

			// continue if this is a root node
			if ($item->is_root())
			{
				continue;
			}

			$item->content->load();

			// dont show hidden items
			if ($item->content->hide_menu)
			{
				if ($item->has_children())
				{
					$continue = count($item->descendants());
				}

				if ($last_item_has_children)
				{
					echo "</li>\n";
				}

				if ($last_item_is_child)
				{
					echo "</ul></li>\n";
				}

				$last_item_has_children = FALSE;
				$last_item_is_child = FALSE;

				continue;
			}

			$active = (urldecode($item->uri()) === Request::instance()->uri()) ? 'active' : '';
			$anchor = Html::anchor($item->uri(), $item->content->menu_title, array('class' => $active));

			$last_item_is_child = FALSE;

			if ($last_item_has_children)
			{
				echo "<ul>\n";
				$last_item_has_children = FALSE;
			}

			if ($item->has_children())
			{
				$last_item_has_children = TRUE;

				if ($level > $item->lvl)
				{
					$last_item_is_child = TRUE;

					echo str_repeat("</ul></li>\n", ($level - $item->lvl));
					echo '<li class="'.$active.'">'.$anchor."\n";
				}
				else
				{
					echo '<li class="'.$active.'">'.$anchor."\n";
				}
			}
			elseif ($level > $item->lvl)
			{
				$last_item_is_child = TRUE;

				echo str_repeat("</ul></li>\n", ($level - $item->lvl));
				echo '<li class="'.$active.'">'.$anchor.'</li>'."\n";
			}
			else
			{
				echo '<li class="'.$active.'">'.$anchor.'</li>'."\n";
			}

			$level = $item->lvl;
		}

		echo str_repeat("</ul></li>\n", ($level-$init_lvl));
	}
}

?>
</ul>