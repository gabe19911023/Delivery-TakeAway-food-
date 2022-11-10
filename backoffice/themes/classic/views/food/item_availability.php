<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'form',
		'enableAjaxValidation' => false,		
	)
);
?>

<div id="vue-availability" class="card">
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

<input type="hidden" ref="available_at_specific" value="<?php echo $model->available_at_specific==1?true:false?>">


<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"available",array(
     'class'=>"custom-control-input",     
     'value'=>1,
     'id'=>"available",
     'v-model'=>'available',
     'checked'=>$model->available==1?true:false,     
   )); ?>   
  <label class="custom-control-label" for="available">
   <?php echo t("Available")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"not_for_sale",array(
     'class'=>"custom-control-input",     
     'value'=>1,
     'id'=>"not_for_sale",
     'v-model'=>'not_for_sale',
     'checked'=>$model->not_for_sale==1?true:false,     
   )); ?>   
  <label class="custom-control-label" for="not_for_sale">
   <?php echo t("Not for sale")?>
   <span class="ml-2 font14"><i data-toggle="tooltip" title="<?php echo t("The menu item will be visible in the menu but your customers will not be able to buy it")?>" class="zmdi zmdi-help"></i></span>
  </label> 
</div>    


<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"available_at_specific",array(
     'class'=>"custom-control-input",     
     'value'=>1,
     'id'=>"available_at_specific",
     'v-model'=>'available_at_specific',
     'checked'=>$model->available_at_specific==1?true:false,     
   )); ?>   
  <label class="custom-control-label" for="available_at_specific">
   <?php echo t("Available at specified times")?>
  </label>
</div>    

<!--DAY LIST-->
<DIV v-if="available_at_specific" class="availability_wrap" v-cloak> 
<div class="d-flex flex-row justify-content-end">
  <div class="p-2">
  
  <a type="button" class="btn btn-black btn-circle checkbox_select_all" 
  href="javascript:;">
    <i class="zmdi zmdi-check"></i>
  </a>
  
  </div>
  <div class="p-2"><h5><?php echo t("Check All")?></h5></div>
</div> <!--flex-->

<?php foreach ($days as $key=> $item):?>
<div class="row mt-3 align-items-center">
 <div class="col">
 
   <div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"available_day[$key]",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"available_day".$key,    
     'checked'=>in_array($key,$data['day'])?true:false,
   )); ?>   
  <label class="custom-control-label" for="available_day<?php echo $key?>">
   <?php echo $item?>
  </label>
</div>    
 
 </div>
 <div class="col">
 
   <div class="form-label-group mr-3">    
	   <?php echo $form->textField($model,  "available_time_start[$key]"  ,array(
	     'class'=>"form-control form-control-text timepick datetimepicker-input",     
	     'placeholder'=>$form->label($model, "available_time_start[$key]" ),       
	     'readonly'=>true,
	     'data-toggle'=>'datetimepicker',
	     'value'=>isset($data['start'][$key])?$data['start'][$key]:'',
	   )); ?>   	   
	   <?php    
	    echo $form->labelEx($model, "available_time_start[$key]"); ?>
	   <?php echo $form->error($model, "available_time_start[$key]" ); ?>
	</div>
 
 </div>
 <div class="col">
 
   <div class="form-label-group mr-3">    
	   <?php echo $form->textField($model,  "available_time_end[$key]"  ,array(
	     'class'=>"form-control form-control-text timepick datetimepicker-input",     
	     'placeholder'=>$form->label($model, "available_time_end[$key]" ),       
	     'readonly'=>true,
	     'data-toggle'=>'datetimepicker',
	     'value'=>isset($data['end'][$key])?$data['end'][$key]:'',
	   )); ?>   	   
	   <?php    
	    echo $form->labelEx($model, "available_time_end[$key]"); ?>
	   <?php echo $form->error($model, "available_time_end[$key]" ); ?>
	</div>
 
 </div>
</div>
<?php endforeach;?>
</DIV>
<!--DAY LIST-->

  </div> <!--body-->
</div> <!--card-->

  
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


<?php $this->endWidget(); ?>
