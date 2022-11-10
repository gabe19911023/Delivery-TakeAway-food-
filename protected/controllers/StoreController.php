<?php
class StoreController extends SiteCommon
{
	public function beforeAction($action)
	{						
		return true;
	}
	
	public function actionIndex()
	{							
	    $local_id = CommonUtility::getCookie(Yii::app()->params->local_id);		
		$setttings = Yii::app()->params['settings'];
		$allow_return_home = isset($setttings['allow_return_home'])?$setttings['allow_return_home']:false;
		$allow_return_home = $allow_return_home==1?true:false;

	    if(!empty($local_id) && $allow_return_home==false){			 
	    	 $this->redirect(array('/store/restaurants'));
	    	 Yii::app()->end();
	    }		
		$this->render("index",[
			'responsive'=>AttributesTools::FrontCarouselResponsiveSettings('full'),   			
		]);
	}	
	
	public function actionrestaurantsOLD()
	{				
						
		$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
		$local_info = CMerchantListing::getLocalID($local_id);				

		if(!$local_info){
			 $this->redirect(array('/'));
		}
				
		$services = Yii::app()->input->get('services'); 		
		$unit = Yii::app()->params['settings']['home_search_unit_type'];
		$popular = array();
		
		try {
			$results = CMerchantListing::countSearchByCoordinates(
			  $local_info->latitude,
			  $local_info->longitude,
			  $unit
			);
		} catch (Exception $e) {			
            $results = 0;
        }

        $list_limit = Yii::app()->params->list_limit;
        
        ScriptUtility::registerScript(array(
		  "var list_limit='$list_limit';",		  
		),'list_limit');	
		
		if(is_numeric($results) && $results>0){

			try {
			   $popular = CMerchantListing::getFeaturedLocation('new',$unit, date("c"), 0,
			   $list_limit,Yii::app()->language);
			} catch (Exception $e) {			
	           $popular = array();
	        }
					
			$this->render('restaurants-column',array(
			  'local_info'=>$local_info,			  			 
			  'local_id'=>$local_id,
			  'services'=>$services,
			  'popular'=>$popular
			));
		} else $this->render('restaurants-noresults',array(
		   'local_info'=>$local_info,			  			 
		   'local_id'=>$local_id			  
		));
		
		//$this->render('restaurants');
		//$this->render('restaurants-noresults');
	}
	
	public function actionRestaurants()
	{
		$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
		if(empty($local_id)){
			 $this->redirect(array('/'));
			 Yii::app()->end();
		}
		AssetsFrontBundle::includeMaps();
		$this->render('feed',array(
			'responsive'=>AttributesTools::FrontCarouselResponsiveSettings('full'), 
			'tabs_suggestion'=>AttributesTools::suggestionTabs()
		));
	}
	
	public function actionFeed()
	{
		$this->render('feed');
	}
	
	public function actionmenu()
	{
		$this->render('menu');
		//$this->render('menu-1');
	}
	
	public function actioncheckout()
	{
		$this->render('checkout');
		//$this->render('checkout-1');
	}	
	
	public function actionreceipt()
	{
		$this->render('receipt');
	}	
		
	public function actionoffers()
	{
		$this->render('offers');
	}	
	
	public function actionpagenotfound()
	{
		$this->render('404-page');
	}	
	
	public function actionregister()
	{
		$this->render('register');
	}
		
}
/*end class*/