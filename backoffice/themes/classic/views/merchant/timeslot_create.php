<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'forms',
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
  
<h6 class="mb-4"><?php echo t("Days")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'days', (array) $days,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'days'),
   )); ?>         
   <?php echo $form->error($model,'days'); ?>
</div>


<div class="row">

<div class="col-md-3">
<div class="form-label-group">    
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
</div> <!--row-->

<div class="col-md-3">
<div class="form-label-group">    
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
</div> <!--row-->

<div class="col-md-3">
<div class="form-label-group">    
   <?php echo $form->textField($model,'number_order_allowed',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'number_order_allowed')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'number_order_allowed'); ?>
   <?php echo $form->error($model,'number_order_allowed'); ?>
</div>
</div> <!--row-->
 
</div>
<!--row-->
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>