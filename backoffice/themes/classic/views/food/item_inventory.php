<?php if($model->isNewRecord):?>
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
<?php endif;?>
  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'form',
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
  <?php echo $form->checkBox($model,"track_stock",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"track_stock",
     'checked'=>$model->track_stock==1?true:false
   )); ?>   
  <label class="custom-control-label" for="track_stock">
   <?php echo t("Track Stock")?>
  </label>
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

<h6 class="mb-3 mt-4"><?php echo t("Supplier")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'supplier_id', (array) $supplier,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'supplier_id'),
   )); ?>         
   <?php echo $form->error($model,'supplier_id'); ?>
</div>


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


<?php $this->endWidget(); ?>