
<DIV id="vue-my-order"  v-cloak>  
<el-skeleton animated :loading="loading" >
<template #template>
    <div class="row mt-3">
        <div class="col"></div>
        <div class="col">
           <div><el-skeleton-item style="width: 100%;" variant="button" /></div>
           <div><el-skeleton-item style="width: 100%;" variant="text" /></div>
        </div>
    </div>
    <div class="mt-4 mb-4">
       <div><el-skeleton-item style="width: 100%;" variant="button" /></div>
       <div><el-skeleton-item style="width: 100%;" variant="text" /></div>
    </div>
    <el-skeleton variant="p" :rows="12" />
</template>
<template #default>

<div class="row mb-4">
  <div class="col-lg-6 col-md-3 col-3 d-flex justify-content-start align-items-center"></div> <!--col-->
   
  <div class="col-lg-6 col d-flex justify-content-end align-items-center order-search-wrap"> 
    
   <div class="position-relative search-geocomplete w-75"> 	
	  <div v-if="!awaitingSearch" class="img-20 m-auto search_placeholder icon"></div>
	  <div v-if="awaitingSearch" class="icon" data-loader="circle"></div>    
	  <input class="form-control form-control-text form-control-text-white" 
      placeholder="<?php echo t("Search order")?>" v-model="q" :disabled="loading"  >		     
	  <div v-if="hasData" @click="clearData" class="icon-remove"><i class="zmdi zmdi-close"></i></div>
   </div>
  
  </div> <!--col-->
</div> <!--row-->

<template v-if="!loading">
<div class="card p-0 p-lg-3 mb-3 border" >
 <div class="rounded p-3 grey-bg" >
  <div class="row no-gutters align-items-center">
    <div class="col-lg-2 d-none d-lg-block">
       <div class="header_icon _icons bag d-flex align-items-center justify-content-center">
         <a class="rounded-pill rounded-button-icon ">
          <i class="zmdi" :class="{ 'zmdi-check': hasResults, 'zmdi-close': !hasResults }"></i>
        </a>
       </div>
    </div>
    
    <template v-if="!hasResults"> 
	    <div class="col-md-7">       
	     <template v-if="q==''">    
	       <h5><?php echo t("You don't have any orders here!")?></h5>
	       <p class="m-0"><?php echo t("Let's change that!")?></p>
	     </template>
	     <template v-else>      
	       <h5><?php echo t("No results")?></h5>
	       <p class="m-0"><?php echo t("Sorry we cannot find what your looking for")?></p>
	     </template>
	    </div>
	    <div class="col-md-3 text-center">
	      <a class="btn btn-green" target="_self" href="<?php echo Yii::app()->createUrl("/store/restaurants")?>">
	        <?php echo t("Order now")?>
	      </a>
	    </div>
    </template>
    
    <template v-else> 
     <div class="col-lg-5 d-none d-lg-block">       
       <h5><?php echo t("We like each other")?></h5>
       <p class="m-0"><?php echo t("Let's not change this!")?></p>
    </div>
    <div class="col-lg-3 d-none d-lg-block ">    
      <h5>{{animatedNumber}}</h5>
      <p><?php echo t("Orders Qty")?></p>
    </div>
    <div class="col-lg-2 d-none d-lg-block ">    
      <h5>{{animatedTotal}}</h5>
      <p><?php echo t("Total amount")?></p>
    </div>

    <!-- mobile view -->
    <div class="col-12 d-block d-lg-none">
       <div class="d-flex justify-content-between align-items-center  w-100">
           <div>
             <h5 class="m-0"><?php echo t("We like each other")?></h5>
             <p class="m-0"><?php echo t("Let's not change this!")?></p>
           </div>
           <div>
              <div class="header_icon _icons bag d-flex align-items-center justify-content-center">
                <a class="rounded-pill rounded-button-icon ">
                  <i class="zmdi" :class="{ 'zmdi-check': hasResults, 'zmdi-close': !hasResults }"></i>
                </a>
              </div>
           </div>
       </div>
    </div>
    <!-- mobile view -->

    </template>
    
  </div>
 </div>
