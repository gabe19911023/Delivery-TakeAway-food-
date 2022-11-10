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
 
<small class="form-text text-muted mb-2">  
  <?php echo t("Define how many minutes that order set to critical order and needs attentions.")?>
</small>   
<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_order_critical_mins',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_order_critical_mins')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_order_critical_mins'); ?>
   <?php echo $form->error($model,'merchant_order_critical_mins'); ?>
</div>


<small class="form-text text-muted mb-2">  
  <?php echo t("Define how many minutes that order will auto rejected.")?>  
</small>   
<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_order_reject_mins',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_order_reject_mins')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_order_reject_mins'); ?>
   <?php echo $form->error($model,'merchant_order_reject_mins'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>  