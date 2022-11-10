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
   <?php echo $form->textField($model,'item_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'item_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'item_name'); ?>
   <?php echo $form->error($model,'item_name'); ?>
</div>


<h6 class="mb-2 mt-2"><?php echo t("Short Description")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'item_short_description',array(
     'class'=>"form-control form-control-text short_description",     
   )); ?>      
   <?php echo $form->error($model,'item_short_description'); ?>
</div>

<h6 class="mb-2 mt-2"><?php echo t("Long Description")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'item_description',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("Description")
   )); ?>      
   <?php echo $form->error($model,'item_description'); ?>
</div>


<?php if($model->isNewRecord):?>
<div class="d-flex">

<div class="form-label-group w-50 mr-3">    
   <?php echo $form->textField($model,'item_price',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'item_price')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'item_price'); ?>
   <?php echo $form->error($model,'item_price'); ?>
</div>

<div class="form-label-group w-50">    
   <?php echo $form->dropDownList($model,'item_unit', (array) $units,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'item_unit'),
   )); ?>         
   <?php echo $form->error($model,'item_unit'); ?>
</div>


</div> <!--flex-->
<?php endif;?>


<h6 class="mb-4 mt-4"><?php echo t("Category")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'category_selected', (array)$category,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'category_selected'),
   )); ?>         
   <?php echo $form->error($model,'category_selected'); ?>
</div>

<div id="vue-uploader">
<component-uploader
ref="uploader"
max_file="<?php echo Yii::app()->params->dropzone['max_file'];?>"
max_file_size = "<?php echo Yii::app()->params->dropzone['max_file_size']?>"
select_type="single"
field = "photo"
field_path = "path"
inline="false"
selected_file="<?php echo $model->photo;?>"
upload_path="<?php echo $upload_path?>"
save_path="<?php echo $model->path?>"

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

<h6 class="mb-4 mt-4"><?php echo t("Featured")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'item_featured', (array)$item_featured,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'item_featured'),
   )); ?>         
   <?php echo $form->error($model,'item_featured'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Background Color Hex")?></h6>
<div class="form-label-group">    
   <?php echo $form->textField($model,'color_hex',array(
     'class'=>"form-control form-control-text colorpicker",
     'placeholder'=>$form->label($model,'color_hex'),
     'readonly'=>false
   )); ?>      
   <?php echo $form->error($model,'color_hex'); ?>
</div>

<h6 class="mb-3 mt-4"><?php echo t("Status")?></h6>
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

<?php $this->renderPartial("/admin/modal_delete_image");?>