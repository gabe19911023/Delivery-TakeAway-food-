

<!--ADDRESS AND DELIVERY DETAILS-->
<component-transaction-info
ref="transaction_info" 
@after-click="setTransactionInfo"
>
</component-transaction-info>

<!--POPUP DELIVERY DETAILS-->
<component-delivery-details
ref="transaction"      
@show-address="showAddress"
@show-trans-options="ShowTransOptions"
:label="{
title:'<?php echo CJavaScript::quote(t("Delivery details"))?>', 
done: '<?php echo CJavaScript::quote(t("Done"))?>',	    
}"
>
</component-delivery-details>


<!--CHANGE DELIVERY OPTIONS SCHEDULE OR NOW-->
<component-trans-options
ref="transaction_options" 
:label="{
title:'<?php echo CJavaScript::quote(t("Time for delivery"))?>', 
save: '<?php echo CJavaScript::quote(t("Save"))?>',	    	    
}"
@after-save="afterSaveTransOptions"
@after-close="afterCloseAddress"
>
</component-trans-options>

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

<!--END COMPONENTS	-->	 