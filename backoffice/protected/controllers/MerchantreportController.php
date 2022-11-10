<?php
class MerchantreportController extends Commonmerchant
{		
	public function beforeAction($action)
	{				
					
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		InlineCSTools::registerServicesCSS();
			
		return true;
	}

	public function actionsales()
	{  
	    $this->pageTitle=t("Sales Report");
		$action_name='sales_report';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/sales_delete");
				
		
		$table_col = array(		  
		  'client_id'=>array(
		    'label'=>t(""),
		    'width'=>'7%'
		  ),
		  'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'5%'
		  ),
		  'order_uuid'=>array(
		    'label'=>t("Items"),
		    'width'=>'20%'
		  ),
		  'service_code'=>array(
		    'label'=>t("Order type"),
		    'width'=>'10%'
		  ),
		  'payment_code'=>array(
		    'label'=>t("Payment type"),
		    'width'=>'10%'
		  ),
		  'total'=>array(
		    'label'=>t("Total"),
		    'width'=>'10%'
		  ),
		);
		$columns = array(		  
		  array('data'=>'client_id','orderable'=>false),
		  array('data'=>'order_id'),
		  array('data'=>'order_uuid','orderable'=>false),
		  array('data'=>'service_code'),
		  array('data'=>'payment_code'),
		  array('data'=>'total'),
		);				
		
		$transaction_type = array();
		
		$this->render('sales_report',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'transaction_type'=>$transaction_type,		  
		));
	}
	
	public function actionsummary()
	{  
	    $this->pageTitle=t("Sales Summary Report");
		
		$table_col = array(		  
		  'photo'=>array(
		    'label'=>t(""),
		    'width'=>'8%'
		  ),
		  'item_name'=>array(
		    'label'=>t("Items"),
		    'width'=>'15%'
		  ),
		  'price'=>array(
		    'label'=>t("Average price"),
		    'width'=>'10%'
		  ),
		  'qty'=>array(
		    'label'=>t("Total qty sold"),
		    'width'=>'10%'
		  ),		  
		  'total'=>array(
		    'label'=>t("Total"),
		    'width'=>'10%'
		  ),		  
		);
		$columns = array(		  
		  array('data'=>'photo'),
		  array('data'=>'item_name'),
		  array('data'=>'price','className'=>'dt-body-right'),
		  array('data'=>'qty','className'=>'dt-body-right'),
		  array('data'=>'total','className'=>'dt-body-right'),
		);			
		
		$transaction_type = CommonUtility::getDataToDropDown("{{item_translation}}",'item_id','item_name',
    	"
    	where language=".q(Yii::app()->language)." 
    	and item_id IN (
    	 select item_id from {{item}}
    	 where merchant_id=".q(Yii::app()->merchant->merchant_id)."
    	 and item_name IS NOT NULL AND TRIM(item_name) <> ''
    	)
    	"
    	);    	
						
		$this->render('summary_report',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'transaction_type'=>$transaction_type,		  
		));
	}
	
	public function actionsummary_chart()
	{
		$this->pageTitle=t("Sales Summary Chart");
		CommonUtility::setMenuActive('.reports','.merchantreport_summary');		
		
		$this->render('summary_chart');
	}

	public function actionrefund()
	{
		
		$this->pageTitle=t("Refund Report");
		
		$table_col = array(		  
		  'date_created'=>array(
		    'label'=>t(""),
		    'width'=>'1%'
		  ),		  
		  'client_id'=>array(
		    'label'=>t(""),
		    'width'=>'8%'
		  ),		  
		  'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'8%'
		  ),		  
		  'transaction_description'=>array(
		    'label'=>t("Description"),
		    'width'=>'20%'
		  ),
		  'payment_code'=>array(
		    'label'=>t("Payment type"),
		    'width'=>'15%'
		  ),
		  'trans_amount'=>array(
		    'label'=>t("Amount"),
		    'width'=>'12%'
		  ),		  		  
		);
		$columns = array(		  
		  array('data'=>'date_created','visible'=>false),
		  array('data'=>'client_id','orderable'=>false),
		  array('data'=>'order_id'),
		  array('data'=>'transaction_description'),
		  array('data'=>'payment_code'),
		  array('data'=>'trans_amount'),		  
		);			
		
		$payment_status = AttributesTools::StatusManagement('payment',Yii::app()->language);		
		
		$this->render('refund_report',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>0,
          'sortby'=>'desc',		  
          'transaction_type'=>$payment_status
		));
	}
	
} 
/*end class*/