</div> <!--card -->
</template>

<!--KMRS ROW-->
<template v-for="datas in data" >
<div class="kmrs-row row m-0 rounded p-2 mb-2" v-for="order in datas" >
 <div class="col p-0">
   <div class="d-flex justify-content-start">
     <div class="pr-2">
       <img class="img-60 rounded-pill" v-if="merchants[order.merchant_id]" :src="merchants[order.merchant_id].url_logo"/>
     </div>
     <div class="flex-fill">
        <div class="d-flex align-items-center">
         <div class="align-self-center mr-2">
            <h6 class="m-0" v-if="merchants[order.merchant_id]" >{{ merchants[order.merchant_id].restaurant_name }}</h6>
         </div>
         <div class="align-self-center">           
           <span class="badge" 
            :style="{background:services[order.service_code].background_color_hex,color:services[order.service_code].font_color_hex}" >
             {{services[order.service_code].service_name}}
           </span>
         </div>
         
        </div>
        <p class="m-0" v-if="merchants[order.merchant_id]" >{{ merchants[order.merchant_id].merchant_address }}</p>        
     </div> 
   </div> <!--flex-->
 </div> <!--col-->
 
 <div class="col pl-5">
   <h6 class="font13">{{order.order_id}} 
      
   <span v-if="status[order.status]" 
            class="badge" :style="{background:status[order.status].background_color_hex,color:status[order.status].font_color_hex}" >
   {{ status[order.status].status }}
   </span>
   <span v-else class="badge">
     {{ order.status }}
   </span>
   
   </h6>
   <p class="badge badge-light m-0">{{order.total_items}}</p>
   <p class="m-0">
   <template v-for="item in order.items">
     <span class="mr-1">
     {{items[item.item_id]}}     
     <span v-if="size[item.item_size_id]">({{size[item.item_size_id]}})</span> ,
      </span>
   </template>      
   </p>   
 </div>
 
 <div class="col">
  <div class="d-flex">
    <div>
      <p class="text-grey m-0"><?php echo t("Total")?>: <b class="text-dark">{{ order.total }}</b></p>      
      <p class="text-grey m-0"><?php echo t("Place on")?> {{order.date_created}}</p>
    </div>
    <div class="flex-grow-1 text-right">
    
    <!--<a href="javascript:;" class="btn btn-green-line">Reorder</a>-->
    
    <div class="dropdown dropleft">	      
   
       <a class="btn btn-sm dropdown-toggle no-arrow text-truncate shadow-none" href="#" 
        role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <i class="zmdi zmdi-more"></i> 
	   </a>
	  
	   <div class="dropdown-menu dropdown-actions" aria-labelledby="dropdownMenuLink">	    
	    <a class="dropdown-item ssm-toggle-nav" href="javascript:;" @click="orderDetails(order.order_uuid)" >
	    <i class="zmdi zmdi-eye mr-2"></i> <?php echo t("View")?>
	    </a>			    
	    
	    <a class="dropdown-item" href="javascript:;" @click="buyAgain(order.order_uuid,2);">
	    <i class="zmdi zmdi-repeat mr-2"></i> <?php echo t("Buy again")?>
	    </a>			    
	    
	    <a class="dropdown-item" :href="order.track" target="_blank">
	    <i class="zmdi zmdi-car mr-2"></i> <?php echo t("Track")?>
	    </a>			    
	    
	    <a class="dropdown-item" :href="order.pdf" target="_blank">
	    <i class="zmdi  zmdi-collection-pdf mr-2"></i> <?php echo t("Download PDF")?>
	    </a>			    
	    
	    <a class="dropdown-item" href="javascript:;"
        :disabled="!status_allowed_review.includes(order.status)"
        @click="writeReview(order.order_uuid)"
	    >
	    <i class="zmdi zmdi-star-outline mr-2"></i> <?php echo t("Write A Review")?>
	    </a>			    
	    
	    <a class="dropdown-item" href="javascript:;"      
	    v-if="merchants[order.merchant_id]" 
        :disabled="!status_allowed_cancelled.includes(order.status)"
        @click="callCancel(order.order_uuid,merchants[order.merchant_id].restaurant_name)" >
	    <i class="zmdi zmdi-close mr-2"></i> <?php echo t("Cancel order")?>	    
	    </a>			    
	    
	  </div> 
	  
 </div> <!--dropdown-->
    
    </div>
  </div>
 </div> <!--col-->
