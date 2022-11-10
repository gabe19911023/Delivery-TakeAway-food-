<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>array(
    t("All Coupon")=>array('promo/coupon'),        
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


<div class="form-label-group">    
   <?php echo $form->textField($model,'voucher_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'voucher_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'voucher_name'); ?>
   <?php echo $form->error($model,'voucher_name'); ?>
</div>


<h6 class="mb-4 mt-4"><?php echo t("Coupon Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'voucher_type', (array)$voucher_type,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'voucher_type'),
   )); ?>         
   <?php echo $form->error($model,'voucher_type'); ?>
</div>		   

<div class="row mt-4">
<div class="col-md-6">

<div class="form-label-group">    
   <?php echo $form->textField($model,'amount',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'amount')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'amount'); ?>
   <?php echo $form->error($model,'amount'); ?>
</div>

</div>
<div class="col-md-6">

<div class="form-label-group">    
   <?php echo $form->textField($model,'min_order',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'min_order')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'min_order'); ?>
   <?php echo $form->error($model,'min_order'); ?>
</div>

</div>
</div>
<!--row-->

<h6 class="mb-4"><?php echo t("Days Available")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'days_available',$days,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'days_available'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'days_available'); ?>
</div>

<h6 class="mb-4"><?php echo t("Applicable to merchant")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'apply_to_merchant',(array)$selected_merchant,array(
     'class'=>"form-control custom-select form-control-select select_two_ajax",
     'placeholder'=>$form->label($model,'apply_to_merchant'),
     'multiple'=>true,
     'action'=>'search_merchant'
   )); ?>         
   <?php echo $form->error($model,'apply_to_merchant'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'expiration',array(
     'class'=>"form-control form-control-text datepick",
     'readonly'=>true,
     'placeholder'=>$form->label($model,'expiration'),     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'expiration'); ?>
   <?php echo $form->error($model,'expiration'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Coupon Options")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'used_once', (array)$coupon_options,array(
     'class'=>"form-control custom-select form-control-select coupon_options",     
     'placeholder'=>$form->label($model,'used_once'),
   )); ?>         
   <?php echo $form->error($model,'used_once'); ?>
</div>		  

<DIV class="coupon_max_number_use">
<div class="form-label-group">    
   <?php echo $form->textField($model,'max_number_use',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'max_number_use')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'max_number_use'); ?>
   <?php echo $form->error($model,'max_number_use'); ?>
</div>
</DIV>

<DIV class="coupon_customer">
<h6 class="mb-4"><?php echo t("Select Customer")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'apply_to_customer',(array)$selected_customer,array(
     'class'=>"form-control custom-select form-control-select select_two_ajax2",
     'placeholder'=>$form->label($model,'apply_to_customer'),
     'multiple'=>true,
     'action'=>'search_customer'
   )); ?>         
   <?php echo $form->error($model,'apply_to_customer'); ?>
</div>
</DIV>

<h6 class="mb-4 mt-4"><?php echo t("Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array)$status,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'status'),
   )); ?>         
   <?php echo $form->error($model,'status'); ?>
</div>		  

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>