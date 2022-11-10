<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'forms',
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
  <?php echo $form->checkBox($model,"merchant_opt_contact_delivery",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_opt_contact_delivery",
     'checked'=>$model->merchant_opt_contact_delivery==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_opt_contact_delivery">
   <?php echo t("Enabled Opt in for no contact delivery")?>
  </label>
</div>    


<div class="custom-control custom-switch custom-switch-md mb-3">  
  <?php echo $form->checkBox($model,"free_delivery_on_first_order",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"free_delivery_on_first_order",
     'checked'=>$model->free_delivery_on_first_order==1?true:false
   )); ?>   
  <label class="custom-control-label" for="free_delivery_on_first_order">
   <?php echo t("Free Delivery On First Order")?>
  </label>
</div>    


<h6 class="mb-3"><?php echo t("Delivery Charge Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_delivery_charges_type', (array) $charge_type,array(
     'class'=>"form-control custom-select form-control-select merchant_delivery_charges_type",
     'placeholder'=>$form->label($model,'merchant_delivery_charges_type'),
   )); ?>         
   <?php echo $form->error($model,'merchant_delivery_charges_type'); ?>
</div>

<?php if($merchant_type==1 || $merchant_type==3):?>

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_service_fee',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_service_fee'),     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_service_fee'); ?>
   <?php echo $form->error($model,'merchant_service_fee'); ?>
</div>

<?php endif;?>

  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>