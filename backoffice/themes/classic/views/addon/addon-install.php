<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs',$links);
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

<h6 class="mb-4"><?php echo t("Import language file")?></h6>

<div class="form-group">
<?php 
echo $form->fileField($model, 'file',array(
    'class'=>"form-control-file"
));
echo $form->error($model, 'file');
?>
</div>

</div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Install/Update")
)); ?>

<?php $this->endWidget(); ?>