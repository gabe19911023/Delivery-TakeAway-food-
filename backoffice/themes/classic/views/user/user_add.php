<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
    'links'=>array(
        t("All User")=>array('user/list'),        
        $model->isNewRecord?t("Add new"):t("Edit User"),
    ),
    'homeLink'=>false,
    'separator'=>'<span class="separator">
    <i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>

<div class="card">

 <div class="card-body">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'profile',
	'enableAjaxValidation'=>false,
)); 
?>

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


<div class="row">
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'first_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'first_name'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'first_name'); ?>
   <?php echo $form->error($model,'first_name'); ?>
</div>
  
  </div> <!--col-->
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'last_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'last_name'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'last_name'); ?>
   <?php echo $form->error($model,'last_name'); ?>
</div>
  
  </div> <!--col-->
</div><!--row-->

<div class="row">
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'email_address',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'email_address'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'email_address'); ?>
   <?php echo $form->error($model,'email_address'); ?>
</div>
  
  </div> <!--col-->
  
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'contact_number',array(
     'class'=>"form-control form-control-text mask_mobile",
     'placeholder'=>$form->label($model,'contact_number'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'contact_number'); ?>
   <?php echo $form->error($model,'contact_number'); ?>
</div>
  
  </div><!-- col-->
</div><!--row-->  


<div class="form-label-group">    
   <?php echo $form->textField($model,'username',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'username'),
     //'readonly'=>true
   )); ?>   
   <?php    
    echo $form->labelEx($model,'username'); ?>
   <?php echo $form->error($model,'username'); ?>
</div>

<h6 class="mb-4"><?php echo CommonUtility::t("Status")?></h6>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status',(array)$status,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status'),
   )); ?>         
   <?php echo $form->error($model,'status'); ?>
</div>

<h6 class="mb-4"><?php echo CommonUtility::t("Role")?></h6>


<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'role',(array)$role,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'role'),
   )); ?>         
   <?php echo $form->error($model,'role'); ?>
</div>


<h6 class="mb-4"><?php echo CommonUtility::t("Password")?></h6>


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