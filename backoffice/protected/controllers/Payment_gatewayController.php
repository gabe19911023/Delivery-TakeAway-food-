<?php
class Payment_gatewayController extends CommonController
{
	
	public function beforeAction($action)
	{										
		InlineCSTools::registerStatusCSS();
		return true;
	}
		
	public function actionlist()
	{		
		/*$data = array(
		  'attr1'=>array(
		    'label'=>"Secret key",
		  ),
		  'attr2'=>array(
		    'label'=>"Publishable Key",
		  ),
		  'attr3'=>array(
		    'label'=>"Webhooks Signing secret",
		  )
		);		
		echo json_encode($data);
		die();*/
		
		$this->pageTitle=t("Payment gateway list");
		$action_name='payment_gateway_list';
		$delete_link = Yii::app()->CreateUrl("payment_gateway/delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
				
		$this->render('list',array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/create")
		));	
	}
	
	public function actionupdate()
	{
		$this->actioncreate(true);
	}
	
	public function actioncreate($update=false)
	{
		$this->pageTitle = $update==false? t("Add Gateway") :  t("Update Gateway");
		CommonUtility::setMenuActive('.payment_gateway',".payment_gateway_list");
		
		$multi_language = CommonUtility::MultiLanguage();
		$attr_json = ''; $instructions = array();
		$upload_path = CMedia::adminFolder();
		
		if($update){
			$id = (integer) Yii::app()->input->get('id');	
			$model = AR_payment_gateway::model()->findByPk( $id );				
			$model->scenario = "update";			
			$attr_json = !empty($model->attr_json)?json_decode($model->attr_json,true):'';		
			$instructions=!empty($model->attr4)?json_decode($model->attr4,true):'';						
			if(!$model){				
				$this->render("error");				
				Yii::app()->end();
			}	
		} else {
			$model=new AR_payment_gateway;	
			$model->scenario = "create";
		}
				
		if(isset($_POST['AR_payment_gateway'])){
			$model->attributes=$_POST['AR_payment_gateway'];
			if($model->validate()){				
								
				if(isset($_POST['photo'])){
					if(!empty($_POST['photo'])){
						$model->logo_image = $_POST['photo'];
						$model->path = isset($_POST['path'])?$_POST['path']:$upload_path;
					} else $model->logo_image = '';
				} else $model->logo_image = '';
				
				if($model->save()){
					if(!$update){
					   $this->redirect(array('payment_gateway/list'));		
					} else {
						Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
						$this->refresh();
					}
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			}
		}	
				
		$this->render("create",array(
		    'model'=>$model,		   		    		    
		    'attr_json'=>$attr_json,
		    'upload_path'=>$upload_path,
		    'status'=>(array)AttributesTools::StatusManagement('gateway'),
		    'instructions'=>$instructions,
		    'protocol'=> isset($_SERVER["HTTPS"]) ? 'https' : 'http',
		));
	}	
	
	public function actionDelete()
	{
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_payment_gateway::model()->findByPk( $id );
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array('payment_gateway/list'));			
		} else $this->render("error");
	}
	
}
/*end class*/