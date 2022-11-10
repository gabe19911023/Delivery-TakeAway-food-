<?php 

	$data = CMerchantListingV1::getMerchantInfo(Yii::app()->user->getState('rest_slug_name'),Yii::app()->language);				
	$merchant_id = intval($data['merchant_id']);
	$merchant_uuid = trim($data['merchant_uuid']);
	$gallery = CMerchantListingV1::getGallery($merchant_id);						
	$opening_hours = CMerchantListingV1::openingHours($merchant_id);
	$static_maps=''; $map_direction='';
	if($data){			  					
	   $static_maps = CMerchantListingV1::staticMapLocation(
	     Yii::app()->params['map_credentials'],
	     $data['latitude'],$data['lontitude'],
	     '500x200',websiteDomain().Yii::app()->theme->baseUrl."/assets/images/marker2@2x.png"
	   );		
	   $map_direction = CMerchantListingV1::mapDirection(Yii::app()->params['map_credentials'],
	     $data['latitude'],$data['lontitude']
	   );	   					  
	}
?>
<ul id="vue-cart-preview" class="top-menu list-unstyled" v-cloak>
 <li class="d-none d-lg-inline mr-3"> 

      <?php $this->widget('application.components.WidgetLangselection');?>
 </li>

 <!-- <li class="d-inline notification-dropdown mr-3 mr-lg-0">
    
   <components-notification
     ref="notification"
     avatar="<?php echo Yii::app()->user->avatar?>"
     image_background="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"	
     ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 		     
     view_url="<?php echo Yii::app()->createUrl("/account/notifications-list")?>" 
     :realtime="{
	   enabled : '<?php echo Yii::app()->params['realtime_settings']['enabled']==1?true:false ;?>',  
	   provider : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['provider'] )?>',  			   
	   key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['key'] )?>',  			   
	   cluster : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['cluster'] )?>', 
	   ably_apikey : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['ably_apikey'] )?>', 
	   piesocket_api_key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_api_key'] )?>', 
	   piesocket_websocket_api : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_websocket_api'] )?>', 
	   piesocket_clusterid : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_clusterid'] )?>', 	   
	   channel : '<?php  echo isset(Yii::app()->user->client_uuid ) ? CJavaScript::quote( Yii::app()->user->client_uuid ) : ''?>', 		   
	   event : '<?php echo CJavaScript::quote( Yii::app()->params->realtime['notification_event'] )?>',  
	 }"  			 
     :label="{
	  title : '<?php echo CJavaScript::quote(t("Notification"))?>',  
	  clear : '<?php echo CJavaScript::quote(t("Clear all"))?>',  
	  view : '<?php echo CJavaScript::quote(t("View all"))?>',  			  
	  pushweb_start_failed : '<?php echo CJavaScript::quote(t("Could not push web notification"))?>',  			  
	  no_notification : '<?php echo CJavaScript::quote(t("No notifications yet"))?>',  	
	  no_notification_content : '<?php echo CJavaScript::quote(t("When you get notifications, they'll show up here"))?>',  	
	}"  			 
     >		      
     </components-notification>
    
 </li> -->
<!--  <li class="d-none d-lg-inline">
 
 <div class="dropdown userprofile">	      
   
      <a class="btn btn-sm dropdown-toggle text-truncate shadow-none width-100" href="javascript:;"
      role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <?php echo Yii::app()->input->xssClean(Yii::app()->user->first_name)?>
	  </a>
	  
	 <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
	    <a class="dropdown-item with-icon-account" href="<?php echo Yii::app()->createUrl("/account/profile");?>">
	    <?php echo t("Manage my account")?>
	    </a>
	    <a class="dropdown-item with-icon-orders" href="<?php echo Yii::app()->createUrl("/account/orders");?>">
	    <?php echo t("My orders")?>
	    </a>
	    <a class="dropdown-item with-icon-addresses" href="<?php echo Yii::app()->createUrl("/account/addresses");?>">
	    <?php echo t("Addresses")?>
	    </a>
	    <a class="dropdown-item with-icon-payments" href="<?php echo Yii::app()->createUrl("/account/payments");?>">
	    <?php echo t("Payments Options")?>
	    </a>
	    <a class="dropdown-item with-icon-savedstore" href="<?php echo Yii::app()->createUrl("/account/favourites");?>">
	    <?php echo t("Saved Stores")?>
	    </a>
	    <a class="dropdown-item with-icon-logout" href="<?php echo Yii::app()->createUrl("/account/logout")?>">
	    <?php echo t("Logout")?>
	    </a>			    
	  </div> 
	  
 </div> 
 
 
 </li> -->
 <li class="d-none d-lg-inline line-left1">
 <a 
   href="<?php echo $cart_preview==true?'javascript:;':'#vue-cart'?>"
   class="<?php echo $cart_preview==true?'ssm-toggle-navx':''?>"
   <?php if($cart_preview):?>
    @click="showCartPreview"  
    <?php endif?>
   >
   <?php echo t("Cart")?>
 </a>
 </li>

 <li class="d-inline  mr-3 mr-lg-0">
 <a 
   href="<?php echo $cart_preview==true?'javascript:;':'#vue-cart'?>"
   class="cart-handle <?php echo $cart_preview==true?'ssm-toggle-navx':''?>"
   <?php if($cart_preview):?>
    @click="showCartPreview"  
    <?php endif?>
   >
    <img src="<?php echo Yii::app()->theme->baseUrl."/assets/images/shopping-bag.svg"?>" />
    <span class="badge small badge-dark rounded-pill">{{items_count}}</span>
 </a>
 </li>

 <li class="d-inline d-lg-none">
 <div @click="drawer=true" class="hamburger hamburger--3dx">
   <div class="hamburger-box">
      <div class="hamburger-inner"></div>
   </div>
  </div> 
 </li>
 