</div> <!--kmrs-row-->
</template>
<!--END ROW-->

<div><el-backtop /></div>

<template v-if="!loading">
	<template v-if="q==''"> 
  
	<div class="d-flex justify-content-center mt-4 mb-5" v-if="show_next_page" >
   <el-button type="primary" round
   @click="loadMoreOrders(page)"
   :loading="load_more"
   size="large"   
    ><?php echo t("Load more")?></el-button> 
	</div>
	<div v-else class="d-flex justify-content-center mt-4 mb-5">
	  <p class="m-0 text-muted"><?php echo t("end of results");?></p>
	</div>
	</template>
</template>


<!--ORDER DETAILS-->
<div class="order-details-panel section-cart" :class="{ open: show_details }" v-cloak >
   <a class="link close-panel p-0" @click="closeOrderDetails()" ><i class="zmdi zmdi-close"></i></a>
   
    <DIV v-if="order_loading">
      <div class="loading mt-5">      
        <div class="m-auto circle-loader" data-loader="circle-side"></div>
      </div>
    </DIV>  
    
    <DIV v-else>         
   
    <div class="mt-3 mb-0">
	    <p class="m-0 bold">{{order_label.your_order_from}}</p>
	    <a :href="order_merchant.restaurant_url" class="m-0 p-0">
	    <h5 class="m-0 chevron d-inline position-relative">{{order_merchant.restaurant_name}}</h5>
	    </a>
	    <p class="m-0 text-muted">{{order_merchant.merchant_address}}</p>
    </div>
    
       
   <div v-if="hasRefund" class="mt-3">
	   <h5><?php echo t("Refund Issued")?></h5>
	   <div v-for="item_refund in refund_transaction" class="p-2 rounded bg-light font11">
	     <div class="d-flex justify-content-between align-items-center">
	       <div class="flex-col">
	         <p class="m-0"><b><?php echo t("Description")?>:</b> {{item_refund.description}}</p>
	         <p class="m-0"><b><?php echo t("Amount")?>:</b> {{item_refund.trans_amount}}</p>
	         <p v-if="item_refund.used_card" class="m-0"><b><?php echo t("Issued to")?>:</b> {{item_refund.used_card}}</p>
	         <p v-else class="m-0"><b><?php echo t("Issued to")?>:</b> {{item_refund.payment_code}}</p>
	         <p class="m-0"><b><?php echo t("Date issued")?>:</b> {{item_refund.date}}</p>
	       </div>
	       <div class="flex-col"></div>
	     </div>
	   </div> <!--rounded-->
   </div>
        
   <div class="mt-3 items">   
    <div class="d-flex justify-content-between ">
       <div>       
       <h6 class="font13 m-0 badge" 
       :style="{background:order_status.background_color_hex,color:order_status.font_color_hex}"      
       >
        {{order_status.status}}
       </h6>
       </div>
       <div><p class="m-0 badge"
       :style="{background:order_services.background_color_hex,color:order_services.font_color_hex}"      
       >
        {{order_services.service_name}}</p>
       </div>
    </div> <!--flex-->    
   <h6 class="font13 m-0"><?php echo t("Order #")?>{{order_info.order_id}}</h6> 
   <p class="m-0 text-muted">{{order_info.payment_name}}</p>
   <p class="m-0 text-muted">{{order_info.place_on}}</p>
   <p class="m-0 text-muted" v-if="order_info.paid_on!=''" >{{order_info.paid_on}}</p>
   </div>
   
   <div class="items" v-if="order_info.customer_name!=''"  >   
     <h6 class="font13 m-0">{{order_info.customer_name}} 
        <span class="text-muted a-12 ml-2" v-if="order_info.contact_number!=''">{{order_info.contact_number}}</span>
     </h6>
     <p class="m-0 text-muted"  v-if="order_info.delivery_address!=''" >{{order_info.delivery_address}}</p>
     
     <p v-if="order_info.whento_deliver=='now'" class="m-0 text-muted">{{order_info.schedule_at}}</p>
     <p v-if="order_info.whento_deliver=='schedule'" class="m-0 text-muted">Scheduled at {{order_info.schedule_at}}</p>
   </div>
    
   <h6 class="mt-3 mb-3">{{order_label.summary}}</h6>    
   
   <div v-cloak class="items" v-for="(items, index) in order_items" >
      
     <div class="line-items row mb-1">
       <div class="col-3">
          <div class="position-relative"> 
		   <div class="skeleton-placeholder"></div>
		   <img class="rounded lazy" :data-src="items.url_image" />
		 </div>		      
       </div> <!--col-->
       
       <div class="col-6 p-0 d-flex justify-content-start flex-column">
         <p class="mb-1">
         {{items.qty}}x
         {{ items.item_name }}
          <template v-if=" items.price.size_name!='' "> 
          ({{items.price.size_name}})
          </template>
          
          <template v-if="items.item_changes=='replacement'">
           <div class="m-0 text-muted small">
            Replace "{{items.item_name_replace}}"
           </div>
           <div class="badge badge-success small"><?php echo t("Replacement")?></div>
           </template>
         </p>
         
         <template v-if="items.price.discount>0">         
           <p class="m-0 font11"><del>{{items.price.pretty_price}}</del> {{items.price.pretty_price_after_discount}}</p>
         </template>
         <template v-else>
           <p class="m-0 font11">{{items.price.pretty_price}}</p>
         </template>
         
         <p class="mb-0" v-if=" items.special_instructions!='' ">{{ items.special_instructions }}</p>	
         
          <template v-if=" items.attributes!='' "> 
          <template v-for="(attributes, attributes_key) in items.attributes">                    
            <p class="mb-0">            
            <template v-for="(attributes_data, attributes_index) in attributes">            
              {{attributes_data}}<template v-if=" attributes_index<(attributes.length-1) ">, </template>
            </template>
            </p>
          </template>
        </template>
         
       </div> <!--col-->
       
       <div class="col-3  quantity d-flex justify-content-start flex-column  text-right">
       <template v-if="items.price.discount<=0 ">
          <p class="mb-0">{{ items.price.pretty_total }}</p>
        </template>
        <template v-else>
           <p class="mb-0">{{ items.price.pretty_total_after_discount }}</p>
        </template>
       </div> <!--col-->
       
     </div> <!--line-items-->
     
      <!--addon-items-->
    <div class="addon-items row mb-1"  v-for="(addons, index_addon) in items.addons" >
      <div class="col-3 "><!--empty--></div> <!--col-->		     
      <div class="col-9 pl-0 d-flex justify-content-start flex-column ">
         <p class="m-0 bold">{{ addons.subcategory_name }}</p>		  
                
         <template v-cloak v-for="addon_items in addons.addon_items">
         <div class="d-flex justify-content-between mb-1">
           <div class="flexrow"><p class="m-0">{{addon_items.qty}} x {{addon_items.pretty_price}} {{addon_items.sub_item_name}}</p></div>
           <div class="flexrow"><p class="m-0">{{addon_items.pretty_addons_total}}</p></div>
         </div>	<!--flex-->                  
        </template>
         
      </div> <!--col-->		      		      
    </div>
    <!-- addon-items-->
     
   </div> <!--items-->
    
   
   <div class="cart-summary mt-2 mb-3 ">
  
      <template v-for="summary in order_summary">      
      <div class="d-flex justify-content-between align-items-center mb-1">
	       <template v-if=" summary.type=='total' ">
	         <div><h6 class="m-0">{{ summary.name }}</h6></div>
	         <div><h6 class="m-0">{{ summary.value }}</h6></div>
	       </template>
	       <template v-else>
	         <div>{{ summary.name }}</div>
	         <div>{{ summary.value }}</div>
	       </template>
      </div> <!--flex-->
      </template>
            
  </div> <!--cart-summary -->
    
  <div class="mt-5 mb-3">
   <div class="row">
      <div class="col"><a :href="order_info.tracking_link" target="_blank" 
       class="btn btn-black w-100 small">{{order_label.track}}</a></div>
      <div class="col">       
        <a href="javascript:;" @click="buyAgain(order_info.order_uuid,1);"
       class="btn btn-green w-100 small">{{order_label.buy_again}}</a>
      </div>
   </div><!--flex-->
  </div>
  
  </DIV> <!--END IF LOADING-->
   
