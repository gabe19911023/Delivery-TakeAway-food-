<div class="<?php echo isset($class_name)?$class_name:''?>">    
   <a href="<?php echo Yii::app()->getBaseUrl(true);?>">
     <?php if(!empty($website_logo)):?>
      <img class="img-200" src="<?php echo $image_url;?>" />
     <?php else :?>
     <img class="img-200" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/logo@2x.png" />
     <?php endif?>
   </a>	  
</div>