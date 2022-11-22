<?php
class CCart
{	
	private static $content;		
	private static $condition;
	private static $items=array();
	private static $summary=array();
	private static $packaging_fee=0;
	private static $tax_condition;	
	private static $tax_type;	
	private static $tax_group=array();
	const CONDITION_NAME = array('promo');
	const CONDITION_RM = array('promo','promo_type','promo_id');
	
	public static function getMerchantId($cart_uuid='')
	{				
		$dependency = CCacheData::dependency();
		$model = AR_cart::model()->cache( Yii::app()->params->cache , $dependency  )->find('cart_uuid=:cart_uuid',array(':cart_uuid'=>$cart_uuid)); 
		if($model){
			return intval($model->merchant_id);
		}
		throw new Exception( 'no results' );
	}
	
	public static function getMerchantForCredentials($cart_uuid='')
	{
		$stmt="
		SELECT a.merchant_id,
		b.merchant_type
		FROM {{cart}} a
		LEFT JOIN {{merchant}} b
		ON
		a.merchant_id = b.merchant_id
		WHERE 
		a.cart_uuid = ".q($cart_uuid)."
		";
		if ($res = CCacheData::queryRow($stmt)){
			return $res;
		}
		throw new Exception( 'no results' );
	}
		
	public static function add($data = array() )
	{		
		if(is_array($data) && count($data)>=1){	
			
			if( $results = CCart::find($data) ){				
												
				$qty = isset($results['qty'])?$results['qty']:0;
								
				if($qty<=0){
					CCart::remove($results['cart_uuid'],$results['cart_row']);
				} else {							
					CCart::update($results['cart_uuid'],$results['cart_row'],$results['qty'] , isset($results['addons'])?$results['addons']:'' );					
				}				
				return true;
				
			} else {
				
				$items = new AR_cart;
				$items->cart_row = isset($data['cart_row'])?$data['cart_row']:'';
				$items->cart_uuid = isset($data['cart_uuid'])?$data['cart_uuid']:'';
				$items->merchant_id = isset($data['merchant_id'])?(integer)$data['merchant_id']:'';
				$items->cat_id = isset($data['cat_id'])?(integer)$data['cat_id']:'';
				$items->item_token = isset($data['item_token'])?$data['item_token']:'';
				$items->item_size_id = isset($data['item_size_id'])?(integer)$data['item_size_id']:'';
				$items->qty = isset($data['qty'])?(integer)$data['qty']:'';
				$items->special_instructions = isset($data['special_instructions'])?$data['special_instructions']:'';
				$items->if_sold_out = isset($data['if_sold_out'])?$data['if_sold_out']:'';
				$items->save();
				
				$builder=Yii::app()->db->schema->commandBuilder;
				
				// addon
				$item_addons = array();
				$addons = isset($data['addons'])?$data['addons']:'';
				if(is_array($addons) && count($addons)>=1){
					foreach ($addons as $item) {					
						$item_addons[] = array(
						 'cart_row'=>isset($item['cart_row'])?$item['cart_row']:'',
						 'cart_uuid'=>isset($item['cart_uuid'])?$item['cart_uuid']:'',
						 'subcat_id'=>isset($item['subcat_id'])?(integer)$item['subcat_id']:0,
						 'sub_item_id'=>isset($item['sub_item_id'])?(integer)$item['sub_item_id']:0,
						 'qty'=>isset($item['qty'])?(integer)$item['qty']:0,
						 'multi_option'=>isset($item['multi_option'])?$item['multi_option']:'',
						);
					}				
					$command=$builder->createMultipleInsertCommand('{{cart_addons}}',$item_addons);
					$command->execute();
				}
				
				// attributes
				$item_attributes = array();
				$attributes = isset($data['attributes'])?$data['attributes']:'';
				if(is_array($attributes) && count($attributes)>=1){
					foreach ($attributes as $item) {					
						$item_attributes[] = array(
						 'cart_row'=>isset($item['cart_row'])?$item['cart_row']:'',
						 'cart_uuid'=>isset($item['cart_uuid'])?$item['cart_uuid']:'',
						 'meta_name'=>isset($item['meta_name'])?$item['meta_name']:'',
						 'meta_id'=>isset($item['meta_id'])?(integer)$item['meta_id']:'',
						);
					}				
					$command=$builder->createMultipleInsertCommand('{{cart_attributes}}',$item_attributes);
					$command->execute();
				}
			    return true;
			}
		} 
		throw new Exception( 'invalid data' );
	}
	
	public static function update($cart_uuid='',$cart_row='',$qty=0, $addons=array())
	{		
		$cart = AR_cart::model()->find('cart_uuid=:cart_uuid AND cart_row=:cart_row', 
		array(':cart_uuid'=>$cart_uuid, ':cart_row'=>$cart_row ));			
		if($cart){			
			$cart->qty = intval($qty);
			$cart->update();
						
			if(is_array($addons) && count($addons)>=1){
				foreach ($addons as $val) {
					$stmt="UPDATE {{cart_addons}}			
					SET qty =".q( intval($val['qty']) )."
					WHERE id=".q( intval($val['id']) )."					
					";
					Yii::app()->db->createCommand($stmt)->query();
				}
			}
			
			return true;
		}
		throw new Exception( 'row not found' );
	}
	
	public static function updateAddon($id='', $qty=0)
	{
		$stmt="
		UPDATE {{cart_addons}}
		SET qty=".q(intval($qty))."
		WHERE id=".q(intval($id))."
		";
		Yii::app()->db->createCommand($stmt)->query();
	}
	
