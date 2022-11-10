
<script type="text/x-template" id="xtemplate_merchant_transaction">
<div ref="modal_merchant_transaction" class="modal"
id="modal_merchant_transaction" data-backdrop="static" 
tabindex="-1" role="dialog" aria-labelledby="modal_merchant_transaction" aria-hidden="true">

   <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
     <div class="modal-content"> 
     	     
     
      <div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">{{merchant.name}}</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  
	  <div class="modal-body grey-bg ">

	    
	  <div class="row">
	    <div class="col-md-8 position-relative">

	    <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
		    <div>
		      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
		    </div>
		 </div>
	  
	    
	    <div class="row mb-2 customer-summary">
            <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="summary_orders">0</h5>
                <p class="m-0 text-grey text-truncate"><?php echo t("Orders")?></p>
               </div>
            </div>
            
             <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="summary_cancel">0</h5>
                <p class="m-0 text-grey text-truncate"><?php echo t("Cancel")?></p>
               </div>
            </div>
            
             <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="total_refund">0</h5>
                <p class="m-0 text-grey text-truncate"><?php echo t("Refund")?></p>
               </div>
            </div>
            
             <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="summary_total">0</h5>
                <p class="m-0 text-grey text-truncate"><?php echo t("Total")?></p>
               </div>
            </div>
            
        </div> 
        <!-- row -->
	    
		<div class="card rounded p-2 pt-3 pb-3 small-table">	    
		<components-datatable
		ref="datatable"
		ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
		actions="merchant_transactions"
		:table_col='<?php echo json_encode($table_col_trans)?>'
		:columns='<?php echo json_encode($columns_trans)?>'
		:date_filter='<?php echo true;?>'		
		:transaction_type_list='<?php echo json_encode($transaction_type)?>'
		:merchant_uuid="merchant_uuid"
		:settings="{
		    auto_load : '<?php echo false;?>',   
		    filter : '<?php echo false;?>',   
		    ordering :'<?php echo false;?>',        
		    order_col :'<?php echo 0;?>',   
            sortby :'<?php echo $sortby;?>',   
		    placeholder : '<?php echo t("Start date -- End date")?>',  
		    separator : '<?php echo t("to")?>',
		    all_transaction : '<?php echo t("All transactions")?>'
		  }"  
		page_limit = "<?php echo Yii::app()->params->list_limit?>"  		
		>
		</components-datatable>
		</div>
	    
	    </div>
	    <div class="col-md-4 position-relative" >
	    
	    <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
		    <div>
		      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
		    </div>
		 </div>
	    
	     <h6><?php echo t("Merchant Info")?></h6>   
	     <div class="card rounded p-2 mb-3">
	         <img 
              :src="merchant.logo_url"
              :data-src="merchant.logo_url"
              :data-background-image="image_placeholder"
              class="rounded-pill img-50 mb-2 lozad" >
                           
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b><?php echo t("Name")?></b></div>
                <div class="col-sm-7 text-secondary">{{merchant.name}}</div>
              </div>
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b><?php echo t("Contact")?></b></div>
                <div class="col-sm-7 text-secondary">{{merchant.contact_phone}}</div>
              </div>
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b><?php echo t("Email")?></b></div>
                <div class="col-sm-7 text-secondary">{{merchant.contact_email}}</div>
              </div>
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b><?php echo t("Member since")?></b></div>
                <div class="col-sm-7 text-secondary">{{merchant.member_since}}</div>
              </div>
              
             
              <button class="btn"
               @click="merchantActiveConfirmation"
               :class="{ 'btn-green': !merchant_active, 'btn-red': merchant_active }"
               >
              <div class="d-flex align-items-center">
                 <div class=""><i style="font-size: 20px;" class="zmdi" :class="{ 'zmdi-shield-check':!merchant_active, 'zmdi-block': merchant_active }" ></i></div>
                 <div class="flex-grow-1">
                   <template v-if="!merchant_active"><?php echo t("Activate Merchant")?></template>
                   <template v-if="merchant_active"><?php echo t("Deactivate Merchant")?></template>
                 </div>
              </div>
              </button>
               
	     </div> <!-- card -->
	    
	       <h6><?php echo t("Membership History")?></h6>   
           <div class="card rounded p-2 fixed-height40 scrollable">           
            <div class="list-group list-group-flush">
	         <a v-for="item in plan_history"	 
	         :href="item.direction"   
	         target="_blank"     
	         class="list-group-item list-group-item-action">
	         {{item.address}}
	         </a>	         
	        </div>
	     
	    </div> <!-- col -->
	  </div> <!-- row -->
	  
	  
	  </div> <!-- body -->
	  		 
	  </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->     	  
</script>