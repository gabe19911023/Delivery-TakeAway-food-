<script type="text/x-template" id="xtemplate_order_details">

<!--COMPONENTS FORMS-->
<components-rejection-forms
ref="rejection"
ajax_url="<?php echo $ajax_url;?>"  
@after-submit="afterRejectionFormsSubmit"
@after-update="afterUpdateStatus" 
:order_uuid="order_uuid"
 :label="{
    title:'<?php echo CJavaScript::quote("Enter why you cannot make this order.")?>',     
    reject_order:'<?php echo CJavaScript::quote("Reject order")?>',   
    reason:'<?php echo CJavaScript::quote("Reason")?>', 
  }"
>
</components-rejection-forms>		

<components-refund-forms
ref="refund"
:order_uuid="order_uuid"
  :label="{
    title:'<?php echo CJavaScript::quote("Refund payment")?>',     
    refund:'<?php echo CJavaScript::quote("Refund")?>',   
    cancel:'<?php echo CJavaScript::quote("Cancel")?>', 
    refund_full:'<?php echo CJavaScript::quote("Refund the full amount")?>', 
  }"
>
</components-refund-forms>

<div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
    <div>
      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
    </div>
</div>

<template v-if="response_code==2">

   <div class="fixed-height text-center justify-content-center d-flex align-items-center">
     <div class="flex-col">
     <img class="img-300" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/order-best-food@2x.png" />
      <h5 class="mt-3"><?php echo t("Order Details will show here")?></h5>
     </div>     
   </div>

</template>

<template v-else>

