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


<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_tax_number',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_tax_number')     
   )); ?>   
   <?php    
    echo $form->label($model,'merchant_tax_number'); ?>
   <?php echo $form->error($model,'merchant_tax_number'); ?>   
   <small class="form-text text-muted mb-2">
	  <?php echo t("This will appear in your receipt")?>
	</small>
</div>


<h6 class="mb-3 mt-3"><?php echo t("Two Flavor Options")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_two_flavor_option', (array) $two_flavor_options,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'merchant_two_flavor_option'),
   )); ?>         
   <?php echo $form->error($model,'merchant_two_flavor_option'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_extenal',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_extenal')     
   )); ?>   
   <?php    
    echo $form->label($model,'merchant_extenal'); ?>
   <?php echo $form->error($model,'merchant_extenal'); ?>      
</div>


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_close_store",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_close_store",
     'checked'=>$model->merchant_close_store==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_close_store">
   <?php echo t("Close Store")?>
  </label>
</div>    

<!--
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_disabled_ordering",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_disabled_ordering",
     'checked'=>$model->merchant_disabled_ordering==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_disabled_ordering">
   <?php echo t("Disabled Ordering")?>
  </label>
</div> -->   

<!--
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_private_menu",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_private_menu",
     'checked'=>$model->enabled_private_menu==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_private_menu">
   <?php echo t("Make menu private")?>
  </label>
</div>    
-->

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_enabled_voucher",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_enabled_voucher",
     'checked'=>$model->merchant_enabled_voucher==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_enabled_voucher">
   <?php echo t("Enabled Voucher")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_enabled_tip",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_enabled_tip",
     'checked'=>$model->merchant_enabled_tip==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_enabled_tip">
   <?php echo t("Enabled Tips")?>
  </label>
</div>    


<h6 class="mb-3 mt-3"><?php echo t("Default Tip")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_default_tip', (array) $tips,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'merchant_default_tip'),
   )); ?>         
   <?php echo $form->error($model,'merchant_default_tip'); ?>
</div>

   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>