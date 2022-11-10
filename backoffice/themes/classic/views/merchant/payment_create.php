<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>array(
    t("All Payment gateway")=>array('merchant/payment_list'),        
    $this->pageTitle,
),
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>

  
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



<?php if ($model->isNewRecord):?>
<h6 class="mb-4"><?php echo t("Payment")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'payment_id', (array) $provider,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'payment_id'),
   )); ?>         
   <?php echo $form->error($model,'payment_id'); ?>
</div>
<?php endif;?>


<?php if(is_array($attr_json) && count($attr_json)>=1):?>
<h4 class="mt-4 mb-3"><?php echo t("Credentials")?> (<?php echo $model->payment_code?>)</h4>

<div class="custom-control custom-switch custom-switch-md mb-2">  
  <?php echo $form->checkBox($model,"is_live",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"is_live",
     'checked'=>$model->is_live==1?true:false
   )); ?>   
  <label class="custom-control-label" for="is_live">
   <?php echo t("Production")?>
  </label>
</div>    

<?php foreach ($attr_json as $key=>$item):?>

<div class="form-label-group">    
   <?php echo $form->textField($model,$key,array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,$key)     
   )); ?>   
   <label for="AR_payment_gateway_merchant_<?php echo $key?>"><?php echo t($item['label'])?></label>
   <?php echo $form->error($model,$key); ?>
</div>

<?php endforeach;?>
<?php endif;?>



<h6 class="mb-4"><?php echo t("Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array) $status,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'status'),
   )); ?>         
   <?php echo $form->error($model,'status'); ?>
</div>

   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>