<div class="card" v-cloak v-if="!loading" >
  <div class="card-body" >
  
   <div class="row align-items-start">
      <div class="col" >
     <template v-for="button in buttons">
     <button v-if="order_info.status=='accepted'&&order_info.service_code=='delivery'&&button.button_name=='Ready for Delivery' || 
     order_info.status=='delivery on its way'&&order_info.service_code=='delivery'&&button.button_name=='Delivered' ||  
     order_info.status=='delivery on its way'&&order_info.service_code=='delivery'&&button.button_name=='Delivery Failed' ||  
     order_info.status=='accepted'&&order_info.service_code=='pickup'&&button.button_name=='Ready for Pickup' || 
     order_info.status=='ready for pickup'&&order_info.service_code=='pickup'&&button.button_name=='Order failed' || 
     order_info.status=='ready for pickup'&&order_info.service_code=='pickup'&&button.button_name=='Complete' || 
     order_info.status=='new'&&button.button_name=='Accept' || order_info.status=='new'&&button.button_name=='Decline'"  
        :class="button.class_name" 
        @click="doUpdateOrderStatus(button.uuid,order_info.order_uuid,button.do_actions)"        
        class="btn normal mr-2 font13  mb-3 mb-xl-0">
           <span>{{button.button_name}}</span>
           <div class="m-auto circle-loader" data-loader="circle-side"></div> 
      </button>                    
      </template>
      <button v-if="order_info.status!='delivered' && order_info.status!='complete' && order_info.status!='rejected' && order_info.status!='delivery failed'" class="btn-blue btn normal mr-2 font13 mb-3 mb-xl-0" @click="delayOrder"><?php echo t("Delay Order")?></button>   

      <button v-if="manual_status=='1'" class="btn btn-yellow normal mr-2" @click="manualStatusList"><?php echo t("Manual Status")?></button>   
                       
      </div> <!-- flex-col -->

      <div class="col" >
        <div class="d-flex justify-content-end">

      <div class="flex-col mr-3"><button class="btn btn-black normal print-receipt"><?php echo t("Print")?></button></div>
    <!--  <div class="flex-col mr-3"><button class="btn btn-black normal" @click="printOrder" ><?php //echo t("Print")?></button></div>--->
          <div class="flex-col">
                            
           <div class="dropdown dropleft">
			  <a class="rounded-pill rounded-button-icon d-inline-block" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    <i class="zmdi zmdi-more"></i>
			  </a>
			
			  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
			    <!--
			    <template v-if="modify_order">
			    <a v-if="refundAvailable" class="dropdown-item" href="javascript:;" @click="refundFull" >Full refund</a>
			    <a v-if="refundAvailable" class="dropdown-item" href="javascript:;" @click="refundPartial" >Partial refund</a>
			    </template>
			    -->
			    <a class="dropdown-item" href="javascript:;" @click="contactCustomer" ><?php echo t("Contact customer")?></a>
			    <!-- <a v-if="enabled_delay_order" class="dropdown-item" href="javascript:;" @click="delayOrder" ><?php //echo t("Delay Order")?></a> -->
			    <a v-if="modify_order" class="dropdown-item" href="javascript:;" @click="cancelOrder" ><?php echo t("Cancel order")?></a>
			    <a class="dropdown-item" href="javascript:;" @click="orderHistory" ><?php echo t("Timeline")?></a>
			    <a class="dropdown-item" target="_blank" :href="link_pdf.pdf_a4" ><?php echo t("Download PDF (A4)")?></a>			    
			  </div>
		  </div>
          
          </div> <!--flex-col-->
        </div><!--flex--> 
      
      </div>
   </div> <!--flex-->
  
   
   <div class="row mt-3"> 
     <div class="col-md-5">
     
     <div class="d-flex">
        <div class="mr-2">
        <img class="img-20" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/orders-icon.png" >
        </div>
        <div>
          <div class="d-flex align-items-center">
           <div class=""><h5 class="m-0"><?php echo t("Order #")?>{{order_info.order_id}}</h5></div>
           <div class="ml-3">
             <h6 v-if="order_status[order_info.status]" 
             :style="{background:order_status[order_info.status].background_color_hex,color:order_status[order_info.status].font_color_hex}"
             class="font13 m-0 badge">{{order_info.status}}</h6>
           </div>
          </div> <!-- flex -->
          <p class="m-0" id="order_time">{{order_info.place_on}}</p>          
        </div>
      </div> <!--flex-->
      
          
      <?php if($view_admin):?>
      <div class="d-flex mt-3">
        <div class="mr-2">
        <img class="img-20 rounded-circle"  :src="merchant.url_logo">
        </div>
        <div>
          <h5 class="m-0"><?php echo t("Restaurant")?> :</h5>          
          <p class="m-0">{{merchant.restaurant_name}}</p>
          <p class="m-0">{{merchant.contact_phone}}</p>          
          <p class="m-0">{{merchant.contact_email}}</p>
          <p class="m-0">{{merchant.merchant_address}}</p>
          <div class="d-flex">
           <div><a @click="$emit('viewMerchantTransaction')" class="link">{{merchant.order_count}} <?php echo t("Orders")?></a></div>
           <div class="text-green ml-2 mr-2">|</div>
           <div><p class="m-0"><a :href="merchant.restaurant_direction" target="_blank" class="a-12"><u><?php echo t("Get direction")?></u></a></p></div>
          </div>
        </div>
      </div> <!--flex-->
      <?php endif?>
      
      <div class="d-flex mt-3">
        <div class="mr-2">
        <img class="img-20 rounded-circle"  :src="customer.avatar">
        </div>
        <div>
          <h5 class="m-0"><?php echo t("Customer")?> :</h5>          
          <p class="m-0">{{customer.first_name}} {{customer.last_name}}</p>
          <p class="m-0">{{customer.contact_phone}}</p>          
          <p class="m-0">{{customer.email_address}}</p>
          <a @click="showCustomer" class="link">{{customer.order_count}} <?php echo t("Orders")?></a>
        </div>
      </div> <!--flex-->
      
      <div class="d-flex mt-3">
        <div class="mr-2">
        <img class="img-20" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/location.png">
        </div>
        <div>
          <h5 class="m-0"><?php echo t("Other info")?> :</h5>
          <p class="m-0">{{order_info.customer_name}}</p>
          <p class="m-0">{{order_info.contact_number}}</p>          
          <p class="m-0">                      
          <a v-if="modify_order" @click="editOrderInformation" class="link"><i class="zmdi zmdi-edit"></i> <?php echo t("Edit")?></a>
          {{order_info.delivery_address}}
          </p>
          <p class="m-0"><a :href="delivery_direction" target="_blank" class="a-12"><u><?php echo t("Get direction")?></u></a></p>          
        </div>
      </div> <!--flex-->
      
      <!-- <div class="d-flex mt-4">
        <div class="mr-2">        
        <img class="img-20 rounded-pill" :src="merchant.url_logo" >
        </div>
        <div>
          <h5 class="m-0">Merchant information :</h5>
          <p class="m-0">{{merchant.restaurant_name}}</p>
          <p class="m-0">{{merchant.contact_phone}}</p>
          <p class="m-0">{{merchant.merchant_address}}</p>          
          <p class="m-0"><a :href="merchant_direction" target="_blank" class="a-12"><u>Get direction</u></a></p>          
        </div>
      </div> --> <!--flex-->
      
      <table class="table table-bordered mt-4">
      <tr>
       <td><?php echo t("Order type")?></td>
       <td>       
        <span v-if="services[order_info.service_code]" class="badge services" 
        :style="{background:services[order_info.service_code].background_color_hex,color:services[order_info.service_code].font_color_hex}"
        >
          <span id="order_type">{{services[order_info.service_code].service_name}}</span>
        </span>
       </td>
      </tr>
      <tr>
       <td><?php echo t("Date/Time requested")?></td>
       <td>       
       <p v-if="order_info.whento_deliver=='now'" class="m-0 text-muted" id="scheduled_at">{{order_info.schedule_at}}</p>
       <p v-if="order_info.whento_deliver=='schedule'" class="m-0 text-muted" id="scheduled_at">{{order_info.schedule_at}}</p>
       </td>
      </tr>
      <tr>
       <td><?php echo t("Include utensils")?></td>
       <td>         
         <p class="m-0" v-if="order_info.include_utensils==1" ><?php echo t("Yes")?></p>         
       </td>
      </tr>      
      <tr>
       <td><?php echo t("Payment")?></td>
       <td id="payment_type">{{order_info.payment_name}}</td>
      </tr>
      <tr>
       <td><?php echo t("Payment status")?></td>
       <td>
       <p class="m-0" v-if="payment_status[order_info.payment_status]">
        <span
        class="badge"
        :style="{background:payment_status[order_info.payment_status].color_hex,color:payment_status[order_info.payment_status].font_color_hex}"
        >{{payment_status[order_info.payment_status].title}}</span>
       </p>
       </td>
      </tr>      
          
      </table>
     
     </div> <!--col-->
     <div class="col-md-7">
     
     
     <div class="card border">
       <div class="card-body pt-3">
       
        <div class="d-flex mb-4 justify-content-between align-items-center">
           <div><h5 class="m-0"><?php echo t("Summary")?></h5></div>
           <div>
                                 
           <a v-if="modify_order" class="btn btn-green small" href="javascript:;" @click="$emit('showMenu')"
           :class="{disabled : hasInvoiceUnpaid}"
           >
           <i class="zmdi zmdi-plus mr-2"></i><?php echo t("Add")?>
           </a>
           </div>
         </div> 
         
        <!-- ITEMS  -->
        <template v-for="(items, index) in items" >
        <div class="row" >
        
         <div class="col-2 d-flex justify-content-center">
           <img class="rounded img-40" :src="items.url_image" >
         </div>
         
         <div class="col-6 d-flex justify-content-start flex-column">
                  
         <p class="mb-1">
         {{items.qty}}x
         {{ items.item_name }}
          <template v-if=" items.price.size_name!='' "> 
          ({{items.price.size_name}})
          </template>          
          
           <template v-if="items.item_changes=='replacement'">
           <div class="m-0 text-muted small">
            Replace "{{items.item_name_replace}}"
           </div>
           <div class="badge badge-success small"><?php echo t("Replacement")?></div>
           </template>
         </p> 
         
         <template v-if="items.price.discount>0">         
           <p class="m-0 font11"><del>{{items.price.pretty_price}}</del> {{items.price.pretty_price_after_discount}}</p>
         </template>
         <template v-else>
           <p class="m-0 font11">{{items.price.pretty_price}}</p>
         </template>
         
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
        
        <!--<template v-if="modify_order">
        <p class="m-0"><b><?php //echo t("If sold out")?></b></p>
        <p class="m-0 text-danger" v-if="sold_out_options[items.if_sold_out]">
        {{sold_out_options[items.if_sold_out]}}        
        </p>
        </template>
                -->
         </div> <!-- col -->        
         
         <div class="col-3 d-flex justify-content-start flex-column text-right">
           <template v-if="items.price.discount<=0 ">
	          {{ items.price.pretty_total }}
	        </template>
	        <template v-else>
	           {{ items.price.pretty_total_after_discount }}
	        </template>	        
         </div> <!-- col -->
         
         <div  v-if="modify_order"  class="col-1">         
           <div class="dropdown dropleft">
			  <a class="more-vert rounded-pill d-inline-block" href="#" role="button" id="dropdownMenuLink" 
			  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
			  :class="{disabled : hasInvoiceUnpaid}"
			  >
			    <i class="zmdi zmdi-more-vert"></i>
			  </a>
			
			  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
			    <a class="dropdown-item"  @click="markItemOutStock(items)" ><?php echo t("Mark item as Out of Stock")?></a>
			    <a class="dropdown-item"  @click="adjustOrder(items)"><?php echo t("Adjust Item")?></a>
			    <a class="dropdown-item"  @click="additionalCharge(items)"><?php echo t("Add an additonal charge")?></a>			    
			  </div>
		  </div>
         
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
             <div class="col-3 d-flex justify-content-start flex-column text-right">{{ summary.value }}</div>
           </div>
         </template>
       </template>
                                   
                               
       <template v-if="hasTotalDecrease">       
        <hr/>
       <div class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b><?php echo t("Paid by customer")?></b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>{{ summary_changes.total_paid }}</b></div>
       </div>   
       
        <div v-for="refund in summary_changes.refund_list" class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b>{{refund.transaction_description}}</b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>({{ refund.transaction_amount }})</b></div>
       </div>  
       
       <div v-if="summary_changes.refund_due>0" class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b><?php echo t("Refund Due")?></b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>{{ summary_changes.refund_due_pretty }}</b></div>
       </div>
       
        <div v-else class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b><?php echo t("Net payment")?></b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>{{ summary_changes.net_payment }}</b></div>
        </div>      
       
       </template>
       
       <template v-else-if="hasTotalIncrease">       
        <hr/>
                
       <div class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b><?php echo t("Paid by customer")?></b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>{{ summary_changes.total_paid }}</b></div>
       </div>   
       
       
       <div v-for="refund in summary_changes.refund_list" class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b>{{refund.transaction_description}}</b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>({{refund.transaction_amount}})</b></div>
       </div>                
       
       <div class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b><?php echo t("Amount to collect")?></b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>{{ summary_changes.refund_due_pretty }}</b></div>
       </div>
       
       </template>
              
            
       <template v-if="summaryTransaction">   
       <hr/>
       <div class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><?php echo t("Paid by customer")?></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right">{{ summary_transaction.total_paid }}</div>
       </div>   
       
       <div v-for="sumlist in summary_transaction.summary_list" class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column">{{sumlist.transaction_description}}</div>
         <div class="col-3 d-flex justify-content-start flex-column text-right">
           <template v-if="sumlist.transaction_type=='debit'">
             ({{sumlist.transaction_amount}})          
           </template>
           <template v-else>
             {{sumlist.transaction_amount}}
           </template>
         </div>
       </div>  
       
        <div v-else class="row mb-1">
         <div class="col-2 d-flex justify-content-center"></div>
         <div class="col-6 d-flex justify-content-start flex-column"><b><?php echo t("Net payment")?></b></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><b>{{ summary_transaction.net_payment }}</b></div>
        </div>    
       
       </template>
       
       
       
       </div> <!--body-->
     </div><!-- card-->
  
     <!--<div class="mt-3">Time line here</div> -->
     
     </div> <!--col-->
   </div> <!--row-->
             
     <h6 class="font13 mt-1"><?php echo t("Payment history")?></h6>
     <div class="table-responsive-md">
     <table class="table table-bordered">
      <tr>
       <th width="15%"><?php echo t("Date")?></th>
       <th width="15%"><?php echo t("Payment")?></th>
       <th width="25%"><?php echo t("Description")?></th>
       <th width="15%"><?php echo t("Amount")?></th>
       <th width="15%"><?php echo t("Status")?></th>       
      </tr>
      <tr v-for="payment in payment_history">
        <td>{{payment.date_created}}</td>
        <td>{{order_info.payment_name}}</td>
        <td>
        {{payment.transaction_description}} 
        <p v-if="payment.payment_reference" class="text-muted"><i><small>Reference# {{payment.payment_reference}}</small></i></p> 
        </td>
        <td>        
          <template v-if="payment.transaction_type==='debit'">
            <b>({{payment.trans_amount}})</b>
          </template>          
          <template v-else>
            {{payment.trans_amount}}
          </template>
        </td>
        <td>
          <span class="badge payment" :class="payment.status">{{payment.status}}</span>          
          <p v-if="payment.reason" class="text-muted"><i><small>{{payment.reason}}</small></i></p> 
        </td>        
      </tr>
     </table>
      </div>

  </div> <!--body-->
