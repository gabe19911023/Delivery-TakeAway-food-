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

<div class="form-label-group">    
   <?php echo $form->textField($model,'supplier_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'supplier_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'supplier_name'); ?>
   <?php echo $form->error($model,'supplier_name'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'contact_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'contact_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'contact_name'); ?>
   <?php echo $form->error($model,'contact_name'); ?>
</div>

<div class="row">
  <div class="col-md-6">
    
<div class="form-label-group">    
   <?php echo $form->textField($model,'email',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'email')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'email'); ?>
   <?php echo $form->error($model,'email'); ?>
</div>
  
  </div> <!--col-->
  
  <div class="col-md-6">
    
<div class="form-label-group">    
   <?php echo $form->textField($model,'phone_number',array(
     'class'=>"form-control form-control-text mask_phone",
     'placeholder'=>$form->label($model,'phone_number')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'phone_number'); ?>
   <?php echo $form->error($model,'phone_number'); ?>
</div>
  
  </div> <!--col-->
    
</div> <!--row-->


<div class="form-label-group">    
   <?php echo $form->textField($model,'address_1',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'address_1')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'address_1'); ?>
   <?php echo $form->error($model,'address_1'); ?>
</div>



<div class="form-label-group">    
   <?php echo $form->textField($model,'address_2',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'address_2')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'address_2'); ?>
   <?php echo $form->error($model,'address_2'); ?>
</div>



<div class="row">
  <div class="col-md-6">
    
<div class="form-label-group">    
   <?php echo $form->textField($model,'city',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'city')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'city'); ?>
   <?php echo $form->error($model,'city'); ?>
</div>
  
  </div> <!--col-->
  
  <div class="col-md-6">
    
<div class="form-label-group">    
   <?php echo $form->textField($model,'postal_code',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'postal_code')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'postal_code'); ?>
   <?php echo $form->error($model,'postal_code'); ?>
</div>
  
  </div> <!--col-->
    
</div> <!--row-->


<div class="form-label-group">    
   <?php echo $form->textField($model,'region',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'region')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'region'); ?>
   <?php echo $form->error($model,'region'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'country_code', (array)$country_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'country_code'),
   )); ?>         
   <?php echo $form->error($model,'country_code'); ?>
</div>		

<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'notes',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Notes")
   )); ?>      
   <?php echo $form->error($model,'notes'); ?>
</div>
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>