	protected static function find( $data=array() )
	{				
		
		$merchant_id = isset($data['merchant_id'])?(integer)$data['merchant_id']:'';		
		$cart_uuid = isset($data['cart_uuid'])?$data['cart_uuid']:'';
		$cat_id = isset($data['cat_id'])?(integer)$data['cat_id']:'';
		$item_token = isset($data['item_token'])?$data['item_token']:'';
		$item_size_id = isset($data['item_size_id'])?(integer)$data['item_size_id']:'';	
		$item_qty = isset($data['qty'])?(integer)$data['qty']:'';			
		$inline_qty = isset($data['inline_qty'])?(integer)$data['inline_qty']:'';				
		
		$addons = array(); $attributes = array();
		if(is_array($data['addons']) && count($data['addons'])>=1 ){
			foreach ($data['addons'] as $add_on) {				
				$addons[]=array(
				  'subcat_id'=>$add_on['subcat_id'],
				  'sub_item_id'=>$add_on['sub_item_id'],
				  'qty'=>$add_on['qty'],
				);
			}
		}
		
		if(is_array($data['attributes']) && count($data['attributes'])>=1 ){
			foreach ($data['attributes'] as $attributes_val) {
				$attributes[]=array(
				  'meta_name'=>$attributes_val['meta_name'],
				  'meta_id'=>$attributes_val['meta_id'],
				);
			}
		}				
		
		$stmt="
		SELECT a.cart_uuid,a.cart_row,a.qty,
		(
		  select GROUP_CONCAT(id,';',subcat_id,';',sub_item_id,';',qty,';',multi_option)
		  from {{cart_addons}}
		  where
		  cart_row = a.cart_row
		) as addons,
		
		(
		  select GROUP_CONCAT(meta_name,';',meta_id)
		  from {{cart_attributes}}
		  where
		  cart_row = a.cart_row
		) as attributes
		
		FROM {{cart}} a
		WHERE merchant_id = ".$merchant_id."
		AND a.cart_uuid = ".q($cart_uuid)."
		AND a.cat_id = ".q($cat_id)."
		AND a.item_token = ".q($item_token)."
		AND a.item_size_id = ".q($item_size_id)."		
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $items) {				
				
				/*attributes*/			
				$found_attr = array(); $attr_not_found = 0;	
				if(is_array($attributes) && count($attributes)>=1 && !empty($items['attributes']) ){
					$attributes_data = isset($items['attributes'])?explode(",",$items['attributes']):'';
					$new_attributes_data  = array();
					foreach ($attributes_data as $attr_data) {						
						$attr_data2 = explode(";",$attr_data);
						$new_attributes_data[] = array(
						  'meta_name'=>isset($attr_data2[0])?$attr_data2[0]:'',
						  'meta_id'=>isset($attr_data2[1])?$attr_data2[1]:'',
						);
					}					
															
					foreach ($attributes as $attributes_data) {						
						if($found = CCart::findAttributes($new_attributes_data,$attributes_data['meta_name'],$attributes_data['meta_id'])){
							$found_attr[]=$found;
						} else $attr_not_found++; 
					}			
								
					if( count($attributes)!= count($new_attributes_data)){
						$attr_not_found = 1;
					}
				} else {					
					if(count($attributes)>0 && empty($items['attributes'])){
						$attr_not_found = 1;
					}
				}
							
				if (is_array($addons) && count($addons)>=1 && !empty($items['addons']) ){				
					$addons_data = !empty($items['addons'])?explode(",",$items['addons']):'';						
					$new_addons_data = array();
					foreach ($addons_data as $addons_data1) {
						$addons_data2 = explode(";",$addons_data1);
						$new_addons_data[] = array(
						   'id'=>isset($addons_data2[0])?$addons_data2[0]:'',
						   'subcat_id'=>isset($addons_data2[1])?$addons_data2[1]:'',
						   'sub_item_id'=>isset($addons_data2[2])?$addons_data2[2]:'',
						   'qty'=>isset($addons_data2[3])?$addons_data2[3]:'',
						   'multi_option'=>isset($addons_data2[4])?$addons_data2[4]:'',
						);						
					}
									
					$found_addons = array(); $addons_not_found = 0;
					foreach ($addons as $addons_val) {										
						if($found = CCart::findaddon($new_addons_data,$addons_val['subcat_id'],$addons_val['sub_item_id'], $addons_val['qty'] )){
							$found_addons[]=$found;
						} else $addons_not_found++; 
					}
					
					if($addons_not_found<=0 && $attr_not_found<=0){						
						$items['qty'] = intval($items['qty']) + intval($item_qty);
						$items['addons'] = $found_addons;						
						return $items;
					}
					
				} else {												
					if (count($addons)<=0 && empty($items['addons']) && $attr_not_found<=0 ){															
						if($inline_qty>0){									
							$items['qty']  = intval($item_qty);	
						} else $items['qty'] = intval($items['qty']) + intval($item_qty);
												
						return $items;
					} 					
				}
			}						
		}				
		return false;		
	}
	
	protected static function findaddon($addon_data, $subcat_id='', $sub_item_id='', $qty=0)
	{
		$found = false;	
		if(is_array($addon_data) && count($addon_data)>=1){
			foreach ($addon_data as $val) {				
				if($val['subcat_id']==$subcat_id && $val['sub_item_id']==$sub_item_id){
					$val['qty'] = intval($val['qty']) + intval($qty);
					$found = $val;
					break;
				}
			}
		}
		return $found;
	}
	
	protected static function findAttributes($attributes_data='',$meta_name='',$meta_id='')
	{		
		$found = false;			
		if(is_array($attributes_data) && count($attributes_data)>=1){
			foreach ($attributes_data as $val) {				
				if($val['meta_name']==$meta_name && $val['meta_id']==$meta_id){					
					$found = $val;
					break;
				}
			}
		}
		return $found;
	}
		
	public static function getContent($cart_uuid='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$content = CCart::getCart($cart_uuid,$lang);				
		$subcategory = CCart::getSubcategory($cart_uuid,$lang);
		$size = CCart::getSize($cart_uuid,$lang);		
		$addon_items = CCart::getAddonItems($cart_uuid,$lang);
		$meta_cooking = CCart::getMetaCooking($cart_uuid,$lang);
		$meta_ingredients = CCart::getMetaIngredients($cart_uuid,$lang);
										
		if($content){
			self::$content = array(			  
			  'content'=>$content,
			  'subcategory'=>$subcategory?$subcategory:'',
		      'size'=>$size?$size:'', 
		      'addon_items'=>$addon_items?$addon_items:'',
		      'attributes'=>array(
		        'cooking_ref'=>$meta_cooking,
		        'ingredients'=>$meta_ingredients
		      )
			);
			return self::$content;
		}
		throw new Exception( 'cart is empty' );
	}
	
	public static function getCountCart($cart_uuid='')
	{
		$stmt="
		SELECT COUNT(*) as total FROM {{cart}}
		WHERE cart_uuid=".q($cart_uuid)."
		";
		if( $res = Yii::app()->db->createCommand($stmt)->queryRow() ){
			return $res['total'];
		}
		return false;
	}
	
	public static function getMerchant($merchant_id='',$lang='')
	{		
		$stmt="
		SELECT merchant_id,merchant_uuid,restaurant_name,restaurant_slug,		
		address,
		distance_unit,delivery_distance_covered,latitude,lontitude,
		merchant_type,
		commision_type,
		percent_commision as commission,
		logo,path,
		
		
		IFNULL((
		 select GROUP_CONCAT(cuisine_name,';',color_hex,';',font_color_hex)
		 from {{view_cuisine}}
		 where language=".q($lang)."
		 and cuisine_id in (
		    select cuisine_id from {{cuisine_merchant}}
		    where merchant_id  = a.merchant_id
		 )		 
		),'') as cuisine_name
			
		FROM {{merchant}} a
		WHERE merchant_id =".q($merchant_id)."
		";						
		if( $res = Yii::app()->db->createCommand($stmt)->queryRow() ){			
			$cuisine_list = array();
			$cuisine_name = explode(",",$res['cuisine_name']);
			if(is_array($cuisine_name) && count($cuisine_name)>=1){
				foreach ($cuisine_name as $cuisine_val) {						
					$cuisine = explode(";",$cuisine_val);								
					$cuisine_list[]=array(
					  'cuisine_name'=>isset($cuisine[0])?Yii::app()->input->xssClean($cuisine[0]):'',
					  'bgcolor'=>isset($cuisine[1])?  !empty($cuisine[1])?$cuisine[1]:'#ffd966'  :'#ffd966',
					  'fncolor'=>isset($cuisine[2])? !empty($cuisine[2])?$cuisine[2]:'#ffd966' :'#000',
					);
				}
			}
			
			$res['cuisine'] = (array)$cuisine_list;
			$res['restaurant_name'] = Yii::app()->input->xssClean($res['restaurant_name']);
			$res['merchant_address'] = Yii::app()->input->xssClean($res['address']);
			return $res;
		}
		return false;
	}
	
	public static function getCart($cart_uuid='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$tax_use = array(); 
		$tax_data_list = array();		
		$tax_type = self::getTaxType();		
						
		try {
		   $merchant_id = CCart::getMerchantId($cart_uuid);
		   $tax_data_list = CTax::getTax($merchant_id,$tax_type);		
		} catch (Exception $e) {
			//
		}
				
		$stmt="
		SELECT a.cart_row,a.cart_uuid,a.cat_id,a.item_token,
		
		(
		 select item_name from {{item_translation}}
		 where item_id IN (
		   select item_id from {{item}}
		   where item_token = a.item_token
		   and language = ".q($lang)."
		 )		 
		) as item_name,
		
		b.item_id,
		b.photo,	
		b.path,
		b.non_taxable as taxable,	
		b.packaging_fee,	
		b.packaging_incremental,
		a.item_size_id,a.qty,a.special_instructions,a.if_sold_out,
				
		(
		 select GROUP_CONCAT(cart_row,';',subcat_id,';',sub_item_id,';',qty,';',multi_option)
		 from {{cart_addons}}
		 where cart_uuid = a.cart_uuid
		 and cart_row = a.cart_row
		) as addon_items,
		
		(
		 select GROUP_CONCAT(meta_name,';',meta_id)
		 from {{cart_attributes}}
		 where cart_uuid = a.cart_uuid
		 and cart_row = a.cart_row
		) as attributes,
		
		(
		 select GROUP_CONCAT(meta_id)
		 from {{item_meta}}
		 where merchant_id = a.merchant_id
		 and item_id = b.item_id
		 and meta_name='tax'
		) as item_tax
		
		FROM {{cart}} a
		LEFT JOIN {{item}} b
		ON
		a.item_token = b.item_token
		
		WHERE cart_uuid = ".q($cart_uuid)."		
		ORDER BY id ASC
		";							
		if( $res = Yii::app()->db->createCommand($stmt)->queryAll() ){
			$data = array();
			foreach ($res as $val) {								
				$addon = array();
				$cart_row = $val['cart_row'];				
								
				$addon_items = array();
				$_addon_items = isset($val['addon_items'])? explode(",",$val['addon_items']) :'';
				if(is_array($_addon_items) && count($_addon_items)>=1){
					foreach ($_addon_items as $val3) {						
						$addonitems = explode(";",$val3);																		
						$row = isset($addonitems[0])?$addonitems[0]:'';
						$subcat_id = isset($addonitems[1])?$addonitems[1]:'';
						$sub_item_id = isset($addonitems[2])?$addonitems[2]:'';
						$qty = isset($addonitems[3])?$addonitems[3]:'';
						$multi_option = isset($addonitems[4])?$addonitems[4]:'';						
						$addon_items[$row][$subcat_id][$sub_item_id] = array(
						 'qty'=>$qty,
						 'multi_option'=>$multi_option
						);
					}
				}
				
				$attributes = array();
				$attributes_raw = isset($val['attributes'])? explode(",",$val['attributes']) :'';
				if(is_array($attributes_raw) && count($attributes_raw)>=1){
					foreach ($attributes_raw as $val4) {
						$attributes_item = explode(";",$val4);
						$meta_name = isset($attributes_item[0])?$attributes_item[0]:'';
						$meta_value = isset($attributes_item[1])?$attributes_item[1]:'';						
						$attributes[$meta_name][$meta_value]=$meta_value;
					}
				}
								
				if($val['packaging_fee']>0 && $val['packaging_incremental']<=0){
					self::$packaging_fee+= $val['packaging_fee'];					
				} elseif ( $val['packaging_fee']>0 && $val['packaging_incremental']>0){
					self::$packaging_fee+= floatval($val['packaging_fee']) * intval($val['qty']);
				}
				
				$tax_use = array();
											
				if($tax_type=="multiple"){						
					if(!empty($val['item_tax'])){					
						$item_tax = isset($val['item_tax'])? explode(",",$val['item_tax']) :'';									
						if(is_array($item_tax) && count($item_tax)>=1){					
							foreach ($item_tax as $tax_id) {
								if(array_key_exists($tax_id,(array)$tax_data_list)){
									array_push($tax_use,$tax_data_list[$tax_id]);
								}
							}
						}
					}
				} else $tax_use = $tax_data_list;
															
				$data[] = array(
				  'cart_row'=>$val['cart_row'],
				  'cart_uuid'=>$val['cart_uuid'],
				  'cat_id'=>$val['cat_id'],
				  'item_id'=>$val['item_id'],
				  'item_token'=>$val['item_token'],
				  'item_name'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['item_name'])),				  				  
				  'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image_thumbnail,
				   CommonUtility::getPlaceholderPhoto('item')),
				  'item_size_id'=>$val['item_size_id'],
				  'qty'=>intval($val['qty']),				  
				  'special_instructions'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['special_instructions'])),				  
				  'if_sold_out'=>$val['if_sold_out'],
				  'addon_items'=>$addon_items,
				  'attributes'=>$attributes,
				  'tax'=>$tax_use
				);								
			}			
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function itemCount($cart_uuid='')
	{
		$stmt="
		SELECT SUM(qty) as item_count
		FROM {{cart}}
		WHERE cart_uuid=".q($cart_uuid)."
		";
		if( $res = Yii::app()->db->createCommand($stmt)->queryRow() ){
			return $res['item_count'];
		}
		return 0;
	}
	
	public static function getSubcategory($cart_uuid='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.subcat_id , b.subcategory_name
		FROM {{subcategory}} a			
		LEFT JOIN {{subcategory_translation}} b
		ON
		a.subcat_id = b.subcat_id
		WHERE b.language = ".q($lang)."
		AND a.subcat_id IN (
		  select subcat_id from {{cart_addons}}
		  where cart_uuid =".q($cart_uuid)."
		)
		";				
		if( $res = Yii::app()->db->createCommand($stmt)->queryAll() ){
			$data = array();
			foreach ($res as $val) {				
				$data[$val['subcat_id']] = array(
				  'subcat_id'=>$val['subcat_id'],
				  'subcategory_name'=>$val['subcategory_name']
				);
			}
			return $data;
		}
		return false;
	}
	
	public static function getSize($cart_uuid='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.item_size_id,a.size_name,
		a.price , a.discount,
		a.discount_type,		
		 (
		  select count(*) from {{view_item_lang_size}}
		  where item_size_id = a.item_size_id 		  
		  and CURDATE() >= discount_start and CURDATE() <= discount_end
		 ) as discount_valid
		
		FROM {{view_item_lang_size}}	a		
				
		WHERE a.language IN ('',".q($lang).")
		AND a.item_size_id IN (
		 select item_size_id from {{cart}}
		 where cart_uuid =".q($cart_uuid)."
		)
		";		
				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {				
				$data[$val['item_size_id']] = array(
				  'item_size_id'=>$val['item_size_id'],
				  'size_name'=>$val['size_name'],
				  'price'=>$val['price'],
				  //'discount'=>$val['discount'],
				  'discount'=>$val['discount_valid']>0?$val['discount']:0,
				  'discount_type'=>$val['discount_type'],
				  'discount_valid'=>$val['discount_valid'],				  
				);
			}
			return $data;
		}
		return false;
	}
	
	public static function getAddonItems($cart_uuid='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.sub_item_id,
		b.sub_item_name, b.item_description,
		a.price, a.photo, a.path
		
		FROM {{subcategory_item}} a		
		LEFT JOIN {{subcategory_item_translation}} b
		ON
		a.sub_item_id = b.sub_item_id
		WHERE a.status = 'publish'		
		AND b.language = ".q($lang)."
		AND a.sub_item_id IN (		  
		  select sub_item_id from {{cart_addons}}
		  where cart_uuid =".q($cart_uuid)."
		)
		ORDER BY sequence,id ASC
		";		
					
		if( $res = Yii::app()->db->createCommand($stmt)->queryAll() ){
			$data = array();
			foreach ($res as $val) {	
				$sub_item_id = (integer) $val['sub_item_id'];
				$data[$sub_item_id] = array(
				  'sub_item_id'=>$sub_item_id,
				  'sub_item_name'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['sub_item_name'])),
				  'item_description'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['item_description'])),
				  'price'=>(float)$val['price'],
				  'pretty_price'=>Price_Formatter::formatNumber($val['price']),
				  'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image_thumbnail,
				               CommonUtility::getPlaceholderPhoto('item'))
				);
			}
			return $data;
		}
		return false;
	}
	
	public static function getMetaCooking($cart_uuid='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.cook_id,a.cooking_name 
		FROM {{cooking_ref_translation}} a
		WHERE a.language = ".q($lang)."
		AND a.cook_id IN (
		  select meta_id from {{cart_attributes}}
		  where cart_uuid =".q($cart_uuid)."
		  and meta_name = 'cooking_ref'
		)
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$id = (integer) $val['cook_id'];
				$data[$id] = Yii::app()->input->xssClean($val['cooking_name']);
			}
			return $data;
		}
		return false;
	}
	
	public static function getMetaIngredients($cart_uuid='', $lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.ingredients_id,a.ingredients_name 
		FROM {{ingredients_translation}} a
		WHERE a.language = ".q($lang)."
		AND a.ingredients_id IN (
		  select meta_id from {{cart_attributes}}
		  where cart_uuid =".q($cart_uuid)."
		  and meta_name = 'ingredients'
		)
		";					
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$id = (integer) $val['ingredients_id'];
				$data[$id] = Yii::app()->input->xssClean($val['ingredients_name']);
			}
			return $data;
		}
		return false;
	}
	
	public static function remove($cart_uuid='',$row_id='')
	{
		
		$cart = AR_cart::model()->find('cart_uuid=:cart_uuid AND cart_row=:cart_row', 
		array(':cart_uuid'=>$cart_uuid, ':cart_row'=>$row_id ));		
	
		if($cart){
			
			AR_cart::model()->deleteAll('cart_uuid=:cart_uuid AND cart_row=:cart_row',array(
			  ':cart_uuid'=>$cart_uuid,
			  ':cart_row'=>$row_id 
			));
			
			AR_cart_addons::model()->deleteAll('cart_uuid=:cart_uuid AND cart_row=:cart_row',array(
			  ':cart_uuid'=>$cart_uuid,
			  ':cart_row'=>$row_id 
			));
			
			AR_cart_attributes::model()->deleteAll('cart_uuid=:cart_uuid AND cart_row=:cart_row',array(
			  ':cart_uuid'=>$cart_uuid,
			  ':cart_row'=>$row_id 
			));
					
		    return true;
		}
		throw new Exception( 'row not found' );
	}
	
	public static function clear($cart_uuid='')
	{
		$cart = AR_cart::model()->find('cart_uuid=:cart_uuid', 
		array(':cart_uuid'=>$cart_uuid));		
		if($cart){
			
			AR_cart::model()->deleteAll('cart_uuid=:cart_uuid',array(
			  ':cart_uuid'=>$cart_uuid			  
			));
			
			AR_cart_addons::model()->deleteAll('cart_uuid=:cart_uuid',array(
			  ':cart_uuid'=>$cart_uuid,			  
			));
			
			AR_cart_attributes::model()->deleteAll('cart_uuid=:cart_uuid',array(
			  ':cart_uuid'=>$cart_uuid,			  
			));
			
			return true;
		}
		throw new Exception( 'cart uuid not found' );
	}
	
	public static function addCondition($data = array() )
	{
		if(is_array($data) && count($data)>=1){
		   CCart::$condition[] = $data;
		}
	}
	
	public static function getCondition()
	{
		if(is_array(CCart::$condition) && count(CCart::$condition)>=1){
		   return CCart::$condition;
		}
		return false;
	}
				
	public static function isEmpty()
	{
		if(is_array(self::$content) && count(self::$content)>=1){
			return false;
		}
		return true;
	}
	
	public static function addTaxCondition($data = array() )
	{
		if(is_array($data) && count($data)>=1){
		   CCart::$tax_condition = $data;
		}
	}
	
	public static function getTaxCondition()
	{
		if(is_array(CCart::$tax_condition) && count(CCart::$tax_condition)>=1){
		   return CCart::$tax_condition;
		}
		return false;
	}
	
	public static function setTaxType($tax_type='')
	{
		if(!empty($tax_type)){
			self::$tax_type = $tax_type;
		}
	}
	
	public static function addTaxGroup($key=0,$data = array() )
	{		
		$current_data=0;
		if(isset(CCart::$tax_group[$key])){
			$current_data = CCart::$tax_group[$key]['total'];
		}
		CCart::$tax_group[$key] = array(
		  'tax_in_price'=>isset($data['tax_in_price'])?$data['tax_in_price']:false,
		  'total'=>floatval($current_data) + floatval($data['tax_total'])
		);
	}
	
	public static function getTaxGroup()
	{
		if(is_array(CCart::$tax_group) && count(CCart::$tax_group)>=1){
		   return CCart::$tax_group;
		}
		return false;
	}
	
	public static function addTax($tax=array(), $total=0){
		if(is_array($tax) && count($tax)>=1 && $total>0){
			foreach ($tax as $tax_item) {
				$tax_rate = isset($tax_item['tax_rate']) ? floatval($tax_item['tax_rate']) :0;
				$tax_rate = $tax_rate/100;
				$tax_total = $tax_rate*floatval($total);
				
				self::addTaxGroup($tax_item['tax_id'], array(
				 'tax_total'=>$tax_total,
				 'tax_in_price'=>$tax_item['tax_in_price']
				));
			}
		}
	}
	
	public static function getTaxType()
	{
		if(!empty(self::$tax_type)){
			return self::$tax_type;
		}
		return false;
	}
		
	protected static function valueIsPercentage($value='')
    {
        return (preg_match('/%/', $value) == 1);
    }
        
    protected static function valueIsToBeSubtracted($value)
    {
        return (preg_match('/\-/', $value) == 1);
    }
       
    protected static function valueIsToBeAdded($value)
    {
        return (preg_match('/\+/', $value) == 1);
    }
    
    protected static function normalizePrice($price)
    {
        return (is_string($price)) ? floatval(CCart::cleanValue($price)) : $price;
    }
    
    protected static function cleanValue($value)
    {
        return str_replace(array('%','-','+'),'',$value);
    }

	public static function cleanValues($value)
    {
        return str_replace(array('%','-','+'),'',$value);
    }
    
    public static function cleanNumber($value)
    {
    	return self::cleanValue($value);
    }
    
    public static function getSubTotal()
    {    	
    	$sub_total = 0; $sub_total_without_cnd = 0; $taxable_subtotal = 0;  $sub_total_without_admin_discount = 0;
    	    	
    	if(!CCart::isEmpty()){
    		$items = isset(CCart::$content['content'])?CCart::$content['content']:'';
    		$size = isset(CCart::$content['size'])?CCart::$content['size']:'';
    		$addon_items = isset(CCart::$content['addon_items'])?CCart::$content['addon_items']:'';
    		foreach ($items as $val) {    	    			
    			    			
    			$qty = intval($val['qty']);
    			
    			$item_size_id = isset($val['item_size_id'])?(integer)$val['item_size_id']:0;
    			$item_price_data = isset($size[$item_size_id])?$size[$item_size_id]:'';    			    			
    			$item_price = CCart::parseItemPrice($item_price_data);
    			$total_price = $qty*$item_price;
    			$sub_total+=$total_price;
    			
    			$addon_total = 0;
    			if(is_array($val['addon_items']) && count($val['addon_items'])>=1){
    				foreach ($val['addon_items'] as $addon_category) {
    					foreach ($addon_category as $addons_item) {    						
    						if(is_array($addons_item) && count($addons_item)>=1){    							
    							foreach ($addons_item as $addon_items_id=>$addon) {    								
    								$addon_price = isset($addon_items[$addon_items_id]['price'])?$addon_items[$addon_items_id]['price']:0;
    								$addon_qty = isset($addon['qty'])?(integer)$addon['qty']:0;
    								$multi_option = isset($addon['multi_option'])?$addon['multi_option']:'';    								
    								if($multi_option=="multiple"){
    									$addon_total_price = floatval($addon_price)*intval($addon_qty); 
    								} else $addon_total_price = floatval($addon_price)*intval($qty); 
    								$addon_total+= $addon_total_price;
    								$sub_total+=$addon_total_price;
    								
    							}
    						}
    					}
    				}
    			} // addons item 
    			
    			/*ADD TAX*/
    			if(isset($val['tax'])){    				    				    				
    				$total_to_tax = floatval($total_price)+floatval($addon_total);        						
    				self::addTax($val['tax'], $total_to_tax);
    			}
    			
    		} // items
    		    		   	    
    			
    		$sub_total_without_cnd = $sub_total;
    		$sub_total_without_admin_discount = $sub_total;
    		/*CONDITION*/
    		if ( $condition = CCart::getCondition()){    			
    			foreach ($condition as $val) {       				
    				if($val['target']=="subtotal"){         					
    					$raw_sub_total = CCart::apply($sub_total,$val['value']);  
    					$sub_total = $raw_sub_total;
    					if(isset($val['voucher_owner'])){
    						if($val['voucher_owner']=='admin'){
    							//
    						} else $sub_total_without_admin_discount = $raw_sub_total;
    					} else $sub_total_without_admin_discount = $raw_sub_total;
    				}
    			}
    		}    		
    		    		    		
    	}
    	
    	return array(
    	  'sub_total'=>floatval($sub_total),
    	  'taxable_subtotal'=>floatval($taxable_subtotal),
    	  'sub_total_without_cnd'=>$sub_total_without_cnd,
    	  'sub_total_without_admin_discount'=>$sub_total_without_admin_discount,
    	);
    }
    
    public static function getSubTotal_lessDiscount()
    {
    	$subtotal = CCart::getSubTotal();    	
    	$sub_total = floatval($subtotal['sub_total']);
    	return $sub_total;
    }
    
    public static function getSubTotal_TobeCommission()
    {
    	$subtotal = CCart::getSubTotal();    	
    	$sub_total = $subtotal['sub_total_without_admin_discount']>0?floatval($subtotal['sub_total_without_admin_discount']):$subtotal['sub_total'];
    	return $sub_total;
    }
    
    public static function getTotal()
    {   
    	self::$tax_group = array(); 	
    	$results = CCart::getSubTotal();    	
    	$sub_total = $results['sub_total'];
    	$taxable_subtotal = $results['taxable_subtotal'];  
    	    	
    	   
    	/*CONDITION*/
    	if ( $condition = CCart::getCondition()){    		
    		foreach ($condition as $val) {    			
    			if($val['target']=="total"){    				
    				    				
    				/*ADD TAX*/    					    					
			        if(isset($val['tax']) && isset($val['taxable'])){        						
			            if($val['taxable']){    										            	
			                $total_to_tax = isset($val['value'])? floatval($val['value']) : 0;			                
			                self::addTax($val['tax'], $total_to_tax);
			            }
			        }    
			        
			        if($val['type']=="tax"){
			           $tax_group_data = self::getTaxGroup();			           
                       $tax_value = isset($tax_group_data[$val['tax_id']]) ? $tax_group_data[$val['tax_id']]['total'] : 0;
                       $tax_in_price = isset($tax_group_data[$val['tax_id']]) ? $tax_group_data[$val['tax_id']]['tax_in_price'] : false;
                       if($tax_in_price==false){                       	  
			              $sub_total = CCart::apply($sub_total,$tax_value);  
			           } 
			        } else {
			        	$sub_total = CCart::apply($sub_total,$val['value']); 
			        }
    			}
    		}
    	}
    	    	    	
    	return $sub_total;
    }
    
    public static function getItems()
    {    	    	    
    	$results = array();
    	if(!CCart::isEmpty()){
    		$items = isset(CCart::$content['content'])?CCart::$content['content']:'';
    		$size = isset(CCart::$content['size'])?CCart::$content['size']:'';    		
    		$subcategory = isset(CCart::$content['subcategory'])?CCart::$content['subcategory']:'';
    		$addon_items = isset(CCart::$content['addon_items'])?CCart::$content['addon_items']:'';
    		$attributes = isset(CCart::$content['attributes'])?CCart::$content['attributes']:'';
    		    		
    		foreach ($items as $val) {    			
    			$qty = intval($val['qty']);
    			$item_size_id = isset($val['item_size_id'])?(integer)$val['item_size_id']:0;
    			$item_price_data = isset($size[$item_size_id])?$size[$item_size_id]:'';    			
    			$item_price_raw = isset($item_price_data['price'])?(float)$item_price_data['price']:0;
    			$item_price = CCart::parseItemPrice($item_price_data);
    			
    			/*ADDON*/
    			$results_addon = array(); $results_addon_item = array();
    			if(is_array($val['addon_items']) && count($val['addon_items'])>=1){
    				foreach ($val['addon_items'] as $addon_category) {    					
    					foreach ($addon_category as $addon_cat_id => $addons_item) {   
    						$results_addon_item = array();
    						if(is_array($addons_item) && count($addons_item)>=1){
    							foreach ($addons_item as $sub_item_id=>$sub_item_data) {     								
    								if(isset($addon_items[$sub_item_id])){
	    								$multi_option = isset($sub_item_data['multi_option'])?$sub_item_data['multi_option']:'';
	    								$addons_qty = isset($sub_item_data['qty'])?intval($sub_item_data['qty']):1;    	
	    								$addons_price = isset($addon_items[$sub_item_id]['price'])?floatval($addon_items[$sub_item_id]['price']):0;    	    								
	    								if($multi_option=="multiple"){    					
	    									$addons_total = $addons_qty*$addons_price; 				
	    									$addon_items[$sub_item_id]['qty']=$addons_qty;
	    									$addon_items[$sub_item_id]['addons_total']=$addons_total;    							
	    								} else {
	    									$addons_total = $qty*$addons_price; 
	    									$addon_items[$sub_item_id]['qty']=$qty;
	    									$addon_items[$sub_item_id]['addons_total']=$addons_total;    									
	    								}
	    								$addon_items[$sub_item_id]['multiple'] = $multi_option;
	    								$addon_items[$sub_item_id]['pretty_addons_total']=Price_Formatter::formatNumber($addons_total);
	    								$results_addon_item[]=$addon_items[$sub_item_id];
    								}
    							}
    						}
    						 						
    						$results_addon[] = array(
    						  'subcat_id'=>$addon_cat_id,
    						  'subcategory_name'=>isset($subcategory[$addon_cat_id]['subcategory_name'])?$subcategory[$addon_cat_id]['subcategory_name']:'',
    						  'addon_items'=>$results_addon_item
    						);    						
    					}
    				}
    			}
    			
    			/*ATTRIBUTES*/
    			$attributes_list=array(); $attributes_list_raw = array();
    			if(is_array($val['attributes']) && count($val['attributes'])>=1 ){
    				foreach ($val['attributes'] as $meta_key=>$data_attributes) {    					
    					$attributes_items = array(); $attributes_items_raw = array();
    					if(is_array($data_attributes) && count($data_attributes)>=1){
    						foreach ($data_attributes as $meta_value) {    	    							
    							if(isset($attributes[$meta_key])){
    							   $attributes_items[] = isset($attributes[$meta_key][$meta_value])?$attributes[$meta_key][$meta_value]:'';
    							   $attributes_items_raw[$meta_value] = isset($attributes[$meta_key][$meta_value])?$attributes[$meta_key][$meta_value]:'';
    							}
    						}
    						$attributes_list[$meta_key] = $attributes_items;
    						$attributes_list_raw[$meta_key] = $attributes_items_raw;
    					}
    				}
    			}    
    			    			    		
    			$price = isset($item_price_data['price'])?(float)$item_price_data['price']:0;
    			$total = intval($val['qty']) * floatval($price);
    			$total_after_discount = intval($val['qty']) * floatval($item_price);
    			$results[] = array(
    			   'cart_row'=>$val['cart_row'],
    			   'cat_id'=>$val['cat_id'],
    			   'item_id'=>$val['item_id'],
    			   'item_token'=>$val['item_token'],
    			   'item_name'=>$val['item_name'],
    			   'url_image'=>$val['url_image'],
    			   'special_instructions'=>$val['special_instructions'],
    			   'if_sold_out'=>$val['if_sold_out'],
    			   'qty'=>intval($val['qty']),
    			   'price'=>array(
    			     'item_size_id'=>$val['item_size_id'],
    			     'price'=>$price,
    			     'size_name'=>isset($item_price_data['size_name'])?$item_price_data['size_name']:'',
    			     'discount'=>isset($item_price_data['discount'])?(float)$item_price_data['discount']:'',
    			     'discount_type'=>isset($item_price_data['discount_type'])?$item_price_data['discount_type']:'',
    			     'price_after_discount'=>(float)$item_price,
    			     'pretty_price'=>Price_Formatter::formatNumber($item_price_raw),
    			     'pretty_price_after_discount'=>Price_Formatter::formatNumber($item_price),
    			     'total'=>$total, 
    			     'pretty_total'=>Price_Formatter::formatNumber($total),
    			     'total_after_discount'=>$total_after_discount,
    			     'pretty_total_after_discount'=>Price_Formatter::formatNumber($total_after_discount),
    			   ),
    			   'attributes'=>$attributes_list,    			   
    			   'attributes_raw'=>$attributes_list_raw,
    			   'addons'=>$results_addon,
    			   'tax'=>isset($val['tax'])?$val['tax']:'',
    			);
    		}
    	}
    	return $results;
    }
    
    public static function getSummary()
    {    	
    	$results = array(); self::$tax_group = array();
    	if(!CCart::isEmpty()){
    		$resp = CCart::getSubTotal();          		
    	    $sub_total = $resp['sub_total'];
    	    $sub_total_without_cnd = $resp['sub_total_without_cnd'];

    	    	    	     
    		if ( $condition = CCart::getCondition()){
    			    			
    			/*SUB TOTAL*/
    			foreach ($condition as $val) {    				    				    				
    				if($val['target']=="subtotal"){           					
    					$value = CCart::summary($val['value'],$sub_total_without_cnd);    					
    					$results[] = array(
    					 'name'=>$val['name'],
    					 'value'=>isset($value['value'])?$value['value']:0,
    					 'raw'=>isset($value['raw'])?$value['raw']:0,
    					 'type'=>$val['type'],
    					);
    				}
    			}
    			
    			$value = CCart::summary( $sub_total );
    			$results[]=array(
    			  'name'=>t("Sub total"),
    			  'value'=>isset($value['value'])?$value['value']:0,
    			  'raw'=>isset($value['raw'])?$value['raw']:0,
    			  'type'=>'subtotal',
    			);
    			
    			/*TOTAL*/
    			foreach ($condition as $val) {    	    				
    				if($val['target']=="total"){    	
    									
    					$value = CCart::summary($val['value'],$sub_total);
    					
    					/*ADD TAX*/    					    					
    					if(isset($val['tax']) && isset($val['taxable'])){        						
    						if($val['taxable']){    							
			    				$total_to_tax = isset($value['raw'])? floatval($value['raw']) : 0;		    				
			    				self::addTax($val['tax'], $total_to_tax);
    						}
		    			}    
		    				
		    			if($val['type']=="tax"){
		    				$tax_group_data = self::getTaxGroup();		    				
		    				$tax_value = isset($tax_group_data[$val['tax_id']]) ? $tax_group_data[$val['tax_id']]['total'] : 0;
		    				$tax_in_price = isset($tax_group_data[$val['tax_id']]) ? $tax_group_data[$val['tax_id']]['tax_in_price'] : false;
		    				$results[] = array(
	    					 'name'=>$val['name'],
	    					 'value'=>Price_Formatter::formatNumber($tax_value),
	    					 'raw'=>$tax_value,
	    					 'type'=>$val['type'],
	    					);	    
	    					if($tax_in_price==false){
	    						$sub_total = CCart::apply($sub_total,$tax_value);  
	    					} 
		    			} else {
	    					$results[] = array(
	    					 'name'=>$val['name'],
	    					 'value'=>isset($value['value'])?$value['value']:0,
	    					 'raw'=>isset($value['raw'])?$value['raw']:0,
	    					 'type'=>$val['type'],
	    					);	    					
	    					$sub_total = CCart::apply($sub_total,$val['value']);    				
		    			}    							    			    					   	
    				}
    			}
    			
    			$value = CCart::summary( $sub_total );
    			$results[]=array(
    			  'name'=>t("Total"),
    			  'value'=> isset($value['value'])?$value['value']:0,
    			  'raw'=>isset($value['raw'])?$value['raw']:0,
    			  'type'=>'total',
    			);
    		} else {
    			$value = CCart::summary( $sub_total );
    			$results[]=array(
    			  'name'=>t("Sub total"),
    			  'value'=> isset($value['value'])?$value['value']:0,
    			  'raw'=>isset($value['raw'])?$value['raw']:0,
    			  'type'=>'subtotal',
    			);
    			$results[]=array(
    			  'name'=>t("Total"),
    			  'value'=> isset($value['value'])?$value['value']:0,
    			  'raw'=>isset($value['raw'])?$value['raw']:0,
    			  'type'=>'total',
    			);
    		}
    		    		
    		return $results;
    	}
    	return false;
    }
    
    public static function parseItemPrice($value='')
    {
    	$price = 0;
    	if(is_array($value) && count($value)>=1){
    		if ($value['discount']>0 && $value['discount_valid']){
    			$raw_price = isset($value['price'])?floatval($value['price']):0;
    			$raw_discount = isset($value['discount'])?floatval($value['discount']):0;
    			if ( $value['discount_type']=="percentage"){    				
    				$price = floatval($raw_price) - ((floatval($raw_discount)/100)*floatval($raw_price));
    			} else $price = floatval($raw_price) - floatval($raw_discount);
    		} else $price = floatval($value['price']);
    	}
    	return $price;
    }
    
    public static function apply($total=0, $condition_val=0)
    {    	
    	$results = 0;    	
    	if ( CCart::valueIsPercentage($condition_val)){
    		
    		$value = (float) CCart::cleanValue($condition_val);    		
    		$raw_value = (float)$total * ($value/100);    		    		
    		
    		if ( CCart::valueIsToBeSubtracted($condition_val)){ 
    			$results = floatval($total) - floatval($raw_value);
    		} else $results = floatval($total) + floatval($raw_value);
    	} else {
    		$raw_value = (float) CCart::cleanValue($condition_val); 
    		if ( CCart::valueIsToBeSubtracted($condition_val)){
    			$results = floatval($total) - floatval($raw_value);
    		} else $results = floatval($total) + floatval($raw_value);
    	}
    	return $results;
    }
    
    public static function summary($condition_val=0,$total=0)
    {    	
    	$results = '';  
    	$raw_value = (float) CCart::cleanValue($condition_val);     	
    	if ( CCart::valueIsPercentage($condition_val)){    		    		
    		$value = (float) CCart::cleanValue($condition_val);        		
    		$raw_value = (float)$total * ($value/100);        		
    		if ( CCart::valueIsToBeSubtracted($condition_val)){ 
    			$total  = t("({{total}})",array(
    			 '{{total}}'=>Price_Formatter::formatNumber($raw_value)
    			));
    			$results = array(
    			  'value'=>$total,
    			  'raw'=>$raw_value
    			);
    		} else $results = array(
    		  'value'=>Price_Formatter::formatNumber($raw_value),
    		  'raw'=>$raw_value,
    		);
    	} else {    		
    		if ( CCart::valueIsToBeSubtracted($condition_val)){    			
    			$total  = t("({{total}})",array(
    			 '{{total}}'=>Price_Formatter::formatNumber($raw_value)
    			));
    			$results = array(
    			  'value'=>$total,
    			  'raw'=>$condition_val
    			);
    		} else $results = array(
    		  'value'=>Price_Formatter::formatNumber($raw_value),
    		  'raw'=>$raw_value
    		);
    	}    	
    	return $results;
    }
    
    public static function getPackagingFee()
    {
    	if(CCart::$packaging_fee>0){
    		return CCart::$packaging_fee;
    	}
    	return false;
    }
    
    public static function savedAttributes($cart_uuid='',$meta_name='', $meta_id='')
    {    	
		$model = AR_cart_attributes::model()->find('cart_uuid=:cart_uuid AND meta_name=:meta_name', 
		   array(
		      ':cart_uuid'=>$cart_uuid, 
		     ':meta_name'=> $meta_name )
		   ); 
		   		   
		if($model){			
			if($model->meta_id!=$meta_id){
				$model->meta_id = $meta_id;
				$model->update();
			}
		} else {
			$insert = new AR_cart_attributes;
			$insert->cart_row=0;
			$insert->cart_uuid=$cart_uuid;
			$insert->meta_name=$meta_name;
			$insert->meta_id = $meta_id;
			$insert->save();
		}
    }
    
    public static function getAttributes($cart_uuid='',$meta_name='')
    {
    	$model = AR_cart_attributes::model()->find('cart_uuid=:cart_uuid AND meta_name=:meta_name', 
		   array(
		      ':cart_uuid'=>$cart_uuid, 
		     ':meta_name'=> $meta_name )
		   ); 
		if($model){
			return $model;
		}
		return false;
    }
    
    public static function deleteAttributes($cart_uuid='',$meta_name='')
    {
    	$model = AR_cart_attributes::model()->find('cart_uuid=:cart_uuid AND meta_name=:meta_name', 
		   array(
		      ':cart_uuid'=>$cart_uuid, 
		     ':meta_name'=> $meta_name )
		   ); 
		if($model){
			if($model->delete()){
				return true;
			}
		}
		return false;
    }
    
    public static function deleteAttributesAll($cart_uuid='',$meta = array() ) 
    {    	
    	$meta_name = '';    
    	foreach ($meta as $val) {
    		$meta_name.=q($val).",";
    	}
    	$meta_name = substr($meta_name,0,-1);
    	
    	$stmt="
    	DELETE
    	FROM {{cart_attributes}}
    	WHERE cart_uuid=".q($cart_uuid)."
    	AND meta_name IN ($meta_name)
    	";    	
    	if(Yii::app()->db->createCommand($stmt)->query()){
    		return true;
    	}
    	return false;
    }
    
    public static function getAttributesAll($cart_uuid='',$meta = array() ) 
    {    	
    	$meta_name = '';    
    	foreach ($meta as $val) {
    		$meta_name.=q($val).",";
    	}
    	$meta_name = substr($meta_name,0,-1);
    	
    	$stmt="
    	SELECT meta_name,meta_id
    	FROM {{cart_attributes}}
    	WHERE cart_uuid=".q($cart_uuid)."
    	AND meta_name IN ($meta_name)
    	";    	    	
    	if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
    		$data = array();    		
    		foreach ($res as $val) {
    			$data[$val['meta_name']]=$val['meta_id'];    			
    		}
    		return $data;
    	}
    	return false;
    }
    
    public static function cartTransaction($cart_uuid='',$meta_name='',$merchant_id='')
    {    	
    	$transaction='';
    	$stmt="
    	SELECT a.service_code
    	FROM {{services}} a
    	WHERE 
		a.status='publish'
		and
		a.service_code IN (
		  select meta_value from {{merchant_meta}}
		  where meta_name='services'
		  and merchant_id = ".q($merchant_id)."
		  and meta_value IN (
		    select meta_id from {{cart_attributes}}
		    where cart_uuid = ".q($cart_uuid)."
		    and meta_name = ".q($meta_name)."
		  )
		)
    	";    	    	    	    	
    	if( $res = Yii::app()->db->createCommand($stmt)->queryRow() ){       		
    		$transaction = $res['service_code'];
    	} else $transaction = CCheckout::getFirstTransactionType($merchant_id,Yii::app()->language);
    	return $transaction;
    }
   
    public static function getDistanceOld($cart_uuid='',$merchant_id='',$current_place_id='')
	{
		$stmt="
		SELECT a.reference_id as place_id, a.latitude,a.longitude,a.address1,a.address2,
		a.country,a.postal_code,a.formatted_address,
		(
		  select concat(latitude,',',lontitude,',',distance_unit,',',delivery_distance_covered)
		  from {{merchant}}
		  where merchant_id = ".q($merchant_id)."
		) as merchant_location,
		
		(
		select meta_id from {{cart_attributes}}
		where cart_uuid = ".q($cart_uuid)."
		and meta_name ='address'
		) as address_components
		
		FROM {{map_places}} a
		WHERE reference_id  = (
		  select meta_id from {{cart_attributes}}
		  where cart_uuid = ".q($cart_uuid)."
		  and meta_name = ".q(Yii::app()->params->local_id)."
		)
		";			
		//dump($stmt);
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){							
			
			$merchant_location = explode(",",$res['merchant_location']);
			$merchant_lat = isset($merchant_location[0])?$merchant_location[0]:'';
			$merchant_lng = isset($merchant_location[1])?$merchant_location[1]:'';	
			$unit = isset($merchant_location[2])?$merchant_location[2]:Yii::app()->params['settings']['home_search_unit_type'];	
			$distance_covered = isset($merchant_location[3])?$merchant_location[3]:'';	
			
			$atts_data = array('delivery_distance','delivery_distance_unit','distance_covered','merchant_lat','merchant_lng');
			$atts = CCart::getAttributesAll($cart_uuid,$atts_data);						
			$atts_merchant_lat = isset($atts['merchant_lat'])?$atts['merchant_lat']:'';
			$atts_merchant_lng = isset($atts['merchant_lng'])?$atts['merchant_lng']:'';
					
			$place_id = $res['place_id'];
			$customer_lat = $res['latitude'];
			$customer_lng = $res['longitude'];
			
			if ( $address = json_decode($res['address_components'],true)){
				$customer_lat = $address['latitude'];
				$customer_lng = $address['longitude'];
			}					
						
			MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];		   
		    MapSdk::setKeys(array(
		     'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
		     'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
		    ));
		    		    		    		    		    		  
		    MapSdk::setMapParameters(array(
		      'from_lat'=>$merchant_lat,
		      'from_lng'=>$merchant_lng,
		      'to_lat'=>$customer_lat,
		      'to_lng'=>$customer_lng,
		      'place_id'=>$place_id,
		      'unit'=>$unit,
		      'mode'=>'driving'
		    ));
		    		    			    
		    if($resp = MapSdk::distance()){
		    	$resp['found']=false;
		    	$resp['distance_covered'] = $distance_covered;
		    	$resp['merchant_lat']=$merchant_lat;
		    	$resp['merchant_lng']=$merchant_lng;
		    	return $resp;
		    }
		} else CCart::savedAttributes($cart_uuid, Yii::app()->params->local_id, $current_place_id);
		return false;
	}			
		
	public static function shippingRate($merchant_id='',$charge_type='',$shipping_type='', $distance='' , $unit='')
	{
		$shipping_type = !empty($shipping_type)?$shipping_type:'standard';
				
		$and = "";
		if($charge_type=="dynamic"){
			$and.="
			AND a.distance_from<=".q( floatval($distance) )."
		    AND a.distance_to>=".q( floatval($distance) )."		
		    AND a.shipping_units = ".q($unit)."   
			";
		}
		
		$stmt="
		SELECT 
		a.id, 
		a.charge_type,
		a.shipping_type,
		a.distance_from,
		a.distance_to,
		a.shipping_units,
		a.distance_price,
		a.minimum_order,
		a.minimum_order,
		a.maximum_order,
		a.estimation
		
		FROM {{shipping_rate}} a
		WHERE a.merchant_id = ".q($merchant_id)."				
		AND charge_type=".q($charge_type)."
		AND shipping_type=".q($shipping_type)."	
		$and	
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			return $res;
		} 
		return false;
	}
	
	public static function getMaxMinEstimationOrder($merchant_id="",$transaction_type='' , $charge_type='',$distance='', $unit='')
	{
		$resp = array();
		//dump("$transaction_type=>$charge_type=>$distance=>$unit");
		if($transaction_type=="delivery" && $charge_type=="dynamic"){
			$resp = CCart::shippingRate($merchant_id,$charge_type,'standard',$distance,$unit);
		} else if ( $transaction_type=="delivery" && $charge_type=="fixed" ) {
			$resp = CCart::shippingRate($merchant_id,$charge_type,'',0,'');
		} else if ( $transaction_type=="pickup" || $transaction_type =="dinein") {
			$resp = CCart::getEstimation($merchant_id,$transaction_type);
		} 		
		if(is_array($resp) && count($resp)>=1){
			return $resp;
		}
		return false;
	}
	
	public static function getEstimation($merchant_id='',$transaction_type='')
	{
		$stmt="
		SELECT 
		estimation,minimum_order,maximum_order
		FROM {{shipping_rate}}
		WHERE service_code = ".q($transaction_type)."
		AND merchant_id = ".q($merchant_id)."
		LIMIT 0,1
		";				
		if( $res = Yii::app()->db->createCommand($stmt)->queryRow() ){
			return $res;
		}
		return false;	
	}
	
	public static function cartCondition($cart_uuid='', $condition_value = CCart::CONDITION_NAME)
	{
		$in = CommonUtility::arrayToQueryParameters( $condition_value );		
		$stmt="
		SELECT id,meta_name,meta_id as meta_value
		FROM {{cart_attributes}}
		WHERE cart_uuid = ".q($cart_uuid)."
		AND meta_name IN ($in)
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			return $res;
		}
		return false;	
	}
	
	public static function getTips($cart_uuid='',$merchant_id='', $merchant_default_tip=0)
	{
		if ( $tips = CCart::getAttributes($cart_uuid,'tips')){
			if($tips->meta_id>0){
				return floatval($tips->meta_id);
			}
		} else {
			if($merchant_default_tip>0){
				return floatval($merchant_default_tip);
			}
		}
		return false;
	}
	
	public static function getLocalDistance($local_id='',$unit='',$merchant_lat='',$merchant_lng='')
	{		
		if(!empty($local_id)){
			$model = AR_map_places::model()->find('reference_id=:reference_id',
			    array(':reference_id'=>$local_id
			)); 		
			if($model){			
				$distance = CMaps::getLocalDistance($unit,$model->latitude,$model->longitude,$merchant_lat,$merchant_lng);
				return array(
				 'distance'=>$distance,
				 'address_component'=>array(
				   'place_id'=>$model->reference_id,
				   'latitude'=>$model->latitude,
				   'longitude'=>$model->longitude,
				   'address1'=>$model->address1,
				   'address2'=>$model->address2,
				   'formatted_address'=>$model->formatted_address
				 )
				);
			}
		}
		return false;
	}
	
	public static function getDistance($client_id='', $place_id='',$unit='',$merchant_lat='',$merchant_lng='')
	{
		 if(!empty(MapSdk::$map_provider)){
			 //
		 } else {
			MapSdk::$map_provider = Yii::app()->params['settings']['map_provider'];		   
			MapSdk::setKeys(array(
			'google.maps'=>Yii::app()->params['settings']['google_geo_api_key'],
			'mapbox'=>Yii::app()->params['settings']['mapbox_access_token'],
			));
		 }
	     		   
	     try {
	     	$address = CClientAddress::getAddress($place_id,$client_id);		
	     		     	
	     	$params = array(
		      'from_lat'=>$merchant_lat,
		      'from_lng'=>$merchant_lng,
		      'to_lat'=>$address['latitude'],
		      'to_lng'=>$address['longitude'],		      
		      'unit'=>$unit,
		      'mode'=>'driving'
		    );		    		    
	     	MapSdk::setMapParameters($params);		    
		    $distance =  MapSdk::distance();
		    
		    return array(
			 'distance'=>$distance['distance'],
			 'address_component'=>array(
			   'place_id'=>$address['place_id'],
			   'latitude'=>$address['latitude'],
			   'longitude'=>$address['longitude'],
			   'address1'=>$address['address']['address1'],
			   'address2'=>$address['address']['address2'],
			   'formatted_address'=>$address['address']['formatted_address'],
			 )
			);
		    	     	
	     } catch (Exception $e) {
	     	//dump($e->getMessage());
	     	return false;
	     }
	}	

	public static function addOrderToCart($merchant_id=0,$items=array())
	{		
		$cart_uuid = CommonUtility::generateUIID();
		if(is_array($items) && count($items)>=1){
			foreach ($items as $val) {
				$cart_row = CommonUtility::generateUIID();				
				$items = new AR_cart;
				$items->cart_row = $cart_row;
				$items->cart_uuid = $cart_uuid;
				$items->merchant_id = intval($merchant_id);
				$items->cat_id = intval($val['cat_id']);
				$items->item_token = $val['item_token'];
				$items->item_size_id = intval($val['price']['item_size_id']);
				$items->qty = (integer)$val['qty'];
				$items->special_instructions = $val['special_instructions'];
				$items->save();	
				
				$builder=Yii::app()->db->schema->commandBuilder;			
				
				// addon
				$item_addons = array();
				if(is_array($val['addons']) && count($val['addons'])>=1){
					foreach ($val['addons'] as $addons) {						
						$subcat_id = $addons['subcat_id'];
						if(is_array($addons['addon_items']) && count($addons['addon_items'])>=1){
							foreach ($addons['addon_items'] as $addon_items) {								
								$item_addons[] = array(
								  'cart_row'=>$cart_row,
								  'cart_uuid'=>$cart_uuid,
								  'subcat_id'=>intval($subcat_id),
								  'sub_item_id'=>intval($addon_items['sub_item_id']),
								  'qty'=>$addon_items['qty'],
								  'multi_option'=>$addon_items['multiple'],
								);
							}							
							$command=$builder->createMultipleInsertCommand('{{cart_addons}}',$item_addons);
					        $command->execute();
						}
					}
				}
											
				// attributes
				$item_attributes = array();
				$attributes = isset($val['attributes_raw'])?$val['attributes_raw']:'';
				if(is_array($attributes) && count($attributes)>=1){
					foreach ($attributes as $meta_name=>$item) {						
						if(is_array($item) && count($item)>=1){
							foreach ($item as $meta_id=> $item_val) {
								$item_attributes[] = array(
								 'cart_row'=>$cart_row,
								 'cart_uuid'=>$cart_uuid,
								 'meta_name'=>$meta_name,
								 'meta_id'=>intval($meta_id),
								);
							}							
						}						
					}
					$command=$builder->createMultipleInsertCommand('{{cart_attributes}}',$item_attributes);
					$command->execute();
				}
				
			} /*each item*/
			return $cart_uuid;
		}
		throw new Exception( 'order has no item' );
	}
	
}
/*end class*/