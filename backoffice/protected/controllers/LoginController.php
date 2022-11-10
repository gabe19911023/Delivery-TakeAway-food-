<?php
class LoginController extends CController
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
			'accessControl',
			array(
			  'application.filters.HtmlCompressorFilter',
			)
		);
	}
	
	public function accessRules()
	{		
		return array(			
		    array('allow',
                'actions'=>array('logout','error','login'),
                'users'=>array('*'),
            ),
			array('allow', 			    
				'expression'=>array('ItemIdentity','initializeIdentity'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
				'deniedCallback' => function() { $this->redirect(array('/login/error')); }
			),
		);
	}
    
	public function beforeAction($action)
	{		
		if(!Yii::app()->user->isGuest){
			$login_type = Yii::app()->user->getState("login_type");
			if($login_type=="admin"){
			   $this->redirect(Yii::app()->createUrl('/admin/dashboard'));			
			}
		}								
		return true;
	}
	
	
	public function actionIndex()
	{
		if (!defined('CRYPT_BLOWFISH')||!CRYPT_BLOWFISH)
			throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");
			
		$this->pageTitle = t("Administrator Login");
		
		if(DEMO_MODE){
			ScriptUtility::registerScript(array(
			 "
			 function copyCredentials() {
		         $('#LoginForm_username').val('admin');
		         $('#LoginForm_password').val('admin');
		     }
			 "
			),'demo_script');
		}
		
		$options = OptionsTools::find(array('captcha_site_key','captcha_secret','captcha_lang','capcha_admin_login_enabled'));
		$captcha_site_key = isset($options['captcha_site_key'])?$options['captcha_site_key']:'';
		$captcha_secret = isset($options['captcha_secret'])?$options['captcha_secret']:'';
		$captcha_lang = isset($options['captcha_lang'])?$options['captcha_lang']:'';
		$captcha_enabled = isset($options['capcha_admin_login_enabled'])?$options['capcha_admin_login_enabled']:'';
		$captcha_enabled = $captcha_enabled==1?true:false;	
		if($captcha_enabled){
			if(empty($captcha_site_key) || empty($captcha_secret) ){
				$captcha_enabled = false;
			}
		}
		
		Yii::app()->reCaptcha->key=$captcha_site_key;
		Yii::app()->reCaptcha->secret=$captcha_secret;
		
						
		$model=new LoginForm;		
		$model->captcha_enabled = $captcha_enabled;
		

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{		
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())			    
				Yii::app()->request->redirect( Yii::app()->createUrl("/admin/dashboard") );
		}		
				
		$this->render('loginForm',array(
		  'model'=>$model,
		  'captcha_enabled'=>$captcha_enabled,
		));
	}
	
	public function actionerror()
	{
		echo 'Something went wrong contact the author https://codecanyon.net/user/bastikikang';
	}
	
}
/*end class*/