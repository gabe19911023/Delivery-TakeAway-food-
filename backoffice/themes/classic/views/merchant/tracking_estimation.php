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

<h6 class="mb-3"><?php echo t("Tracking initial estimation")?></h6>  

<div class="d-flex">

<div class="form-label-group mr-3">    
   <?php echo $form->textField($model,'tracking_estimation_delivery1',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'tracking_estimation_delivery1')     
   )); ?>   
   <?php    
    echo $form->label($model,'tracking_estimation_delivery1'); ?>
   <?php echo $form->error($model,'tracking_estimation_delivery1'); ?>      
   <small class="form-text text-muted mb-2">
	  <?php echo t("in minutes")?>
	</small>
</div>  

<div class="form-label-group">    
   <?php echo $form->textField($model,'tracking_estimation_delivery2',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'tracking_estimation_delivery2')     
   )); ?>   
   <?php    
    echo $form->label($model,'tracking_estimation_delivery2'); ?>
   <?php echo $form->error($model,'tracking_estimation_delivery2'); ?>      
</div>  

</div> <!--flex-->



  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>  