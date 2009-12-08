<?php echo Html::anchor('admin/static/create', Html::image(Theme::uri('images/page_add.png')), array('class' => 'button_add tooltip', 'title' => 'Add new static page')) ?>
&nbsp; <?php echo Html::anchor('admin/module/create', Html::image(Theme::uri('images/module_add.png')), array('class' => 'button_add tooltip', 'title' => 'Add new module page')) ?>
&nbsp; <?php echo Html::anchor('admin/redirect/create', Html::image(Theme::uri('images/redirect_add.png')), array('class' => 'button_add tooltip', 'title' => 'Add new redirect page')) ?>
&nbsp;&nbsp;
&nbsp; <?php echo Html::anchor('#', Html::image(Theme::uri('images/delete.png')), array('class' => 'button_delete tooltip', 'title' => 'Delete selected item')) ?>