<components-ocr
ref="<?php echo $payment_code;?>"
title="<?php echo t("Add New Card")?>"	 	  
payment_code="<?php echo $payment_code;?>"
merchant_id="<?php echo isset($credentials['merchant_id'])?$credentials['merchant_id']:0;?>"
@set-paymentlist="SavedPaymentList"
:label="{
card_name: '<?php echo t("Card Name")?>',
credit_card_number: '<?php echo t("Card Number")?>',
expiry_date: '<?php echo t("Exp. Date")?>',
cvv: '<?php echo t("Security Code")?>',
billing_address: '<?php echo t("Billing Address")?>',
submit: '<?php echo t("Add Card")?>',
}"
>
</components-ocr>