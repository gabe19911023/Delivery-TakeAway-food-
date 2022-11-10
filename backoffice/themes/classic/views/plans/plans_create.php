<?php if($update==false):?>
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
   <?php echo $form->textField($model,'title',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'title')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'title'); ?>
   <?php echo $form->error($model,'title'); ?>
</div>


<h6 class="mb-4"><?php echo t("Description")?></h6>
<div class="form-label-group">    
   <?php echo $form->textArea($model,'description',array(
     'class'=>"form-control form-control-text summernote",     
     'placeholder'=>t("Description")
   )); ?>      
   <?php echo $form->error($model,'description'); ?>
</div>

<div class="row mt-4">
  <div class="col-md-6">
  
  <div class="form-label-group">    
   <?php echo $form->textField($model,'price',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'price')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'price'); ?>
   <?php echo $form->error($model,'price'); ?>
</div>
  
  </div> <!--col-->
  <div class="col-md-6">
  
  
  <div class="form-label-group">    
	   <?php echo $form->textField($model,'promo_price',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'promo_price')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'promo_price'); ?>
	   <?php echo $form->error($model,'promo_price'); ?>
	</div>
  
  </div> <!--col-->
</div>
<!--row-->


<div class="row mb-3 mt-2">
 <div class="col">

     <h6><?php echo t("Plan period")?></h6>
	 <div class="form-label-group">    
	   <?php echo $form->dropDownList($model,'package_period',(array)$package_period_list,array(
	     'class'=>"form-control custom-select form-control-select",
	     'placeholder'=>$form->label($model,'package_period'),	     
	   )); ?>         
	   <?php echo $form->error($model,'package_period'); ?>
	</div>
 
 </div>
 <div class="col">

   <h6>&nbsp;</h6>
   <div class="form-label-group">    
	   <?php echo $form->textField($model,'item_limit',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'item_limit')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'item_limit'); ?>
	   <?php echo $form->error($model,'item_limit'); ?>
	   <small><?php echo t("0 is unlimited numbers of items")?></small>
	</div>
 
 </div>
</div>
<!--row-->


<div class="row mb-3 mt-2">
 <div class="col">
   	 
   <div class="form-label-group">    
	   <?php echo $form->textField($model,'order_limit',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'order_limit')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'order_limit'); ?>
	   <?php echo $form->error($model,'order_limit'); ?>
	   <small><?php echo t("0 is unlimited numbers of orders per period")?></small>
	</div>   
  
 
 </div>
 <div class="col">

    <div class="form-label-group">    
	   <?php echo $form->textField($model,'trial_period',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'trial_period')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'trial_period'); ?>
	   <?php echo $form->error($model,'trial_period'); ?>
	   <small><?php echo t("trial period number of days")?></small>
	</div>   
  
 </div>
</div>
<!--row-->

<h6 class="mb-3"><?php echo t("Ordering")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"ordering_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"ordering_enabled",
     'checked'=>$model->ordering_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="ordering_enabled">
   <?php echo t("Enabled")?>
  </label>
</div>       

<h6 class="mb-4 mt-4"><?php echo t("Status")?></h6>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array)$status,array(
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