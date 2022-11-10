
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

<h6 class="mb-3"><?php echo t("Choose Map Provider")?></h6>
<p class="text-muted"><?php echo t("Notice : this section need to be fill only if you have single website restaurant")?>.</p>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'merchant_map_provider', (array)$map_provider,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'merchant_map_provider'),
   )); ?>         
   <?php echo $form->error($model,'merchant_map_provider'); ?>
</div>		

<h6 class="mb-4"><?php echo t("Google Maps")?></h6>

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_google_geo_api_key',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_google_geo_api_key')     
   )); ?>   
   <?php    
    echo $form->label($model,'merchant_google_geo_api_key'); ?>
   <?php echo $form->error($model,'merchant_google_geo_api_key'); ?>   
</div>
   
<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_google_maps_api_key',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_google_maps_api_key')     
   )); ?>   
   <?php    
    echo $form->label($model,'merchant_google_maps_api_key'); ?>
   <?php echo $form->error($model,'merchant_google_maps_api_key'); ?>   
</div>

<h6 class="mb-4"><?php echo t("Mapbox")?></h6>

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_mapbox_access_token',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_mapbox_access_token')     
   )); ?>   
   <?php    
    echo $form->label($model,'merchant_mapbox_access_token'); ?>
   <?php echo $form->error($model,'merchant_mapbox_access_token'); ?>   
</div>

   
  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>