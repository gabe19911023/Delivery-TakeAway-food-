<!--MAIN SEARCH BANNER-->
<div class="container-fluid d-flex justify-content-center" id="main-search-banner">

<div class="banner-center align-self-center d-none d-sm-block">

  <h2 class="text-center mb-3"><?php echo t("Let's find best food for you")?></h2>
  
  <!-- <DIV id="vue-home-search" class="home-search-wrap position-relative d-none d-lg-block">  
	  <component-home-search
	  ref="childref"
	  next_url="<?php echo Yii::app()->createAbsoluteUrl('store/restaurants')?>"
	  auto_generate_uuid = "true"
	  :label="{		    
		 enter_address: '<?php echo CJavaScript::quote(t("Enter delivery address"))?>', 		    
	  }"	    
	  />
	  </component-home-search>   
  </DIV> -->
  
</div>
<!--banner-center-->

</div>
<!--banner-center -->


</div> <!--container-->
<!--MAIN SEARCH BANNER-->

<!-- Mobile search  -->
<div class="d-block d-lg-none container mt-3">  
 <DIV id="vue-home-search-mobile" class="position-relative">  
	  <component-home-search
	  ref="childref"
	  next_url="<?php echo Yii::app()->createAbsoluteUrl('store/restaurants')?>"
	  auto_generate_uuid = "true"
	  :label="{		    
		 enter_address: '<?php echo CJavaScript::quote(t("Enter delivery address"))?>', 		    
	  }"	    
	  />
	  </component-home-search>   
  </DIV>
</div>
<!-- mobile search -->

<DIV id="vue-home-widgets" >

