
  
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

<h6 class="mb-4"><?php echo t("Merchant Registration")?></h6>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_enabled_registration",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_enabled_registration",
     'checked'=>$model->merchant_enabled_registration==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_enabled_registration">
   <?php echo t("Enabled Registration")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_enabled_registration_capcha",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_enabled_registration_capcha",
     'checked'=>$model->merchant_enabled_registration_capcha==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_enabled_registration_capcha">
   <?php echo t("Enabled CAPTCHA")?>
  </label>
</div>    

<!--
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_reg_verification",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_reg_verification",
     'checked'=>$model->merchant_reg_verification==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_reg_verification">
   <?php echo t("Enabled Signup Verification")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_reg_admin_approval",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_reg_admin_approval",
     'checked'=>$model->merchant_reg_admin_approval==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_reg_admin_approval">
   <?php echo t("Enabled admin approval")?>
  </label>  
</div>    -->

<!--
<h6 class="mb-4 mt-4"><?php echo t("Registration Default Country")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_default_country', (array)$country_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'merchant_default_country'),
   )); ?>         
   <?php echo $form->error($model,'merchant_default_country'); ?>
</div>		   
-->

<h6 class="mb-4 mt-4"><?php echo t("Membership Program")?></h6>   
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'registration_program', (array)$program_list,array(
     'class'=>"form-control custom-select form-control-select select_two",     
     'placeholder'=>$form->label($model,'registration_program'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'registration_program'); ?>
</div>	

<h6 class="mb-4 mt-4"><?php echo t("Set Specific Country")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_specific_country',(array)$country_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'merchant_specific_country'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'merchant_specific_country'); ?>
</div>
<small class="form-text text-muted mb-2">
  <?php echo t("leave empty to show all country")?>
</small>

<h6 class="mb-4 mt-4"><?php echo t("Terms and conditions")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'registration_terms_condition',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("Your terms and condition here...")
   )); ?>      
   <?php echo $form->error($model,'registration_terms_condition'); ?>
</div>

<h6 class="mb-4"><?php echo t("Pre-configure food item size")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'pre_configure_size',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("")
   )); ?>      
   <?php echo $form->error($model,'pre_configure_size'); ?>
</div>
<small class="form-text text-muted mb-3">
  <?php echo t("this will be added as default food item size to merchant during registration. value must be separated by comma eg. small,medium,large")?>
</small>

<hr/>

<h5><?php echo t("Templates")?></h5>

<h6 class="mb-4 mt-4"><?php echo t("Confirm Account")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'registration_confirm_account_tpl', (array)$template_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'registration_confirm_account_tpl'),
   )); ?>         
   <?php echo $form->error($model,'registration_confirm_account_tpl'); ?>
</div>		   

<h6 class="mb-4 mt-4"><?php echo t("Welcome email")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_registration_welcome_tpl', (array)$template_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'merchant_registration_welcome_tpl'),
   )); ?>         
   <?php echo $form->error($model,'merchant_registration_welcome_tpl'); ?>
</div>		   


<h6 class="mb-4 mt-4"><?php echo t("Plan Near Expiration")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_plan_near_expired_tpl', (array)$template_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'merchant_plan_near_expired_tpl'),
   )); ?>         
   <?php echo $form->error($model,'merchant_plan_near_expired_tpl'); ?>
</div>		   

<h6 class="mb-4 mt-4"><?php echo t("Plan Expired")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_plan_expired_tpl', (array)$template_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'merchant_plan_expired_tpl'),
   )); ?>         
   <?php echo $form->error($model,'merchant_plan_expired_tpl'); ?>
</div>		   


<h6 class="mb-4 mt-4"><?php echo t("New Signup")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_registration_new_tpl', (array)$template_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'merchant_registration_new_tpl'),
   )); ?>         
   <?php echo $form->error($model,'merchant_registration_new_tpl'); ?>
   <small><?php echo t("this template will send to admin")?></small>
</div>		   


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>