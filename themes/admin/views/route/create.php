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
	<dd><label for="page"><?php echo Form::select('page', $select) ?></label></dd>

	<dt><label for="page">URI</label></dt>
	<dd><label for="page"><?php echo Form::input('uri') ?></label></dd>
</dl>

<?php echo Form::submit('submit', 'Submit') ?>
<?php echo Form::close() ?>