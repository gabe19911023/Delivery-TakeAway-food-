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
  

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_merchant_table_booking",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_merchant_table_booking",
     'checked'=>$model->enabled_merchant_table_booking==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_merchant_table_booking">
   <?php echo t("Enabled Booking")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"accept_booking_sameday",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"accept_booking_sameday",
     'checked'=>$model->accept_booking_sameday==1?true:false
   )); ?>   
  <label class="custom-control-label" for="accept_booking_sameday">
   <?php echo t("Accept booking same day")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_merchant_booking_alert",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_merchant_booking_alert",
     'checked'=>$model->enabled_merchant_booking_alert==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_merchant_booking_alert">
   <?php echo t("Enabled Alert Notifications")?>
  </label>
</div>    

<h6 class="mb-3 mt-3"><?php echo t("Fully booked message")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'fully_booked_msg',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Content")
   )); ?>      
   <?php echo $form->error($model,'fully_booked_msg'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_booking_receiver',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_booking_receiver')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_booking_receiver'); ?>
   <?php echo $form->error($model,'merchant_booking_receiver'); ?>
    <small class="form-text text-muted mb-2">
	  <?php echo t("Email address that will new booking.")?>
	</small>
</div>

  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>