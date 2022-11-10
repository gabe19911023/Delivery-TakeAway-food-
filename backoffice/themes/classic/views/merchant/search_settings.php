
  
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


<h6 class="mt-4"><?php echo t("Set Specific Country")?></h6>
<p class="text-muted mb-4"><?php echo t("Notice : this section need to be fill only if you have single website restaurant")?>.</p>	

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_set_default_country', (array)$country_list,array(
     'class'=>"form-control custom-select form-control-select select_two",     
     'placeholder'=>$form->label($model,'merchant_set_default_country'),
   )); ?>         
   <?php echo $form->error($model,'merchant_set_default_country'); ?>
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