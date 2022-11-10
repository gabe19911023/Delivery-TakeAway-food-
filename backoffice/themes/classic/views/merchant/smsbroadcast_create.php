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
  

<h6 class="mb-4"><?php echo t("Sending Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'send_to', (array) $send_to,array(
     'class'=>"form-control custom-select form-control-select broadcast_send_to",
     'placeholder'=>$form->label($model,'send_to'),
   )); ?>         
   <?php echo $form->error($model,'send_to'); ?>
</div>

<h6 class="mb-4"><?php echo t("SMS Message")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'sms_alert_message',array(
     'class'=>"form-control form-control-text",          
   )); ?>      
   <?php echo $form->error($model,'sms_alert_message'); ?>
</div>

<div class="broadcast_list_mobile">
<h6 class="mb-4"><?php echo t("List of mobile number")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'list_mobile_number',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("List of mobile number")
   )); ?>      
   <?php echo $form->error($model,'list_mobile_number'); ?>

   <small class="form-text text-muted mb-2">
    <?php echo t("Mobile number must be separated by comma")?>
  </small>
   
</div>
</div>


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>