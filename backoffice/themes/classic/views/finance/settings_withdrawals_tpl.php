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

<h5 class="card-title"><?php echo t("Template new payout request - for admin")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'payout_new_payout_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'payout_new_payout_template_id'),
   )); ?>         
   <?php echo $form->error($model,'payout_new_payout_template_id'); ?>
</div>

<h5 class="card-title"><?php echo t("Template Payout paid")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'payout_paid_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'payout_paid_template_id'),
   )); ?>         
   <?php echo $form->error($model,'payout_paid_template_id'); ?>
</div>

<h5 class="card-title"><?php echo t("Template Payout Cancel")?></h5>  
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'payout_cancel_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'payout_cancel_template_id'),
   )); ?>         
   <?php echo $form->error($model,'payout_cancel_template_id'); ?>
</div>

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full",
'value'=>t("Save")
)); ?>

      
<?php $this->endWidget(); ?>
