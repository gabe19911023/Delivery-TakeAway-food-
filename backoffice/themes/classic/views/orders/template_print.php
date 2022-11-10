<script type="text/x-template" id="xtemplate_print_order">

<div ref="print_modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static" id="print_modal" data-flag="1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
              
        <h5 class="modal-title" id="exampleModalLabel">Print Order #{{order_info.order_id}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
	    <div>
	      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
	    </div>
	  </div>
	  
      <div class="modal-body printhis">
      	<div v-if="hasData" id='showcanvasFrame'>
			<canvas id='showCanvasPaper' width='576' height='640'>
				Your browser does not support Canvas!
			</canvas>
		</div>
      
      <DIV v-if="hasData" class="m-autox pt-2" style="display:none;">
	      <div class="text-center mb-3">
		      <h5 class="m-0 mb-1">{{merchant.restaurant_name}}</h5>
		      <p class="m-0">{{merchant.merchant_address}}</p>
		      <p class="m-0">Phone : <span>{{merchant.contact_phone}}</span> /  Email : {{merchant.contact_phone}}</p>
	      </div>
	      
	      <span v-for="index in line">-</span>
	      	      
	      <template v-if="order_info.service_code=='pos'">
	      <div class="details mt-2 mb-2">	     
	         <div class="row">
	           <div class="col">Order ID : {{order_info.order_id}}</div>	         
	          </div>	         	        	        
	          <div class="row">
	           <div class="col">Date : {{order_info.place_on_raw}}</div>	         
	          </div>	         	        	        
	          <div class="row">
	           <div class="col">Customer : 
	             <span v-if="order_info.client_id>0">{{order_info.customer_name}}</span>
	             <span v-else>Walk-in Customer</span>
	           </div>	         
	          </div>	         	        	        
	          
	          <div v-if="order_info.order_notes!=''" class="row">
	           <div class="col">Notes : {{order_info.order_notes}}</div>	         
	          </div>	         	        	        
	          
	      </div> <!-- order details -->
	      </template>
	      
	      <template v-else>
	      <div class="details mt-2 mb-2">	        	        	        
	        
	        <div class="row">
	         <div class="col">Order ID : <span>{{order_info.order_id}}</span></div>	         
	        </div>	      
	        
	        <div class="row">
	         <div class="col">Customer Name : <span>{{order_info.customer_name}}</span></div>	         
	        </div>	          
	        
	        <div class="row">
	         <div class="col">Phone : <span>{{order_info.contact_number}}</span></div>	         
	        </div>	          
	        
	        <div class="row">
	         <div class="col">Address : <span>{{order_info.delivery_address}}</span></div>	         
	        </div>	          
	        
	        <div class="row mt-2">
	         <div class="col">Order Type : 
	          <template v-if="services[order_info.service_code]" > {{services[order_info.service_code].service_name}}</template>
	         </div>	         
	        </div>	
	                  
	        <div class="row">
	         <div class="col">Delivery Date/Time :
	           <template v-if="order_info.whento_deliver=='now'">
	           {{order_info.schedule_at}}
	           </template>
	           <template v-else>
	           {{order_info.schedule_at}}
	           </template>	            	            
	         </div>	         
	        </div>	  
	                
	        <div class="row">
	         <div class="col">{{order_info.payment_name}}</div>	         
	        </div>	          
	        
	      </div>
	      <!-- details -->
	      </template>
	      
	      <span v-for="index in line">-</span>
	      
	      
	      <div class="items-details mt-2 mb-2"> 
	      
	       <!-- ITEMS  -->
	       <template v-for="(items, index) in items" >
	       <div class="row mb-1 items-list item">
	         <div class="col">
	           <b>{{items.qty}} x {{ items.item_name }}</b><br/>
	           
	           <template v-if=" items.price.size_name!='' "> 
               ({{items.price.size_name}})
               </template>          
               
               <template v-if="items.price.discount>0">         
	           <del>{{items.price.pretty_price}}</del> {{items.price.pretty_price_after_discount}}
	           </template>
	           <template v-else>
	           {{items.price.pretty_price}}
	           </template>
	           
               <template v-if="items.item_changes=='replacement'">
	           <div class="m-0 text-muted small">
	            Replace "{{items.item_name_replace}}"
	           </div>	           
	           </template>
	           
	           <p class="mb-0" v-if=" items.special_instructions!='' ">{{ items.special_instructions }}</p>
	           
	          <template v-if=" items.attributes!='' "> 
	           <template v-for="(attributes, attributes_key) in items.attributes">                    
	            <p class="mb-0">            
	            <template v-for="(attributes_data, attributes_index) in attributes">            
	              {{attributes_data}}<template v-if=" attributes_index<(attributes.length-1) ">, </template>
	            </template>
	            </p>
	           </template>
	          </template>
               
	         </div> 
	         
	         <div class="col text-right">
	         
	           <template v-if="items.price.discount<=0 ">
	           {{ items.price.pretty_total }}
	           </template>
	          <template v-else>
	           {{ items.price.pretty_total_after_discount }}
	          </template>	           
	         
	         </div>
	       </div> <!-- row -->
	       
	       <!-- ADDON -->
	       <div class="mt-2 mb-2 items-list addon">
	       <template v-for="(addons, index_addon) in items.addons" >
	       <h6 class="m-0 ">{{ addons.subcategory_name }}</h6>
	       
	        <div class="row"  v-for="addon_items in addons.addon_items" >
	         <div class="col">
	            {{addon_items.qty}} x {{addon_items.pretty_price}} {{addon_items.sub_item_name}}
	         </div>
	         <div class="col text-right">{{addon_items.pretty_addons_total}}</div>
	       </div>	       
	       </template>
	       </div>
	       <!-- ADDON -->
	       
	       
	       <!-- ADDITIONAL CHARGE -->  
	       <div class="row mb-1"  v-for="item_charge in items.additional_charge_list" >
	         <div class="col">
	            <i><b>{{item_charge.charge_name}}</b></i>
	         </div>
	         <div class="col text-right">{{item_charge.pretty_price}}</div>
	       </div>
	       <!-- ADDITIONAL CHARGE -->  
	       
	       </template>
	       <!-- ITEMS  -->
	       	       
	      
	      </div>
	      <!-- items-details -->
	      
	      <span v-for="index in line">-</span>
	      
	      <div class="summary mt-2 mb-2"> 
	      	       
	       <div class="row summary-items" v-for="summary in order_summary" >	         
	         
	         <template v-if=" summary.type=='total' ">
	         
	         <div class="col summary-total">	           
	           <h5 class="m-0 summary-heading">{{summary.name}}</h5>
	         </div>
	         <div class="col text-right"><h5 class="m-0 summary-value">{{summary.value}}</h5></div>
	         
	         </template>
	         <template v-else>
	         
	         <div class="col summary-other">	           
	           <span class="summary-heading">{{summary.name}}</span>
	         </div>
	         <div class="col text-right summary-value">{{summary.value}}</div>
	         
	         </template>
	         
	       </div>	      	      
	      
	      </div>
	      <!-- summary -->
	      	      
	      <template v-if="order_info.service_code=='pos'">
	          <div  class="row">
	            <div v-if="payment_list[order_info.payment_code]" class="col">{{payment_list[order_info.payment_code]}}</div>	         
	            <div v-else class="col">{{order_info.payment_code}}</div>	         
	            <div class="col text-right">	              
	              <money-format :amount="order_info.receive_amount" ></money-format>
	            </div>	         
	          </div>	         	        	        
	          <span v-for="index in line">-</span>
	          <div class="row">
	            <div class="col">Total Tendered</div>	         
	            <div class="col text-right"><money-format :amount="order_info.receive_amount" ></money-format></div>	         
	          </div>	         	        	        
	          <div class="row">
	            <div class="col">Change</div>	         
	            <div class="col text-right"><money-format :amount="order_info.order_change" ></money-format></div>	         
	          </div>	         	        	        
	      </template>
	      
	      
	      <span v-for="index in line">-</span>
	      <div class="footer text-center mt-2 mb-2">
	        <h4>{{print_settings.receipt_thank_you}}</h4>	        
	      </div>
	      <span v-for="index in line">-</span>
	      
	      
      </DIV>
      <!-- receipt-container -->
      
      <DIV v-else > 
        <div class="text-center p-3" >
          <h5 class="text-muted" v-if="!is_loading">Data not available</h5>
        </div>
      </DIV>   
      <!-- Start For printing     -->
      	<div class="receipt-container-print" style="display:none;">
		  <span id="merchant_name">{{merchant.restaurant_name}}</span>
		  <span id="merchant_phone">{{merchant.contact_phone}}</span>
		  <span id="order_id">{{order_info.order_id}}</span>
		  <span id="customer_name">{{order_info.customer_name}}</span>
		  <span id="contact_number">{{order_info.contact_number}}</span>
		  <span id="delivery_address">{{order_info.delivery_address}}</span>
		  <span id="merchant_address">{{merchant.address}}</span>
		  <span id="order_type">{{order_info.order_type}}</span>
		  <span id="payment_type">{{payment_list.cod}}</span>
		  <span id="order_time">{{order_info.place_on_raw}}</span>
		    <div class="items-details mt-2 mb-2"> 
		        <template v-for="(items, index) in items" >
		       <div class="row mb-1 items-list item">
		         <div class="col">
		           <b>{{items.qty}} x {{ items.item_name }}</b><br/>
		           
		           <template v-if=" items.price.size_name!='' "> 
		           ({{items.price.size_name}})
		           </template>          
		           
		           <template v-if="items.price.discount>0">         
		           <del>{{items.price.pretty_price}}</del> {{items.price.pretty_price_after_discount}}
		           </template>
		           <template v-else>
		           {{items.price.pretty_price}}
		           </template>
		           
		           <template v-if="items.item_changes=='replacement'">
		           <div class="m-0 text-muted small">
		            Replace "{{items.item_name_replace}}"
		           </div>              
		           </template>
		           
		           
		          <template v-if=" items.attributes!='' "> 
		           <template v-for="(attributes, attributes_key) in items.attributes">                    
		            <p class="mb-0">            
		            <template v-for="(attributes_data, attributes_index) in attributes">            
		              {{attributes_data}}<template v-if=" attributes_index<(attributes.length-1) ">, </template>
		            </template>
		            </p>
		           </template>
		          </template>
		           
		         </div> 
		         
		         <div class="col text-right">
		         
		           <template v-if="items.price.discount<=0 ">
		           {{ items.price.pretty_total }}
		           </template>
		          <template v-else>
		           {{ items.price.pretty_total_after_discount }}
		          </template>              
		         
		         </div>
		       </div> <!-- row -->
		       
		       <!-- ADDON -->
		       <div class="mt-2 mb-2 items-list addon">
		           <template v-for="(addons, index_addon) in items.addons" >
		           <h6 class="m-0 items-addons">{{ addons.subcategory_name }}</h6>
		           
		            <div class="row" :class="addons.subcategory_name.toLowerCase().split(' ').join('')"  v-for="addon_items in addons.addon_items" >
		             <div class="col">
		                {{addon_items.qty}} x {{addon_items.pretty_price}} <span class="sub_item_name">{{addon_items.sub_item_name}}</span>
		             </div>
		             <div class="col text-right">{{addon_items.pretty_addons_total}}</div>
		           </div>          
		           </template>
				   <p class="mb-0 special_instructions" v-if=" items.special_instructions!='' ">{{ items.special_instructions }}</p>
		       </div>
		       <!-- ADDON -->
		       
		       
		       <!-- ADDITIONAL CHARGE -->  
		       <div class="row mb-1"  v-for="item_charge in items.additional_charge_list" >
		         <div class="col">
		            <i><b>{{item_charge.charge_name}}</b></i>
		         </div>
		         <div class="col text-right">{{item_charge.pretty_price}}</div>
		       </div>
		       <!-- ADDITIONAL CHARGE -->  
		       
		    </template>
		     <!-- ADDON -->
		     <div class="mt-2 mb-2 items-list addon">
		         <template v-for="(addons, index_addon) in items.addons" >
		         <h6 class="m-0 items-addons">{{ addons.subcategory_name }}</h6>
		         
		          <div class="row" :class="addons.subcategory_name.toLowerCase().split(' ').join('')"  v-for="addon_items in addons.addon_items" >
		           <div class="col">
		              {{addon_items.qty}} x {{addon_items.pretty_price}} {{addon_items.sub_item_name}}
		           </div>
		           <div class="col text-right">{{addon_items.pretty_addons_total}}</div>
		         </div>        
		         </template>
		    </div>
		    <div class="summary mt-2 mb-2"> 
		                   
		       <div class="row summary-items" v-for="summary in order_summary" >             
		         
		         <template v-if=" summary.type=='total' ">
		         
		         <div class="col summary-total">               
		           <h5 class="m-0 summary-heading">{{summary.name}}</h5>
		         </div>
		         <div class="col text-right"><h5 class="m-0 summary-value">{{summary.value}}</h5></div>
		         
		         </template>
		         <template v-else>
		         
		         <div class="col summary-other">               
		           <span class="summary-heading">{{summary.name}}</span>
		         </div>
		         <div class="col text-right summary-value">{{summary.value}}</div>
		         
		         </template>
		         
		       </div>                 
		    </div>
		     	<!--  <div id='canvasFrame' style="display:none1;">
					<canvas id='canvasPaper' width='576' height='640'>
						Your browser does not support Canvas!
					</canvas>
				</div>  -->
		</div> 
      	<!-- End for printing -->
      </div> <!-- body -->    
      
      <div class="modal-footer justify-content-end border-0">            
         <button class="btn btn-black" data-dismiss="modal" >&nbsp;&nbsp;Close&nbsp;&nbsp;</button>
         <!-- <button @click="print" ref="print_button"
         :disabled="!hasData" class="btn btn-green" >&nbsp;&nbsp;Print&nbsp;&nbsp;</button>-->
         <button 
         :disabled="!hasData" class="btn btn-green print-receipt" >&nbsp;&nbsp;Print&nbsp;&nbsp;</button>
      </div>
      <!-- footer -->
        
    </div> <!-- content -->      
  </div> <!-- dialog -->      
</div>  <!-- modal -->              

</script>