<components-stripe
ref="<?php echo $payment_code?>"
title="<?php echo t("Add Stripe")?>"	 	  
payment_code="<?php echo $payment_code?>"
merchant_id="<?php echo isset($credentials['merchant_id'])?$credentials['merchant_id']:0;?>"
merchant_type="<?php echo isset($credentials['merchant_type'])?$credentials['merchant_type']:2;?>"
prefix="<?php echo $prefix?>"
reference="<?php echo $reference?>"

publish_key="<?php echo isset($credentials['attr2'])?$credentials['attr2']:''; ?>"
:amount="amount_to_pay"
:cart_uuid="cart_uuid"
currency_code="<?php echo Price_Formatter::$number_format['currency_code'];?>"
ajax_url = "<?php echo Yii::app()->createAbsoluteUrl("$payment_code/cashin")?>"
cardholder_name = "<?php echo "";?>"

@set-paymentlist="SavedPaymentList"	 	
@after-cancel-payment="AfterCancelPayment"	
@alert="Alert"	
@show-loader="showLoadingBox"	
@close-loader="closeLoadingBox"

:label="{		    
submit: '<?php echo CJavaScript::quote(t("Add Stripe"))?>',
notes : '<?php echo CJavaScript::quote(t("Add your stripe account"))?>',
payment_title : '<?php echo CJavaScript::quote(t("Pay using Stripe"))?>',
payment_notes : '<?php echo CJavaScript::quote(t("You will re-direct to Stripe account to login to your account."))?>',
cardholder_name: '<?php echo CJavaScript::quote(t("Cardholder name"))?>',
agreement: '<?php echo CJavaScript::quote(t("I authorise [website_name] to send instructions to the financial institution that issued my card to take payments from my card account in accordance with the terms of my agreement with [website_name]",array(
  '[website_name]'=>Yii::app()->params['settings']['website_title']
)))?>',
}"  
:on_error="{		    
error: '<?php echo CJavaScript::quote(t("An error has occured"))?>',
}"  
>
</components-stripe>