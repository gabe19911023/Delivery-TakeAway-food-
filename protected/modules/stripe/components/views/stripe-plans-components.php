<components-plans-stripe
ref="<?php echo $payment_code?>"
title="<?php echo t("Enter your card details")?>"	 	  
payment_code="<?php echo $payment_code?>"
publish_key="<?php echo isset($credentials['attr2'])?$credentials['attr2']:''; ?>"
ajax_url = "<?php echo Yii::app()->createAbsoluteUrl("$payment_code/plans")?>"
return_url = "<?php echo Yii::app()->createAbsoluteUrl("$payment_code/plans/validate")?>"
trial_url = "<?php echo Yii::app()->createAbsoluteUrl("$payment_code/plans/trial_validate")?>"
:merchant_uuid="merchant_uuid"
:package_uuid="package_uuid"

:label="{		      
  cardholder_name: '<?php echo CJavaScript::quote(t("Cardholder name"))?>',
  submit: '<?php echo CJavaScript::quote(t("Subscribe"))?>',
}"  
/>
</components-plans-stripe>