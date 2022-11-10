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
  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'active-form',
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


<div class="form-label-group">    
   <?php echo $form->textField($model,'shortcode',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'shortcode')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'shortcode'); ?>
   <?php echo $form->error($model,'shortcode'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'country_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'country_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'country_name'); ?>
   <?php echo $form->error($model,'country_name'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'phonecode',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'phonecode')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'phonecode'); ?>
   <?php echo $form->error($model,'phonecode'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>