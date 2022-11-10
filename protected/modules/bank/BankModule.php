<?php
class BankModule extends CWebModule
{	
	public function init()
	{
		$this->setImport(array(			
			'cod.components.*',
			'cod.models.*'
		));
	}
		
	public function beforeControllerAction($controller, $action)
	{									
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here									
			return true;
		}
		else
			return false;
	}
	
	public function paymentInstructions()
	{
		return array(
		  'method'=>"offline",
		  'redirect'=>''
		);
	}
	
	public function savedTransaction($data)
	{				
		$order = AR_ordernew::model()->find('order_id=:order_id', 
		array(':order_id'=>$data->order_id)); 
		if($order){
			$order->scenario = "new_order";
			$order->status = COrders::newOrderStatus();
			$order->cart_uuid = $data->cart_uuid;
	    	$order->save();	
		}
		        
		$payment_ref = CommonUtility::generateToken("{{ordernew_transaction}}",'payment_reference',
		CommonUtility::generateAplhaCode(10) );
		
		$model = new AR_ordernew_transaction;
		$model->order_id = $data->order_id;
		$model->merchant_id = $data->merchant_id;
		$model->client_id = $data->client_id;
		$model->payment_code = $data->payment_code;
		$model->trans_amount = $data->total;
		$model->currency_code = Price_Formatter::$number_format['currency_code'];
		$model->payment_reference = $payment_ref;
		$model->save();		
	}

	public function delete($data)
	{		
		AR_payment_method_meta::model()->deleteAll("payment_method_id=:payment_method_id",array(
		  ':payment_method_id'=>$data->payment_method_id
		));		
	}
	
}
/*end class*/