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
   <?php echo $form->textField($model,'type_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'type_id'),
     'readonly'=>$model->isNewRecord?false:true
   )); ?>   
   <?php    
    echo $form->labelEx($model,'type_id'); ?>
   <?php echo $form->error($model,'type_id'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'type_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'type_name'),     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'type_name'); ?>
   <?php echo $form->error($model,'type_name'); ?>
</div>

<div class="row">
  <div class="col">
  
   <h6 class="mb-2"><?php echo t("Commission Type")?></h6>
   <div class="form-label-group">    
	   <?php echo $form->dropDownList($model,'commision_type', (array) $commission_type_list,array(
	     'class'=>"form-control custom-select form-control-select",
	     'placeholder'=>$form->label($model,'commision_type'),
	   )); ?>         
	   <?php echo $form->error($model,'commision_type'); ?>
	</div>
  
  </div>
  <div class="col">

   <h6 class="mb-2">&nbsp;</h6>
    <div class="form-label-group">    
	   <?php echo $form->textField($model,'commission',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'commission'),     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'commission'); ?>
	   <?php echo $form->error($model,'commission'); ?>
	</div> 
  
  </div>
</div>
<!--row-->

<h6 class="mb-4"><?php echo t("Commission based on Subtotal / Total")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'based_on', (array) $commission_based,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'based_on'),
   )); ?>         
   <?php echo $form->error($model,'based_on'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Background Color Hex")?></h6>
<div class="form-label-group">    
   <?php echo $form->textField($model,'color_hex',array(
     'class'=>"form-control form-control-text colorpicker",
     'placeholder'=>$form->label($model,'color_hex'),
     'readonly'=>true
   )); ?>      
   <?php echo $form->error($model,'color_hex'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Font Color Hex")?></h6>
<div class="form-label-group">    
   <?php echo $form->textField($model,'font_color_hex',array(
     'class'=>"form-control form-control-text colorpicker",
     'placeholder'=>$form->label($model,'font_color_hex'),
     'readonly'=>true
   )); ?>      
   <?php echo $form->error($model,'font_color_hex'); ?>
</div>


<h6 class="mb-4"><?php echo t("Status")?></h6>
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