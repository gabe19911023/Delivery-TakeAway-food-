<?php
class Commonmerchant extends CController
{
	public $ctr_name='merchant';
	
	public function __construct($id,$module=null){
		parent::__construct($id,$module);
		// If there is a post-request, redirect the application to the provided url of the selected language 
		if(isset($_POST['language'])) {
			$lang = $_POST['language'];
			$MultilangReturnUrl = $_POST[$lang];
			$this->redirect($MultilangReturnUrl);
		}
		// Set the application language if provided by GET, session or cookie
		if(isset($_GET['language'])) {
			Yii::app()->language = $_GET['language'];
			Yii::app()->merchant->setState('language', $_GET['language']); 
			$cookie = new CHttpCookie('language', $_GET['language']);
			$cookie->expire = time() + (60*60*24*365); // (1 year)
			Yii::app()->request->cookies['language'] = $cookie; 
		}
		else if (Yii::app()->merchant->hasState('language'))
			Yii::app()->language = Yii::app()->merchant->getState('language');			
		else if(isset(Yii::app()->request->cookies['language']))
			Yii::app()->language = Yii::app()->request->cookies['language']->value;			
	}

	public function createMultilanguageReturnUrl($lang='en'){
		if (count($_GET)>0){
			$arr = $_GET;
			$arr['language']= $lang;
		}
		else 
			$arr = array('language'=>$lang);
		return $this->createUrl('', $arr);
	}

