
<DIV class="mt-5">
<div class="card w-50 m-auto">
  <div class="card-body">
  
     <?php if($model->card_id>0):?>
     <h5>Card link successful</h5>     
     <p>You already have link cards in your account.</p>
     <?php else :?>
     <h5>Create Card</h5>     
     <p>There is no currently link cards in your account, click create below to proceed.</p>
     <?php endif;?>
          
      <?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'create_card',
		'enableAjaxValidation'=>false,		
	  ));	  
	  
	  ?>
	  		 
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
	
	 <?php echo $form->hiddenField($model,'card_uuid',array(
       'class'=>"form-control form-control-text",     
     )); ?>      
                    
     <?php if($model->card_id>0):?>
     <a href="<?php echo $return_link?>" class="btn btn-link">Return</a>
     <?php else:?>
     <button class="btn btn-success"          
      type="submit">Create My Card</button>
     <?php endif;?>
     
     <?php $this->endWidget(); ?>
  
  </div>
</div>
</DIV>