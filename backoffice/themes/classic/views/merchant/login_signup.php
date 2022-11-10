
  
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


<h6 class="m-0"><?php echo t("Signup Verifications")?></h6>
<p class="text-muted"><?php echo t("Notice : this section need to be fill only if you have single website restaurant")?>.</p>	
<p class="mb-3"><small><?php echo t("This settings only works in standard signup")?></small></p>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_signup_enabled_verification",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_signup_enabled_verification",
     'checked'=>$model->merchant_signup_enabled_verification==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_signup_enabled_verification">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_signup_resend_counter',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_signup_resend_counter'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_signup_resend_counter'); ?>
   <?php echo $form->error($model,'merchant_signup_resend_counter'); ?>
</div>


<hr/>

<h6 class="mt-3 mb-3"><?php echo t("Terms and condition")?></h6>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_signup_enabled_terms",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_signup_enabled_terms",
     'checked'=>$model->merchant_signup_enabled_terms==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_signup_enabled_terms">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'merchant_signup_terms',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("Your terms and condition here...")
   )); ?>      
   <?php echo $form->error($model,'merchant_signup_terms'); ?>
</div>

   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>