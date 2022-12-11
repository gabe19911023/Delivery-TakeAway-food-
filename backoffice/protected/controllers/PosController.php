<?php
class PosController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		$this->layout = 'backend_merchant_orders';
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		InlineCSTools::registerServicesCSS();		
		return true;
	}
	
	public function actioncreate_order()
	{				
		$this->render("//pos/create_order",array(
		   'ajax_url'=>Yii::app()->createUrl("/apibackend"),
		   'merchant_id'=>Yii::app()->merchant->merchant_id,
		   'view_admin'=>false,
		   'responsive'=>AttributesTools::CategoryResponsiveSettings('half'),
		));
	}
	
	public function actionorders()
	{
				
		$this->layout = 'backend_merchant';
		$this->pageTitle = t("POS Orders");
		
		$table_col = array(
		//   'logo'=>array(
		//     'label'=>'',
		//     'width'=>'8%'
		//   ),
		  'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'8%'
		  ),
		  'client_id'=>array(
		    'label'=>t("Customer"),
		    'width'=>'15%'
		  ),		  
		  'status'=>array(
		    'label'=>t("Order Information"),
		    'width'=>'25%'
		  ),
		  'order_uuid'=>array(
		    'label'=>t("Actions"),
		    'width'=>'10%'
		  ),
		);
		$columns = array(
		//   array('data'=>'logo','orderable'=>false),
		  array('data'=>'order_id'),
		  array('data'=>'client_id','orderable'=>false),
		  array('data'=>'status','orderable'=>false),		  
		  array('data'=>'order_uuid','orderable'=>false),		  
		);				
		
		$this->render("post-order-list",array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,		
		  'order_col'=>1,
          'sortby'=>'desc',  
		));
	}
	
} 
/*end class*/