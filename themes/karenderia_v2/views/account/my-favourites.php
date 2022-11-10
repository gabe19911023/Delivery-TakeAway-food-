<DIV id="vue-saved-store" v-cloak  >

<el-skeleton animated :loading="is_loading" >
<template #template>
  
  <div class="m-3 mb-4">
    <div><el-skeleton-item style="width: 100%;" variant="button" /></div>
    <div><el-skeleton-item style="width: 100%;" variant="text" /></div>
  </div>

  <el-skeleton :count="3" >
  <template #template>
  <div class="row m-0">
    <div class="col-lg-3 mb-3 col-md-6">
         <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
    <div class="col-lg-3 mb-3 col-md-6">
        <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
    <div class="col-lg-3 mb-3 col-md-6">
       <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
    <div class="col-lg-3 mb-3 col-md-6">
       <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
  </div>
  </template>
  </el-skeleton>

</template>
<template #default>


<div class="card p-3 mb-3 d-none d-lg-block"  v-if="!is_loading" >
 <div class="rounded p-3 grey-bg" >
  <div class="row no-gutters align-items-center">
    <div class="col-md-2">
       <div class="header_icon _icons favourite d-flex align-items-center justify-content-center">         
       </div>
    </div>
    
    <div class="col-md-6">             
       <template  v-if="data.length>0">
         <h5><?php echo t("Saved Stores")?></h5>
         <p class="m-0"><?php echo t("Your collection of restaurant and foods")?></p>
       </template>
       <template v-else>
          <h5><?php echo t("You don't have any save stores here!")?></h5>
          <p class="m-0"><?php echo t("Let's change that!")?></p>
       </template>
    </div>
    
    <div class="col-md-4 text-center">
      <a class="btn btn-green" target="_self" href="<?php echo Yii::app()->createUrl("/store/restaurants")?>">
	     <?php echo t("Order now")?>
	  </a>
    </div>
    
  </div>
 </div>
</div> <!--card -->

<!-- mobile view -->
<div class="card mb-3 mt-3 d-block d-lg-none">
<div class="rounded p-3 grey-bg" >
   <div class="d-flex justify-content-between align-items-center w-100">
     <div>
	     <h5><?php echo t("Saved Stores")?></h5>
         <p class="m-0"><?php echo t("Your collection of restaurant and foods")?></p>
     </div>
     <div>
	 <a class="btn btn-green" target="_self" href="<?php echo Yii::app()->createUrl("/store/restaurants")?>">
	     <?php echo t("Order now")?>
	  </a>
     </div>
   </div>
 </div>
</div>
<!-- mobile view -->


<div class="row equal align-items-center position-relative">

  <div class="col-lg-3 mb-3 col-md-6"  v-for="item in data" 
   :class="{ 'make-grey': item.saved_store==false  }" 
   >   
    <div class="card p-3 list-items" >  
        
		 <div class="position-relative"> 
			<a :href="item.merchant_url">
			<el-image
				style="width: 100%; height: 170px"
				:src="item.url_logo"
				:fit="contain"
			></el-image>
			</a>
			<div v-if="item.saved_store==false" class="layer-grey"></div>
         </div>
	     
	  <div class="row align-items-center mt-2" >
	      <div class="col text-truncate">
	       <h6 v-if="item.merchant_open_status=='0'" class="m-0">
	       {{item.next_opening}}
	       </h6> 
	       <a :href="item.merchant_url">
	         <h5 class="m-0 text-truncate">{{item.restaurant_name}}</h5>
	       </a>
	      </div>
	      <div class="col-md-auto text-right">
	           	      	     	      
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
	      <div class="col-md-auto text-right">
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
    
    </div> <!--card-->
  </div> <!--col-->

</div> <!--row-->


</template>
</template>

</DIV>
<!--vue-saved-store-->

<?php $this->renderPartial("//components/vue-bootbox")?>