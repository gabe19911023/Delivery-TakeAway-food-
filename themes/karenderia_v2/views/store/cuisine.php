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
	     <label for="search" class="required"><?php echo t("Search cuisine")?></label>   
	     
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


<div class="section-results list-items mt-4 row hover01">

   <div class="col-lg-3 col-md-3 mb-4 mb-lg-3">
      <a href="<?php echo Yii::app()->createUrl("/cuisine/american")?>">
      <div class="position-relative">
       <div class="skeleton-placeholder"></div>
       <figure><img class="rounded lazy" data-src="<?php echo CommonUtility::getPhoto('sample-1.jpg')?>"/></figure>
       <h6 class="mt-2">American (6)</h6>

	  <div class="position-top">
	      <span class="badge" style="background:#b8d6f2;">Pizza</span>
		  <span class="badge badge-dark rounded-pill float-right">4.0</span> 		  
	  </div>	  
      </div> <!--relative-->
      </a>
   </div> <!--col-->
      
    <div class="col-lg-3 col-md-3 mb-4 mb-lg-3">
      <a href="<?php echo Yii::app()->createUrl("/cuisine/american")?>">
      <div class="position-relative">
       <div class="skeleton-placeholder"></div>
       <figure><img class="rounded lazy" data-src="<?php echo CommonUtility::getPhoto('sample-2.jpg')?>"/></figure>
       <h6 class="mt-2">Diner  (1)</h6>

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
