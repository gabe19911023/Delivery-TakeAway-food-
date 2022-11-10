<!--TOP SEARCH -->
<div class="container-fluid mt-0 change-address-wrap">

<div class="container">
   <div class="d-flex align-self-center row">
      <div class="mr-auto">
      
        <h5 class="m-0">145 restaurants in Convent Street 2983</h5>
        <a href=""><?php echo t("Change Address")?></a>
       
      </div> <!-- div-->
      
      <div class="border w-25">
      
       <div class="form-label-group w-100 float-right width-auto m-0" id="search-form">    
	     <input class="form-control form-control-text form-control-text-white" placeholder="" id="search" type="text"  maxlength="40">   
	     <label for="search" class="required"><?php echo t("Search restaurant")?></label>   
	     
	     <div class="d-flex align-items-center filter">
	       <button class="btn shadow-none" type="submit">
	        <img class="img-20" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/magnifiying-glass.svg"?>" />
	       </button>
	     </div>
	     
       </div> <!--form group-->
      
      </div> <!--div-->
   </div> <!--flex-->
</div><!-- container-->

</div> <!--container-fluid-->
<!--TOP SEARCH -->


<div class="container">

<div class="d-flex border-bottom mt-2 justify-content-end">
 <div class="align-self-center">Filters</div>
  <div class="align-self-center">
    <a class="btn" data-toggle="collapse" href="#section-filter" role="button" aria-expanded="false" aria-controls="collapseExample">
     <img class="img-15" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/filter.svg"?>">
    </a>
  </div>
</div> <!--flex-->

<div id="section-filter" class="section-filter border-bottom pb-3 pt-3 collapse">
<div class="row">
  <div class="col ">
     <h6>Services</h6>
     
     <div class="row m-0">
     
     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
        <div class="custom-control custom-checkbox">
         <input type="checkbox" id="payment" name="payment" class="custom-control-input" checked >
         <label class="custom-control-label" for="payment">All</label>
       </div>   		      
     </div> 
     
     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
        <div class="custom-control custom-checkbox">
         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
         <label class="custom-control-label" for="payment">Delivery</label>
       </div>   		      
     </div> 
     
     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
        <div class="custom-control custom-checkbox">
         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
         <label class="custom-control-label" for="payment">Pickup</label>
       </div>   		      
     </div> 
     
     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
        <div class="custom-control custom-checkbox">
         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
         <label class="custom-control-label" for="payment">Dinein</label>
       </div>   		      
     </div> 
     
     </div> <!--row-->
     
  </div> <!--col-->
  
  <div class="col ">
     <h6>Cuisine</h6>
     
      <div class="row m-0">
     
	     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
	        <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" checked >
	         <label class="custom-control-label" for="payment">American</label>
	       </div>   		      
	     </div> 
	     
	     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
         <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
	         <label class="custom-control-label" for="payment">Deli</label>
	       </div>   		      
	     </div> 
	     
	     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
         <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
	         <label class="custom-control-label" for="payment">Indian</label>
	       </div>   		      
	     </div>  
	     
	     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
         <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
	         <label class="custom-control-label" for="payment">Sandiwches</label>
	       </div>   		      
	     </div>  
	     
	      <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		      
         <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
	         <label class="custom-control-label" for="payment">Mideterian</label>
	       </div>   		      
	     </div>  
     
	     <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 		                 
           <a class="btn btn-sm dropdown-toggle text-truncate shadow-none m-0 p-0 a-12" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
           More
           </a>
	     </div>  
	     
	     <div class="collapse multi-collapse col-lg-6 col-md-6 mb-4 mb-lg-3" id="multiCollapseExample1"> 		      
         <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
	         <label class="custom-control-label" for="payment">Sandiwches</label>
	       </div>   		      
	     </div>  	
	     
	     <div class="collapse multi-collapse col-lg-6 col-md-6 mb-4 mb-lg-3" id="multiCollapseExample1"> 		      
         <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
	         <label class="custom-control-label" for="payment">Sandiwches</label>
	       </div>   		      
	     </div>  
	     
	     <div class="collapse multi-collapse col-lg-6 col-md-6 mb-4 mb-lg-3" id="multiCollapseExample1"> 		      
         <div class="custom-control custom-checkbox">
	         <input type="checkbox" id="payment" name="payment" class="custom-control-input" >
	         <label class="custom-control-label" for="payment">Sandiwches</label>
	       </div>   		      
	     </div>  
	     
     </div><!-- row-->
     
  </div> <!--col-->
  
  <div class="col ">
     <h6>Minimum Order</h6>
     
      <div class="form-group">
	    <label for="formControlRange">Minimum selected <b><span class="min-selected-range"></span></b></label>
	    <input id="min_range_slider" value="5" type="range" class="custom-range" id="formControlRange"  min="1" max="20" >
	  </div>
     
  </div> <!--col-->
  
  <div class="col ">
     <h6>Ratings</h6>
     
      <div class="star-rating"
      data-totalstars="5"
      data-minrating="0"
      data-initialrating="0"
      data-strokecolor="#fedc79"
      data-ratedcolor="#fedc79"
      data-hovercolor="#fedc79"
      data-strokewidth="10"
      data-starsize="25"
      data-readonly="false"
      data-disableafterrate="false"
      data-usefullstars="true"
      >
      <input type="hidden" name="ratings_selected" id="ratings_selected" class="ratings_selected">
      </div>      
     
  </div> <!--col-->
    
