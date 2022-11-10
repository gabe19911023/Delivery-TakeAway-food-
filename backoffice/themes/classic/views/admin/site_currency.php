
  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
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


<h6 class="mb-4 mt-4"><?php echo t("Default Currency")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'admin_currency_set', (array)$currency,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'admin_currency_set'),
   )); ?>         
   <?php echo $form->error($model,'admin_currency_set'); ?>
</div>		   

<h6 class="mb-4 mt-4"><?php echo t("Position")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'admin_currency_position', (array)$currency_position,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'admin_currency_position'),
   )); ?>         
   <?php echo $form->error($model,'admin_currency_position'); ?>
</div>		  

<div class="form-label-group">    
   <?php echo $form->numberField($model,'admin_decimal_place',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'admin_decimal_place'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'admin_decimal_place'); ?>
   <?php echo $form->error($model,'admin_decimal_place'); ?>
</div>

<div class="row mt-3">
 <div class="col-md-6">
 
<div class="form-label-group">    
   <?php echo $form->textField($model,'admin_decimal_separator',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'admin_decimal_separator'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'admin_decimal_separator'); ?>
   <?php echo $form->error($model,'admin_decimal_separator'); ?>
</div>
 
 </div>
 <div class="col-md-6">

 <div class="form-label-group">    
   <?php echo $form->textField($model,'admin_thousand_separator',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'admin_thousand_separator'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'admin_thousand_separator'); ?>
   <?php echo $form->error($model,'admin_thousand_separator'); ?>
</div>
 
 </div>
</div>
<!--row-->


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>