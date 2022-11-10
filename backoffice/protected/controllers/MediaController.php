<?php
class MediaController extends CommonController
{
		
	public function beforeAction($action)
	{					
		return true;
	}
	
	public function actionlibrary()
	{
		$this->pageTitle=t("Media Library");
		$action_name='media_list';
		$delete_link = Yii::app()->CreateUrl(Yii::app()->controller->id."/media_delete");
					
		$upload_path = CMedia::adminFolder();
		
		$this->render("media_gallery",array(			  
		  'upload_path'=>$upload_path
		));
	}	
		
}
/*end class*/