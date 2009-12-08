<?php defined('SYSPATH') or die('No direct script access.') ?>
<div class="block">
	<h3>
		Update Page
	</h3>
	<div class="content">
		<div class="inner">
			<?php echo Form::open('admin/static/update/'.$page->id, array('class' => 'form', 'id' => 'ajaxform')) ?>
				<div class="group">
					<?php echo $page->content->label('title', array('class' => 'label')) ?> <span class="error"></span>
					<?php echo Form::input('title', $page->content->title, array('class' => 'text_field')) ?>
					<span class="description">Title of the Page</span>
				</div>
				<div class="group">
					<?php echo $page->content->label('slug', array('class' => 'label')) ?> <span class="error"></span>
					<?php echo $page->content->input('slug', array('class' => 'text_field')) ?>
					<span class="description">Unique URI of this Page</span>
				</div>
				<div class="group">
					<?php echo $page->content->label('data', array('class' => 'label')) ?> <span class="error"></span>
					<?php echo $page->content->input('data', array('class' => 'text_area')) ?>
					<span class="description">Write here a long text</span>
				</div>
				<div class="group navform">
					<input type="submit" class="button" value="Save" />
				</div>
			<?php echo Form::close() ?>
		</div>
	</div>
</div>