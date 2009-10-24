<?php defined('SYSPATH') or die('No direct script access.') ?>

<h1>S7Ncms 0.8</h1>
<ul>
<?php foreach (Sprig::factory('menu')->load(NULL, FALSE) as $menuitem): ?>
	<li><?php echo Html::anchor($menuitem->route->uri, $menuitem->title) ?></li>
<?php endforeach ?>
</ul>
<h2><?php echo $title ?></h2>
<?php echo $content ?>