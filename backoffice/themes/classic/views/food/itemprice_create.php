<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>isset($sub_link)?$sub_link:'',
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

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"available",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"available",
     'checked'=>$model->available==1?true:false
   )); ?>   
  <label class="custom-control-label" for="available">
   <?php echo t("Available")?>
  </label>
</div>    

<div class="d-flex">

<div class="form-label-group w-50 mr-3">    
   <?php echo $form->textField($model,'price',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'price')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'price'); ?>
   <?php echo $form->error($model,'price'); ?>
</div>
<div class="form-label-group w-50">    
   <?php echo $form->dropDownList($model,'size_id', (array) $units,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'size_id'),
   )); ?>         
   <?php echo $form->error($model,'size_id'); ?>
</div>

</div> <!--flex-->

<div class="d-flex">
	<div class="form-label-group w-50 mr-3">    
	   <?php echo $form->textField($model,'cost_price',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'cost_price')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'cost_price'); ?>
	   <?php echo $form->error($model,'cost_price'); ?>
	</div>
	
		
	<div class="form-label-group w-50">    
	   <?php echo $form->textField($model,'discount',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'discount')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'discount'); ?>
	   <?php echo $form->error($model,'discount'); ?>
	</div>

</div> <!--flex-->

<div class="d-flex">
<div class="form-label-group w-50 mr-3">    
   <?php echo $form->textField($model,'discount_start',array(
     'class'=>"form-control form-control-text datepick",
     'readonly'=>true,
     'placeholder'=>$form->label($model,'discount_start'),          
   )); ?>   
   <?php    
    echo $form->labelEx($model,'discount_start'); ?>
   <?php echo $form->error($model,'discount_start'); ?>
</div>

<div class="form-label-group w-50 mr-3">    
   <?php echo $form->textField($model,'discount_end',array(
     'class'=>"form-control form-control-text datepick",
     'readonly'=>true,
     'placeholder'=>$form->label($model,'discount_end'),          
   )); ?>   
   <?php    
    echo $form->labelEx($model,'discount_end'); ?>
   <?php echo $form->error($model,'discount_end'); ?>
</div>
</div> <!--flex-->

<h6 class="mb-4"><?php echo t("Discount Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'discount_type', (array) $discount_type,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'discount_type'),
   )); ?>         
   <?php echo $form->error($model,'discount_type'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'sku',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'sku')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'sku'); ?>
   <?php echo $form->error($model,'sku'); ?>
</div>


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>