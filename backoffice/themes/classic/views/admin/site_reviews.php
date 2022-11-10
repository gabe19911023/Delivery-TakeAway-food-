
  
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


<h6 class="mb-4"><?php echo t("Merchant can edit/delete review")?></h6>
<div class="custom-control custom-switch custom-switch-md mb-3">  
  <?php echo $form->checkBox($model,"merchant_can_edit_reviews",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_can_edit_reviews",
     'checked'=>$model->merchant_can_edit_reviews==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_can_edit_reviews">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'review_image_resize_width',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'review_image_resize_width'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'review_image_resize_width'); ?>
   <?php echo $form->error($model,'review_image_resize_width'); ?>
   <small><?php echo t("upload review image will resize to set width, if below set width no resizing will happen.")?></small>
</div>

<h5 class="card-title mb-0"><?php echo t("Template review")?></h5>  
<div class="mb-3">
<small><?php echo t("Send email reminder to customer to review there order.")?></small>
</div>

<div class="custom-control custom-switch custom-switch-md mb-3">  
  <?php echo $form->checkBox($model,"review_template_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"review_template_enabled",
     'checked'=>$model->review_template_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="review_template_enabled">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'review_template_id', (array) $template_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'review_template_id'),
   )); ?>         
   <?php echo $form->error($model,'review_template_id'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'review_send_after',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'review_send_after'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'review_send_after'); ?>
   <?php echo $form->error($model,'review_send_after'); ?>
</div>

 <!--
   <hr/> 

<h6 class="mb-0"><?php echo t("Review Status")?></h6>
<small class="form-text text-muted mb-4">
  <?php echo t("customer can review the order based on this order status")?>
</small>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'review_baseon_status',$status_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'review_baseon_status'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'review_baseon_status'); ?>
</div>

<h6 class="mb-0"><?php echo t("Earning Points review status")?></h6>
<small class="form-text text-muted mb-4">
  <?php echo t("customer will earn points based on this status")?>
</small>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'earn_points_review_status',$status_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'earn_points_review_status'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'earn_points_review_status'); ?>
</div>-->


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>