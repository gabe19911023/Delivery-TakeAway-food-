<?php
class AccountController extends CommonController
{
		
	public function beforeAction($action)
	{				
		return true;
	}
	
	public function actiontransactions()
	{
		$this->pageTitle=t("Statement");
		
		try {
		    CWallet::getCardID( Yii::app()->params->account_type['admin']);
		} catch (Exception $e) {
			$this->redirect(array('/ewallet/create_card'));
		    Yii::app()->end();
		}		
		
		$transaction_type = AttributesTools::transactionTypeList(true);
		
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
				
		$this->render('//finance/transactions',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'transaction_type'=>$transaction_type,		  
		));
	}
	
} 
/*end class*/