<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links,
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>

  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
		'enableAjaxValidation' => false,		
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
  
<h6 class="mb-3"><?php echo t("Transaction Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'transaction_type', (array) $services,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'transaction_type'),
   )); ?>         
   <?php echo $form->error($model,'transaction_type'); ?>
</div>

<h6 class="mb-4"><?php echo t("days")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'days_selected', (array)$days,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'days_selected'),
   )); ?>         
   <?php echo $form->error($model,'days_selected'); ?>
</div>

<div class="d-flex">

<div class="form-label-group mr-3 ">    
   <?php echo $form->textField($model,'start_time',array(
     'class'=>"form-control form-control-text timepick datetimepicker-input",     
     'placeholder'=>$form->label($model,'start_time'),     
     'readonly'=>true,
     'data-toggle'=>'datetimepicker'
   )); ?>   
   <?php    
    echo $form->labelEx($model,'start_time'); ?>
   <?php echo $form->error($model,'start_time'); ?>
</div>

<div class="form-label-group mr-3">    
   <?php echo $form->textField($model,'end_time',array(
     'class'=>"form-control form-control-text timepick datetimepicker-input",     
     'placeholder'=>$form->label($model,'end_time'),     
     'readonly'=>true,
     'data-toggle'=>'datetimepicker'
   )); ?>   
   <?php    
    echo $form->labelEx($model,'end_time'); ?>
   <?php echo $form->error($model,'end_time'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'number_order_allowed',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'number_order_allowed')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'number_order_allowed'); ?>
   <?php echo $form->error($model,'number_order_allowed'); ?>
</div>


</div> <!--flex-->

<h6 class="mb-4"><?php echo t("Order Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'order_status_selected', (array)$order_status,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'order_status_selected'),
   )); ?>         
   <?php echo $form->error($model,'order_status_selected'); ?>
   <small class="form-text text-muted mb-2">
	  <?php echo t("Status that will count the existing order, if empty will use all status.")?>
	</small>
</div>


<h6 class="mb-4"><?php echo t("Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array) $status,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status'),
   )); ?>         
   <?php echo $form->error($model,'status'); ?>
</div>


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>