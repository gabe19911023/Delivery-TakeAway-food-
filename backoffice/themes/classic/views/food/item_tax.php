<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'form',
		'enableAjaxValidation' => false,		
	)
);
?>

<div id="vue-availability" class="card">
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



<h6 class="mb-4 mt-4"><?php echo t("Item Tax")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_tax', (array)$tax_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'merchant_tax'),
   )); ?>         
   <?php echo $form->error($model,'merchant_tax'); ?>
</div>


  </div> <!--body-->
</div> <!--card-->

  
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


<?php $this->endWidget(); ?>