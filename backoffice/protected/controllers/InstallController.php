<?php
class InstallController extends CController
{
	public $layout='install';

	public function init()
	{
		AssetsBundle::registerBundle(array(		 
		 'install-css'
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
		if(Yii::app()->db->schema->getTable("{{admin_meta}}")){
			$model = AR_admin_meta::getValue('complete_installation');
			$meta_value = isset($model['meta_value'])?$model['meta_value']:false;
			$meta_value = $meta_value==1?true:false;
			if($meta_value){
				$this->redirect(Yii::app()->createUrl('/admin/dashboard'));
				return false;
			}
		}
		return true;
	}
	
    
    public function actionIndex()
    {
    	$this->pageTitle="Installation - Step 1";
    	
    	try {
	    	$back_office_path = Yii::getPathOfAlias('webroot');
	    	$home_path = Yii::getPathOfAlias('home_dir');    	
	    	
	    	
	    	try {
	    		$connection = Yii::app()->db; 
	    		$db_connection = true;
	    	} catch (Exception $e) {
	    		$db_connection = false;	    		
	    	}
	    	
	    	$check = array();
	    	$check['php']  = number_format((float) phpversion(), 2, '.', '');
	    	$check['database'] = $db_connection;
	    	$check['curl_enabled'] = function_exists('curl_version');    	
	    	$check['runtime_backoffice'] = is_writable($back_office_path."/protected/runtime");
	    	$check['runtime_home'] = is_writable($home_path."/protected/runtime");
	    	$check['upload_folder'] = is_writable($home_path."/upload");
	    	$check['pdo'] = defined('PDO::ATTR_DRIVER_NAME'); 
	        
	    	$this->render("step1", array(
	    	 'check'=>$check
	    	));	    	
    	} catch (Exception $e) {
    		echo $e->getMessage();
    	}
    }
    
    public function actionstep2()
    {
    	$this->pageTitle="Installation - Step 2";
    	$this->render("step2");	    	
    }
    
    public function actionimportsql()
    {
    	set_time_limit(300); 
    	$error = '';
    	try {
    		
    		Yii::app()->db->createCommand("SET SQL_BIG_SELECTS=1")->query();
    		
	    	$sql_path = Yii::getPathOfAlias('home_dir')."/karenderia.sql"; 	    	
	    	if(file_exists($sql_path)){
	    	   $stmt_query = file_get_contents($sql_path);		
	    	   Yii::app()->db->createCommand($stmt_query)->query();
	    	   	    	   
	    	   ob_start();
	    	   $this->actionCreateview();	    	   	    	   
	    	   $contents = ob_get_clean();
			   
			   $sql2_path = Yii::getPathOfAlias('home_dir')."/template-only.sql"; 	
			   if(file_exists($sql2_path)){    	
				    $stmt_query2 = file_get_contents($sql2_path);
					$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);                
					$stmt = $conn->prepare($stmt_query2);
					$stmt->execute(); 	   
			   }
	    	   	    	   
	    	   $this->redirect(array('/install/admin_information'));	    	   
	    	   
	    	} else $error = "SQL import file not found";	    		    	
    	} catch (Exception $e) {
    		$error = $e->getMessage();
    	}
    	$this->render("step3",array(
    	  'error'=>$error
    	));	 
    }
    
    public function actionCreateview()
    {
    	$up = new MG_view_status_management; $up->up();
    	$up = new MG_view_ratings; $up->up();
    	$up = new MG_view_cuisine; $up->up();
    	$up = new MG_view_item_lang_size; $up->up();
    	$up = new MG_view_item_relationship_subcategory; $up->up();
    	$up = new MG_view_item_relationship_subcategory_item; $up->up();
    	$up = new MG_view_item_size; $up->up();
    	$up = new MG_view_services_fee; $up->up();
    	$up = new MG_view_order_status; $up->up();
    	
    	Yii::app()->db->createCommand("ALTER TABLE {{ordernew}} AUTO_INCREMENT = 10000")->query();
    }
    
    public function actionadmin_information()
    {
    	$model = new AR_AdminUser;
    	$model->scenario='create';
    	    	
    	if(isset($_POST['AR_AdminUser'])){
    		$model->attributes=$_POST['AR_AdminUser'];
    		$model->role=1;
    		$model->main_account = 1;
    		if($model->validate()){
    			if(!empty($model->repeat_password) && !empty($model->new_password)){					
					$model->password = md5(trim($model->new_password));
				}
    			if($model->save()){
    				$this->redirect(array('/install/finish'));	
    			} else Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString($model->getErrors(),'<br/>') );	
    		} else Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString($model->getErrors(),'<br/>') );	
    	}
    	
    	$this->render("step4",array(
    	  'model'=>$model,
    	));	 
    }
    
    public function actionfinish()
    {
    	AR_admin_meta::saveMeta('complete_installation',1);    	
    	$this->render("step5",array(
    	 'home'=>CMedia::homeUrl(),
    	 'backoffice'=>Yii::app()->getBaseUrl(true)
    	));	 
    }
    
}
/*end class*/