
  
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

<h6 class="mb-4"><?php echo t("reCAPTCHA v2")?></h6>

<div class="form-label-group">    
   <?php echo $form->textField($model,'captcha_site_key',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'captcha_site_key')     
   )); ?>   
   <?php    
    echo $form->label($model,'captcha_site_key'); ?>
   <?php echo $form->error($model,'captcha_site_key'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'captcha_secret',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'captcha_secret')     
   )); ?>   
   <?php    
    echo $form->label($model,'captcha_secret'); ?>
   <?php echo $form->error($model,'captcha_secret'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'captcha_lang',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'captcha_lang')     
   )); ?>   
   <?php    
    echo $form->label($model,'captcha_lang'); ?>
   <?php echo $form->error($model,'captcha_lang'); ?>   
</div>
<small class="form-text text-muted mb-2">
  <?php echo t("default is = en")?>
</small>

<hr/>

<h6 class="mb-4"><?php echo t("Administration login")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"capcha_admin_login_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"capcha_admin_login_enabled",
     'checked'=>$model->capcha_admin_login_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="capcha_admin_login_enabled">
   <?php echo t("Enabled")?>
  </label>
</div>    


<h6 class="mb-4"><?php echo t("Merchant login")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"capcha_merchant_login_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"capcha_merchant_login_enabled",
     'checked'=>$model->capcha_merchant_login_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="capcha_merchant_login_enabled">
   <?php echo t("Enabled")?>
  </label>
</div>    
   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>