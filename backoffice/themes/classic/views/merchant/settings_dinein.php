<!--<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links,
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>-->
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'forms',
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


<?php if($merchant_type==1 || $merchant_type==3):?>
<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_service_fee',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_service_fee'),     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_service_fee',array(
     'label'=>t("Service fee")
    )); ?>
   <?php echo $form->error($model,'merchant_service_fee'); ?>
</div>
<?php endif;?>

<div class="form-label-group">    
   <?php echo $form->textField($model,'estimation',array(
     'class'=>"form-control form-control-text estimation",
     'placeholder'=>$form->label($model,'estimation')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'estimation',array(
     'label'=>t("Dinein estimation")
    )); ?>
   <?php echo $form->error($model,'estimation'); ?>
   <small><?php echo t("in minutes example 10-20mins")?></small>
</div>


<div class="row">
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'minimum_order',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'minimum_order')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'minimum_order'); ?>
   <?php echo $form->error($model,'minimum_order'); ?>
</div>
  
  </div> <!--col-->
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'maximum_order',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'maximum_order')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'maximum_order'); ?>
   <?php echo $form->error($model,'maximum_order'); ?>
</div>
  
  </div>
</div> <!--col-->
</div> <!--row-->

  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>