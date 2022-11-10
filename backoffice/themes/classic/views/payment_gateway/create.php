<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>array(
    t("All Payment gateway")=>array('payment_gateway/list'),        
    $this->pageTitle,
),
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


<div class="row mb-3 align-items-center">
<div class="col-lg-4 col-sm-6 col-xs-12">

    <div class="custom-control custom-switch custom-switch-md mb-2">  
	  <?php echo $form->checkBox($model,"is_online",array(
	     'class'=>"custom-control-input checkbox_child",     
	     'value'=>1,
	     'id'=>"is_online",
	     'checked'=>$model->is_online==1?true:false
	   )); ?>   
	  <label class="custom-control-label" for="is_online">
	   <?php echo t("Online Payment")?>
	  </label>
	</div>     
  
  </div> <!--col-->
  <div class="col-lg-4 col-sm-6 col-xs-12">
  
     <div class="custom-control custom-switch custom-switch-md mb-2">  
	  <?php echo $form->checkBox($model,"is_payout",array(
	     'class'=>"custom-control-input checkbox_child",     
	     'value'=>1,
	     'id'=>"is_payout",
	     'checked'=>$model->is_payout==1?true:false
	   )); ?>   
	  <label class="custom-control-label" for="is_payout">
	   <?php echo t("Available for payout")?>
	  </label>
	 </div>   
  
  </div> <!--col-->
  
  <div class="col-lg-4 col-sm-6 col-xs-12">
  
     <div class="custom-control custom-switch custom-switch-md mb-2">  
	  <?php echo $form->checkBox($model,"is_plan",array(
	     'class'=>"custom-control-input checkbox_child",     
	     'value'=>1,
	     'id'=>"is_plan",
	     'checked'=>$model->is_plan==1?true:false
	   )); ?>   
	  <label class="custom-control-label" for="is_plan">
	   <?php echo t("Available for plan")?>
	  </label>
	 </div>   
  
  </div> <!--col-->
  
</div> <!--flex-->

<div class="form-label-group">    
   <?php echo $form->textField($model,'payment_code',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'payment_code'),
     'disabled'=>$model->isNewRecord?false:true    
   )); ?>   
   <?php    
    echo $form->labelEx($model,'payment_code'); ?>
   <?php echo $form->error($model,'payment_code'); ?>
   <small class="form-text text-muted mb-2"><?php echo t("This fields must not have spaces")?></small>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'payment_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'payment_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'payment_name'); ?>
   <?php echo $form->error($model,'payment_name'); ?>
</div>

<h6 class="mb-4"><?php echo t("Logo type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'logo_type', (array) array(
     'icon'=>t("Icon"),
     'image'=>t("Image"),
   ),array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'logo_type'),
   )); ?>         
   <?php echo $form->error($model,'logo_type'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'logo_class',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'logo_class')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'logo_class'); ?>
   <?php echo $form->error($model,'logo_class'); ?>
</div>
<p class="text-muted">
 <?php echo t("Get icon here")?> <a target="_blank" href="http://zavoloklom.github.io/material-design-iconic-font/icons.html"><?php echo t("Click here")?></a>
</p>

<div id="vue-uploader">
<component-uploader
ref="uploader"
max_file="<?php echo Yii::app()->params->dropzone['max_file'];?>"
max_file_size = "<?php echo Yii::app()->params->dropzone['max_file_size']?>"
select_type="single"
field = "photo"
field_path = "path"
inline="false"
selected_file="<?php echo $model->logo_image;?>"
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

<?php if(is_array($attr_json) && count($attr_json)>=1):?>
<h4 class="mt-4 mb-3"><?php echo t("Credentials")?></h4>

<div class="custom-control custom-switch custom-switch-md mb-2">  
  <?php echo $form->checkBox($model,"is_live",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"is_live",
     'checked'=>$model->is_live==1?true:false
   )); ?>   
  <label class="custom-control-label" for="is_live">
   <?php echo t("Production")?>
  </label>
</div>    

<?php foreach ($attr_json as $key=>$item):?>

<?php 
$field_type = isset($item['field_type'])?$item['field_type']:'';
?>
<?php if($field_type=="textarea") :?>
  <h6 class="mb-1"><?php echo t($item['label'])?></h6>
  <div class="form-label-group mt-2">    
    <?php echo $form->textArea($model,$key,array(
      'class'=>"form-control form-control-text summernote",     
      'placeholder'=>$form->label($model,$key)     
    )); ?>      
    <?php echo $form->error($model,$key); ?>
  </div>
<?php else :?>
  <div class="form-label-group">    
    <?php echo $form->textField($model,$key,array(
      'class'=>"form-control form-control-text",
      'placeholder'=>$form->label($model,$key)     
    )); ?>   
    <label for="AR_payment_gateway_<?php echo $key?>"><?php echo t($item['label'])?></label>
    <?php echo $form->error($model,$key); ?>
  </div>
<?php endif?>

<?php endforeach;?>
<?php endif;?>

<?php if(is_array($instructions) && count($instructions)>=1):?>
<?php foreach ($instructions as $ins_key=>$ins_item):?>
  <?php if($ins_key=="webhooks"):?>
  <div class="card border">
   <div class="card-body">
    <h5><?php echo t("Webhooks")?></h5>   
	    <div class="d-flex">
	    <p class="text-dark m-0 mr-2"><?php echo t($ins_item,array('{{site_url}}'=> $protocol."://".Yii::app()->request->getServerName() ));?></p>	    
	    </div>
    </div>
  </div>
  <?php endif;?>
<?php endforeach;?>
<?php endif;?>


<h6 class="mb-4 mt-3"><?php echo t("Status")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array) $status,array(
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