<?php 

   $data = CMerchantListingV1::getMerchantInfo(Yii::app()->user->getState('rest_slug_name'),Yii::app()->language);       
?>
<?php $this->beginContent('/layouts/main-layout'); ?>

<!--TOP NAV-->
<!--TOP SECTION-->
<div class="container-fluid">
 <div id="top-navigation" class="row" >
    <div id="vue-checkout-back" v-cloak class="col-lg-auto col-md-6 col-6 d-flex justify-content-start align-items-center">    
      <a :href="back_url" class="back-arrow text-green"><?php echo t("Back to Menu");?></a>
    </div>
    <div class="col-lg-9 col-md-6 col-6 d-flex justify-content-start justify-content-lg-center align-items-center">            
       <?php 
       // $this->widget('application.components.WidgetSiteLogo',array(
       //   'class_name'=>'top-logo'
       // ));
       ?>
       <div class="top-logo">    
         <a href="https://geamenu.com/<?= Yii::app()->user->getState('rest_slug_name') ?>">
                 <img class="img-200" src="<?php echo $data['url_logo'];?>">
              </a>    
      </div>
    </div> <!--col-->            
 </div><!-- row-->
</div> <!--container-->
<!--END TOP SECTION-->
<!--END TOP NAV-->

<!--MAIN CONTENT-->
<div class="page-content">
<?php echo $content; ?>
</div>
<!--END MAIN CONTENT-->

<!--SUB FOOTER-->
<?php $this->renderPartial("//layouts/sub-footer")?>
<!--END SUB FOOTER-->

<!--FOOTER-->
<?php $this->renderPartial("//layouts/footer")?>
<!--END FOOTER-->

<?php $this->endContent(); ?>