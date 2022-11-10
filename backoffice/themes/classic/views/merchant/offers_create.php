<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>isset($links)?$links:array(),
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

<div class="row">
 <div class="col-md-6">  
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'offer_percentage',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'offer_percentage')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'offer_percentage'); ?>
	   <?php echo $form->error($model,'offer_percentage'); ?>
	</div>
 </div> <!--col-->
 
 <div class="col-md-6">  
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'offer_price',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'offer_price')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'offer_price'); ?>
	   <?php echo $form->error($model,'offer_price'); ?>
	</div>
 </div> <!--col-->
 
</div> <!--row-->


<DIV class="row">
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'valid_from',array(
	     'class'=>"form-control form-control-text datepick",
	     'placeholder'=>$form->label($model,'valid_from'),
	     'readonly'=>true, 
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'valid_from'); ?>
	   <?php echo $form->error($model,'valid_from'); ?>
	</div>
 </DIV>
 <DIV class="col-md-6">	 	
 <div class="form-label-group">    
   <?php echo $form->textField($model,'valid_to',array(
     'class'=>"form-control form-control-text datepick",
     'readonly'=>true,
     'placeholder'=>$form->label($model,'valid_to'),          
   )); ?>   
   <?php    
    echo $form->labelEx($model,'valid_to'); ?>
   <?php echo $form->error($model,'valid_to'); ?>
</div>
 </DIV> 
</DIV>
<!--row-->

<h6 class="mb-3"><?php echo t("Applicable to")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'applicable_selected', (array)$services,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'applicable_selected'),
   )); ?>         
   <?php echo $form->error($model,'applicable_selected'); ?>
</div>

<h6 class="mb-3"><?php echo t("Status")?></h6>
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

<?php $this->endWidget(); ?>