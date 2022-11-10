
  
<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'form',
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

<h6 class="mb-3"><?php echo t("Promo Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'promo_type', (array) $promo_type,array(
     'class'=>"form-control custom-select form-control-select promo_type",
     'placeholder'=>$form->label($model,'promo_type'),
   )); ?>         
   <?php echo $form->error($model,'promo_type'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'buy_qty',array(
     'class'=>"form-control form-control-text buy_qty",
     'placeholder'=>$form->label($model,'buy_qty')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'buy_qty'); ?>
   <?php echo $form->error($model,'buy_qty'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'get_qty',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'get_qty')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'get_qty'); ?>
   <?php echo $form->error($model,'get_qty'); ?>
</div>


<h6 class="mb-4"><?php echo t("Select Item")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'item_id_promo',(array)$items,array(
     'class'=>"form-control custom-select form-control-select select_two_ajax",
     'placeholder'=>$form->label($model,'item_id_promo'),   
     'action'=>'search_item'
   )); ?>         
   <?php echo $form->error($model,'item_id_promo'); ?>
</div>


<div class="d-flex">
<div class="form-label-group w-50 mr-3">    
   <?php echo $form->textField($model,'discount_start',array(
     'class'=>"form-control form-control-text datepick",
     'readonly'=>true,
     'placeholder'=>$form->label($model,'discount_start'),          
   )); ?>   
   <?php    
    echo $form->labelEx($model,'discount_start'); ?>
   <?php echo $form->error($model,'discount_start'); ?>
</div>

<div class="form-label-group w-50 mr-3">    
   <?php echo $form->textField($model,'discount_end',array(
     'class'=>"form-control form-control-text datepick",
     'readonly'=>true,
     'placeholder'=>$form->label($model,'discount_end'),          
   )); ?>   
   <?php    
    echo $form->labelEx($model,'discount_end'); ?>
   <?php echo $form->error($model,'discount_end'); ?>
</div>
</div> <!--flex-->

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

  </div> <!--body-->
</div> <!--card-->
  
 


<?php $this->endWidget(); ?>

<?php $this->renderPartial("/admin/modal_delete_image");?>