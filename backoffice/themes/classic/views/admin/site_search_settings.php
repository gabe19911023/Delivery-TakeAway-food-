
  
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


<h6 class="mb-4 mt-4"><?php echo t("Search Mode")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'home_search_mode', (array)$search_type,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'home_search_mode'),
   )); ?>         
   <?php echo $form->error($model,'home_search_mode'); ?>
</div>		   

<hr/>

<h6 class="mb-4"><?php echo t("Settings for Address")?></h6>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"search_enabled_select_from_map",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"search_enabled_select_from_map",
     'checked'=>$model->search_enabled_select_from_map==1?true:false
   )); ?>   
  <label class="custom-control-label" for="search_enabled_select_from_map">
   <?php echo t("Enabled choose address from map")?>
  </label>
</div>    

<!--<h6 class="mb-4 mt-3"><?php echo t("Default Country")?></h6>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'search_default_country', (array)$country_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'search_default_country'),
   )); ?>         
   <?php echo $form->error($model,'search_default_country'); ?>
</div>		-->
   
<h6 class="mb-4 mt-4"><?php echo t("Set Specific Country")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_specific_country',(array)$country_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'merchant_specific_country'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'merchant_specific_country'); ?>
</div>
<small class="form-text text-muted mb-2">
  <?php echo t("leave empty to show all country")?>
</small>
   
   

<hr/>

<h6 class="mb-4 mt-4"><?php echo t("Settings for define locations")?></h6>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'location_searchtype', (array)$location_search,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'location_searchtype'),
   )); ?>         
   <?php echo $form->error($model,'location_searchtype'); ?>
</div>		   


<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'location_default_country', (array)$country_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'location_default_country'),
   )); ?>         
   <?php echo $form->error($model,'location_default_country'); ?>
</div>		   

<hr/>

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>