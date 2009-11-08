<?php defined('SYSPATH') or die('No direct script access.') ?>

<?php echo Form::label('type', 'Type:') ?>
<?php echo Form::select('type', array('' => 'Select Type', 'static' => 'Static Page', 'redirect' => 'Redirect', 'module' => 'Module'), NULL, array('id' => 'type')) ?>
<div id="type_form"></div>
