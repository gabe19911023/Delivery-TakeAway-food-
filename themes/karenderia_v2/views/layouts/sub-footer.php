<?php 

   $data = CMerchantListingV1::getMerchantInfo(Yii::app()->user->getState('rest_slug_name'),Yii::app()->language);       
?>
<div class="sub-footer">
  <div class="container">

    <div class="row">    

     <div class="col-xl-4 col-lg-4 col-md-6 col-6 d-flex justify-content-start align-items-center">       
       <?php 
       // $this->widget('application.components.WidgetSiteLogo',array(
       //   'class_name'=>'footer-logo'
       // ));
       ?>
       <div class="footer-logo">    
           <a href="https://geamenu.com/<?= Yii::app()->user->getState('rest_slug_name') ?>">
                   <img class="img-200" src="<?php echo $data['url_logo'];?>">
                </a>    
        </div>
     </div> <!--col-->
     
     <div class="col-xl-4 col-lg-4 col-md-6 col-6 d-flex 
     justify-content-end justify-content-lg-center align-items-center">
         <div class="d-flex align-items-center social-list">
            <?php if(!empty($data['facebook'])): ?>
                <div class="">
                  <a href="<?php echo $data['facebook']?>" class="facebook"><i class="zmdi zmdi-facebook"></i></a>
                </div>
            <?php endif; ?>
            <?php if(!empty($data['instagram'])): ?>
                <div class="ml-2 ml-md-3 ml-lg-4">
                  <a href="<?php echo $data['instagram']?>" class="instagram"><i class="zmdi zmdi-instagram"></i></a>
                </div>
            <?php endif; ?>
            <?php if(!empty($data['ticktok'])): ?>
                <div class="ml-2 ml-md-3 ml-lg-4">
                   <a href="<?php echo $data['ticktok']?>" class="ticktok">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 15.02px;margin-top: -2px;"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/></svg>
                   </a>
                </div>
            <?php endif; ?>
            <!-- <div class="ml-2 ml-md-3 ml-lg-4">
              <a href="#" class="linkedin"><i class="zmdi zmdi-linkedin"></i></a>
            </div>
            <div class="ml-2 ml-md-3 ml-lg-4">
               <a href="#" class="twitter"><i class="zmdi zmdi-twitter"></i></a>
            </div>
            <div class="ml-2 ml-md-3 ml-lg-4">
               <a href="#" class="youtube"><i class="zmdi zmdi-youtube-play"></i></a>
            </div> -->
         </div>
     </div> <!--col-->
     
     <div class="col-md-4 d-none d-lg-block">
       
     </div> <!--col-->        
    </div> <!--row-->
    
   <?php $this->widget('application.components.WidgetFooterMenu');?>
  
  </div> <!--container-->
</div> <!--sub-footer-->