<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>array(
    t("All Currency")=>array('attributes/currency'),        
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


<h6 class="mb-4"><?php echo t("Set as Default")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"as_default",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"as_default",
     'checked'=>$model->as_default==1?true:false
   )); ?>   
  <label class="custom-control-label" for="as_default">
   <?php echo t("Default")?>
  </label>
</div>    

<div class="row">
 <div class="col-md-6">
 
 <h6 class="mb-4 mt-4"><?php echo t("Currency")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'currency_code', (array)$currency_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'currency_code'),
   )); ?>         
   <?php echo $form->error($model,'currency_code'); ?>
</div>		   

 
 </div>
 <div class="col-md-6">

 
<h6 class="mb-4 mt-4"><?php echo t("Position")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'currency_position', (array)$currency_position,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'currency_position'),
   )); ?>         
   <?php echo $form->error($model,'currency_position'); ?>
</div>		   
 
 </div>
</div>
<!--row-->


<div class="row">
 <div class="col-md-6">
 
 
<div class="form-label-group">    
   <?php echo $form->numberField($model,'exchange_rate',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'exchange_rate'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'exchange_rate'); ?>
   <?php echo $form->error($model,'exchange_rate'); ?>
</div>
 
 </div>
 <div class="col-md-6">

 
<div class="form-label-group">    
   <?php echo $form->numberField($model,'exchange_rate_fee',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'exchange_rate_fee'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'exchange_rate_fee'); ?>
   <?php echo $form->error($model,'exchange_rate_fee'); ?>
</div>
 
 </div>
</div>
<!--row-->


<div class="form-label-group">    
   <?php echo $form->numberField($model,'number_decimal',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'number_decimal'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'number_decimal'); ?>
   <?php echo $form->error($model,'number_decimal'); ?>
</div>



<div class="row">
 <div class="col-md-6">
 
 
<div class="form-label-group">    
   <?php echo $form->textField($model,'decimal_separator',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'decimal_separator'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'decimal_separator'); ?>
   <?php echo $form->error($model,'decimal_separator'); ?>
</div>
 
 </div>
 <div class="col-md-6">

 
<div class="form-label-group">    
   <?php echo $form->textField($model,'thousand_separator',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'thousand_separator'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'thousand_separator'); ?>
   <?php echo $form->error($model,'thousand_separator'); ?>
</div>
 
 </div>
</div>
<!--row-->


  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>