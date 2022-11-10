
<h6 class="mb-4"><?php echo t("Reset Password")?></h6>

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


<p class="dim"><?php echo t("Please enter a new password for your account.")?></p>

<div class="form-label-group">    
   <?php echo $form->passwordField($model,'new_password',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("New Password"),     
   )); ?>   
   <?php echo $form->labelEx($model,'new_password'); ?>
   <?php echo $form->error($model,'new_password'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->passwordField($model,'repeat_password',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Confirm New Password"),     
   )); ?>   
   <?php echo $form->labelEx($model,'repeat_password'); ?>
   <?php echo $form->error($model,'repeat_password'); ?>
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
'value'=>t("Next")
)); ?>
</div>
</div>

<?php $this->endWidget(); ?>