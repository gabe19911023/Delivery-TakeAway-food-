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

<p class="text-muted"><?php echo t("Notice : this section need to be fill only if you have single website restaurant")?>.</p>

<h6 class="mb-2"><?php echo t("Facebook")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_fb_flag",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_fb_flag",
     'checked'=>$model->merchant_fb_flag==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_fb_flag">
   <?php echo t("Enabled Facebook Login")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_fb_app_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_fb_app_id'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_fb_app_id'); ?>
   <?php echo $form->error($model,'merchant_fb_app_id'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_fb_app_secret',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_fb_app_secret'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_fb_app_secret'); ?>
   <?php echo $form->error($model,'merchant_fb_app_secret'); ?>
</div>

<h6 class="mb-2"><?php echo t("Google")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"merchant_google_login_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"merchant_google_login_enabled",
     'checked'=>$model->merchant_google_login_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="merchant_google_login_enabled">
   <?php echo t("Enabled Google Login")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_google_client_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_google_client_id'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_google_client_id'); ?>
   <?php echo $form->error($model,'merchant_google_client_id'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'merchant_google_client_secret',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'merchant_google_client_secret'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'merchant_google_client_secret'); ?>
   <?php echo $form->error($model,'merchant_google_client_secret'); ?>
</div>

<hr/>

<h6 class="mt-3"><?php echo t("Social URL Page")?></h6>

<div class="form-label-group">    
   <?php echo $form->textField($model,'facebook_page',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'facebook_page')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'facebook_page'); ?>
   <?php echo $form->error($model,'facebook_page'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'twitter_page',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'twitter_page')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'twitter_page'); ?>
   <?php echo $form->error($model,'twitter_page'); ?>
</div>


<div class="form-label-group">    
   <?php echo $form->textField($model,'google_page',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'google_page')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'google_page'); ?>
   <?php echo $form->error($model,'google_page'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'instagram_page',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'instagram_page')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'instagram_page'); ?>
   <?php echo $form->error($model,'instagram_page'); ?>
</div>


  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>  