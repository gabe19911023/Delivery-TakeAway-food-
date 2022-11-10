<div id="vue-feed" class="container-fluid" >


<!-- search and filter mobile view -->
<div id="feed-search-mobile" class="d-block d-lg-none mt-2 mb-3">

  <div class="position-relative inputs-box-wrap">
     <input @click="showSearchSuggestion" class="inputs-box-grey rounded" placeholder="<?php echo t("Search")?>">
     <div class="search_placeholder pos-right img-15"></div>       
	 <div class="filter_wrap"><a @click="showFilter" class="filter_placeholder btn"></a></div>
  </div>

</div>
<!-- search and filter mobile view -->

<component-filter-feed
ref="filter_feed"
@after-filter="afterApplyFilter"
:data_attributes="data_attributes"
:data_cuisine="data_cuisine"
:label="{		    
	filters: '<?php echo CJavaScript::quote(t("Filters"))?>', 
	price_range: '<?php echo CJavaScript::quote(t("Price range"))?>', 		    
	cuisine: '<?php echo CJavaScript::quote(t("Cuisines"))?>', 
	max_delivery_fee: '<?php echo CJavaScript::quote(t("Max Delivery Fee"))?>', 
	delivery_fee: '<?php echo CJavaScript::quote(t("Delivery Fee"))?>', 
	ratings: '<?php echo CJavaScript::quote(t("Ratings"))?>', 
	over: '<?php echo CJavaScript::quote(t("Over"))?>', 
	done: '<?php echo CJavaScript::quote(t("Done"))?>', 
	clear_all: '<?php echo CJavaScript::quote(t("Clear all"))?>', 	
}"	    
>
</component-filter-feed>

<component-mobile-search-suggestion
ref="search_suggestion"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
:tabs_suggestion='<?php echo json_encode($tabs_suggestion)?>'
:label="{		    
	clear: '<?php echo CJavaScript::quote(t("Clear"))?>', 
	search: '<?php echo CJavaScript::quote(t("Search"))?>', 		    
	no_results: '<?php echo CJavaScript::quote(t("No results"))?>',	
}"	    
>
</component-mobile-search-suggestion>

