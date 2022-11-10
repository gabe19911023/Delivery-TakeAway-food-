<components-manage-stripe
ref="<?php echo $payment_code?>"
payment_code="<?php echo $payment_code?>"
publish_key="<?php echo isset($credentials['attr2'])?$credentials['attr2']:''; ?>"
ajax_url = "<?php echo Yii::app()->createAbsoluteUrl("$payment_code/manage")?>"
api_url="<?php echo Yii::app()->createUrl("/apibackend")?>" 
return_url="<?php echo Yii::app()->createAbsoluteUrl("$payment_code/manage/validate_card")?>" 
@show-plan="showPlan"
@notify="notify"
@show-loading="showLoading"
@close-loading="closeLoading"
@after-changeplan="afterChangeplan"
@after-cancelplan="afterCancelplan"
@refresh-datatables="refreshDatatables"

:label="{		        
  manage_plan: '<?php echo CJavaScript::quote(t("Manage plan"))?>',
  current_plan: '<?php echo CJavaScript::quote(t("Current Plan"))?>',
  credit_card: '<?php echo CJavaScript::quote(t("Credit Card"))?>',
  change_plan: '<?php echo CJavaScript::quote(t("Change plan"))?>',
  cancel_subscriptions: '<?php echo CJavaScript::quote(t("Cancel subscriptions"))?>',
  invoice: '<?php echo CJavaScript::quote(t("Invoice"))?>',
  subscribe: '<?php echo CJavaScript::quote(t("Subscribe"))?>',
  cancel: '<?php echo CJavaScript::quote(t("Cancel"))?>',
  confirm: '<?php echo CJavaScript::quote(t("Confirm"))?>',
  confirm_cancel: '<?php echo CJavaScript::quote(t("Confirm subscription cancellation"))?>',
  confirm_cancel_sub: '<?php echo CJavaScript::quote(t("Are you sure? if you cancel your subscription you will not receive order."))?>',
}"  
/>
</components-manage-stripe>