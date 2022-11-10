<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>

<DIV id="vue-withdrawals" class="card">
<div class="card-body">

<div class="mb-3">
  <h5 class="mb-4"><?php echo t("Payout History")?></h5>
  
  <div class="row">
    <div class="col">
    
     <div class="bg-light p-3 mb-3 rounded">
	   <div class="d-flex align-items-center">    
	     <div class="flex-fill">
	     
	       <div class="d-flex">
	       <p class="m-0 mr-2 text-muted"><?php echo t("Available Balance")?></p><h5 class="m-0">
	         <components-merchant-balance
	         ref="balance"
	         ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
	         @after-balance="afterBalance"
	         >
	         </components-merchant-balance>
	       </h5>
	       </div>
	        
	     </div>
	     <div class="flex-fill text-right">
	      <button 
          @click="requestPayout"
          :disabled="balance<0"
          class="btn btn-green"><?php echo t("Request Payout")?></button>
	     </div>
	   </div> <!--d-flex-->
	  </div><!-- bg-light-->

    
    </div> <!--col-->
    <div class="col">
       <div class="bg-light p-3 mb-3 rounded">
          <div class="d-flex align-items-center">    
            <div class="flex-fill"  v-cloak >
              <h5 class="m-0"><?php echo t("Payout account")?></h5>              
              
              <div class="position-relative">
              <div v-if="is_loading" class="skeleton-placeholder" style="height:20px;width:100px;"></div>
              <p class="m-0 text-muted">{{payout_account}}&nbsp;</p>   
              <p class="m-0 text-muted"><i>{{provider}}&nbsp;</i></p>   
              </div>
              
            </div>
            <div class="flex-fill text-right">
	         <button @click="showAccountForm" class="btn btn-green"><?php echo t("Set Account")?></button>
	        </div>
          </div>
       </div>
    </div> <!--col-->
  </div> <!--row-->
 
  
<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
actions="withdrawalsHistory"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:settings="{
    filter : '<?php echo false;?>',   
    ordering :'<?php echo false;?>',    
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>', 
    placeholder : '<?php echo CJavaScript::quote(t("Start date -- End date"))?>',  
    separator : '<?php echo CJavaScript::quote(t("to"))?>',
    all_transaction : '<?php echo CJavaScript::quote(t("All transactions"))?>'
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
>
</components-datatable>


<components-set-account
ref="account"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 

@after-save="afterSave"

:label="{
  title : '<?php echo CJavaScript::quote(t("Set your default payout account"))?>',  
  payment_method : '<?php echo CJavaScript::quote(t("Select a payment method"))?>',  
  submit : '<?php echo CJavaScript::quote(t("Submit"))?>',  
  close : '<?php echo CJavaScript::quote(t("Close"))?>',  
  
  email_address : '<?php echo CJavaScript::quote(t("Email address"))?>',  
  account_number : '<?php echo CJavaScript::quote(t("Account number"))?>',  
  account_holder_name : '<?php echo CJavaScript::quote(t("Account name"))?>',  
  account_holder_type : '<?php echo CJavaScript::quote(t("Account type"))?>',  
  currency : '<?php echo CJavaScript::quote(t("Currency"))?>',  
  routing_number : '<?php echo CJavaScript::quote(t("Routing number"))?>',  
  country : '<?php echo CJavaScript::quote(t("Country"))?>',    
  
  full_name : '<?php echo CJavaScript::quote(t("Full Name"))?>',  
  billing_address1 : '<?php echo CJavaScript::quote(t("Billing Address Line 1"))?>',  
  billing_address2 : '<?php echo CJavaScript::quote(t("Billing Address Line 2"))?>',  
  city : '<?php echo CJavaScript::quote(t("City"))?>',  
  state : '<?php echo CJavaScript::quote(t("State"))?>',  
  post_code : '<?php echo CJavaScript::quote(t("Postcode"))?>',  
  country : '<?php echo CJavaScript::quote(t("Country"))?>',  
  account_name : '<?php echo CJavaScript::quote(t("Bank Account Holders Name"))?>',  
  account_number_iban : '<?php echo CJavaScript::quote(t("Bank Account Number/IBAN"))?>',  
  swift_code : '<?php echo CJavaScript::quote(t("SWIFT Code"))?>',  
  bank_name : '<?php echo CJavaScript::quote(t("Bank Name in Full"))?>',  
  bank_branch : '<?php echo CJavaScript::quote(t("Bank Branch City"))?>',  
  billing_address : '<?php echo CJavaScript::quote(t("Billing Address"))?>',  
  account_information : '<?php echo CJavaScript::quote(t("Account information"))?>',  
}"  
>
</components-set-account>


<components-request-payout
ref="payout"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
@after-requestpayout="afterRequestpayout"
:balance="balance"
:label="{
  title : '<?php echo CJavaScript::quote(t("Request Payout"))?>',  
  amount : '<?php echo CJavaScript::quote(t("Amount"))?>',  
  submit : '<?php echo CJavaScript::quote(t("Submit"))?>',  
  close : '<?php echo CJavaScript::quote(t("Close"))?>',  
}"  
>
</components-request-payout>

</div> <!--card body-->

</DIV>