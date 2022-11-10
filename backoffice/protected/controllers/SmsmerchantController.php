<?php
class SmsmerchantController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
			
		return true;
	}
	
	public function actionIndex()
	{	
		$this->redirect(array(Yii::app()->controller->id.'/sms_settings'));		
	}	
	
	public function actionsms_settings()
	{
		$this->pageTitle=t("Basic Settings");
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
		
		$model=new AR_option;
		$model->scenario=Yii::app()->controller->action->id;		
		$options= array('sms_notify_number','order_verification','order_sms_code_waiting');
		
		if($data = OptionsTools::find($options,$merchant_id)){
			foreach ($data as $name=>$val) {
				$model[$name]=$val;
			}			
		}		
				
		if(isset($_POST['AR_option'])){
			$model->attributes=$_POST['AR_option'];
			if($model->validate()){				
				OptionsTools::$merchant_id = $merchant_id;
				if(OptionsTools::save($options, $model, $merchant_id)){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_settings_saved));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
				
		$this->render("//merchant/sms_settings",array(
		  'model'=>$model		  
		));
	}
	
	public function actionbroadcast()
	{
		$this->pageTitle=t("SMS BroadCast");
		$action_name='smsbroadcast_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/smsbroadcast_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//merchant/smsbroadcast_list';

		$this->render($tpl,array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/smsbroadcast_create")
		));	
	}
	
	public function actionsmsbroadcast_create($update=false)
	{
		$this->pageTitle = $update==false? t("Add SMS") : t("Add SMS");
		CommonUtility::setMenuActive('.sms','.smsmerchant_broadcast');
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_smsbroadcast::model()->findByPk( $id );				
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}												
		} else {
		   $model=new AR_smsbroadcast;		   
		}
		
		if(isset($_POST['AR_smsbroadcast'])){
			$model->attributes=$_POST['AR_smsbroadcast'];
			if($model->validate()){
				$model->merchant_id = $merchant_id;
				
				if($model->save()){
					if(!$update){
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
					   $this->redirect(array(Yii::app()->controller->id.'/broadcast'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		
		$this->render("//merchant/smsbroadcast_create",array(
		  'model'=>$model,
		  'status'=>(array)AttributesTools::StatusManagement('post'),
		  'send_to'=>(array) AttributesTools::SMSBroadcastType(),
		  'links'=>array(
	            t("All Broadcast")=>array(Yii::app()->controller->id.'/broadcast'),        
                $this->pageTitle,
		    ),	 
		));
	}
	
	public function actionsmsbroadcast_delete()
	{
		$id = (integer) Yii::app()->input->get('id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
				
		$model = AR_smsbroadcast::model()->find('merchant_id=:merchant_id AND broadcast_id=:broadcast_id', 
		array(':merchant_id'=>$merchant_id, ':broadcast_id'=>$id ));
		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/broadcast'));			
		} else $this->render("error");
	}
	
	public function actionbroadcast_details()
	{
		$this->pageTitle=t("Broadcast Details");
		CommonUtility::setMenuActive('.sms','.smsmerchant_broadcast');
		
		$action_name='broadcast_details';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/smsbroadcast_delete");
		
		$broadcast_id = (integer) Yii::app()->input->get('broadcast_id');	
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		$model = AR_smsbroadcast::model()->find('merchant_id=:merchant_id AND broadcast_id=:broadcast_id', 
		array(':merchant_id'=>$merchant_id, ':broadcast_id'=>$broadcast_id ));
		
		$this->render("//merchant/smsbroadcast_details",array(
		  'model'=>$model,
		  'links'=>array(
	            t("All Broadcast")=>array(Yii::app()->controller->id.'/broadcast'),        
                $this->pageTitle,
                t("#[broadcast_id]",array('[broadcast_id]'=>$model->broadcast_id))
		    ),	 
		));	
	}
}
/*end class*/