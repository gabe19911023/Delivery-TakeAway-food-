

<DIV id="vue-cashin" class="container mt-3 mb-5" >

<div class="w-50 m-auto">
    <h4 class="mb-4 mt-4"><?php echo t("Cash In")?></h4>
  
    <div class="p-2 border rounded mb-3 text-center">
      <p class="m-0"><?php echo t("Cash In Amount")?></p>
      <h4 class="m-0"><?php echo Price_Formatter::formatNumber($amount)?></h4>       
    </div>
    
    <components-merchant-saved-payment
	ref="merchant_saved_payment"
	ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 	
	merchant_uuid="<?php echo $merchant_uuid?>"
	@set-defaultpayment="setDefaultpayment"		
	>
	</components-merchant-saved-payment>

           
	<components-payment-list
	ref="payment_list"
	ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 
	payment_type="cashin"
	@after-clickpayment="afterClickpayment"	
	>
	</components-payment-list>
	
	<?php CComponentsManager::renderComponents($payments,$payments_credentials,$this,'cashin')?>  
	
			
	<components-cashin-payment
	ref="cashin_payment"
	amount="<?php echo floatval($amount)?>"		
	merchant_uuid="<?php echo $merchant_uuid?>"
	:default_payment="default_payment"
	ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 	
	:label="{	    
	    continue: '<?php echo CJavaScript::quote(t("Continue"))?>', 	    
	    confirm: '<?php echo CJavaScript::quote(t("Confirm cash in"))?>', 	    
	    are_you_sure: '<?php echo CJavaScript::quote(t("Cash in amount {{amount}}, click yes to continue.",array('{{amount}}'=>Price_Formatter::formatNumber($amount))))?>', 
	    cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>', 
	    yes: '<?php echo CJavaScript::quote(t("Yes"))?>', 
	 }"
	>
	</components-cashin-payment>
	
	
	<div class="col w-50 mt-4 d-flex justify-content-center align-items-center" >
      <a href="<?php echo $back_link;?>" class="back-arrow text-green">	  
	  <?php echo t("Back to dashboard")?>
	  </a>
    </div>
	
	
</div>   <!--center-->

</DIV>
<!--vue-cashin-->


<?php $this->renderPartial("//components/loading-box")?>
<?php $this->renderPartial("//components/vue-bootbox")?>