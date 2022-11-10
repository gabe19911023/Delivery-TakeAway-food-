<?php $image = $data['footer_image'];?>
<div class="section-join-us mt-0 mb-0" style="background: url('<?= $image ?>') no-repeat rgb(246, 247, 248);">        
 <div class="d-flex align-items-start flex-column">
   <div class="mb-auto"></div>
   <div class="w-100 text-center" style="visibility:hidden;">
     <h5><?php echo t("Are you a restaurant owner?")?></h5>
     <h1><?php echo t("Join us and reach new customers")?></h1>
     <h5 class="mb-4"><?php echo t("Just a few steps to join our family")?></h5>
     <div class="btn-white-parent non-trasparent"><a href="<?php echo Yii::app()->createUrl('/merchant')?>"
      class="btn btn-link w25"><?php echo t("Join")?></a></div>
   </div>
   <div class="mt-auto border"></div>
 </div>
</div> <!--sections-->