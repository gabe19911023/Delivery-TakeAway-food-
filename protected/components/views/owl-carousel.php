<!--POPULAR CAROUSEL-->  
<?php 
$atts = '';
if(is_array($this->owl_data) && count($this->owl_data)>=1){
	foreach ($this->owl_data as $owl_data_key=>$owl_data_val) {
		$atts.="data-$owl_data_key=\"$owl_data_val\" ";
	}	
}
?>
  <DIV class="owl-carousel-parent" <?php echo $atts;?> >
  
	  <div class="row" >    
	     <div class="col d-flex justify-content-start align-items-center">       
	       <h5 class="mb-4 section-title">
	       <?php echo $this->title?>
	       </h5>
	     </div>
	     <div class="col d-flex justify-content-end align-items-center">        
	        <a href="javascript:;" class="owl-carousel-nav <?php echo $this->owl_prev?> mr-4"><i class="zmdi zmdi-long-arrow-left"></i></a>
	        <a href="javascript:;" class="owl-carousel-nav <?php echo $this->owl_next?>"><i class="zmdi zmdi-long-arrow-right"></i></a>
	     </div> <!--col-->        
	  </div> <!--row-->
	  
	  <?php if($this->template=="promo"):?>
	  
	  <div class="owl-carousel owl-theme">	 
	     <div class="owl_link section-promo">	      
	      <img class="owl-lazy <?php echo $this->template?>" data-src="<?php echo CommonUtility::getPhoto('promo-1.png')?>" alt="">
	      <div class="inner">
	      
	         <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4>Your first osahan eat order</h4></div>
               <div>               
               <h2 class="font-weight-bold">50%</h2>
               </div>
               <div class="mt-auto">
                  <p>Osahaneat50</p>
                  <div class="btn-white-parent"><a href="" class="btn btn-link">Copy code</a></div>
               </div>
             </div>  
	      
	      </div> <!--inner-->
	     </div> <!--owl_link-->
	     
	     <div class="owl_link section-promo">	      
	      <img class="owl-lazy <?php echo $this->template?>" data-src="<?php echo CommonUtility::getPhoto('promo-2.png')?>" alt="">
	      <div class="inner">
	      
	         <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4>Your first osahan eat order</h4></div>
               <div>               
               <h2 class="font-weight-bold">50%</h2>
               </div>
               <div class="mt-auto">
                  <p>Osahaneat50</p>
                  <div class="btn-white-parent"><a href="" class="btn btn-link">Copy code</a></div>
               </div>
             </div>  
	      
	      </div> <!--inner-->
	     </div> <!--owl_link-->
	     
	     <div class="owl_link section-promo">	      
	      <img class="owl-lazy <?php echo $this->template?>" data-src="<?php echo CommonUtility::getPhoto('promo-3.png')?>" alt="">
	      <div class="inner">
	      
	         <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4>Your first osahan eat order</h4></div>
               <div>               
               <h2 class="font-weight-bold">50%</h2>
               </div>
               <div class="mt-auto">
                  <p>Osahaneat50</p>
                  <div class="btn-white-parent"><a href="" class="btn btn-link">Copy code</a></div>
               </div>
             </div>  
	      
	      </div> <!--inner-->
	     </div> <!--owl_link-->
	     
	     <div class="owl_link section-promo">	      
	      <img class="owl-lazy <?php echo $this->template?>" data-src="<?php echo CommonUtility::getPhoto('promo-4.png')?>" alt="">
	      <div class="inner">
	      
	         <div class="d-flex align-items-start flex-column">
               <div class="mb-auto"><h4>Your first osahan eat order</h4></div>
               <div>               
               <h2 class="font-weight-bold">50%</h2>
               </div>
               <div class="mt-auto">
                  <p>Osahaneat50</p>
                  <div class="btn-white-parent"><a href="" class="btn btn-link">Copy code</a></div>
               </div>
             </div>  
	      
	      </div> <!--inner-->
	     </div> <!--owl_link--> 
	    
	  </div><!-- owl-carousel-->
	  
	  
	  <?php elseif ( $this->template=="others-promo" ):?>
	  <div class="owl-carousel owl-theme">	
	  
	     <div class="other-promo-wrap text-center">	     
	       <div class="position-relative">
	         <img class="owl-lazy rounded-circle" data-src="<?php echo CommonUtility::getPhoto('sample-1.jpg')?>" alt="">
	         <span class="badge big top-right rounded-pill" style="background:#e99895;">25%</span>
	       </div>	       
	       <h6 class="mt-2">Spacca Napoli</h6>
	       <p class="badge" style="background:#e99895;">Fast food</p>	       
	       <p class="text-grey">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the indus 1500s</p>	       
	       <a href="javascript:;" class="a-12"><u>Copy code</u></a>	      
	     </div> <!--other-promo-wrap--> 
	     
	     <div class="other-promo-wrap text-center">	     
	       <div class="position-relative">
	         <img class="owl-lazy rounded-circle" data-src="<?php echo CommonUtility::getPhoto('sample-2.jpg')?>" alt="">
	         <span class="badge big top-right rounded-pill" style="background:#d4ecdc;">25%</span>
	       </div>	       
	       <h6 class="mt-2">Spacca Napoli</h6>
	       <p class="badge" style="background:#d4ecdc;">Fast food</p>	       
	       <p class="text-grey">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the indus 1500s</p>	       
	       <a href="javascript:;" class="a-12"><u>Copy code</u></a>	      
	     </div> <!--other-promo-wrap--> 
	     
	  </div><!-- owl-carousel-->
	  <?php else :?>	  
	  <div class="owl-carousel owl-theme hover13">	  
	  
	    <?php if(isset($this->data['data'])):?>
		<?php foreach ($this->data['data'] as $val):?>
		 <a href="<?php echo $val['url']?>" class="owl_link <?php echo $this->template=="rounded-circle"?"text-center":'';?>">		
		 <figure><img class="owl-lazy <?php echo $this->template?>" data-src="<?php echo CommonUtility::getPhoto($val['logo'])?>" alt=""></figure>
		 <h6 class="mt-2"><?php echo stripslashes($val['restaurant_name'])?></h6>
		 <p class="m-0"><i class="zmdi zmdi-time"></i> <?php echo $val['delivery_estimation']?></p>	
		 <?php if($this->template=="rounded-circle"):?>		 
			 <?php if(isset($val['cuisine']['cuisine_name'])):?>
			 <div class="text-center"><p class="badge badge-success"><?php echo stripslashes($val['cuisine']['cuisine_name'])?></p></div>	 
			 <?php endif;?>			 			 
			 <span class="badge top-right badge-dark rounded-pill"><?php echo $val['ratings']?></span>		 
		 <?php endif;?>
		</a>	
		<?php endforeach;?>
		<?php endif;?>
		
	  </div><!-- owl-carousel-->
	   <?php endif;?>
	  
  </DIV>
  <!--END POPULAR CAROUSEL-->  