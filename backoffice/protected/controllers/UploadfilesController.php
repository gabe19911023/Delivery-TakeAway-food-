<?php
require 'intervention/vendor/autoload.php';
use Intervention\Image\ImageManager;
								
class UploadfilesController extends CommonController
{	
	public $code=2;
	public $msg;
	public $details;
	public $data;
	public $enableCsrfValidation = false;
	
	
	public function beforeAction($action)
	{								
		/*$this->data = Yii::app()->input->xssClean($_POST);		   
		return true;*/
		
		$method = Yii::app()->getRequest()->getRequestType();
		if($method=="PUT"){
			$this->data = Yii::app()->input->xssClean(json_decode(file_get_contents('php://input'), true));
		} else $this->data = Yii::app()->input->xssClean($_POST);				
		return true;
	}
		
	public function actionFile()
	{
		$allowed_extension = explode(",",Helper_imageType);
		$maxsize = (integer)Helper_maxSize;			
		$merchant_id = 0;
		$upload_path = isset($this->data['upload_path'])?$this->data['upload_path']:'';
				
		if (!empty($_FILES)) {
		
			$title = $_FILES['file']['name'];   
			$file_size = (integer)$_FILES['file']['size'];   
			$filetype = $_FILES['file']['type'];   								
			
			if(isset($_FILES['file']['name'])){
			   $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			   $extension = strtolower($extension);
			} else $extension = strtolower(substr($title,-3,3)); 	
				
			if(!in_array($extension,$allowed_extension)){			
				$this->msg = t("Invalid file extension");
				$this->jsonResponse();
			}
			if($file_size>$maxsize){
				$this->msg = t("Invalid file size, allowed size are [size]",array(
				 '[size]'=>CommonUtility::HumanFilesize($maxsize)
				));
				$this->jsonResponse();
			}
			
						
			$tempFile = $_FILES['file']['tmp_name'];
			$upload_uuid = CommonUtility::createUUID("{{media_files}}",'upload_uuid');
			$filename = $upload_uuid.".$extension";			
			$path = CommonUtility::uploadDestination($upload_path)."/".$filename;						
			$path2 = CommonUtility::uploadDestination($upload_path)."/";
								
			$image_resizing = !empty(Yii::app()->params['settings']['image_resizing'])?Yii::app()->params['settings']['image_resizing']:0;
			$saved = false;
											
			if($image_resizing==1){
				try {

					$image_driver = !empty(Yii::app()->params['settings']['image_driver'])?Yii::app()->params['settings']['image_driver']:Yii::app()->params->image['driver'];
					$sizes = Yii::app()->params->image['sizes'];
							
					$manager = new ImageManager(array('driver' => $image_driver ));
					$image = $manager->make($tempFile)->save($path);
					foreach ($sizes as $key=> $val) {
						$size = explode("x",$val);			
						$filename_sizes = $path2.$upload_uuid."$key".".$extension";					
						$image = $manager->make($tempFile)->resize($size[0], $size[1])->save($filename_sizes);
					}			
					$saved = true;						
				} catch (Exception $e) {
					$this->msg = t($e->getMessage());
				}		
			} else {
				if(move_uploaded_file($tempFile,$path)){					
					$saved = true;
				} else $this->msg = t("Failed cannot upload file.");
			}
			
			if($saved){
				$media = new AR_media;		
				$media->merchant_id = (integer)$merchant_id;		
				$media->title = $title;
				$media->path = $upload_path;
				$media->filename = $filename;
				$media->size = $file_size;
				$media->upload_uuid = $upload_uuid;
				$media->media_type = $filetype;	
				//$media->meta_name = AttributesTools::metaMedia();		
				$media->meta_name = '';
				if($media->save()){
				  $this->code = 1; $this->msg = "OK";
				  $this->details = array();	
				} else $this->msg = CommonUtility::parseError($media->getErrors());
			}
			
		} else $this->msg = t("Invalid file");		
		$this->jsonResponse();
	}
	
