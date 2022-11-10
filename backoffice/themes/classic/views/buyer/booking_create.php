<?php if(!$hide_nav):?>
<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links2,
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>
<?php endif;?>
  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'active-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => array('enctype' => 'multipart/form-data'),
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

<DIV class="row">
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'number_guest',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'number_guest')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'number_guest'); ?>
	   <?php echo $form->error($model,'number_guest'); ?>
	</div>
 </DIV>
 <DIV class="col-md-6">	 	
 <div class="form-label-group">    
   <?php echo $form->textField($model,'date_booking',array(
     'class'=>"form-control form-control-text datepick",
     'readonly'=>true,
     'placeholder'=>$form->label($model,'date_booking'),          
   )); ?>   
   <?php    
    echo $form->labelEx($model,'date_booking'); ?>
   <?php echo $form->error($model,'date_booking'); ?>
</div>
 </DIV> 
</DIV>
<!--row-->

<div class="form-label-group">    
   <?php echo $form->textField($model,'booking_time',array(
     'class'=>"form-control form-control-text timepick datetimepicker-input",     
     'placeholder'=>$form->label($model,'booking_time'),     
     'readonly'=>true,
     'data-toggle'=>'datetimepicker'
   )); ?>   
   <?php    
    echo $form->labelEx($model,'booking_time'); ?>
   <?php echo $form->error($model,'booking_time'); ?>
</div>



<DIV class="row">
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'booking_name',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'booking_name')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'booking_name'); ?>
	   <?php echo $form->error($model,'booking_name'); ?>
	</div>
 </DIV>
 <DIV class="col-md-6">	 	
 <div class="form-label-group">    
   <?php echo $form->textField($model,'email',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>$form->label($model,'email'),     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'email'); ?>
   <?php echo $form->error($model,'email'); ?>
</div>
 </DIV> 
</DIV>
<!--row-->

<div class="form-label-group">    
   <?php echo $form->textField($model,'mobile',array(
     'class'=>"form-control form-control-text mask_mobile",
     'placeholder'=>$form->label($model,'mobile')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'mobile'); ?>
   <?php echo $form->error($model,'mobile'); ?>
</div>

<h6 class="mb-4"><?php echo t("Customer Instructions")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'booking_notes',array(
     'class'=>"form-control form-control-text",          
   )); ?>      
   <?php echo $form->error($model,'booking_notes'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array)$status_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'status'),
   )); ?>         
   <?php echo $form->error($model,'status'); ?>
</div>		   

  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>