<script type="text/x-template" id="xtemplate_order_details_pos">
<div class="card">

  <div class="card-body">
    
	<div class="input-group mb-3">
	  <select ref="customer" class="custom-select" id="inputGroupSelect02">   	      
	  </select>
	  <div class="input-group-append">
	    <label @click="showCustomer" class="input-group-text cursor-pointer btn-green" for="inputGroupSelect02">
	      <i class="zmdi zmdi-account-add"></i>
	    </label>
	  </div>
	</div>    
  
   <h5 class="mb-2"><?php echo t("Items")?></h5>
          
   <!-- ITEMS  -->   
   <DIV class="pos-order-details nice-scroll p-2 pb-0">
    <template v-for="(items, index) in items" >
    <div class="row" >
    
     <div class="col-2 d-flex justify-content-center">
       <img class="rounded img-40" :src="items.url_image" >
     </div>
       
     <div class="col-5 d-flex justify-content-start flex-column">
              
	     <p class="mb-1">
	     <!-- {{items.qty}}x -->
	     {{ items.item_name }}
	      <template v-if=" items.price.size_name!='' "> 
	      ({{items.price.size_name}})
	      </template>                      
	     </p> 
	     	    
	     <template v-if="items.price.discount>0">         
	       <p class="m-0 font11"><del>{{items.price.pretty_price}}</del> {{items.price.pretty_price_after_discount}}</p>
	     </template>
	     <template v-else>
	       <p class="m-0 font11">{{items.price.pretty_price}}</p>
	     </template>
	     
	     <!-- QUANTITTY -->
	     <div class="mt-1 mb-1 quantity-wrap">
		  <div class="quantity d-flex justify-content-between align-items-center">
		    <div>
		      <a @click="changeQty(items,'less')" href="javascript:;" class="rounded-pill qty-btn">
		        <i class="zmdi zmdi-minus"></i>
		      </a>
		    </div>
		    <div class="qty">{{items.qty}}</div>
		    <div>
		      <a  @click="changeQty(items,'add')"  href="javascript:;" class="rounded-pill qty-btn">
		        <i class="zmdi zmdi-plus"></i>
		      </a>
		    </div>
		  </div>
		</div>
		<!-- QUANTITTY -->
	     	     
	     <p class="mb-0 text-success" v-if=" items.special_instructions!='' ">{{ items.special_instructions }}</p>
	     
	     <template v-if=" items.attributes!='' "> 
	      <template v-for="(attributes, attributes_key) in items.attributes">                    
	        <p class="mb-0">            
	        <template v-for="(attributes_data, attributes_index) in attributes">            
	          {{attributes_data}}<template v-if=" attributes_index<(attributes.length-1) ">, </template>
	        </template>
	        </p>
	      </template>
	    </template>
	                    
     </div> <!-- col -->        
     
     <div class="col-4 d-flex justify-content-start flex-column text-right pr-0 pl-0">
     
       <!--REMOVE ITEM -->
       <a @click="removeItem(items)" href="javascript:;" class="rounded-pill circle-button ml-auto mb-1"><i class="zmdi zmdi-close"></i></a>
     
       <template v-if="items.price.discount<=0 ">
          {{ items.price.pretty_total }}
        </template>
        <template v-else>
           {{ items.price.pretty_total_after_discount }}
        </template>	        
     </div> <!-- col -->
          
   </div> <!-- row -->
   <!-- ITEMS  -->       
  
    <!-- ADDON -->
   <div class="row mb-2" v-for="(addons, index_addon) in items.addons" >
      <div class=" col-2 d-flex justify-content-center">&nbsp;</div>
      <div class=" col-9 d-flex justify-content-start flex-column">
        <p class="m-0"><b>{{ addons.subcategory_name }}</b></p>		
        
        <div class="row" v-for="addon_items in addons.addon_items" >
          <div class=" col-8">
            <p class="m-0">{{addon_items.qty}} x {{addon_items.pretty_price}} {{addon_items.sub_item_name}}</p>
          </div> <!-- col -->          
          <div class=" col-4 text-right">
            <p class="m-0">{{addon_items.pretty_addons_total}}</p>
          </div>
          <!-- col -->          
        </div>
          
      </div> <!-- col -->          
   </div> <!-- row -->
   <!-- ADDON -->
   
   <!-- ADDITIONAL CHARGE -->       
   <div class="row mb-2" v-for="item_charge in items.additional_charge_list" >
      <div class=" col-2 d-flex justify-content-center">&nbsp;</div>
      <div class=" col-6 d-flex justify-content-start flex-column">
        <span class="text-success">{{item_charge.charge_name}} </span>
      </div>
      <div class=" col-3 d-flex justify-content-start flex-column text-right">
       <p class="m-0">{{item_charge.pretty_price}}</p>
      </div>
   </div> <!-- row -->
   <!-- ADDITIONAL CHARGE -->
   
   <hr> 
   </template>
   </DIV> <!-- END OF ITEMS -->
   

   <DIV class="pt-3 "> 
   <template v-for="summary in order_summary" >
     <template v-if=" summary.type=='total' ">
     <hr/>
     <div class="row mb-1">
       <div class="col-2 d-flex justify-content-center"></div>
       <div class="col-6 d-flex justify-content-start flex-column"><h6 class="m-0">{{summary.name}}</h6></div>
       <div class="col-3 d-flex justify-content-start flex-column text-right"><h6 class="m-0">{{summary.value}}</h6></div>
     </div>
     </template>
     
     <template v-else>
       <div class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column">{{ summary.name }}</div>
         <div class="col-3 d-flex justify-content-start flex-column text-right">
         
         <template v-if="summary.type=='voucher'">
           <div class="d-flex">             
             <div>{{ summary.value }}</div>
             <div class="ml-2"><a ref="voucher"  @click.once="removeVoucher"><h5 class="m-0"><i class="zmdi zmdi-delete"></i></h5></a></div>
           </div>
         </template>
         <template v-else>
         {{ summary.value }}
         </template>
         
         </div>
       </div>
     </template>
   </template>
   
  
  <div class="btn-group btn-group-lg w-100 mt-3" role="group" aria-label="Large button group">
    <button @click="showPromo" :disabled="!hasData" type="button" class="btn btn-secondary text-left">
      <p class="m-0"><i class="zmdi zmdi-label"></i></p>
      <p class="m-0"><?php echo t("Promo")?></p>
    </button>
    <button :disabled="!hasData" type="button" class="btn btn-secondary text-left">
      <p class="m-0"><i class="zmdi zmdi-money-off"></i></p>
      <p class="m-0"><?php echo t("Discount")?></p>
    </button>    
    <button @click="resetPos" type="button" class="btn btn-secondary text-left">
      <p class="m-0"><i class="zmdi zmdi-refresh"></i></p>
      <p class="m-0"><?php echo t("Reset")?></p>
    </button>    
  </div>
  
  <button @click="showPayment" class="btn-green btn w-100 mt-2" :disabled="!hasData">
   <div class="d-flex justify-content-between align-items-center">
     <div class="flex-col text-left">
       <p class="m-0"><b><?php echo t("Proceed to pay")?></b></p>
       <p class="m-0"><i>{{items.length}} <?php echo t("Items")?></i></p>
     </div>
     <div class="flex-col">
       <h5>       
       <money-format :amount="summary_total" ></money-format>
       </h5>
     </div>
   </div>
  </button>  
  </DIV>
   
 
       
  
  </div> <!-- card body -->
