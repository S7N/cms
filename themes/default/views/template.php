<?php defined('SYSPATH') or die('No direct script access.') ?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo $page_title ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<style type="text/css">
		/*<![CDATA[*/
			body { font-family: Georgia; }
		/*]]>*/
		</style>
	</head>
	<body>
		<h1>Menu</h1>
		<?php echo new View('menu/first_level', array('menu' => Sprig::factory('menu2')->first_level())) ?>
		<h1>Content</h1>
		<?php echo $page_content ?>

		<?php if(Kohana::$profiling) echo View::factory('profiler/stats') ?>
		<hr />
		<h1>Submenu</h1>
		<?php echo new View('menu/all', array('menu' => Sprig::factory('menu2')->submenu())); ?>
	</body>
</html>