</div> <!--card-->  


<div ref="manual_status_modal" class="modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo t("Select Order Status")?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
    
     
      <div class="list-group list-group-flush">
         <a v-for="item in status_data" 
         @click="stats_id=item.stats_id"
         :class="{ active: stats_id==item.stats_id }"
         class="text-center list-group-item list-group-item-action">
         {{item.description}}
         </a>
      </div>
      
      </div>      
      <div class="modal-footer">            
        <button type="button" @click="confirm" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
         :disabled="!hasData"
         >
          <span><?php echo t("Confirm")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
      </div>
      
    </div>
  </div>
</div>  
<!-- manual_status_modal -->


<div ref="out_stock_modal" class="modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo t("Item is Out of Stock")?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            
      <h5 class="mb-1">{{item_row.item_name}}</h5>
      <h6><?php echo t("is out of stock")?></h6>      
            
      <ul class="list-group list-group-flush">
         <li class="list-group-item">
            <div class="custom-control custom-radio">
			  <input v-model="out_stock_options" 
			  type="radio" id="out_stock_1" name="out_stock_options" class="custom-control-input" value="1">
			  <label class="custom-control-label" for="out_stock_1">Until end of the day</label>
			</div>
         </li>
         
         <li class="list-group-item">
            <div class="custom-control custom-radio">
			  <input v-model="out_stock_options" 
			  type="radio" id="out_stock_2" name="out_stock_options" class="custom-control-input" value="2">
			  <label class="custom-control-label" for="out_stock_2"><?php echo t("Until end of the day tomorrow")?></label>
			</div>
         </li>
         
      </ul>

      </div>      
      <div class="modal-footer">            
        <button type="button" @click="setOutOfStocks" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
         :disabled="!outStockOptions"
         >
          <span><?php echo t("Confirm")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
      </div>
      
    </div>
  </div>
