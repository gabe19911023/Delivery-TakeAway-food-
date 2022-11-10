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


<h6 class="mb-4"><?php echo t("Real time applications")?></h6>
<div class="custom-control custom-switch custom-switch-md">  
  <?php echo $form->checkBox($model,"realtime_app_enabled",array(
     'class'=>"custom-control-input realtime_app_enabled",     
     'value'=>1,
     'id'=>"realtime_app_enabled",
     'checked'=>$model->realtime_app_enabled==1?true:false
   )); ?>   
  <label class="custom-control-label" for="realtime_app_enabled">
   <?php echo t("Enabled")?>
  </label>
</div>    

<h6 class="mt-4 mb-4"><?php echo t("Select Realtime Provider")?></h6>

<div class="radio-group-image  parent ">
<div class="row">

    <div class='col col-lg-3 col-md-4 col-sm-6 text-center'>    
    <?php 
     echo $form->radioButton($model,'realtime_provider',
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
    <p class="m-">pusher</p>    
    </div>
    
    <div class='col col-lg-3 col-md-4 col-sm-6 text-center'>   
        <?php 
	     echo $form->radioButton($model,'realtime_provider',
	     array('value'=>'ably',
	      'class'=>'d-none imgbgchk',     
	      'id'=>"img2",
	      'uncheckValue' => null,
	    ));
	    ?> 
          <label for="img2">
            <img class="border rounded" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/ably.jpg"  >
            <div class="tick_container">
              <div class="tick"><i class="fa fa-check"></i></div>
            </div>            
          </label>
    <p class="m-">ably</p>         
    </div>
    
    <div class='col col-lg-3 col-md-4 col-sm-6 text-center'>   
        <?php 
	     echo $form->radioButton($model,'realtime_provider',
	     array('value'=>'piesocket',
	      'class'=>'d-none imgbgchk',     
	      'id'=>"img3",
	      'uncheckValue' => null,
	    ));
	    ?> 
          <label for="img3">
            <img class="border rounded" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/piesocket.png"  >
            <div class="tick_container">
              <div class="tick"><i class="fa fa-check"></i></div>
            </div>            
          </label>
    <p class="m-">piesocket</p>                   
    </div>
    
</div>    
</div>
<!--row-->

<h6 class="mb-0 mt-4"><?php echo t("PUSHER")?></h6>
<p><small><?php echo t("signup and get your credentials in")?> <a href="http://pusher.com" target="_blank">pusher.com</a></small></p>

<div class="form-label-group">    
   <?php echo $form->textField($model,'pusher_app_id',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'pusher_app_id')     
   )); ?>   
   <?php    
    echo $form->label($model,'pusher_app_id'); ?>
   <?php echo $form->error($model,'pusher_app_id'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'pusher_key',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'pusher_key')     
   )); ?>   
   <?php    
    echo $form->label($model,'pusher_key'); ?>
   <?php echo $form->error($model,'pusher_key'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'pusher_secret',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'pusher_secret')     
   )); ?>   
   <?php    
    echo $form->label($model,'pusher_secret'); ?>
   <?php echo $form->error($model,'pusher_secret'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'pusher_cluster',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'pusher_cluster')     
   )); ?>   
   <?php    
    echo $form->label($model,'pusher_cluster'); ?>
   <?php echo $form->error($model,'pusher_cluster'); ?>   
</div>

<hr/>

<h6 class="mb-0 mt-4"><?php echo t("ABLY")?></h6>
<p><small><?php echo t("signup and get your credentials in")?> <a href="https://ably.com/sign-up" target="_blank">ably.com</a></small></p>

<div class="form-label-group">    
   <?php echo $form->textField($model,'ably_apikey',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'ably_apikey')     
   )); ?>   
   <?php    
    echo $form->label($model,'ably_apikey'); ?>
   <?php echo $form->error($model,'ably_apikey'); ?>   
</div>

<hr/>
<h6 class="mb-0 mt-4"><?php echo t("PIESOCKET")?></h6>
<p><small><?php echo t("signup and get your credentials in")?> <a href="https://www.piesocket.com/register?plan=free&c=200&m=1" target="_blank">piesocket.com</a></small></p>

<div class="form-label-group">    
   <?php echo $form->textField($model,'piesocket_clusterid',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'piesocket_clusterid')     
   )); ?>   
   <?php    
    echo $form->label($model,'piesocket_clusterid'); ?>
   <?php echo $form->error($model,'piesocket_clusterid'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'piesocket_api_key',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'piesocket_api_key')     
   )); ?>   
   <?php    
    echo $form->label($model,'piesocket_api_key'); ?>
   <?php echo $form->error($model,'piesocket_api_key'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'piesocket_api_secret',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'piesocket_api_secret')     
   )); ?>   
   <?php    
    echo $form->label($model,'piesocket_api_secret'); ?>
   <?php echo $form->error($model,'piesocket_api_secret'); ?>   
</div>

<div class="form-label-group">    
   <?php echo $form->textField($model,'piesocket_websocket_api',array(
     'class'=>"form-control form-control-text",
     'placeholder'=>$form->label($model,'piesocket_websocket_api')     
   )); ?>   
   <?php    
    echo $form->label($model,'piesocket_websocket_api'); ?>
   <?php echo $form->error($model,'piesocket_websocket_api'); ?>   
</div>

<hr/>

<?php echo CHtml::submitButton('submit',array(
'class'=>"btn btn-green btn-full mt-3",
'value'=>t("Save")
)); ?>

<?php $this->endWidget(); ?>