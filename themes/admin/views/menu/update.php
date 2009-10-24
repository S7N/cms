<?php echo Form::open() ?>

<?php
$select = array('' => 'Select A Route Node');
foreach ($routes as $route)
{
	$select[$route->id] = '/'.$route->uri.' (Page: '.$route->page->content->title.')';
}
?>

<dl>
	<dt><label for="page">Route</label></dt>
	<dd><label for="page"><?php echo Form::select('route', $select, $menuitem->route->id) ?></label></dd>

	<dt><label for="page">Title</label></dt>
	<dd><label for="page"><?php echo Form::input('title', $menuitem->title) ?></label></dd>
</dl>

<?php echo Form::submit('submit', 'Submit') ?>
<?php echo Form::close() ?>

<?php echo Html::anchor('admin/menu/delete/'.$menuitem->id, 'Delete Menu Item!') ?> (deletes the menu item immediately without warning!)