<div class="container mt-4 mb-3" v-cloak >
  
  <h6 class="mb-3"><?php echo t("Cuisine type")?>:</h6>  

  <!-- cuisine list -->
  <div class="d-none d-lg-block">
  <div class="row no-gutters list-inline"> 

    <template v-for="(cuisine, index) in data_cuisine" >
    <div v-if="index<=7" class="col">
      <a > {{ cuisine.cuisine_name }}</a>
    </div>    
    </template>
    
    <template v-if="data_cuisine.length" >
    <template v-if="data_cuisine[8]" >
    <div class="col">          
      <a class="btn btn-sm dropdown-toggle text-truncate shadow-none" 
      href="javascript:;" id="dropdownCuisine" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <?php echo t("More")?>
	  </a>	  
	  
	  <div class="dropdown-menu" aria-labelledby="dropdownCuisine">
	     <template v-for="(cuisine, index) in data_cuisine.slice(8)" >
	     <a class="p-2 pl-2 d-block "  >
	       {{ cuisine.cuisine_name }}
	     </a>	     
	     </template>
	  </div>	  
    </div> <!--col-->   
    </template>
    </template>
    
  </div> <!--row-->
  </div>
  <!-- cuisine list -->

  <div class="d-block d-lg-none">
     <component-cuisine
     :data="data_cuisine"
     :responsive='<?php echo json_encode($responsive);?>'
     ></component>
  </div>
 
    
  <!--COMPONENTS FEATURED LOCATION-->
  <component-carousel
  title="<?php echo t("Popular nearby")?>"
  featured_name="popular"
  :settings="{
      theme: '<?php echo CJavaScript::quote('rounded')?>',       
      items: '<?php echo CJavaScript::quote(5)?>', 
      lazyLoad: '<?php echo CJavaScript::quote(true)?>', 
      loop: '<?php echo CJavaScript::quote(true)?>', 
      margin: '<?php echo CJavaScript::quote(15)?>', 
      nav: '<?php echo CJavaScript::quote(false)?>', 
      dots: '<?php echo CJavaScript::quote(false)?>', 
      stagePadding: '<?php echo CJavaScript::quote(10)?>', 
      free_delivery: '<?php echo CJavaScript::quote( t("Free delivery") )?>',       
  }"
  :responsive='<?php echo json_encode($responsive);?>'
  />
  </component-carousel>  
  <!--COMPONENTS FEATURED LOCATION-->
    
  
  <!-- order 3 steps -->
  <div class="order-three-steps d-none d-lg-block">  
  <div class="section-addons row mt-4 mb-0">      
      <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
        <div class="addons addons-1">
	        <div class="inner">
	        <h1>01</h1>
	        <h5><?php echo t("No Minimum Order")?></h5>
	        <p><?php echo t("Order in for yourself or for the group, with no restrictions on order value")?></p>
	        </div>
        </div>
      </div>
      
       <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
        <div class="addons addons-2">
	        <div class="inner">
	        <h1>02</h1>
	        <h5><?php echo t("Live Order Tracking")?></h5>
	        <p><?php echo t("Know where your order is at all times, from the restaurant to your doorstep")?></p>
	        </div>
        </div>
      </div>
      
       <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
        <div class="addons addons-3">
	        <div class="inner">
	        <h1>03</h1>
	        <h5><?php echo t("Lightning-Fast Delivery")?></h5>
	        <p><?php echo t("Experience karenderia superfast delivery for food delivered fresh & on time")?></p>
	        </div>
        </div>
      </div>            
   </div> <!--ordering-steps-->
   </div> <!-- order 3 steps -->   

    <!-- order 3 steps mobile -->
   <div class="d-block d-lg-none">
     <component-three-steps      
     >      
     </component-three-steps>
   </div>
   <!-- order 3 steps mobile -->
   
   <!-- section-benefits -->
   <div class="section-benefits mt-3 mb-0 row">   
   
      <div class="col-lg-3 col-md-3 col-sm-6 mb-4 mb-lg-3">
        <div class="benefits benefits-1">
           <div class="inner">  
             <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4><?php echo t("Best promotions in your area")?></h4></div>
               <div>
               <p class="m-0"><?php echo t("Up to")?></p>
               <h4>50%</h4>
               </div>
               <div class="mt-auto"><div class="btn-white-parent"><a  class="btn btn-link"><?php echo t("Check")?></a></div></div>
             </div>
           </div> <!--inner-->
        </div> <!--benefits-->
      </div> <!--col-->
      
      <div class="col-lg-3 col-md-3 col-sm-6 mb-4 mb-lg-3">
        <div class="benefits benefits-2">
           <div class="inner">  
             <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4><?php echo t("Rising stars restaurants")?></h4></div>
               <div>
               <p class="m-0"><?php echo t("Try something")?></p>
               <h4><?php echo t("New")?></h4>
               </div>
               <div class="mt-auto"><div class="btn-white-parent"><a class="btn btn-link"><?php echo t("Check")?></a></div></div>
             </div>
           </div> <!--inner-->
        </div> <!--benefits-->
      </div> <!--col-->
      
      <div class="col-lg-3 col-md-3  col-sm-6 mb-4 mb-lg-3">
        <div class="benefits benefits-3">
           <div class="inner">  
             <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4><?php echo t("Fastest delivery for you!")?></h4></div>
               <div>
               <p class="m-0"><?php echo t("Best quick")?></p>
               <h4><?php echo t("Lunch")?></h4>
               </div>
               <div class="mt-auto"><div class="btn-white-parent"><a class="btn btn-link"><?php echo t("Check")?></a></div></div>
             </div>
           </div> <!--inner-->
        </div> <!--benefits-->
      </div> <!--col-->
      
      <div class="col-lg-3 col-md-3  col-sm-6 mb-4 mb-lg-3">
        <div class="benefits benefits-3">
           <div class="inner">  
             <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4><?php echo t("Party night?")?></h4></div>
               <div>
               <p class="m-0"><?php echo t("Maybe")?></p>
               <h4><?php echo t("Snacks?")?></h4>
               </div>
               <div class="mt-auto"><div class="btn-white-parent"><a  class="btn btn-link"><?php echo t("Check")?></a></div></div>
             </div>
           </div> <!--inner-->
        </div> <!--benefits-->
      </div> <!--col--> 
            
   </div> <!--section-benefits-->
      
   <!--COMPONENTS FEATURED LOCATION-->
  <component-carousel
  title="<?php echo t("New restaurant")?>"
  featured_name="new"
  :settings="{
      theme: '<?php echo CJavaScript::quote('rounded-circle')?>',      
      items: '<?php echo CJavaScript::quote(6)?>',      
      lazyLoad: '<?php echo CJavaScript::quote(true)?>', 
      loop: '<?php echo CJavaScript::quote(true)?>', 
      margin: '<?php echo CJavaScript::quote(15)?>', 
      nav: '<?php echo CJavaScript::quote(false)?>', 
      dots: '<?php echo CJavaScript::quote(false)?>', 
      stagePadding: '<?php echo CJavaScript::quote(10)?>', 
      free_delivery: '<?php echo CJavaScript::quote( t("Free delivery") )?>', 
  }"
  :responsive='<?php echo json_encode($responsive);?>'
  />
  </component-carousel>  
  <!--COMPONENTS FEATURED LOCATION-->
  
  <!--JOIN US-->
  <div class="mt-4">
  <?php //$this->renderPartial("//store/join-us")?>
  </div>
  <!--END JOIN US-->
      