</div> 
<!--ORDER DETAILS-->


<!--COMPONENTS CANCEL ORDER-->
<component-cancel-order
ref="orderCancelRef"
:label="{
  cancel: '<?php echo t("Cancel order")?>',
  dont: '<?php echo CJavaScript::quote(t("Don't cancel"))?>',  
  how: '<?php echo CJavaScript::quote(t("How would you like to proceed?"))?>',
  are_you_sure: '<?php echo CJavaScript::quote(t("Are you sure?"))?>',
}"
>
</component-cancel-order>
<!--COMPONENTS CANCEL ORDER-->

<!--COMPONENTS REVIEW-->
<components-review
ref="ReviewRef"
accepted_files = "image/jpeg,image/png,image/gif/mage/webp"
:max_file = "2"
:label="{
  write_review: '<?php echo CJavaScript::quote(t("Write A Review"))?>',
  what_did_you_like: '<?php echo CJavaScript::quote(t("What did you like?"))?>',  
  what_did_you_not_like: '<?php echo CJavaScript::quote(t("What did you not like?"))?>',
  add_photo: '<?php echo CJavaScript::quote(t("Add Photos"))?>',
  write_your_review: '<?php echo CJavaScript::quote(t("Write your review"))?>',
  post_review_anonymous: '<?php echo CJavaScript::quote(t("post review as anonymous"))?>',
  review_helps: '<?php echo CJavaScript::quote(t("Your review helps us to make better choices"))?>',  
  drop_files_here: '<?php echo CJavaScript::quote(t("Drop files here to upload"))?>', 
  add_review: '<?php echo CJavaScript::quote(t("Add Review"))?>',  
  max_file_exceeded : '<?php echo CJavaScript::quote(t("Maximum files exceeded"))?>',  
  dictDefaultMessage : '<?php echo CJavaScript::quote(t("Drop files here to upload"))?>',  
  dictFallbackMessage : '<?php echo CJavaScript::quote(t("Your browser does not support drag'n'drop file uploads."))?>',  dictFallbackText : '<?php echo CJavaScript::quote(t("Please use the fallback form below to upload your files like in the olden days."))?>',  
  dictFileTooBig: '<?php echo CJavaScript::quote(t("File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.w"))?>',  
  dictInvalidFileType: '<?php echo CJavaScript::quote(t("You can't upload files of this type."))?>',  
  dictResponseError: '<?php echo CJavaScript::quote(t("Server responded with {{statusCode}} code."))?>',  
  dictCancelUpload: '<?php echo CJavaScript::quote(t("Cancel upload"))?>',  
  dictCancelUploadConfirmation: '<?php echo CJavaScript::quote(t("Are you sure you want to cancel this upload?"))?>',  
  dictRemoveFile: '<?php echo CJavaScript::quote(t("Remove file"))?>',  
  dictMaxFilesExceeded: '<?php echo CJavaScript::quote(t("You can not upload any more files."))?>',   
  search_tag: '<?php echo CJavaScript::quote(t("Search tag"))?>',   
}"
:rating-value="rating_value"
@update:rating-value="rating_value = $event"
>
</components-review>

<!--COMPONENTS ALERT-->
<div id="component_alert">
<component-alert
ref="Alertref"
title="<?php echo Yii::app()->params['settings']['website_title']?>"
button="<?php echo t("OK")?>"
>
</component-alert>
</div>
<!--COMPONENTS ALERT-->


</template>
</el-skeleton>

</DIV>