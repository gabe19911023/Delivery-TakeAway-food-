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


<h6 class="mb-4"><?php echo t("Featured name")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'featured_name', (array) $featured_name,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'featured_name'),
   )); ?>         
   <?php echo $form->error($model,'featured_name'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'location_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'location_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'location_name'); ?>
   <?php echo $form->error($model,'location_name'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'latitude',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'latitude')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'latitude'); ?>
   <?php echo $form->error($model,'latitude'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'longitude',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'longitude')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'longitude'); ?>
   <?php echo $form->error($model,'longitude'); ?>
</div>

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