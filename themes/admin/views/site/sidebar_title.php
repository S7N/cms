<?php echo Html::anchor('admin/page/create', Html::image(Theme::uri('images/page_add.png')), array('class' => 'button_add')) ?>
&nbsp; <?php echo Html::anchor('admin/module/create', Html::image(Theme::uri('images/module_add.png')), array('class' => 'button_add')) ?>
&nbsp; <?php echo Html::anchor('admin/redirect/create', Html::image(Theme::uri('images/redirect_add.png')), array('class' => 'button_add')) ?>
&nbsp;&nbsp;
&nbsp; <?php echo Html::anchor('#', Html::image(Theme::uri('images/delete.png')), array('class' => 'button_delete')) ?>