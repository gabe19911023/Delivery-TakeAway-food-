<?php
class ImagesController extends Commonmerchant
{
		
	public function beforeAction($action)
	{				
		
		InlineCSTools::registerStatusCSS();
		InlineCSTools::registerOrder_StatusCSS();
			
		return true;
	}
		
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else	    	    
	        	$this->render('error', array(
	        	 'error'=>$error
	        	));
	    }
	}
	
	public function actionindex()
	{
		$this->redirect(array(Yii::app()->controller->id.'/gallery'));
	}
	
	public function actiongallery()
	{
		$this->pageTitle=t("Gallery list");
		$action_name='gallery_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/gallery_delete");
		
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;
		$upload_path = CMedia::merchantFolder();
		
		if(isset($_POST['yt0'])){			
									
			if(DEMO_MODE && in_array($merchant_id,DEMO_MERCHANT)){		
			    $this->render('//tpl/error',array(  
			          'error'=>array(
			            'message'=>t("Modification not available in demo")
			          )
			        ));	
			    return false;
			}
				
			AR_merchant_meta::model()->deleteAll('merchant_id=:merchant_id 			
			AND meta_name=:meta_name', 
			 array(
			    ':merchant_id' => $merchant_id,			    
			    ':meta_name'=>AttributesTools::metaMedia()
			));
			
			if(isset($_POST['merchant_gallery'])){	
				$params = array();				
				foreach ($_POST['merchant_gallery'] as $key=> $items) {
					$params[]=array(
					  'merchant_id'=>$merchant_id,					  
					  'meta_name'=>AttributesTools::metaMedia(),
					  'meta_value'=>$items,
					  //'meta_value1'=>isset($_POST['path'][$key])?$_POST['path'][$key]:$upload_path,
					  'meta_value1'=>$upload_path,
					  'date_modified'=>CommonUtility::dateNow()
					);				
				}					
				$builder=Yii::app()->db->schema->commandBuilder;
				$command=$builder->createMultipleInsertCommand('{{merchant_meta}}',$params);
				$command->execute();	
			}
			
			Yii::app()->user->setFlash('success',CommonUtility::t(Helper_update));
			$this->refresh();		
		}
		
		$gallery = array();
		$meta = AR_merchant_meta::model()->findAll("merchant_id=:merchant_id 
		AND meta_name=:meta_name ",array(
		  ':merchant_id'=>$merchant_id,		  
		  ':meta_name'=>AttributesTools::metaMedia()
		));
		if($meta){
			foreach ($meta as $item) {				
				$gallery[] = $item->meta_value;
			}			
		}
							
		$this->render("gallery",array(		 
		  'gallery'=>$gallery,
		  'upload_path'=>$upload_path
		));
	}
	
	public function actiongallery_create()
	{
		$this->pageTitle=t("Add Gallery");
		CommonUtility::setMenuActive('.merchant_images','.images_gallery');		
		
		$upload_ajaxurl = Yii::app()->createUrl("/upload");
		$upload_params = array(
		  Yii::app()->request->csrfTokenName=>Yii::app()->request->csrfToken,		  
		);		
		$upload_params = json_encode($upload_params);
		
		ScriptUtility::registerScript(array(
		  "var upload_ajaxurl='$upload_ajaxurl';",		  
		  "var upload_params='$upload_params';",		  
		),'upload_ajaxurl');
				
		
		$this->render("/merchant/gallery_create",array(
		  'links'=>array(
	            t("All Gallery")=>array(Yii::app()->controller->id.'/gallery'),    	            
		    ),	    	
		   'done'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/gallery")
		));
	}
	
	public function actiongallery_delete()
	{
		$this->actionmedia_remove(Yii::app()->controller->id."/gallery");
	}
	
	public function actionmedia_remove($page_redirect='')
	{
		$merchant_id = (integer) Yii::app()->merchant->merchant_id;		
		$id = (integer) Yii::app()->input->get('id');	
		$page = Yii::app()->input->get('page');	
		$page = !empty($page)?$page:$page_redirect;
		
		$model = AR_media::model()->find('merchant_id=:merchant_id AND id=:id', 
		array(':merchant_id'=>$merchant_id, ':id'=>$id ));
				
		if($model){
			$model->delete(); 
			Yii::app()->user->setFlash('success', t("Succesful") );					
			$this->redirect(array($page));			
		} else $this->render("error");
	}
	
	public function actionmedia_library()
	{
		$this->pageTitle=t("Media Library");
		$action_name='media_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/media_delete");
		
		/*ScriptUtility::registerScript(array(
		  "var action_name='$action_name';",
		  "var delete_link='$delete_link';",
		),'action_name');
		
		
		$this->render("//tpl/lazy_list",array(
		  'link'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/media_create")
		));	*/
				
		$upload_path = CMedia::merchantFolder();
		
		$this->render("media_gallery",array(
		  'upload_path'=>$upload_path
		));
	}
	
    public function actionmedia_create()
	{
		$this->pageTitle=t("Add Media");
		CommonUtility::setMenuActive('.merchant_images','.images_gallery');		
		
		$upload_ajaxurl = Yii::app()->createUrl("/upload");
		$upload_params = array(
		  Yii::app()->request->csrfTokenName=>Yii::app()->request->csrfToken,		  
		);		
		$upload_params = json_encode($upload_params);
		
		ScriptUtility::registerScript(array(
		  "var upload_ajaxurl='$upload_ajaxurl';",		  
		  "var upload_params='$upload_params';",		  
		),'upload_ajaxurl');
				
		
		$this->render("/merchant/gallery_create",array(
		  'links'=>array(
	            t("All Media")=>array(Yii::app()->controller->id.'/media_library'),    	            
		    ),	    	
		   'done'=>Yii::app()->CreateUrl(Yii::app()->controller->id."/media_library")
		));
	}		
	
	public function actionmedia_delete()
	{
		$this->actionmedia_remove(Yii::app()->controller->id."/media_library");
	}
	
	
}
/*end class*/