
<div id="vue-orders-track" class="container" v-cloak  >

 <component-order-tracking
 ref="tracking" 
 ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 		      
 :realtime="{
   enabled : '<?php echo Yii::app()->params['realtime_settings']['enabled']==1?true:false ;?>',  
   provider : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['provider'] )?>',  			   
   key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['key'] )?>',  			   
   cluster : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['cluster'] )?>', 
   ably_apikey : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['ably_apikey'] )?>', 
   piesocket_api_key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_api_key'] )?>', 
   piesocket_websocket_api : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_websocket_api'] )?>', 
   piesocket_clusterid : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_clusterid'] )?>', 
   channel : '<?php echo CJavaScript::quote( Yii::app()->user->client_uuid  )?>',  			   
   event : '<?php echo CJavaScript::quote( Yii::app()->params->realtime['event_tracking_order'] )?>',  
 }"  			      
 @after-receive="afterProgress"
 >		      
 </component-order-tracking>
 
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

<div class="row no-gutters" v-if="merchant_info" >
  <div class="col-lg-3 col-md-12 pt-4 pb-4 pr-2">
          
    <h5 class="m-0 mb-1 top_order_status">{{order_status}}</h5>    
    <p class="m-0 top_order_status2" style="    text-transform: capitalize;">{{order_status_details}}</p>        
        
    <template v-if="order_info.order_type=='delivery'">       
    <div class="mt-3 mb-5">
	    <ul id="progressbar" class="text-center">
	        <li class="step-order" :class="{ active: order_progress>='1', 'progressing': order_progress=='1' , 'order_failed': order_progress=='0'  }" >
	          <div class="progress-value"></div>
	        </li>  
	        <li class="step-merchant" :class="{ active: order_progress>='2', 'progressing': order_progress=='2' , 'order_failed': order_progress=='0'  }"  >
	           <div class="progress-value"></div>
	        </li>        
	        <li class="step-car " :class="{ active: order_progress>='3' , 'progressing': order_progress=='3' , 'order_failed': order_progress=='0'   }" >
	           <div class="progress-value"></div>
	        </li>
	        <li class="step-home" :class="{ active: order_progress>='4', 'order_failed': order_progress=='0' }" ></li>
	    </ul>
    </div>
    </template>
    
    <template v-else>       
       <div class="mt-3 mb-5">
	    <ul id="progressbar" class="text-center three-column">
	        <li class="step-order" :class="{ active: order_progress>='1', 'progressing': order_progress=='1' , 'order_failed': order_progress=='0'  }" >
	          <div class="progress-value"></div>
	        </li>  
	        <li class="step-merchant" :class="{ active: order_progress>='2', 'progressing': order_progress=='2' , 'order_failed': order_progress=='0'  }"  >
	           <div class="progress-value"></div>
	        </li>        	        
	        <li class="step-home" :class="{ active: order_progress>='3', 'order_failed': order_progress=='0' }" ></li>
	    </ul>
    </div>
    </template>
    
    <div class="card body mt-5"  >      
     <div class="items d-flex justify-content-between">
        <div>
            <div class="position-relative"> 
			   <div class="skeleton-placeholder"></div>
			   <img class="rounded-pill lazy" :data-src="merchant_info.url_logo"/>
			 </div>
        </div> <!--col-->
        <div class=" flex-fill pl-2">
          <a target="_blank"  :href="merchant_info.restaurant_url"><h6 class="d-inline mr-1">{{merchant_info.restaurant_name}}</h6></a>
          <template v-for="(cuisine, index) in merchant_info.cuisine"  >
          <span v-if="index <= 0" class="badge mr-1" :style="'background:'+cuisine.bgcolor+';font-color:'+cuisine.fncolor" >
            {{ cuisine.cuisine_name }}
          </span>
          </template>
          <p class="m-0">{{ merchant_info.merchant_address }}</p>
          
          <!--DIRECTIONS -->
          <div v-if="order_info.order_type=='pickup'" class="mt-2">            
            <a :href="'tel:'+ merchant_info.contact_phone"  class="btn btn-circle btn-white border mr-2"><i class="zmdi zmdi-phone"></i></a>
            <a :href="merchant_info.restaurant_direction" target="_blank" class="btn btn-circle btn-white border"><i class="zmdi zmdi-turning-sign"></i></a>
          </div>          
          <div v-else-if="order_info.order_type=='dinein'" class="mt-2">            
            <a :href="'tel:'+ merchant_info.contact_phone"  class="btn btn-circle btn-white border mr-2"><i class="zmdi zmdi-phone"></i></a>
            <a :href="merchant_info.restaurant_direction" target="_blank" class="btn btn-circle btn-white border"><i class="zmdi zmdi-turning-sign"></i></a>
          </div>
          
        </div> <!--col-->
     </div> <!--items-->                
   </div> <!--card body-->
    
    <div class="divider p-0 mt-2 mb-2"></div>
        
    <!-- <template  v-if="order_progress===4">
    <div class="mt-3 mb-3" >
        <h6><?php echo t("HOWS WAS YOUR ORDER?")?></h6>
        <p><?php echo t("let us know how your delivery wen and how you liked your order!")?></p>
        <div class="d-flex justify-content-end">
          <div><a @click="writeReview(order_uuid)" class="btn link text-green"><?php echo t("Rate Your Order")?></a></div>
        </div>
    </div>    
    <div  class="divider p-0 mt-2 mb-2"></div>
    </template> -->
        
    <template v-if="hasInstructions">   
    <div class="mt-3 mb-3" >
      <h6><?php echo t("UPON ARRIVAL")?></h6>
      <p>{{instructions.text}}</p>
    </div>
    <div class="divider p-0 mt-2 mb-2"></div>
    </template>
    
    
    <div class="mt-3 mb-3" >
     <a target="_blank" :href="merchant_info.restaurant_url"><h6>{{merchant_info.restaurant_name}}</h6></a>
     <p class="m-0 mb-1 bold"><?php echo t("Order #")?>{{order_info.order_id}}</p>
     <p class="m-0 mb-1 bold">{{items_count}} items</p>
     <p class="m-0 mb-1">{{order_info.sub_total}} <?php echo t("Subtotal")?></p>
     <ul class="list-unstyled m-0 p-0" v-if="items.length>0" >
      <template v-for="(item, index) in items"  >
      <li><p class="m-0">
       {{item.qty}}x {{item.item_name}}   
       <template v-if=" item.size_name!='' "> 
          ({{item.size_name}})
       </template>      
      </p></li>      
      </template>
     </ul>
    </div>
    
    <div class="divider p-0 mt-2 mb-2"></div>
        
    <div class="mt-3 mb-3" v-if="meta">
      <h6><?php echo t("Delivery Address")?></h6>
      <p class="m-0">{{meta.formatted_address}}</p>
    </div>
    
    <div class="divider p-0 mt-2 mb-2"></div>
        
    <div class="mt-3 mb-3" v-if="meta">
      <h6><?php echo t("Estimated Time")?></h6>
      <p class="m-0 estimate_address">Status Loading...</p>
    </div>
    
    
  </div> <!--col-->
  
  <div class="col-lg-9 col-md-12 page-grey p-0 track-map-div" >
   <div id="cmaps" class="map-fullscreen" ></div>
  </div> <!--col-->
</div> <!--row-->



</div> <!--container-->
<script type="text/javascript">
    window.order_ajax = true;
    window.order_uuid = "<?= $order_uuid ?>";
</script>