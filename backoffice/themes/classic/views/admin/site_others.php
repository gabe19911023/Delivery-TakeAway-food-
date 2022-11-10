
  
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

<h6 class="mb-4"><?php echo t("Others")?></h6>


<h6 class="mb-3"><?php echo t("Forgot Backend Password Template")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'backend_forgot_password_tpl', (array)$template_list ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'backend_forgot_password_tpl'),
   )); ?>         
   <?php echo $form->error($model,'backend_forgot_password_tpl'); ?>
</div>

<h6 class="mb-4"><?php echo t("Allow return to home")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"allow_return_home",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"allow_return_home",
     'checked'=>$model->allow_return_home==1?true:false
   )); ?>   
  <label class="custom-control-label" for="allow_return_home">
   <?php echo t("Enabled")?>
  </label>
</div>    

<h6 class="mb-4 mt-4"><?php echo t("Image resizing")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"image_resizing",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"image_resizing",
     'checked'=>$model->image_resizing==1?true:false
   )); ?>   
  <label class="custom-control-label" for="image_resizing">
   <?php echo t("Enabled")?>
  </label>
</div>    


<h6 class="mb-3 mt-3"><?php echo t("Runactions")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'runactions_method', (array)$runactions_method ,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'runactions_method'),
   )); ?>         
   <?php echo $form->error($model,'runactions_method'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>