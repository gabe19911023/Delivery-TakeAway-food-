
<script type="text/x-template" id="xtemplate_payout_filter">
<div sidebarjs>
<div class="card">

  <div class="card-header bg-white">
    <div class="d-flex justify-content-between">
       <div><h5 class="m-0"><?php echo t("Filters")?></h5></div>
       <div>
         <button @click="closePanel" type="button" class="close">
            <span aria-hidden="true">&times;</span>
          </button>
       </div>
    </div>
  </div>
  
  <div class="card-body">
  
     <p class="mb-2"><b><?php echo t("By Merchant")?></b></p>
	 <div class="form-label-group mb-4">  
	 <select ref="merchant_id" class="form-control select2-merchant"  style="width:100%">	  	  
	 </select>
	 </div>
	 
	 	 	 	
  </div> <!-- card-body -->
 
</div> <!-- card -->

<div class="card-footer sidebar-footer bg-white">
  <div class="row">
    <div class="col"><button @click="clearFilter" class="btn btn-black w-100"><?php echo t("Clear Filters")?></button></div>
    <div class="col"><button @click="submitFilter" class="btn btn-green w-100"><?php echo t("Apply Filters")?></button></div>
  </div>
</div>

</div>
</script>