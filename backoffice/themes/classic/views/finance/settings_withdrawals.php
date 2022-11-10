<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => array('enctype' => 'multipart/form-data'),
	)
);
?>     

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


<h6 class="mb-3"><?php echo t("Emabled request payout")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"payout_request_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"payout_request_enabled",
     'checked'=>$model->payout_request_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="payout_request_enabled">
   <?php echo t("Enabled")?>
  </label>
</div>    

<h6 class="mt-3 mb-3"><?php echo t("Payout request auto process")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"payout_request_auto_process",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"payout_request_auto_process",
     'checked'=>$model->payout_request_auto_process==1?true:false
   )); ?>   
  <label class="custom-control-label" for="payout_request_auto_process">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="mt-2 form-label-group">    
   <?php echo $form->textField($model,'payout_process_days',array(
     'class'=>"form-control form-control-text numeric_only",
     'placeholder'=>$form->label($model,'payout_process_days'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'payout_process_days'); ?>
   <?php echo $form->error($model,'payout_process_days'); ?>
   <small><?php echo t("number of days that payout will automatically process (this works only if payout auto process is enabled). count starts from the day of request of merchant")?>.</small>   
</div>

<div class="mt-2 form-label-group">    
   <?php echo $form->textField($model,'payout_minimum_amount',array(
     'class'=>"form-control form-control-text numeric_only",
     'placeholder'=>$form->label($model,'payout_minimum_amount'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'payout_minimum_amount'); ?>
   <?php echo $form->error($model,'payout_minimum_amount'); ?>   
</div>


<div class="mt-2 form-label-group">    
   <?php echo $form->textField($model,'payout_number_can_request',array(
     'class'=>"form-control form-control-text numeric_only",
     'placeholder'=>$form->label($model,'payout_number_can_request'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'payout_number_can_request'); ?>
   <?php echo $form->error($model,'payout_number_can_request'); ?>
   <small><?php echo t("Number of payouts can request per month.")?></small>
</div>

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

      
<?php $this->endWidget(); ?>
