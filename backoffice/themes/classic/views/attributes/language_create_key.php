<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links,
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
	
	<?php for ($x = 0; $x <= 5; $x++) :?>
	<div class="form-label-group">  
	 <?php 
	 echo CHtml::textField("message[$x]",'',array(
	   'class'=>"form-control form-control-text",	   
	 ));
	 ?>	
	 <label for="message_<?php echo $x?>" class="required"><?php echo t("Key")?></label> 
	</div>
	<?php endfor;?>

  </div> <!--body-->
</div> <!--card-->

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green w-100 mt-3",
'value'=>t("Save")
)); ?>  

<?php $this->endWidget(); ?>