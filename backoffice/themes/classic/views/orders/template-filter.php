
<script type="text/x-template" id="xtemplate_order_filter">
<div sidebarjs>
<div class="card">

  <div class="card-header bg-white">
    <div class="d-flex justify-content-between">
       <div><h5 class="m-0">Filters</h5></div>
       <div>
         <button @click="closePanel" type="button" class="close">
            <span aria-hidden="true">&times;</span>
          </button>
       </div>
    </div>
  </div>
  
  <div class="card-body">
  
     <p class="mb-2"><b>By customer</b></p>
	 <div class="form-label-group mb-4">  
	 <select ref="client_id" class="form-control select2-customer"  style="width:100%">	  	  
	 </select>
	 </div>
	 	 	 
	 <p class="mb-2"><b>By Status</b></p>
	 <div class="form-label-group mb-4 ">  
	 <select ref="order_status" class="form-control select2-single"  style="width:100%">
	  <option value=""></option>
	  <option v-for="(item,index) in status_list" :value="index">{{item}}</option>    	  
	 </select>
	 </div>
	 	 
	 <p class="mb-2"><b>By Order Type</b></p>
	 <div class="form-label-group mb-4">  
	 <select ref="order_type" class="form-control select2-single"  style="width:100%">
	  <option value=""></option>
	  <option v-for="(item,index) in order_type_list" :value="index">{{item}}</option>    	  
	 </select>
	 </div>
      
  </div> <!-- card-body -->
 
</div> <!-- card -->

<div class="card-footer sidebar-footer bg-white">
  <div class="row">
    <div class="col"><button @click="clearFilter" class="btn btn-black w-100">Clear Filters</button></div>
    <div class="col"><button @click="submitFilter" class="btn btn-green w-100">Apply Filters</button></div>
  </div>
</div>

</div>
</script>