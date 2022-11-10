
<div id="vue-widget-nav-mobile" class="mt-2 d-block d-lg-none d-flex justify-content-between align-items-center">
    <div class="flexcol">
       <!--ADDRESS AND DELIVERY DETAILS-->       
      <component-transaction-info
      ref="transaction_info" 
      @after-click="setTransactionInfo"
      layout="widget-dropdown-address"
      :transaction_type ="transaction_type"
      :label="{
           now:'<?php echo CJavaScript::quote(t("Now"))?>', 
        }"
      >
      </component-transaction-info> 
      
    </div>

    <div class="flexcol widget-services">
    <component-services      
     @after-settransaction="reloadFeed"
     @set-transaction="setTransaction"
     >
     </component-services>
    </div>

    <div class="widget-search">
       <a  @click="showSearchSuggestion"  v-cloak
		   class="btn bg-light btn-circle rounded-pill"><i class="zmdi zmdi-search font20"></i></a>   
    </div>
    <!-- widget-search -->


<component-mobile-search-suggestion
ref="search_suggestion"
ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
:tabs_suggestion='<?php echo json_encode(AttributesTools::suggestionTabs())?>'
:label="{		    
	clear: '<?php echo CJavaScript::quote(t("Clear"))?>', 
	search: '<?php echo CJavaScript::quote(t("Search"))?>', 		    
	no_results: '<?php echo CJavaScript::quote(t("No results"))?>',	
}"	    
>
</component-mobile-search-suggestion>    

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
title:'<?php echo CJavaScript::quote(t("Pick a time"))?>', 
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


 </div>
 <!-- vue-widget-nav-mobile -->