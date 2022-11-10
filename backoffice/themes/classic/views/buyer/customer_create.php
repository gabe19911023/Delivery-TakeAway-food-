<?php if(!$hide_nav):?>
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
		'id' => 'active-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => array('enctype' => 'multipart/form-data', 'autocomplete'=>"off"),
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


<DIV class="row">
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'first_name',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'first_name')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'first_name'); ?>
	   <?php echo $form->error($model,'first_name'); ?>
	</div>
 </DIV>
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'last_name',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'last_name')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'last_name'); ?>
	   <?php echo $form->error($model,'last_name'); ?>
	</div>
 </DIV> 
</DIV>
<!--row-->


<DIV class="row">
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'email_address',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'email_address')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'email_address'); ?>
	   <?php echo $form->error($model,'email_address'); ?>
	</div>
 </DIV>
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->textField($model,'contact_phone',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'contact_phone')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'contact_phone'); ?>
	   <?php echo $form->error($model,'contact_phone'); ?>
	</div>
 </DIV> 
</DIV>
<!--row-->


<DIV class="row">
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->passwordField($model,'npassword',array(
	     'class'=>"form-control form-control-text new-passowrd",
	     'placeholder'=>$form->label($model,'npassword'),
	     'autocomplete'=>'new-passowrd'    
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'npassword'); ?>
	   <?php echo $form->error($model,'npassword'); ?>
	</div>
 </DIV>
 <DIV class="col-md-6">
	<div class="form-label-group">    
	   <?php echo $form->passwordField($model,'cpassword',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'cpassword')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'cpassword'); ?>
	   <?php echo $form->error($model,'cpassword'); ?>
	</div>
 </DIV> 
</DIV>
<!--row-->

<div id="vue-uploader">
<component-uploader
ref="uploader"
max_file="<?php echo Yii::app()->params->dropzone['max_file'];?>"
max_file_size = "<?php echo Yii::app()->params->dropzone['max_file_size']?>"
select_type="single"
field = "avatar"
field_path = "path"
inline="false"
selected_file="<?php echo $model->avatar;?>"
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

<h6 class="mb-4 mt-4"><?php echo t("Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array)$status_list,array(
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