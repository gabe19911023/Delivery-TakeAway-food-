
<div class="card mt-3">
  <div class="card-body">  
  <a class="btn" data-toggle="collapse" href="#seo" role="button">
    <div class="d-flex flex-row">
        <div class="p-2"><?php echo t("SEO")?></div>
        <div class="p-2"><i class="zmdi zmdi-plus"></i></div>
    </div>
  </a>
  
<div class="collapse" id="seo">
  <div class="card card-body">
 

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
field = "photo"
field_path = "path"
inline="false"
selected_file="<?php echo $model->meta_image;?>"
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

  
  </div> <!--card body-->
</div>
<!--collapse-->

  </div> <!--body-->
</div> <!--card-->

<?php $this->renderPartial("/admin/modal_delete_image");?>
  