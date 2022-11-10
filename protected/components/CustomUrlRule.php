<?php
class CustomUrlRule extends CBaseUrlRule
{
	public $connectionID = 'db';
	 	
    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {    	    	
    	$matches = explode('/', $pathInfo);    	
        //if (preg_match('%^(\w+)(/(\w+))?$%', $pathInfo, $matches))
        if(is_array($matches) && count($matches)>=1)
        {
            // check $matches[1] and $matches[3] to see
            // if they match a manufacturer and a model in the database
            // If so, set $_GET['manufacturer'] and/or $_GET['model']
            // and return 'car/index'      
                                                
            $slug_name = isset($matches[0])?$matches[0]:'';                
            
            $dependency = CCacheData::dependency();
            $model = AR_merchant::model()->cache(Yii::app()->params->cache, $dependency)->find('restaurant_slug=:restaurant_slug AND status=:status', 
		    array(':restaurant_slug'=>$slug_name, ':status'=>'active' )); 
		    if($model){		       
               return 'menu/menu';
		    }
		    		    
		    $model_page = AR_pages::model()->cache(Yii::app()->params->cache, $dependency)->find('slug=:slug AND status=:status', 
		    array(':slug'=>trim($slug_name), ':status'=>'publish' )); 
		    if($model_page){
		    	return 'page/index';
		    }
        } 
        return false;  // this rule does not apply
    }
    
    public function createUrl($manager,$route,$params,$ampersand)
    {    	    	    	
        if ($route==='menu/menu')
        {        	        	
            if (isset($params['menu'], $params['model']))
                return $params['menu'] . '/' . $params['model'];
            else if (isset($params['menu']))
                return $params['menu'];
        }
        return false;  // this rule does not apply
    }
        
}
/*end class*/