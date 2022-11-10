

<div class="card">

  <div class="text-center p-2 pt-4">
    <h5>Webiste & Admin Account Settings</h5>
    <p>Fill this form with basic infomation & admin login credentials</p>
  </div>
  
    <div class="card-body">
	    
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'profile',
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
  		
	<p class="lead">Admin accoun information</p>
    <div class="form-group">
       <?php echo $form->label($model,'first_name')?>
	   <?php echo $form->textField($model,'first_name',array(
	     'class'=>"form-control form-control-text",	     
	   )); ?>   	   
	   <?php echo $form->error($model,'first_name'); ?>
	</div>
	
	<div class="form-group">
       <?php echo $form->label($model,'last_name')?>
	   <?php echo $form->textField($model,'last_name',array(
	     'class'=>"form-control form-control-text",	     
	   )); ?>   	   
	   <?php echo $form->error($model,'last_name'); ?>
	</div>
	
	<div class="form-group">
       <?php echo $form->label($model,'email_address')?>
	   <?php echo $form->textField($model,'email_address',array(
	     'class'=>"form-control form-control-text",	     
	   )); ?>   	   
	   <?php echo $form->error($model,'email_address'); ?>
	</div>
	
	<div class="form-group">
       <?php echo $form->label($model,'contact_number')?>
	   <?php echo $form->textField($model,'contact_number',array(
	     'class'=>"form-control form-control-text",	
	     'maxlength'=>12,
	   )); ?>   	   
	   <?php echo $form->error($model,'contact_number'); ?>
	</div>

	<div class="form-group">
       <?php echo $form->label($model,'username')?>
	   <?php echo $form->textField($model,'username',array(
	     'class'=>"form-control form-control-text",	     
	   )); ?>   	   
	   <?php echo $form->error($model,'username'); ?>
	</div>
	
	<div class="form-group">
       <?php echo $form->label($model,'new_password')?>
	   <?php echo $form->passwordField($model,'new_password',array(
	     'class'=>"form-control form-control-text",	     
	   )); ?>   	   
	   <?php echo $form->error($model,'new_password'); ?>
	</div>
	
	<div class="form-group">
       <?php echo $form->label($model,'repeat_password')?>
	   <?php echo $form->passwordField($model,'repeat_password',array(
	     'class'=>"form-control form-control-text",	     
	   )); ?>   	   
	   <?php echo $form->error($model,'repeat_password'); ?>
	</div>

	
	<?php echo CHtml::submitButton('submit',array(
	'class'=>"btn btn-success w-100 rounded-0",
	'value'=>"Install"
	)); ?>
 
	<?php $this->endWidget(); ?>
    </div> <!--cad-body-->
  	
</div> 
 
</div> 
<!--card-->