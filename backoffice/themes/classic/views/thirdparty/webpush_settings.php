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


<h6 class="mb-4"><?php echo t("Web push notifications")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"webpush_app_enabled",array(
     'class'=>"custom-control-input webpush_app_enabled",     
     'value'=>1,
     'id'=>"webpush_app_enabled",
     'checked'=>$model->webpush_app_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="webpush_app_enabled">
   <?php echo t("Enabled")?>
  </label>
</div>    

<h6 class="mt-4 mb-4"><?php echo t("Select Web Push Provider")?></h6>

<div class="radio-group-image parent">
<div class="row">

    <div class='col col-lg-3 col-md-4 col-sm-6 text-center'>    
    <?php 
     echo $form->radioButton($model,'webpush_provider',
     array('value'=>'pusher',
      'class'=>'d-none imgbgchk',     
      'id'=>"img1",
      'uncheckValue' => null,
    ));
    ?> 
      <label for="img1">
        <img class="border rounded" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/pusher.png"  >
        <div class="tick_container">
          <div class="tick"><i class="fa fa-check"></i></div>
        </div>        
        </label>
    <p class="m-0">Pusher</p>   
    </div>
    
    <div class='col col-lg-3 col-md-4 col-sm-6 text-center d-none'>   
        <?php 
	     echo $form->radioButton($model,'webpush_provider',
	     array('value'=>'onesignal',
	      'class'=>'d-none imgbgchk',     
	      'id'=>"img2",
	      'uncheckValue' => null,
	    ));
	    ?> 
          <label for="img2">
            <img class="border rounded" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/onesignal.jpg"  >
            <div class="tick_container">
              <div class="tick"><i class="fa fa-check"></i></div>
            </div>            
          </label>
    <p class="m-0">Onesignal</p>          
    </div>
    
   
    
</div>    
</div>
<!--row-->


<h6 class="mb-3 mt-4"><?php echo t("PUSHER")?></h6>

<div class="form-label-group">    
   <?php echo $form->textField($model,'pusher_instance_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'pusher_instance_id')     
   )); ?>   
   <?php    
    echo $form->label($model,'pusher_instance_id'); ?>
   <?php echo $form->error($model,'pusher_instance_id'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'pusher_primary_key',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'pusher_primary_key')     
   )); ?>   
   <?php    
    echo $form->label($model,'pusher_primary_key'); ?>
   <?php echo $form->error($model,'pusher_primary_key'); ?>   
</div>


<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>