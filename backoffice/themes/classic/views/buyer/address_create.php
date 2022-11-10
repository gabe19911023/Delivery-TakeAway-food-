<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'active-form',
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


<div class="form-label-group">    
   <?php echo $form->textField($model,'formatted_address',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'formatted_address')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'formatted_address'); ?>
   <?php echo $form->error($model,'formatted_address'); ?>
</div>

<div class="row">
  <div class="col">
     <div class="form-label-group">    
	   <?php echo $form->textField($model,'latitude',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'latitude')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'latitude'); ?>
	   <?php echo $form->error($model,'latitude'); ?>
	</div>
  </div> <!--col-->
   <div class="col">
     <div class="form-label-group">    
	   <?php echo $form->textField($model,'longitude',array(
	     'class'=>"form-control form-control-text",
	     'placeholder'=>$form->label($model,'longitude')     
	   )); ?>   
	   <?php    
	    echo $form->labelEx($model,'longitude'); ?>
	   <?php echo $form->error($model,'longitude'); ?>
	</div>
  </div> <!--col-->
</div>
<!--row-->

<div class="form-label-group">    
   <?php echo $form->textField($model,'place_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'place_id')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'place_id'); ?>
   <?php echo $form->error($model,'place_id'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'location_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'location_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'location_name'); ?>
   <?php echo $form->error($model,'location_name'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'delivery_options', (array)$delivery_option,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'delivery_options'),
   )); ?>         
   <?php echo $form->error($model,'delivery_options'); ?>
</div>		


<h6 class="mb-4"><?php echo t("Add delivery instructions")?></h6>
<div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'delivery_instructions',array(
     'class'=>"form-control form-control-text",          
   )); ?>      
   <?php echo $form->error($model,'delivery_instructions'); ?>
</div>


 <h6 class="mb-4"><?php echo t("Address label")?></h6>
  
 <div class="btn-group btn-group-toggle" data-toggle="buttons">
   <?php foreach ($address_label as $lcn_key=>$lcn_name):?>
   <label class="btn btn-black <?php echo $model->address_label==$lcn_key?"active":''?> ">    
    <?php 
    echo $form->radioButton($model,'address_label',
    array('value'=>$lcn_key,
      'uncheckValue'=>null
    ));
    ?> 
    <?php echo $lcn_name?>
  </label>
   <?php endforeach;?>
    <?php echo $form->error($model,'address_label'); ?>
 </div>


  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>