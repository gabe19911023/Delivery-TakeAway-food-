<?php
class CustomerController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
			
		return true;
	}
		
	public function actionIndex()
	{	
		$this->redirect(array(Yii::app()->controller->id.'/reviews'));		
	}		
	
	public function actionsubscriber()
	{
		$this->pageTitle=t("Subscriber List");
		$action_name='subscriber_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/subscriber_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		
		if(Yii::app()->params['isMobile']==TRUE){
			$tpl = '//tpl/lazy_list';
		} else $tpl = '//tpl/list';

		$this->render($tpl);	
	}
	
	public function actionreviews()
	{
		$this->pageTitle=t("Customer reviews");
		$action_name='customer_review';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/customerreview_delete");
		
		ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');

		$tpl = '//merchant/review_list';				
		
		$this->render($tpl);	
	}
	
	public function actionreviews_update()
	{
		$this->pageTitle = t("Update Review");
		CommonUtility::setMenuActive('.buyer','.customer_reviews');
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$id = (integer) Yii::app()->input->get('id');			
				
		$can_edit_reviews = isset(Yii::app()->params['settings']['merchant_can_edit_reviews'])?Yii::app()->params['settings']['merchant_can_edit_reviews']:'';
		if($can_edit_reviews!=1){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("Your not allowed to access this page")
			 )
			));
			Yii::app()->end();
		}
		
		
		$model = AR_review::model()->find('merchant_id=:merchant_id AND id=:id', 
		array(':merchant_id'=>$merchant_id, ':id'=>$id ));
		
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}		 
		
		if(isset($_POST['AR_review'])){
			$model->attributes=$_POST['AR_review'];
			if($model->validate()){					
				if($model->save()){
					Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
					$this->refresh();
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$this->render("//merchant/reviews_create",array(
		  'model'=>$model,
		  'status'=>(array)AttributesTools::StatusManagement('post'),		  
		  'links'=>array(
	            t("All Review")=>array(Yii::app()->controller->id.'/reviews'),        
                $this->pageTitle,
		    ),	 
		));		
	}
	
	public function actionreview_reply()
	{
	    $this->pageTitle = t("Update Review");
		CommonUtility::setMenuActive('.buyer','.customer_reviews');
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$id = (integer) Yii::app()->input->get('id');	
		
		$find = AR_review::model()->find('merchant_id=:merchant_id AND id=:id', 
		array(':merchant_id'=>$merchant_id, ':id'=>$id ));
		
		if(!$find){				
			$this->render("error");				
			Yii::app()->end();
		}		 
		
		$model = new AR_review;
		$model->scenario = 'reply';
		
		if(isset($_POST['AR_review'])){
			$model->attributes=$_POST['AR_review'];
			if($model->validate()){
								
				$merchant = AR_merchant::model()->findByPk( $merchant_id);
								
				$model->parent_id = $id;
				$model->reply_from = $merchant->restaurant_name;
				$model->review = $model->reply_comment;
								
				if($model->save()){
					$this->redirect(array(Yii::app()->controller->id.'/reviews'));
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$this->render("//merchant/review_reply",array(
		  'model'=>$model,
		  'find'=>$find,
		  'status'=>(array)AttributesTools::StatusManagement('post'),		  
		  'links'=>array(
	            t("All Review")=>array(Yii::app()->controller->id.'/reviews'),        
                $this->pageTitle,
		    ),	 
		));				
	}
	
	public function actionreview_reply_update()
	{
	    $this->pageTitle = t("Update Review");
		CommonUtility::setMenuActive('.buyer','.customer_reviews');
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_review::model()->findByPk( $id );
		
		if(!$model){				
			$this->render("error");				
			Yii::app()->end();
		}		
		
		$model->scenario = 'reply';
		$model->reply_comment = $model->review;
		
		$find = AR_review::model()->find('id=:id', 
		array(':id'=>$model->parent_id));
		
		if(isset($_POST['AR_review'])){
			$model->attributes=$_POST['AR_review'];
			if($model->validate()){
				$model->review = $model->reply_comment;
				if($model->save()){
					$this->redirect(array(Yii::app()->controller->id.'/reviews'));
				} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
			} else Yii::app()->user->setFlash('error',t(HELPER_CORRECT_FORM));
		}
		
		$this->render("//merchant/review_reply",array(
		  'model'=>$model,
		  'find'=>$find,
		  'status'=>(array)AttributesTools::StatusManagement('post'),		  
		  'links'=>array(
	            t("All Review")=>array(Yii::app()->controller->id.'/reviews'),        
                $this->pageTitle,
		    ),	 
		));		
	}
	
	public function actioncustomerreview_delete()
	{
		$can_edit_reviews = isset(Yii::app()->params['settings']['merchant_can_edit_reviews'])?Yii::app()->params['settings']['merchant_can_edit_reviews']:'';
		if($can_edit_reviews!=1){
			$this->render('//tpl/error',array(
			 'error'=>array(
			   'message'=>t("Your not allowed to access this page")
			 )
			));
			Yii::app()->end();
		}
		
		$id = (integer) Yii::app()->input->get('id');			
		$model = AR_review::model()->findByPk( $id );		
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array(Yii::app()->controller->id.'/reviews'));			
		} else $this->render("error");
	}
	
}
/*end class*/