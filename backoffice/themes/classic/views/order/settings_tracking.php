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

<!--<h5 class="card-title"><?php echo t("Status for order receive")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_receive', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_receive'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_receive'); ?>
</div>-->

<h5 class="card-title"><?php echo t("Status for order processing")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_process', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_process'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_process'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for food ready")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_ready', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_ready'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_ready'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for in transit")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_in_transit', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_in_transit'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_in_transit'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for delivered")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_delivered', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_delivered'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_delivered'); ?>
</div>


<h5 class="card-title"><?php echo t("Status for delivery failed")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_delivery_failed', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_delivery_failed'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_delivery_failed'); ?>
</div>

<hr>

<h5 class="card-title"><?php echo t("Status for completed pickup/dinein order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_completed', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_completed'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_completed'); ?>
</div>

<h5 class="card-title"><?php echo t("Status for failed pickup/dinein order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tracking_status_failed', (array) $status_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tracking_status_failed'),
   )); ?>         
   <?php echo $form->error($model,'tracking_status_failed'); ?>
</div>
		
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full",
'value'=>t("Save")
)); ?>
      
<?php $this->endWidget(); ?>
