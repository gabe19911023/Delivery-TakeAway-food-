<components-bank
ref="<?php echo $payment_code;?>"
title="<?php echo t("Choose Bank Transfer")?>"	 	  
payment_code="<?php echo $payment_code;?>"
merchant_id="<?php echo isset($credentials['merchant_id'])?$credentials['merchant_id']:0;?>"
@set-paymentlist="SavedPaymentList"	 	
:label="{		    
submit: '<?php echo t("Submit")?>',
notes : '<?php echo t("Pay using bank Transfer.")?>'
}"  
>
</components-bank>