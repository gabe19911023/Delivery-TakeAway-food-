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
  

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"points_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"points_enabled",
     'checked'=>$model->points_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="points_enabled">
   <?php echo t("Enabled Points")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"packaging_incremental",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"packaging_incremental",
     'checked'=>$model->packaging_incremental==1?true:false
   )); ?>   
  <label class="custom-control-label" for="packaging_incremental">
   <?php echo t("Enabled Packaging Incrementals")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"cooking_ref_required",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"cooking_ref_required",
     'checked'=>$model->cooking_ref_required==1?true:false
   )); ?>   
  <label class="custom-control-label" for="cooking_ref_required">
   <?php echo t("Cooking Reference Mandatory")?>
  </label>
</div>    

<div class="form-label-group mt-3">    
   <?php echo $form->textField($model,'points_earned',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'points_earned')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'points_earned'); ?>
   <?php echo $form->error($model,'points_earned'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'packaging_fee',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'packaging_fee')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'packaging_fee'); ?>
   <?php echo $form->error($model,'packaging_fee'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Cooking Reference")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'cooking_selected', (array)$cooking_ref,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'cooking_selected'),
   )); ?>         
   <?php echo $form->error($model,'cooking_selected'); ?>
</div>


<h6 class="mb-4 mt-4"><?php echo t("Ingredients")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'ingredients_selected', (array)$ingredients,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'ingredients_selected'),
   )); ?>         
   <?php echo $form->error($model,'ingredients_selected'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Dish")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'dish_selected', (array)$dish,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'dish_selected'),
   )); ?>         
   <?php echo $form->error($model,'dish_selected'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Delivery options")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'delivery_options_selected', (array)$transport,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'delivery_options_selected'),
   )); ?>         
   <?php echo $form->error($model,'delivery_options_selected'); ?>
   <small class="form-text text-muted mb-2">
	  <?php echo t("Select vehicle type for this item can be used for delivery")?>
	</small>
</div>
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


<?php $this->endWidget(); ?>