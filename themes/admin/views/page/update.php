<?php defined('SYSPATH') or die('No direct script access.') ?>
<div class="block">
	<div class="content">
		<h2 class="title">
			Create a new page
		</h2>
		<div class="inner">
			<?php echo Form::open(NULL, array('class' => 'form')) ?>
				<div class="group">
					<?php echo Form::label('title', $page->content->label('title'), array('class' => 'label')) ?>
					<?php echo Form::input('title', $page->content->title, array('class' => 'text_field')) ?>
					<span class="description">Title of the Page</span>
				</div>
				<div class="group">
					<?php echo Form::label('data', $page->content->label('data'), array('class' => 'label')) ?>
					<?php echo Form::textarea('data', $page->content->data, array('class' => 'text_area')) ?>
					<span class="description">Write here a long text</span>
				</div>
				<div class="group navform">
					<input type="submit" class="button" value="Save" /> or <?php echo Html::anchor('admin/page/delete/'.$page->id, 'Delete') ?> (deletes the page immediately without warning!)
				</div>
			<?php echo Form::close() ?>
		</div>
	</div>
</div>