<?php
class EarningsController extends CommonController
{
		
	public function beforeAction($action)
	{				
		return true;
	}
	
	public function actionmerchant()
	{
		$this->pageTitle=t("Merchant Earnings");
		$transaction_type = AttributesTools::transactionTypeList(false);
		$transaction_type2 = AttributesTools::transactionTypeList(true);
				
		$table_col = array(
		  'merchant_id'=>array(
		    'label'=>t("Merchant ID"),
		    'width'=>'12%'
		  ),
		  'logo'=>array(
		    'label'=>t(""),
		    'width'=>'10%'
		  ),
		  'restaurant_name'=>array(
		    'label'=>t("Merchant"),
		    'width'=>'30%'
		  ),
		  'balance'=>array(
		    'label'=>t("Balance"),
		    'width'=>'20%'
		  ),		  
		  'merchant_uuid'=>array(
		    'label'=>t("Actions"),
		    'width'=>'20%'
		  ),		  
		);
		$columns = array(
		  array('data'=>'merchant_id','visible'=>false),
		  array('data'=>'logo','orderable'=>false),
		  array('data'=>'restaurant_name'),
		  array('data'=>'balance'),		  
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_view_transaction normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>				
			</div>
		     '
		  ),
		);				
		
		//<a class="btn btn-light tool_tips"><i class="zmdi zmdi-format-valign-bottom"></i></a>
		
		/*-----------------------------------------------*/
		$table_col_trans = array(
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
		$columns_trans = array(
		  array('data'=>'transaction_date'),
		  array('data'=>'transaction_description'),
		  array('data'=>'transaction_amount'),
		  array('data'=>'running_balance'),
		);				
		/*-----------------------------------------------*/
				
		$this->render('//finance/merchant_earnings',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'table_col_trans'=>$table_col_trans,
		  'columns_trans'=>$columns_trans,
		  'transaction_type'=>$transaction_type,
		  'transaction_type2'=>$transaction_type2,
		));
	}
	
}
/*end class*/