<script type="text/x-template" id="xtemplate_customer">


<div ref="customer_modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static" >
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Customer ID #{{client_id}} </h5>        
        <button type="button" class="close" @click="close" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
	    <div>
	      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
	    </div>
	  </div>
	  
      <div class="modal-body grey-bg">
      
      <div class="row" :class="{'d-none':!hasData}">
         <div class="col-md-8">
           
           <h6>Orders</h6>  
           
           
           <div class="row mb-2 customer-summary">
            <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="summary_orders">0</h5>
                <p class="m-0 text-grey text-truncate">Orders</p>
               </div>
            </div>
            
            <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="summary_cancel">0</h5>
                <p class="m-0 text-grey text-truncate">Cancel</p>
               </div>
            </div>
            
            <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="summary_refund">0</h5>
                <p class="m-0 text-grey text-truncate">Refund</p>
               </div>
            </div>
            
            <div class="col-2 pr-1">
               <div class="card rounded text-center">
                <h5 class="m-0 mb-1 text-truncate" ref="summary_total">0</h5>
                <p class="m-0 text-grey text-truncate">Total</p>
               </div>
            </div>
            
           </div> 
           <!-- row -->
           
           <div class="card rounded p-2 pt-3 pb-3 small-table">
           
           <table ref="order_table" class="table table-sm w-100 order_table">
            <thead> 
             <tr>
              <td>Order ID</td>
              <td>Total</td>
              <td>Status</td>
              <td width="25%">Action</td>
             </tr>
            </thead>
            <tbody>            
            </tbody>
           </table>
           
           </div> <!--- card -->
         
         </div> 
         <div class="col-md-4">
         
           <h6><?php echo t("Customer Info")?></h6>   
           <div class="card rounded p-2 mb-3">
               
              <img 
              :src="customer.avatar"
              :data-src="customer.avatar"
              :data-background-image="image_placeholder"
              class="rounded-pill img-50 mb-2 lozad" >
              
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b>Name</b></div>
                <div class="col-sm-7 text-secondary">{{customer.first_name}} {{customer.last_name}}</div>
              </div>
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b>Contact</b></div>
                <div class="col-sm-7 text-secondary">{{customer.contact_phone}}</div>
              </div>
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b>Email</b></div>
                <div class="col-sm-7 text-secondary">{{customer.email_address}}</div>
              </div>
              <div class="row no-gutters mb-1">
                <div class="col-sm-4"><b>Member since</b></div>
                <div class="col-sm-7 text-secondary">{{customer.member_since}}</div>
              </div>
                            
              <button class="btn"
               @click="blockCustomerConfirmation"
               :class="{ 'btn-green': block_from_ordering, 'btn-red': !block_from_ordering }"
               >
              <div class="d-flex align-items-center">
                 <div class=""><i style="font-size: 20px;" class="zmdi" :class="{ 'zmdi-shield-check': block_from_ordering, 'zmdi-block': !block_from_ordering }" ></i></div>
                 <div class="flex-grow-1">
                   <template v-if="block_from_ordering"><?php echo t("Unblock Customer")?></template>
                   <template v-if="!block_from_ordering"><?php echo t("Block Customer")?></template>
                 </div>
              </div>
              </button>
                            
           </div> <!-- card -->
           
           <h6>Addresses</h6>   
           <div class="card rounded p-2 fixed-height40 scrollable">           
            <div class="list-group list-group-flush">
	         <a v-for="item in addresses"	 
	         :href="item.direction"   
	         target="_blank"     
	         class="list-group-item list-group-item-action">
	         {{item.address}}
	         </a>	         
	        </div>
           
           </div> <!-- card -->
         
         </div> <!-- col -->
      </div> <!-- row -->

           
       <div v-if="!hasData" class="fixed-height40 text-center justify-content-center d-flex align-items-center">
	     <div class="flex-col">
	     <img class="img-300" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/order-best-food@2x.png" />
	      <h5 class="mt-3"><?php echo t("Customer information not found")?></h5>
	     </div>     
	   </div>  
  
      </div> <!--modal body-->
      
     
    </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->

</script>