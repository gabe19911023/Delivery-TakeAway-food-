<?php
class PageController extends SiteCommon
{
	public function beforeAction($action)
	{						
		return true;
	}
	
	public function actionindex()
	{
		$pathInfo = Yii::app()->request->getPathInfo();		
		$matches = explode('/', $pathInfo);
		if(is_array($matches) && count($matches)>=1){
			$slug_name = isset($matches[0])?$matches[0]:''; 						
			try {
			   $model = PPages::pageDetailsSlug($slug_name , Yii::app()->language );			   
			   $this->render('//store/page',array(
			    'model'=>$model,
				'responsive'=>AttributesTools::FrontCarouselResponsiveSettings('full'), 
			   ));
			   return ;
			} catch (Exception $e) {
			    //$this->msg = t($e->getMessage());
			}			
		} 		
		$this->render("//store/404-page");		
	}
			
}
/*end class*/