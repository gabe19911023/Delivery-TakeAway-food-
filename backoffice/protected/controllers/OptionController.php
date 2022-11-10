<?php
class OptionController extends CommonController
{
	public $layout='backend';
		
	public function beforeAction($action)
	{										
		return true;
	}
		
	public function actiondelete()
	{
		$id = Yii::app()->input->get('name');			
		$page = Yii::app()->input->get('page');				
		$model = AR_option::model()->find('option_name=:option_name AND merchant_id=0', array(':option_name'=>$id));
		if($model){			
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array($page));
		} else $this->render("error");
	}
	
}	
/*end class*/