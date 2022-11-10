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
   <?php echo $form->textField($model,'card_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'card_name')     
   )); ?>   
   <?php    
    echo $form->label($model,'card_name'); ?>
   <?php echo $form->error($model,'card_name'); ?>   
   <small class="form-text text-muted mb-2">
	  <?php echo t("Full name as displayed on card")?>
	</small>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'credit_card_number',array(
     'class'=>"form-control form-control-text card_number",
     'placeholder'=>$form->label($model,'credit_card_number')     
   )); ?>   
   <?php    
    echo $form->label($model,'credit_card_number'); ?>
   <?php echo $form->error($model,'credit_card_number'); ?>      
</div>

<div class="d-flex">

<div class="form-label-group mr-3">    
   <?php echo $form->textField($model,'expiration',array(
     'class'=>"form-control form-control-text card_expiration",
     'placeholder'=>$form->label($model,'expiration')     
   )); ?>   
   <?php    
    echo $form->label($model,'expiration'); ?>
   <?php echo $form->error($model,'expiration'); ?>      
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'cvv',array(
     'class'=>"form-control form-control-text card_cvv",
     'placeholder'=>$form->label($model,'cvv')     
   )); ?>   
   <?php    
    echo $form->label($model,'cvv'); ?>
   <?php echo $form->error($model,'cvv'); ?>      
</div>

</div> <!--flex-->
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>