<?php if($model->isNewRecord):?>
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
<?php endif;?>
  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'form',
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
   <?php echo $form->textField($model,'meta_title',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'meta_title')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'meta_title'); ?>
   <?php echo $form->error($model,'meta_title'); ?>
</div>  

<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'meta_description',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Meta description")
   )); ?>      
   <?php echo $form->error($model,'meta_description'); ?>
</div>


<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'meta_keywords',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Keywords")
   )); ?>      
   <?php echo $form->error($model,'meta_keywords'); ?>
</div>

<div id="vue-uploader">
<component-uploader
ref="uploader"
max_file="<?php echo Yii::app()->params->dropzone['max_file'];?>"
max_file_size = "<?php echo Yii::app()->params->dropzone['max_file_size']?>"
select_type="single"
field = "meta_image"
field_path = "path"
inline="false"
selected_file="<?php echo $model->meta_image;?>"
upload_path="<?php echo $upload_path?>"
save_path="<?php echo $model->meta_image_path?>"

@set-afer-upload="afterUpload"
@set-afer-delete="afterDelete"
:label="{
    select_file:'<?php echo CJavaScript::quote(t("Select File"))?>',       
    upload_new:'<?php echo CJavaScript::quote(t("Upload New"))?>',     
    upload_button:'<?php echo CJavaScript::quote(t("Featured Image"))?>',     
    add_file:'<?php echo CJavaScript::quote(t("Add Files"))?>',
    previous:'<?php echo CJavaScript::quote(t("Previous"))?>',
    next:'<?php echo CJavaScript::quote(t("Next"))?>',
    search:'<?php echo CJavaScript::quote(t("Search"))?>', 
    delete_file:'<?php echo CJavaScript::quote(t("Delete File"))?>',   
    drop_files:'<?php echo CJavaScript::quote(t("Drop files anywhere to upload"))?>',   
    or:'<?php echo CJavaScript::quote(t("or"))?>',   
    select_files:'<?php echo CJavaScript::quote(t("Select Files"))?>',      
}"
>
</component-uploader>
</div>


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>

<?php $this->renderPartial("/admin/modal_delete_image");?>