<template v-if="response_code==1">
<div class="row mt-2 mt-lg-4 row mb-4">
  
 <div class="col-lg-3 col-md-3 d-none d-lg-block column-1">
 
   <el-skeleton :loading="data_attributes_loading" animated :count="4">
   <template #template>
       <div class="mb-2"><el-skeleton :rows="5" /></div>
   </template>
   <template #default>   

   <div class="d-flex justify-content-between align-items-center mb-2" >
     <div class="flex-col"><h4 class="m-0" v-cloak >{{this.total_message}}</h4></div>
     <div class="flex-col" v-if="hasFilter"  v-cloak  >
       <a href="javascript:;" @click="clearFilter" ><p class="m-0" ><u><?php echo t("Clear all")?></u></p></a>
     </div>
   </div>
 
   <div class="accordion section-filter" id="sectionFilter">
   
    <!--SORT-->    
    <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterSort" class="d-block" data-toggle="collapse" aria-expanded="true" aria-controls="filterSort"  >
     <?php echo t("Filter")?>
     </a>
     </h5>   
   
     <div id="filterSort" class="collapse show" aria-labelledby="headingOne" v-cloak >   
     
      <div v-for="(sort_by, key) in data_attributes.sort_by" class="row m-0 ml-2 mb-2">
		<div class="custom-control custom-radio">
	      <input @click="AutoFeed"  v-model="sortby" :value="key" type="radio" :id="key" name="sort" class="custom-control-input">
	      <label class="custom-control-label" :for="key">{{sort_by}}</label>
		 </div>   		      
	  </div><!--row-->  
      
     </div> <!--filterSort-->   
   </div> <!--filter-row-->
   <!--END SORT-->
   
   
     <!--PRICE-->
   <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterPrice" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterPrice"  >
     <?php echo t("Price range")?>
     </a>
   </h5>   
   
    <div id="filterPrice" class="collapse" :class="{show:collapse}" aria-labelledby="headingOne" >
        
       <div class="btn-group btn-group-toggle input-group-small mt-2 mb-2" >
          <label  v-for="(price, key) in data_attributes.price_range" class="btn" :class="{ active: price_range==key }"   >
             <input @click="AutoFeed"  type="radio" :value="key" name="price_range" v-model="price_range"> 
             {{price}}
           </label>                                               
       </div>
   
     </div> <!-- filterCuisine-->  
   </div> <!--filter-row-->
   
   <!--END PRICE-->
   
   
   <!--CUISINE-->
   
     <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterCuisine" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterCuisine"  >
     <?php echo t("Cuisines")?>
     </a>
     </h5>   
   
     <div id="filterCuisine" class="collapse" :class="{show:collapse}" aria-labelledby="headingOne" >
            
        <div class="row m-0">              
            <template v-for="(item_cuisine, index) in data_cuisine" >         
	        <div class="col-lg-6 col-md-6 mb-4 mb-lg-3" v-if="index<=5">
	         <div class="custom-control custom-checkbox">	          
	          <input @click="AutoFeed" type="checkbox" class="custom-control-input cuisine" :id="'cuisine'+item_cuisine.cuisine_id" 
              :value="item_cuisine.cuisine_id"
              v-model="cuisine"
	           >
	          <label class="custom-control-label" :for="'cuisine'+item_cuisine.cuisine_id">
	          {{item_cuisine.cuisine_name}}
	          </label>
	         </div>   		      
	        </div> <!--col-->	         	       
	        </template>	       	      	       
	    </div><!-- row-->
	    	   
	    <div class="collapse" id="moreCuisine">
	      <div class="row m-0">
	       
	         <template v-if="data_cuisine[6]">
	         <template v-for="(item_cuisine, index) in data_cuisine.slice(6)" >
	         <div class="col-lg-6 col-md-6 mb-4 mb-lg-3"> 	         
	         <div class="custom-control custom-checkbox">	          
	          <input @click="AutoFeed"  v-model="cuisine" type="checkbox" class="custom-control-input cuisine" :id="'cuisine'+item_cuisine.cuisine_id"
	          :value="item_cuisine.cuisine_id" >
	          <label class="custom-control-label" :for="'cuisine'+item_cuisine.cuisine_id" >
	           {{item_cuisine.cuisine_name}}
	          </label>
	         </div>   		   	         
	         </div> <!--col-->
	         </template>
	         </template>
	         
	      </div> <!--row-->
	    </div> <!--collapse-->
	    
	    <template v-if="data_cuisine[6]">
	    <div class="row ml-3 mt-1 mt-0 mb-2">
		 <a class="btn link more-cuisine" data-toggle="collapse" href="#moreCuisine" role="button" aria-expanded="false" aria-controls="collapseExample">
		  <u><?php echo t("Show more +")?></u>
		 </a>
		</div>
		</template>
		  	    
     
     </div> <!-- filterCuisine-->  
   </div> <!--filter-row-->

   <!--END CUISINE-->
   
   
   <!--MAX DELIVERY FEE-->
   <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterMinimum" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterMinimum"  >
     <?php echo t("Max Delivery Fee")?>
     </a>
     </h5>   
   
     <div id="filterMinimum" class="collapse" :class="{show:collapse}" aria-labelledby="headingOne" >       
     
     <div class="form-group">
	    <label for="formControlRange"><?php echo t("Delivery Fee")?> <b><span class="min-selected-range"></span></b></label>
	    <input v-model="max_delivery_fee" 
	          id="min_range_slider" value="10" type="range" class="custom-range" id="formControlRange"  min="1" max="20" >
	  </div>
     
     </div> <!-- filterMinimum-->  
   </div> <!--filter-row-->
   <!--END MAX DELIVERY FEE-->
   
   
   <!--RATINGS-->
    <div class="filter-row border-bottom pb-2 pt-2">
     <h5>
     <a href="#filterRating" class="d-block collapsed" data-toggle="collapse" aria-expanded="false" aria-controls="filterRating"  >
     <?php echo t("Ratings")?>
     </a>
     </h5>   
   
     <div id="filterRating" class="collapse" :class="{show:collapse}" aria-labelledby="headingOne" >    
       
         <p class="bold"><?php echo t("Over")?> {{rating}}</p>
         <star-rating  
         v-model:rating="rating"
		 :star-size="30"
		 :show-rating="false" 
		 @update:rating="rating = $event"
		 >
		 </star-rating>

     </div>
     <!--filterRating-->
     
     
   </div> <!--filter-row-->
   <!--END RATINGS-->
   
   
   </div> <!--section-filter-->
  
   <div class="mt-3 mb-3">
   
   <!--SUBMIT BUTTON-->
   
   <!--<button @click="Search(false)" type="submit" class="btn btn-green w-100" 
   :class="{ loading: is_loading }" 
   :disabled="!hasFilter"
   >
     <span class="label"><?php echo t("Show restaurants")?></span>
     <div class="m-auto circle-loader" data-loader="circle-side"></div>
   </button>-->
   </div>
   
   </template>
   </el-skeleton>

  </div> <!--column-1-->
  <!--FILTERS-->

  
 <!--SEARCH RESULTS-->  
 <div class="col-lg-9 col-md-12 column-2 ">
 
 <div class="d-block d-lg-none"><h4 class="m-0" v-cloak >{{this.total_message}}</h4></div>

 <div class="mt-1 mt-lg-5"></div>
 
    <el-skeleton :loading="is_loading" animated :count="4" >
	<template #template>
	   <div class="row equal align-items-center">	  
			<div v-for="dummy_index in 3" class="col-lg-4  col-md-6 mb-3 list-items">
				<div><el-skeleton-item variant="image" style="width: 100%; height: 170px" /></div>
				<div><el-skeleton-item style="width: 50%;" variant="text" /></div>
				<div><el-skeleton-item  variant="text" /></div>
			</div>
       </div>
	</template>  
	<template #default>
		
	<div class="row equal align-items-center position-relative">
	
	<!-- <DIV v-if="is_loading" class="overlay-loader">
	  <div class="loading mt-5">      
	    <div class="m-auto circle-loader" data-loader="circle-side"></div>
	  </div>
	</DIV>   -->	
	
	   <template v-for="(data,data_index) in datas"  >
	   <div class="col-lg-4 mb-3 col-md-6 list-items"  v-for="item in data" 
	   :class="{ 'make-grey': item.merchant_open_status=='0' || item.close_store=='1' || item.disabled_ordering=='1' }"  >  
	   	   
	     <!--IMAGE-->
	     <div class="position-relative"> 
	       <!-- <div class="skeleton-placeholder"></div> -->
	       <a :href="item.merchant_url">
			   <!-- <img class="rounded lazy" :data-src="item.url_logo"/> -->
			   <el-image
					style="width: 100%; height: 170px"
					:src="item.url_logo"
					:fit="cover"
					lazy
				></el-image>
			</a>
	       
	       <a :href="item.merchant_url">
	       
	         <div v-if="item.merchant_open_status=='0'" class="layer-grey"></div>
	         <div v-else-if="item.close_store == '1' || item.disabled_ordering == '1' || item.disabled_ordering=='1' || item.pause_ordering=='1'  " 
	          class="layer-black d-flex align-items-center justify-content-center" >
	         </div>
	         
	         <div v-if="item.close_store == '1' || item.disabled_ordering=='1'" 
	          class="layer-content d-flex align-items-center justify-content-center">
	           <p class="bold"><?php echo t("Currently unavailable")?></p>
	         </div>
	         
	         <div v-if="item.pause_ordering=='1' && item.disabled_ordering!='1' && item.close_store!='1' " 
	          class="layer-content d-flex align-items-center justify-content-center">
	             <p class="bold" v-if="pause_reason_data[item.merchant_id]">{{pause_reason_data[item.merchant_id]}}</p>
	             <p class="bold" v-else><?php echo t("Currently unavailable")?></p>
	         </div>
	         
	       </a>
	     </div>  
	     <!--END IMAGE-->
	     
	     <div class="row align-items-center mt-2" >
	      <div class="col text-truncate">
	       <h6 v-if="item.merchant_open_status=='0'" class="m-0">
	       {{item.next_opening}}
	       </h6> 
	       <a :href="item.merchant_url">
	         <h5 class="m-0 text-truncate">{{item.restaurant_name}}</h5>
	       </a>
	      </div>
	      <div class="col col-md-auto text-right">
	           	      	      
	        <!--COMPONENTS-->
	        <component-save-store
	         :active="item.saved_store=='1'?true:false"
	         :merchant_id="item.merchant_id"
	         @after-save="afterSaveStore(item)"
	        />
	        </component-save-store>
	        <!--COMPONENTS-->
	        
	      </div>
	     </div> <!--flex-->
	     
	     
	     <div class="row align-items-center" >
	      <div class="col text-truncate">
       
	        <template  v-for="(cuisine,index) in item.cuisine_name"  >	        
	         <span class="a-12 mr-1">{{cuisine.cuisine_name}},</span>	      	         
	        </template>
	        
	      </div>
	      <div class="col col-md-auto text-right">
	       <p class="m-0 bold">
	         <template v-if="estimation[item.merchant_id]">
	           <template v-if="services[item.merchant_id]">
	             <template v-for="(service_name,index_service) in services[item.merchant_id]"  >
	               <template v-if="index_service<=0">
	               
	                <template v-if=" estimation[item.merchant_id][service_name][item.charge_type] "> 
	                   {{ estimation[item.merchant_id][service_name][item.charge_type].estimation }} <?php echo t("min")?>
	                </template>
	                   
	               </template>
	             </template>
	           </template>
	         </template>
	       </p>
	      </div>
	    </div> <!--flex-->
	     
	    
	    <div class="row align-items-center">
	      <div class="col text-truncate">
	      <p class="m-0">
	      <b class="mr-1">{{item.ratings.rating}}</b> 
	      <i class="zmdi zmdi-star mr-1 text-grey"></i>
	        
	       <u v-if="item.ratings.review_count>0">{{item.ratings.review_count}}+ <?php echo t("Ratings")?></u>
	       <u v-else>{{item.ratings.review_count}} <?php echo t("rating")?></u>
	       
	      </p>	      
	      </div>
	      
	      <div class="col-md-auto text-right">
	        <p class="m-0" v-if="item.free_delivery==='1'" ><?php echo t("Free delivery")?></p>
	      </div>
	    </div> <!--flex-->
	   
	   </div> <!--col-->
	   </template>
	</div> <!--row-->
	
	</template>
	</el-skeleton>
	
 
    
	<!--LOAD MORE-->	
	<div class="d-flex justify-content-center mt-2 mt-lg-5">
	  <template v-if="hasMore">
	  <a href="javascript:;" @click="ShowMore" class="btn btn-black m-auto w25"
	    :class="{ loading: is_loading }"       
	  >	     
	     <span class="label"><?php echo t("Show more")?></span>
         <div class="m-auto circle-loader" data-loader="circle-side"></div>
	  </a>
	  </template>
	  
	  <template v-else>
	  <template v-if="hasData">
	    <p class="text-muted" v-if="page>1"><?php echo t("end of result")?></p>
	  </template>
	  </template>
	  
	</div>	
	<!--END LOAD MORE-->
		


 
 </div><!--column-2-->
 <!--END SEARCH RESULTS-->
 
