<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'frm-merchant',
	'enableAjaxValidation'=>false,
)); ?>

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


<h6 class="mb-4 mt-4"><?php echo t("Zone")?></h6>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'zone', (array)$zone_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'zone'),
   )); ?>         
   <?php echo $form->error($model,'zone'); ?>
</div>


<div class="row text-left mt-4">
<div class="col-md-12 m-0">
<?php echo CHtml::submitButton('save',array(
'class'=>"btn btn-green btn-full",
'value'=>CommonUtility::t("Save")
)); ?>
</div>
</div>

<?php $this->endWidget(); ?>
 