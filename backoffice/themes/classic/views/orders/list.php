<DIV id="vue-order-management">


<components-delay-order
ref="delay"
@after-confirm="afterConfirmDelay"
@after-update="afterUpdateStatus" 
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"  
:order_uuid="order_uuid"
:label="{
    title:'<?php echo CJavaScript::quote(t("Delay Order"))?>',     
    sub1:'<?php echo CJavaScript::quote(t("How much additional time you need?"))?>',   
    sub2:'<?php echo CJavaScript::quote(t("We'll notify the customer about the delay."))?>',   
    confirm:'<?php echo CJavaScript::quote(t("Confirm"))?>', 
  }"
>
</components-delay-order>

<components-order-history
ref="history"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
:order_uuid="order_uuid"
:label="{
    title:'<?php echo CJavaScript::quote(t("Timeline"))?>',         
    close:'<?php echo CJavaScript::quote(t("Close"))?>', 
  }"  
>
</components-order-history>


<div class="d-flex justify-content-between align-items-center headroom2 position-relative" style="z-index:2;"> 

  <div class="flex-col d-none d-lg-block">
   <h5 class="m-0"><?php echo $heading?></h5>
   <p class="m-0"><a href="#"><?php echo t("How to manage orders")?></a></p>    
  </div> <!--flex-col-->
    
  <div class="flex-col">
    <div class="d-flex align-items-center">
      <div class="flexcol">
      <components-pause-order
	    ref="pause_order"
	    ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
	    @after-clickpause="afterClickpause"
	    :label="{
        accepting_orders : '<?php echo CJavaScript::quote(t("Accepting Orders"))?>',  		
        not_accepting_orders : '<?php echo CJavaScript::quote(t("Not accepting orders"))?>',  		
        store_pause : '<?php echo CJavaScript::quote(t("Store pause for"))?>',  
		  }"  		
	    />
	    </components-pause-order>
      </div>
      <div class="flexcol ml-3 notification-parent">
      
          <components-notification
		     ref="notification"
		     ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 		     
		     view_url="<?php echo Yii::app()->createUrl("/merchant/all_notification")?>" 
		     :realtime="{
			   enabled : '<?php echo Yii::app()->params['realtime_settings']['enabled']==1?true:false ;?>',  
			   provider : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['provider'] )?>',  			   
			   key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['key'] )?>',  			   
			   cluster : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['cluster'] )?>', 
			   ably_apikey : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['ably_apikey'] )?>', 
			   piesocket_api_key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_api_key'] )?>', 
			   piesocket_websocket_api : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_websocket_api'] )?>', 
			   piesocket_clusterid : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_clusterid'] )?>', 
			   channel : '<?php echo CJavaScript::quote( Yii::app()->merchant->merchant_uuid )?>',  			   
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
      
      </div> <!--flexcol-->
      
    </div> <!-- flex-->
  </div> <!--flex-col-->

  <div class="d-block d-lg-none">
  
       <div class="hamburger hamburger--3dx ssm-toggle-nav">
		    <div class="hamburger-box">
		      <div class="hamburger-inner"></div>
		    </div>
		    </div> 

  </div>
  <!--flexcol-->


</div>

<div class="d-block d-lg-none mt-2">
  <h5 class="m-0"><?php echo $heading?></h5>
  <p class="m-0"><a href="#"><?php echo t("How to manage orders")?></a></p>    
</div>

<hr class="m-0 mt-3"/>

<components-pause-modal
ref="pause_modal"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
@after-pause="afterPause"
:label="{
   pause_new_order:'<?php echo CJavaScript::quote(t("Pause New Orders"))?>',       
   resume_orders:'<?php echo CJavaScript::quote(t("How long you would like to pause new orders?"))?>',
   cancel:'<?php echo CJavaScript::quote(t("Cancel"))?>',
   confirm:'<?php echo CJavaScript::quote(t("Confirm"))?>',
   reason:'<?php echo CJavaScript::quote(t("Reason for pausing"))?>',
   next:'<?php echo CJavaScript::quote(t("Next"))?>',
   hours:'<?php echo CJavaScript::quote(strtoupper(t("hours")))?>',
   minute:'<?php echo CJavaScript::quote(strtoupper(t("minutes")))?>',
}"
/>
</components-pause-modal>

