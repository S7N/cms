<?php echo Form::open() ?>

<?php
$select = array('' => 'Select A Route Node');
foreach ($routes as $route)
{
	$select[$route->id] = '/'.$route->uri.' (Page: '.$route->page->content->title.')';
}
?>

<dl>
	<dt><label for="route">Route</label></dt>
	<dd><label for="route"><?php echo Form::select('route', $select) ?></label></dd>

	<dt><label for="title">Title</label></dt>
	<dd><label for="title"><?php echo Form::input('title') ?></label></dd>
</dl>

<?php echo Form::submit('submit', 'Submit') ?>
<?php echo Form::close() ?>