<?php Yii::app()->controller->renderPartial("//components/cart-preview",array(
 'cart_preview'=>$cart_preview
))?>

<el-drawer v-model="drawer"
 direction="ltr" 
 custom-class="drawer-menu"
 :with-header="true"
 :show-close="true"
 size="60%"
 >
 
 <template #default>
 
 
<div class="mt-1 lsection-address">
	
      <section id="section-address" class="mb-4 p-2 p-lg-0">
         <div class="row">
          <div class="col-lg-3 col-md-12 p-0 mb-3 mb-lg-0">
              <div class="d-flex">
                <div class="mr-3"><img class="img-20 contain" style="height:28px;" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/location.png"?>"/></div>
                <div>
                  <h5><?php echo t("Address")?>:</h5>
                  <div class="mb-3">
                     <p class="m-0"><?php echo $data['merchant_address']?></p>
                     <?php if($map_direction):?>
                     <a href="<?php echo $map_direction;?>" target="_blank" class="a-12"><u><?php echo t("Get direction")?></u></a>
                     <?php endif;?>
                  </div>
                  
                </div>
             </div> <!--d-flex-->
             
             <div class="d-flex">
                <div class="mr-3"><img class="img-20" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/clock.png"?>"/></div>
                <div class="flex-fill">
                   <h5><?php echo t("Opening hours")?>:</h5>
                   <?php if(is_array($opening_hours) && count($opening_hours)>=1):?>
                   <table class="w-100">              
                   <?php foreach ($opening_hours as $opening_hours_val):?>
                      <tr >
                      <td class="align-top pb-1"><?php echo ucwords(t($opening_hours_val['day']))?></td>
                       <td class="bold align-top pb-1">
                        <p class="m-0">
                        <?php echo t("[start] - [end]",
                             array(
                             '[start]'=>Date_Formatter::Time($opening_hours_val['start_time']),
                             '[end]'=>Date_Formatter::Time($opening_hours_val['end_time'])) )
                        ?>
                        </p>
                        <?php if(!empty($opening_hours_val['start_time_pm'])):?>
                        
                           <p class="m-0">
                           <?php echo t("[start] - [end]",
                                array(
                                '[start]'=>Date_Formatter::Time($opening_hours_val['start_time_pm']),
                                '[end]'=>Date_Formatter::Time($opening_hours_val['end_time_pm'])) )
                           ?>
                           </p>  
                        
                        <?php endif;?>
                        
                        <?php if(!empty($opening_hours_val['custom_text'])):?>
                        <p class="m-0"><?php echo $opening_hours_val['custom_text'];?></p>
                        <?php endif;?>
                        
                       </td>
                      </tr>
                   <?php endforeach;?>
                   </table>
                   <?php endif;?>
                </div>
             </div> <!--d-flex-->
             
             
          </div> <!--col-->
          
          <div class="col-lg-9 col-md-12">
            <?php if(!empty($static_maps)):?>
            <img class="rounded w-100"  src="<?php echo $static_maps?>" alt="<?php echo $data['restaurant_name']?>">
            <?php endif;?>     
          </div> <!--col-->
          
        </div> <!--row-->
        </section>
</div>
 </template>

<!--  <template #footer >
   <div class="text-left">
      <div class="d-flex align-items-center">
         <div class="mr-2">
              <img class="img-fluid" src="<?php echo Yii::app()->theme->baseUrl;?>/assets/images/logok.png">
         </div>
         <div class="">
            <p class="m-0 font-weight-bold"><?php echo t("Best restaurants In your pocket")?></p>
         </div>
      </div>
      <a class="btn btn-light mt-2 rounded-pill"><?php echo t("Get the app")?></a>
   </div>
 </template> -->

</el-drawer>
</ul>