</div> <!--container-->


<div class="section-mobileapp tree-columns-center d-none d-md-block"> 
<div class="container">
   <div class="mb-0 row">
   
   <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
      <div class="d-flex align-items-center">
       <div class="w-100 text-center text-md-left">
         <h5><?php echo t("Best restaurants")?></h5>
         <h1 class="mb-4"><?php echo t("In your pocket")?></h1>
         <p class=""><?php echo t("Order from your favorite restaurants & track on the go, with the all-new K app.")?></p>
       </div>
      </div>
   </div>  
   
   <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
      <div class="d-flex align-items-center">
       <div class="w-100 text-center">
          <img class="mobileapp" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/mobileapp.png"?>" />
       </div>
      </div>
   </div>
   
    <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
      <div class="d-flex align-items-center">
       <div class="w-100 text-center text-md-right">
         <h5><?php echo t("Download")?></h5>
         <h1 class="mb-4"><?php echo t("K mobile app")?></h1>
         
         <div class="app-store-wrap">
           <a href="#" class="d-inline mr-2">
		        <img src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/app-store@2x.png">
		       </a>
          <a href="#" class="d-inline">
            <img src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/google-play@2x.png">
          </a>
         </div>
         
       </div>
      </div>
   </div>
   
   </div> <!--row-->
</div> <!--container-->
</div> <!--sections-->

<!-- section mobile app view -->
<div class="d-block d-md-none">
  <div class="section-mobileapp border"> 
     <div class="container text-center"> 
     
         <h5><?php echo t("Best restaurants")?></h5>
         <h1 class="mb-3"><?php echo t("In your pocket")?></h1>
         <p class=""><?php echo t("Order from your favorite restaurants & track on the go, with the all-new K app.")?></p>

         <div class="d-flex justify-content-center app-store-wrap mb-5 mt-4">
           <div class="mr-2">
            <a href="#" class="d-inline mr-2">
              <img src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/app-store@2x.png">
            </a>
           </div>
           <div class="">
            <a href="#" class="d-inline">
              <img src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/google-play@2x.png">
            </a>
           </div>
         </div>

         <img class="mobileapp" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/mobileapp-half.png"?>" />

     </div>
   </div>
</div>
<!-- section mobile app view -->


<div class="container">

   <!--COMPONENTS FEATURED LOCATION-->
  <component-carousel
  title="<?php echo t("Try something new in")?>"
  featured_name="best_seller"
  :settings="{
      theme: '<?php echo CJavaScript::quote('rounded')?>',       
      items: '<?php echo CJavaScript::quote(5)?>', 
      lazyLoad: '<?php echo CJavaScript::quote(true)?>', 
      loop: '<?php echo CJavaScript::quote(true)?>', 
      margin: '<?php echo CJavaScript::quote(15)?>', 
      nav: '<?php echo CJavaScript::quote(false)?>', 
      dots: '<?php echo CJavaScript::quote(false)?>', 
      stagePadding: '<?php echo CJavaScript::quote(10)?>', 
      free_delivery: '<?php echo CJavaScript::quote( t("Free delivery") )?>', 
  }"
  :responsive='<?php echo json_encode($responsive);?>'
  />
  </component-carousel>  
  <!--COMPONENTS FEATURED LOCATION-->

</div> <!--container-->

</DIV>
<!--vue-home-widgets-->


<script type="text/x-template" id="three-steps-ordering">

  <div ref="carousel_three_steps" class="section-addons carousel-three-steps owl-carousel owl-theme">

    <div class="mr-2">
       <div class="addons addons-1">
	        <div class="inner">
	        <h1>01</h1>
	        <h5><?php echo t("No Minimum Order")?></h5>
	        <p><?php echo t("Order in for yourself or for the group, with no restrictions on order value")?></p>
	        </div>
        </div>
    </div> 
    <!-- item -->

    <div class="mr-2">
       <div class="addons addons-2">
	        <div class="inner">
	        <h1>02</h1>
	        <h5><?php echo t("Live Order Tracking")?></h5>
	        <p><?php echo t("Know where your order is at all times, from the restaurant to your doorstep")?></p>
	        </div>
        </div>
    </div>
    <!-- item -->

    <div class="">
       <div class="addons addons-3">
	        <div class="inner">
	        <h1>03</h1>
	        <h5><?php echo t("Lightning-Fast Delivery")?></h5>
	        <p><?php echo t("Experience karenderia superfast delivery for food delivered fresh & on time")?></p>
	        </div>
        </div>
    </div>
    <!-- item -->

  </div> 
  <!-- carousel -->
</script>