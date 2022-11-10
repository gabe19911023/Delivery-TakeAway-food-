<DIV id="vue-my-payments" v-cloak >


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

<div class="card p-3 mb-3 d-none d-lg-block" v-if="!is_loading"  >
 <div class="rounded p-3 grey-bg" >
  <div class="row no-gutters align-items-center">
    <div class="col-md-2">
       <div class="header_icon _icons credit_card d-flex align-items-center justify-content-center">         
       </div>
    </div>
    
    <div class="col-md-6">             
       <h5><?php echo t("Payment")?></h5>
       <p class="m-0"><?php echo t("You can add your payment info here")?></p>
    </div>
    
    <div class="col-md-4 text-center">
      <a class="btn btn-green"  href="javascript:;" @click="payment_method = !payment_method">
        <template v-if="!payment_method"><?php echo t("Add new payment")?></template>
        <template v-if="payment_method"><?php echo t("Close Payment")?></template>
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
        <h5><?php echo t("Payment")?></h5>
        <p class="m-0"><?php echo t("You can add your payment info here")?></p>
     </div>
     <div>
      <a class="btn btn-green"  href="javascript:;" @click="payment_method = !payment_method">
          <template v-if="!payment_method"><?php echo t("Add new payment")?></template>
          <template v-if="payment_method"><?php echo t("Close Payment")?></template>
        </a>
     </div>
   </div>
 </div>
</div>
<!-- mobile view -->

<!--COMPONENTS PAYMENT METHOD-->
<components-payment-method
ref="payment_method"
payment_type='default'
@set-Payment="showPayment"
:label="{
  add_new_payment: '<?php echo CJavaScript::quote(t("Add New Payment Method"))?>',
}"
>
</components-payment-method>
<!--COMPONENTS PAYMENT METHOD-->

<div class="row equal align-items-center position-relative">

<DIV v-if="reload_loading" class="overlay-loader">
  <div class="loading mt-5">      
    <div class="m-auto circle-loader" data-loader="circle-side"></div>
  </div>
</DIV>  

<div class="col-lg-3 mb-3 col-md-6" v-for="item in data" >   
   <div class="card p-3 fixed-height card-listing" >
   
     <div class="d-flex">
        <div class="flex-col">
          <i v-if="item.logo_type=='icon'" :class="item.logo_class" class="font20"></i>
	      <img v-else class="img-35" :src="item.logo_image" /> 
        </div>
        <div class="flex-col flex-grow-1">
          <h5 class="ml-2">{{item.attr1}} 
            <span v-if="item.as_default==1">                    
             <i class="zmdi zmdi-check text-success font20 ml-2"></i>
            </span>
          </h5>
        </div>
        <div class="flex-col">
        
        <div class="dropdown">
         <a href="javascript:;" class="rounded-pill rounded-button-icon d-inline-block" 
         id="dropdownMenuLink" data-toggle="dropdown" >
           <i class="zmdi zmdi-more" style="font-size: inherit;"></i>
         </a>
             <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
			    <a  v-if="item.as_default!=1" 
         @click="setDefaultPayment(item.payment_uuid)"
         class="dropdown-item a-12" href="javascript:;"><?php echo t("Set Default")?></a>
			    			   
			  </div>
         </div> <!--dropdown-->
        
        
        </div> <!--flex col-->
	 </div> <!--flex-->     
     
     <div class="text-truncate">
     <p>{{item.attr2}}&nbsp;</p>
     </div>
     
     <div class="d-flex justify-content-between">
       <div><a @click="editPayment(item)" href="javascript:;" :disabled="item.reference_id==0"
class="btn normal small"><?php echo t("Edit")?> <span class="ml-1">
       <i class="zmdi zmdi-edit"></i></span></a>
       </div>
       
       <div><a @click="ConfirmDelete(item.payment_uuid)" 
href="javascript:;" class="btn normal small"><?php echo t("Delete")?></a></div>
       
     </div> <!--flex-->
     
   </div> <!--card-->
  </div> <!--col-->  

</div> <!--row-->

<!--RENDER PAYMENT COMPONENTS-->
<?php CComponentsManager::renderComponents($payments,$payments_credentials,$this)?>

  </template>
</template>

</DIV>
<!--vue-my-payments-->

<?php $this->renderPartial("//components/vue-bootbox")?>