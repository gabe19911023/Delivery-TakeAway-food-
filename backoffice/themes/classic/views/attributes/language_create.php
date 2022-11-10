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

<h6 class="mb-4"><?php echo t("Locale")?></h6>
<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'code', (array) $locale,array(
     'class'=>"form-control custom-select form-control-select locale selectpicker",
     'placeholder'=>$form->label($model,'code'),
     'data-live-search'=>true,
     'disabled'=>$model->scenario=="update"?true:false
   )); ?>         
   <?php echo $form->error($model,'code'); ?>
</div>

<h6 class="mb-4 mt-4"><?php echo t("Select Flag")?></h6>
<div id="flag">
<div class="form-label-group">    
<select class="selectpicker form-control form-control-text" data-live-search="true" name="AR_language[flag]">    
<?php foreach ($country_list as $code=>$val):?>
  <?php 
  $code = strtolower($code);
  $selected='';
  if($model->flag==$code){
  	$selected='selected';
  }
  ?>
  <option <?php echo $selected;?> data-content='<span class="text"><div class="">
    <img src="<?php echo websiteDomain().Yii::app()->baseUrl."/assets/flag/$code.svg"?>" class="mr-2">
  <span><?php echo $val?></span></div></span>'>
  <?php echo $code?>
  </option>
<?php endforeach;?>  
</select>
<?php echo $form->error($model,'code'); ?>
</div>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'title',array(
     'class'=>"form-control form-control-text locale_title",
     'placeholder'=>$form->label($model,'title')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'title'); ?>
   <?php echo $form->error($model,'title'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'description',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'description')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'description'); ?>
   <?php echo $form->error($model,'description'); ?>
</div>

<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"rtl",array(
     'class'=>"custom-control-input checkbox_child",     
     'value'=>1,
     'id'=>"rtl",
     'checked'=>$model->rtl==1?true:false
   )); ?>   
  <label class="custom-control-label" for="rtl">
   <?php echo t("RTL")?>
  </label>
</div>    

<div class="form-label-group">    
   <?php echo $form->numberField($model,'sequence',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'sequence')     
   )); ?>   
   <?php    
    echo $form->labelEx($model,'sequence'); ?>
   <?php echo $form->error($model,'sequence'); ?>
</div>

<div class="form-label-group">    
   <?php echo $form->dropDownList($model,'status', (array)$status_list,array(
     'class'=>"form-control custom-select form-control-select",     
     'placeholder'=>$form->label($model,'status'),
   )); ?>         
   <?php echo $form->error($model,'status'); ?>
</div>		


  </div> <!--body-->
</div> <!--card-->


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>