
  
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


<h6 class="mb-2"><?php echo t("Facebook")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"fb_flag",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"fb_flag",
     'checked'=>$model->fb_flag==1?true:false
   )); ?>   
  <label class="custom-control-label" for="fb_flag">
   <?php echo t("Enabled Facebook Login")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'fb_app_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'fb_app_id'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'fb_app_id'); ?>
   <?php echo $form->error($model,'fb_app_id'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'fb_app_secret',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'fb_app_secret'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'fb_app_secret'); ?>
   <?php echo $form->error($model,'fb_app_secret'); ?>
</div>

<h6 class="mb-2"><?php echo t("Google")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"google_login_enabled",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"google_login_enabled",
     'checked'=>$model->google_login_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="google_login_enabled">
   <?php echo t("Enabled Google Login")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->textField($model,'google_client_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'google_client_id'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'google_client_id'); ?>
   <?php echo $form->error($model,'google_client_id'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'google_client_secret',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'google_client_secret'),
   )); ?>   
   <?php    
    echo $form->labelEx($model,'google_client_secret'); ?>
   <?php echo $form->error($model,'google_client_secret'); ?>
</div>

<!--
<div class="form-label-group">    
   <?php echo $form->textField($model,'google_client_redirect_url',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'google_client_redirect_url'),
     'readonly'=>true
   )); ?>   
   <?php    
    echo $form->labelEx($model,'google_client_redirect_url'); ?>
   <?php echo $form->error($model,'google_client_redirect_url'); ?>
</div>-->

  </div> <!--body-->
</div> <!--card-->



<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>