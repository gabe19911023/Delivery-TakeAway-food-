<?php
class UpdateController extends CController
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

    private static function getVersion()
    {
        try {
            $connection = Yii::app()->db; 
            $db_connection = true;
        } catch (Exception $e) {
            $db_connection = false;	    		
        }
        if($db_connection){
            if(Yii::app()->db->schema->getTable("{{option}}")){					
                $options = OptionsTools::find(['backend_version']);
                $backend_version = isset($options['backend_version'])?$options['backend_version']:"1";
                return $backend_version;
            }
        }
        return "1";
    }

    private static function getNewVersion()
    {        
        $path = Yii::getPathOfAlias('home_dir')."/version.txt";        
        if(file_exists($path)){            
            $content = file_get_contents($path);
            return trim($content);
        }
        return false;
    }
    
    public function beforeAction($action)
	{				   
        $action_name = $action->id ;        
        $version = $this->getVersion();    
        $new_version = $this->getNewVersion();
        if($new_version){                        
            if($new_version==$version && $action_name!="finish"){
               $this->redirect(array('/update/finish'));	    	                                   
               return false;  
            } 
        }
		return true;
	}
	
    public function actionIndex()
    {
        $new_version = $this->getNewVersion();
        if($new_version){
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

                $activated = ItemIdentity::instantiateIdentity();
                $check['activated'] = $activated;
                
                $this->render("step1", array(
                'check'=>$check
                ));	    	
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $this->render("step0");
        }
    }

    public function actionstep2()
    {
        set_time_limit(300); 
    	$error = '';
        $back_office_path = Yii::getPathOfAlias('webroot');
        $home_path = Yii::getPathOfAlias('home_dir');    	

        try {
            $new_version = $this->getNewVersion();            
            if($new_version=='1.0.1'){
                Yii::app()->db->createCommand("SET SQL_BIG_SELECTS=1")->query();
                $sql_path = Yii::getPathOfAlias('home_dir')."/sqlupdates/v101.sql"; 	 
                $stmt_query = file_get_contents($sql_path);	    	   
                
                $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);                
                $stmt = $conn->prepare($stmt_query);
                $stmt->execute();	    	    

                $backend_main = $back_office_path."/protected/config/backend_main.php";
                $backend_main_new = $back_office_path."/protected/config/backend_main.txt";

                $front_main = $home_path."/protected/config/front_main.php";
                $front_main_new = $home_path."/protected/config/front_main.txt";

                copy($backend_main_new,$backend_main);
                copy($front_main_new,$front_main);
                              
                OptionsTools::save(['backend_version'],['backend_version'=>$new_version]);

                $this->redirect(array('/update/finish'));	                                 
                
            } elseif ($new_version=='1.0.2') {
                
                Yii::app()->db->createCommand("SET SQL_BIG_SELECTS=1")->query();
                $sql_path = Yii::getPathOfAlias('home_dir')."/sqlupdates/v102.sql"; 	 
                $stmt_query = file_get_contents($sql_path);	    	                   
                
                Yii::app()->db->createCommand($stmt_query)->query();                 
                              
                OptionsTools::save(
                    ['backend_version','image_resizing'],
                    [
                      'backend_version'=>$new_version,
                      'image_resizing'=>1
                    ]
                );                
                
                $model = AR_item::model()->findAll("slug=:slug",[':slug'=>'']);
                if($model){
                    foreach ($model as $items) {                        
                        $update_model = AR_item_slug::model()->findByPk($items->item_id);                        
                        $update_model->date_modified = CommonUtility::dateNow();
                        $update_model->save();                                                
                    }
                }
                
                $this->redirect(array('/update/finish'));	

            } else {
                $this->render("update-finish");
            }
        } catch (Exception $e) {
    		$error = $e->getMessage();
    	}
    	$this->render("step-error",array(
    	  'error'=>$error
    	));	 
    }

    public function actionfinish()
    {
        $this->render("update-finish",[
           'home'=>CMedia::homeUrl(),
    	   'backoffice'=>Yii::app()->getBaseUrl(true)
        ]);
    }

}
// end class