<?php
class CMercadopagoError
{
	public static function get($status_detail='')
	{
		$error = array();
		$error['cc_rejected_bad_filled_card_number'] = t("Check card number.");
		$error['cc_rejected_bad_filled_date'] = t("Check expiration date.");
		$error['cc_rejected_bad_filled_other'] = t("Check data.");
		$error['cc_rejected_bad_filled_security_code'] = t("Check card security code.");
		$error['cc_rejected_blacklist'] = t("Your payment couldn't be processed.");
		$error['cc_rejected_call_for_authorize'] = t("Authorize the amount payment to payment_method_id.");
		$error['cc_rejected_card_disabled'] = t("Call payment_method_id to activate your card, or use a different payment method. The phone is on the back of your card.
");
		$error['cc_rejected_card_error'] = t("Your payment couldn't be processed.");
		$error['cc_rejected_duplicated_payment'] = t("You have already made a payment for that value");
		$error['cc_rejected_high_risk'] = t("Your payment was rejected.");
		$error['cc_rejected_invalid_installments'] = t("payment_method_id does not process payments in installments installments.");
		$error['cc_rejected_max_attempts'] = t("You reached the allowed attempt limit.");
		$error['cc_rejected_other_reason'] = t("payment_method_id did not process payment.");
		
		if(array_key_exists($status_detail,$error)){
			return $error[$status_detail];
		}
		return t("An error has occured. please try again");
	}
}
/*end class*/