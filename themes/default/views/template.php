<?php defined('SYSPATH') or die('No direct script access.') ?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title><?php echo $page_title ?></title>
	<link rel="stylesheet" href="<?php echo URL::base() ?>themes/default/css/style.css" type="text/css" media="screen" charset="utf-8" />
</head>

<body>
	<div id="header">
		<h1><?php echo Html::anchor('', 'S7Ncms') ?></h1>
	</div>
	<div id="main">
		<div id="content">
			<?php echo $page_content ?>
		</div>
	</div>
	<div id="sidebar">
		<ul>
			<li><h2>Menu</h2>
				<?php echo new View('menu/all', array('menu' => Sprig::factory('menu2')->load_all())) ?>
			</li>
		</ul>
	</div>
	<div id="footer">
		<p>&copy; 2009 Eduard Baun</p>
	</div>
	<?php if(Kohana::$profiling) echo View::factory('profiler/stats') ?>
</body>
</html>
