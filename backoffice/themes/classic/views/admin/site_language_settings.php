
  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
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


<h6 class="mb-2"><?php echo t("Language Settings")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_multiple_translation_new",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_multiple_translation_new",
     'checked'=>$model->enabled_multiple_translation_new==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_multiple_translation_new">
   <?php echo t("Enabled Multiple Field Translation")?>
  </label>
</div>    

<h6 class="mb-3 mt-3"><?php echo t("Enabled Language Selection")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_language_admin",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_language_admin",
     'checked'=>$model->enabled_language_admin==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_language_admin">
   <?php echo t("Enabled in Admin Panel")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_language_merchant",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_language_merchant",
     'checked'=>$model->enabled_language_merchant==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_language_merchant">
   <?php echo t("Enabled in Merchant Panel")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_language_front",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_language_front",
     'checked'=>$model->enabled_language_front==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_language_front">
   <?php echo t("Enabled in Front End")?>
  </label>
</div>    

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>