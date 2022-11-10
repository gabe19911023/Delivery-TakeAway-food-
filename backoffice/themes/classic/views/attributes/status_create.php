<?php if($model->isNewRecord):?>
<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>array(
    t("All Status")=>array('attributes/order_status'),        
    $this->pageTitle,
),
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>
<?php endif;?>

  
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


<div class="form-label-group">    
   <?php echo $form->textField($model,'description',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'description'),
     'readonly'=>$model->isNewRecord?false:true
   )); ?>   
   <?php    
    echo $form->labelEx($model,'description'); ?>
   <?php echo $form->error($model,'description'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Background Color Hex")?></h6>
<div class="form-label-group">    
   <?php echo $form->textField($model,'background_color_hex',array(
     'class'=>"form-control form-control-text colorpicker",
     'placeholder'=>$form->label($model,'background_color_hex'),
     'readonly'=>false
   )); ?>      
   <?php echo $form->error($model,'background_color_hex'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Font Color Hex")?></h6>
<div class="form-label-group">    
   <?php echo $form->textField($model,'font_color_hex',array(
     'class'=>"form-control form-control-text colorpicker",
     'placeholder'=>$form->label($model,'font_color_hex'),
     'readonly'=>false
   )); ?>      
   <?php echo $form->error($model,'font_color_hex'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->


<!--TRANSLATION-->
<?php if($multi_language && is_array($language) && count($language)>=1 ):?>
<?php 
$this->widget('application.components.WidgetTranslation',array(
  'form'=>$form,
  'model'=>$model,
  'language'=>$language,
  'field'=>$fields,
  'data'=>$data
));
?>   
<?php endif;?>
<!--END TRANSLATION-->	


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>