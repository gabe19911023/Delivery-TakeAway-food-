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


<h6 class="mb-2"><?php echo t("Action Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'action_type', (array) $action_type_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'action_type'),     
   )); ?>         
   <?php echo $form->error($model,'action_type'); ?>
</div>

<h6 class="mb-2"><?php echo t("Template")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'action_value', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'action_value'),     
   )); ?>         
   <?php echo $form->error($model,'action_value'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>