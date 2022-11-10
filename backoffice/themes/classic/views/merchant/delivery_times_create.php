<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>array(
    t("All Store Hours")=>array(Yii::app()->controller->id.'/delivery_times'), 
    $this->pageTitle,           
),
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


<h6 class="mb-4"><?php echo t("days")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'day', (array) $days,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'day'),
   )); ?>         
   <?php echo $form->error($model,'day'); ?>
</div>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"status",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>'open',
     'id'=>"status",
     'checked'=>$model->status=='open'?true:false
   )); ?>   
  <label class="custom-control-label" for="status">
   <?php echo t("Open")?>
  </label>
</div>    

<div class="d-flex">

<div class="form-label-group mr-3">    
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
 
</div>
<!--flex-->



<div class="d-flex">

<div class="form-label-group mr-3">    
   <?php echo $form->textField($model,'start_time_pm',array(
     'class'=>"form-control form-control-text timepick datetimepicker-input",     
     'placeholder'=>$form->label($model,'start_time_pm'),     
     'readonly'=>true,
     'data-toggle'=>'datetimepicker'
   )); ?>   
   <?php    
    echo $form->labelEx($model,'start_time_pm'); ?>
   <?php echo $form->error($model,'start_time_pm'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'end_time_pm',array(
     'class'=>"form-control form-control-text timepick datetimepicker-input",     
     'placeholder'=>$form->label($model,'end_time_pm'),     
     'readonly'=>true,
     'data-toggle'=>'datetimepicker'
   )); ?>   
   <?php    
    echo $form->labelEx($model,'end_time_pm'); ?>
   <?php echo $form->error($model,'end_time_pm'); ?>
</div>
 
</div>
<!--flex-->


<div class="form-label-group">    
   <?php echo $form->textField($model,'custom_text',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'custom_text')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'custom_text'); ?>
   <?php echo $form->error($model,'custom_text'); ?>
</div>

   
  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>