<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
		'enableAjaxValidation' => false,		
	)
);
?>


<div class="d-flex flex-row justify-content-end">
  <div class="p-2">
  
  <a type="button" class="btn btn-black btn-circle checkbox_select_all" 
  href="javascript:;">
    <i class="zmdi zmdi-check"></i>
  </a>
  
  </div>
  <div class="p-2"><h5><?php echo t("Check All")?></h5></div>
</div> <!--flex-->

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

<?php if(Yii::app()->params['isMobile']!=TRUE):?>
<div class="row">
  <div class="col-md-4"><h6 class="mb-4"><?php echo t("Days available")?></h6></div>
  <div class="col-md-4"><h6 class="mb-4"><?php echo t("Start Time")?></h6></div>
  <div class="col-md-4"><h6 class="mb-4"><?php echo t("End Time")?></h6></div>
</div>
<?php endif;?>

<?php foreach ($days as $day=>$day_name):?>
<div class="row">
  <div class="col-md-4">
  
   <div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,$day,array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>$day,
     'checked'=>$model[$day]==1?true:false
   )); ?>   
  <label class="custom-control-label" for="<?php echo $day?>">
   <?php echo ucwords($day_name)?>
  </label>
</div>    
  
  </div> <!--row-->
  
  <div class="col-md-4">
  
    <div class="form-label-group mr-3">    
	   <?php echo $form->textField($model,  $day."_start"  ,array(
	     'class'=>"form-control form-control-text timepick datetimepicker-input",     
	     'placeholder'=>$form->label($model, $day."_start" ),     
	     'readonly'=>true,
	     'data-toggle'=>'datetimepicker'
	   )); ?>   	   
	   <?php    
	    echo $form->labelEx($model, $day."_start"); ?>
	   <?php echo $form->error($model, $day."_start" ); ?>
	</div>
  
  </div> <!--row-->
  
  <div class="col-md-4">
  
   <div class="form-label-group mr-3">    
	   <?php echo $form->textField($model,  $day."_end"  ,array(
	     'class'=>"form-control form-control-text timepick datetimepicker-input",     
	     'placeholder'=>$form->label($model, $day."_end" ),     
	     'readonly'=>true,
	     'data-toggle'=>'datetimepicker'
	   )); ?>   	   
	   <?php    
	    echo $form->labelEx($model, $day."_end"); ?>
	   <?php echo $form->error($model, $day."_end" ); ?>
	</div>
  
  </div> <!--row-->
  
</div> <!--row-->
<?php endforeach;?>  
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


  </div> <!--body-->
</div> <!--card-->


<?php $this->endWidget(); ?>