</div> <!--section-results-->



<div class="section-fast-delivery tree-columns-center d-none d-lg-block">
  <div class="row">
  
  <div class="col col-4">
      <div class="d-flex align-items-center">
       <div class="w-100">      
        <img class="rider mirror" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/rider.png"?>" />
       </div>
      </div>
   </div>  
   
   <div class="col col-4">
      <div class="d-flex align-items-center">
       <div class="w-100 text-center">
       
         <h5><?php echo t("Fastest delivery in")?></h5>
         <h1 class="mb-4">Los angeles, california</h1>
         <p><?php echo t("Receive food in less than 20 minutes")?></p>   
       
         <a href="" class="btn btn-black w25"><?php echo t("Check")?></a>
         
       </div>
      </div>
   </div>
   
   <div class="col col-4">
      <div class="d-flex align-items-center">
       <div class="w-100 text-right">      
       <img class="rider" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/rider.png"?>" />
       </div>
      </div>
   </div>   
  
  </div> <!--row-->
</div> <!--section-fast-delivery-->

<!-- mobile view -->
<div class="d-block d-lg-none rounded mb-3 section-fast-delivery-mobile">

     <div class="w-100 text-center pt-3 pt-md-5">       
	   <h5><?php echo t("Fastest delivery in")?></h5>
	   <h1 class="mb-4">Los angeles, california</h1>
	   <p><?php echo t("Receive food in less than 20 minutes")?></p>   
	 
	   <a href="" class="btn btn-black w25"><?php echo t("Check")?></a>	   
	 </div>

</div>
<!-- mobile view -->

</template>

<!--NO RESULTS-->
<template v-else>

<div class="container mt-3 mb-5" v-if="!is_loading">

<div class="no-results-section mb-4 mt-5">
  <img class="img-350 m-auto d-block" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/404@2x.png"?>" />
</div>

<div class="text-center w-50 m-auto">
  <h3><?php echo t("Sorry! We're not there yet")?></h3>
  <p><?php echo t("We're working hard to expand our area. However, we're not in this location yet. So sorry about this, we'd still love to have you as a customer.")?></p>
  <!--<a href="<?php echo Yii::app()->createUrl("/")?>" class="btn btn-green w25">Go home</a>-->
</div>
 
</div> <!--container-->

</template>
<!--NO RESULTS END-->


</div> <!--container-->


<div class="container-fluid m-0 p-0 full-width">
 <?php $this->renderPartial("//store/join-us")?>
</div>



<div id="vue-carousel" class="container">

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

</div> <!--vue-carousel-->  
