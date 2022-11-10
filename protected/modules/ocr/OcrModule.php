<?php
class OcrModule extends CWebModule
{	
	public function init()
	{
		$this->setImport(array(			
			'ocr.components.*',
			'ocr.models.*'
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
				
		if($payments = CPayments::defaultPayment( $model->client_id )){
		   $cc_id = isset($payments['reference_id'])?intval($payments['reference_id']):0;		  
		   $card = AR_client_cc::model()->find('cc_id=:cc_id', 
		   array(':cc_id'=>$cc_id)); 		
		   
		   if($card){
			   $card_model=new AR_ordernew_trans_meta;
			   $card_model->transaction_id = $model->transaction_id;		   			
			   $card_model->order_id = $model->order_id;
			   $card_model->meta_name = 'card_number';
			   $card_model->meta_value = 'card_number';
			   $card_model->meta_binary = $card->encrypted_card;
			   $card_model->save();
			   
			   $card_params = array();
			   $card_params[]=array(
			     'transaction_id'=>$model->transaction_id,
			     'order_id'=>$model->order_id,
			     'meta_name'=>'card_name',
			     'meta_value'=>$card->card_name
			   );		   
			   $card_params[]=array(
			     'transaction_id'=>$model->transaction_id,
			     'order_id'=>$model->order_id,
			     'meta_name'=>'expiration_month',
			     'meta_value'=>$card->expiration_month
			   );		   
			   $card_params[]=array(
			     'transaction_id'=>$model->transaction_id,
			     'order_id'=>$model->order_id,
			     'meta_name'=>'expiration_yr',
			     'meta_value'=>$card->expiration_yr
			   );		   
			   $card_params[]=array(
			     'transaction_id'=>$model->transaction_id,
			     'order_id'=>$model->order_id,
			     'meta_name'=>'cvv',
			     'meta_value'=>$card->cvv
			   );		   		   
			   $builder=Yii::app()->db->schema->commandBuilder;
			   $command=$builder->createMultipleInsertCommand('{{ordernew_trans_meta}}',$card_params);
			   $command->execute();
		   }
		}
	}
}
/*end class*/