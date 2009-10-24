<p><?php echo Html::anchor('admin/page/create', 'Create a new Page') ?></p>
<?php foreach ($pages as $page): ?>
	<?php echo Html::anchor('admin/page/update/'.$page->id, $page->content->title) ?><br />
<?php endforeach ?>