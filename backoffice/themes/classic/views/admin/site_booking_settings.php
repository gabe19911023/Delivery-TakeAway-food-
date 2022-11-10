
  
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

<h6 class="mb-4"><?php echo t("Booking")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_tbl_book_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_tbl_book_enabled",
     'checked'=>$model->merchant_tbl_book_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_tbl_book_enabled">
   <?php echo t("Enabled Booking")?>
  </label>
</div>    

<h6 class="mb-4 mt-4"><?php echo t("Cancelation of booking will only be applied on the following condition")?></h6>


<div class="form-label-group">    
   <?php echo $form->numberField($model,'booking_cancel_days',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'booking_cancel_days'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'booking_cancel_days'); ?>
   <?php echo $form->error($model,'booking_cancel_days'); ?>
</div>


  <div class="form-label-group">    
   <?php echo $form->textField($model,'booking_cancel_hours',array(
     'class'=>"form-control form-control-text mask_time",
     'placeholder'=>$form->label($model,'booking_cancel_hours'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'booking_cancel_hours'); ?>
   <?php echo $form->error($model,'booking_cancel_hours'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>