<components-resume-order-modal
ref="resume_order"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
@after-pause="afterPause"
:label="{
   store_pause:'<?php echo CJavaScript::quote(t("Store Pause"))?>',       
   resume_orders:'<?php echo CJavaScript::quote(t("Would you like to resume accepting orders?"))?>',
   cancel:'<?php echo CJavaScript::quote(t("Cancel"))?>',
   confirm:'<?php echo CJavaScript::quote(t("Confirm"))?>',
}"
/>
</components-resume-order-modal>


<components-order-search-nav
ref="order_search_nav"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
@after-filter="afterFilter"
:filter_status="false"
:label="{
   status:'<?php echo CJavaScript::quote("Status")?>',     
   order_type_list:'<?php echo CJavaScript::quote(t("Order type"))?>',     
   payment_status_list:'<?php echo CJavaScript::quote(t("Payment status"))?>',     
   sort:'<?php echo CJavaScript::quote(t("Sort"))?>',     
   placeholder:'<?php echo CJavaScript::quote(t("Filter by order number or customer name"))?>',     
}"
>
</components-order-search-nav>


<div class="row no-gutters fixed-height">
 <div class="col col-lg-3 col-md-4 col-sm-3 position-relative border">
 
 <!--NEW ORDERS-->
 <DIV class="orders-left-section nice-scroll">    
  <components-orderlist
  ref="orderlist"
  :order_status='<?php echo isset($status)?json_encode($status):''?>'
  show_critical='<?php echo $show_critical?>'
  :schedule='<?php echo isset($schedule)?$schedule:false;?>'  
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"  
  :label="{
    title:'<?php echo CJavaScript::quote($title)?>',     
  }"
  @after-select="afterSelectOrder"
  >
  </components-orderlist>    
 </DIV>
 <!--NEW ORDERS-->
  
 </div> <!--col-->
  
 <div class="col col-lg-9 col-md-8 col-sm-8 border d-none d-sm-block" :class="{ order_modal: show_as_popup }" >
 
 <div v-cloak v-if="show_as_popup" class="text-right p-2 pr-3">
 <a @click="closeOrderModal" href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>
 </div>
 
  <!--CONTENT SECTION-->  
  <components-orderinfo
  ref="orderinfo"  
  group_name="<?php echo $group_name?>"
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
  @after-update="afterUpdateStatus" 
  @show-menu="showMerchantMenu" 
  @refresh-order="refreshOrderInformation"
  @view-customer="viewCustomer"
  @to-print="toPrint"
  @delay-orderform="delayOrder"
  @rejection-orderform = "orderReject"
  @order-history = "orderHistory"
  
  :manual_status="<?php echo $manual_status?>"
  :modify_order="<?php echo $modify_order?>"
  :filter_buttons='<?php echo isset($filter_buttons)?$filter_buttons:false;?>'  
  :enabled_delay_order="<?php echo $enabled_delay_order;?>"
  
  :refund_label="{
    title:'<?php echo CJavaScript::quote(t("Refund this item"))?>',    
    content:'<?php echo CJavaScript::quote(t("This automatically remove this item from your active orders, and you cannot undo this changes."))?>',    
    go_back:'<?php echo CJavaScript::quote(t("Go back"))?>', 
    complete:'<?php echo CJavaScript::quote(t("Confirm"))?>', 
  }"
  :remove_item="{
    title:'<?php echo CJavaScript::quote(t("Remove this item"))?>',    
    content:'<?php echo CJavaScript::quote(t("This will remove this item from your active orders, and you cannot undo this changes."))?>',    
    go_back:'<?php echo CJavaScript::quote(t("Go back"))?>', 
    confirm:'<?php echo CJavaScript::quote(t("Confirm"))?>', 
  }"
  :out_stock_label="{
    title:'<?php echo CJavaScript::quote(t("Item is Out of Stock"))?>',        
  }"
  
  :update_order_label="{
    title:'<?php echo CJavaScript::quote(t("Order decrease"))?>', 
    title_increase:'<?php echo CJavaScript::quote(t("Order Increase"))?>', 
    content:'<?php echo CJavaScript::quote(t("By accepting this order, we will refund the amount of {{amount}} to customer."))?>',     
    content_collect:'<?php echo CJavaScript::quote(t("Total amount for this order has increase, Send invoice to customer or less from your account with total amount of {{amount}}."))?>',     
    cancel:'<?php echo CJavaScript::quote(t("Cancel"))?>',     
    confirm:'<?php echo CJavaScript::quote(t("Confirm"))?>',     
    send_invoice:'<?php echo CJavaScript::quote(t("Send invoice"))?>',     
    less_acccount :'<?php echo CJavaScript::quote(t("Less on my account"))?>',     
    close :'<?php echo CJavaScript::quote(t("Close"))?>',     
    content_payment:'<?php echo CJavaScript::quote(t("This order has unpaid invoice, until its paid you cannot change the order status."))?>',     
    content_payment:'<?php echo CJavaScript::quote(t("This order has unpaid invoice, until its paid you cannot change the order status."))?>',     
    collect_cash:'<?php echo CJavaScript::quote(t("By accepting this order we will less the commission total {{amount}} to your account."))?>',     
  }"    
  
  >
  </components-orderinfo>
  <!--CONTENT SECTION-->
  
  <!--content_collect:'<?php echo CJavaScript::quote("Total amount for this order has increase, we will send invoice to customer with total amount of {{amount}}.")?>',     -->
    
  <components-menu
  ref="menu"    
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
  @show-item="showItemDetails"
  
  image_placeholder="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"
  merchant_id="<?php echo Yii::app()->merchant->merchant_id?>"  
  :label="{
    previous:'<?php echo CJavaScript::quote(t("Previous"))?>', 
    next:'<?php echo CJavaScript::quote(t("Next"))?>',     
  }"    
  :responsive='<?php echo json_encode($responsive);?>'
  >
  </components-menu>
  
  <components-item-details
  ref="item"    
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"
  @go-back="showMerchantMenu"
  @close-menu="hideMerchantMenu"
  @refresh-order="refreshOrderInformation"  
  
  image_placeholder="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"
  merchant_id="<?php echo Yii::app()->merchant->merchant_id?>"  
  :order_type="order_type"
  :order_uuid="order_uuid"
  >
  </components-item-details>
  
  <components-customer-details
  ref="customer"    
  :client_id="client_id"
  merchant_id="<?php echo Yii::app()->merchant->merchant_id?>"  
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"  
  image_placeholder="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"
  page_limit = "<?php echo Yii::app()->params->list_limit?>"  
  :label="{
    block_customer:'<?php echo CJavaScript::quote(t("Block Customer"))?>', 
    block_content:'<?php echo CJavaScript::quote(t("You are about to block this customer from ordering to your restaurant, click confirm to continue?"))?>',     
    cancel:'<?php echo CJavaScript::quote(t("Cancel"))?>',     
    confirm:'<?php echo CJavaScript::quote(t("Confirm"))?>',     
  }"    
  >
  </components-customer-details>
  
  <components-order-print
  ref="print"      
  :order_uuid="order_uuid"
  mode="popup"
  :line="75"
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>"  
  >
  </components-order-print>
   
 </div> <!--col--> 
 
</div> <!--row-->

</DIV> <!--vue-order-management-->

<?php $this->renderPartial("/orders/order-details",array('ajax_url'=>Yii::app()->createUrl("/apibackend"),'view_admin'=>$view_admin));?>
<?php $this->renderPartial("/orders/template_menu");?>
<?php $this->renderPartial("/orders/template_item");?>
<?php $this->renderPartial("/orders/template_customer");?>
<?php $this->renderPartial("/orders/template_print");?>


<DIV id="vue-bootbox">
<component-bootbox
ref="bootbox"
@callback="Callback"
size='small'
:label="{
  confirm: '<?php echo CJavaScript::quote(t("Delete Confirmation"))?>',
  are_you_sure: '<?php echo CJavaScript::quote(t("Are you sure you want to continue?"))?>',
  yes: '<?php echo CJavaScript::quote(t("Yes"))?>',
  cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>',  
  ok: '<?php echo CJavaScript::quote(t("Okay"))?>',  
}"
>
</component-bootbox>
</DIV>