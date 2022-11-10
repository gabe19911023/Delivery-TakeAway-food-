<div class="d-flex justify-content-between">
<nav class="navbar navbar-light justify-content-between">
<a class="navbar-brand">
<h5><?php echo CHtml::encode($this->pageTitle)?></h5>
</a>
</nav>
</div>

<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
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
   <?php echo $form->textField($model,'sms_notify_number',array(
     'class'=>"form-control form-control-text mask_mobile",
     'placeholder'=>$form->label($model,'sms_notify_number')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'sms_notify_number'); ?>
   <?php echo $form->error($model,'sms_notify_number'); ?>
</div>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"order_verification",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"order_verification",
     'checked'=>$model->order_verification==1?true:false
   )); ?>   
  <label class="custom-control-label" for="order_verification">
   <?php echo t("Enabled Order SMS Verification")?>
  </label>
</div>    


<div class="form-label-group">    
   <?php echo $form->textField($model,'order_sms_code_waiting',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'order_sms_code_waiting')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'order_sms_code_waiting'); ?>
   <?php echo $form->error($model,'order_sms_code_waiting'); ?>
</div>

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

  </div> <!--body-->
</div> <!--card-->



<?php $this->endWidget(); ?>