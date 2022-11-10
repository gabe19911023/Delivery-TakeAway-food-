<div class="card">

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

<div class="form-label-group">    
   <?php echo $form->passwordField($model,'old_password',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'old_password'),
     'autocomplete'=>"new-password",
   )); ?>   
   <?php    
    echo $form->labelEx($model,'old_password'); ?>
   <?php echo $form->error($model,'old_password'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->passwordField($model,'new_password',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'new_password'),
     'autocomplete'=>"new-password",
   )); ?>   
   <?php    
    echo $form->labelEx($model,'new_password'); ?>
   <?php echo $form->error($model,'new_password'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->passwordField($model,'repeat_password',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'repeat_password'),
     'autocomplete'=>"new-password",
   )); ?>   
   <?php    
    echo $form->labelEx($model,'repeat_password'); ?>
   <?php echo $form->error($model,'repeat_password'); ?>
</div>

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>
 
 </div> <!--card body-->

</div><!--card-->