<?php
class AddonController extends CommonController
{
		
	public function beforeAction($action)
	{							
		return true;
	}

    public function actionIndex()
    {
        $this->redirect(Yii::app()->createUrl("/addon/manager"));
    }

    public function actionmanager()
    {
        $this->pageTitle = t("Addon manager");
        $this->render("addon-list");
    }

    public function actioninstall()
    {
        $this->pageTitle = t("Addon install/update");
        CommonUtility::setMenuActive('.addon_manager',"");

        $model = new AR_addons;
        $model->scenario = "upload";
        if(isset($_POST['AR_addons'])){
            $model->attributes=$_POST['AR_addons'];            
            if($model->validate()){
                $model->file = CUploadedFile::getInstance($model,'file');
                if($model->file){
                    $path = CommonUtility::uploadDestination('upload/addons/').$model->file->name;                
                    $model->file->saveAs( $path );
                    $this->redirect(Yii::app()->createUrl("/addon/extract",['filename'=>$model->file->name]));
                } else {
                    Yii::app()->user->setFlash('error', t("an error has occured") );
                }
            } else {                
                Yii::app()->user->setFlash('error', CommonUtility::parseModelErrorToString($model->getErrors(),"<br/>") );
            }
        }

        $this->render("addon-install",[
            'model'=>$model,
            'links'=>array(
				'links'=>array(
				    t("Addon manager")=>array('addon/manager'),        
				    t("install/update"),
				),
				'homeLink'=>false,
				'separator'=>'<span class="separator">
				<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
			)
        ]);
    }

    public function actionextract()
    {
        $error = ''; $home_path = Yii::getPathOfAlias('home_dir');    
        $file_to = ''; 	$file_from='';

        $filename = Yii::app()->input->get('filename');          
        $path = CommonUtility::uploadDestination('upload/addons/').$filename;
        $extract_path = CommonUtility::uploadDestination('protected/modules');        
        if (file_exists($path)){
            $modules_folder = str_replace(".zip","",$filename);
            $zip = new ZipArchive;
            $res = $zip->open($path);
            if ($res === TRUE) {
                $zip->extractTo($extract_path);
                $zip->close();                
                $package = $extract_path."/$modules_folder/package.json";
                if(file_exists($package)){
                    $data = file_get_contents($extract_path."/$modules_folder/package.json");
                    $data = json_decode($data,true);                    
                    $version = $data['version'];
                    $version = str_replace(".","",$version);                    
                    $model = AR_addons::model()->find("uuid=:uuid",[
                       ':uuid'=>$data['uuid']
                    ]);                    
                    if(!$model){                                               
                       $sqlupdate = $extract_path."/$modules_folder/filesupdate/v$version.sql";
                    } else {
                       $sqlupdate = $extract_path."/$modules_folder/filesupdate/v$version-update.sql";
                    }                                        
                    if(file_exists($sqlupdate)){                        
                        $sql_cmd = file_get_contents($sqlupdate);                        
                        Yii::app()->db->createCommand($sql_cmd)->query();

                        $image_from =  $extract_path."/$modules_folder/filesupdate/".$data['image'];
                        $image_to =  $home_path."/upload/all/".$data['image'];

                        if(!file_exists($home_path."/upload/all/")){
                            mkdir($home_path."/upload/all/",0777);
                        }

                        if(file_exists($image_from )){
                            copy($image_from,$image_to);
                        }

                        $files = $data['file'];
                        if(is_array($files) && count($files)>=1){                            
                            foreach ($files as $items){                                
                                switch ($items['action']) {                                    
                                    case "copy":                                                                                
                                        $file_from =  $extract_path."/$modules_folder/".$items['file'];
                                        $file_to = $home_path."/".$items['to'];                                        
                                        copy($file_from,$file_to);
                                        break;
                                    case "extract":
                                        $file_from =  $extract_path."/$modules_folder/".$items['file'];
                                        $file_to = $home_path."/".$items['to'];  
                                        $zip2 = new ZipArchive;
                                        $fileres = $zip2->open($file_from);
                                        if ($fileres === TRUE) {
                                            $zip2->extractTo($file_to);
                                            $zip2->close(); 
                                        }
                                        break;   
                                }
                            }
                        }
                        $this->redirect(Yii::app()->createUrl("/addon/manager"));
                    } else $error = t("File not found {{file}}",['{{file}}'=>$sqlupdate]);
                } else $error = t("File not found {{file}}",['{{file}}'=>$package]);
            } else $error = t("Cannot open file {{file}}",['{{file}}'=>$path]);
        } else $error = t("File not found {{file}}",['{{file}}'=>$path]);
        
        if(!empty($error)){
            $this->render("//tpl/error",[
               'error'=>['message'=>$error]
            ]);
        }
    }

}
// end class