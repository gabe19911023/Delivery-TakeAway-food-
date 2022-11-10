<?php
class MenuController extends SiteCommon
{
	public function beforeAction($action)
	{						
		return true;
	}
			
	public function actionMenu()
	{
		$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
		if(empty($local_id)){
			$address_uuid = '';
			$place_id = 'ChIJA9KNRIL-1BIRb15jJFz1LOI';
			
			$resp = CMaps::locationDetails($place_id,'');
						
			$resp_place_id = $resp['place_id'];
			$set_place_id = !empty($resp_place_id)?$resp_place_id:$place_id;
									
			CommonUtility::WriteCookie( Yii::app()->params->local_id , $set_place_id );	
			$address_uuid = CCheckout::saveDeliveryAddress($place_id , Yii::app()->user->id , $resp);
		}	
				
		try {
			
			$pathInfo = Yii::app()->request->getPathInfo();		
			$matches = explode('/', $pathInfo);			
			if(is_array($matches) && count($matches)>=1){
				$slug_name = isset($matches[0])?$matches[0]:''; 
				Yii::app()->user->setState('rest_slug_name', $slug_name); 
							
				$data = CMerchantListingV1::getMerchantInfo($slug_name,Yii::app()->language);				
				$merchant_id = intval($data['merchant_id']);
				$merchant_uuid = trim($data['merchant_uuid']);
				$gallery = CMerchantListingV1::getGallery($merchant_id);						
				$opening_hours = CMerchantListingV1::openingHours($merchant_id);
										
				$static_maps=''; $map_direction='';
				if($data){			  					
				   $static_maps = CMerchantListingV1::staticMapLocation(
				     Yii::app()->params['map_credentials'],
				     $data['latitude'],$data['lontitude'],
				     '500x200',websiteDomain().Yii::app()->theme->baseUrl."/assets/images/marker2@2x.png"
				   );		
				   $map_direction = CMerchantListingV1::mapDirection(Yii::app()->params['map_credentials'],
				     $data['latitude'],$data['lontitude']
				   );	   					  
				}
								
				
				$payload = array(
				  'items','subtotal','distance_local','merchant_info','items_count'
				);			
									
				ScriptUtility::registerScript(array(
				  "var merchant_id='".CJavaScript::quote($merchant_id)."';",		
				  "var merchant_uuid='".CJavaScript::quote($merchant_uuid)."';",		
				  "var payload='".CJavaScript::quote(json_encode($payload))."';",
				),'merchant_id');
				
				$checkout_link = Yii::app()->createUrl("account/login?redirect=". Yii::app()->createAbsoluteUrl("/account/checkout") );
				if(!Yii::app()->user->isGuest){
					$checkout_link = Yii::app()->createUrl("/account/checkout");
				}

				if($data['has_header']){
					Yii::app()->clientScript->registerCss('headerCSS', '
						.top-merchant-details{
							background: url("'.$data['url_header'].'") no-repeat center center #fedc79;
							background-size:cover;
						}
					');
				}
																					
				if($data){		    												
			    	$this->render('//store/menu',array(
			    	  'data'=>$data,
			    	  'gallery'=>$gallery,
			    	  'opening_hours'=>$opening_hours,
			    	  'static_maps'=>$static_maps,
			    	  'map_direction'=>$map_direction,
			    	  'checkout_link'=>$checkout_link			    	  
			    	));
			    }
			} else $this->render("//store/404-page");		
			
		} catch (Exception $e) {
			$this->render("//store/404-page");		
		}
	}
	
}
/*end class*/