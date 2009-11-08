<?php defined('SYSPATH') or die('No direct script access.') ?>

<h1>S7Ncms 0.8</h1>
<?php echo new View('menu/default', array('menu' => Sprig::factory('menu')->load(NULL, FALSE))) ?>
<?php echo $content ?>