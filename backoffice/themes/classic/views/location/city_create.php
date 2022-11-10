<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'active-form',
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
   <?php echo $form->textField($model,'name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'name'); ?>
   <?php echo $form->error($model,'name'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'postal_code',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'postal_code')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'postal_code'); ?>
   <?php echo $form->error($model,'postal_code'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Select State")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'state_id', (array)$state_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'state_id'),
   )); ?>         
   <?php echo $form->error($model,'state_id'); ?>
</div>		   

<div class="form-label-group">    
   <?php echo $form->textField($model,'sequence',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'sequence')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'sequence'); ?>
   <?php echo $form->error($model,'sequence'); ?>
</div>


  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>