
<div class="container mt-3">
	<div class="row w-75 m-auto">
	  <div class="col-md-3 d-flex align-items-center">
	    <img class="contain w-100" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/offers1@2x.png"?>" />
	  </div>
	  <div class="col-md-6 d-flex align-items-center">
	    <div class="w-100 text-center">
	     <h1>Offers for you</h1>
	     <h6 class="font-weight-normal">Explore top deals and offers exclusively for you!</h6>
	    </div>
	  </div>
	  <div class="col-md-3 d-flex align-items-center">
	    <img class="contain w-100" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/offers2@2x.png"?>" />
	  </div>
	</div>
</div> <!--container-->	

<div class="divider"></div>	
	
<div class="container mt-3 mb-3">
	
 <div class="section-offers mt-4 mb-5">   
   <?php 
   $this->widget('application.components.WidgetOwlCarousel',array(
     'owl_data'=>array('items'=>4,'lazyload'=>true,'loop'=>true,'margin'=>30,
         'next'=>'owl_next','prev'=>'owl_prev'
      ),
     'title'=> t("Best Discounts")." <span>Los angeles, california</span>",
     'owl_prev'=>'owl_next',
     'owl_next'=>'owl_prev',  
     'template'=>"promo"
   ));
  ?>
  </div> <!--sections-->	
  
  <div class="section-other-offers mt-5 mb-5">   
   <?php 
   $this->widget('application.components.WidgetOwlCarousel',array(
     'owl_data'=>array('items'=>4,'lazyload'=>true,'loop'=>true,'margin'=>25,
         'next'=>'owl_next2','prev'=>'owl_prev2'
      ),
     'title'=> t("Best Discounts")." <span>Los angeles, california</span>",
     'owl_prev'=>'owl_next2',
     'owl_next'=>'owl_prev2',     
     'template'=>"others-promo"  
   ));
  ?>
  </div> <!--sections-->	
	
</div> <!--container-->