<!--promoModal-->
<div class="modal" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">      
      <div class="modal-body">
      
      <a href="javascript:;" @click="close" 
class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
      
      <h4 class="m-0 mb-3 mt-3"><?php echo t("Delivery details")?></h4>

      <div  v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
	    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>   
	   	   
	   <div id="cmaps" :class="{ 'map-large': cmaps_full, 'map-small': !cmaps_full }" ></div>	   
	   	   	   
	   <template v-if="!cmaps_full">
	   <div class="row mt-3" v-if="hasLocationData" >
	     <div class="col">
	       <h5 class="m-0">{{location_data.address.address1}}</h5>	       
	       
	       <template v-if="!inline_edit">
		   <p class="m-0">{{formatted_address}}</p>	  		       
		   <!-- <a href="javascript:;" @click="inline_edit=true"><?php echo t("Edit")?>
		   <span class="ml-1"><i class="zmdi zmdi-edit"></i></span></a> -->
	       </template>
	       
	     </div>
	     <div class="col text-right">
	       <button class="btn small btn-black" @click="adjustPin" :disabled="!hasLocationData" >Adjust pin</button>
	     </div>
	   </div>
	   <!--row-->
	   
	  
	   <template v-if="inline_edit">
	   <div class="mt-2 mb-2">       
			<div class="form-label-group">    
			 <input class="form-control form-control-text" v-model="formatted_address"
			   id="formatted_address" type="text" >   
			 <label for="formatted_address"><?php echo t("Complete Address")?></label> 
			</div>
			
			<a href="javascript:;" @click="inline_edit=false" class="mr-2">Save
			   <span class="ml-1"><i class="zmdi zmdi-check-square"></i></span></a>
			   
			<a href="javascript:;" @click="inline_edit=false">Cancel
			   <span class="ml-1"><i class="zmdi zmdi-close"></i></span></a>   
			
		</div>
	   </template>		 
	   
      
	   <div class="forms mt-2 mb-2">
	   	   
	   <div class="form-label-group">    
         <input class="form-control form-control-text" v-model="location_name"
           id="location_name" type="text" >   
         <label for="location_name">Aparment, suite or floor</label> 
       </div>   
       
      <h5 class="m-0 mt-2 mb-2">Delivery options</h5>       
      <select class="form-control custom-select" v-model="delivery_options">		 
        <option v-for="(items, key) in delivery_options_data" :value="key" >{{items}}</option>      
	  </select>  
       
      <h5 class="m-0 mt-3 mb-2">Add delivery instructions</h5>      
      <div class="form-label-group">    
        <textarea id="delivery_instructions" style="max-height:150px;" v-model="delivery_instructions"  class="form-control form-control-text font13" 
              placeholder="eg. ring the bell after dropoff, leave next to the porch, call upon arrival, etc">
        </textarea>       
      </div>  
      
      <!-- <div class="form-label-group  mt-3">    
         <input class="form-control form-control-text" v-model="address_label"
           id="address_label" type="text" >   
         <label for="address_label">Address label</label> 
       </div>   -->
         
      
      
        <div class="btn-group btn-group-toggle input-group-small mb-4" >
           <label class="btn" v-for="(items, key) in address_label_data" 
               v-model="address_label" :class="{ active: address_label==key }" >
             <input v-model="address_label" type="radio" :value="key" > 
             {{ items }}
           </label>           
        </div>
	  <!--btn-group-->
	   
	   </div> <!--forms-->
     
	   </template>
      </div> <!--modal body-->
      
      <div class="modal-footer justify-content-start">
      
       <template v-if="!cmaps_full">
       <div class="border flex-fill">
           <button class="btn btn-black w-100" @click="hide" >
	          Cancel
	       </button>
       </div>
       <div class="border flex-fill">
           <button class="btn btn-green w-100" @click="save" :class="{ loading: is_loading }" :disabled="!hasLocationData" >
	          <span class="label">Save</span>
	          <div class="m-auto circle-loader" data-loader="circle-side"></div>
	       </button>
       </div>
       </template>
       
       <template v-else-if="cmaps_full">
        <div class="border flex-fill">
           <button class="btn btn-black w-100" @click="cancelPin" >
	          Cancel
	       </button>
       </div>       
       <div class="border flex-fill">
           <button class="btn btn-green w-100" @click="setNewCoordinates" :class="{ loading: is_loading }" :disabled="!hasNewCoordinates"   >
	          <span class="label">Save</span>
	          <div class="m-auto circle-loader" data-loader="circle-side"></div>
	       </button>
       </div>
       </template>
      
      </div> <!--footer-->
    </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->              