
  
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

<h6 class="mb-4"><?php echo t("Admin Do Not Allow User Multiple Sigin")?></h6>
<div class="custom-control custom-switch custom-switch-md mb-4">  
  <?php echo $form->checkBox($model,"website_admin_mutiple_login",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"website_admin_mutiple_login",
     'checked'=>$model->website_admin_mutiple_login==1?true:false
   )); ?>   
  <label class="custom-control-label" for="website_admin_mutiple_login">
   <?php echo t("Enabled")?>
  </label>
</div>    


<h6 class="mb-4"><?php echo t("Merchant Do Not Allow User Multiple Sigin")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"website_merchant_mutiple_login",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"website_merchant_mutiple_login",
     'checked'=>$model->website_merchant_mutiple_login==1?true:false
   )); ?>   
  <label class="custom-control-label" for="website_merchant_mutiple_login">
   <?php echo t("Enabled")?>
  </label>
</div>    


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>