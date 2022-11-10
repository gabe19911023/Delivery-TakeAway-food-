<?php
class PlanController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
					
		return true;
	}
	
	public function actionmanage()
	{
		$payments = array(); $payments_credentials = array();
		try {					
			$payments = AttributesTools::PaymentPlansProvider();			
			$payments_credentials = CPayments::getPaymentCredentials(0,'',0);			
			AComponentsManager::RegisterBundle($payments ,'manage-');
		} catch (Exception $e) {
		    //
		}	
				
		$terms = isset(Yii::app()->params['settings']['registration_terms_condition_payment'])?Yii::app()->params['settings']['registration_terms_condition_payment']:'';
		$site = CNotifications::getSiteData();	
		$site = CommonUtility::arrayToMustache($site);		
		$terms = t($terms,$site);
				
		$table_col = array(		  
		  'payment_code'=>array(
		    'label'=>t("Payment code"),
		    'width'=>'1%'
		  ),
		  'invoice_ref_number'=>array(
		    'label'=>t("Invoice #"),
		    'width'=>'20%'
		  ),
		  'created'=>array(
		    'label'=>t("Created"),
		    'width'=>'15%'
		  ),
		  'package_id'=>array(
		    'label'=>t("Plan"),
		    'width'=>'15%'
		  ),
		  'amount'=>array(
		    'label'=>t("Amount"),
		    'width'=>'15%'
		  ),
		  'status'=>array(
		    'label'=>t("Status"),
		    'width'=>'15%'
		  ),
		  'invoice_number'=>array(
		    'label'=>t("Actions"),
		    'width'=>'15%'
		  ),
		);
		$columns = array(		  
		  array('data'=>'payment_code','visible'=>false),
		  array('data'=>'invoice_ref_number'),
		  array('data'=>'created'),
		  array('data'=>'package_id'),
		  array('data'=>'amount'),
		  array('data'=>'status'),
		  array('data'=>null,'orderable'=>false,
		     'defaultContent'=>'
		     <div class="btn-group btn-group-actions" role="group">
			    <a class="ref_invoice normal btn btn-light tool_tips"><i class="zmdi zmdi-eye"></i></a>			    
			 </div>
		     '
		  ),
		);				
		
		$this->render("plan-details",array(
		  'payments'=>$payments,
		  'payments_credentials'=>$payments_credentials,
		  'table_col'=>$table_col,
		  'columns'=>$columns,
		  'order_col'=>1,
		  'terms'=>$terms
		));
	}
	
	public function actionerror()
	{
		$message = Yii::app()->input->get('message');
		$this->render("//tpl/error",array(
		 'error'=>array( 
		   'message'=>$message
		 )
		));
	}
	
}
/*end class*/