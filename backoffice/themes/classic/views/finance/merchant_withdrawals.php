<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>

<div id="vue-payout" class="card">
<div class="card-body">

<div class="row">
<div class="col">
 <div class="bg-light p-4 mb-3 rounded">
   <div class="d-flex align-items-center">    
     <div class="flex-fill">
     	     	    	       
       <div class="d-flex">
        <p class="m-0 mr-2 text-muted"><?php echo t("Unpaid")?></p><h5 ref="ref_unpaid" class="m-0">0</h5>
       </div>
  
     </div>
     
   </div> <!--d-flex-->
  </div><!-- bg-light-->
</div> <!--col-->
<div class="col">
   <div class="bg-light p-4 mb-3 rounded">
      <div class="d-flex align-items-center">    
        <div class="flex-fill"  >
        
           <div class="d-flex">
	       <p class="m-0 mr-2 text-muted"><?php echo t("Paid")?></p><h5 ref="ref_paid" class="m-0">0</h5>
	       </div>  
        
        </div>            
      </div>
   </div>
</div> <!--col-->
<div class="col">
   <div class="bg-light p-4 mb-3 rounded">
      <div class="d-flex align-items-center">    
        <div class="flex-fill"  >
        
           <div class="d-flex">
	       <p class="m-0 mr-2 text-muted"><?php echo t("Total Unpaid")?></p><h5 ref="total_unpaid" class="m-0">0</h5>
	       </div>  
        
        </div>            
      </div>
   </div>
</div> <!--col-->
<div class="col">
   <div class="bg-light p-4 mb-3 rounded">
      <div class="d-flex align-items-center">    
        <div class="flex-fill"  >
        
           <div class="d-flex">
	       <p class="m-0 mr-2 text-muted"><?php echo t("Total Paid")?></p><h5 ref="ref_total_paid" class="m-0">0</h5>
	       </div>  
        
        </div>            
      </div>
   </div>
</div> <!--col-->
</div> <!--row-->

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
actions="withdrawalList"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:transaction_type_list='<?php echo json_encode($transaction_type)?>'
:date_filter='<?php echo true;?>'
:filter="<?php echo true; ?>"
:settings="{
    auto_load : '<?php echo true;?>',
    filter : '<?php echo false;?>',   
    ordering :'<?php echo false;?>',  
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>',      
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("Payment status")?>'
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
@view-transaction="viewPayoutDetails"
>
</components-datatable>

<components-payout-details
ref="payout"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
:label="{    
    title : '<?php echo CJavaScript::quote(t("Withdrawals Details"))?>',
    close : '<?php echo CJavaScript::quote(t("Close"))?>',
    approved : '<?php echo CJavaScript::quote(t("Process this payout"))?>',
    cancel_payout : '<?php echo CJavaScript::quote(t("Cancel this payout"))?>',
    set_paid : '<?php echo CJavaScript::quote(t("Set status to paid"))?>',
    amount : '<?php echo CJavaScript::quote(t("Amount"))?>',
    payment_method : '<?php echo CJavaScript::quote(t("Payment Method"))?>',
    merchant : '<?php echo CJavaScript::quote(t("Merchant"))?>',
    date_requested : '<?php echo CJavaScript::quote(t("Date requested"))?>',
    status : '<?php echo CJavaScript::quote(t("Status"))?>',
    payment_to_account : '<?php echo CJavaScript::quote(t("Payment to account"))?>',
    account_number : '<?php echo CJavaScript::quote(t("Account number"))?>',
    account_name : '<?php echo CJavaScript::quote(t("Account name"))?>',
    account_type : '<?php echo CJavaScript::quote(t("Account type"))?>',
    account_currency : '<?php echo CJavaScript::quote(t("Account currency"))?>',
    routing_number : '<?php echo CJavaScript::quote(t("Routing number"))?>',
    country : '<?php echo CJavaScript::quote(t("Country"))?>',
    account_holders_name : '<?php echo CJavaScript::quote(t("Account Holders Name"))?>',
    iban : '<?php echo CJavaScript::quote(t("Bank Account Number/IBAN"))?>',
    switf_code : '<?php echo CJavaScript::quote(t("SWIFT Code"))?>',
    bank_name : '<?php echo CJavaScript::quote(t("Bank Name in Full"))?>',
    bank_branch : '<?php echo CJavaScript::quote(t("Bank Branch City"))?>',
    online_payment : '<?php echo CJavaScript::quote(t("Online Payment"))?>',
    offline_payment : '<?php echo CJavaScript::quote(t("offline payment"))?>',
  }"  
@after-save="afterSave"  
>
</components-payout-details>

</div> <!--card body-->
</div> <!--card-->

<?php $this->renderPartial("/finance/filter_payout");?>