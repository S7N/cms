<?php defined('SYSPATH') or die('No direct script access.') ?>
<div class="block">
	<h3>
		Update Redirect
	</h3>
	<div class="content">
		<div class="inner">
			<?php echo Form::open('admin/redirect/update/'.$page->id, array('class' => 'form', 'id' => 'ajaxform')) ?>
				<div class="group">
					<?php echo $page->content->label('title', array('class' => 'label')) ?> <span class="error"></span>
					<?php echo $page->content->input('title', array('class' => 'text_field')) ?>
					<span class="description">Unique URI of this Redirect</span>
				</div>
				<div class="group">
					<?php echo $page->content->label('slug', array('class' => 'label')) ?> <span class="error"></span>
					<?php echo $page->content->input('slug', array('class' => 'text_field')) ?>
					<span class="description">Unique URI of this Redirect</span>
				</div>
				<div class="group">
					<?php echo $page->content->label('data', array('class' => 'label')) ?> <span class="error"></span>
					<?php echo $page->content->input('data', array('class' => 'text_area')) ?>
					<span class="description">Target</span>
				</div>
				<div class="group">
					<?php echo $page->content->label('menu_title', array('class' => 'label')) ?> <span class="error"></span>
					<?php echo $page->content->input('menu_title', array('class' => 'text_field')) ?>
				</div>
				<div class="group">
					<?php echo $page->content->input('hide_menu') ?>
				</div>
				<div class="group navform">
					<input type="submit" class="button" value="Save" />
				</div>
			<?php echo Form::close() ?>
		</div>
	</div>
</div>