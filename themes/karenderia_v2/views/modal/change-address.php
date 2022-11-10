<!--changeAddressModal-->
<div class="modal change-address-modal" id="changeAddressModal" tabindex="-1" role="dialog" aria-labelledby="changeAddressModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">  <!--opened-->    
      <div class="modal-body">
      
      <h4 class="m-0 mb-3"><?php echo t("Change Address")?></h4>

      <div  v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
	    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
	   </div>   
	  	   
	   
	   <div class="position-relative search-geocomplete"> 
	     <div v-if="!awaitingSearch" class="img-20 m-auto pin_placeholder icon"></div>
	     <div v-if="awaitingSearch" class="icon" data-loader="circle"></div>
	     <input @click="showList" v-model="q" class="form-control form-control-text" placeholder="Enter delivery address" >
	     <div v-if="hasData" @click="clearData" class="icon-remove"><i class="zmdi zmdi-close"></i></div>
	   </div>
	   	   
	   <div class="search-geocomplete-results">
	    <ul class="list-unstyled m-0" v-if="show_list">
	     <li v-for="items in data">
	      <a href="javascript:;" @click="setLocationDetails(items.id)"  >
	       <h6 class="m-0">{{items.addressLine1}}</h6>
	       <p class="m-0 text-grey">{{items.addressLine2}}</p>
	      </a>
	     </li>	     
	    </ul>
	   </div>
     
      </div> <!--modal body-->
      
      <div class="modal-footer justify-content-start">
      
      
      
      </div> <!--footer-->
    </div> <!--content-->
  </div> <!--dialog-->
</div> <!--modal-->              
