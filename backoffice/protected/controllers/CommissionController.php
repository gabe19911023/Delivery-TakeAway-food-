<?php
class CommissionController extends Commonmerchant
{
		
	public function beforeAction($action)
	{							
		InlineCSTools::registerStatusCSS();	
		return true;
	}
	
	public function actionstatement()
	{
		$this->pageTitle=t("Statement");
		
		try {
		    CWallet::getCardID( Yii::app()->params->account_type['merchant'] , Yii::app()->merchant->merchant_id );
		} catch (Exception $e) {
			$this->redirect(array('/wallet/create_card'));
		    Yii::app()->end();
		}			
		
		$transaction_type = AttributesTools::transactionTypeList();
				
		$options = AdminTools::getPayoutSettings();
		$payout_request_enabled = isset($options['enabled'])?$options['enabled']:false;
		$payout_minimum_amount = isset($options['minimum_amount'])?$options['minimum_amount']:false;
		
		$table_col = array(
		  'transaction_date'=>array(
		    'label'=>t("Date"),
		    'width'=>'15%'
		  ),
		  'transaction_description'=>array(
		    'label'=>t("Transaction"),
		    'width'=>'30%'
		  ),
		  'transaction_amount'=>array(
		    'label'=>t("Debit/Credit"),
		    'width'=>'20%'
		  ),
		  'running_balance'=>array(
		    'label'=>t("Running Balance"),
		    'width'=>'20%'
		  ),
		);
		$columns = array(
		  array('data'=>'transaction_date'),
		  array('data'=>'transaction_description'),
		  array('data'=>'transaction_amount'),
		  array('data'=>'running_balance'),
		);				
				
		$cash_in_link = CMedia::homeUrl()."/merchant/cashin/?uuid=".Yii::app()->merchant->merchant_uuid;		
		
		$this->render('statement',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'transaction_type'=>$transaction_type,
		  'payout_request_enabled'=>$payout_request_enabled,
		  'payout_minimum_amount'=>$payout_minimum_amount,
		  'cash_in_link'=>$cash_in_link,
		  'amount_selection'=>AttributesTools::CashinAmount(),
		  'minimum_cashin'=>AttributesTools::CashinMinimumAmount(),
		));
	}
	
	public function actionwithdrawals()
	{
		
        if(Yii::app()->merchant->merchant_type==1){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("This page is not available in your account.")
			 )
			));
			return ;
		}
		
		$this->pageTitle=t("Withdrawals");
		
		try {
		    CWallet::getCardID( Yii::app()->params->account_type['merchant'] , Yii::app()->merchant->merchant_id );
		} catch (Exception $e) {
			$this->redirect(array('/wallet/create_card'));
		    Yii::app()->end();
		}			

		$table_col = array(
		  'transaction_amount'=>array(
		    'label'=>t("Amount"),
		    'width'=>'15%'
		  ),
		  'transaction_description'=>array(
		    'label'=>t("Transaction"),
		    'width'=>'30%'
		  ),		  
		  'transaction_date'=>array(
		    'label'=>t("Date Processed"),
		    'width'=>'20%'
		  ),
		);
		$columns = array(
		  array('data'=>'transaction_amount'),
		  array('data'=>'transaction_description'),
		  array('data'=>'transaction_date'),		  
		);				
							
		$this->render('withdrawals',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,	
		  'order_col'=>1,
          'sortby'=>'desc',	  
		));
	}
	
	
}
/*end class*/