<?php
class EwalletController extends CommonController
{
	public function actioncreate_card()
	{
		$model = AR_wallet_cards::model()->find("account_type=:account_type AND account_id=:account_id",array(
		  ':account_type'=>Yii::app()->params->account_type['admin'],
		  ':account_id'=>0
		));
		if(!$model){
			$model = new AR_wallet_cards;			
		}		
					
		if(isset($_POST['AR_wallet_cards'])){			
			$model->account_type = Yii::app()->params->account_type['admin'] ;			
			if($model->save()){
				Yii::app()->user->setFlash('success',CommonUtility::t(Helper_created));
				$this->refresh();
			} else Yii::app()->user->setFlash('error',t("Failed creating card"));
		}
		
		$this->render('//wallet/create_card',array(
		 'model'=>$model,
		 'return_link'=>Yii::app()->createUrl('/admin/dashboard')
		));
	}
}
/*end class*/