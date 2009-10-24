<?php echo Form::open() ?>
<dl>
<?php foreach ($page->content->inputs() as $label => $input): ?>
    <dt><?php echo $label ?></dt>
    <dd><?php echo $input ?></dd>

<?php endforeach ?>
</dl>
<?php echo Form::submit('submit', 'Submit') ?>
<?php echo Form::close() ?>

<?php echo Html::anchor('admin/page/delete/'.$page->id, 'Delete Page!') ?> (deletes the page immediately without warning!)