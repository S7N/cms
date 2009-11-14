<?php defined('SYSPATH') or die('No direct script access.') ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>S7Ncms Administration</title>

		<script type="text/javascript">
			var base_uri = function(uri){return '<?php echo url::base(); ?>'+uri;}
			var site_uri = function(uri){return '<?php echo url::base(TRUE); ?>'+uri;}
			var theme_uri = function(uri){return base_uri('themes/admin/'+uri);}
		</script>
		<?php echo Assets::show() ?>
	</head>
	<body>
		<div id="dialog" style="display: none"></div>
		<div id="container">
			<div id="header">
				<h1>
					<a href="#">S7Ncms Administration</a>
				</h1>
				<div id="user-navigation">
					<ul>
						<li>
							<a href="#">Settings</a>
						</li>
						<li>
							<a class="logout" href="#">Logout</a>
						</li>
					</ul>
					<div class="clear"></div>
				</div>
				<div id="main-navigation">
					<ul>
						<li class="first">
							<?php echo Html::anchor('admin', 'Dashboard')?>
						</li>
						<li>
							<?php echo Html::anchor('admin/site', 'Site Structure')?>
						</li>
						<li>
							<?php echo Html::anchor('admin/menu', 'Menu')?>
						</li>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
			<div id="wrapper">
				<div id="sidebar">
					<div class="block">
						<h3>
							<?php echo $sidebar_title ?>
						</h3>
						<div class="content">
							<?php echo $sidebar_content ?>
						</div>
					</div>
				</div>
				<div id="main">
					<?php if (($msg = Session::instance()->get_once('error')) !== NULL): ?>
						<div class="flash">
							<div class="message error">
								<p><?php echo $msg ?></p>
							</div>
						</div>
					<?php endif ?>

					<?php if (($msg = Session::instance()->get_once('warning')) !== NULL): ?>
						<div class="flash">
							<div class="message warning">
								<p><?php echo $msg ?></p>
							</div>
						</div>
					<?php endif ?>

					<?php if (($msg = Session::instance()->get_once('notice')) !== NULL): ?>
						<div class="flash">
							<div class="message notice">
								<p><?php echo $msg ?></p>
							</div>
						</div>
					<?php endif ?>

					<div id="ajax_content">
					<?php echo $page_content ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</body>
</html>
