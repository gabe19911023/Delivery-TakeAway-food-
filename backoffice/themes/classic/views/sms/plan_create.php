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
		'id' => 'active-form',
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
   <?php echo $form->textField($model,'title',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'title')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'title'); ?>
   <?php echo $form->error($model,'title'); ?>
</div>

<h6 class="mb-4"><?php echo t("Description")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'description',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("description")
   )); ?>      
   <?php echo $form->error($model,'description'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'price',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'price')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'price'); ?>
   <?php echo $form->error($model,'price'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'promo_price',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'promo_price')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'promo_price'); ?>
   <?php echo $form->error($model,'promo_price'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'sms_limit',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'sms_limit')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'sms_limit'); ?>
   <?php echo $form->error($model,'sms_limit'); ?>
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