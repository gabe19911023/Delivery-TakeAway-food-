<?php
class ResetpswdController extends CController
{
	public $layout='login';

	public function init()
	{
		AssetsBundle::registerBundle(array(		 
		 'login-css'
		));			
	}
	
	public function behaviors() {
		return array(
	        'BodyClassesBehavior' => array(
	            'class' => 'ext.yii-body-classes.BodyClassesBehavior'
	        ),        
	    );
    }
    
    public function filters()
	{
		return array(			
			array(
			  'application.filters.HtmlCompressorFilter',
			)
		);
	}
    
	public function beforeAction($action)
	{		
		if(!Yii::app()->user->isGuest){
			$this->redirect(Yii::app()->createUrl('/merchant/dashboard'));
			Yii::app()->end();
		}								
		return true;
	}
	
	public function actionIndex()
	{
		$this->pageTitle = t("Merchant - Forgot Password");
		
		$model = new AR_merchant_login;
		$model->scenario='forgot_password';
		
		if(isset($_POST['AR_merchant_login'])){
			$model->attributes=$_POST['AR_merchant_login'];				
			if($model->validate()){				
				$user = AR_merchant_login::model()->find('contact_email=:contact_email', array(':contact_email'=>$model->email_address));			
				$user->scenario = "send_forgot_password";
				$user->date_modified = CommonUtility::dateNow();
				$user->save();				
				Yii::app()->user->setFlash('success',t("E-mail has been sent to your account."));
				$this->refresh();
			}
		}
		
		$this->render('//forgotpassword/forgot_password',
		   array(
		     'model'=>$model,
		     'back_link'=>Yii::app()->createUrl("/auth/login")
		));
	}

	public function actionreset()
	{
		$this->pageTitle = t("Merchant - Forgot Password");
		$token = Yii::app()->input->get('token');		
		$model = AR_merchant_user::model()->find("user_uuid=:user_uuid",[
			':user_uuid'=>$token
		]);		
		if($model){
			$model->scenario = "reset_password";
			if(isset($_POST['AR_merchant_user'])){
				$model->attributes=$_POST['AR_merchant_user'];
				if($model->validate()){
					$model->password = md5($model->new_password);					
					$model->date_modified = CommonUtility::dateNow();
					$model->user_uuid = CommonUtility::generateToken("{{merchant_user}}",'user_uuid');
					if($model->save()){
					   $this->redirect( Yii::app()->createUrl("/resetpswd/success") );
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} else Yii::app()->user->setFlash('error',t("An error has occured."));
			}

			$this->render('//forgotpassword/reset_password',
			array(		    
				'model'=>$model,
				'back_link'=>Yii::app()->createUrl("/auth/login")
			));
		} else {
			$this->render("//admin/error",[
				'error'=>[
					'message'=>t("Sorry we cannot find what your looking for.")
				]
			]);
		}
	}

	public function actionsuccess()
	{
		Yii::app()->user->setFlash('success',CommonUtility::t("Your password has been reset."));
		$this->render('//forgotpassword/reset_success',[
			'back_link'=>Yii::app()->createUrl("/auth/login")
		]);
	}
	
}
/*end class*/