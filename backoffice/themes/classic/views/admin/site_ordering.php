
  
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


<h6 class="mb-4"><?php echo t("Enabled Ordering")?></h6>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_website_ordering",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_website_ordering",
     'checked'=>$model->enabled_website_ordering==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_website_ordering">
   <?php echo t("Enabled")?>
  </label>
</div>    

<h6 class="mb-4 mt-3"><?php echo t("Cannot do order again if previous order status is")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'restrict_order_by_status',$status_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'restrict_order_by_status'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'restrict_order_by_status'); ?>
</div>

<h6 class="mb-4 mt-3"><?php echo t("Order Cancellation")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"cancel_order_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"cancel_order_enabled",
     'checked'=>$model->cancel_order_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="cancel_order_enabled">
   <?php echo t("Enabled cancellation of order")?>
  </label>
</div>    


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>