<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
		'enableAjaxValidation' => false,	
	)
);
?>


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

<?php if(is_array($payment_list) && count($payment_list)>=1):?>
<?php foreach ($payment_list as $items):?>
	
	<h5 class="m-0"><?php echo t($items['payment_name'])?></h5>
	<p class="m-0 mb-1">
	 <?php 	 
	 $payment_code = $items['payment_code']; $payment_label = '';
	 switch ($payment_code) {
	 	case "paypal":	 		
	 	    echo t("Plan ID");
	 	    $payment_label = t("P-**************");
	 		break;	 
	 	case "stripe":	 	
	 	    echo t("Price IDs");
	 	    $payment_label = t("price_**************");
	 		break;
	 	case "razorpay":	 		
	 	    echo t("Plan ID");
	 	    $payment_label = t("plan_**************");
	 		break;	 	
	 }
	 $payment_ref = "plan_price_$payment_code";	 
	 ?>
	</p>
	<div class="form-label-group">    
	   <?php echo $form->textField($model,"payment_plan_id[".$payment_code."]",array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>"",	   
	     'value'=>isset($new_data[$payment_ref])?$new_data[$payment_ref]:''
	   )); ?>   	   
	   <label for="AR_admin_meta_payment_plan_id_<?php echo $payment_code;?>"> 
	    <?php echo $payment_label?>
	   </label>
	   <?php echo $form->error($model,'payment_plan_id'); ?>
	</div>

<?php endforeach;?>
<?php else :?>
<p><?php echo t("No payment yet available")?></p>
<?php endif;?>


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>