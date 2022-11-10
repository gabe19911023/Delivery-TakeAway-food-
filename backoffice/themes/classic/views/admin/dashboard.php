<DIV id="vue-dashboard">

<div class="row m-0 p-0 ">
 <div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r1">   
      <div class="report-inner">
        <h5><?php echo t("Total Sales")?></h5>
        <p ref="summary_sales">0</p>
      </div>
   </div> <!--rounded-status-report--> 
 </div> <!--col-->
 
 <div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r2">   
   
     <div class="report-inner">
        <h5><?php echo t("Total Merchant")?></h5>
        <p ref="summary_merchant">0</p>
      </div>  
   
   </div> <!--rounded-status-report--> 
 </div> <!--col-->
 
 <div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r3">   
   
    <div class="report-inner">
        <h5><?php echo t("Total Commission")?></h5>
        <p ref="summary_commission">0</p>
      </div>  
   
   </div> <!--rounded-status-report--> 
 </div> <!--col-->
 
 <div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r4">   
   
   
    <div class="report-inner">
        <h5><?php echo t("Total Subscriptions")?></h5>
        <p ref="summary_subscriptions">0</p>
      </div> 
   
   </div> <!--rounded-status-report--> 
 </div> <!--col-->
 
</div> <!--row-->

<div class="row mt-3">
 <div class="col-lg-8 mb-3 mb-xl-0">

    <div class="position-relative mb-3">
      <components-sales-summary
      ref="sales_overview"
      ajax_url="<?php echo $ajax_url?>"        
      :label="{    
	    commission_week : '<?php echo CJavaScript::quote(t("Commission this week"));?>',    
	    commission_month : '<?php echo CJavaScript::quote(t("Commission this month"));?>',    
	    subscription_month : '<?php echo CJavaScript::quote(t("Subscriptions this month"));?>',    
	    }"       
      domain="<?php echo Yii::app()->request->getServerName()?>"        
      >
      </components-sales-summary>
     </div>
     
     <div class="dashboard-statistic position-relative mb-3">
     <components-daily-statistic
        ref="daily_statistic"
        ajax_url="<?php echo $ajax_url?>"  
        :label="{    
		    order_received : '<?php echo CJavaScript::quote(t("Order received"));?>',    
		    today_delivered : '<?php echo CJavaScript::quote(t("Today delivered"));?>',    
		    new_customer : '<?php echo CJavaScript::quote(t("New customer"));?>',    
		    total_refund : '<?php echo CJavaScript::quote(t("Total refund"));?>',    
		}"            
     />
     </components-daily-statistic>            
     </div>     
     
     <div class="position-relative mb-3">
      <components-last-orders
      ref="last_order"
      ajax_url="<?php echo $ajax_url?>"       
      :orders_tab='<?php echo json_encode($orders_tab)?>'
      :limit="<?php echo intval($limit)?>"
      :label="{    
	    title : '<?php echo CJavaScript::quote(t("Last Orders"));?>',    
	    sub_title : '<?php echo CJavaScript::quote(t("Quick management of the last {{limit}} orders", array('{{limit}}'=>$limit) ));?>',    	    
	  }"  
	  @view-customer="viewCustomer"
      >
      </components-last-orders>
      </div>

      <components-customer-details
	  ref="customer"    
	  :client_id="client_id"
	  ajax_url="<?php echo $ajax_url?>"  
	  merchant_id="<?php echo 0?>"  
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
	             
        
      <div class="position-relative mb-3">
	    <components-popular-items   
	       ref="popular_items"
	       ajax_url="<?php echo $ajax_url?>"       
	       :limit="<?php echo intval($limit)?>"
	       :item_tab='<?php echo json_encode($item_tab)?>'
	       :label="{    	          
	          sold : '<?php echo CJavaScript::quote(t("Sold"));?>',	          
	       }"  
	    >
	    </components-popular-items>
	  </div>  
	  
	  <div class="position-relative">
	  <components-popular-merchant   
	       ref="popular_merchant"
	       ajax_url="<?php echo $ajax_url?>"       
	       :limit="<?php echo intval($limit)?>"
	       :item_tab='<?php echo json_encode($popular_merchant_tab)?>'
	       :label="{    	          
	          ratings : '<?php echo CJavaScript::quote(t("ratings"));?>',	          
	       }"  
	    >
	    </components-popular-items>
	  </div>  
 
 </div> <!--col-->
 
 <div class="col-lg-4">
 
   <div class="position-relative">
   <components-chart-sales
   ref="chart"
   ajax_url="<?php echo $ajax_url?>"   
   :months="<?php echo intval($months)?>"
   :label="{    
      sales : '<?php echo CJavaScript::quote(t("sales"));?>',    	    
      sales_overview : '<?php echo CJavaScript::quote(t("Sales overview"));?>',    	        
   }"      
   >
   </components-chart-sales>
   </div>
   
   
   <div class="position-relative mb-3">
    <components-popular-customer   
       ref="popular_customer"
       ajax_url="<?php echo $ajax_url?>"       
       :limit="<?php echo intval($limit)?>"
       :label="{    
       title : '<?php echo CJavaScript::quote(t("Top Customers"));?>',    	    
     }"  
     @view-customer="viewCustomer"
    >
    </components-popular-customer>
    </div>   
 
    <div class="position-relative mb-3">
    <components-latest-review   
       ref="latest_review"
       ajax_url="<?php echo $ajax_url?>"       
       :limit="<?php echo intval($limit)?>"
       :label="{    
          title : '<?php echo CJavaScript::quote(t("Overview of Review"));?>',    	    
          star : '<?php echo CJavaScript::quote(t("Star"));?>',  
          all_review : '<?php echo CJavaScript::quote(t("Checkout All Reviews"));?>',            
     }"       
     @view-customer="viewCustomer"
    >
    </components-latest-review>
    </div>   
    
    <div class="position-relative mb-3">
    <components-recent-payout
      ref="recent_payout"
      ajax_url="<?php echo $ajax_url?>"       
      :limit="<?php echo intval($limit)?>"
      :label="{    
          recent_payout : '<?php echo CJavaScript::quote(t("Recent payout"));?>',    	              
     }"       
     @view-payout="viewPayout"
    >
    </components-recent-payout>
    </div>
    
    <components-payout-details
	ref="payout"
	ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
	:label="{    
	    title : '<?php echo t("Withdrawals Details")?>',
	    close : '<?php echo t("Close")?>',
	    approved : '<?php echo t("Process this payout")?>',
	    cancel_payout : '<?php echo t("Cancel this payout")?>',
	    set_paid : '<?php echo t("Set status to paid")?>',
	  }"  
	@after-save="afterSave"  
	>
	</components-payout-details>
   
    
 </div> <!--col-->
 
</div> <!--row--> 

</DIV> <!--vue-->

<?php $this->renderPartial("/orders/template_customer_all");?>