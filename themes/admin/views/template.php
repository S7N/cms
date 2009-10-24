<?php defined('SYSPATH') or die('No direct script access.') ?>

<h1>S7Nadmin</h1>
<ul>
	<li><?php echo Html::anchor('admin/page', 'Pages') ?></li>
	<li><?php echo Html::anchor('admin/route', 'Routes') ?></li>
	<li><?php echo Html::anchor('admin/menu', 'Menu') ?></li>
</ul>

<h2><?php echo $title ?></h2>
<?php echo $content ?>