</div> <!-- card -->

<components-customer-entry
ref="customer_entry"    
  :ajax_url="ajax_url"  
  :label="{
      clear_items:'<?php echo CJavaScript::quote(t("Clear all items"))?>',     
      customer:'<?php echo CJavaScript::quote(t("Customer"))?>',     
      first_name:'<?php echo CJavaScript::quote(t("First Name"))?>',     
      last_name:'<?php echo CJavaScript::quote(t("Last Name"))?>',     
      emaiL_address:'<?php echo CJavaScript::quote(t("Email address"))?>',     
      contact_phone:'<?php echo CJavaScript::quote(t("Contact Phone"))?>',     
      submit:'<?php echo CJavaScript::quote(t("Submit"))?>',     
  }"  
  @after-savecustomer="afterSavecustomer"
>
</components-customer-entry>


<div ref="promo_modal" class="modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
      
      <a @click="closePromo" href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>
      
      <h4 class="m-0 mb-3 mt-3"><?php echo t("Have a promo code?")?></h4>
      
      <form @submit.prevent="applyPromoCode"  class="forms mt-2 mb-2">
	  <div class="form-label-group">
	    <input ref="promo_code" v-model="promo_code" class="form-control form-control-text" placeholder="" id="promo_code" type="text" maxlength="20">
	    <label for="promo_code" class="required"><?php echo t("Add promo code")?></label>
	  </div>
	  </form>
      
      </div>      
      <div class="modal-footer border-0">            
        <button type="button" @click="applyPromoCode" class="btn btn-green pl-4 pr-4" :class="{ loading: promo_loading }"         
         :disabled="!hasCoopon"
         >
          <span><?php echo t("Apply")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
      </div>
      
    </div>
  </div>
