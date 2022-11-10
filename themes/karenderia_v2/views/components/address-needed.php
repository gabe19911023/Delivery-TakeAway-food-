<div id="vue-address-needed" v-cloak>

<div class="container-fluid" v-if="visible" >
    <div class="container">
       <div class="d-flex justify-content-center">
              
       <div class="w-100 text-center mt-3 mb-3">   
	      <h4><?php echo t("Enter your address")?></h4>	      
	      <p class="m-0"><?php echo t("We'll confirm that you can have this restaurant delivered.")?></p>
	      <a href="javascript:;" @click="show" class="font-weight-bold"><?php echo t("Add Address")?></a>
	   </div>
       
       </div>
    </div>
</div>

<!--CHANGE ADDRRESS-->      
<component-change-address
ref="address"
@set-location="afterChangeAddress"
@after-close="afterCloseAddress"	
@set-placeid="afterSetAddress"	
@set-edit="editAddress"
@after-delete="afterDeleteAddress"
:label="{
	title:'<?php echo CJavaScript::quote(t("Delivery Address"))?>', 
	enter_address: '<?php echo CJavaScript::quote(t("Enter your address"))?>',	    	    
}"
:addresses="addresses"
:location_data=""
>
</component-change-address>

</div>
<!--vue-address-needed-->