
  
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


<div id="vue-uploader">
<component-uploader
ref="uploader"
max_file="<?php echo Yii::app()->params->dropzone['max_file'];?>"
max_file_size = "<?php echo Yii::app()->params->dropzone['max_file_size']?>"
select_type="single"
field = "receipt_logo"
field_path = "path"
inline="false"
selected_file="<?php echo $model->receipt_logo;?>"
upload_path="<?php echo $upload_path?>"
save_path="<?php echo $upload_path?>"

@set-afer-upload="afterUpload"
@set-afer-delete="afterDelete"
:label="{
    select_file:'<?php echo CJavaScript::quote(t("Select File"))?>',       
    upload_new:'<?php echo CJavaScript::quote(t("Upload New"))?>',     
    upload_button:'<?php echo CJavaScript::quote(t("Receipt Logo"))?>',     
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

<h6 class="m-0 mt-3"><?php echo t("Receipt Thank you text")?></h6>
<div class="form-label-group mt-3">    
   <?php echo $form->textArea($model,'receipt_thank_you',array(
     'class'=>"form-control form-control-text autosize",     
     'placeholder'=>''
   )); ?>      
   <?php echo $form->error($model,'receipt_thank_you'); ?>
</div>

<hr/>

<h6 class="m-0"><?php echo t("Receipt Footer text")?></h6>
<div class="form-label-group mt-3">    
   <?php echo $form->textArea($model,'receipt_footer',array(
     'class'=>"form-control form-control-text autosize",     
     'placeholder'=>t("")
   )); ?>      
   <?php echo $form->error($model,'receipt_footer'); ?>
</div>



<!--TRANSLATION-->
<?php if(is_array($language) && count($language)>=1 ):?>
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


   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>

<?php $this->renderPartial("/admin/modal_delete_image");?>