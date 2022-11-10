<DIV id="vue-my-address"  v-cloak >

<el-skeleton animated :loading="is_loading" >
<template #template>
  
  <div class="m-3 mb-4">
    <div><el-skeleton-item style="width: 100%;" variant="button" /></div>
    <div><el-skeleton-item style="width: 100%;" variant="text" /></div>
  </div>

  <el-skeleton :count="3" >
  <template #template>
  <div class="row m-0">
      <div class="col-lg-3 mb-3 col-md-6">
         <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
    <div class="col-lg-3 mb-3 col-md-6">
        <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
    <div class="col-lg-3 mb-3 col-md-6">
       <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
    <div class="col-lg-3 mb-3 col-md-6">
       <div><el-skeleton-item style="width: 100%;height:120px" variant="button" /></div>
    </div>
  </div>
  </template>
  </el-skeleton>

</template>
<template #default>


<div class="card p-3 mb-3 d-none d-lg-block" >
 <div class="rounded p-3 grey-bg" >
  <div class="row no-gutters align-items-center">
    
    <div class="col-md-2 ">
       <div class="header_icon _icons location d-flex align-items-center justify-content-center">         
       </div>
    </div>
    
    <div class="col-md-4 ">             
       <h5><?php echo t("Addresses")?></h5>
       <p class="m-0" v-if="data.length>0"><?php echo t("Wow, man of many places :)")?></p>            
       <p class="m-0" v-else><?php echo t("No address, lets change that!")?></p>            
    </div>
    
    <div class="col-md-3 ">     
      <h5>{{animatedTotal}}</h5>
      <p><?php echo t("Addresses")?></p>
    </div>
    
    <div class="col-md-3  text-center ">
      <a class="btn btn-green"  href="javascript:;" @click="showNewAddress">
        <?php echo t("Add new address")?>
      </a>
    </div>
    
  </div>
 </div>
</div> <!--card -->

<!-- mobile view -->
<div class="card mb-3 mt-3 d-block d-lg-none">
<div class="rounded p-3 grey-bg" >
   <div class="d-flex justify-content-between align-items-center w-100">
     <div>
       <h5><?php echo t("Addresses")?></h5>
       <p class="m-0" v-if="data.length>0"><?php echo t("Wow, man of many places :)")?></p>            
       <p class="m-0" v-else><?php echo t("No address, lets change that!")?></p>            
     </div>
     <div>
        <a class="btn btn-green"  href="javascript:;" @click="showNewAddress">
          <?php echo t("Add new address")?>
        </a>
     </div>
   </div>
 </div>
</div>
<!-- mobile view -->

 <!--COMPONENTS NEW ADDRESS-->	 		    
 <component-new-address 
ref="refnewaddress"
:label="{
    title:'<?php echo CJavaScript::quote(t("Change address"))?>', 
    enter_address: '<?php echo CJavaScript::quote(t("Enter delivery address"))?>',	    	    
}"
title="<?php echo t("Add new address")?>"
:addresses=""
:location_data=""
@set-location="setLocationDetails"
>
</component-new-address>
<!--END COMPONENTS NEW ADDRESS-->


 <!--COMPONENTS ADDRESS-->	 	
<?php $maps = CMaps::config();?>   	    
<component-address
ref="address"
@after-save="getAddresses"
@after-delete="getAddresses"
:cmaps_config="{
  provider: '<?php echo CJavaScript::quote($maps['provider'])?>',  
  key: '<?php echo CJavaScript::quote($maps['key'])?>',  
  zoom: '<?php echo CJavaScript::quote($maps['zoom'])?>',
  icon: '<?php echo CJavaScript::quote($maps['icon'])?>',
  icon_merchant: '<?php echo CJavaScript::quote($maps['icon_merchant'])?>',
  icon_destination: '<?php echo CJavaScript::quote($maps['icon_destination'])?>',
}"
:label="{
  title: '<?php echo CJavaScript::quote(t("Address details"))?>',  
  adjust_pin: '<?php echo CJavaScript::quote(t("Adjust pin"))?>',  
  delivery_options: '<?php echo CJavaScript::quote(t("Delivery options"))?>',
  delivery_instructions: '<?php echo CJavaScript::quote(t("Add delivery instructions"))?>',
  notes: '<?php echo CJavaScript::quote(t("eg. ring the bell after dropoff, leave next to the porch, call upon arrival, etc"))?>',
  address_label: '<?php echo CJavaScript::quote(t("Address label"))?>',
  save: '<?php echo CJavaScript::quote(t("Save"))?>',
  cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>',
  location_name: '<?php echo CJavaScript::quote(t("Aparment, suite or floor"))?>',  
  address_label: '<?php echo CJavaScript::quote(t("Address label"))?>',  
  confirm: '<?php echo CJavaScript::quote(t("Confirm"))?>',
  yes: '<?php echo CJavaScript::quote(t("Yes"))?>',
  are_you_sure: '<?php echo CJavaScript::quote(t("Are you sure you want to continue?"))?>',
  complete_address: '<?php echo CJavaScript::quote(t("Complete Address"))?>',
  edit: '<?php echo CJavaScript::quote(t("Edit"))?>',
}"
>
</component-address>
<!--END COMPONENTS  ADDRESS-->

<div class="row equal align-items-center position-relative">

<DIV v-if="reload_loading" class="overlay-loader">
  <div class="loading mt-5">      
    <div class="m-auto circle-loader" data-loader="circle-side"></div>
  </div>
</DIV>  

  <div class="col-lg-3 col-md-4 mb-3 " v-for="item in data" >
   <div class="card p-3 fixed-height address-slot" >
     <h5>{{item.attributes.address_label}}</h5>
     
     <div class="module truncate-overflow">
     <p>{{item.address.formatted_address}}&nbsp;</p>
     </div>
     
     <div class="d-flex justify-content-between">
       <div><a @click="ShowAddress(item.address_uuid)" href="javascript:;" 
class="btn normal small"><?php echo t("Edit")?> <span class="ml-1"><i class="zmdi zmdi-edit"></i></span></a></div>
       
       <div><a @click="ConfirmDelete(item.address_uuid)" 
href="javascript:;" class="btn normal small"><?php echo t("Delete")?></a></div>
       
     </div> <!--flex-->
     
   </div> <!--card-->
  </div> <!--col-->  
  
</div> <!--row-->

</template>
</template>

</DIV>
<!--vue-my-address-->

<?php $this->renderPartial("//components/vue-bootbox")?>