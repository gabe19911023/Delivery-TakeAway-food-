<?php
class PromoController extends CommonController
{
	public $layout='backend';
		
	public function beforeAction($action)
	{	
		InlineCSTools::registerStatusCSS();									
		return true;
	}	
	
	public function actioncoupon()
	{
		$this->pageTitle=t("Coupon list");
		$action_name='coupon_list';
		$delete_link = Yii::app()->CreateUrl("promo/coupon_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		$tpl = 'coupon_list';
		
		$this->render( $tpl ,array(
			'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/coupon_create")
		));
	}
	
	public function actioncoupon_delete()
	{
		$id = (integer) Yii::app()->input->get('id');					
		$model = AR_voucher::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('promo/coupon'));			
		} else $this->render("error");
	}
	
	public function actioncoupon_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add Coupon") : t("Update Coupon");
		CommonUtility::setMenuActive('.promo',".promo_coupon");
			
		$id='';	$days = AttributesTools::dayList();
		$selected_days = array(); $selected_merchant = array();
		$selected_customer = array();
				
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_voucher::model()->findByPk( $id );						
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
			
			foreach ($days as $day) {
				if($model[$day]==1){
					$selected_days[]=$day;
				}
			}	
			
			$model->days_available = $selected_days;	
			$selected_merchant = !empty($model->joining_merchant) ? json_decode(stripslashes($model->joining_merchant)): '';			
			$model->apply_to_merchant = $selected_merchant; 
			$selected_merchant = MerchantAR::getSelected($selected_merchant);			
			
			$selected_customer = !empty($model->selected_customer) ? json_decode(stripslashes($model->selected_customer)): '';			
			$model->apply_to_customer = $selected_customer; 
			$selected_customer = CustomerAR::getSelected($selected_customer);			
			
		} else {			
			$model=new AR_voucher;							
		}			

		if(isset($_POST['AR_voucher'])){
			$model->attributes=$_POST['AR_voucher'];
			if($model->validate()){										
				foreach ($days as $day) {
					if(in_array($day,$model->days_available)){
						$model[$day]=1;
					} else $model[$day]=0;
				}				
				
				$model->voucher_owner = 'admin';
				$model->joining_merchant = !empty($model->apply_to_merchant) ? json_encode($model->apply_to_merchant): '';
				$model->selected_customer = !empty($model->apply_to_customer) ? json_encode($model->apply_to_customer): '';

				$model->amount = (float) $model->amount;			
				$model->min_order = (float) $model->min_order;
				$model->max_number_use = (integer) $model->max_number_use;
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array('promo/coupon'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else {
				//dump($model);die();
			}
		}
		
		$model->amount = Price_Formatter::convertToRaw($model->amount,2,true);
		$model->min_order = Price_Formatter::convertToRaw($model->min_order,2,true);
		$model->max_number_use = Price_Formatter::convertToRaw($model->max_number_use,0);
						
		$this->render("coupon_create",array(
		    'model'=>$model,
		    'voucher_type'=>array(),		    
		    'coupon_options'=>array(),
		    'status'=>(array)AttributesTools::StatusManagement('post'),
		    'voucher_type'=>AttributesTools::couponType(),
		    'coupon_options'=>AttributesTools::couponOoptions(),
		    'days'=>$days,
		    'selected_merchant'=>$selected_merchant,
		    'selected_customer'=>$selected_customer
		));
	}
	
	public function actioncoupon_update()
	{
	    $this->actioncoupon_create(true);
	}
	
}
/*end class*/