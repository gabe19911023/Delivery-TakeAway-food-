
  
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


<h6 class="mb-4 mt-4"><?php echo t("Phone country list")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'mobilephone_settings_country',(array)$country_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'mobilephone_settings_country'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'mobilephone_settings_country'); ?>
</div>
<small class="form-text text-muted mb-2">
  <?php echo t("define the country selection for mobile phone, empty means will show all.")?>
</small>


<h6 class="mb-4 mt-4"><?php echo t("Default country")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'mobilephone_settings_default_country', (array)$country_list,array(
     'class'=>"form-control custom-select form-control-select select_two",     
     'placeholder'=>$form->label($model,'mobilephone_settings_default_country'),
   )); ?>         
   <?php echo $form->error($model,'mobilephone_settings_default_country'); ?>
</div>		   
<small class="form-text text-muted mb-2">
  <?php echo t("default mobile country")?>
</small>


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>