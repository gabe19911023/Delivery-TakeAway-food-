<DIV id="vue-manage-plan" class="mt-5">

<div class="row align-items-start">
  <div class="col-md-4">
  
  <components-merchant-status
  ref="merchant_status"
  tpl="1"	
  ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
  
 :label="{    
   current_status : '<?php echo CJavaScript::quote(("Current status"))?>',   
   trial_ended : '<?php echo CJavaScript::quote(("Trial has ended"))?>',		  
 }"   
  >
  </components-merchant-status>
  
  <div class="mt-3"></div> 
  
  <?php AComponentsManager::renderComponents($payments,$payments_credentials,$this,'manage')?>
  
  </div>
  <div class="col-md-8">

   <div class="card"> 
	  <div class="card-body">
	    <h5 class="mb-4">Invoice</h5>
	    	    
	    <components-datatable
		ref="datatable"
		ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
		actions="planInvoiceList"
		:table_col='<?php echo json_encode($table_col)?>'
		:columns='<?php echo json_encode($columns)?>'		
		:settings="{
		    filter : '<?php echo false;?>',   
		    ordering :'<?php echo false;?>',    
		    order_col :'<?php echo intval($order_col);?>',   
		    placeholder : '<?php echo t("Start date -- End date")?>',  
		    separator : '<?php echo t("to")?>',
		    all_transaction : '<?php echo t("All transactions")?>'
		  }"  
		page_limit = "<?php echo Yii::app()->params->list_limit?>"  
		@view-invoice="viewInvoice"
		>
		</components-datatable>
	    
	  </div>
	</div> <!--card--> 
  
  </div>
</div>


<components-planlist
ref="planlist"
ajax_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
@change-plan="changePlan"

:label="{    
  select_payment : '<?php echo CJavaScript::quote(("Select Payment"))?>',
  subscription_plan : '<?php echo CJavaScript::quote(t("Subscription Plans"))?>',  
  terms : '<?php echo CJavaScript::quote($terms)?>',  
  submit : '<?php echo CJavaScript::quote(t('Submit'))?>',  
}"  
  
>
</components-planlist>

<components-loading-box
ref="box"
message="<?php echo t("Processing ...")?>"
donnot_close="<?php echo t("don't close this window")?>"
>
</components-loading-box>


</DIV> <!--vue-->