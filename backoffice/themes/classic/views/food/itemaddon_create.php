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

<div class="d-flex">


<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"require_addon",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"require_addon",
     'checked'=>$model->require_addon==1?true:false
   )); ?>   
  <label class="custom-control-label" for="require_addon">
   <?php echo t("Required")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"pre_selected",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"pre_selected",
     'checked'=>$model->pre_selected==1?true:false
   )); ?>   
  <label class="custom-control-label" for="pre_selected">
   <?php echo t("Pre-selected")?>
  </label>
</div>    

</div> <!--flex-->

<h6 class="mb-3 mt-4"><?php echo t("Select Item Price")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'item_size_id', (array) $size_list ,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'item_size_id'),
   )); ?>         
   <?php echo $form->error($model,'item_size_id'); ?>
</div>

<h6 class="mb-3 mt-4"><?php echo t("Addon Category")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'subcat_id', (array) $addon_caregory_list ,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'subcat_id'),
   )); ?>         
   <?php echo $form->error($model,'subcat_id'); ?>
</div>


<h6 class="mb-3 mt-4"><?php echo t("Select Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'multi_option', (array) $multi_option ,array(
     'class'=>"form-control custom-select form-control-select item_multi_options",
     'placeholder'=>$form->label($model,'multi_option'),
   )); ?>         
   <?php echo $form->error($model,'multi_option'); ?>
</div>


<div class="form-label-group multi_option_value_text">    
   <?php echo $form->textField($model,'multi_option_value_text',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'multi_option_value_text')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'multi_option_value_text'); ?>
   <?php echo $form->error($model,'multi_option_value_text'); ?>
</div>


<div class="multi_option_value_selection">
<h6 class="mb-3 mt-4"><?php echo t("Select Two Flavor Properties")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'multi_option_value_selection', (array) $two_flavor_properties ,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'multi_option_value_selection'),
   )); ?>         
   <?php echo $form->error($model,'multi_option_value_selection'); ?>
</div>
</div>


  </div> <!--body-->
</div> <!--card-->

   
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


<?php $this->endWidget(); ?>

<?php $this->renderPartial("/admin/modal_delete_image");?>