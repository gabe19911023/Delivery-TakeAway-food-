
<!--Modal orderTypeTime-->
<div class="modal" id="orderTypeTime" tabindex="-1" role="dialog" aria-labelledby="orderTypeTime" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content 
    <?php echo Yii::app()->params['isMobile']==TRUE?"modal-mobile":"" ?>
    ">      
    
      <div class="modal-body">
             
       <a href="javascript:;" @click="close" 
	   class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a> 
      
      <h5 class="m-0 mb-3 mt-3"><?php echo t("Order Type")?></h5>
           
      <ul class="list-unstyled list-selection m-0 p-0">
      
        <li class="d-flex align-items-center" v-cloak v-for="transaction in transactions" >
         <div class="flexcol  mr-3">
         
            <div class="custom-control custom-radio">
		      <input type="radio" v-model="transaction_type" class="custom-control-input"
              :id="transaction.service_code" :value="transaction.service_code"
		      >
		      <label class="custom-control-label font14 bold" :for="transaction.service_code">
		       {{ transaction.service_name }}		       
		      </label>
		    </div>   	           
         
         </div> <!--flexcol-->         
       </li>       
       
      </ul>  
                  
      </div> <!--body--> 
      
      <div class="divider p-0"></div>
            
      
      <div class="modal-body">
        <h5 class="m-0 mb-3"><?php echo t("Desired time")?></h5>        
         <!--when to delivery-->
        <!-- <div class="btn-group btn-group-toggle input-group-small mb-4" >
           <label class="btn" v-for="when_to in delivery_option" :class="{ active: whento_deliver==when_to.value }" >
             <input v-model="whento_deliver" type="radio" :value="when_to.value" v-model="whento_deliver"> 
             {{ when_to.name }}
           </label>           
        </div> -->
        <!--when-->
         
        
        <div class="schedule-section" v-if="whento_deliver=='schedule'">         
         
         <template v-if="opening_hours_date">
         <select class="form-control custom-select mb-3 custom_delivery_days" v-model="delivery_date" id="custom_delivery_days">		
		     <option v-for="(item, index) in opening_hours_date" :value="item.value"  >
		     {{ item.name}}
		     </option> 
		 </select> 
		 
		 <!-- <select id="delivery_time" class="form-control custom-select" 
		    v-model="delivery_time" v-if="opening_hours_date[delivery_date]" >		
		     <option v-for="(item, index) in opening_hours_time[delivery_date]" 
		     :value="JsonValue(item)"    >
		     {{item.pretty_time}}
			 </option> 
	     </select> -->
	     <select id="custom_delivery_time" class="form-control custom-select custom_delivery_time">
	     </select>
	     </template>
	     
	     <template v-else>
	      <div class="alert alert-warning" role="alert">
	      <p class="m-0"><?php echo t("Not available")?></p>	    
	      </div>
	     </template>
         
		</div> <!--schedule-->
        
		 <div v-cloak v-if="error.length>0" class="alert alert-warning m-0 mt-2" role="alert">
	       <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	     </div>        
		
      </div> <!--modal body-->
      
      <div class="modal-footer justify-content-start">
           
      <button class="btn btn-green w-100" @click="validate" :class="{ loading: is_loading }" >
          <span class="label"><?php echo t("Save")?></span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div>
      </button>
      
      </div> <!--footer-->
    </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->        

<!--END Modal orderTypeTime-->