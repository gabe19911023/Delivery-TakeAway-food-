<!--<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links,
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>-->
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'forms',
		'enableAjaxValidation' => false,		
	)
);
?>

<div class="card">
  <div class="card-body">
 
<?php if(Yii::app()->user->hasFlash('success')): ?>
	<div class="alert alert-success">
		<?php echo Yii::app()->user->getFlash('success'); ?>
	</div>
<?php endif;?>

<?php if(Yii::app()->user->hasFlash('error')): ?>
	<div class="alert alert-danger">
		<?php echo Yii::app()->user->getFlash('error'); ?>
	</div>
<?php endif;?>

<h6 class=""><?php echo isset($label['title'])?$label['title']:''?></h6>
<p><?php echo isset($label['sub'])?$label['sub']:''?></p>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'meta_value',array(
     'class'=>"form-control form-control-text",          
   )); ?>      
   <?php echo $form->error($model,'meta_value'); ?>
</div>

  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>