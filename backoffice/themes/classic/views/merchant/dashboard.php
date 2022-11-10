<DIV id="vue-dashboard" class="dashboard-desktopx">

<div class="row m-0 p-0 ">
<div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r1">   
      <div class="report-inner">
        <h5><?php echo t("Total Orders")?></h5>
        <p ref="summary_orders">0</p>
      </div>
   </div> <!--rounded-status-report--> 
 </div> <!--col-->
 
 <div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r2">   
   
     <div class="report-inner">
        <h5><?php echo t("Total Cancel")?></h5>
        <p ref="summary_cancel">0</p>
      </div>  
   
   </div> <!--rounded-status-report--> 
 </div> <!--col-->
 
 <div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r3">   
   
    <div class="report-inner">
        <h5><?php echo t("Total refund")?></h5>
        <p ref="total_refund">0</p>
      </div>  
   
   </div> <!--rounded-status-report--> 
 </div> <!--col-->
 
 <div class="col p-0 col-lg-3 col-md-3 col-sm-6 col-6  mb-3 mb-xl-0"> 
   <div class="rounded-status-report rounded r4">   
   
   
    <div class="report-inner">
        <h5><?php echo t("Total Sales")?></h5>
        <p ref="summary_total">0</p>
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
      merchant_type="<?php echo $merchant_type;?>"
      :label="{    
	    sales_this_week : '<?php echo CJavaScript::quote(t("Sales this week"));?>',    
	    earning_this_week : '<?php echo CJavaScript::quote(t("Earning this week"));?>',    
	    your_balance : '<?php echo CJavaScript::quote(t("Your balance"));?>',    
	  }"       
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
		    today_sales : '<?php echo CJavaScript::quote(t("Today sales"));?>',    
		    total_refund : '<?php echo CJavaScript::quote(t("Today refund"));?>',    
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
	  merchant_id="<?php echo $merchant_id?>"  
	  image_placeholder="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"
	  page_limit = "<?php echo Yii::app()->params->list_limit?>"  
	  :label="{
	    block_customer:'<?php echo CJavaScript::quote("Block Customer")?>', 
	    block_content:'<?php echo CJavaScript::quote("You are about to block this customer from ordering to your restaurant, click confirm to continue?")?>',     
	    cancel:'<?php echo CJavaScript::quote("Cancel")?>',     
	    confirm:'<?php echo CJavaScript::quote("Confirm")?>',     
	  }"    
	  >
	  </components-customer-details>
	  
	  <div class="position-relative">
	    <components-popular-items   
	       ref="popular_items"
	       ajax_url="<?php echo $ajax_url?>"       
	       :limit="<?php echo intval($limit)?>"
	       :item_tab='<?php echo json_encode($item_tab)?>'
	       :label="{    
	          title : '<?php echo CJavaScript::quote(t("Popular items"));?>',    	    
	          sub_title : '<?php echo CJavaScript::quote(t("latest popular items"));?>',  
	          sold : '<?php echo CJavaScript::quote(t("Sold"));?>',  
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
 
 </div> <!--col-->
 
</div> <!--row--> 

<!--END SECTION SALES OVER VIEW-->
 

</DIV> <!--vue dashboard-->

<?php $this->renderPartial("/orders/template_customer");?>