</div>  
<!-- out_stock_modal -->
      

<div ref="adjust_order_modal" class="modal adjust_order_modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo t("Adjust Order")?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-light p-0 m-0">
      
      <div class="p-2 pl-3 pr-3">
        <h5>{{order_info.customer_name}} <?php echo t("said")?> :</h5>
        
        <template v-if="item_row.if_sold_out=='substitute'">        
        <p><?php echo t("Go with merchant recommendation")?></p>
        </template>
        
        <template v-else-if="item_row.if_sold_out=='refund'">
        <p><?php echo t("Refund this item")?></p>
        </template>
        
        <template v-else-if="item_row.if_sold_out=='contact'">
        <h4>{{order_info.contact_number}}</h4>
        <p><?php echo t("Call the customer, ask them if they like to replace")?><br/>
        <?php echo t("the item, refund the item, or cancel the entire order.")?></p>
        </template>
        
        <template v-else-if="item_row.if_sold_out=='cancel'">
        <p><?php echo t("Cancel the entire order")?></p>
        </template>
        
      </div>
                        
      <div class="bg-white p-2 pl-3 pr-3">
        <div class="d-flex justify-content-between">
          <div class="flex-col"><h6>{{item_row.item_name}}</h6></div>
          <div class="flex-col">

          <template v-if="item_row.price">
          {{item_row.price.pretty_total_after_discount}}
          </template>
           
          </div>
        </div>
      </div>
      
      </div>      
      <div class="modal-footer">            
            
        <template v-if="order_info.payment_status=='paid'">
        <button type="button"         
        @click="refundItem" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"           
         >
          <span><?php echo t("Remove Item")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
        </template>
        <template v-else>
         <button type="button"         
        @click="removeItem" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"           
         >
          <span><?php echo t("Remove Item")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
        </template>
        
        <button type="button" @click="replaceItem" class="btn btn-black pl-4 pr-4" :class="{ loading: is_loading }"           
         >
          <span><?php echo t("Replace")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
        
        <button type="button" @click="cancelEntireOrder" class="btn btn-yellow pl-4 pr-4"   
         >
          <span><?php echo t("Cancel the entire order")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
        
      </div>
      
    </div>
  </div>
