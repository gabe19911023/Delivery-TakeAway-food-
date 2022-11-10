<div id="vue-change-address">

<div class="container-fluid" v-cloak v-if="out_of_range">
    <div class="container">
       <div class="d-flex justify-content-center">
              
       <div class="w-50 text-center mt-3 mb-3">   
	      <h4><?php echo t("You're out of range")?></h4>
	      <p class="m-0"><?php echo t("This restaurant cannot deliver to")?> 
	      <span v-if="address_component.address1">{{address_component.address1}} {{address_component.address2}}</span>
	      </p>
	      <a href="javascript:;" @click="show" class="font-weight-bold"><?php echo t("Change address")?></a>
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
	title:'<?php echo CJavaScript::quote(t("Change address"))?>', 
	enter_address: '<?php echo CJavaScript::quote(t("Enter delivery address"))?>',	    	    
}"
:addresses="addresses"
:location_data=""
>
</component-change-address>

<!--COMPONENTS ADDRESS-->	 		 
<?php $this->renderPartial("//components/component-address")?>
<!--END COMPONENTS  ADDRESS-->


</div>
<!--vue-change-address-->