	public function filters()
	{		
		if(Yii::app()->merchant->isGuest){
			$this->redirect(Yii::app()->merchant->loginUrl);		
		}	
		
		$route = Yii::app()->urlManager->parseUrl(Yii::app()->request);
		$allow = array('merchant/profile','merchant/logout','plan/manage','plan/error','merchant/change_password');
		if(Yii::app()->merchant->merchant_type==1 && Yii::app()->merchant->status=="expired"){
			if(!in_array($route,$allow)){
			  $this->redirect(Yii::app()->createUrl('/plan/manage'));			
			}
		}		
		
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
                'actions'=>array('logout','error','access_denied','profile','change_password','profile_remove_image','plan'),
                'users'=>array('*'),                
            ),
			array('allow', 			    
				'expression'=>array('UserIdentityMerchant','verifyAccess'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
				'deniedCallback' => function() { $this->redirect(array('/merchant/access_denied')); }
			),
		);
	}
	
	public function behaviors() {
		return array(
	        'BodyClassesBehavior' => array(
	            'class' => 'ext.yii-body-classes.BodyClassesBehavior'
	        ),        
	    );
    }
    
	public function init()
	{
		
		$this->initSettings();
		
		$detect = CommonUtility::MobileDetect();
		$is_mobile = false;

		$this->layout = 'backend_merchant';	
		$ajaxurl = Yii::app()->createUrl("/backendmerchant");
		$apibackend = Yii::app()->createUrl("/apibackend");
				
		if ($detect->isMobile() || $detect->isTablet() ) {						
			//$is_mobile=true;
			//$ajaxurl = Yii::app()->createUrl("/backendappmerchant");
		} 
		
		Yii::app()->params['isMobile'] = $is_mobile;
		 
		$realtime = AR_admin_meta::getMeta(array('realtime_app_enabled','realtime_provider','webpush_app_enabled','webpush_provider'));		
		$realtime_app_enabled = isset($realtime['realtime_app_enabled'])?$realtime['realtime_app_enabled']['meta_value']:'';
		$realtime_provider = isset($realtime['realtime_provider'])?$realtime['realtime_provider']['meta_value']:'';
		
		$webpush_app_enabled = isset($realtime['webpush_app_enabled'])?$realtime['webpush_app_enabled']['meta_value']:'';
		$webpush_provider = isset($realtime['webpush_provider'])?$realtime['webpush_provider']['meta_value']:'';
				
		$include = array('backend-core','backend-css','merchant-js');
		if($realtime_app_enabled==1){
		   array_unshift($include, $realtime_provider);
		}
				
		if($webpush_app_enabled==1){
		   array_unshift($include, "webpush_".$webpush_provider );
		}
												
		AssetsBundle::registerBundle($include);		
		/*AssetsBundle::registerBundle(array(		 
		 'backend-core',
		 'backend-css',
		 'merchant-js',
		));*/
				
		$upload_ajaxurl = Yii::app()->createUrl("/upload");
		$settings = AttributesTools::MoneyConfig();		
		$translation_vendor = AttributesTools::translationVendor();		
		
		ScriptUtility::registerScript(array(
		  "var ajaxurl='$ajaxurl';",
		  "var upload_ajaxurl='$upload_ajaxurl';",
		  "var apibackend='$apibackend';",
		  "var is_mobile='$is_mobile';",
		  "var money_config='".CJavaScript::quote($settings)."';",		
		  "var translation_vendor='".CJavaScript::quote(json_encode($translation_vendor))."';"  
		),'admin_script');
			
	}
	
	public function initSettings()
	{
		Yii::app()->params['settings'] = OptionsTools::find(array(
			  'website_date_format_new','website_time_format','website_timezone_new',
			  'image_resizing','image_driver','enabled_manual_status','merchant_can_edit_reviews','registration_terms_condition_payment',
			  'enabled_language_bar','default_language'
		));				
		/*SET TIMEZONE*/
		$timezone = isset(Yii::app()->params['settings']['website_timezone_new'])?Yii::app()->params['settings']['website_timezone_new']:'';		
		if (is_string($timezone) && strlen($timezone) > 0){
		   Yii::app()->timeZone=$timezone;		   
		}
		
		Price_Formatter::init();				
		
		$realtime = AR_admin_meta::getMeta(array('realtime_app_enabled','realtime_provider',
		  'pusher_key','pusher_cluster','ably_apikey','piesocket_api_key','piesocket_websocket_api','piesocket_clusterid'
		));				
		$realtime_app_enabled = isset($realtime['realtime_app_enabled'])?$realtime['realtime_app_enabled']['meta_value']:'';
		
		$realtime_provider = isset($realtime['realtime_provider'])?$realtime['realtime_provider']['meta_value']:'';
		$pusher_key = isset($realtime['pusher_key'])?$realtime['pusher_key']['meta_value']:'';
		$pusher_cluster = isset($realtime['pusher_cluster'])?$realtime['pusher_cluster']['meta_value']:'';		
		$ably_apikey = isset($realtime['ably_apikey'])?$realtime['ably_apikey']['meta_value']:'';
		
		$piesocket_api_key = isset($realtime['piesocket_api_key'])?$realtime['piesocket_api_key']['meta_value']:'';
		$piesocket_websocket_api = isset($realtime['piesocket_websocket_api'])?$realtime['piesocket_websocket_api']['meta_value']:'';
		$piesocket_clusterid = isset($realtime['piesocket_clusterid'])?$realtime['piesocket_clusterid']['meta_value']:'';
		
		Yii::app()->params['realtime_settings'] = array(
		  'enabled'=>$realtime_app_enabled,
		  'provider'=>$realtime_provider,
		  'key'=>$pusher_key,
		  'cluster'=>$pusher_cluster,
		  'ably_apikey'=>$ably_apikey,
		  'piesocket_api_key'=>$piesocket_api_key,
		  'piesocket_websocket_api'=>$piesocket_websocket_api,
		  'piesocket_clusterid'=>$piesocket_clusterid,
		);				
	}
	
	public function actionDatableLocalize()
	{
		$data = CommonUtility::dataTablesLocalization();
		header('Content-Type: application/json; charset="UTF-8"');
		echo CJSON::encode($data);
	}
	
	public function jsonResponse()
	{
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
	}
	
	public function DataTablesNodata()
	{
		if (isset($_POST['draw'])){
			$feed_data['draw']=(integer)$_POST['draw'];
		} else $feed_data['draw']=1;	   
		     
        $feed_data['recordsTotal']=0;
        $feed_data['recordsFiltered']=0;
        $feed_data['data']=array();		        
        echo CJSON::encode($feed_data);    	
	}

	public function DataTablesData($feed_data='')
	{
	    echo CJSON::encode($feed_data);    
    }    
    
    public function responseSelect2($data)
    {
    	header('Content-type: application/json');
		echo CJSON::encode($data);
		Yii::app()->end();
    }        
    
    public function responseJson()
    {
    	header('Content-type: application/json');
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
    }       
    
}
/*end class*/