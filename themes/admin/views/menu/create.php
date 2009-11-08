<?php defined('SYSPATH') or die('No direct script access.');

$select = array('' => 'Select A Route Node...');
foreach ($routes as $route)
{
	$select[$route->id] = str_repeat('---', $route->lvl).' '.$route->title();
}
?>

<form>
<?php echo Form::label('route', 'Route:') ?>
<?php echo Form::select('route', $select, NULL, array('id' => 'route')) ?><br />

<?php echo Form::label('title', 'Title:') ?>
<?php echo Form::input('title', NULL, array('id' => 'title')) ?><br />
</form>