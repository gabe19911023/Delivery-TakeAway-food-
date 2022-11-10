<?php 

   $data = CMerchantListingV1::getMerchantInfo(Yii::app()->user->getState('rest_slug_name'),Yii::app()->language);       
?>
<!--TOP SECTION-->
<div class="container-fluid">
 <div id="top-navigation" class="row" >

    <div class=" col-lg-auto col-md-6 col d-flex justify-content-start align-items-center">  
          <div class="top-logo">    
            <a href="https://geamenu.com/<?= Yii::app()->user->getState('rest_slug_name') ?>">
                    <img class="img-200" src="<?php echo $data['url_logo'];?>">
                 </a>     
         </div>        
       <?php 
       // $this->widget('application.components.WidgetSiteLogo',array(
       //   'class_name'=>'top-logo'
       // ));
       ?>
    </div> <!--col-->
      <div class=" col d-none d-lg-block">  
        <div class="d-flex justify-content-start align-items-center"></div>
      </div>
   <!--  <div id="vue-widget-nav" class=" col d-none d-lg-block">    
      <div class="d-flex justify-content-start align-items-center">
      <?php       
      if(!empty($widget_col1)){
         // $this->renderPartial("//components/$widget_col1");
      }
      ?>   
      </div>     
    </div> -->

    <div class=" col-lg-auto col-md-6 col d-flex justify-content-end align-items-center">          
     <?php           
     if(!empty($widget_col2)){        
    	 $this->renderPartial("//components/$widget_col2");
     }
     ?>
     <?php $this->widget('application.components.WidgetUserNav');?>    
    </div> <!--col-->

 </div><!-- row-->

 <!-- mobile view --> 
 <?php 
 $action_id = Yii::app()->controller->action->id;
 if($action_id=="restaurants" || $action_id=="menu"){
    $this->renderPartial("//components/widget-subnav");
 }
 ?>
 <!-- mobile view -->

</div> <!--container-->
<!--END TOP SECTION-->