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

<h5 class="card-title"><?php echo t("Template Invoice")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'invoice_create_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'invoice_create_template_id'),
   )); ?>         
   <?php echo $form->error($model,'invoice_create_template_id'); ?>
</div>

<h5 class="card-title"><?php echo t("Template Refund")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'refund_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'refund_template_id'),
   )); ?>         
   <?php echo $form->error($model,'refund_template_id'); ?>
</div>

<h5 class="card-title"><?php echo t("Template Partial Refund")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'partial_refund_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'partial_refund_template_id'),
   )); ?>         
   <?php echo $form->error($model,'partial_refund_template_id'); ?>
</div>

<h5 class="card-title"><?php echo t("Delay Order")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'delayed_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'delayed_template_id'),
   )); ?>         
   <?php echo $form->error($model,'delayed_template_id'); ?>
</div>

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full",
'value'=>t("Save")
)); ?>

      
<?php $this->endWidget(); ?>
