<?php
require 'intervention/vendor/autoload.php';
use Intervention\Image\ImageManager;
                    
class ApiController extends SiteCommon
{
	public function beforeAction($action)
	{								
		$method = Yii::app()->getRequest()->getRequestType();
		if($method=="PUT"){
			$this->data = Yii::app()->input->xssClean(json_decode(file_get_contents('php://input'), true));
		} else $this->data = Yii::app()->input->xssClean($_POST);				
		return true;
	}
	
	public function actiongetlocation_autocomplete()
	{			
		// header('Content-type: application/json');
		// echo '{"code":1,"msg":"ok","details":{"data":[{"id":"ChIJA9KNRIL-1BIRb15jJFz1LOI","provider":"google.maps","addressLine1":"Italy","addressLine2":"","place_type":"country,political,geocode","description":"Italy"},{"id":"ChIJ94iv4IKB0IkRdM5upWgn5Qo","provider":"google.maps","addressLine1":"Ithaca","addressLine2":"NY, USA","place_type":"locality,political,geocode","description":"Ithaca, NY, USA"},{"id":"ChIJgcJtIYniToYRzkD1aq016_w","provider":"google.maps","addressLine1":"Italy","addressLine2":"TX, USA","place_type":"locality,political,geocode","description":"Italy, TX, USA"},{"id":"ChIJ1ZxeBy6CRo4RQj1cRMgc0m0","provider":"google.maps","addressLine1":"Itag\u00fc\u00ed","addressLine2":"Antioquia, Colombia","place_type":"locality,political,geocode","description":"Itag\u00fc\u00ed, Antioquia, Colombia"},{"id":"ChIJXR2GCXi_vJURhj2d_Vl4yWs","provider":"google.maps","addressLine1":"Ituzaing\u00f3","addressLine2":"Buenos Aires Province, Argentina","place_type":"locality,political,geocode","description":"Ituzaing\u00f3, Buenos Aires Province, Argentina"}]}}';	
		// Yii::app()->end();		
		try {
					   
		   $q = isset($this->data['q'])?$this->data['q']:'';
		   
		   if(!isset(Yii::app()->params['settings']['map_provider'])){
		   	   $this->msg = t("No default map provider, check your settings.");
		   	   $this->responseJson();
		   }
		   
		   MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];		   
		   MapSdk::setKeys(array(
		     'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
		     'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
		   ));
		   		   
		   if ( $country_params = AttributesTools::getSetSpecificCountry()){
		   	   MapSdk::setMapParameters(array(
		        'country'=>$country_params
		       ));
		   }		   
		     		  
		   $resp = MapSdk::findPlace($q);		   
		   $this->code =1; $this->msg = "ok";
		   $this->details = array(
		     'data'=>$resp
		   );		   
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
		
	public function actiongetLocationDetails()
	{		
		// echo '{"code":1,"msg":"ok","details":{"data":{"address":{"address1":"Italy","address2":"","country":"Italy","country_code":"IT","postal_code":"","formatted_address":"Italy"},"latitude":"41.87194","longitude":"12.56738","place_id":"ChIJA9KNRIL-1BIRb15jJFz1LOI","reference":"ChIJA9KNRIL-1BIRb15jJFz1LOI"},"cart_uuid":""}}';
		// exit;		
		try {
			$address_uuid = '';
			$place_id = isset($this->data['id'])?trim($this->data['id']):'';			
			$autosaved_addres = isset($this->data['autosaved_addres'])?trim($this->data['autosaved_addres']):'';
			$auto_generate_uuid = isset($this->data['auto_generate_uuid'])?($this->data['auto_generate_uuid']):'';
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			
			$resp = CMaps::locationDetails($place_id,'');
						
			$resp_place_id = $resp['place_id'];
			$set_place_id = !empty($resp_place_id)?$resp_place_id:$place_id;
									
			CommonUtility::WriteCookie( Yii::app()->params->local_id , $set_place_id );		
			
			if(!Yii::app()->user->isGuest){				
				if($autosaved_addres===true || $autosaved_addres==="true"){
				  $address_uuid = CCheckout::saveDeliveryAddress($place_id , Yii::app()->user->id , $resp);
				  $resp['address_uuid']=$address_uuid;				  
				}
			} 
			
			if($auto_generate_uuid===true || $auto_generate_uuid==="true"){
				$cart_uuid = !empty($cart_uuid)?$cart_uuid:CommonUtility::generateUIID();
				$trans_type = CServices::getSetService($cart_uuid);				
				CCart::savedAttributes($cart_uuid,Yii::app()->params->local_transtype,$trans_type);	
				$when = CCheckout::getWhenDeliver($cart_uuid);
		        CCart::savedAttributes($cart_uuid,'whento_deliver',$when);			  
		        CommonUtility::WriteCookie( "cart_uuid_local" ,$cart_uuid);		
			}
			
			$this->code =1; $this->msg = "ok";
			$this->details = array(
			  'data'=>$resp,		
			  'cart_uuid'=>$cart_uuid,
			);
							
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
		
	public function actionupdateSavedLocation()
	{			
		try {
			$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
			$local_info = CMaps::locationDetails($local_id,'');
			$reference_type = MapSdk::$map_provider;
			$place_id = $local_info['place_id'];
			$model = AR_map_places::model()->find('reference_type=:reference_type AND reference_id=:reference_id', 
		    array(':reference_type'=>$reference_type, ':reference_id'=>$place_id )); 
		    $model->street_number = $this->data['street_number'];
		    $model->address = $this->data['address'];
		    $model->postal_code = $this->data['postal_code'];
		    $model->city = $this->data['city'];
		    $model->save();
		    $local_info = CMaps::locationDetails($local_id,'');			
			$this->code =1; $this->msg = "ok";
			$this->details = array(
			  'data'=>$local_info
			);
							
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionreverseGeocoding()
	{		
		$lat = isset($this->data['lat'])?$this->data['lat']:'';
		$lng = isset($this->data['lng'])?$this->data['lng']:'';
		$next_steps = isset($this->data['next_steps'])?$this->data['next_steps']:'';
		
		$services = isset($this->data['services'])?$this->data['services']:'';		  
	    if(!empty($services)){
	   	  $services = substr($services,0,-1);
	    } else $services="all";
		
		try {
			
		   MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];		   
		   MapSdk::setKeys(array(
		     'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
		     'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
		   ));
		   
		   if(MapSdk::$map_provider=="mapbox"){
			   MapSdk::setMapParameters(array(
			    'types'=>"poi",
			    'limit'=>1
			   ));
		   }
		   
		   $resp = MapSdk::reverseGeocoding($lat,$lng);
		   
		   $this->code =1; $this->msg = "ok";
		   $this->details = array(
		     'next_action'=>$next_steps,		     
		     'services'=>$services,
		     'provider'=>MapSdk::$map_provider,
		     'data'=>$resp
		   );		   		   
		   
		} catch (Exception $e) {		   
		   $this->msg = t($e->getMessage());	
		   $this->details = array(
		     'next_action'=>"show_error_msg"		     
		   );	   
		}
		$this->jsonResponse();
	}	
	
	public function actionCuisineList()
	{
		try {			
		    $data_cuisine = CCuisine::getList( Yii::app()->language );
		    $this->code = 1;
		    $this->msg = "OK";
		    $this->details = array(
		      'data_cuisine'=>$data_cuisine
		    );
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actionsearchAttributes()
	{
		$data = array(
		  'price_range'=>AttributesTools::SortPrinceRange(),
		  'sort_by'=>AttributesTools::SortMerchant()
		);
		$this->code = 1;
		$this->msg = "OK";
		$this->details = $data;
		$this->responseJson();
	}
	
	public function actiongetFeedV1()
	{
		try {
		  
		  $transaction_type=''; $whento_deliver='';
		  $page  = isset($this->data['page'])?(integer)$this->data['page']:0;
		  $filters  = isset($this->data['filters'])?(array)$this->data['filters']:array();
		  $cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		  
		  $local_id = CommonUtility::getCookie(Yii::app()->params->local_id);		  
		  $local_info = CMaps::locationDetails($local_id,'');		  
		  
		  $offset = 0; $show_next_page = false;
		  		  		  
		  if(empty($filters['transaction_type'])){
		  	 $transaction_type = CServices::getSetService($cart_uuid);
		  	 $filters['transaction_type'] = $transaction_type;
		  } else $transaction_type  = $filters['transaction_type'];

		  $todays_date = date("Y-m-d H:i"); $set_date = '';
		  
		  /*CHECK IF TIME IS SCHEDULE*/			  
		  $whento_deliver = isset($filters['whento_deliver'])?$filters['whento_deliver']:"now";
		  if($whento_deliver=="schedule"){
		  	 $delivery_date = isset($filters['delivery_date'])?$filters['delivery_date']:'';
		  	 $delivery_time = isset($filters['delivery_time'])?$filters['delivery_time']:'';
		  	 if(!empty($delivery_date) && !empty($delivery_time) ){
		  	 	 $set_date = $delivery_date." ".$delivery_time;		  	 	 
		  	 	 $todays_date = !empty($set_date)?date("Y-m-d H:i" , strtotime($set_date)):$todays_date;	
		  	 }
		  }
		  		  		  		
		  $day_of_week = strtolower(date("N",strtotime($todays_date)));
		  $filter = array(
		    'lat'=>isset($local_info['latitude'])?$local_info['latitude']:'',
		    'lng'=>isset($local_info['longitude'])?$local_info['longitude']:'',
		    'unit'=>Yii::app()->params['settings']['home_search_unit_type'],
		    'limit'=>intval(Yii::app()->params->list_limit),		    
		    'day_of_week'=>$day_of_week>6?1:$day_of_week,
		    'today_now'=>strtolower(date("l",strtotime($todays_date))),
		    'time_now'=>date("H:i",strtotime($todays_date)),
		    'date_now'=>$todays_date,
		    'client_id'=>!Yii::app()->user->isGuest?Yii::app()->user->id:0,
		    'filters'=>$filters,
		  );
		  
		  
		  $count = CMerchantListingV1::preSearch($filter);
		  $total_message = $count<=1? t("{{count}} store",array('{{count}}'=>$count)) : t("{{count}} stores",array('{{count}}'=>$count)) ;
		  
		  $pages = new CPagination($count);
		  $pages->pageSize = intval(Yii::app()->params->list_limit);
		  $pages->setCurrentPage($page);
		  $offset = $pages->getOffset();	
		  $page_count = $pages->getPageCount();	
		  
		  if($page_count > ($page+1) ){
			 $show_next_page = true;
		  }
		  
		  $filter['offset'] = intval($offset);
		  
		  $data = CMerchantListingV1::Search($filter);
		  $services = CMerchantListingV1::services( $filter );	
		  $estimation = CMerchantListingV1::estimation( $filter );			
		  		  
		  $this->code = 1;
		  $this->msg = "OK";
		  $this->details = array(		      
		    'total_message'=>$total_message,
		    'transaction_type'=>$transaction_type,
		    'show_next_page'=>$show_next_page,
		    'page'=>intval($page)+1,
	        'data'=>$data,
	        'services'=>$services,
	        'estimation'=>$estimation
		  );		  
		} catch (Exception $e) {
			$this->msg[] = $e->getMessage();			
		}
		$this->responseJson();
	}
	
	public function actionpauseReasonList()
	{
		try {
		   
			$model = AR_merchant_meta::model()->findAll("meta_name=:meta_name AND meta_value<>''",array(
			 ':meta_name'=>'pause_reason'
			));
			if($model){
				$data = array();
				foreach ($model as $items) {
					$data[$items->merchant_id] = $items->meta_value;
				}
				$this->code = 1;
				$this->msg = t("ok");
				$this->details = $data;
			} else $this->msg = t("No results");
			
		} catch (Exception $e) {
			$this->msg = $e->getMessage();			
		}
		$this->responseJson();
	}
	
	public function actiongetCategory()
	{		
	    $merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:'';
	    	    
	    try {
		   $category = CMerchantMenu::getCategory($merchant_id,Yii::app()->language);				   
		   $data = array(
		     'category'=>$category,		     
		   );		   		   
		   $this->code = 1; $this->msg = "OK";
		   $this->details = array(		     		    
		     'data'=>$data
		   );		   		   
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}		
		$this->responseJson();
	}
	
	public function actionservicesList()
	{		
		$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:'';
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		
		try {
			
			$merchant = CMerchants::get($merchant_id);			
			$data = CCheckout::getMerchantTransactionList($merchant_id,Yii::app()->language);
			$transaction = CCart::cartTransaction($cart_uuid,Yii::app()->params->local_transtype,$merchant_id);
			
			$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
			$local_info = CMaps::locationDetails($local_id,'');
						
			$filter = array(
			    'merchant_id'=>$merchant_id,
			    'lat'=>isset($local_info['latitude'])?$local_info['latitude']:'',
			    'lng'=>isset($local_info['longitude'])?$local_info['longitude']:'',
			    'unit'=> !empty($merchant->distance_unit)?$merchant->distance_unit:Yii::app()->params['settings']['home_search_unit_type'],
			    'shipping_type'=>"standard"
		    );				    
		    
		    $estimation  = CMerchantListingV1::estimationMerchant($filter);
		    $charge_type = OptionsTools::find(array('merchant_delivery_charges_type'),$merchant_id);
		    $charge_type = isset($charge_type['merchant_delivery_charges_type'])?$charge_type['merchant_delivery_charges_type']:'';
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$data,
			  'transaction'=>$transaction,
			  'charge_type'=>$charge_type,
			  'estimation'=>$estimation,
			);						
		} catch (Exception $e) {
		   // $this->msg = t($e->getMessage());	
		   	$merchant = CMerchants::get($merchant_id);			
			$data = CCheckout::getMerchantTransactionList($merchant_id,Yii::app()->language);	   
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$data,
			  'transaction'=>'pickup',
			  'charge_type'=>'fixed',
			  'estimation'=>false,
			);		   
		}		
		$this->responseJson();
	}
	
	public function actionupdateService()
	{		
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';		
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';
		
		if(!empty($cart_uuid) && !empty($transaction_type)){
		   CCart::savedAttributes($cart_uuid,Yii::app()->params->local_transtype,$transaction_type);	
		}
		
		$this->code = 1;
		$this->msg = "OK";
		$this->jsonResponse();
	}
		
	public function actiongeStoreMenu()
	{
				
		$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:0;
		$image_use = isset($this->data['image_use'])?$this->data['image_use']:'';
				
		try {
		   $category = CMerchantMenu::getCategory($merchant_id,Yii::app()->language);		
		   $items = CMerchantMenu::getMenu($merchant_id,Yii::app()->language);		   		   
		   $data = array(
		     'category'=>$category,
		     'items'=>$items
		   );		   				   
		   $this->code = 1; $this->msg = "OK";
		   $this->details = array(		     		      
		     'merchant_id'=>$merchant_id,
		     'data'=>$data
		   );		   
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}		
		$this->responseJson();
	}	
		
	public function actiongetMenuItem()
	{		
				
		$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:'';
		$item_uuid = isset($this->data['item_uuid'])?trim($this->data['item_uuid']):'';
		$cat_id = isset($this->data['cat_id'])?(integer)$this->data['cat_id']:0;
				
		try {
			$items = CMerchantMenu::getMenuItem($merchant_id,$cat_id,$item_uuid,Yii::app()->language);
			$addons = CMerchantMenu::getItemAddonCategory($merchant_id,$item_uuid,Yii::app()->language);
			$addon_items = CMerchantMenu::getAddonItems($merchant_id,$item_uuid,Yii::app()->language);	
			$meta = CMerchantMenu::getItemMeta($merchant_id,$item_uuid);
			$meta_details = CMerchantMenu::getMeta($merchant_id,$item_uuid,Yii::app()->language);	
							
			$data = array(
			  'items'=>$items,
			  'addons'=>$addons,
			  'addon_items'=>$addon_items,
			  'meta'=>$meta,
			  'meta_details'=>$meta_details
			);
			
			$this->code = 1; $this->msg = "ok";
		    $this->details = array(
		      'next_action'=>"show_item_details",
		      'sold_out_options'=>AttributesTools::soldOutOptions(),
		      'data'=>$data
		    );		    		    
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $this->details = array(
		      'data'=>array()
		    );		    	   
		}		
		$this->responseJson();
	}
	
	public function actionpriceFormat()
	{		
		$price = isset($this->data['price'])?(float)$this->data['price']:'';
		$target = isset($this->data['target'])?trim($this->data['target']):'';
		
		$this->code = 1; $this->msg = "ok";
	    $this->details = array(
	      'next_action'=>"fill_price_format",
	      'data'=>array(
	        'target'=>$target,
	        'pretty_price'=>Price_Formatter::formatNumber($price)
	      )
	    );		    	    
		    
		$this->jsonResponse();
	}

	public function actionaddCartItems()
	{							
		$uuid = CommonUtility::createUUID("{{cart}}",'cart_uuid');
		$cart_row = CommonUtility::generateUIID();
		
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';		
		$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';		
		$cart_uuid = !empty($cart_uuid)?$cart_uuid:$uuid;
		$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:'';
		$cat_id = isset($this->data['cat_id'])?(integer)$this->data['cat_id']:'';
		$item_token = isset($this->data['item_token'])?$this->data['item_token']:'';
		$item_size_id = isset($this->data['item_size_id'])?(integer)$this->data['item_size_id']:0;
		$item_qty = isset($this->data['item_qty'])?(integer)$this->data['item_qty']:0;
		$special_instructions = isset($this->data['special_instructions'])?$this->data['special_instructions']:'';
		$if_sold_out = isset($this->data['if_sold_out'])?$this->data['if_sold_out']:'';
		$inline_qty = isset($this->data['inline_qty'])?(integer)$this->data['inline_qty']:0;
		
		
		$addons = array();
		$item_addons = isset($this->data['item_addons'])?$this->data['item_addons']:'';
		if(is_array($item_addons) && count($item_addons)>=1){
			foreach ($item_addons as $val) {				
				$multi_option = isset($val['multi_option'])?$val['multi_option']:'';
				$subcat_id = isset($val['subcat_id'])?(integer)$val['subcat_id']:0;
				$sub_items = isset($val['sub_items'])?$val['sub_items']:'';
				$sub_items_checked = isset($val['sub_items_checked'])?(integer)$val['sub_items_checked']:0;				
				if($multi_option=="one" && $sub_items_checked>0){
					$addons[] = array(
					  'cart_row'=>$cart_row,
					  'cart_uuid'=>$cart_uuid,
					  'subcat_id'=>$subcat_id,
					  'sub_item_id'=>$sub_items_checked,					 
					  'qty'=>1,
					  'multi_option'=>$multi_option,
					);
				} else {
					foreach ($sub_items as $sub_items_val) {
						if($sub_items_val['checked']==1){							
							$addons[] = array(
							  'cart_row'=>$cart_row,
							  'cart_uuid'=>$cart_uuid,
							  'subcat_id'=>$subcat_id,
							  'sub_item_id'=>isset($sub_items_val['sub_item_id'])?(integer)$sub_items_val['sub_item_id']:0,							  
							  'qty'=>isset($sub_items_val['qty'])?(integer)$sub_items_val['qty']:0,
							  'multi_option'=>$multi_option,
							);
						}
					}
				}
			}
		}
		
		
		$attributes = array();
		$meta = isset($this->data['meta'])?$this->data['meta']:'';
		if(is_array($meta) && count($meta)>=1){
			foreach ($meta as $meta_name=>$metaval) {				
				if($meta_name!="dish"){
					foreach ($metaval as $val) {
						if($val['checked']>0){	
							$attributes[]=array(
							  'cart_row'=>$cart_row,
							  'cart_uuid'=>$cart_uuid,
							  'meta_name'=>$meta_name,
							  'meta_id'=>$val['meta_id']
							);
						}
					}
				}
			}
		}
		
		$items = array(
		  'merchant_id'=>$merchant_id,
		  'cart_row'=>$cart_row,
		  'cart_uuid'=>$cart_uuid,
		  'cat_id'=>$cat_id,
		  'item_token'=>$item_token,
		  'item_size_id'=>$item_size_id,
		  'qty'=>$item_qty,
		  'special_instructions'=>$special_instructions,
		  'if_sold_out'=>$if_sold_out,
		  'addons'=>$addons,
		  'attributes'=>$attributes,
		  'inline_qty'=>$inline_qty
		);		
				
		
		try {
			
		  CCart::add($items);
		  		  		  		  
		  CCart::savedAttributes($cart_uuid,Yii::app()->params->local_transtype,$transaction_type);
		  CommonUtility::WriteCookie( "cart_uuid_local" ,$cart_uuid);	
		  		  
		  /*SAVE DELIVERY DETAILS*/
		  if(!CCart::getAttributes($cart_uuid,'whento_deliver')){		     
		     $whento_deliver = isset($this->data['whento_deliver'])?$this->data['whento_deliver']:'now';
		     CCart::savedAttributes($cart_uuid,'whento_deliver',$whento_deliver);
		     if($whento_deliver=="schedule"){
		        $delivery_date = isset($this->data['delivery_date'])?$this->data['delivery_date']:'';
		        $delivery_time_raw = isset($this->data['delivery_time_raw'])?$this->data['delivery_time_raw']:'';
		        if(!empty($delivery_date)){
		        	CCart::savedAttributes($cart_uuid,'delivery_date',$delivery_date);
		        }
		        if(!empty($delivery_time_raw)){
		        	CCart::savedAttributes($cart_uuid,'delivery_time',json_encode($delivery_time_raw));
		        }
		     }
		  }
		  		  		  		
		  $this->code = 1 ; $this->msg = "OK";			
	      $this->details = array(
	        'cart_uuid'=>$cart_uuid
	      );		 
		    
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $this->details = array(
		      'data'=>array()
		    );		    	   
		}		
		$this->jsonResponse();
	}
	
	public function actiongetCart()
	{			
		$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
		$cart_uuid = isset($this->data['cart_uuid'])?trim($this->data['cart_uuid']):'';
		$payload = isset($this->data['payload'])?$this->data['payload']:'';
		
		$distance = 0; 
		$unit = isset(Yii::app()->params['settings']['home_search_unit_type'])?Yii::app()->params['settings']['home_search_unit_type']:'mi';
		$error = array(); 
		$minimum_order = 0; 
		$maximum_order=0;
		$merchant_info = array(); 
		$delivery_fee = 0; 
		$distance_covered=0;
		$merchant_lat = ''; 
		$merchant_lng=''; 
		$out_of_range = false;
		$address_component = array();
		
		try {
												
			require_once 'get-cart.php';			
						
			$this->code = 1; $this->msg = "ok";
		    $this->details = array(			      
		      'cart_uuid'=>$cart_uuid,
		      'payload'=>$payload,
		      'error'=>$error,
		      'checkout_data'=>$checkout_data,
		      'out_of_range'=>$out_of_range,
		      'address_component'=>$address_component,
		      'go_checkout'=>$go_checkout,
		      'items_count'=>$items_count,
		      'data'=>$data,		      
		    );				    
		} catch (Exception $e) {
		   $error[] = t($e->getMessage());			   		   
		}						
		$this->responseJson();
	}
	
	public function actionremoveCartItem()
	{		
		$cart_uuid = isset($this->data['cart_uuid'])?trim($this->data['cart_uuid']):'';		
		$row = isset($this->data['row'])?trim($this->data['row']):'';		
		
		try {
			
			CCart::remove($cart_uuid,$row);
			$this->code = 1; $this->msg = "Ok";
			$this->details = array(
		      'data'=>array()
		    );		    	   			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $this->details = array(
		      'data'=>array()
		    );		    	   
		}		
		$this->jsonResponse();
	}
	
	public function actionupdateCartItems()
	{		
		$cart_uuid = isset($this->data['cart_uuid'])?trim($this->data['cart_uuid']):'';		
		$cart_row = isset($this->data['cart_row'])?trim($this->data['cart_row']):'';		
		$item_qty = isset($this->data['item_qty'])?(integer)trim($this->data['item_qty']):0;		
		try {
			
			CCart::update($cart_uuid,$cart_row,$item_qty);
			$this->code = 1; $this->msg = "Ok";
			$this->details = array(
		      'data'=>array()
		    );		    	   			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $this->details = array(
		      'data'=>array()
		    );		    	   
		}		
		$this->jsonResponse();
	}
	
	public function actionclearCart()
	{				
		$cart_uuid = isset($this->data['cart_uuid'])?trim($this->data['cart_uuid']):'';		
		try {
			
			CCart::clear($cart_uuid);
			$this->code = 1; $this->msg = "Ok";
			$this->details = array(
		      'data'=>array()
		    );		    	   			
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		   $this->details = array(
		      'data'=>array()
		    );		    	   
		}		
		$this->jsonResponse();
	}
	
	public function actiongetReview()
	{		
		$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:'';
		$page = isset($this->data['page'])?(integer)$this->data['page']:0;
		
		try {			
			
			$offset = 0; $show_next_page = false;
		    $limit = Yii::app()->params->list_limit;
		
		    $total_rows = CReviews::reviewsCount($merchant_id);
		   
		   	$pages = new CPagination($total_rows);
			$pages->pageSize = $limit;
			$pages->setCurrentPage($page);
			$offset = $pages->getOffset();	
			$page_count = $pages->getPageCount();
								
		   if($page_count > ($page+1) ){
				$show_next_page = true;
		   }
		   		   		 
		   $data = CReviews::reviews($merchant_id,$offset,$limit);		   
		   $this->code = 1;
		   $this->msg = "OK";
		   $this->details = array(
		     'show_next_page'=>$show_next_page,
		     'page'=>intval($page)+1,
		     'data'=>$data
		   );		   		   		   
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		   
		}		
		$this->responseJson();
	}
	
	public function actionuploadReview()
	{
		$upload_uuid = CommonUtility::generateUIID();
		$merchant_id = isset($this->data['merchant_id'])?(integer)$this->data['merchant_id']:0;		
		$allowed_extension = explode(",",  Yii::app()->params['upload_type']);
		$maxsize = (integer) Yii::app()->params['upload_size'] ;
					
		if (!empty($_FILES)) {
			
			$title = $_FILES['file']['name'];   
			$size = (integer)$_FILES['file']['size'];   
			$filetype = $_FILES['file']['type'];   								
			
			if(isset($_FILES['file']['name'])){
			   $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			} else $extension = strtolower(substr($title,-3,3));
			
			if(!in_array($extension,$allowed_extension)){			
				$this->msg = t("Invalid file extension");
				$this->jsonResponse();
			}
			if($size>$maxsize){
				$this->msg = t("Invalid file size");
				$this->jsonResponse();
			}
			
			$upload_path = "upload/reviews";
			$tempFile = $_FILES['file']['tmp_name'];   								
			$upload_uuid = CommonUtility::createUUID("{{media_files}}",'upload_uuid');
			$filename = $upload_uuid.".$extension";						
			$path = CommonUtility::uploadDestination($upload_path)."/".$filename;						
			
			$image_set_width = isset(Yii::app()->params['settings']['review_image_resize_width']) ? intval(Yii::app()->params['settings']['review_image_resize_width']) : 0;
			$image_set_width = $image_set_width<=0?300:$image_set_width;
						
			$image_driver = !empty(Yii::app()->params['settings']['image_driver'])?Yii::app()->params['settings']['image_driver']:Yii::app()->params->image['driver'];			
			$manager = new ImageManager(array('driver' => $image_driver ));								
			$image = $manager->make($tempFile);
			$image_width = $manager->make($tempFile)->width();
						
			if($image_width>$image_set_width){
				$image->resize(null, $image_set_width, function ($constraint) {
				    $constraint->aspectRatio();
				});
				$image->save($path);
			} else {
				$image->save($path,60);
			}				
			
			//move_uploaded_file($tempFile,$path);
			
			$media = new AR_media;		
			$media->merchant_id = intval($merchant_id);
			$media->title = $title;			
			$media->path = $upload_path;
			$media->filename = $filename;
			$media->size = $size;
			$media->media_type = $filetype;						
			$media->meta_name = AttributesTools::metaReview();		
			$media->upload_uuid = $upload_uuid;
			$media->save();
			
			$this->code = 1; $this->msg = "OK";			
			$this->details = array(			   			   
			   'url_image'=>CMedia::getImage($filename,$upload_path),
			   'filename'=>$media->filename,
			   'id'=>$upload_uuid			   
			);			
			
		} else $this->msg = t("Invalid file");
		$this->responseJson();		
	}
	
	public function actionremoveReviewImage()
	{				
		$id = isset($this->data['id'])?$this->data['id']:'';
		$media = AR_media::model()->find('upload_uuid=:upload_uuid', 
		array(':upload_uuid'=>$id)); 		
		if($media){
			$media->delete();
			$this->code = 1; 
			$this->msg = "OK";			
			$this->details = $id;
		} else $this->msg = t("record not found");
		$this->jsonResponse();		
	}
	public function actionremoveAllReviewImage()
	{		
		if(isset($this->data['upload_images'])){
			$all_uuid = array();
			foreach ($this->data['upload_images'] as $val) {															
				$all_uuid[]=$val['id'];				
			}						
			$criteria = new CDbCriteria();
			$criteria->addInCondition('upload_uuid', $all_uuid);
			AR_media::model()->deleteAll($criteria);			
		}
		$this->code = 1; $this->msg = "ok";
		$this->jsonResponse();		
	}
	
	public function actionaddReview()
	{		
		try {

			
			$order_uuid = isset($this->data['order_uuid'])?trim($this->data['order_uuid']):'';
			$order = COrders::get($order_uuid);
						
			$find = AR_review::model()->find('merchant_id=:merchant_id AND client_id=:client_id
			AND order_id=:order_id', 
		    array( 
		      ':merchant_id'=>intval($order->merchant_id),
		      ':client_id'=>intval(Yii::app()->user->id),
		      ':order_id'=>intval($order->order_id)
		    )); 	
		    
		    if(!$find){
				$model = new AR_review;	
				$model->merchant_id  = intval($order->merchant_id);
				$model->order_id  = intval($order->order_id);
				$model->client_id = intval(Yii::app()->user->id) ;
				$model->review  = isset($this->data['review_content'])?$this->data['review_content']:'';		
				$model->rating  = isset($this->data['rating_value'])?(integer)$this->data['rating_value']:0;
				$model->date_created = CommonUtility::dateNow();
				$model->ip_address = CommonUtility::userIp();
				$model->as_anonymous = isset($this->data['as_anonymous'])?(integer)$this->data['as_anonymous']:0;		
				$model->scenario = 'insert';
				if ($model->save()){
					$this->code = 1; $this->msg = t("Review has been added. Thank you.");
					CReviews::insertMeta($model->id,'tags_like',$this->data['tags_like']);
					CReviews::insertMeta($model->id,'tags_not_like',$this->data['tags_not_like']);
					CReviews::insertMetaImages($model->id,'upload_images',$this->data['upload_images']);
				} else {							
					if ( $error = CommonUtility::parseError( $model->getErrors()) ){
						$this->msg = $error;
					} else $this->msg[] = array('invalid error');				
				}				
		    }else $this->msg[] = t("You already added review for this order");
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}		
	
	public function actionuserLogin()
	{					
		$redirect = isset($this->data['redirect'])?$this->data['redirect']:'';
		$_POST['AR_customer_login'] = array(
		  'username'=>isset($this->data['username'])?$this->data['username']:'',
		  'password'=>isset($this->data['password'])?$this->data['password']:'',
		  'rememberMe'=>intval($this->data['rememberme'])
		);		
		
		$options = OptionsTools::find(array('signup_enabled_verification','signup_enabled_capcha'));
		$signup_enabled_capcha = isset($options['signup_enabled_capcha'])?$options['signup_enabled_capcha']:false;
		$capcha = $signup_enabled_capcha==1?true:false;
		$recaptcha_response = isset($this->data['recaptcha_response'])?$this->data['recaptcha_response']:'';			
		
		$model=new AR_customer_login;
		$model->attributes=$_POST['AR_customer_login'];
		$model->capcha = $capcha;
		$model->recaptcha_response = $recaptcha_response;
		$model->merchant_id = 0;		
			
		if($model->validate() && $model->login() ){
												
			$place_id = CommonUtility::getCookie(Yii::app()->params->local_id);			
			$address_uuid = CCheckout::saveDeliveryAddress($place_id , Yii::app()->user->id);
			
			$this->code = 1 ;
			$this->msg = t("Login successful");
			$this->details = array(
			  'redirect'=>!empty($redirect)?$redirect:Yii::app()->getBaseUrl(true)
			);			
		} else {			
			$this->msg = CommonUtility::parseError( $model->getErrors() );
		}
		$this->jsonResponse();
	}
	
	public function actiongetphoneprefix()
	{
		if ( $data = AttributesTools::countryMobilePrefix()){
			$this->code = 1; $this->msg = "ok";
			$this->details = array(
			  'data'=>$data
			);						
		} else $this->msg = "failed";
		$this->responseJson();
	}
	
	public function actionregisterUser()
	{	
		try {
						
			$options = OptionsTools::find(array('signup_enabled_verification','signup_enabled_capcha'));
			$enabled_verification = isset($options['signup_enabled_verification'])?$options['signup_enabled_verification']:false;
			$verification = $enabled_verification==1?true:false;
			
			$signup_enabled_capcha = isset($options['signup_enabled_capcha'])?$options['signup_enabled_capcha']:false;
			$capcha = $signup_enabled_capcha==1?true:false;
		
			$digit_code = CommonUtility::generateNumber(5);
						
			$recaptcha_response = isset($this->data['recaptcha_response'])?$this->data['recaptcha_response']:'';			
			
			$prefix = isset($this->data['mobile_prefix'])?$this->data['mobile_prefix']:'';
			$mobile_number = isset($this->data['mobile_number'])?$this->data['mobile_number']:'';
			$redirect = isset($this->data['redirect'])?$this->data['redirect']:'';
			$next_url = isset($this->data['next_url'])?$this->data['next_url']:'';									
			
			$model=new AR_clientsignup;
			$model->scenario = 'register';
			$model->capcha = $capcha;
			$model->recaptcha_response = $recaptcha_response;
			
			$model->first_name = isset($this->data['firstname'])?$this->data['firstname']:'';
			$model->last_name = isset($this->data['lastname'])?$this->data['lastname']:'';
			$model->email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
			$model->contact_phone = $prefix.$mobile_number;
			$model->password = isset($this->data['password'])?$this->data['password']:'';		
			$password = $model->password;
			$model->cpassword = isset($this->data['cpassword'])?$this->data['cpassword']:'';
			$model->phone_prefix = $prefix;			
			$model->mobile_verification_code = $digit_code;
			$model->merchant_id = 0;
			
			if($verification==1 || $verification==true){
				$model->status='pending';
			}
			
			if ($model->save()){
				$this->code = 1 ;

				$redirect = !empty($redirect)?$redirect:Yii::app()->getBaseUrl(true);			
				
				if($verification==1 || $verification==true){
										
					$this->msg = t("Please wait until we redirect you");	
					$redirect = Yii::app()->createUrl("/account/verify",array(
					  'uuid'=>$model->client_uuid,
					  'redirect'=>$redirect
					));			
					$this->details = array(
					  'redirect'=>$redirect
					);			
				} else {
					$this->msg = t("Registration successful");				
					$this->details = array(
					  'redirect'=>$redirect
					);			
					
					//AUTO LOGIN
					$this->autoLogin($model->email_address,$password);
				}
			} else {				
				$this->msg = CommonUtility::parseError( $model->getErrors() );
			}		
			
		} catch (Exception $e) {
			$this->msg[] = $err;			
		}
		$this->jsonResponse();
	}
	
	public function actioncheckoutTransaction()
	{		
		$cart_uuid = isset($this->data['cart_uuid'])?trim($this->data['cart_uuid']):'';			
		try {
		   $merchant_id = CCart::getMerchantId($cart_uuid);		  
		   $transactions = CCheckout::getMerchantTransactionList( $merchant_id , Yii::app()->language);
		   $delivery_option = CCheckout::deliveryOptionList();		   
		   $opening_hours = CMerchantListingV1::openHours($merchant_id);		   
		   $transaction_type = '';
		   $data = array();
		   		   
		   $this->code = 1; $this->msg = "ok";
		   $this->details = array(		     
		     'data'=>$data,
		     'transaction_type'=>$transaction_type,
		     'transactions'=>$transactions,
		     'delivery_option'=>$delivery_option,
		     'opening_hours'=>$opening_hours,		     		     
		   );			  
		   //dump($this->details); die();
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actioncheckoutSave()
	{				
		try {
			
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';	
		    $whento_deliver = isset($this->data['whento_deliver'])?$this->data['whento_deliver']:'';	
							
			CCart::savedAttributes($cart_uuid,'transaction_type',
			 isset($this->data['transaction_type'])?$this->data['transaction_type']:''
			);
			
			CCart::savedAttributes($cart_uuid,'whento_deliver',
			 isset($this->data['whento_deliver'])?$this->data['whento_deliver']:''
			);
			
			CCart::savedAttributes($cart_uuid,'delivery_date',
			 isset($this->data['delivery_date'])?$this->data['delivery_date']:''
			);
			
			if($whento_deliver=="schedule"){
				CCart::savedAttributes($cart_uuid,'delivery_time',
				 isset($this->data['delivery_time'])? json_encode($this->data['delivery_time']) :''
				);
			} else CCart::deleteAttributesAll($cart_uuid, array('delivery_time','delivery_date') );
			
			$this->code = 1; $this->msg = "ok";
			$this->details = array(
			   'whento_deliver'=>$whento_deliver,
			   'delivery_date'=>isset($this->data['delivery_date'])?$this->data['delivery_date']:'',
			   'delivery_time'=>isset($this->data['delivery_time'])?$this->data['delivery_time']:'',
			);
						
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());
		}
		
		$this->jsonResponse();
	}
	
	public function actionloadPromo()
	{			
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		
		try {
			
			$merchant_id = CCart::getMerchantId($cart_uuid);			
			$data = CPromos::promo($merchant_id,date("Y-m-d"));	
			
			$promo_selected = array();
			$atts = CCart::getAttributesAll($cart_uuid,array('promo','promo_type','promo_id'));
			if($atts){
				$saving = '';
				if(isset($atts['promo'])){
					if ($promo = json_decode($atts['promo'],true)){												
						if($promo['type']=="offers"){
							//
						} elseif ( $promo['type']=="voucher" ){
							$discount_value = isset($promo['value'])?$promo['value']:0;
							$discount_value = $discount_value*-1;	
							$saving = t("You're saving {{discount}}",array(
							  '{{discount}}'=>Price_Formatter::formatNumber($discount_value)
							));
						}
						$promo_selected = array( $atts['promo_type'],$atts['promo_id'] , $saving );
					}
				}				
			}
				
			if($data){
				$this->code = 1; $this->msg = "ok";	
				$this->details = array(
				  'count'=>count($data),
				  'data'=>$data,
				  'promo_selected'=>$promo_selected
				);				
			} else $this->msg = t("no results");			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionapplyPromo()
	{
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		$promo_id = isset($this->data['promo_id'])?intval($this->data['promo_id']):'';
		$promo_type = isset($this->data['promo_type'])?$this->data['promo_type']:'';
		
		try {

			$merchant_id = CCart::getMerchantId($cart_uuid);
			CCart::getContent($cart_uuid,Yii::app()->language);	
			$subtotal = CCart::getSubTotal();
			$sub_total = floatval($subtotal['sub_total']);
			
			$now = date("Y-m-d");			
			$params = array();
				   
			if($promo_type==="voucher"){
												
				$resp = CPromos::applyVoucher( $merchant_id, $promo_id, Yii::app()->user->id , $now , $sub_total);
				$less_amount = $resp['less_amount'];
				
				$params = array(
				  'name'=>"less voucher",
				  'type'=>$promo_type,
				  'id'=>$promo_id,
				  'target'=>'subtotal',
				  'value'=>"-$less_amount",
				);		
				
				
			} else if ($promo_type=="offers") {		
				
				$transaction_type = CCart::cartTransaction($cart_uuid,Yii::app()->params->local_transtype,$merchant_id);			
				$resp = CPromos::applyOffers( $merchant_id, $promo_id, $now , $sub_total , $transaction_type);
				$less_amount = $resp['less_amount'];
				
				$name = array(
				  'label'=>"Discount {{discount}}%",
				  'params'=>array(
				   '{{discount}}'=>Price_Formatter::convertToRaw($less_amount,0)
				  )
				);
				$params = array(
				  'name'=> json_encode($name),
				  'type'=>$promo_type,
				  'id'=>$promo_id,
				  'target'=>'subtotal',
				  'value'=>"-%$less_amount"
				);													
			}
			
			CCart::savedAttributes($cart_uuid,'promo',json_encode($params));
			CCart::savedAttributes($cart_uuid,'promo_type',$promo_type);
			CCart::savedAttributes($cart_uuid,'promo_id',$promo_id);
								
			$this->code = 1; 
			$this->msg = "succesful";
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->jsonResponse();
	}
	
	public function actionremovePromo()
	{
				
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		$promo_id = isset($this->data['promo_id'])?intval($this->data['promo_id']):'';
		$promo_type = isset($this->data['promo_type'])?$this->data['promo_type']:'';
		
		
		try {
			
			$merchant_id = CCart::getMerchantId($cart_uuid);			
			CCart::deleteAttributesAll($cart_uuid,CCart::CONDITION_RM);
			$this->code = 1;
			$this->msg = "ok";
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->jsonResponse();
	}
	
	public function actionloadTips()
	{
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		try {
			
			$merchant_id = CCart::getMerchantId($cart_uuid);			
			$data = CTips::data();
			
			$tips = 0; $transaction_type = '';			
			if ( $resp = CCart::getAttributesAll($cart_uuid,array('tips','transaction_type')) ){				
				$tips = isset($resp['tips'])?floatval($resp['tips']):0;
				$transaction_type = isset($resp['transaction_type'])?$resp['transaction_type']:'';				
			}
						
			$this->code = 1; $this->msg = "OK";
			$this->details = array(
			  'transaction_type'=>$transaction_type,
			  'tips'=>$tips,
			  'data'=>$data
			);
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actioncheckoutAddTips()
	{
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		$value = isset($this->data['value'])?floatval($this->data['value']):0;		
		try {
			
			$merchant_id = CCart::getMerchantId($cart_uuid);
			CCart::savedAttributes($cart_uuid,'tips',$value);	
			$this->code = 1; $this->msg = "OK";
			$this->details = array(
			  'tips'=>$value,			  
			);
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->jsonResponse();
	}
	
	public function actioncheckoutAddress()
	{		
		$data = array();
		$attributes = array(); $addresses = array();
		$transaction_type = '';
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		try {
			
			$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
			$merchant_id = CCart::getMerchantId($cart_uuid);
			
			$resp = CCart::getAttributesAll($cart_uuid,array(
			  'transaction_type','location_name','delivery_instructions','delivery_options','address_label'
			));			
						
			$transaction_type = isset($resp['transaction_type'])?$resp['transaction_type']:'';					
			
			if(!Yii::app()->user->isGuest){
				$addresses = CClientAddress::getAddresses( Yii::app()->user->id );				
			}
						
			$data = CClientAddress::getAddress($local_id,Yii::app()->user->id);					
									
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'transaction_type'=>$transaction_type,
			  'data'=>$data,	
			  'addresses'=>$addresses,			  
			  'delivery_option'=>CCheckout::deliveryOption(),
			  'address_label'=>CCheckout::addressLabel(),
			  'maps_config'=>CMaps::config()
			);			
		} catch (Exception $e) {		    			
		    $this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'transaction_type'=>$transaction_type,
			  'data'=>$data,	
			  'addresses'=>$addresses,			  
			  'delivery_option'=>CCheckout::deliveryOption(),
			  'address_label'=>CCheckout::addressLabel(),
			  'maps_config'=>CMaps::config()
			);				
		}
		$this->responseJson();
	}
	
	public function actioncheckoutValidateCoordinates()
	{		
		$unit = Yii::app()->params['settings']['home_search_unit_type'];	
		$lat = isset($this->data['lat'])?$this->data['lat']:'';
		$lng = isset($this->data['lng'])?$this->data['lng']:'';
		$new_lat = isset($this->data['new_lat'])?$this->data['new_lat']:'';
		$new_lng = isset($this->data['new_lng'])?$this->data['new_lng']:'';
		
		$distance = CMaps::getLocalDistance($unit,$lat,$lng,$new_lat,$new_lng);		
		if($distance=="NaN"){
			$this->code = 1;
			$this->msg = "OK";
		} else if ($distance<0.2) {	
			$this->code = 1;
			$this->msg = "OK";
		} else if ($distance>=0.2) {
			$this->msg[] = t("Pin location is too far from the address");
		}
		$this->details = array(
		  'distance'=>$distance
		);
		$this->jsonResponse();
	}
	
	public function actioncheckoutsaveaddress()
	{		
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		$data = isset($this->data['data'])?$this->data['data']:'';
		$location_name = isset($this->data['location_name'])?$this->data['location_name']:'';
		$delivery_instructions = isset($this->data['delivery_instructions'])?$this->data['delivery_instructions']:'';
		$delivery_options = isset($this->data['delivery_options'])?$this->data['delivery_options']:'';
		$address_label = isset($this->data['address_label'])?$this->data['address_label']:'';		
		try {
			
						
			$address = array(); 			
			$new_place_id = isset($data['place_id'])?$data['place_id']:'';
			$address_uuid = isset($data['address_uuid'])?$data['address_uuid']:'';
			$new_lat = isset($data['latitude'])?$data['latitude']:''; 
			$new_lng = isset($data['longitude'])?$data['longitude']:'';
			$place_id = isset($data['place_id'])?$data['place_id']:'';
									
			$model = AR_client_address::model()->find('address_uuid=:address_uuid AND client_id=:client_id', 
		    array(':address_uuid'=>$address_uuid,'client_id'=>Yii::app()->user->id)); 
		    if($model){		    	
		    			    	
		    	$model->latitude = $new_lat;
		    	$model->longitude = $new_lng;
		    	$model->location_name = $location_name;
		    	$model->delivery_options = $delivery_options;
		    	$model->delivery_instructions = $delivery_instructions;
		    	$model->address_label = $address_label;
		    	$model->formatted_address = isset($this->data['formatted_address'])?$this->data['formatted_address']:'';
		    	$model->save();
		    }
		    		    
		    if(!empty($place_id)){
		       CommonUtility::WriteCookie( Yii::app()->params->local_id ,$place_id);  
		    }
		
			$this->code = 1;
			$this->msg = "OK";
					
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->jsonResponse();
	}
	
	public function actionsetPlaceID()
	{		
		$place_id = isset($this->data['place_id'])?trim($this->data['place_id']):'';
		if(!empty($place_id)){
	       CommonUtility::WriteCookie( Yii::app()->params->local_id ,$place_id);  
	    }
	    $this->code = 1;
		$this->msg = "OK";
		$this->responseJson();
	}
	
	public function actiondeleteAddress()
	{		
		$address_uuid = isset($this->data['address_uuid'])?trim($this->data['address_uuid']):'';
		if(!Yii::app()->user->isGuest){			
			try {
				CClientAddress::delete(Yii::app()->user->id,$address_uuid);
				$this->code = 1; 
				$this->msg = "OK";
			} catch (Exception $e) {
			    $this->msg = t($e->getMessage());
			}
		} else $this->msg = t("User not login or session has expired");
		$this->responseJson();
	}
	
	public function actiongetCheckoutPhone()
	{
		try {
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$atts = CCart::getAttributesAll($cart_uuid,array('contact_number','contact_number_prefix'));			
			$contact_number = isset($atts['contact_number'])?$atts['contact_number']:'';
			$default_prefix = isset($atts['contact_number_prefix'])?$atts['contact_number_prefix']:63;	
						
			$contact_number = str_replace($default_prefix,"",$contact_number);
			$default_prefix = str_replace("+","",$default_prefix);
			
			$data = AttributesTools::countryMobilePrefix();
			$this->code = 1;
			$this->msg = "OK";			
			$this->details = array(
			  'contact_number_w_prefix'=>isset($atts['contact_number'])?$atts['contact_number']:'',
			  'contact_number'=>$contact_number,
			  'default_prefix'=>$default_prefix,
			  'prefixes'=>$data,
			);
			
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionRequestEmailCode()
	{
		try {
		    		    
		    if(!Yii::app()->user->isGuest){		    
		    	$model = AR_client::model()->find('client_id=:client_id', 
		        array(':client_id'=>Yii::app()->user->id)); 	
		        if($model){		           
		           $digit_code = 12345;//CommonUtility::generateNumber(5);
		           $model->mobile_verification_code = $digit_code;
				   $model->scenario="resend_otp";
		           if($model->save(false)){		   
		           	   // SEND EMAIL HERE         
			           $this->code = 1;
			           $this->msg = t("We sent a code to {{email_address}}.",array(
			             '{{email_address}}'=> CommonUtility::maskEmail($model->email_address)
			           ));			           
                       if(DEMO_MODE==TRUE){
		    			  $this->details['verification_code']=t("Your verification code is {{code}}",array('{{code}}'=>$digit_code));
		    		   }
		           } else $this->msg = CommonUtility::parseError($model->getErrors());
		        } else $this->msg[] = t("Record not found");
		    } else $this->msg[] = t("Your session has expired please relogin");
		    
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function action2authVerication()
	{
		try {
			
			$code = isset($this->data['code'])?$this->data['code']:'';
			$model = AR_client::model()->find('client_id=:client_id AND mobile_verification_code=:mobile_verification_code', 
		    array(':client_id'=>Yii::app()->user->id,':mobile_verification_code'=>trim($code) )); 		
		    if($model){
		    	$this->code = 1; $this->msg = "OK";
		    } else $this->msg[] = t("Invalid 6 digit code");			
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());
		}		
		$this->responseJson();
	}
	
	public function actionChangePhone()
	{
		try {
			
		   $cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		   $data = isset($this->data['data'])?$this->data['data']:'';
		   $code = isset($this->data['code'])?$this->data['code']:'';
		   $mobile_prefix = isset($data['mobile_prefix'])?$data['mobile_prefix']:'';
		   $mobile_number = isset($data['mobile_number'])?$data['mobile_number']:'';
		   		   
		   $model = AR_client::model()->find('client_id=:client_id AND mobile_verification_code=:mobile_verification_code', 
		   array(':client_id'=>Yii::app()->user->id,':mobile_verification_code'=>trim($code) )); 		
		   if($model){
		   	   $model->phone_prefix = $mobile_prefix;
		   	   $model->contact_phone = $mobile_prefix.$mobile_number;
		   	   if($model->save(false)){	
			   	   CCart::savedAttributes($cart_uuid,'contact_number', $model->contact_phone );
			   	   CCart::savedAttributes($cart_uuid,'contact_number_prefix', $mobile_prefix );
			   	   
			   	   Yii::app()->user->setState('contact_number', $model->contact_phone );
			   	   
			   	   $this->code = 1;
			   	   $this->msg = t("Succesfull change contact number");
			   	   $this->details = array(
			   	     'contact_number'=>$model->contact_phone
			   	   );
		   	   } else $this->msg = CommonUtility::parseError($model->getErrors()); 
		   } else $this->msg[] = t("Invalid 6 digit code");
		   
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());
		}		
		$this->responseJson();
	}
	
	public function actionUpdatePhone()
	{
		try {
			$cart_uuid = CommonUtility::getCookie("cart_uuid_local");
		   $mobile_prefix = $this->data['mobile_prefix'];
		   $mobile_number = $this->data['mobile_number'];
		   		   
		   $model = AR_client::model()->find('client_id=:client_id', 
		   array(':client_id'=>Yii::app()->user->id)); 		
			if($model){
				$model->first_name = isset($this->data['first_name'])?$this->data['first_name']:'';
		    	$model->last_name = isset($this->data['last_name'])?$this->data['last_name']:'';
				$model->phone_prefix = $mobile_prefix;
		   	   $model->contact_phone = $mobile_number;
		   	   if($model->save(false)){	
			   	   CCart::savedAttributes($cart_uuid,'contact_number', $model->contact_phone );
			   	   CCart::savedAttributes($cart_uuid,'contact_number_prefix', $mobile_prefix );
			   	   
			   	   Yii::app()->user->setState('contact_number', $model->contact_phone );
			   	   
			   	   $this->code = 1;
			   	   $this->msg = t("Succesfull change contact number");
			   	   $this->details = array(
			   	     'contact_number'=>$model->contact_phone
			   	   );
		   	   } else $this->msg = CommonUtility::parseError($model->getErrors()); 
		   } else $this->msg = t("Invalid Request");
		   
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());
		}		
		$this->responseJson();
	}
	
	public function actionapplyPromoCode()
	{		
		$promo_code = isset($this->data['promo_code'])?trim($this->data['promo_code']):'';
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		try {
			
			$merchant_id = CCart::getMerchantId($cart_uuid);
			CCart::getContent($cart_uuid,Yii::app()->language);	
			$subtotal = CCart::getSubTotal();
			$sub_total = floatval($subtotal['sub_total']);
			$now = date("Y-m-d");	
			
			$model = AR_voucher::model()->find('voucher_name=:voucher_name', 
		    array(':voucher_name'=>$promo_code)); 		
		    if($model){
		    	
		    	$promo_id = $model->voucher_id;
		    	$voucher_owner = $model->voucher_owner;
		    	$promo_type = 'voucher';
		    	
		    	$resp = CPromos::applyVoucher( $merchant_id, $promo_id, Yii::app()->user->id , $now , $sub_total);
		    	$less_amount = $resp['less_amount'];
		    	
		    	$params = array(
				  'name'=>"less voucher",
				  'type'=>$promo_type,
				  'id'=>$promo_id,
				  'target'=>'subtotal',
				  'value'=>"-$less_amount",
				  'voucher_owner'=>$voucher_owner,
				);						
				
				CCart::savedAttributes($cart_uuid,'promo',json_encode($params));
			    CCart::savedAttributes($cart_uuid,'promo_type',$promo_type);
			    CCart::savedAttributes($cart_uuid,'promo_id',$promo_id);
			    
			    $this->code = 1; 
			    $this->msg = "succesful";
			    
		    } else $this->msg = t("Voucher code not found");
					
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actiongetAppliedPromo()
	{
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		try {
			$promo = array();
			$atts = CCart::getAttributesAll($cart_uuid,array('promo','promo_type','promo_id'));
			if($atts){
				$saving = '';
				if(isset($atts['promo'])){
					if ($promo = json_decode($atts['promo'],true)){												
						if($promo['type']=="offers"){
							
						} elseif ( $promo['type']=="voucher" ){
							$discount_value = isset($promo['value'])?$promo['value']:0;
							$discount_value = $discount_value*-1;	
							$saving = t("You're saving [discount]",array(
							  '[discount]'=>Price_Formatter::formatNumber($discount_value)
							));
						}
					}
				}	
								
				$this->code = 1; $this->msg = "ok";	
				$this->details = array(
				  'data'=>$promo,
				  'saving'=>$saving
				);									
			} else $this->msg = t("No results");	
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionPaymentList()
	{
		try {
			
		   $cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
		   $merchant_id = CCart::getMerchantId($cart_uuid);
		   $data = CPayments::PaymentList($merchant_id);	
			
			$data_merchant = CCart::getMerchantForCredentials($cart_uuid);
			$default_payment_code = '';
			$model = AR_client_payment_method::model()->find(
			'client_id=:client_id AND as_default=:as_default AND merchant_id=:merchant_id ', 
		    array(
		      ':client_id'=>Yii::app()->user->id,		      
		      ':as_default'=>1,
		      ':merchant_id'=>$merchant_id
		    )); 	
		    if($model){		    	
		    	$default_payment_code=$model->payment_code;
		    }
		    foreach ($data as $key => $value) {
		    	$data[$key]['as_default'] = 0;
		    	if($value['payment_code'] == $default_payment_code)
		    		$data[$key]['as_default'] = 1;
		    }
			// try {
			// 	$paymentList = CPayments::SavedPaymentList( Yii::app()->user->id , $data_merchant['merchant_type'], $data_merchant['merchant_id'] );
				

			// 	foreach ($paymentList as $key => $value) {
			// 		$index = array_search($value['payment_code'], array_column($data, 'payment_code'));
			// 		if($index !== false){
			// 			unset($data[$index]);
			// 		}
			// 	}	
			// } catch (Exception $e) {
				
			// }	
			$data = array_values($data);
		   $this->code = 1;
		   $this->msg = "ok";
		   $this->details = array(		     
		     'data'=>$data,
		     'default_payment_code'=>$default_payment_code,
		   );		   
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionsavedCards()
	{		
		try {
			
			$expiration_month='';$expiration_yr=''; $error_data = array(); $error = array();
			$card_name = isset($this->data['card_name'])?$this->data['card_name']:'';
			$credit_card_number = isset($this->data['credit_card_number'])?$this->data['credit_card_number']:'';
			$expiry_date = isset($this->data['expiry_date'])?$this->data['expiry_date']:'';
			$cvv = isset($this->data['cvv'])?$this->data['cvv']:'';
			$billing_address = isset($this->data['billing_address'])?$this->data['billing_address']:'';
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
			$card_uuid = isset($this->data['card_uuid'])?$this->data['card_uuid']:'';
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
					
			if(empty($card_uuid)){
				$model=new AR_client_cc;
				$model->scenario='add';
			} else {
				$model = AR_client_cc::model()->find('client_id=:client_id AND card_uuid=:card_uuid', 				
			    array(
			      ':client_id'=>Yii::app()->user->id,
			      ':card_uuid'=>$card_uuid
			    )); 	
			    if(!$model){
			    	$this->msg[] = t("Record not found");
			    	$this->responseJson();
			    }
			    $model->scenario='update';
			}
						
			$model->client_id = Yii::app()->user->id;
			$model->payment_code = $payment_code;
			$model->card_name = $card_name;
			$model->credit_card_number = str_replace(" ","",$credit_card_number);
			$model->expiration = $expiry_date;
			$model->cvv = $cvv;
			$model->billing_address = $billing_address;
			$model->merchant_id = $merchant_id;

			if($model->save()){
	    		$this->code = 1;
		    	$this->msg = "OK";	
	    	} else $this->msg = CommonUtility::parseError( $model->getErrors());
			
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}				
		$this->responseJson();
	}
	
	public function actionSavedPaymentList()
	{		
		try {
			
			$default_payment_uuid = '';
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			
			$data_merchant = CCart::getMerchantForCredentials($cart_uuid);			
			$merchant_id = isset($data_merchant['merchant_id'])?$data_merchant['merchant_id']:0;
						
			if($data_merchant['merchant_type']==2){	
				$merchant_id=0;			
			}
						
			$model = AR_client_payment_method::model()->find(
			'client_id=:client_id AND as_default=:as_default AND merchant_id=:merchant_id ', 
		    array(
		      ':client_id'=>Yii::app()->user->id,		      
		      ':as_default'=>1,
		      ':merchant_id'=>$merchant_id
		    )); 	
		    if($model){		    	
		    	$default_payment_uuid=$model->payment_uuid;
		    }
		    
			$data = CPayments::SavedPaymentList( Yii::app()->user->id , $data_merchant['merchant_type'] , 
			$data_merchant['merchant_id'] );		
			$this->code = 1;
		    $this->msg = "ok";
		    $this->details = array(
		      'default_payment_uuid'=>$default_payment_uuid,
		      'data'=>$data,
		    );		    
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}
	
	public function actiondeleteSavedPaymentMethod()
	{
		try {
		   $payment_uuid = isset($this->data['payment_uuid'])?$this->data['payment_uuid']:'';
		   $payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
		   CPayments::delete(Yii::app()->user->id,$payment_uuid);
		   $this->code = 1;
		   $this->msg = "ok";
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}
	
	public function actionSavedPaymentProvider()
	{		
		try {
			
			$payment_code = isset($this->data['payment_code'])?$this->data['payment_code']:'';
			$merchant_id = isset($this->data['merchant_id'])?$this->data['merchant_id']:'';
			
			$payment = AR_payment_gateway::model()->find('payment_code=:payment_code', 
		    array(':payment_code'=>$payment_code)); 	
		    
		    if($payment){		    	
				$model = new AR_client_payment_method;
				$model->scenario = "insert";
				$model->client_id = Yii::app()->user->id;
				$model->payment_code = $payment_code;
				$model->as_default = intval(1);
				$model->attr1 = $payment?$payment->payment_name:'unknown';	
				$model->merchant_id = intval($merchant_id);
				if($model->save()){
					$this->code = 1;
		    		$this->msg = t("Succesful");
				}else{
					$model = AR_client_payment_method::model()->find('client_id=:client_id AND payment_code=:payment_code', 
					array(
					  ':client_id'=>Yii::app()->user->id,
					  ':payment_code'=>$payment_code
					)); 	
					$model->as_default = 1;
					$model->save();
					$this->code = 1;
		    		$this->msg = t("Succesful");

				} 
				// else{
				// 	$this->msg = CommonUtility::parseError($model->getErrors());
				// }
		    } else $this->msg[] = t("Payment provider not found");
			
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}				
		$this->responseJson();
	}
	
	public function actionSetDefaultPayment()
	{			
		try {	
			$payment_uuid = isset($this->data['payment_uuid'])?$this->data['payment_uuid']:'';
			$model = AR_client_payment_method::model()->find('client_id=:client_id AND payment_uuid=:payment_uuid', 
			array(
			  ':client_id'=>Yii::app()->user->id,
			  ':payment_uuid'=>$payment_uuid
			)); 		
			if($model){
				$model->as_default = 1;
				$model->save();
				$this->code = 1;
		    	$this->msg = t("Succesful");
			} else $this->msg = t("Record not found");			
		    
	    } catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		
		$this->responseJson();
	}
	
	public function actionPlaceOrder()
	{		
				
		$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
		$cart_uuid = isset($this->data['cart_uuid'])?trim($this->data['cart_uuid']):'';
		$payment_uuid = isset($this->data['payment_uuid'])?trim($this->data['payment_uuid']):'';
		
		$payload = array(
		   'items','merchant_info','service_fee',
		   'delivery_fee','packaging','tax','tips','checkout','discount','distance',
		   'summary','total'
	    );		
	    
	    
	    $unit = Yii::app()->params['settings']['home_search_unit_type']; 
	    $distance = 0; 	    
		$error = array(); 
		$minimum_order = 0; 
		$maximum_order=0;
		$merchant_info = array(); 
		$delivery_fee = 0; 
		$distance_covered=0;
		$merchant_lat = ''; 
		$merchant_lng=''; 
		$out_of_range = false;
		$address_component = array();
		$commission = 0;
		$commission_based = ''; 
		$merchant_id = 0; 
		$merchant_earning = 0; 
		$total_discount = 0; 
		$service_fee = 0; 
		$delivery_fee = 0; 
		$packagin_fee = 0; 
		$tip = 0;
		$total_tax = 0;
		$tax = 0;
		$promo_details = array();
		$summary = array();
		$offer_total = 0;
		$tax_type = '';
        $tax_condition = '';
				
		/*CHECK IF MERCHANT IS OPEN*/
		try {
			$merchant_id = CCart::getMerchantId($cart_uuid);			
			$date = date("Y-m-d");
			$time_now = date("H:i");
			
			$choosen_delivery = isset($this->data['choosen_delivery'])?$this->data['choosen_delivery']:'';		
			$whento_deliver = isset($choosen_delivery['whento_deliver'])?$choosen_delivery['whento_deliver']:'';
			
			if($whento_deliver=="schedule"){
				$date = isset($choosen_delivery['delivery_date'])?$choosen_delivery['delivery_date']:$date;
				$time_now = isset($choosen_delivery['delivery_time'])?$choosen_delivery['delivery_time']['start_time']:$time_now;
			}
						
			$datetime_to = date("Y-m-d g:i:s a",strtotime("$date $time_now"));
			CMerchantListingV1::checkCurrentTime( date("Y-m-d g:i:s a") , $datetime_to);
			
			$resp = CMerchantListingV1::checkStoreOpen($merchant_id,$date,$time_now);			
			if($resp['merchant_open_status']<=0){
				$this->msg[] = t("This store is close right now, but you can schedulean order later.");
				$this->responseJson();
			}					
						
			CMerchantListingV1::storeAvailableByID($merchant_id);
			
									
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		    $this->responseJson();
		}	
				
		try {

			
			require_once 'get-cart.php';
			
			$include_utensils = isset($this->data['include_utensils'])?$this->data['include_utensils']:false;
		    $include_utensils = $include_utensils=="true"?true:false;
		    CCart::savedAttributes($cart_uuid,'include_utensils',$include_utensils);
						
			if(is_array($error) && count($error)>=1){				
				$this->msg = $error;
			} else {					
												
				$merchant_type = $data['merchant']['merchant_type'];
				$commision_type = $data['merchant']['commision_type'];				
				$merchant_commission = $data['merchant']['commission'];				
								
				$sub_total_based  = CCart::getSubTotal_TobeCommission();						
				$resp_comm = CCommission::getCommissionValue($merchant_type,$commision_type,$merchant_commission,$sub_total_based,$total);				
				
				if($resp_comm){					
					$commission_based = $resp_comm['commission_based'];
					$commission = $resp_comm['commission'];
					$merchant_earning = $resp_comm['merchant_earning'];
				}
				
				$atts = CCart::getAttributesAll($cart_uuid,array('whento_deliver',
				  'promo','promo_type','promo_id','tips','delivery_date','delivery_time'
				));						
				
				$payments = CPayments::getPaymentMethod( $payment_uuid, Yii::app()->user->id );
				$sub_total_less_discount  = CCart::getSubTotal_lessDiscount();				
											
				if(is_array($summary) && count($summary)>=1){	
					foreach ($summary as $summary_item) {						
						switch ($summary_item['type']) {
							case "voucher":								
								$total_discount = CCart::cleanNumber($summary_item['raw']);
								break;
						
							case "offers":	
							    $total_discount += CCart::cleanNumber($summary_item['raw']);
								$offer_total = CCart::cleanNumber($summary_item['raw']);
							    //$offer_total = $total_discount;
							    //$total_discount = floatval($total_discount)+ floatval($total_discount);
								break;
								
							case "service_fee":
								$service_fee = CCart::cleanNumber($summary_item['raw']);
								break;
								
							case "delivery_fee":
								$delivery_fee = CCart::cleanNumber($summary_item['raw']);
								break;	
							
							case "packaging_fee":
								$packagin_fee = CCart::cleanNumber($summary_item['raw']);
								break;			
								
							case "tip":
								$tip = CCart::cleanNumber($summary_item['raw']);
								break;				
								
							case "tax":
								$total_tax+= CCart::cleanNumber($summary_item['raw']);
								break;					
									
							default:
								break;
						}
					}				
				}
				
				if($tax_enabled){					
					$tax_type = CCart::getTaxType();									
					$tax_condition = CCart::getTaxCondition();					
					if($tax_type=="standard" || $tax_type=="euro"){			
						if(is_array($tax_condition) && count($tax_condition)>=1){
							foreach ($tax_condition as $tax_item_cond) {
								$tax = isset($tax_item_cond['tax_rate'])?$tax_item_cond['tax_rate']:0;
							}
						}
					}									
				}			
															
				$model = new AR_ordernew;
				$model->scenario = $transaction_type;
				$model->order_uuid = CommonUtility::generateUIID();
				$model->merchant_id = intval($merchant_id);	
				$model->client_id = intval(Yii::app()->user->id);
				$model->service_code = $transaction_type;
				$model->payment_code = isset($payments['payment_code'])?$payments['payment_code']:'';
				$model->total_discount = floatval($total_discount);
				$model->sub_total = floatval($sub_total);
				$model->sub_total_less_discount = floatval($sub_total_less_discount);
				$model->service_fee = floatval($service_fee);
				$model->delivery_fee = floatval($delivery_fee);
				$model->packaging_fee = floatval($packagin_fee);
				$model->tax_type = $tax_type;
				$model->tax = floatval($tax);
				$model->tax_total = floatval($total_tax);				
				$model->courier_tip = floatval($tip);				
				$model->total = floatval($total);
				$model->total_original = floatval($total);				
				
				if(is_array($promo_details) && count($promo_details)>=1){
					if($promo_details['promo_type']=="voucher"){
						$model->promo_code = $promo_details['voucher_name'];
						$model->promo_total = $promo_details['less_amount'];
					} elseif ( $promo_details['promo_type']=="offers" ){						
						$model->offer_discount = $promo_details['less_amount'];
						$model->offer_total = floatval($offer_total);
					}
				}
				
				$model->whento_deliver = isset($atts['whento_deliver'])?$atts['whento_deliver']:'';
				if($model->whento_deliver=="now"){
					$model->delivery_date = CommonUtility::dateNow();
				} else {
					$model->delivery_date = isset($atts['delivery_date'])?$atts['delivery_date']:'';
					$model->delivery_time = isset($atts['delivery_time'])?CCheckout::jsonTimeToSingleTime($atts['delivery_time']):'';
					$model->delivery_time_end = isset($atts['delivery_time'])?CCheckout::jsonTimeToSingleTime($atts['delivery_time'],'end_time'):'';
				}
												
				$model->commission_type = $commision_type;
				$model->commission_value = $merchant_commission;
				$model->commission_based = $commission_based;
				$model->commission = floatval($commission);
				$model->commission_original = floatval($commission);
				$model->merchant_earning = floatval($merchant_earning);	
				$model->merchant_earning_original = floatval($merchant_earning);	
				$model->formatted_address = isset($address_component['formatted_address'])?$address_component['formatted_address']:'';
				
				$metas = CCart::getAttributesAll($cart_uuid,
				  array('promo','promo_type','promo_id','tips',
				  'cash_change','customer_name','contact_number','contact_email','include_utensils'
				  )
				);
				
				/*LINE ITEMS*/
				$model->items = $data['items'];				
				$model->meta = $metas;
				$model->address_component = $address_component;
				$model->cart_uuid = $cart_uuid;
				$model->use_currency_code = Price_Formatter::$number_format['currency_code'];
				$model->base_currency_code = Price_Formatter::$number_format['currency_code'];
				$model->exchange_rate = 1;				
				$model->tax_use = $tax_settings;				
				$model->tax_for_delivery = $tax_delivery;
								
				if($model->save()){
											
					$redirect = Yii::app()->createAbsoluteUrl("orders/index",array(
					   'order_uuid'=>$model->order_uuid
					));					
									
					/*EXECUTE MODULES*/							
					$payment_instructions = Yii::app()->getModule($model->payment_code)->paymentInstructions();
					if($payment_instructions['method']=="offline"){
						Yii::app()->getModule($model->payment_code)->savedTransaction($model);							
					}									
					
					$order_bw = OptionsTools::find(array('bwusit'));
					$order_bw = isset($order_bw['bwusit'])?$order_bw['bwusit']:0;
																		
					$this->code = 1;
					$this->msg = t("Your Order has been place");
					$this->details = array(  
					  'order_uuid' => $model->order_uuid,
					  'redirect'=>$redirect,
					  'payment_code'=>$model->payment_code,
					  'payment_uuid'=>$payment_uuid,
					  'payment_instructions'=>$payment_instructions,		
					  'order_bw'=>$order_bw
					);								
				} else {					
					if ( $error = CommonUtility::parseError( $model->getErrors()) ){				
						$this->msg = $error;						
					} else $this->msg[] = array('invalid error');
				}				
			}		
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}					
		$this->responseJson();
	}
	
	public function actiongetOrder()
	{		
		try {
			
		   $order_uuid = isset($this->data['order_uuid'])?trim($this->data['order_uuid']):'';		
		   $merchant_id = COrders::getMerchantId($order_uuid);
		   $merchant_info = COrders::getMerchant($merchant_id,Yii::app()->language);	
		   
		   COrders::getContent($order_uuid);
		   $items = COrders::getItemsOnly();		   
		   $meta  = COrders::orderMeta();
		   $order_id = COrders::getOrderID();
		   $items_count = COrders::itemCount($order_id);
		   $progress = CTrackingOrder::getProgress($order_uuid , date("Y-m-d g:i:s a") );		   
		   $order_info  = COrders::orderInfo(Yii::app()->language,date("Y-m-d"));
		   $order_info  = isset($order_info['order_info'])?$order_info['order_info']:'';
		   $order_type = isset($order_info['order_type'])?$order_info['order_type']:'';    			   
		   
		   $subtotal = COrders::getSubTotal();
		   $subtotal = isset($subtotal['sub_total'])?$subtotal['sub_total']:0;
		   $subtotal = Price_Formatter::formatNumber(floatval($subtotal));
		   $order_info['sub_total'] = $subtotal;
		   
		   $instructions = CTrackingOrder::getInstructions($merchant_id,$order_type);
		   		   
		   $this->code = 1;
		   $this->msg = "Ok";
		   $this->details = array(
		     'merchant_info'=>$merchant_info,
		     'order_info'=>$order_info,
		     'items_count'=>$items_count,		     
		     'items'=>$items,
		     'meta'=>$meta,		    
		     'progress'=>$progress,
		     'instructions'=>$instructions,
		     'maps_config'=>CMaps::config()
		   );		   
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());
		}	
		$this->responseJson();
	}
	
	public function actionorderHistory()
	{	     
	     try {
	     	  	     	  
	     	  $page = isset($this->data['page'])?intval($this->data['page']):'';	     
	     	  $q = isset($this->data['q'])?trim($this->data['q']):'';
	     	     	  
	     	  $offset = 0; $show_next_page = false;
	     	  $limit = Yii::app()->params->list_limit;
	     	  $total_rows = COrders::orderHistoryTotal(Yii::app()->user->id);    	
	     	  	          
	          $pages = new CPagination($total_rows);
			  $pages->pageSize = $limit;
			  $pages->setCurrentPage($page);
			  $offset = $pages->getOffset();	
			  $page_count = $pages->getPageCount();
									
			  if($page_count > ($page+1) ){
				  $show_next_page = true;
			  }   
			  			  			  			  
			  $data = COrders::getOrderHistory(Yii::app()->user->id,$q,$offset,$limit);			 
			  	          	 	                   	       
	          $this->code = 1;
	          $this->msg = "Ok";	        
	          $this->details = array(
			     'show_next_page'=>$show_next_page,
			     'page'=>intval($page)+1,
			     'data'=>$data
			  );			  
	     } catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		 }	
		 $this->responseJson();
	}
	public function actionorderSummary()
	{
		$summary = COrders::getOrderSummary(Yii::app()->user->id);
		$this->code = 1; $this->msg = "OK";
		$this->details = array(
		  'summary'=>$summary
		);
		$this->responseJson();
	}
	
	public function actionorderdetails()
	{
		try {		 	
						
			 $refund_transaction = array();
		     $order_uuid = isset($this->data['order_uuid'])?trim($this->data['order_uuid']):'';
		     
		     COrders::getContent($order_uuid,Yii::app()->language);
		     $merchant_id = COrders::getMerchantId($order_uuid);
		     $merchant_info = COrders::getMerchant($merchant_id,Yii::app()->language);
		     $items = COrders::getItems();		     
		     $summary = COrders::getSummary();	
		     $order = COrders::orderInfo();		     
		     		     
		     try {
			     $order_id = COrders::getOrderID();		     
			     $refund_transaction = COrders::getPaymentTransactionList(Yii::app()->user->id,$order_id,array(
			       'paid'
			     ),array(
			       'refund',
			       'partial_refund'
			     ));					     
		     } catch (Exception $e) {
		     	//echo $e->getMessage(); die();
		     }
		     
		     $label = array(		       
		       'your_order_from'=>t("Your order from"),
		       'summary'=>t("Summary"),	
		       'track'=>t("Track"),
		       'buy_again'=>t("Buy again"),
		     );
		     
		     $data = array(
		       'merchant'=>$merchant_info,
		       'order'=>$order,
		       'items'=>$items,
		       'summary'=>$summary,	
		       'label'=>$label,
		       'refund_transaction'=>$refund_transaction,
		     );		     
		    		     		     
		     $this->code = 1; $this->msg = "ok";
		     $this->details = array(			 		      
		       'data'=>$data,		      
		     );
		     		     		     		     		
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		   
		}	
		$this->responseJson();
	}
	
	public function actionorderBuyAgain()
	{	
		try {
		    $current_cart_uuid = isset($this->data['cart_uuid'])?trim($this->data['cart_uuid']):'';
		    CCart::clear($current_cart_uuid);
		} catch (Exception $e) {
			//
		}
		
		try {
			
		   $order_uuid = isset($this->data['order_uuid'])?trim($this->data['order_uuid']):'';		   		  
		   
		   COrders::$buy_again = true;
		   COrders::getContent($order_uuid,Yii::app()->language);
		   $merchant_id = COrders::getMerchantId($order_uuid);
		   $items = COrders::getItems();
		   
		   $merchant_info = COrders::getMerchant($merchant_id,Yii::app()->language);
		   $restaurant_url = isset($merchant_info['restaurant_url'])?$merchant_info['restaurant_url']:'';
		   	 
		   $cart_uuid = CCart::addOrderToCart($merchant_id,$items);
		   
		   $transaction_type = COrders::orderTransaction($order_uuid,$merchant_id,Yii::app()->language);
		   CCart::savedAttributes($cart_uuid,Yii::app()->params->local_transtype,$transaction_type);	
		   CCart::savedAttributes($cart_uuid,'whento_deliver','now');
		   CommonUtility::WriteCookie( "cart_uuid_local" ,$cart_uuid);	
		   
		   $this->code = 1 ; $this->msg = "OK";			
	       $this->details = array(
	         'cart_uuid'=>$cart_uuid,
	         'restaurant_url'=>$restaurant_url
	       );			   
		   
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		 		    
		}	
		$this->responseJson();
	}
	
	public function actioncancelOrderStatus()
	{
		try {

			$order_uuid = isset($this->data['order_uuid'])?trim($this->data['order_uuid']):'';			
			$resp = COrders::getCancelStatus($order_uuid);					
			$this->code = 1;
		    $this->msg = "OK";
		    $this->details = $resp;
			
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}
	
	public function actionapplycancelorder()
	{
		try {			
			$order_uuid = isset($this->data['order_uuid'])?trim($this->data['order_uuid']):'';
			$order = COrders::get($order_uuid);
			$resp = COrders::getCancelStatus($order_uuid);			
			
			$cancel = AR_admin_meta::getValue('status_cancel_order');			
			$cancel_status = isset($cancel['meta_value'])?$cancel['meta_value']:'cancelled';
			
			$reason = "Customer cancel this order";
			
			if($resp['payment_type']=="online"){
				if($resp['cancel_status']==1 && $resp['refund_status']=="full_refund"){
					// FULL REFUND
					$order->scenario = "cancel_order";
					if($order->status==$cancel_status){
						$this->msg = t("This order has already been cancelled");
				        $this->responseJson();
					}					
					$order->status = $cancel_status;					
			        $order->remarks = $reason;
					if($order->save()){
					   $this->code = 1;
					   $this->msg = t("Your order is now cancel. your refund is on its way.");			   
					   if(!empty($reason)){
					   	  COrders::savedMeta($order->order_id,'rejetion_reason',$reason);
					   }			   
					} else $this->msg = CommonUtility::parseError( $order->getErrors());
					
				} elseif ( $resp['cancel_status']==1 && $resp['refund_status']=="partial_refund" ){
					///PARTIAL REFUND
					$refund_amount = floatval($resp['refund_amount']);
					$order->scenario = "customer_cancel_partial_refund";
					
					$model = new AR_ordernew_summary_transaction;
					$model->scenario = "refund";
					$model->order = $order;
					$model->order_id = $order->order_id;
					$model->transaction_description = "Refund";
					$model->transaction_amount = floatval($refund_amount);
					
					if($model->save()){					
						$order->status = $cancel_status;
						$order->remarks = $reason;
						if($order->save()){
						   $this->code = 1;
						   $this->msg = t("Your order is now cancel. your partial refund is on its way.");			   
						   if(!empty($reason)){
						   	  COrders::savedMeta($order->order_id,'rejetion_reason',$reason);
						   }			   
						} else $this->msg = CommonUtility::parseError( $order->getErrors());					
					} else $this->msg = CommonUtility::parseError( $order->getErrors());
										
				} else {
					//REFUND NOT AVAILABLE
					$this->msg = $resp['cancel_msg'];
				}
			} else {				
				if($resp['cancel_status']==1 && $resp['refund_status']=="full_refund"){
					//CANCEL ORDER
					$order->scenario = "cancel_order";
					if($order->status==$cancel_status){
						$this->msg = t("This order has already been cancelled");
				        $this->responseJson();
					}					
					$order->status = $cancel_status;
					$reason = "Customer cancell this order";
			        $order->remarks = $reason;
					if($order->save()){
					   $this->code = 1;
					   $this->msg = t("Your order is now cancel.");			   
					   if(!empty($reason)){
					   	  COrders::savedMeta($order->order_id,'rejetion_reason',$reason);
					   }			   
					} else $this->msg = CommonUtility::parseError( $order->getErrors());
					
				} else {
					$this->msg = $resp['cancel_msg'];
				}
			}						
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}
	
	public function actiongetAddressAttributes()
	{
		try {			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(			
			  'delivery_option'=>CCheckout::deliveryOption(),
			  'address_label'=>CCheckout::addressLabel(),
			  //'maps_config'=>CMaps::config(),
			  'default_atts'=>CCheckout::defaultAttrs()
			);				
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());	
		}
		$this->responseJson();
	}
	
	public function actiongetAddresses()
	{				
		if(!Yii::app()->user->isGuest){
			if ( $data = CClientAddress::getAddresses(Yii::app()->user->id)){
				$this->code = 1;
				$this->msg = "OK";
				$this->details = array(
				  'data'=>$data
				);			
			} else $this->msg[] = t("No results");
		} else $this->msg = "not login";
		$this->responseJson();
	}
	
	public function actiongetAdddress()
	{
		try {	
			
		   $address_uuid = isset($this->data['address_uuid'])?trim($this->data['address_uuid']):'';
		   $data = CClientAddress::find(Yii::app()->user->id,$address_uuid);
		   $this->code = 1;
		   $this->msg = "OK";
		   $this->details = array(
		     'data'=>$data
		   );		  		   
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());	
		}
		$this->responseJson();
	}	
	
	public function actionSaveAddress()
	{
		try {	
			
			$update = false;		
			$address_uuid = isset($this->data['address_uuid'])?trim($this->data['address_uuid']):'';			
			$set_place_id = isset($this->data['set_place_id'])?($this->data['set_place_id']):false;
			$data =  isset($this->data['data'])?$this->data['data']:array();
			
			$model = AR_client_address::model()->find('address_uuid=:address_uuid AND client_id=:client_id', 
		    array(':address_uuid'=>$address_uuid,'client_id'=>Yii::app()->user->id)); 
		    if(!$model){		    	
		    	$model = new AR_client_address;
		    	$model->client_id = intval(Yii::app()->user->id);
		    	$model->address_uuid = CommonUtility::generateUIID();		    	
		    	$model->place_id = isset($data['place_id'])?$data['place_id']:'';
		    	$model->country = isset($data['address']['country'])?$data['address']['country']:'';
		    	$model->country_code = isset($data['address']['country_code'])?$data['address']['country_code']:'';
		    } 
		    
		    $model->location_name = isset($this->data['location_name'])?$this->data['location_name']:'';
	    	$model->delivery_instructions = isset($this->data['delivery_instructions'])?$this->data['delivery_instructions']:'';
	    	$model->delivery_options = isset($this->data['delivery_options'])?$this->data['delivery_options']:'';
	    	$model->address_label = isset($this->data['address_label'])?$this->data['address_label']:'';
	    	$model->latitude = isset($this->data['latitude'])?$this->data['latitude']:'';
	    	$model->longitude = isset($this->data['longitude'])?$this->data['longitude']:'';
	    	$model->address1 = isset($this->data['address1'])?$this->data['address1']:'';
	    	$model->formatted_address = isset($this->data['formatted_address'])?$this->data['formatted_address']:'';
	    	
	    	if($model->save()){
	    		$this->code = 1;
		    	$this->msg = "OK";	
		    	$this->details = array(
		    	  'place_id'=>$model->place_id
		    	);
		    	
		    	if($set_place_id=="true" || $set_place_id==true){
		    		CommonUtility::WriteCookie( Yii::app()->params->local_id ,$model->place_id );  
		    	}
		    	
	    	} else $this->msg = CommonUtility::parseError( $model->getErrors());
			
		} catch (Exception $e) {
			$this->msg[] = t($e->getMessage());				
		}
		$this->responseJson();
	}
	
	public function actionMyPayments()
	{
		try {
			
			$default_payment_uuid = '';
			$model = AR_client_payment_method::model()->find('client_id=:client_id AND as_default=:as_default', 
		    array(
		      ':client_id'=>Yii::app()->user->id,
		      ':as_default'=>1
		    )); 	
		    if($model){		    	
		    	$default_payment_uuid=$model->payment_uuid;
		    }
		    
			$data = CPayments::SavedPaymentList( Yii::app()->user->id , 0);
			
			$this->code = 1;
		    $this->msg = "ok";
		    $this->details = array(
		      'default_payment_uuid'=>$default_payment_uuid,
		      'data'=>$data,
		    );					
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}
	
	public function actiondeletePayment()
	{		
		try {
						
			$payment_uuid = isset($this->data['payment_uuid'])?trim($this->data['payment_uuid']):'';
			CPayments::delete(Yii::app()->user->id,$payment_uuid);
			$this->code = 1;
		    $this->msg = "ok";
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}	
		$this->responseJson();
	}
	
	public function actiongetCards()
	{
		try {
					   
		   $cc_id = isset($this->data['cc_id'])?trim($this->data['cc_id']):'';
		   $model = AR_client_cc::model()->find('client_id=:client_id AND cc_id=:cc_id', 
		   array(
		     ':client_id'=>Yii::app()->user->id,
		     ':cc_id'=>$cc_id,
		   )); 	
		   if($model){
		   			   	  
		   	  try {
					$card = CreditCardWrapper::decryptCard($model->encrypted_card);
			  } catch (Exception $e) {
					$card ='';
			  }		
			  			  
		   	  $data = array(
		   	    'card_uuid'=>$model->card_uuid,
		   	    'card_name'=>$model->card_name,
		   	    'credit_card_number'=>$card,
		   	    'expiry_date'=>$model->expiration_month."/".$model->expiration_yr,
		   	    'cvv'=>$model->cvv,
		   	    'billing_address'=>$model->billing_address,
		   	  );
		   	  $this->code = 1;
		   	  $this->msg = "OK";
		   	  $this->details = array('data'=>$data);		   	  
		   } else $this->msg[] = t("Record not found");
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    		    
		}	
		$this->responseJson();
	}
	
	public function actionPaymentMethod()
	{
		try {
			
		   $data = array();
		   $payment_type = isset($this->data['payment_type'])?trim($this->data['payment_type']):'';
		   $filter=array(
		     'payment_type'=>$payment_type
		   );
		   $data = CPayments::DefaultPaymentList();
		   
		   $this->code = 1;
		   $this->msg = "OK";		  
		   $this->details = array(
		     'data'=>$data
		   );		   
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    		    
		}	
		$this->responseJson();
	}
	
	public function actiongetSaveStore()
	{
		try {			
					   
		   if(!Yii::app()->user->isGuest){
			   $merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
			   $data = CSavedStore::getStoreReview($merchant_id,Yii::app()->user->id);
			   $this->code = 1;
			   $this->msg = "OK";		   
		   } else $this->msg = t("not login");
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		  		      		   
		}	
		$this->responseJson();
	}
	
	public function actionSaveStore()
	{
		try {			
					   
		   if(!Yii::app()->user->isGuest){
			   $merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
			   
			   $model = AR_favorites::model()->find('fav_type=:fav_type AND merchant_id=:merchant_id AND client_id=:client_id', 
		       array(
				   ':fav_type'=>"restaurant",
				   ':merchant_id'=>$merchant_id ,
				   'client_id'=> Yii::app()->user->id  
				)); 		
		       
		       if($model){
		       	  $model->delete();
		       	  $this->code = 1;
				  $this->msg = "OK";	
				  $this->details = array('found'=>false);
		       } else {			   
				   $model = new AR_favorites;
				   $model->client_id = Yii::app()->user->id;
				   $model->merchant_id = $merchant_id;
				   if($model->save()){
				   	  $this->code = 1;
				      $this->msg = "OK";	
				      $this->details = array('found'=>true);	   
				   } else $this->msg = CommonUtility::parseModelErrorToString( $model->getErrors());
		       }
		   } else $this->msg = t("You must login to save this store");
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		  		      		   
		}	
		$this->responseJson();
	}
	
	public function actionsaveStoreList()
	{
		try {	
			
		   $data = CSavedStore::Listing( Yii::app()->user->id );		   
		   $services = CSavedStore::services( Yii::app()->user->id  );
		   $estimation = CSavedStore::estimation( Yii::app()->user->id  );					   
		   $this->code = 1;
		   $this->msg = "Ok";		   
		   $this->details = array(
		     'data'=>$data,
		     'services'=>$services,
		     'estimation'=>$estimation
		   );		   				   
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		  		      		   		   
		}
		$this->responseJson();
	}
	
	public function actionGetFeaturedLocation()
	{		
		try {
			
			$featured_name = isset($this->data['featured_name'])?trim($this->data['featured_name']):0;		
				
			$location_details = CFeaturedLocation::Details($featured_name);
			$data = CFeaturedLocation::Listing($featured_name, Yii::app()->language );
			$services = CFeaturedLocation::services( $featured_name );	
			$estimation = CFeaturedLocation::estimation( $featured_name );			
			
			$this->code = 1;
		    $this->msg = "Ok";
		    $this->details = array(
		      'location_details'=>$location_details,
		      'data'=>$data,
		      'services'=>$services,
		      'estimation'=>$estimation
		    );		   			    
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}
		$this->responseJson();
	}
	
	public function actiongetServices()
	{		
		$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';				
		try {

			$transaction_type = CServices::getSetService($cart_uuid);			
			$data = CServices::Listing(  Yii::app()->language );			
			if(!array_key_exists($transaction_type,(array)$data)){				
				$keys = array_keys($data);
				$transaction_type = isset($keys[0])?$keys[0]:$transaction_type;
			}			
			$this->code = 1; $this->msg = "OK";
			$this->details = array(
			  'transaction_type'=>$transaction_type,
			  'data'=>$data
			);						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}
		$this->responseJson();
	}
	
	public function actionsetTransactionType()
	{		
		try {
			
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$transaction_type = isset($this->data['transaction_type'])?$this->data['transaction_type']:'';
			if(empty($cart_uuid)){
				$cart_uuid = CommonUtility::generateUIID();			
			}		
			CCart::savedAttributes($cart_uuid,Yii::app()->params->local_transtype,$transaction_type);
			CommonUtility::WriteCookie( "cart_uuid_local" ,$cart_uuid);
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'cart_uuid'=>$cart_uuid,
			  'transaction_type'=>$transaction_type
			);
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    
		}
		$this->responseJson();
	}
	
	public function actionTransactionInfo()
	{
		try {
			
			$whento_deliver = ''; $delivery_datetime='';
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$local_id = CommonUtility::getCookie(Yii::app()->params->local_id);			
			$local_info = CMaps::locationDetails($local_id,'');			
			
			$delivery_option = CCheckout::deliveryOptionList();
												
			$data = isset($this->data['choosen_delivery'])?$this->data['choosen_delivery']:'';
			
			if(is_array($data) && count($data)>=1){				
				$whento_deliver = isset($data['whento_deliver'])?$data['whento_deliver']:'now';
				$delivery_date = isset($data['delivery_date'])?$data['delivery_date']:date("Y-m-d");
				$delivery_time = isset($data['delivery_time'])?$data['delivery_time']:'';				
				$delivery_datetime = CCheckout::jsonTimeToFormat2($delivery_date,json_encode($delivery_time));
			} else {
				$whento_deliver = CCheckout::getWhenDeliver($cart_uuid);
				$delivery_datetime = CCheckout::getScheduleDateTime($cart_uuid,$whento_deliver);				
			}
									
			$this->code = 1; $this->msg ="ok";
			$this->details = array(
			  'address1'=>$local_info['address']['address1'],
			  'formatted_address'=>$local_info['address']['formatted_address'],
			  'delivery_option'=>$delivery_option,
			  'whento_deliver'=>$whento_deliver,
			  'delivery_datetime'=>$delivery_datetime,
			);			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    		    
		}
		$this->responseJson();
	}
	
	public function actiongetDeliveryTimes()
	{
		try {
			
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
			
			$delivery_option = CCheckout::deliveryOptionList();
			$whento_deliver = CCheckout::getWhenDeliver($cart_uuid);
						
			$model = AR_opening_hours::model()->find("merchant_id=:merchant_id",array(
			  ':merchant_id'=>$merchant_id
			));
			if(!$model){
				$this->msg[] = t("Merchant has not set time opening yet");
				$this->responseJson();
			}			
			
			$opening_hours = CMerchantListingV1::openHours($merchant_id);		
			$delivery_date = ''; $delivery_time='';

			if($atts = CCart::getAttributesAll($cart_uuid,array('delivery_date','delivery_time'))){				
				$delivery_date = isset($atts['delivery_date'])?$atts['delivery_date']:'';
				$delivery_time = isset($atts['delivery_time'])?$atts['delivery_time']:'';
			}
						
			$this->code = 1; $this->msg = "ok";			
		    $this->details = array(		     
		       'delivery_option'=>$delivery_option,
		       'whento_deliver'=>$whento_deliver,
		       'delivery_date'=>$delivery_date,
		       'delivery_time'=>$delivery_time,
		       'opening_hours'=>$opening_hours,		       
		    );
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    		    
		}
		$this->responseJson();
	}
	
	public function actiongetCDeliveryTimes()
	{
		try {
			
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):0;
			$type = isset($this->data['type'])?$this->data['type']:'delivery';
			$delivery_option = CCheckout::deliveryOptionList();
			$whento_deliver = CCheckout::getWhenDeliver($cart_uuid);
						
			$model = AR_opening_hours::model()->find("merchant_id=:merchant_id",array(
			  ':merchant_id'=>$merchant_id
			));
			if(!$model){
				$this->msg[] = t("Merchant has not set time opening yet");
				$this->responseJson();
			}			
			
			if($type == 'pickup'){
				$opening_hours = CMerchantListingV1::pickupOpenHours($merchant_id);
			}else{
				$opening_hours = CMerchantListingV1::openHours($merchant_id);
			}
			$delivery_date = ''; $delivery_time='';

			if($atts = CCart::getAttributesAll($cart_uuid,array('delivery_date','delivery_time'))){				
				$delivery_date = isset($atts['delivery_date'])?$atts['delivery_date']:'';
				$delivery_time = isset($atts['delivery_time'])?$atts['delivery_time']:'';
			}
						
			$this->code = 1; $this->msg = "ok";			
		    $this->details = array(		     
		       'delivery_option'=>$delivery_option,
		       'whento_deliver'=>$whento_deliver,
		       'delivery_date'=>$delivery_date,
		       'delivery_time'=>$delivery_time,
		       'opening_hours'=>$opening_hours,		       
		    );
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    		    
		}
		$this->responseJson();
	}
	
	public function actionsaveTransactionInfo()
	{
		try {
						
			$cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$whento_deliver = isset($this->data['whento_deliver'])?$this->data['whento_deliver']:'';
			$delivery_date = isset($this->data['delivery_date'])?$this->data['delivery_date']:'';
			$delivery_time = isset($this->data['delivery_time'])?$this->data['delivery_time']:'';
						
			CCart::savedAttributes($cart_uuid,'whento_deliver',$whento_deliver);			  
			CCart::savedAttributes($cart_uuid,'delivery_date',$delivery_date);
			CCart::savedAttributes($cart_uuid,'delivery_time',json_encode($delivery_time));
								
			$delivery_datetime = CCheckout::jsonTimeToFormat2($delivery_date,json_encode($delivery_time));
			
			$this->code = 1; $this->msg = "OK";
			$this->details = array(
			  'whento_deliver'=>$whento_deliver,
			  'delivery_date'=>$delivery_date,
			  'delivery_time'=>$delivery_time,
			  'delivery_datetime'=>$delivery_datetime,			  
			);						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    		    
		}
		$this->responseJson();
	}
	
	public function actionsaveCTransactionInfo()
	{
		try {
			$cart_uuid = CommonUtility::getCookie("cart_uuid_local");
			// $cart_uuid = isset($this->data['cart_uuid'])?$this->data['cart_uuid']:'';
			$whento_deliver = isset($this->data['whento_deliver'])?$this->data['whento_deliver']:'';
			$delivery_date = isset($this->data['delivery_date'])?$this->data['delivery_date']:'';
			$delivery_time = isset($this->data['delivery_time'])?json_decode($this->data['delivery_time'],1):'';
						
			CCart::savedAttributes($cart_uuid,'whento_deliver',$whento_deliver);			  
			CCart::savedAttributes($cart_uuid,'delivery_date',$delivery_date);
			CCart::savedAttributes($cart_uuid,'delivery_time',json_encode($delivery_time));
								
			$delivery_datetime = CCheckout::jsonTimeToFormat2($delivery_date,json_encode($delivery_time));
			
			$this->code = 1; $this->msg = "OK";
			$this->details = array(
			  'whento_deliver'=>$whento_deliver,
			  'delivery_date'=>$delivery_date,
			  'delivery_time'=>$delivery_time,
			  'delivery_datetime'=>$delivery_datetime,			  
			);						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    		    
		}
		$this->responseJson();
	}
	
	public function actiongetSearchSuggestion()
	{
		try {
			
			$q = isset($this->data['q'])?$this->data['q']:'';
			$local_info = '';
			try {
			   $local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
		       $local_info = CMerchantListingV1::getLocalID($local_id);				
		    } catch (Exception $e) {
		    	//
		    }
		    
			$filter = array(			
			  'search'=>$q,
			  'lat'=>$local_info?$local_info->latitude:'',
			  'lng'=>$local_info?$local_info->longitude:'',
			  'unit'=>Yii::app()->params['settings']['home_search_unit_type'],
			  'page'=>0,
			  'limit'=>Yii::app()->params->list_limit,
			);			
			$data = CMerchantListingV1::searchSuggestion($filter , Yii::app()->language );
			$this->code = 1; $this->msg = "OK";
			$this->details = array(
			  'data'=>$data
			);			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    		    
		}
		$this->responseJson();		
	}

	public function actiongetsearchsuggestionv1()
	{		
		try {

			$q = Yii::app()->input->post('q');
			$category = Yii::app()->input->post('category');			

			$local_info = '';
			try {
			   $local_id = CommonUtility::getCookie(Yii::app()->params->local_id);
		       $local_info = CMerchantListingV1::getLocalID($local_id);				
		    } catch (Exception $e) {
		    	//
		    }

			$filter = array(			
			  'search'=>$q,
			  'lat'=>$local_info?$local_info->latitude:'',
			  'lng'=>$local_info?$local_info->longitude:'',
			  'unit'=>Yii::app()->params['settings']['home_search_unit_type'],
			  'page'=>0,
			  'limit'=>Yii::app()->params->list_limit,
			);		
			
			if($category=="restaurant"){			   					
				$data = CMerchantListingV1::searchSuggestion($filter , Yii::app()->language );
				$this->code = 1; $this->msg = "OK";
				$this->details = array( 'data'=>$data);							
			} else {
				$data = CMerchantListingV1::searchSuggestionFood($filter , Yii::app()->language );				
				$this->code = 1; $this->msg = "OK";
				$this->details = array( 'data'=>$data);							
			}


		} catch (Exception $e) {
			$this->msg = t($e->getMessage());		    		    			
		}		
		$this->responseJson();	
	}
		
	public function actiongetSignupAttributes()
	{
		try {
			
			$capcha = Yii::app()->params['settings']['merchant_enabled_registration_capcha'];			
			$program = Yii::app()->params['settings']['registration_program'];			
			$program = !empty($program)?json_decode($program,true):false;
			
			$membership_list = array();
			$mobile_prefixes = AttributesTools::countryMobilePrefix(); 
			try {
			    $membership_list = CMerchantSignup::membershipProgram( Yii::app()->language , (array)$program );	
			} catch (Exception $e) {
				//
			}
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'capcha'=>$capcha==1?true:false,
			  'mobile_prefixes'=>$mobile_prefixes,
			  'membership_list'=>$membership_list
			);			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    		    
		}
		$this->responseJson();
	}
	
	public function actiongetLocationCountries()
	{
		try {
			
			$default_country = isset($this->data['default_country'])?$this->data['default_country']:'';
			$only_countries = isset($this->data['only_countries'])?(array)$this->data['only_countries']:array();
			$filter = array(
			  'only_countries'=>(array)$only_countries
			);
			
			$data = ClocationCountry::listing($filter);
			$default_data = ClocationCountry::get($default_country);			
			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(
			  'data'=>$data,
			  'default_data'=>$default_data,			  
			);			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		    		    
		}
		$this->responseJson();		
	}
	
	public function actionCreateAccountMerchant()
	{				
		$model = new AR_merchant;
		$model->scenario = 'website_registration';
		$model->restaurant_name = isset($this->data['restaurant_name'])?$this->data['restaurant_name']:'';
		$model->address = isset($this->data['address'])?$this->data['address']:'';
		$model->contact_email = isset($this->data['contact_email'])?$this->data['contact_email']:'';
		$mobile_prefix = isset($this->data['mobile_prefix'])?$this->data['mobile_prefix']:'';
		$model->contact_phone = isset($this->data['mobile_number'])?$mobile_prefix.$this->data['mobile_number']:'';
		$model->merchant_type = isset($this->data['membership_type'])? intval($this->data['membership_type']) :0;	
				
		if($program = CMerchantSignup::get($model->merchant_type)){								
			if($program->type_id==2){
				$model->commision_type = trim($program->commision_type);
				$model->percent_commision = floatval($program->commission);
				$model->commision_based = $program->based_on;				
			}
		}		
				
		if ($model->save()){
			$this->code = 1; $this->msg = t("Registration successful");
							
			$redirect = Yii::app()->createAbsoluteUrl("merchant/user-signup/?uuid=".$model->merchant_uuid);
			
			$this->details = array(
			  'redirect'=>$redirect
			);
		} else {							
			if ( $error = CommonUtility::parseError( $model->getErrors()) ){
				$this->msg = $error;
			} else $this->msg[] = array('invalid error');				
		}			
		$this->responseJson();		
	}

	public function actioncreateMerchantUser()
	{
		try {
					   
		   $merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';
		   $merchant = CMerchants::getByUUID($merchant_uuid);
		   
		   $model =  AR_merchant_user::model()->find("merchant_id=:merchant_id AND main_account=:main_account",array(
		    ':merchant_id'=>intval($merchant->merchant_id),
		    ':main_account'=>1
		   ));
		   
		   if(!$model){
		   	   $model = new AR_merchant_user;
		   	   $model->scenario = 'register';
		   }
		   		   		   
		   
		   $model->username = isset($this->data['username'])?$this->data['username']:'';
		   $model->password = isset($this->data['password'])?trim($this->data['password']):'';
		   $model->new_password = isset($this->data['password'])?trim($this->data['password']):'';
		   $model->repeat_password = isset($this->data['cpassword'])?trim($this->data['cpassword']):'';
		   
		   if($model->scenario=="update"){
		   	  $model->password = md5($model->password);		   	  
		   }
		   
		   $model->first_name = isset($this->data['first_name'])?$this->data['first_name']:'';
		   $model->last_name = isset($this->data['last_name'])?$this->data['last_name']:'';
		   $model->contact_email = isset($this->data['contact_email'])?$this->data['contact_email']:'';
		   $mobile_prefix = isset($this->data['mobile_prefix'])?$this->data['mobile_prefix']:'';
		   $model->contact_number = isset($this->data['mobile_number'])?$mobile_prefix.$this->data['mobile_number']:'';		   
		   $model->merchant_id = $merchant->merchant_id;
		   $model->main_account = 1;		   
		   if($model->save()){
		   	   $this->code = 1;
		   	   $this->msg = t("Registration successful");				
		   	   
		   	   $redirect = '';
			   if($merchant->merchant_type==1){
					$redirect = Yii::app()->createAbsoluteUrl("merchant/choose_plan",array(
					  'uuid'=>$merchant->merchant_uuid
					));
			   } elseif ($merchant->merchant_type==2){
					$redirect = Yii::app()->createAbsoluteUrl("merchant/getbacktoyou");
			   }
		   	   
			   $this->details = array(
				  'redirect'=>$redirect
			   );			
		   } else $this->msg =  CommonUtility::parseError( $model->getErrors());
		   
		} catch (Exception $e) {
		   $this->msg = t($e->getMessage());		
		}
		$this->responseJson();	
	}
	
	public function actiongetPlan()
	{
		try {
			
			$details = array();
			$merchant_uuid = isset($this->data['merchant_uuid'])?$this->data['merchant_uuid']:'';
						
			$data = CPlan::listing( Yii::app()->language );			
			try {
			    $details = CPlan::Details();		
			} catch (Exception $e) {
				//
			}			
			$this->code = 1;
			$this->msg = "OK";
			$this->details = array(			
			  'data'=>$data,
			  'plan_details'=>$details,			  
			);									
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());		
		}
		$this->responseJson();	
	}
	
	public function actionPaymenPlanList()
	{
		 try {
		 	
		 	$payment_list = AttributesTools::PaymentPlansProvider(); 
		 	$this->code = 1;
		 	$this->msg = "ok";
		 	$this->details = $payment_list; 
		 	
		 } catch (Exception $e) {
		    $this->msg = t($e->getMessage());		
		 }
		 $this->responseJson();	
	}
	
	public function actionverifyRecaptcha()
	{
		try {
			
			$options = OptionsTools::find(array('captcha_secret'));
			$secret = isset($options['captcha_secret'])?$options['captcha_secret']:'';						
			$recaptcha_response = isset($this->data['recaptcha_response'])?$this->data['recaptcha_response']:'';			
			$resp = CRecaptcha::verify($secret,$recaptcha_response);
			
			$this->code = 1;
			$this->msg = "ok";
			
		} catch (Exception $e) {
		    $this->msg[] = $e->getMessage();
		    $err = CRecaptcha::getError();		    
		    if($err == "timeout-or-duplicate"){
		    	$this->code = 3;
		    }
		}
		$this->responseJson();	
	}
	
	public function actionRegistrationPhone()
	{		
		
		$capcha = false;
		if(isset(Yii::app()->params['settings']['captcha_customer_signup'])){
		   $capcha = Yii::app()->params['settings']['captcha_customer_signup']==1?true:false;
		}
		$recaptcha_response = isset($this->data['recaptcha_response'])?$this->data['recaptcha_response']:'';				
				
		try {
						
			$digit_code = CommonUtility::generateNumber(5);
		    $mobile_number = isset($this->data['mobile_number'])?$this->data['mobile_number']:'';
		    $mobile_prefix = isset($this->data['mobile_prefix'])?$this->data['mobile_prefix']:'';		    
		    $mobile_number = $mobile_prefix.$mobile_number;
		    		    
		    $model = AR_clientsignup::model()->find('contact_phone=:contact_phone', 
		    array(':contact_phone'=>$mobile_number)); 
		    if(!$model){		    	
		    	$model = new AR_clientsignup;		
		    	$model->capcha = $capcha;
			    $model->recaptcha_response = $recaptcha_response;	
		    	$model->scenario = 'registration_phone';
		    	$model->phone_prefix = $mobile_prefix;
		    	$model->contact_phone = $mobile_number;
		    	$model->mobile_verification_code = $digit_code;
		    	$model->status='pending';
				$model->merchant_id = 0;
				
		    	if ($model->save()){
		    		$this->code = 1;
		    		$this->msg = "OK";
		    		$this->details = array(
		    		  'client_uuid'=>$model->client_uuid
		    		);		    	
		    		if(DEMO_MODE==TRUE){
		    			$this->details['verification_code']=t("Your verification code is {{code}}",array('{{code}}'=>$digit_code));
		    		}
		    	} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    } else {
		    	if($model->status=='pending'){		    		
		    		$model->scenario = 'registration_phone';
		    		$model->capcha = $capcha;
			        $model->recaptcha_response = $recaptcha_response;	
		    		$model->mobile_verification_code = $digit_code;
		    		if ($model->save()){
			    		$this->code = 1;
			    		$this->msg = "OK";
			    		$this->details = array(
			    		  'client_uuid'=>$model->client_uuid
			    		);			    	
			    		if(DEMO_MODE==TRUE){
			    			$this->details['verification_code']=t("Your verification code is {{code}}",array('{{code}}'=>$digit_code));
			    		}			    				    	
		    		} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    		
		    	} else $this->msg[]  = t("Phone number already exist");		    	
		    }		    	
		    
		} catch (Exception $e) {
		    $this->msg[] = $e->getMessage();		    
		}
		$this->responseJson();	
	}
	
	public function actionverifyCode()
	{		
		try {
			
			$client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';
			$verification_code = isset($this->data['verification_code'])?intval($this->data['verification_code']):'';
			
			$redirect_to = isset($this->data['redirect_to'])?$this->data['redirect_to']:'';
			$auto_login = isset($this->data['auto_login'])?$this->data['auto_login']:'';
			
			$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		    array(':client_uuid'=>$client_uuid)); 
		    		    		   
		    if($model){
		    	$model->scenario = 'complete_standard_registration';
		    	if($model->mobile_verification_code==$verification_code){
		    		$model->account_verified = 1;
		    		
		    		if($auto_login==1){
		    			$model->status='active';
		    		}
		    				    		
		    		if($model->save()){
			    		$this->code = 1;
			    		$this->msg = "ok";		    		
			    		
			    		if($auto_login==1){
			    			$this->msg = t("Login successful");
			    			$this->details = array(
							  'redirect'=>!empty($redirect_to)?$redirect_to:Yii::app()->getBaseUrl(true)
							);			
							
							//AUTO LOGIN						
							$login=new AR_customer_autologin;
							$login->username = $model->email_address;
							$login->password = $model->password;
							$login->rememberMe = 1;
							if($login->validate() && $login->login() ){
								//
							} 
			    		}
		    		} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    		
		    	} else $this->msg[] = t("Invalid 6 digit code");
		    } else $this->msg[] = t("Records not found");
			
		} catch (Exception $e) {							
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();	
	}
	
	public function actioncompleteSignup()
	{
		try {
			
			$client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';
			$next_url = isset($this->data['next_url'])?$this->data['next_url']:'';
			
			$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		    array(':client_uuid'=>$client_uuid)); 
		    if($model){
		    	$model->scenario = 'complete_registration';
		    	if($model->account_verified==1){
			    	$model->first_name = isset($this->data['firstname'])?$this->data['firstname']:'';
			    	$model->last_name = isset($this->data['lastname'])?$this->data['lastname']:'';
			    	$model->email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
			    				    	
			    	$model->password = isset($this->data['password'])? trim($this->data['password']) :'';
			    	$model->cpassword = isset($this->data['cpassword'])? trim($this->data['cpassword']) :'';			    
			    	$password = isset($this->data['password'])? trim($this->data['password']) :'';
			    				    	
			    	$model->status='active';
			    	if ($model->save()){
			    		$this->code = 1;
			    		$this->msg = t("Registration successful");
			    		
			    		$redirect = !empty($next_url)?$next_url:Yii::app()->getBaseUrl(true);
			    		
			    		$this->details = array(
						  'redirect_url'=>$redirect
						);			
						
						//AUTO LOGIN
						$this->autoLogin($model->email_address,$password);
						
			    	} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    	} else $this->msg[] = t("Accout not verified");		    	
		    } else $this->msg[] = t("Records not found");
		} catch (Exception $e) {
		    $this->msg[] = $e->getMessage();		    
		}
		$this->responseJson();	
	}
	
	public function actionSocialRegister()
	{		
		try {
									
			$digit_code = CommonUtility::generateNumber(5);
			$redirect_to = isset($this->data['redirect_to'])?$this->data['redirect_to']:'';
			$email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
			$id = isset($this->data['id'])?$this->data['id']:'';			
			$verification = isset($this->data['verification'])?$this->data['verification']:'';	
			$social_strategy = isset($this->data['social_strategy'])?$this->data['social_strategy']:'';	
			$social_token = isset($this->data['social_token'])?$this->data['social_token']:'';	
												
			$model = AR_clientsignup::model()->find('email_address=:email_address', 
		    array(':email_address'=>$email_address)); 
		    if(!$model){
		    	$model = new AR_clientsignup;		
		    	$model->scenario = 'registration_social';		    	
		    	$model->social_token = $social_token;
		    	$model->email_address = $email_address;
		    	$model->password = $id;		    	
		    	$model->social_id = $id;
		    	$model->first_name = isset($this->data['first_name'])?$this->data['first_name']:'';
		    	$model->last_name = isset($this->data['last_name'])?$this->data['last_name']:'';
		    	$model->mobile_verification_code = $digit_code;
		    	$model->status = $verification==1?'pending':'active';
		    	$model->social_strategy = $social_strategy;		    	
		    	$model->account_verified  = $verification==1?0:1;
				$model->merchant_id = 0;
		    	
		    	if ($model->save()){			    					    	
		    		$this->SocialRegister($verification,$model,$redirect_to);
		    	} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    } else {		    	
		    	$model->scenario = 'social_login';		
		    	$model->social_strategy = $social_strategy;	
		    	$model->social_token = $social_token;    		    	
		    	if($model->status=='pending' && $model->social_id==$id){
		    		$model->mobile_verification_code = $digit_code;
		    		if ($model->save()){
		    			$this->SocialRegister($verification,$model,$redirect_to);
		    		} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    	} elseif ( $model->status=="active" ){		 
		    				    	
		    		$model->password = md5($id);	
		    		if ($model->save()){
		    			
		    			//AUTO LOGIN
			    		$this->autoLogin($model->email_address,$id);			    		
			    		
			    		$this->code = 1;
			    		$this->msg = t("Login successful");
						$this->details = array(
						  'redirect'=>!empty($redirect_to)?$redirect_to:Yii::app()->getBaseUrl(true)
						);			
		    		} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    	} else $this->msg[] = t("Your account is {{status}}",array('{{status}}'=> t($model->status) ) );
		    }
			
		} catch (Exception $e) {
		    $this->msg[] = $e->getMessage();		    
		}		
		$this->responseJson();
	}
	
	private function SocialRegister($verification='',$model ,$redirect_to='')
	{
		$this->code = 1;			
		$redirect='';
				
		if($verification==1){
			// SEND EMAIL CODE
			$this->msg = t("Please wait until we redirect you");				
			
			$redirect = Yii::app()->createUrl("/account/verification",array(
			  'uuid'=>$model->client_uuid,
			  'redirect_to'=>$redirect_to
			));
		
		} else {			
			
			$this->msg = t("Login successful");
			$redirect = Yii::app()->createUrl("/account/complete_registration",array(
			  'uuid'=>$model->client_uuid,
			  'redirect_to'=>$redirect_to
			));						
		}
		$this->details = array(		    		  
		  'redirect'=>$redirect
		);
	}
		
	private function autoLogin($username='',$password='')
	{		
		$login=new AR_customer_login;
		$login->username = $username;
		$login->password = $password;
		$login->rememberMe = 1;
		if($login->validate() && $login->login() ){
			//echo 'ok';
		} //else dump( $model->getErrors() );			
	}
	
	public function actiongetCustomerInfo()
	{
		try {
			
			$client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';
			$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		    array(':client_uuid'=>$client_uuid)); 
		    if($model){
		    	$this->code = 1;
		    	$this->msg  = "Ok";
		    	$this->details = array(
		    	  'firstname'=>$model->first_name,
		    	  'lastname'=>$model->last_name,
		    	  'email_address'=>$model->email_address,
		    	);
		    } else $this->msg[] = t("Records not found");						
		} catch (Exception $e) {
		    $this->msg[] = $e->getMessage();		    
		}
		$this->responseJson();	
	}
	
	public function actioncompleteSocialSignup()
	{
		try {
		    						
			$client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';
			$next_url = isset($this->data['next_url'])?$this->data['next_url']:'';
			$prefix = isset($this->data['mobile_prefix'])?$this->data['mobile_prefix']:'';
		    $mobile_number = isset($this->data['mobile_number'])?$this->data['mobile_number']:'';
		    		   
			$model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		    array(':client_uuid'=>$client_uuid)); 
		    if($model){
		    	$model->scenario = 'complete_social_registration';
		    	$password = $model->social_id;
		    	if($model->account_verified==1){
		    		$model->first_name = isset($this->data['firstname'])?$this->data['firstname']:'';
			    	$model->last_name = isset($this->data['lastname'])?$this->data['lastname']:'';
		    		$model->contact_phone = $prefix.$mobile_number;
		    		$model->phone_prefix = $prefix;		    		
		    		$model->status='active';
		    		if ($model->save()){
		    			
		    			$this->code = 1;
			    		$this->msg = t("Registration successful");
			    		
			    		$redirect = !empty($next_url)?$next_url:Yii::app()->getBaseUrl(true);
			    		
			    		$this->details = array(
						  'redirect_url'=>$redirect
						);			
						
						//AUTO LOGIN
						$this->autoLogin($model->email_address,$password);
						
		    		} else $this->msg = CommonUtility::parseError( $model->getErrors() );		    		
		    	} else $this->msg[] = t("Accout not verified");	
		    } else $this->msg[] = t("Records not found");			
		} catch (Exception $e) {
		    $this->msg[] = $e->getMessage();		    
		}
		$this->responseJson();	
	}
	
	public function actionrequestCode()
	{
		try {
			
		   $client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';
		   
		   $model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		   array(':client_uuid'=>$client_uuid)); 
		   if($model){
		   	  $digit_code = CommonUtility::generateNumber(5);
		   	  $model->mobile_verification_code = $digit_code;
			  $model->scenario = 'resend_otp';
		   	  if($model->save()){	
		   	  	 
		   	  	   // SEND EMAIL HERE  
		   	  	   	   	  	
		   	  	   $this->code = 1;
		           $this->msg = t("We sent a code to {{email_address}}.",array(
		             '{{email_address}}'=> CommonUtility::maskEmail($model->email_address)
		           ));			          
		   	  } else $this->msg = CommonUtility::parseError($model->getErrors());		   	  
		   } else $this->msg[] = t("Records not found");
		   
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();	
	}
	
	public function actionrequestCodePhone()
	{
		try {
			
		   $client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';		   
		   
		   $model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		   array(':client_uuid'=>$client_uuid)); 
		   if($model){
		   	  $digit_code = CommonUtility::generateNumber(5);
		   	  $model->scenario = 'resend_otp';
		   	  $model->mobile_verification_code = $digit_code;
		   	  if($model->save()){	
		   	  	 		   	  	   	   	  
		   	  	   $this->code = 1;
		           $this->msg = t("We sent a code to +[contact_phone].",array(
		             '[contact_phone]'=> $model->contact_phone
		           ));			          
		   	  } else $this->msg = CommonUtility::parseError($model->getErrors());		   	  
		   } else $this->msg[] = t("Records not found");
		   
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();	
	}
	
	public function actionrequestResetPassword()
	{
		try {
			
			$email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
			//AR_client
			$model = AR_clientsignup::model()->find('email_address=:email_address', 
		    array(':email_address'=>$email_address)); 
		    if($model){
		    	if($model->status=="active"){
		    		$model->scenario = "reset_password";
		    		$model->reset_password_request = 1;
		    		if($model->save()){											
						$this->code = 1;
						$this->msg = t("Check {{email_address}} for an email to reset your password.",array(
						'{{email_address}}'=>$model->email_address
						));
						$this->details = array(
						'uuid'=>$model->client_uuid
						);
					} else {
						$this->msg = CommonUtility::parseError($model->getErrors());
					}							    				    	
		    	} else $this->msg[] = t("Your account is either inactive or not verified.");
		    } else $this->msg[] = t("No email address found in our records. please verify your email.");
			
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();	
	}

	public function actionresendResetEmail()
	{
		try {
			
		   $client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';
		   
		   $model = AR_clientsignup::model()->find('client_uuid=:client_uuid', 
		   array(':client_uuid'=>$client_uuid)); 
		   if($model){		   	  
			  $model->scenario = "reset_password";
		   	  $model->reset_password_request = 1;		    		
		   	  if($model->save()){			   	  	 
		   	  	      	  	   	   	  
		   	  	   $this->code = 1;
		           $this->msg = t("Check {{email_address}} for an email to reset your password.",array(
		    		  '{{email_address}}'=>$model->email_address
		    	   ));

		   	  } else $this->msg = CommonUtility::parseError($model->getErrors());		   	  
		   } else $this->msg[] = t("Records not found");
		   
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();	
	}
	
	public function actionresetPassword()
	{
		try {
			
		    $client_uuid = isset($this->data['client_uuid'])?$this->data['client_uuid']:'';
		    $password = isset($this->data['password'])?$this->data['password']:'';
		    $cpassword = isset($this->data['cpassword'])?$this->data['cpassword']:'';
		    
		    $model = AR_client::model()->find('client_uuid=:client_uuid', 
		    array(':client_uuid'=>$client_uuid)); 		
		    
		    if($model){
		    	if($model->status=="active"){
		    				    		
		    		$model->scenario = "reset_password";
		    		$model->npassword =  $password;
		    		$model->cpassword =  $cpassword;
		    		$model->password = md5($password);
		    		$model->reset_password_request = 0;
		    		
		    		if($model->save()){
					    $this->code = 1;
					    $this->msg  = t("Your password is now updated.");
					    $this->details = array(
					      'redirect'=>Yii::app()->createUrl("/account/login")
					    );
		    		} else $this->msg =  CommonUtility::parseError( $model->getErrors() );;
		    		
		    	} else $this->msg[] = t("Account not active");
		    } else $this->msg[] = t("Records not found"); 
		    
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();	
	}	
	
	public function actiongetProfile()
	{
		try {
			
			$model = AR_client::model()->find('client_id=:client_id', 
		    array(':client_id'=> intval(Yii::app()->user->id) )); 		
			if($model){
				$this->code = 1; $this->msg = "ok";
				$this->details = array(
				  'first_name'=>$model->first_name,
				  'last_name'=>$model->last_name,
				  'email_address'=>$model->email_address,
				  'mobile_prefix'=>$model->phone_prefix,
				  'mobile_number'=>str_replace($model->phone_prefix,"",$model->contact_phone),
				);
			} else $this->msg = t("User not login or session has expired");
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionsaveProfile()
	{
		try {
			
			$code = isset($this->data['code'])?$this->data['code']:'';
		    $email_address = isset($this->data['email_address'])?$this->data['email_address']:'';
		    $mobile_prefix = isset($this->data['mobile_prefix'])?$this->data['mobile_prefix']:'';
		    $mobile_number = isset($this->data['mobile_number'])?$this->data['mobile_number']:'';
		    $contact_number = $mobile_prefix.$mobile_number;
		    
		    $model = AR_client::model()->find('client_id=:client_id', 
		    array(':client_id'=> intval(Yii::app()->user->id) )); 	
		    if($model){
		    	$_change = false;
		    	if ($model->email_address!=$email_address){
		    		$_change = true;
		    	}
		    	if ($model->contact_phone!=$contact_number){
		    		$_change = true;
		    	}
		    	if($_change){
		    		if($model->mobile_verification_code!=$code){
		    			$this->msg[] = t("Invalid 6 digit code");
		    			$this->responseJson();
		    			Yii::app()->end();
		    		}
		    	}

		    	$model->first_name = isset($this->data['first_name'])?$this->data['first_name']:'';
		    	$model->last_name = isset($this->data['last_name'])?$this->data['last_name']:'';
		    	$model->email_address = $email_address;
		    	$model->phone_prefix = $mobile_prefix;
		    	$model->contact_phone = $contact_number;
		    	if($model->save()){
		    		$this->code = 1;
		    		$this->msg = t("Profile updated");

					Yii::app()->user->contact_number = $contact_number;
					Yii::app()->user->email_address = $email_address;

		    	} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    		    	
		    } else $this->msg = t("User not login or session has expired");
		    		    
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionupdatePassword()
	{
		try {
					   
		   $model = AR_client::model()->find('client_id=:client_id', 
		   array(':client_id'=> intval(Yii::app()->user->id) )); 	
		   if($model){
		   	   //array('old_password,npassword,cpassword', 'required', 'on'=>'update_password'), 
		   	   $model->scenario = 'update_password';
		   	   $model->old_password = isset($this->data['old_password'])?$this->data['old_password']:'';
		   	   $model->npassword = isset($this->data['new_password'])?$this->data['new_password']:'';
		   	   $model->cpassword = isset($this->data['confirm_password'])?$this->data['confirm_password']:'';
		   	   $model->password = md5($model->npassword);
		   	   if($model->save()){
		    	  $this->code = 1;
		    	  $this->msg = t("Password change");
		      } else $this->msg = CommonUtility::parseError( $model->getErrors() );		   	   
		   } else $this->msg[] = t("User not login or session has expired");
		   		   
		} catch (Exception $e) {							
		    $this->msg[] = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionverifyAccountDelete()
	{
		$code = isset($this->data['code'])?$this->data['code']:'';
		$model = AR_client::model()->find('client_id=:client_id', 
		array(':client_id'=> intval(Yii::app()->user->id) )); 	
		if($model){
			if($model->mobile_verification_code==$code){
			   	$this->code = 1;
			   	$this->msg = "ok";			   	
			} else $this->msg[] = t("Invalid 6 digit code");
		} else $this->msg[] = t("User not login or session has expired");
		$this->responseJson();
	}
	
	public function actiondeleteAccount()
	{		
		$code = isset($this->data['code'])?$this->data['code']:'';
		$model = AR_client::model()->find('client_id=:client_id', 
		array(':client_id'=> intval(Yii::app()->user->id) )); 	
		if($model){
			if($model->mobile_verification_code==$code){
			   	//$model->delete();
			   	Yii::app()->user->logout(false);
			   	$this->code = 1;
			   	$this->msg = "ok";
			   	$this->details = array(
			   	  'redirect'=>Yii::app()->getBaseUrl(true)
			   	);
			} else $this->msg[] = t("Invalid 6 digit code");
		} else $this->msg[] = t("User not login or session has expired");
		$this->responseJson();
	}
	
	public function actionrequestData()
	{		
		$model = AR_client::model()->find('client_id=:client_id', 
		array(':client_id'=> intval(Yii::app()->user->id) )); 	
		if($model){
			$gpdr = AR_gpdr_request::model()->find('client_id=:client_id AND request_type=:request_type AND status=:status', 
		    array( 
		      ':client_id'=> intval(Yii::app()->user->id),
		      ':request_type'=> 'request_data',
		      ':status'=> 'pending'
		    )); 			    
		    if(!$gpdr){
				$gpdr = new AR_gpdr_request;
				$gpdr->request_type = "request_data";
				$gpdr->client_id = intval(Yii::app()->user->id);
				$gpdr->first_name = $model->first_name;
				$gpdr->last_name = $model->last_name;
				$gpdr->email_address = $model->email_address;
				if($gpdr->save()){
					$this->code = 1;
				   	$this->msg = "ok";
				} else $this->msg = CommonUtility::parseError( $model->getErrors() );
		    } else $this->msg[] = t("You have already existing request.");
		} else $this->msg[] = t("User not login or session has expired");
		$this->responseJson();
	}
	
	public function actionuploadProfilePhoto()
	{
		$upload_uuid = CommonUtility::generateUIID();
		$allowed_extension = explode(",",  Yii::app()->params['upload_type']);
		$maxsize = (integer) Yii::app()->params['upload_size'] ;
		if (!empty($_FILES)) {
			
			$title = $_FILES['file']['name'];   
			$file_size = (integer)$_FILES['file']['size'];   
			$filetype = $_FILES['file']['type'];   								
			
			
			if(isset($_FILES['file']['name'])){
			   $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			} else $extension = strtolower(substr($title,-3,3));
			
			if(!in_array($extension,$allowed_extension)){			
				$this->msg = t("Invalid file extension");
				$this->jsonResponse();
			}
			if($file_size>$maxsize){
				$this->msg = t("Invalid file size");
				$this->jsonResponse();
			}
			
			$allowed_extension = explode(",",Helper_imageType);
		    $maxsize = (integer)Helper_maxSize;			
		    
		    if(isset($_FILES['file']['name'])){
			   $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			   $extension = strtolower($extension);
			} else $extension = strtolower(substr($title,-3,3)); 	
				
			if(!in_array($extension,$allowed_extension)){			
				$this->msg = t("Invalid file extension");
				$this->jsonResponse();
			}
			if($file_size>$maxsize){
				$this->msg = t("Invalid file size, allowed size are {{size}}",array(
				 '{{size}}'=>CommonUtility::HumanFilesize($maxsize)
				));
				$this->jsonResponse();
			}
			
			$upload_path = CMedia::avatarFolder();
			$tempFile = $_FILES['file']['tmp_name'];
			$upload_uuid = CommonUtility::createUUID("{{media_files}}",'upload_uuid');
			$filename = $upload_uuid.".$extension";			
			$path = CommonUtility::uploadDestination($upload_path)."/".$filename;						
			$path2 = CommonUtility::uploadDestination($upload_path)."/";
						
			if(move_uploaded_file($tempFile,$path)){					
			   	$this->code = 1; $this->msg = "OK";	
				$this->details = array(			   
				   'url_image'=> CMedia::getImage($filename,$upload_path,'',CommonUtility::getPlaceholderPhoto('customer')),
				   'filename'=>$filename,
				   'id'=>$upload_uuid			   
				);		
			} else $this->msg = t("Failed cannot upload file.");
					
		} else $this->msg = t("Invalid file");
	
		$this->jsonResponse();		
	}
	
	public function actionsaveProfilePhoto()
	{				
		$model = AR_client::model()->find('client_id=:client_id', 
		array(':client_id'=> intval(Yii::app()->user->id) )); 	
		if($model){
			$filename = isset($this->data['filename'])?$this->data['filename']:'';
			$img = isset($_POST['photo'])?$_POST['photo']:'';						
			if(!empty($filename)  && !empty($img)){									
				$upload_path = CMedia::avatarFolder();
				$path = CommonUtility::uploadDestination($upload_path)."/".$filename;
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data = base64_decode($img);		
				@file_put_contents($path,$data);
				
				$model->avatar = $filename;
				$model->path = $upload_path;
				if($model->save()){
					$this->code = 1;
					$this->msg = t("Profile photo saved");
					
					$url_image = CMedia::getImage($filename,$upload_path,'',CommonUtility::getPlaceholderPhoto('customer'));
					Yii::app()->user->avatar = $url_image;						 
					
					$this->details = array(			   
					   'url_image'=>$url_image,
					   'filename'=>$filename,					   
					);	
				} else $this->msg = CommonUtility::parseError( $model->getErrors() );						
			} else $this->msg[] = t("Invalid data");			
		} else $this->msg[] = t("User not login or session has expired");
		$this->responseJson();		
	}
	
	public function actionremoveProfilePhoto()
	{				
		$id = isset($this->data['id'])?$this->data['id']:'';
			if(!empty($id)){
			$upload_path = CMedia::avatarFolder();
			$path = CommonUtility::uploadDestination($upload_path)."/".$id;
			if(file_exists($path)){
				@unlink($path);
				$this->code = 1;
				$this->msg = "OK";			
			} else $this->msg = t("File not found");
		} else $this->msg = t("ID is empty");
		$this->responseJson();
	}
	
	public function actioncheckStoreOpen()
	{		
		try {
						
			$merchant_id = isset($this->data['merchant_id'])?intval($this->data['merchant_id']):'';			
			
			$date = date("Y-m-d");
			$time_now = date("H:i");
			
			$choosen_delivery = isset($this->data['choosen_delivery'])?$this->data['choosen_delivery']:'';		
			$whento_deliver = isset($choosen_delivery['whento_deliver'])?$choosen_delivery['whento_deliver']:'';
			
			if($whento_deliver=="schedule"){
				$date = isset($choosen_delivery['delivery_date'])?$choosen_delivery['delivery_date']:$date;
				$time_now = isset($choosen_delivery['delivery_time'])?$choosen_delivery['delivery_time']['start_time']:$time_now;
			}
						
			$datetime_to = date("Y-m-d g:i:s a",strtotime("$date $time_now"));
			CMerchantListingV1::checkCurrentTime( date("Y-m-d g:i:s a") , $datetime_to);		
			
			$resp = CMerchantListingV1::checkStoreOpen($merchant_id,$date,$time_now);
			$this->code = 1;
			$this->msg = $resp['merchant_open_status']>0?"ok":t("This store is close right now, but you can schedulean order later.");
			$this->details =  $resp;
					
		} catch (Exception $e) {							
		    $this->msg = t($e->getMessage());		    
		}					
		$this->responseJson();
	}
	
	public function actionstoreAvailable()
	{
		try {		   
			
			$merchant_uuid = Yii::app()->input->post('merchant_uuid');
			CMerchantListingV1::storeAvailable($merchant_uuid);
			$this->code = 1; $this->msg = "ok";
		} catch (Exception $e) {							
		    $this->msg = t($e->getMessage());		    
		}					
		$this->responseJson();
	}
	
	public function actiongetWebpushSettings()
	{
		try {						
						
			$settings = AR_admin_meta::getMeta(array('webpush_app_enabled','webpush_provider','pusher_instance_id','onesignal_app_id'
			));		
						
			$enabled = isset($settings['webpush_app_enabled'])?$settings['webpush_app_enabled']['meta_value']:'';
			$provider = isset($settings['webpush_provider'])?$settings['webpush_provider']['meta_value']:'';
			$pusher_instance_id = isset($settings['pusher_instance_id'])?$settings['pusher_instance_id']['meta_value']:'';			
			$onesignal_app_id = isset($settings['onesignal_app_id'])?$settings['onesignal_app_id']['meta_value']:'';	
			
			$user_settings = array();
			
			try {
			   $user_settings = CNotificationData::getUserSettings(Yii::app()->user->id,'client');		
			   array_unshift($user_settings['interest'], Yii::app()->user->client_uuid);
			} catch (Exception $e) {
			   //
			}
									
			$data = array(
			  'enabled'=>$enabled,
			  'provider'=>$provider,
			  'pusher_instance_id'=>$pusher_instance_id,			  
			  'onesignal_app_id'=>$onesignal_app_id,
			  'safari_web_id'=>'',			  
			  'user_settings'=>$user_settings,
			);				
			$this->code = 1;
			$this->msg = "OK";
			$this->details = $data;
						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
	
	public function actiongetNotifications()
	{
		try {											
			$data = CNotificationData::getList( Yii::app()->user->client_uuid );			
			$this->code = 1; $this->msg = "ok";
			$this->details = $data;
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
		
	public function actionclearNotifications()
	{
		try {						
						
			AR_notifications::model()->deleteAll('notication_channel=:notication_channel',array(
			 ':notication_channel'=> Yii::app()->user->client_uuid
			));
			$this->code = 1; $this->msg = "ok";
						
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
	
    public function actiongetwebnotifications()
	{
		try {
			
			$data = CNotificationData::getUserSettings(Yii::app()->user->id,'client');
			$this->code = 1;
		    $this->msg = "OK";
		    $this->details = $data;
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    
		}	
		$this->responseJson();		
	}
	
	public function actionsavewebnotifications()
	{
		try {		
			
			$user_type='client';
					    			
		    $webpush_enabled = isset($this->data['webpush_enabled'])?intval($this->data['webpush_enabled']):0;	
		    $interest = isset($this->data['interest'])?$this->data['interest']:'';
		    $device_id = isset($this->data['device_id'])?$this->data['device_id']:'';
		    		    
		    $model = AR_device::model()->find("user_id=:user_id AND user_type=:user_type",array(
		      ':user_id'=>intval(Yii::app()->user->id),
		      ':user_type'=>$user_type
		    ));
		    if(!$model){
		       $model = new AR_device;			       
		    } 		    		    
		    $model->interest = $interest;
		    $model->user_type = $user_type;
	    	$model->user_id = intval(Yii::app()->user->id);
	    	$model->platform = "web";
	    	$model->device_token = $device_id;
	    	$model->browser_agent = $_SERVER['HTTP_USER_AGENT'];
	    	$model->enabled = $webpush_enabled;
	    	if($model->save()){
		   	   $this->code = 1;
			   $this->msg = t("Setting saved");		    
		    } else $this->msg = CommonUtility::parseError( $model->getErrors());
		    		   		    		    
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
	}	

	public function actionnotificationList()
	{
		try {
			
			PrettyDateTime::$category = 'front';

			$page = isset($this->data['page'])?intval($this->data['page']):0;	
			$length = Yii::app()->params->list_limit;
			$show_next_page = false;
					
			$criteria=new CDbCriteria();
			$criteria->condition="notication_channel=:notication_channel";
			$criteria->params = array(':notication_channel'=> Yii::app()->user->client_uuid );
			
			$criteria->order = "date_created DESC";
		    $count = AR_notifications::model()->count($criteria); 
		    
		    $pages=new CPagination( intval($count) );
            $pages->setCurrentPage( intval($page) );        
            $pages->pageSize = intval($length);
            $pages->applyLimit($criteria);        
            $model = AR_notifications::model()->findAll($criteria);
            
            if($model){
            	$data = array();
            	foreach ($model as $item) {
					
					$image=''; $url = '';
					if($item->image_type=="icon"){
						$image = !empty($item->image)?$item->image:'';
					} else {
						if(!empty($item->image)){
							$image = CMedia::getImage($item->image,$item->image_path,
							Yii::app()->params->size_image_thumbnail ,
							CommonUtility::getPlaceholderPhoto('item') );
						}
					}
					
					$params = !empty($item->message_parameters)?json_decode($item->message_parameters,true):'';
					
					$data[]=array(
					  'notification_type'=>$item->notification_type,
					  'message'=>t($item->message,(array)$params),
					  'date'=>PrettyDateTime::parse(new DateTime($item->date_created)),				  
					  'image_type'=>$item->image_type,
					  'image'=>$image,
					  'url'=>$url
					);
				}
				
				$page_count = $pages->getPageCount();					
				if($page_count > ($page+1) ){
				   $show_next_page = true;
				}
		  
				$this->code = 1; $this->msg = "OK";
				$this->details =  array(
				  'count'=>$count,
				  'show_next_page'=>$show_next_page,
				  'page'=>intval($page)+1,
				  'data'=>$data
				);				
            } else $this->msg = t("No results");
				
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());			    		    
		}	
		$this->responseJson();		
	}
	
	public function actiongetpaymentlist()
	{
		try {
					   		   
		   $data = CPayments::getPaymentList();		   
		   $this->code = 1;
		   $this->msg = "ok";
		   $this->details = array(		     
		     'data'=>$data
		   );		   
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}
		$this->responseJson();
	}
	
	public function actionmerchantsavedpayment()
	{
		try {

		   $default_payment_uuid = ''; $default_payment = array();
		   $merchant_uuid = Yii::app()->input->post('merchant_uuid');		   
		   $merchant = CMerchants::getByUUID($merchant_uuid);		   
		   
		   $model = AR_merchant_payment_method::model()->find("merchant_id=:merchant_id AND as_default=:as_default",array(
		     ':merchant_id'=>$merchant->merchant_id,
		     'as_default'=>1
		   ));
		    if($model){		  		    	 
		    	$default_payment_uuid=$model->payment_uuid;
		    	$default_payment = array(
		    	  'payment_uuid'=>$model->payment_uuid,
		    	  'payment_code'=>$model->payment_code
		    	);
		    }
		    
		    $data = CPayments::MerchantSavedPaymentList($merchant->merchant_id);		
		    $this->code = 1;
		    $this->msg = "ok";
		    $this->details = array(
		      'default_payment_uuid'=>$default_payment_uuid,
		      'default_payment'=>$default_payment,
		      'data'=>$data,
		    );		    
		   
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}		
		$this->responseJson();
	}
	
	public function actionmerchantsetdefaultpayment()
	{
		try {
									
			$payment_uuid = Yii::app()->input->post('payment_uuid');
			
			$model = AR_merchant_payment_method::model()->find("payment_uuid=:payment_uuid",array(			 
			 ":payment_uuid"=>$payment_uuid
			));
			
			if($model){
				$model->as_default = 1;
				$model->save();
				$this->code = 1;
		    	$this->msg = t("Succesful");
			} else $this->msg = t("Payment not found");	
			
		} catch (Exception $e) {
		    $this->msg = t($e->getMessage());
		}				
		$this->responseJson();
	}
	
	public function actionmerchantdeletesavedpaymentmethod()
	{
		try {
		   $payment_uuid = isset($this->data['payment_uuid'])?$this->data['payment_uuid']:'';		   
		   $model = AR_merchant_payment_method::model()->find("payment_uuid=:payment_uuid",array(			 
			 ":payment_uuid"=>$payment_uuid
			));
		   if($model){
		   	   $model->delete();
			   $this->code = 1;
			   $this->msg = "ok";
		   } else $this->msg = t("Payment not found");
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}

	public function actiongetLanguage()
	{
		try {
			$data = WidgetLangselection::getData();		
			$this->code = 1; 
			$this->msg = "OK";
			$this->details = $data;
		} catch (Exception $e) {
		    $this->msg[] = t($e->getMessage());		    
		}	
		$this->responseJson();
	}
	
}
/*end class*/