<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'form',
		'enableAjaxValidation' => false,		
	)
);
?>

<div id="vue-tax" class="card">
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


<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"tax_enabled",array(
     'class'=>"custom-control-input",     
     'value'=>1,
     'id'=>"tax_enabled",     
     'checked'=>$model->tax_enabled==1?true:false,     
   )); ?>   
  <label class="custom-control-label" for="tax_enabled">
   <?php echo t("Tax enabled")?>
  </label>
</div>    

<hr/>

<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"tax_service_fee",array(
     'class'=>"custom-control-input",     
     'value'=>1,
     'id'=>"tax_service_fee",     
     'checked'=>$model->tax_service_fee==1?true:false,     
   )); ?>   
  <label class="custom-control-label" for="tax_service_fee">
   <?php echo t("Tax on service fee")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"tax_on_delivery_fee",array(
     'class'=>"custom-control-input",     
     'value'=>1,
     'id'=>"tax_on_delivery_fee",     
     'checked'=>$model->tax_on_delivery_fee==1?true:false,     
   )); ?>   
  <label class="custom-control-label" for="tax_on_delivery_fee">
   <?php echo t("Tax on delivery fee")?>
  </label>
</div>    

<div class="custom-control custom-switch custom-switch-md mr-4">  
  <?php echo $form->checkBox($model,"tax_packaging",array(
     'class'=>"custom-control-input",     
     'value'=>1,
     'id'=>"tax_packaging",     
     'checked'=>$model->tax_packaging==1?true:false,     
   )); ?>   
  <label class="custom-control-label" for="tax_packaging">
   <?php echo t("Tax on packaging fee")?>
  </label>
</div>    

<h6 class="mt-3"><?php echo t("Tax Type")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tax_type', (array) $tax_type_list,array(
     'class'=>"form-control custom-select form-control-select",
     'placeholder'=>$form->label($model,'tax_type'),
   )); ?>         
   <?php echo $form->error($model,'tax_type'); ?>
</div>

<?php if($model->tax_type=="multiple"):?>
<h6 class="mt-3"><?php echo t("Tax for delivery,service and packaging fee.")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'tax_for_delivery', (array)$mutilple_tax_list,array(
     'class'=>"form-control custom-select form-control-select select_two",
     'multiple'=>true,
     'placeholder'=>$form->label($model,'tax_for_delivery'),
   )); ?>         
   <?php echo $form->error($model,'tax_for_delivery'); ?>
</div>
<?php endif;?>

<div class="d-flex justify-content-end">
  <a @click="newTax" class="btn btn-link btn-sm text-green border"><?php echo t("Add new tax")?></a>
</div>

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
actions="taxList"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:settings="{
    filter : '<?php echo false;?>',   
    ordering :'<?php echo false;?>',    
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>', 
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All transactions")?>',
    filter_date_disabled : '<?php echo true;?>',  
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
@edit-tax="editTax"
@delete-tax="deleteTax"
>
</components-datatable>

<components-tax
ref="tax"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
@after-save="afterSave"
:tax_in_price_list='<?php echo json_encode($tax_in_price_list)?>'
tax_type='<?php echo $model->tax_type?>'
:label="{      
    title : '<?php echo CJavaScript::quote(t("Add new tax"))?>',
    cancel : '<?php echo CJavaScript::quote(t("Cancel"))?>',
    save : '<?php echo CJavaScript::quote(t("Save"))?>',
    confirmation : '<?php echo CJavaScript::quote(t("Delete Confirmation"))?>',
    content : '<?php echo CJavaScript::quote(t("Are you sure you want to permanently delete the selected item?"))?>',
    confirm : '<?php echo CJavaScript::quote(t("Confirm"))?>',
    rate : '<?php echo CJavaScript::quote(t("Rate %"))?>',
    tax_name : '<?php echo CJavaScript::quote(t("Tax name"))?>',    
    active : '<?php echo CJavaScript::quote(t("active"))?>',      
    default_tax : '<?php echo CJavaScript::quote(t("Default tax"))?>',          
  }"  
>
</components-tax>


  </div> <!--body-->
</div> <!--card-->
  
  
<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>


<?php $this->endWidget(); ?>