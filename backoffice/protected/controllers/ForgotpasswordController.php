<?php
class ForgotpasswordController extends CController
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
    
	public function beforeAction($action)
	{		
		if(!Yii::app()->user->isGuest){
			$this->redirect(Yii::app()->createUrl('/admin/dashboard'));
			Yii::app()->end();
		}								
		return true;
	}
	
	public function actionIndex()
	{
		$this->pageTitle = t("Administrator - Forgot Password");
		
		$model = new AR_AdminUser;
		$model->scenario='forgot_password';
		
		if(isset($_POST['AR_AdminUser'])){
			$model->attributes=$_POST['AR_AdminUser'];				
			if($model->validate()){				
				$user = AR_AdminUser::model()->find('email_address=:email_address', array(':email_address'=>$model->email_address));			
				$user->scenario = "send_forgot_password";
				$user->date_modified = CommonUtility::dateNow();
				$user->save();				
				Yii::app()->user->setFlash('success',t("E-mail has been sent to your account."));
				$this->refresh();
			}
		}
		
		$this->render('forgot_password',
		  array(
		    'model'=>$model,
		    'back_link'=>Yii::app()->createUrl("/login")
		));
	}
	
	public function actionreset()
	{
		$this->pageTitle = t("Administrator - Forgot Password");
		
		$token = Yii::app()->input->get('token');		
		$model = AR_AdminUser::model()->find("admin_id_token=:admin_id_token",[
			':admin_id_token'=>$token
		]);
		if($model){
			$model->scenario = "reset_password";

			if(isset($_POST['AR_AdminUser'])){
				$model->attributes=$_POST['AR_AdminUser'];
				if($model->validate()){
					$model->password = md5($model->new_password);					
					$model->date_modified = CommonUtility::dateNow();
					$model->admin_id_token = CommonUtility::generateToken("{{admin_user}}",'admin_id_token');
					if($model->save()){
					   $this->redirect( Yii::app()->createUrl("/forgotpassword/success") );
					} else Yii::app()->user->setFlash('error',t(Helper_failed_update));
				} else Yii::app()->user->setFlash('error',t("An error has occured."));
			}

			$this->render('reset_password',
			array(		    
				'model'=>$model,
				'back_link'=>Yii::app()->createUrl("/login")
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
		$this->render('reset_success',[
			'back_link'=>Yii::app()->createUrl("/login")
		]);
	}

}
/*end class*/