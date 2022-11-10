
  
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


<h6 class="mb-4"><?php echo t("Facebook Pixel Setting")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_fb_pixel",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_fb_pixel",
     'checked'=>$model->enabled_fb_pixel==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_fb_pixel">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'fb_pixel_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'fb_pixel_id'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'fb_pixel_id'); ?>
   <?php echo $form->error($model,'fb_pixel_id'); ?>
</div>


<h6 class="mb-4"><?php echo t("Google Analytics Setting")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_google_analytics",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_google_analytics",
     'checked'=>$model->enabled_google_analytics==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_google_analytics">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'google_analytics_tracking_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'google_analytics_tracking_id'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'google_analytics_tracking_id'); ?>
   <?php echo $form->error($model,'google_analytics_tracking_id'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>