<?php echo Form::open() ?>
<dl>
<?php foreach ($page->inputs() as $label => $input): ?>
    <dt><?php echo $label ?></dt>
    <dd><?php echo $input ?></dd>

<?php endforeach ?>
</dl>
<?php echo Form::submit('submit', 'Submit') ?>
<?php echo Form::close() ?>