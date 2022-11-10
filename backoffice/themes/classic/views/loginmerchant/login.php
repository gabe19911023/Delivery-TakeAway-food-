 <!-- <h6 class="mb-4"><?php //echo Yii::t("app","Merchant Login")?></h6> -->


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableAjaxValidation'=>false,
)); ?>


<div class="form-label-group">    
   <?php echo $form->textField($model,'username',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>Yii::t("app","Username"),
     'autocomplete'=>"new-password",
   )); ?>   
   <?php echo $form->labelEx($model,'username'); ?>
   <?php echo $form->error($model,'username'); ?>     
</div>

<div class="form-label-group change_field_password">    
   <?php echo $form->passwordField($model,'password',array(
     'class'=>"form-control form-control-text",
     'autocomplete'=>"new-password",
     'placeholder'=>Yii::t("app","Password")
   )); ?>
    <?php echo $form->labelEx($model,'password'); ?>
   <?php echo $form->error($model,'password'); ?>
   <a href="javascript:;" class="change_field_href">
      <i class="zmdi zmdi-eye"></i>
   </a>
</div>

<?php if($captcha_enabled):?>
<?php
$this->widget('ext.yiiReCaptcha.ReCaptcha', array(
'model'     => $model,
'attribute' => 'verifyCode',
));
?>
<?php echo $form->error($model,'verifyCode'); ?>
<?php endif;?>

<div class="d-flex mb-2">
 <div class="p-2 flex-fill ">
 
    <div class="custom-checkbox ml-3"> 
	   <?php echo $form->checkBox($model,'rememberMe',array(
	     'class'=>"custom-control-input"
	   )); ?>
		<?php echo $form->label($model,'rememberMe',array(
		  'class'=>"dim custom-control-label"
		)); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>
 
 </div>
 <div class="p-2 flex-fill text-right">
   <a href="<?php echo Yii::app()->createUrl("/resetpswd")?>" 
	class="dim underline"><?php echo Yii::t("app","Forgot password?")?>
  </a>
 </div>
</div>
<!--flex-->

<div class="row text-left">
<div class="col-md-12 m-0">
<?php echo CHtml::submitButton('Login',array(
'class'=>"btn btn-green btn-full",
'value'=>Yii::t("app","Sign in")
)); ?>
</div>
</div>


<?php if(DEMO_MODE):?>
<div class="card border mt-4">
<div class="card-body p-3">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <p class="m-0">Username : mcuser</p>
        <p class="m-0">Password&nbsp; : mcuser</p>
      </div>
      <div>
       <a href="javascript:copyCredentials();" class="btn btn-green normal"><i class="zmdi zmdi-copy"></i></a>
      </div>
    </div>
</div>
</div>
<?php endif;?>

<?php $this->endWidget(); ?>