</div>  
<!-- modal -->


<div ref="submit_order_modal" class="modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
         <a @click="closePayment" href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>
         
         <h4 class="m-0 mb-3 mt-3"><?php echo t("Create Payment")?></h4>
         
         <div class="menu-categories medium mb-3 mt-4 d-flex">
		  <a class="text-center rounded align-self-center text-center">
		    <h6 class="m-0"><?php echo t("Total Due")?></h6>
		    <h5 class="m-0 text-green word_wrap"><money-format :amount="summary_total" ></money-format></h5>
		  </a>
		  <!--<a class="text-center rounded align-self-center text-center">
		    <h6 class="m-0">Total Paying</h6>
		    <h5 class="m-0 text-violet word_wrap"><money-format :amount="receive_amount" ></money-format></h5>
		  </a>-->
		  <a class="text-center rounded align-self-center text-center">
		    <h6 class="m-0"><?php echo t("Pay Left")?></h6>
		    <h5 class="m-0 text-danger word_wrap"><money-format :amount="pay_left" ></money-format></h5>
		  </a>
		  <a class="text-center rounded align-self-center text-center">
		    <h6 class="m-0"><?php echo t("Change")?></h6>
		    <h5 class="m-0 text-orange word_wrap"><money-format :amount="change" ></money-format></h5>
		  </a>
		</div>
         
         <div class="row">
           <div class="col">            
             <div class="form-label-group">
			    <input ref="receive_amount" v-model="receive_amount" 
			     v-maska="'#*.##'" 
			    class="form-control form-control-text" placeholder="" id="receive_amount" type="text" maxlength="14">
			    <label for="receive_amount" class="required"><?php echo t("Receive amount")?></label>
			  </div>             
           </div>
           <div class="col">            
             <div class="form-label-group">             
             <select ref="payment_code" v-model="payment_code" class="form-control custom-select form-control-select" id="payment_code">   
               <template v-for="payment in payment_list" >
               <option :value="payment.payment_code" :selected="payment.payment_code==payment_code" >{{payment.payment_name}}</option>
               </template>
	         </select>
	         </div>
           </div>
         </div>
         <!-- row --> 
         
         <div class="row">
          <div class="col">
           <textarea v-model="order_notes" 
           placeholder="Add order note"
           class="form-control form-control-select">
           </textarea>
          </div>
         </div>
                  
         
      </div>
      <!-- modal body --> 
      
      <div class="modal-footer border-0">            
        <button type="button" @click="submitOrder" class="btn btn-green pl-4 pr-4" :class="{ loading: create_payment_loading }"         
         :disabled="!hasValidPayment"
         >
          <span><?php echo t("Submit")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
      </div>

    </div>
  </div>
</div>  
<!-- modal -->      

</script>