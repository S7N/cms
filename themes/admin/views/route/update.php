<?php echo Form::open() ?>

<?php
$select = array('' => 'Select a Page');
foreach ($pages as $page)
{
	$select[$page->id] = $page->content->title.' (ID: '.$page->id.')';
}

?>

<dl>
	<dt><label for="page">Page</label></dt>
	<dd><label for="page"><?php echo Form::select('page', $select, $route->page->id) ?></label></dd>

	<dt><label for="page">URI</label></dt>
	<dd><label for="page"><?php echo Form::input('uri', $route->uri) ?></label></dd>
</dl>

<?php echo Form::submit('submit', 'Submit') ?>
<?php echo Form::close() ?>

<?php echo Html::anchor('admin/route/delete/'.$route->id, 'Delete Route!') ?> (deletes the route immediately without warning!)