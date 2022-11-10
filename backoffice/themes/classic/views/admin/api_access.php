<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'frm-merchant',
	'enableAjaxValidation'=>false,
)); ?>

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

<?php if($data_found):?>

<h6 class="mb-3 mt-4"><?php echo t("Mobile API URL")?></h6>    
  <div class="form-label-group">    
   <?php echo $form->textField($model,'api_url',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'api_url'),     
     'disabled'=>true,
     'value'=>CommonUtility::getHomebaseUrl()."/interface"
   )); ?>   
   <?php echo $form->labelEx($model,'api_url'); ?>
   <?php echo $form->error($model,'api_url'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'payment_api_url',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'payment_api_url'),     
     'disabled'=>true,
     'value'=>CommonUtility::getHomebaseUrl()."/pv1"
   )); ?>   
   <?php echo $form->labelEx($model,'payment_api_url'); ?>
   <?php echo $form->error($model,'payment_api_url'); ?>
</div>

  <h6 class="mb-3 mt-4"><?php echo t("Mobile API Keys")?></h6>  
  <p class="text-muted"><?php echo t("Below is mobile app api keys that you need to set in your mobile app configurations")?></p>
  <div class="form-label-group mt-2">    
   <?php echo $form->textArea($model,'website_jwt_token',array(
     'class'=>"form-control form-control-text",     
     'placeholder'=>t("Contact Content"),
     'disabled'=>true,     
   )); ?>      
   <?php echo $form->error($model,'website_jwt_token'); ?>
   <a class="btn btn-link" href="<?php echo Yii::app()->createUrl("/admin/delete_apikeys")?>"><?php echo t("Delete Keys")?></a>
</div>

<?php else :?>

 <!-- <h6 class="mb-4 mt-4"><?php echo t("Website Domain URL address")?></h6> -->
 <div class="form-label-group">    
   <?php echo $form->hiddenField($model,'website_jwt_token',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'website_jwt_token'),     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'website_jwt_token'); ?>
   <?php echo $form->error($model,'website_jwt_token'); ?>
</div>

<div class="row text-left mt-4">
<div class="col-md-12 m-0">
<?php echo CHtml::submitButton('save',array(
'class'=>"btn btn-green btn-full",
'value'=>CommonUtility::t("Generate API keys")
)); ?>
</div>
</div>

<?php endif?>


<?php $this->endWidget(); ?>
 