
  
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

<?php 
echo $form->hiddenField($model,'website_logo');
echo $form->hiddenField($model,'mobilelogo');
?>   
   

<div class="form-label-group">    
   <?php echo $form->textField($model,'website_title',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'website_title')     
   )); ?>   
   <?php    
    echo $form->label($model,'website_title',array(
     'label'=>t("Title")
    )); ?>
   <?php echo $form->error($model,'website_title'); ?>   
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
selected_file="<?php echo $model->website_logo;?>"
upload_path="<?php echo $upload_path?>"
save_path="<?php echo $upload_path?>"

@set-afer-upload="afterUpload"
@set-afer-delete="afterDelete"
:label="{
    select_file:'<?php echo CJavaScript::quote(t("Select File"))?>',       
    upload_new:'<?php echo CJavaScript::quote(t("Upload New"))?>',     
    upload_button:'<?php echo CJavaScript::quote(t("Website logo"))?>',     
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

<h6 class="mb-4 mt-4"><?php echo t("Business Address")?></h6>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'admin_country_set', (array)$country_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'admin_country_set'),
   )); ?>         
   <?php echo $form->error($model,'admin_country_set'); ?>
</div>		

<div class="form-label-group">    
   <?php echo $form->textField($model,'website_address',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'website_address')     
   )); ?>   
   <?php    
    echo $form->label($model,'website_address',array(
     'label'=>t("Address")
    )); ?>
   <?php echo $form->error($model,'website_address'); ?>   
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'website_contact_phone',array(
     'class'=>"form-control form-control-text mask_phone",
     'placeholder'=>$form->label($model,'website_contact_phone')     
   )); ?>   
   <?php    
    echo $form->label($model,'website_contact_phone',array(
     'label'=>t("Contact Phone Number")
    )); ?>
   <?php echo $form->error($model,'website_contact_phone'); ?>   
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'website_contact_email',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'website_contact_email')     
   )); ?>      
   <?php    
    echo $form->label($model,'website_contact_email',array(
     'label'=>t("Contact email")
    )); ?>
   <?php echo $form->error($model,'website_contact_email'); ?>   
</div>

   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>


<?php $this->renderPartial("/admin/modal_delete_image");?>