<?php
class CommonApi extends CController
{
	public $code=2,$msg,$details,$data;		
	
	public function __construct($id,$module=null){
		parent::__construct($id,$module);		
		// Set the application language if provided by GET, session or cookie
		if(isset($_GET['language'])) {
			Yii::app()->language = $_GET['language'];
			Yii::app()->user->setState('language', $_GET['language']); 
			$cookie = new CHttpCookie('language', $_GET['language']);
			$cookie->expire = time() + (60*60*24*365); // (1 year)
			Yii::app()->request->cookies['language'] = $cookie; 
		}
		else if (Yii::app()->user->hasState('language'))
			Yii::app()->language = Yii::app()->user->getState('language');
		else if(isset(Yii::app()->request->cookies['language']))
			Yii::app()->language = Yii::app()->request->cookies['language']->value;
	}

	public function filters()
	{		
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules()
	{		
		return array(			
		    array('allow',
                'actions'=>array('logout','error'),
                'users'=>array('*'),
            ),
			/*array('allow', 			    
				'expression'=>array('AdminUserIdentity','verifyAccess'),
			),*/
			array('allow', 			    
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function init()
	{
		Yii::app()->params['settings'] = OptionsTools::find(array(
			  'website_date_format_new','website_time_format','website_timezone_new',
			  'image_resizing','image_driver','website_date_format_new','website_time_format_new'
		));		
		
		/*SET TIMEZONE*/
		$timezone = isset(Yii::app()->params['settings']['website_timezone_new'])?Yii::app()->params['settings']['website_timezone_new']:'';		
		if (is_string($timezone) && strlen($timezone) > 0){
		   Yii::app()->timeZone=$timezone;		   
		}
		
		Price_Formatter::init();				
	}
	
	public function responseJson()
    {
    	header('Content-type: application/json');
		$resp=array('code'=>$this->code,'msg'=>$this->msg,'details'=>$this->details);
		echo CJSON::encode($resp);
		Yii::app()->end();
    }      
    
    public function responseTable($datatables=array())
    {
    	header('Content-type: application/json');
		echo CJSON::encode($datatables);
		Yii::app()->end();
    }
    
    public function responseSelect2($data)
    {
    	header('Content-type: application/json');
		echo CJSON::encode($data);
		Yii::app()->end();
    }        
}
/*end class*/