	public function actiongetMedia()
	{		
									
		$method = Yii::app()->getRequest()->getRequestType();
		if($method!="PUT"){			
			Yii::app()->end();
		}
		
		$merchant_id = 0;
		$page = isset($this->data['page'])?intval($this->data['page']):0;	
		$search_string = isset($this->data['q'])?trim($this->data['q']):'';	
		$selected_file = isset($this->data['selected_file'])?$this->data['selected_file']:'';	
		$item_selected = isset($this->data['item_selected'])?$this->data['item_selected']:'';	
		
		if(is_array($item_selected) && count($item_selected)>=1){
			$selected_data = array();
			foreach ($item_selected as $items) {
				$selected_data[]=$items['filename'];
			}
			$selected_file = $selected_data;
		}
		
		if($page>0){
			$page = $page-1;
		}
								
		$criteria=new CDbCriteria();			
		$criteria->condition = "merchant_id = :merchant_id";
		$criteria->params = array (':merchant_id'=>$merchant_id);	
		
		if(!empty($search_string)){
			$criteria->addSearchCondition('title', $search_string);
		}
		
		$criteria->order = 'id DESC';	
        $count=AR_media::model()->count($criteria);        
        
        $pages=new CPagination($count);
        $pages->setCurrentPage($page);        
        $pages->pageSize = intval(Yii::app()->params->media_list_limit);        
        $pages->applyLimit($criteria);        
        $models=AR_media::model()->findAll($criteria);
                       
        $page_count = $pages->getPageCount();	        
        $current_page = $pages->getCurrentPage();
        $fallback_image = CommonUtility::getPlaceholderPhoto('item_photo');
        
        if($models){
        	$data = array();
        	foreach ($models as $item) {        		
        		$data[] = array(
        		  'id'=>$item->id,
        		  'title'=>$item->title,
        		  'filename'=>$item->filename,
        		  'size'=>CommonUtility::HumanFilesize($item->size),        		  
        		  'image_url'=>CMedia::getImage($item->filename,$item->path,'@thumbnail',$fallback_image),
        		  'is_selected'=>in_array($item->filename,(array)$selected_file)?true:false,
        		  'path'=>$item->path
        		);
        	}
        	$this->code =1; $this->msg = "OK";
			$this->details = array(
			  'page_count'=>$page_count,	
			  'current_page'=>$current_page+1,
			  'data'=>$data,			  
			);			
        } else {
        	$this->msg = t("No files has been uploaded yet"); 
        	if(!empty($search_string)){
        		$this->msg = t("No results found"); 
        	}
        }                       
		$this->responseJson();
	}
			
	public function actiongetMediaSeleted()
	{		
		$merchant_id = 0;
		$selected_file = isset($this->data['selected_file'])?$this->data['selected_file']:'';		
		$save_path = isset($this->data['save_path'])?$this->data['save_path']:'';
		
		if(!empty($selected_file)){
			
			$fallback_image = CommonUtility::getPlaceholderPhoto('item_photo');
			
			$data = array(
			  'filename'=>$selected_file,
			  'image_url'=>CMedia::getImage($selected_file,$save_path,'@thumbnail',$fallback_image),
			  'path'=>$save_path,
			);
			$this->code = 1;
			$this->msg ="OK";
			$this->details = array($data);
		} else $this->msg = "No results";
		$this->responseJson();
	}
	
	public function actiongetMediaMultipleSeleted()
	{			
		$merchant_id = 0;
		$selected_file = isset($this->data['selected_file'])?$this->data['selected_file']:'';	
			
		if(is_array($selected_file) && count($selected_file)>=1){

			$data = array();		
			$fallback_image = CommonUtility::getPlaceholderPhoto('item_photo');
			
			foreach ($selected_file as $item) {				
				$data[] = array(
				  'filename'=>$item,
				  'image_url'=>CMedia::getImage($item,"/upload/$merchant_id",'@thumbnail',$fallback_image),
				  'is_selected'=>true,
				);
			}			
			$this->code = 1;
			$this->msg ="OK";
			$this->details = $data;			
		} else $this->msg = "No results";
		$this->responseJson();
	}

	public function actiondeleteFiles()
	{
		
		if(DEMO_MODE){
		    $this->msg[] = t("Modification not available in demo");
		    $this->responseJson();
		}
  
		$merchant_id = 0;
		
		$selected_file = array();
		$item_selected = isset($this->data['item_selected'])?$this->data['item_selected']:'';	
		if(is_array($item_selected) && count($item_selected)>=1){
			$selected_data = array();
			foreach ($item_selected as $items) {
				$selected_data[]=$items['filename'];
			}
			$selected_file = $selected_data;			
			
			$criteria=new CDbCriteria();			
		    $criteria->condition = "merchant_id = :merchant_id";
		    $criteria->params = array (':merchant_id'=>$merchant_id);		    
		    $criteria->addInCondition('filename', $selected_file);		    
		    $model = AR_media::model()->deleteAll($criteria);
		    
		    CMedia::deleteFilesInArray($selected_file,"/upload/$merchant_id");
		    
		    $sizes = Yii::app()->params->image['sizes'];		    
		    $file_sizes = CMedia::getFilenameSize($selected_file,$sizes);
		    CMedia::deleteFilesInArray($file_sizes,"/upload/$merchant_id");
		    
		    $this->code = 1;
		    $this->msg = "OK";
		    $this->details = array();
		    
		} else $this->msg = t("No records to delete.");		
		$this->responseJson();
	}
	
}
/*end class*/