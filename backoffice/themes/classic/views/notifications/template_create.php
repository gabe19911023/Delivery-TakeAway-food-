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

<!--<div class="form-label-group">    
   <?php echo $form->textField($model,'template_key',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'template_key'),
     'readonly'=>$model->isNewRecord?false:true
   )); ?>   
   <?php    
    echo $form->labelEx($model,'template_key'); ?>
   <?php echo $form->error($model,'template_key'); ?>
</div>-->

<div class="form-label-group">    
   <?php echo $form->textField($model,'template_name',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'template_name')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'template_name'); ?>
   <?php echo $form->error($model,'template_name'); ?>
</div>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_email",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_email",
     'checked'=>$model->enabled_email==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_email">
   <?php echo t("Enabled Email")?>
  </label>
</div>   

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_sms",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_sms",
     'checked'=>$model->enabled_sms==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_sms">
   <?php echo t("Enabled SMS")?>
  </label>
</div>   

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"enabled_push",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"enabled_push",
     'checked'=>$model->enabled_push==1?true:false
   )); ?>   
  <label class="custom-control-label" for="enabled_push">
   <?php echo t("Enabled Push")?>
  </label>
</div>   


  </div> <!--body-->
</div> <!--card-->


<!--EMAIL-->
<?php if($multi_language && is_array($language) && count($language)>=1 ):?>
<?php 
$this->widget('application.components.WidgetTemplates',array(
  'form'=>$form,
  'model'=>$model,
  'language'=>$language,
  'field'=>$fields_email,
  'data'=>$data['email'],
  'title'=>t("Email Templates"),
  'target'=>"email"
));
?>   
<?php endif;?>
<!--END EMAIL-->


<!--SMS-->
<?php if($multi_language && is_array($language) && count($language)>=1 ):?>
<?php 
$this->widget('application.components.WidgetTemplates',array(
  'form'=>$form,
  'model'=>$model,
  'language'=>$language,
  'field'=>$fields_sms,
  'data'=>$data['sms'],
  'title'=>t("SMS Templates"),
  'target'=>"sms"
));
?>   
<?php endif;?>
<!--END SMS-->


<!--PUSH-->
<?php if($multi_language && is_array($language) && count($language)>=1 ):?>
<?php 
$this->widget('application.components.WidgetTemplates',array(
  'form'=>$form,
  'model'=>$model,
  'language'=>$language,
  'field'=>$fields_push,
  'data'=>$data['push'],
  'title'=>t("Push Templates"),
  'target'=>"push"
));
?>   
<?php endif;?>
<!--END PUSH-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>