</div> <!--row-->

<div class="mt-4 d-flex justify-content-end">
 <div class="mr-3"><a href="javascript:;" class="btn btn-green-line">Clear</a></div>
 <div><button class="btn btn-green">Show restaurants</button></div>
</div>

</div> <!--section-filter-->

<div class="section-results list-items mt-4 row hover01">

   <div class="col-lg-3 col-md-3 mb-4 mb-lg-3">
      <a href="">
      <div class="position-relative">
       <div class="skeleton-placeholder"></div>
       <figure><img class="rounded lazy" data-src="<?php echo CommonUtility::getPhoto('sample-1.jpg')?>"/></figure>
       <h6 class="mt-2">Spacca Napoli</h6>
	   
       <div class="row">    
	     <div class="col d-flex justify-content-start align-items-center">       
	        <p class="m-0"><i class="zmdi zmdi-time"></i> 40-50 min</p>
	     </div>
	     <div class="col d-flex justify-content-end align-items-center">        
	        <p class="m-0"><i class="zmdi zmdi-car"></i> $40.00</p>
	     </div> <!--col-->        
	  </div> <!--row-->
      
	  <div class="position-top">
	      <span class="badge" style="background:#b8d6f2;">Pizza</span>
		  <span class="badge badge-dark rounded-pill float-right">4.0</span> 		  
	  </div>
	  
      </div> <!--relative-->
      </a>
   </div> <!--col-->
      
  
</div> <!--section-results-->


<div class="section-most-popular mt-4 mb-0">   
  <?php 
   $this->widget('application.components.WidgetOwlCarousel',array(
     'owl_data'=>array('items'=>5,'lazyload'=>true,'loop'=>true,'margin'=>15,'next'=>'owl_next','prev'=>'owl_prev'),
     'title'=> t("New restaurant")." <span>Los angeles, california</span>",
     'owl_prev'=>'owl_prev',
     'owl_next'=>'owl_next',
     'template'=>"rounded"
   ));
  ?>
</div>


<div class="section-fast-delivery tree-columns-center">
  <div class="row">
  
  <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
      <div class="d-flex align-items-center">
       <div class="w-100">      
        <img class="rider mirror" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/rider.png"?>" />
       </div>
      </div>
   </div>  
   
    <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
      <div class="d-flex align-items-center">
       <div class="w-100 text-center">
       
         <h5>Fastest delivery in</h5>
         <h1 class="mb-4">Los angeles, california</h1>
         <p>Receive food in less than 20 minutes</p>   
       
         <a href="" class="btn btn-black w25">Check</a>
         
       </div>
      </div>
   </div>
   
  <div class="col-lg-4 col-md-4 mb-4 mb-lg-3">
      <div class="d-flex align-items-center">
       <div class="w-100 text-right">      
       <img class="rider" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/rider.png"?>" />
       </div>
      </div>
   </div>   
  
  </div> <!--row-->
</div> <!--section-fast-delivery-->

</div> <!-- container-->

<div class="container-fluid m-0 p-0 full-width">
 <?php $this->renderPartial("//store/join-us")?>
</div>
