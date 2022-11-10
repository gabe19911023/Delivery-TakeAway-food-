<div class="row mb-4">
  <div class="col d-flex justify-content-start align-items-center"> 
   <h6 class="m-0 p-2 pd-5 with-icon-orders with-icon">Profile</h6>
  </div> <!--col-->     
</div> <!--row-->


<div class="card">
  <div class="card-body">
  
  <div class="row">
    <div class="col-md-4">
    
    <div class="preview-image mb-2">
     <div class="col-lg-7">
      <img src="<?php echo $avatar?>" class="img-fluid mb-2 rounded-circle img-120">
     </div>     
    </div>
    
    <div class="attributes-menu-wrap">
    <?php $this->widget('application.components.WidgetUserProfile',array());?>
    </div>
    
    </div> <!--col-->
    <div class="col-md-8">
    
    
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
   <?php echo $form->passwordField($model,'npassword',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'npassword'),
     'autocomplete'=>"new-password",
   )); ?>   
   <?php    
    echo $form->labelEx($model,'npassword'); ?>
   <?php echo $form->error($model,'npassword'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->passwordField($model,'cpassword',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'cpassword'),
     'autocomplete'=>"new-password",
   )); ?>   
   <?php    
    echo $form->labelEx($model,'cpassword'); ?>
   <?php echo $form->error($model,'cpassword'); ?>
</div>

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green w-100",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>
    
    </div> <!--col-->
  </div> <!--row-->
  
  </div> <!--card-body-->
</div> <!--card-->