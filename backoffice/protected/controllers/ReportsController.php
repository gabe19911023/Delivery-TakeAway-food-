<?php
class ReportsController extends CommonController
{
	public $layout='backend';
		
	public function beforeAction($action)
	{						
		/*InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
					
		$ajaxurl = Yii::app()->createUrl("/backendreports");
		ScriptUtility::registerScript(array(
		  "var ajaxurl='$ajaxurl';"
		),'admin_script');
		
		$is_mobile = Yii::app()->params['isMobile'];
		if($is_mobile){
			$ajaxurl = Yii::app()->createUrl("/backendapp");
				ScriptUtility::registerScript(array(
			    "var ajaxurl='$ajaxurl';",
			    "var is_mobile='$is_mobile';"
			),'admin_script');
		}*/
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		InlineCSTools::registerServicesCSS();					
			
		return true;
	}
		
	public function actionmerchant_registration()
	{		
		$this->pageTitle=t("Merchant Registration");		
		
		$table_col = array(
		  'logo'=>array(
		    'label'=>t(""),
		    'width'=>'8%'
		  ),
		  'restaurant_name'=>array(
		    'label'=>t("Name"),
		    'width'=>'25%'
		  ),
		  'address'=>array(
		    'label'=>t("Address"),
		    'width'=>'20%'
		  ),
		  'merchant_type'=>array(
		    'label'=>t("Membership Program"),
		    'width'=>'20%'
		  ),
		);
		$columns = array(
		  array('data'=>'logo','orderable'=>false),
		  array('data'=>'restaurant_name'),
		  array('data'=>'address'),
		  array('data'=>'merchant_type','orderable'=>false),
		);				
		
		$this->render('//reports/merchant_registration',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'asc',
		  'transaction_type'=>AttributesTools::StatusManagement('customer' , Yii::app()->language ),
		));
	}		
	
	public function actionmerchant_payment()
	{		
		$this->pageTitle=t("Membership Payment");		
			
		$table_col = array(		  
		 'logo'=>array(
		    'label'=>"",
		    'width'=>'8%'
		  ), 
		 'created'=>array(
		    'label'=>t("Created"),
		    'width'=>'12%'
		  ),
		 'merchant_id'=>array(
		    'label'=>t("Merchant"),
		    'width'=>'18%'
		  ),
		  'payment_code'=>array(
		    'label'=>t("Payment type"),
		    'width'=>'12%'
		  ),
		  'invoice_ref_number'=>array(
		    'label'=>t("Invoice #"),
		    'width'=>'18%'
		  ),		  
		  'package_id'=>array(
		    'label'=>t("Plan"),
		    'width'=>'15%'
		  ),		  
		);
		$columns = array(		  
		  array('data'=>'logo','orderable'=>false),
		  array('data'=>'created'),
		  array('data'=>'merchant_id'),
		  array('data'=>'payment_code'),
		  array('data'=>'invoice_ref_number'),		  
		  array('data'=>'package_id','orderable'=>false),		  
		);			
		
		$this->render('//reports/merchant_payment',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'transaction_type'=>AttributesTools::StatusManagement('payment' , Yii::app()->language ),
		));		
	}		
	
	public function actionmerchant_sales()
	{		
		$this->pageTitle=t("Merchant Sales Report");
		
		$table_col = array(
		  'merchant_id'=>array(
		    'label'=>t(""),
		    'width'=>'8%'
		  ),		  
		  'status'=>array(
		    'label'=>t("Order Information"),
		    'width'=>'20%'
		  ),
		  'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'10%'
		  ),
		  'restaurant_name'=>array(
		    'label'=>t("Merchant"),
		    'width'=>'15%'
		  ),
		  'client_id'=>array(
		    'label'=>t("Customer"),
		    'width'=>'15%'
		  ),		  		  
		  'order_uuid'=>array(
		    'label'=>t("Actions"),
		    'width'=>'10%'
		  ),
		  'view'=>array(
		    'label'=>t("view"),
		    'width'=>'10%'
		  ),
		);
		$columns = array(
		  array('data'=>'merchant_id','orderable'=>false),
		  array('data'=>'status','orderable'=>false),		  
		  array('data'=>'order_id'),
		  array('data'=>'restaurant_name','orderable'=>false),
		  array('data'=>'client_id','orderable'=>false),		  
		  array('data'=>null,'orderable'=>false,'visible'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_view_order normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>
			    <a class="ref_pdf_order normal btn btn-light tool_tips"><i class="zmdi zmdi-download"></i></a>
			 </div>
		     '
		  ),
		  array('data'=>null,'orderable'=>false,'visible'=>false),
		);	

		$this->render('//reports/merchant_sales_report',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',		  
		));		
		
	}		
		
	public function actionorder_earnings()
	{
		$this->pageTitle=t("Order earnings report");		
			
		$table_col = array(		  
		 'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'8%'
		  ), 
		  'sub_total'=>array(
		    'label'=>t("Subtotal"),
		    'width'=>'15%'
		  ),
		 'total'=>array(
		    'label'=>t("Total"),
		    'width'=>'15%'
		  ),
		 'merchant_earning'=>array(
		    'label'=>t("Merchant Earnings"),
		    'width'=>'15%'
		  ),
		  'commission'=>array(
		    'label'=>t("Admin commission"),
		    'width'=>'15%'
		  ),		  		  
		);
		$columns = array(		  		  
		  array('data'=>'order_id'),
		  array('data'=>'sub_total','className'=>"text-right"),
		  array('data'=>'total','className'=>"text-right"),
		  array('data'=>'merchant_earning','className'=>"text-right"),
		  array('data'=>'commission','className'=>"text-right"),
		);			
		
		$this->render('//reports/order_earnings',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',		  
		));		
	}
	
	public function actionrefund()
	{
		
		$this->pageTitle=t("Refund Report");
		
		$table_col = array(		  
		  'date_created'=>array(
		    'label'=>t(""),
		    'width'=>'1%'
		  ),		  
		  'merchant_id'=>array(
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
		  array('data'=>'merchant_id','orderable'=>false),
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
?>