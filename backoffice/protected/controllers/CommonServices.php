<?php
class CommonServices extends CController
{
	public $code=2,$msg,$details,$data;		
	
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

	public function filters()
	{		
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules()
	{								
		/*return array(			
		    array('allow', 			    
				'expression'=>array('UserIdentityMerchant','verifyAccess'),
			),
			array('deny', 
				'users'=>array('*'),
				'deniedCallback' => function() { $this->redirect(array('/merchant/access_denied')); }
			),
		);*/
				
		return array(					    
			array('allow', 			    
				'expression'=>array('UserIdentityMerchant','verifyLogin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
		
	}
	
	public function init()
	{
		Yii::app()->params['settings'] = OptionsTools::find(array(
			  'website_date_format_new','website_time_format_new',
			  'image_resizing','image_driver','website_timezone_new','enabled_manual_status'
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
    
    public function actionDatableLocalize()
	{
		$data = CommonUtility::dataTablesLocalization();
		header('Content-Type: application/json; charset="UTF-8"');
		echo CJSON::encode($data);
	}
	
	public function DataTablesData($feed_data='')
	{
	    echo CJSON::encode($feed_data);    
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
    
}
/*end class*/