
  
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

<h6 class="mb-3"><?php echo t("Contact Settings")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_contact_form",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_contact_form",
     'checked'=>$model->enabled_contact_form==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_contact_form">
   <?php echo t("Enabled")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'contact_email_receiver',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'contact_email_receiver'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'contact_email_receiver'); ?>
   <?php echo $form->error($model,'contact_email_receiver'); ?>
</div>

<h6 class="mb-4"><?php echo t("Content")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'contact_content',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("Contact Content")
   )); ?>      
   <?php echo $form->error($model,'contact_content'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Contact Fields")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'contact_field',$contact_fields,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'placeholder'=>$form->label($model,'contact_field'),
     'multiple'=>true,
   )); ?>         
   <?php echo $form->error($model,'contact_field'); ?>
</div>

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>