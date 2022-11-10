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
      
<h5 class="card-title"><?php echo t("Status for new order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status_new_order', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status_new_order'),
   )); ?>         
   <?php echo $form->error($model,'status_new_order'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for delivered order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status_delivered', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status_delivered'),
   )); ?>         
   <?php echo $form->error($model,'status_delivered'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for completed pickup/dinein order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status_completed', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status_completed'),
   )); ?>         
   <?php echo $form->error($model,'status_completed'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for cancel order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status_cancel_order', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status_cancel_order'),
   )); ?>         
   <?php echo $form->error($model,'status_cancel_order'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for order rejection")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status_rejection', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status_rejection'),
   )); ?>         
   <?php echo $form->error($model,'status_rejection'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for delivery failed")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status_delivery_fail', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status_delivery_fail'),
   )); ?>         
   <?php echo $form->error($model,'status_delivery_fail'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for failed pickup/dinein order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status_failed', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status_failed'),
   )); ?>         
   <?php echo $form->error($model,'status_failed'); ?>
</div>


		
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full",
'value'=>t("Save")
)); ?>
      
<?php $this->endWidget(); ?>
