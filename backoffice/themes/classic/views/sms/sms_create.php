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
   <?php echo $form->textField($model,'contact_phone',array(
     'class'=>"form-control form-control-text mask_mobile",
     'placeholder'=>$form->label($model,'contact_phone')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'contact_phone'); ?>
   <?php echo $form->error($model,'contact_phone'); ?>
</div>


<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'sms_message',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Message")
   )); ?>      
   <?php echo $form->error($model,'sms_message'); ?>
</div>

<h6 class="mb-4"><?php echo t("Provider")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'gateway', (array) $provider,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'gateway'),
   )); ?>         
   <?php echo $form->error($model,'gateway'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>