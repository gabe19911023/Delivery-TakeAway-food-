
  
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

<h6 class="mb-4"><?php echo t("Menu Options")?></h6>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"admin_menu_allowed_merchant",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"admin_menu_allowed_merchant",
     'checked'=>$model->admin_menu_allowed_merchant==1?true:false
   )); ?>   
  <label class="custom-control-label" for="admin_menu_allowed_merchant">
   <?php echo t("Allow merchant to change there own menu")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"admin_menu_lazyload",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"admin_menu_lazyload",
     'checked'=>$model->admin_menu_lazyload==1?true:false
   )); ?>   
  <label class="custom-control-label" for="admin_menu_lazyload">
   <?php echo t("Enabled lazyload")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"mobile2_hide_empty_category",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"mobile2_hide_empty_category",
     'checked'=>$model->mobile2_hide_empty_category==1?true:false
   )); ?>   
  <label class="custom-control-label" for="mobile2_hide_empty_category">
   <?php echo t("Hide empty category")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_food_search_menu",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_food_search_menu",
     'checked'=>$model->enabled_food_search_menu==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_food_search_menu">
   <?php echo t("Enabled Food name Search")?>
  </label>
</div>    

<h6 class="mb-4 mt-4"><?php echo t("Menu Style")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'admin_activated_menu', (array)$menu_style,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'admin_activated_menu'),
   )); ?>         
   <?php echo $form->error($model,'admin_activated_menu'); ?>
</div>		   


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>