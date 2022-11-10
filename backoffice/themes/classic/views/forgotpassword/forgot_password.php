<h6 class="mb-4"><?php echo t("Forgot Password")?></h6>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableAjaxValidation'=>false,
)); ?>


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


<p class="dim"><?php echo t("Please input your E-mail address.")?><br/>
<?php echo t("An E-mail with link to password recovery will be sent to your account.")?></p>

<div class="form-label-group">    
   <?php echo $form->textField($model,'email_address',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Email Address"),     
   )); ?>   
   <?php echo $form->labelEx($model,'email_address'); ?>
   <?php echo $form->error($model,'email_address'); ?>
</div>


<div class="d-flex mb-2">
 
 <div class="p-2 flex-fill">
   <a href="<?php echo isset($back_link)?$back_link:''?>" 
	class="dim underline"><?php echo t("Login here")?>
  </a>
 </div>
</div>
<!--flex-->

<div class="row text-left">
<div class="col-md-12 m-0">
<?php echo CHtml::submitButton('Login',array(
'class'=>"btn btn-green btn-full",
'value'=>t("Request E-mail")
)); ?>
</div>
</div>

<?php $this->endWidget(); ?>