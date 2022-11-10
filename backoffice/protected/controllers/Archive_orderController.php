<?php
class Archive_orderController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
		InlineCSTools::registerServicesCSS();
			
		return true;
	}
	
	public function actionList()
	{
		$this->pageTitle=t("Archive Order List");
		
		$table_col = array(	
		  'avatar'=>array(
		    'label'=>t(""),
		    'width'=>'8%'
		  ),	  
		  'order_id'=>array(
		    'label'=>t("Order ID"),
		    'width'=>'8%'
		  ),
		  'client_id'=>array(
		    'label'=>t("Customer"),
		    'width'=>'15%'
		  ),
		  'json_details'=>array(
		    'label'=>t("Order Information"),
		    'width'=>'20%'
		  ),		  		  		  
		  /*'date_created'=>array(
		    'label'=>t(""),
		    'width'=>'10%'
		  ),*/		  		  		  
		);
		$columns = array(		  
		  array('data'=>'avatar','orderable'=>false),
		  array('data'=>'order_id'),
		  array('data'=>'client_id'),
		  array('data'=>'json_details'),	
		  /*array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_edit normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>			    
			 </div>
		     '
		  ),	 	  */
		);			
		
		$this->render('order_list',array(
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
          'sortby'=>'desc',
		  'transaction_type'=>array(),			  
		));
	}
	
}
/*end class*/