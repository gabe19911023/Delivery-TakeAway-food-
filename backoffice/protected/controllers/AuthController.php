<?php
class AuthController extends CController
{
	public $layout='login';
	
	public function init()
	{
		AssetsBundle::registerBundle(array(		 
		 'login-css','login-js'
		));			
	}

	public function beforeAction($action)
	{		
		if(!Yii::app()->merchant->isGuest){
			$this->redirect(Yii::app()->createUrl('/merchant/dashboard'));			
		}	
		return true;
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
		
	public function actionlogin()
	{	
		$this->pageTitle = t("Merchant Login");
		$model=new AR_merchant_login;	
		$model->scenario='login';
		
		if(DEMO_MODE){
			ScriptUtility::registerScript(array(
			 "
			 function copyCredentials() {
		         $('#AR_merchant_login_username').val('mcuser');
		         $('#AR_merchant_login_password').val('mcuser');
		     }
			 "
			),'demo_script');
		}
		
		$options = OptionsTools::find(array('captcha_site_key','captcha_secret','captcha_lang','capcha_merchant_login_enabled'));
		$captcha_site_key = isset($options['captcha_site_key'])?$options['captcha_site_key']:'';
		$captcha_secret = isset($options['captcha_secret'])?$options['captcha_secret']:'';
		$captcha_lang = isset($options['captcha_lang'])?$options['captcha_lang']:'';
		$captcha_enabled = isset($options['capcha_merchant_login_enabled'])?$options['capcha_merchant_login_enabled']:'';
		$captcha_enabled = $captcha_enabled==1?true:false;	
		if($captcha_enabled){
			if(empty($captcha_site_key) || empty($captcha_secret) ){
				$captcha_enabled = false;
			}
		}
		
		Yii::app()->reCaptcha->key=$captcha_site_key;
		Yii::app()->reCaptcha->secret=$captcha_secret;
		$model->captcha_enabled = $captcha_enabled;
		
		if(isset($_POST['AR_merchant_login'])){
			$model->attributes=$_POST['AR_merchant_login'];
			if($model->validate() && $model->login() ){
			   Yii::app()->request->redirect( Yii::app()->createUrl("merchant/dashboard") );
			}
		}
				
		$this->render('//loginmerchant/login',array(
		 'model'=>$model,
		 'captcha_enabled'=>$captcha_enabled,
		));
	}	
		
}
/*end class*/	