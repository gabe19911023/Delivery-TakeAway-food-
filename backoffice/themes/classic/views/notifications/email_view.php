<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links,
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>

<?php $this->beginWidget('CHtmlPurifier'); ?>
<div class="card with-border">
 <div class="card-header">
    <?php echo $model->subject?>
  </div>  
  <div class="card-body">
   <p class="card-text"><?php echo $model->content?></div>
 </div> <!--body-->
</div> <!--card-->
<?php $this->endWidget(); ?>