</div>  
<!-- adjust_order_modal -->      


<div ref="additional_charge_modal" class="modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo t("Add an additional charge")?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

       <div class="form-label-group">    
        <input 
        v-model="additional_charge"  v-maska="'#############'" 
        class="form-control form-control-text" placeholder="" id="charge" type="text"  maxlength="14">   
        <label for="charge" class="required"><?php echo t("Charge amount")?></label> 
       </div>    
       
       <div class="form-label-group">    
        <input 
        v-model="additional_charge_name" 
        class="form-control form-control-text" placeholder="" id="additional_charge_name" type="text"  maxlength="14">   
        <label for="additional_charge_name" class="required"><?php echo t("Reason for additional charge (optional)")?></label> 
       </div>    
       
      </div>      
      <div class="modal-footer">   
      
        <button class="btn btn-black pl-4 pr-4" data-dismiss="modal" >
        <?php echo t("Cancel")?>
        </button>
             
        <button type="button" @click="doAdditionalCharge" 
        class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
         :disabled="!hasValidCharge"
         >
          <span><?php echo t("Add Charge")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>
      </div>
      
    </div>
  </div>
</div>  
<!-- additional_charge_modal -->



<div ref="update_info_modal" class="modal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo t("Update Delivery Information")?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form @submit.prevent="updateOrderDeliveryInformation" >
      <div class="modal-body">
      
            
         <div class="form-label-group">    
          <input v-model="customer_name" type="text" id="customer_name" placeholder="" class="form-control form-control-text">
          <label for="customer_name"><?php echo t("Customer name")?></label>
         </div>    
         
         <div class="form-label-group">    
          <input v-model="contact_number"  v-maska="'############'"
          type="text" id="contact_number" placeholder="" class="form-control form-control-text">
          <label for="contact_number"><?php echo t("Contact number")?></label>
         </div>    
         
         <div class="form-label-group">    
          <input v-model="delivery_address" type="text" id="delivery_address" placeholder="" class="form-control form-control-text">
          <label for="delivery_address"><?php echo t("Address")?></label>
         </div>    
         
         <div class="row">
           <div class="col">
	         <div class="form-label-group">    
	          <input v-model="latitude" type="text" id="latitude" placeholder="" class="form-control form-control-text">
	          <label for="latitude"><?php echo t("Latitude")?></label>
	         </div>    
           </div>
           <div class="col">
              <div class="form-label-group">    
	          <input v-model="longitude" type="text" id="longitude" placeholder="" class="form-control form-control-text">
	          <label for="longitude"><?php echo t("Longitude")?></label>
	          </div>
           </div>
         </div>
         
         
         <div  v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
		    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
		 </div>   
      
       </div>      
      <div class="modal-footer">   

         <button type="button" class="btn btn-black pl-4 pr-4" data-dismiss="modal" >         
         <?php echo t("Cancel")?>
        </button>
             
        <button type="submit" 
        class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"                  
         >
          <span><?php echo t("Save Changes")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button>     
      
      </div>
       </form>
      
    </div>
  </div>
</div>  
	<div class="receipt-container-details" style="display:none;">
		<span id="merchant_name">{{merchant.restaurant_name}}</span>
		<span id="merchant_phone">{{merchant.contact_phone}}</span>
		<span id="order_id">{{order_info.order_id}}</span>
		<span id="customer_name">{{order_info.customer_name}}</span>
		<span id="contact_number">{{order_info.contact_number}}</span>
		<span id="delivery_address">{{order_info.delivery_address}}</span>
		<span id="merchant_address">{{merchant.address}}</span>
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
				
				<div class="row" :class="addons.subcategory_name.toLowerCase().split(' ').join('')"   v-for="addon_items in addons.addon_items" >
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
				
				<div class="row" :class="addons.subcategory_name.toLowerCase().split(' ').join('')"   v-for="addon_items in addons.addon_items" >
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
		<!-- <div id='canvasFrame' style="display:none;">
			<canvas id='canvasPaper' width='576' height='640'>
				Your browser does not support Canvas!
			</canvas>
		</div>  -->
	</div>
<!-- additional_charge_modal -->      
</template>

</script>