<div class="row">
<div class="col-md-6">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
    'links'=>isset($links)?$links:'',
    'homeLink'=>false,
    'separator'=>'<span class="separator">
    <i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</div> <!--col-->

<div class="col-md-6">

<div class="d-flex flex-row justify-content-end">
  <div class="p-2">
  
  <a type="button" class="btn btn-black btn-circle checkbox_select_all" 
  href="javascript:;">
    <i class="zmdi zmdi-check"></i>
  </a>
  
  </div>
  <div class="p-2"><h5><?php echo t("Check All")?></h5></div>
</div> <!--flex-->

</div> <!--col-->
</div><!-- row-->


<div class="card">
 <div class="card-body">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'form',
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
   <?php echo $form->textField($model,'role_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'role_name'),     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'role_name'); ?>
   <?php echo $form->error($model,'role_name'); ?>
</div>

<?php echo $form->error($model,'role_access');?>

<?php if(is_array($menu) && count($menu)>=1):?>
<div class="row mt-3">

<?php foreach ($menu as $val):?>
<?php $action_name = !empty($val['action_name'])?$val['action_name']:$val['label'];?>

	<div class="col-lg-3	 col-md-12 mb-4 mb-lg-3">
	<div class="card">
	<div class="card-header"><?php echo t($val['label'])?></div>
	 <div class="card-body">
	 
	 
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"role_access[$action_name]",array(
     'class'=>"custom-control-input checkbox_child",     
     'id'=>"role_access[$action_name]",
     'checked'=>in_array($action_name,(array)$role_access)?true:false
   )); ?>   
  <label class="custom-control-label" for="role_access[<?php echo $action_name?>]">
   <?php echo t($val['label'])?>
  </label>
</div>    

<?php if(isset($val['items'])):?>
   <?php foreach ($val['items'] as $val_items):?>
   
   <?php $items_action_name = !empty($val_items['action_name'])?$val_items['action_name']:$val_items['label'];?>
   
   <div class="custom-control custom-switch custom-switch-md">  
	  <?php echo $form->checkBox($model,"role_access[$items_action_name]",array(
	     'class'=>"custom-control-input checkbox_child",     
	     'id'=>"role_access[$items_action_name]",
	     'checked'=>in_array($items_action_name,(array)$role_access)?true:false
	   )); ?>   
	  <label class="custom-control-label" for="role_access[<?php echo $items_action_name?>]">
	   <?php echo t($val_items['label'])?>
	  </label>
	</div>    
   
   <?php endforeach;?>
<?php endif;?>

	 
	</div> <!--body-->
	</div> <!--card--> 
	</div> <!--col-->
<?php endforeach;?>	
		
</div> <!--row-->
<?php endif;?>
 

 
 </div> <!--body-->
</div> <!--card-->

<DIV class="float-button  text-center p-4">
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green w-100",
'value'=>t("Save")
)); ?>
</DIV>

<?php $this->endWidget(); ?>
