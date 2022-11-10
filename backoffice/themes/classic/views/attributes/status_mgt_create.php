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

<h6 class="mb-4 mt-4"><?php echo t("Group Name")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'group_name', (array)$group,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'group_name'),
   )); ?>         
   <?php echo $form->error($model,'group_name'); ?>
</div>		   

<div class="form-label-group">    
   <?php echo $form->textField($model,'status',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'status'),
     'readonly'=>$model->isNewRecord?false:true     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'status'); ?>
   <?php echo $form->error($model,'status'); ?>
   <small class="form-text text-muted mb-2">
    <?php echo t("This fields must not have spaces")?>
  </small>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'title',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'title')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'title'); ?>
   <?php echo $form->error($model,'title'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Background Color Hex")?></h6>
<div class="form-label-group">    
   <?php echo $form->textField($model,'color_hex',array(
     'class'=>"form-control form-control-text colorpicker",
     'placeholder'=>$form->label($model,'color_hex'),     
   )); ?>      
   <?php echo $form->error($model,'color_hex'); ?>
</div>


<h6 class="mb-4 mt-4"><?php echo t("Font Color Hex")?></h6>
<div class="form-label-group">    
   <?php echo $form->textField($model,'font_color_hex',array(
     'class'=>"form-control form-control-text colorpicker",
     'placeholder'=>$form->label($model,'font_color_hex'),     
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