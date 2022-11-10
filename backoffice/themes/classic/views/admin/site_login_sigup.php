
  
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

<h6 class="mb-3"><?php echo t("Signup Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'signup_type', (array)$signup_type_list ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'signup_type'),
   )); ?>         
   <?php echo $form->error($model,'signup_type'); ?>
</div>		


<h6 class="m-0"><?php echo t("Signup Verifications")?></h6>
<p class="mb-3"><small><?php echo t("This settings only works in standard signup")?></small></p>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"signup_enabled_verification",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"signup_enabled_verification",
     'checked'=>$model->signup_enabled_verification==1?true:false
   )); ?>   
  <label class="custom-control-label" for="signup_enabled_verification">
   <?php echo t("Enabled")?>
  </label>
</div>    

<!--<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'signup_verification_type', (array)$verification_list ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'signup_verification_type'),
   )); ?>         
   <?php echo $form->error($model,'signup_verification_type'); ?>
</div>		-->

<div class="form-label-group">    
   <?php echo $form->textField($model,'signup_resend_counter',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'signup_resend_counter'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'signup_resend_counter'); ?>
   <?php echo $form->error($model,'signup_resend_counter'); ?>
</div>


<hr/>

<h6 class="mt-3 mb-3"><?php echo t("Google reCapcha")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"signup_enabled_capcha",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"signup_enabled_capcha",
     'checked'=>$model->signup_enabled_capcha==1?true:false
   )); ?>   
  <label class="custom-control-label" for="signup_enabled_capcha">
   <?php echo t("Enabled")?>
  </label>
</div>    

<h6 class="mt-3 mb-3"><?php echo t("Terms and condition")?></h6>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"signup_enabled_terms",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"signup_enabled_terms",
     'checked'=>$model->signup_enabled_terms==1?true:false
   )); ?>   
  <label class="custom-control-label" for="signup_enabled_terms">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'signup_terms',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("Your terms and condition here...")
   )); ?>      
   <?php echo $form->error($model,'signup_terms'); ?>
</div>

<hr/>
<h6 class="mb-3"><?php echo t("Welcome Template")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'signup_welcome_tpl', (array)$template_list ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'signup_welcome_tpl'),
   )); ?>         
   <?php echo $form->error($model,'signup_welcome_tpl'); ?>
</div>		

<h6 class="mb-3"><?php echo t("New Signup Template")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'signupnew_tpl', (array)$template_list ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'signupnew_tpl'),
   )); ?>         
   <?php echo $form->error($model,'signupnew_tpl'); ?>
   <small><?php echo t("this template will send to admin user")?></small>
</div>		

<h6 class="mb-3"><?php echo t("Verification Template")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'signup_verification_tpl', (array)$template_list ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'signup_verification_tpl'),
   )); ?>         
   <?php echo $form->error($model,'signup_verification_tpl'); ?>
</div>		

<h6 class="mb-3"><?php echo t("Reset Password Template")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'signup_resetpass_tpl', (array)$template_list ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'signup_resetpass_tpl'),
   )); ?>         
   <?php echo $form->error($model,'signup_resetpass_tpl'); ?>
</div>		

<hr/>

<h6 class="mb-3"><?php echo t("Block user from registering")?></h6>

<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'blocked_email_add',array(
     'class'=>"form-control form-control-text autosize",     
     'placeholder'=>t("Block email address list")
   )); ?>      
   <?php echo $form->error($model,'blocked_email_add'); ?>
</div>
<small class="form-text text-muted mb-3">
  <?php echo t("Multiple email separated by comma")?>
</small>

<div class="form-label-group">    
   <?php echo $form->textArea($model,'blocked_mobile',array(
     'class'=>"form-control form-control-text autosize",     
     'placeholder'=>t("Block mobile number list")
   )); ?>      
   <?php echo $form->error($model,'blocked_mobile'); ?>
</div>
<small class="form-text text-muted">
  <?php echo t("Multiple mobile separated by comma")?>
</small>
   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>