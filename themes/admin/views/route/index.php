<p><?php echo Html::anchor('admin/route/create', 'Create a new Route') ?></p>
<?php foreach ($routes as $route): ?>
	<?php echo Html::anchor('admin/route/update/'.$route->id, '/'.$route->uri) ?><br />
<?php endforeach ?>