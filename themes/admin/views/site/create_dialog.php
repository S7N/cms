<?php defined('SYSPATH') or die('No direct script access.') ?>
<p>
<?php echo Form::label('type', 'Select Type:') ?><br />
<?php echo Form::radio('type', 'static') ?> Static Page<br />
<?php echo Form::radio('type', 'redirect') ?> Redirect<br />
<?php echo Form::radio('type', 'module') ?> Module
<?php //echo Form::select('types', array('' => 'Select Type', 'static' => 'Static Page', 'redirect' => 'Redirect', 'module' => 'Module'), NULL, array('id' => 'type')) ?>
</p>
<div id="type_form"></div>
