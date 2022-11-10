<components-razorpay
ref="<?php echo $payment_code?>"
title="<?php echo t("Add Razorpay")?>"	 	  
payment_code="<?php echo $payment_code?>"
merchant_id="<?php echo isset($credentials['merchant_id'])?$credentials['merchant_id']:0;?>"
merchant_type="<?php echo isset($credentials['merchant_type'])?$credentials['merchant_type']:2;?>"
key_id="<?php echo isset($credentials['attr1'])?$credentials['attr1']:''; ?>"
:amount="amount_to_pay"
:cart_uuid="cart_uuid"
currency_code="<?php echo Price_Formatter::$number_format['currency_code'];?>"
ajax_url = "<?php echo Yii::app()->createAbsoluteUrl("$payment_code/RazorpayAPI")?>"

@set-paymentlist="SavedPaymentList"	 	
@after-cancel-payment="AfterCancelPayment"	
@alert="Alert"	
@show-loader="showLoadingBox"	
@close-loader="closeLoadingBox"

:label="{		    
submit: '<?php echo CJavaScript::quote(t("Add Razorpay"))?>',
notes : '<?php echo CJavaScript::quote(t("Pay using your Razorpay account"))?>',
payment_title : '<?php echo CJavaScript::quote(t("Pay using Razorpay"))?>',
payment_notes : '<?php echo CJavaScript::quote(t("You will re-direct to Razorpay account to login to your account."))?>',
pay_with_razor : '<?php echo CJavaScript::quote(t("Pay with Razorpay"))?>',
creating_account : '<?php echo CJavaScript::quote(t("Creating account"))?>',
getting_payment : '<?php echo CJavaScript::quote(t("Getting payment information...."))?>',
processing_payment : '<?php echo CJavaScript::quote(t("Processing payment.."))?>',
}"  
:on_error="{		    
error: '<?php echo CJavaScript::quote(t("An error has occured"))?>',
}"  
>
</components-razorpay>