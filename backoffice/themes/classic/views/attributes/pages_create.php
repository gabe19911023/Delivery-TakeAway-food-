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

<div class="form-label-group">    
   <?php echo $form->textField($model,'title',array(
     'class'=>"form-control form-control-text copy_text_to",
     'placeholder'=>$form->label($model,'title'),
     'data-id'=>".slug"
   )); ?>   
   <?php    
    echo $form->labelEx($model,'title'); ?>
   <?php echo $form->error($model,'title'); ?>
</div>

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon3"><?php echo CommonUtility::getHomebaseUrl()?>/</span>
  </div>
  <?php echo $form->textField($model,'slug',array(
     'class'=>"form-control form-control-text slug",
     'placeholder'=>t("Slug")
   )); ?>      
</div>
<?php echo $form->error($model,'slug'); ?>

<h6 class="mb-4"><?php echo t("Content")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'long_content',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("Content")
   )); ?>      
   <?php echo $form->error($model,'long_content'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Short Description")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'short_content',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Short Description")
   )); ?>      
   <?php echo $form->error($model,'short_content'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array)$status_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'status'),
   )); ?>         
   <?php echo $form->error($model,'status'); ?>
</div>		

  </div> <!--body-->
</div> <!--card-->

<?php $this->renderPartial("/attributes/pages_seo",array(
 'model'=>$model,
 'form'=>$form,
 'upload_path'=>$upload_path,
));?>

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