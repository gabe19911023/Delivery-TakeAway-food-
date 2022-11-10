<nav class="navbar navbar-light justify-content-between">
  <a class="navbar-brand">
  <h5><?php echo CHtml::encode($this->pageTitle)?></h5>
  </a>     
</nav>

<div id="vue-commission-statement" class="card">
<div class="card-body">


  <div class="row">
    <div class="col">
    
     <div class="bg-light p-4 mb-3 rounded">
	   <div class="d-flex align-items-center">    
	     <div class="flex-fill">
	     	     	    	       
	       <div class="d-flex">
	        <p class="m-0 mr-2 text-muted"><?php echo t("Total Commission")?></p><h5 class="m-0">
	         <components-commission-balance
	         ref="balance"
	         ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
	         @after-balance="afterBalance"
	         >
	         </components-commission-balance>   
	        </h5>
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
		       <p class="m-0 mr-2 text-muted"><?php echo t("Total Balance")?></p><h5 class="m-0">
		       <components-total-balance
		         ref="total_balance"
		         ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
		         @after-balance="afterTotalBalance"
		         >
		       </components-total-balance>   
		       </h5>
		       </div>  
            
            </div>            
          </div>
       </div>
    </div> <!--col-->
    
    <div class="col text-right">   
       <div class="bg-light p-3 mb-3 rounded">
       
        <div class="dropdown">
		  <button class="btn btn-green dropdown-toggle"           
          type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    <?php echo t("Create a Transaction")?>
		  </button>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		    <a @click="createMerchantAdjustment" class="dropdown-item" ><?php echo t("Adjustment")?></a>		    
		  </div>
		</div> 
       
       </div>
    </div> <!--col-->
    
  </div> <!--row-->

<components-datatable
ref="datatable"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
actions="merchant_earninglist"
:table_col='<?php echo json_encode($table_col)?>'
:columns='<?php echo json_encode($columns)?>'
:date_filter='<?php echo false;?>'
:settings="{
    auto_load : '<?php echo true;?>',
    filter : '<?php echo true;?>',   
    ordering :'<?php echo true;?>',  
    order_col :'<?php echo intval($order_col);?>',   
    sortby :'<?php echo $sortby;?>',         
    placeholder : '<?php echo t("Start date -- End date")?>',  
    separator : '<?php echo t("to")?>',
    all_transaction : '<?php echo t("All transactions")?>'
  }"  
page_limit = "<?php echo Yii::app()->params->list_limit?>"  
@view-transaction="viewMerchantTransaction"
>
</components-datatable>


<components-merchant-transaction
ref="merchant_transaction"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
image_placeholder="<?php echo websiteDomain().Yii::app()->theme->baseUrl."/assets/images/placeholder.png"?>"
:label="{    
    block : '<?php echo t("Deactivate Merchant")?>',
    block_content : '<?php echo t("You are about to deactivate this merchant, click confirm to continue?")?>',
    cancel : '<?php echo t("Cancel")?>',
    confirm : '<?php echo t("Confirm")?>',
  }"  
>
</components-merchant-transaction>

<components-merchant-earning-adjustment
ref="merchant_adjustment"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>"
:transaction_type_list='<?php echo json_encode($transaction_type2)?>'
:label="{    
    title : '<?php echo t("Create adjustment")?>',
    close : '<?php echo t("Close")?>',
    submit : '<?php echo t("Submit")?>',
    transaction_description : '<?php echo t("Transaction Description")?>',
    transaction_amount : '<?php echo t("Amount")?>',
    merchant : '<?php echo t("Merchant")?>',
  }"  
@after-save="afterSave"
>
</components-merchant-earning-adjustment>

</div> <!--card body-->
</div> <!--card-->

<?php $this->renderPartial("//finance/template_merchant_transaction",array(
  'table_col_trans'=>$table_col_trans,
  'columns_trans'=>$columns_trans,
  'transaction_type'=>$transaction_type,
  'sortby'=>$sortby,
))?>