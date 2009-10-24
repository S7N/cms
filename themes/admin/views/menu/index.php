<p><?php echo Html::anchor('admin/menu/create', 'Create a new Menu Item') ?></p>
<?php foreach ($menuitems as $menuitem): ?>
	<?php echo Html::anchor('admin/menu/update/'.$menuitem->id, $menuitem->title) ?><br />
<?php endforeach ?>