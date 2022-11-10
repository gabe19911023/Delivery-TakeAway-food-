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
  

<h6 class="mb-4"><?php echo t("Customer reviews")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'review',array(
     'class'=>"form-control form-control-text",          
   )); ?>      
   <?php echo $form->error($model,'review'); ?>
</div>

<div class="form-label-group">    
	   <?php echo $form->textField($model,'rating',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'rating')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'rating'); ?>
	   <?php echo $form->error($model,'rating'); ?>
	</div>

<h6 class="mb-4 mt-4"><?php echo t("Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array)$status,array(
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