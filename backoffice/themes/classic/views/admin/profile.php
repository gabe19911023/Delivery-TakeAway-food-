<div class="card">

 <div class="card-body">

 <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'profile',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

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

<div class="row">
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'first_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'first_name'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'first_name'); ?>
   <?php echo $form->error($model,'first_name'); ?>
</div>
  
  </div> <!--col-->
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'last_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'last_name'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'last_name'); ?>
   <?php echo $form->error($model,'last_name'); ?>
</div>
  
  </div> <!--col-->
</div><!--row-->

<div class="row">
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'email_address',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'email_address'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'email_address'); ?>
   <?php echo $form->error($model,'email_address'); ?>
</div>
  
  </div> <!--col-->
  
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'contact_number',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'contact_number'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'contact_number'); ?>
   <?php echo $form->error($model,'contact_number'); ?>
</div>
  
  </div><!-- col-->
</div><!--row-->  


<div class="form-label-group">    
   <?php echo $form->textField($model,'username',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'username'),
     'readonly'=>true
   )); ?>   
   <?php    
    echo $form->labelEx($model,'username'); ?>
   <?php echo $form->error($model,'username'); ?>
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
selected_file="<?php echo $model->profile_photo;?>"
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

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


<?php $this->endWidget(); ?>
 
 </div> <!--card body-->

</div><!--card-->

<?php $this->renderPartial("/admin/modal_delete_image");?>