<?php
class CMerchantMenu
{
	public static $parameters;
	
	public static function setParameters($parameters=array())
	{
		self::$parameters = $parameters;
	}
	
	public static function getParameters()
	{
		return self::$parameters;
	}
	
	public static function getCategory($merchant_id='',$lang=KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.merchant_id,a.cat_id,
		a.photo, a.path, a.icon, a.icon_path,
		b.category_name,
		b.category_description,
		
		IFNULL((
		 select GROUP_CONCAT(DISTINCT item_id SEPARATOR ',')
		 from {{item_relationship_category}}
		 where merchant_id = a.merchant_id
		 and cat_id = a.cat_id
		 and item_id in (
		    select item_id from {{item}}
		    where status='publish'
		    and available = 1
		 )
		),'') as items
		
		FROM {{category}} a
		LEFT JOIN {{category_translation}} b
		ON
		b.cat_id = a.cat_id
		
		WHERE a.merchant_id = ".q($merchant_id)."
		AND a.status='publish'
		AND b.language = ".q($lang)."
		ORDER BY sequence, category_name ASC
		";						
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			$data = array();
			foreach ($res as $val) {				
				$items = explode(",",$val['items']);				
				$first_item = isset($items[0])?$items[0]:'';				
				if($first_item>0){
					$data[] = array(
					'cat_id'=>$val['cat_id'],
					'category_uiid'=>CommonUtility::toSeoURL($val['category_name']),
					'category_name'=>CHtml::decode($val['category_name']),
					'category_description'=>CHtml::decode($val['category_description']),
					'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image_thumbnail
					,CommonUtility::getPlaceholderPhoto('item')),
					
					'url_icon'=>CMedia::getImage($val['icon'],$val['icon_path'],Yii::app()->params->size_image_thumbnail
					,CommonUtility::getPlaceholderPhoto('icon')),

					'items'=>$items
					);
				}
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getMenu($merchant_id='',$lang='en')
	{
				
		$stmt="
		SELECT a.merchant_id,a.item_id, a.slug, a.item_token,a.photo,a.path,
		b.item_name,a.item_short_description,
		
		(
		select GROUP_CONCAT(f.size_uuid,';',f.price,';',f.size_name,';',f.discount,';',f.discount_type,';',
		 (
		  select count(*) from {{view_item_lang_size}}
		  where item_id = a.item_id 
		  and size_uuid = f.size_uuid
		  and CURDATE() >= discount_start and CURDATE() <= discount_end
		 ),';',f.item_size_id
		)
		
		from {{view_item_lang_size}} f
		where 
		item_id = a.item_id
		and language IN('',".q($lang).")
		) as prices,
		
		(
		select count(*) from {{item_relationship_subcategory}}
		where item_id = a.item_id 
		and item_size_id > 0 and subcat_id > 0
		) as total_addon,
		
		(
		select count(*) from {{item_meta}}
		where item_id = a.item_id 		
		and meta_name not in ('delivery_options','dish','delivery_vehicle')
		) as total_meta
		 
		
		FROM {{item}} a
		LEFT JOIN {{item_translation}} b
		ON
		a.item_id = b.item_id
		
		WHERE 
		a.merchant_id = ".q($merchant_id)."
		AND a.status ='publish'
		AND a.available=1
		AND b.language = ".q($lang)."				
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){						
			$data = array();
			foreach ($res as $val) {				
				$price = array();
				$prices = explode(",",$val['prices']);				
				if(is_array($prices) && count($prices)>=1){
					foreach ($prices as $pricesval) {
						$sizes = explode(";",$pricesval);							
						$item_price = isset($sizes[1])?(float)$sizes[1]:0;
						$item_discount = isset($sizes[3])?(float)$sizes[3]:0;
						$discount_type = isset($sizes[4])?$sizes[4]:'';
						$discount_valid = isset($sizes[5])?(integer)$sizes[5]:0;						
												
						$price_after_discount=0;
						if($item_discount>0 && $discount_valid>0){
							if($discount_type=="percentage"){
								$price_after_discount = $item_price - (($item_discount/100)*$item_price);
							} else $price_after_discount = $item_price-$item_discount;
						
						} else $item_discount = 0;
						
						$price[] = array(
						  'size_uuid'=>isset($sizes[0])?$sizes[0]:'',
						  'item_size_id'=>isset($sizes[6])?$sizes[6]:'',
						  'price'=>$item_price,
						  'size_name'=>isset($sizes[2])?$sizes[2]:'',
						  'discount'=>$item_discount,
						  'discount_type'=>$discount_type,
						  'price_after_discount'=>$price_after_discount,
						  'pretty_price'=>Price_Formatter::formatNumber($item_price),
						  'pretty_price_after_discount'=>Price_Formatter::formatNumber($price_after_discount),
						);
					}
				}
				$data[$val['item_id']] = array(  
				  'item_id'=>$val['item_id'],
				  'item_uuid'=>$val['item_token'],
				  'slug'=>$val['slug'],
				  'item_name'=>CHtml::decode(htmlspecialchars_decode($val['item_name'])),
				  'item_description'=>CommonUtility::formatShortText(htmlspecialchars_decode($val['item_short_description']),130),
				  'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image
				  ,CommonUtility::getPlaceholderPhoto('item')),
				  'price'=>$price,
				  'total_addon'=>(integer)$val['total_addon'],
				  'total_meta'=>(integer)$val['total_meta'],
				  'qty'=>0
				);
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}		
	
	public static function getMenuItem($merchant_id='', $cat_id='', $item_uuid='',$lang=KMRS_DEFAULT_LANGUAGE)
	{
		$data = array();
		
		$stmt="
		SELECT a.item_id, a.photo,a.path, a.item_token,
		b.item_name,b.item_description,
		
		(
		select GROUP_CONCAT(f.size_uuid,';',f.price,';',f.size_name,';',f.discount,';',f.discount_type,';',
		 (
		  select count(*) from {{view_item_lang_size}}
		  where item_id = a.item_id 
		  and size_uuid = f.size_uuid
		  and CURDATE() >= discount_start and CURDATE() <= discount_end
		 ),';',f.item_size_id
		)
		
		from {{view_item_lang_size}} f
		where 
		item_id = a.item_id
		and language IN('',".q($lang).")
		) as prices,
		
		
		IFNULL((
		select GROUP_CONCAT(DISTINCT g.item_size_id,';', 
		
		 (
		  select GROUP_CONCAT(subcat_id)
		  from {{item_relationship_subcategory}}
		  where item_id = a.item_id
		  and item_size_id = g.item_size_id
		)
		 SEPARATOR '|'
		)
		from {{item_relationship_subcategory}} g
		where item_id  = a.item_id
		and item_size_id <> 0
		order by g.id ASC
		),'') as addons
		
		
		FROM {{item}} a
		LEFT JOIN {{item_translation}} b
		ON
		a.item_id = b.item_id
		
		WHERE merchant_id =	".q($merchant_id)."	
		AND item_token=".q($item_uuid)."
		AND b.language = ".q($lang)."
		AND a.available = 1
		
		LIMIT 0,1		
		";			
		$item = Yii::app()->db->createCommand($stmt)->queryRow();
		if($item){
			$price = array();
			$prices = !empty($item['prices'])? explode(",",$item['prices']) : '';			
			if(is_array($prices) && count($prices)>=1){
				foreach ($prices as $price_key=>$pricesval) {
					$sizes = explode(";",$pricesval);					
					$item_price = isset($sizes[1])?(float)$sizes[1]:0;
					$item_discount = isset($sizes[3])?(float)$sizes[3]:0;
					$discount_type = isset($sizes[4])?$sizes[4]:'';
					$discount_valid = isset($sizes[5])?(integer)$sizes[5]:0;						
					$item_size_id = isset($sizes[6])?(integer)$sizes[6]:0;
					$size_uuid = isset($sizes[0])?$sizes[0]:'';
											
					$price_after_discount=0;
					if($item_discount>0 && $discount_valid>0){
						if($discount_type=="percentage"){
							$price_after_discount = $item_price - (($item_discount/100)*$item_price);
						} else $price_after_discount = $item_price-$item_discount;
					
					} else $item_discount = 0;
					
					$price[$item_size_id] = array(
					  'key'=>$price_key,
					  'size_uuid'=>$size_uuid,
					  'item_size_id'=>$item_size_id,
					  'price'=>$item_price,
					  'size_name'=>isset($sizes[2])?$sizes[2]:'',
					  'discount'=>$item_discount,
					  'discount_type'=>$discount_type,
					  'price_after_discount'=>$price_after_discount,
					  'pretty_price'=>Price_Formatter::formatNumber($item_price),
					  'pretty_price_after_discount'=>Price_Formatter::formatNumber($price_after_discount),
					);
				}
			}
			
			$addons = array();			
			$addon = !empty($item['addons'])? explode("|",$item['addons']) : '';
			if(is_array($addon) && count($addon)>=1){
				foreach ($addon as $addon_val) {
					$itemsizeid = explode(";",$addon_val);					
					$item_size_id = isset($itemsizeid[0])?(integer)$itemsizeid[0]:0;
					$subcategory = isset($itemsizeid[1])?$itemsizeid[1]:'';
					$subcategory1 = explode(",",$subcategory);					
					$addons[$item_size_id] = $subcategory1;
				}
			}
			
			return array(
			  'merchant_id'=>$merchant_id,
			  'item_id'=>$item['item_id'],
			  'item_token'=>$item['item_token'],
			  'cat_id'=>$cat_id,
			  'item_name'=>Yii::app()->input->xssClean(htmlspecialchars_decode($item['item_name'])),
			  'item_description'=>Yii::app()->input->xssClean(htmlspecialchars_decode($item['item_description'])), 
			  'url_image'=>CMedia::getImage($item['photo'],$item['path'],"@2x",
				CommonUtility::getPlaceholderPhoto('item')),
			  'price'=>$price,
			  'item_addons'=>$addons
			);			
		}
		throw new Exception( 'no results' );
	}
	
	public static function getItemAddonCategory($merchant_id='', $item_uuid='',$lang = KMRS_DEFAULT_LANGUAGE)
	{
		
		$data = array();

		$stmt="
		SELECT a.subcat_id,
		b.subcategory_name,b.subcategory_description,
		c.multi_option,c.multi_option_value,c.require_addon,c.pre_selected,
		c.item_size_id,c.id as size_primary_id,
		
		(
		select GROUP_CONCAT(sub_item_id)
		from {{subcategory_item_relationships}}
		where subcat_id = a.subcat_id		
		) as sub_items
		
		
		FROM {{subcategory}} a
		LEFT JOIN {{subcategory_translation}} b
		ON
		a.subcat_id = b.subcat_id
		
		LEFT JOIN {{view_item_relationship_subcategory}} c
		ON
		a.subcat_id = c.subcat_id
		
		WHERE a.merchant_id = ".q($merchant_id)."	
		AND a.status = 'publish'			
		AND b.language = ".q($lang)."		
		AND c.item_token =".q($item_uuid)."
		ORDER BY c.id ASC		
		";		
		if(Yii::app()->params->db_cache_enabled){
		  $dependency = new CDbCacheDependency('SELECT count(*),MAX(date_modified) FROM {{item}}');
		  $res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll();		  
		} else $res = Yii::app()->db->createCommand($stmt)->queryAll();		
					
		if($res){		   
		   foreach ($res as $val) {		
		   	    $sub_items = !empty($val['sub_items'])? explode(",",$val['sub_items']) : '';		   	    	   		 
		   		$data[$val['item_size_id']][$val['subcat_id']] = array(		   		
		   		  'subcat_id'=>$val['subcat_id'],
		   		  'subcategory_name'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['subcategory_name'])),
		   		  'subcategory_description'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['subcategory_description'])),
		   		  'multi_option'=>$val['multi_option'],
		   		  'multi_option_value'=>$val['multi_option_value'],
		   		  'require_addon'=>$val['require_addon'],
		   		  'pre_selected'=>$val['pre_selected'],
		   		  'sub_items'=>$sub_items
		   		);
		   	}			   	
		   	return $data;
		}		 
		return false;
	}
	
	public static function getAddonItems($merchant_id='', $item_uuid='',$lang = KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.sub_item_id,
		b.sub_item_name, b.item_description,
		a.price, a.photo,a.path
		
		FROM {{subcategory_item}} a		
		LEFT JOIN {{subcategory_item_translation}} b
		ON
		a.sub_item_id = b.sub_item_id
		WHERE a.merchant_id = ".q($merchant_id)."	
		AND a.status = 'publish'		
		AND b.language = ".q($lang)."
		AND a.sub_item_id IN (		  
		  select sub_item_id from {{view_item_relationship_subcategory_item}}
		  where merchant_id = ".q($merchant_id)."
		  and item_token=".q($item_uuid)."
		)
		ORDER BY sequence,id ASC
		";		
		if(Yii::app()->params->db_cache_enabled){
		  $dependency = new CDbCacheDependency('SELECT count(*),MAX(date_modified) FROM {{item}}');
		  $res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll();		  
		} else $res = Yii::app()->db->createCommand($stmt)->queryAll();		
				
		if($res){
			$data = array();
			foreach ($res as $val) {	
				$sub_item_id = (integer) $val['sub_item_id'];
				$data[$sub_item_id] = array(
				  'sub_item_id'=>$sub_item_id,
				  'sub_item_name'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['sub_item_name'])),
				  'item_description'=>Yii::app()->input->xssClean(htmlspecialchars_decode($val['item_description'])),
				  'price'=>(float)$val['price'],
				  'pretty_price'=>Price_Formatter::formatNumber($val['price']),				  
				  'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image,
				   CommonUtility::getPlaceholderPhoto('item')),
				   'hasimage'=>!empty($val['photo'])?true:false
				);
			}
			return $data;
		}
		return false;
	}
	
	public static function getItemMeta($merchant_id='', $item_uuid='')
	{
		$stmt="
		SELECT a.id, a.merchant_id, a.item_id, 
		a.meta_name, a.meta_id		
		FROM {{item_meta}} a
		WHERE 
		meta_name IN ('ingredients','cooking_ref','dish')
		AND a.item_id IN (
		  select item_id from {{item}}
		  where item_token = ".q($item_uuid)."
		  AND merchant_id = ".q( (integer) $merchant_id )."
		)
		ORDER BY a.id ASC
		";
					
		if(Yii::app()->params->db_cache_enabled){
		  $dependency = new CDbCacheDependency('SELECT count(*),MAX(date_modified) FROM {{item}}');
		  $res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll();		  
		} else $res = Yii::app()->db->createCommand($stmt)->queryAll();		
		
		if($res){
			$data = array();
			foreach ($res as $val){				
				$data[$val['meta_name']][] = $val['meta_id'];
			}	
			return $data;	
		}		
		return false;
	}
	
	public static function getMeta($merchant_id='', $item_uuid='',$lang= KMRS_DEFAULT_LANGUAGE )
	{
		$stmt="
		SELECT 'cooking_ref' as meta_type,a.merchant_id,a.cook_id as meta_id,
		b.cooking_name  as meta_name
		FROM {{cooking_ref}} a		
		LEFT JOIN {{cooking_ref_translation}} b
		ON
		a.cook_id = b.cook_id
		
		WHERE a.status = 'publish' 
		AND b.language = ".q($lang)."
		AND a.merchant_id = ".q($merchant_id)."
		AND a.cook_id IN (
		  select meta_id from {{item_meta}}
		  where meta_name='cooking_ref'
		  and item_id IN (
		    select item_id from {{item}}
		    where item_token = ".q($item_uuid)."		  
		  )
		)
		
		UNION ALL
		
		SELECT 'ingredients' as meta_type,a.merchant_id,a.ingredients_id as meta_id,
		b.ingredients_name  as meta_name
		FROM {{ingredients}} a		
		LEFT JOIN {{ingredients_translation}} b
		ON
		a.ingredients_id = b.ingredients_id
		
		WHERE a.status = 'publish' 
		AND b.language = ".q($lang)."
		AND a.merchant_id = ".q($merchant_id)."
		AND a.ingredients_id IN (
		  select meta_id from {{item_meta}}
		  where meta_name='ingredients'
		  and item_id IN (
		    select item_id from {{item}}
		    where item_token = ".q($item_uuid)."		  
		  )
		)
		
		
		UNION ALL
		
		SELECT 'dish' as meta_type,'',a.dish_id as meta_id,
		b.dish_name  as meta_name
		FROM {{dishes}} a		
		LEFT JOIN {{dishes_translation}} b
		ON
		a.dish_id = b.dish_id
		
		WHERE a.status = 'publish' 
		AND b.language = ".q($lang)."		
		AND a.dish_id IN (
		  select meta_id from {{item_meta}}
		  where meta_name='dish'
		  and item_id IN (
		    select item_id from {{item}}
		    where item_token = ".q($item_uuid)."		  
		    AND merchant_id = ".q( (integer) $merchant_id )."
		  )
		)
		";
		
		if(Yii::app()->params->db_cache_enabled){
		  $dependency = new CDbCacheDependency('SELECT count(*),MAX(date_modified) FROM {{item}}');
		  $res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll();		  
		} else $res = Yii::app()->db->createCommand($stmt)->queryAll();		
		
		if($res){
			$data = array();
			foreach ($res as $val){								
				$data[$val['meta_type']][$val['meta_id']] = array(
				  'meta_id'=>$val['meta_id'],
				  'meta_name'=>$val['meta_name'],
				);
			}				
			return $data;	
		}		
		return false;		
	}
	
	
	public static function CategoryItem($merchant_id=0,$cat_id=0,$search='',$page=0,$lang=KMRS_DEFAULT_LANGUAGE)
	{
	
	    $criteria=new CDbCriteria();	
	    $criteria->alias = "a";
	    $criteria->select="a.merchant_id,a.item_id, a.item_token,a.photo,a.path,
	    b.item_name,a.item_short_description,

	    (
		select GROUP_CONCAT(f.size_uuid,';',f.price,';',f.size_name,';',f.discount,';',f.discount_type,';',
		 (
		  select count(*) from {{view_item_lang_size}}
		  where item_id = a.item_id 
		  and size_uuid = f.size_uuid
		  and CURDATE() >= discount_start and CURDATE() <= discount_end
		 ),';',f.item_size_id
		)
		
		from {{view_item_lang_size}} f
		where 
		item_id = a.item_id
		and language IN('',".q($lang).")
		) as prices,
		
		(
		select GROUP_CONCAT(cat_id)
		from {{item_relationship_category}}
		where item_id = a.item_id
		) as group_category
	    	    
	    ";		
	    $criteria->condition = "merchant_id = :merchant_id 
	    AND status=:status AND available=:available AND b.language=:language";
	    
	    if($cat_id>0){
	    	$criteria->condition = " 
	    	merchant_id = :merchant_id AND status=:status AND available=:available
	    	AND b.language=:language
	    	AND
	    	a.item_id IN (
	    	   select item_id from {{item_relationship_category}}
		       where cat_id = ".q($cat_id)."
	    	)
	    	";
	    }
	    
	    $criteria->params = array (
	       ':merchant_id'=>intval($merchant_id),
	       ':status'=>'publish',
	       ':available'=>1,
	       ':language'=>$lang
	    );		    
	    
	    $criteria->mergeWith(array(
			'join'=>'LEFT JOIN {{item_translation}} b ON a.item_id = b.item_id',				
		));
		
		if (is_string($search) && strlen($search) > 0){
		   $criteria->addSearchCondition('b.item_name', $search );
		}
		    		
	    $count = AR_item::model()->count($criteria);        	
	    		    
	    $pages=new CPagination($count);
        $pages->setCurrentPage($page);        
        $pages->pageSize = intval(Yii::app()->params->list_limit);        
        $pages->applyLimit($criteria);        
        $models=AR_item::model()->findAll($criteria);
        
        $page_count = $pages->getPageCount();	        
        $current_page = $pages->getCurrentPage();
                    
        if($models){
        	$data = array();
        	foreach ($models as $val) {            		
        		
        		$price = array();
        		$prices = explode(",",$val->prices);
        		
        		$group_category = explode(",",$val->group_category);
        		
        		if(is_array($prices) && count($prices)>=1){
					foreach ($prices as $pricesval) {
						$sizes = explode(";",$pricesval);							
						$item_price = isset($sizes[1])?(float)$sizes[1]:0;
						$item_discount = isset($sizes[3])?(float)$sizes[3]:0;
						$discount_type = isset($sizes[4])?$sizes[4]:'';
						$discount_valid = isset($sizes[5])?(integer)$sizes[5]:0;						
												
						$price_after_discount=0;
						if($item_discount>0 && $discount_valid>0){
							if($discount_type=="percentage"){
								$price_after_discount = $item_price - (($item_discount/100)*$item_price);
							} else $price_after_discount = $item_price-$item_discount;
						
						} else $item_discount = 0;
						
						$price[] = array(
						  'size_uuid'=>isset($sizes[0])?$sizes[0]:'',
						  'item_size_id'=>isset($sizes[6])?$sizes[6]:'',
						  'price'=>$item_price,
						  'size_name'=>isset($sizes[2])?$sizes[2]:'',
						  'discount'=>$item_discount,
						  'discount_type'=>$discount_type,
						  'price_after_discount'=>$price_after_discount,
						  'pretty_price'=>Price_Formatter::formatNumber($item_price),
						  'pretty_price_after_discount'=>Price_Formatter::formatNumber($price_after_discount),
						);
					}
				}          
				
				$data[$val['item_id']] = array(  
				  'item_id'=>$val['item_id'],
				  'item_uuid'=>$val['item_token'],
				  'item_name'=>stripslashes(htmlspecialchars_decode($val['item_name'])),
				  'item_description'=>CommonUtility::formatShortText(htmlspecialchars_decode($val['item_short_description']),130),
				  'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image
				  ,CommonUtility::getPlaceholderPhoto('item')),
				  'category_id'=>$cat_id>0?array($cat_id):$group_category,
				  'price'=>$price,					  
				);
				  		
        	}/* end foreach*/
        	
        	return array(
        	  'total_records'=>t("{{total}} results",array('{{total}}'=>$count)),
        	  'page_count'=>$page_count,
        	  'current_page'=>$current_page+1,
        	  'data'=>$data
        	);
        	
        } /*if model*/
        throw new Exception( 'no results' );
	}

	public static function getItemFeatured($merchant_id=0,$meta_name='',$lang=KMRS_DEFAULT_LANGUAGE)
	{
		$criteria=new CDbCriteria();	
	    $criteria->alias = "a";
	    $criteria->select="a.merchant_id,a.item_id, a.item_token, a.slug , a.photo,a.path,
	    b.item_name,a.item_short_description,

	    (
		select GROUP_CONCAT(f.size_uuid,';',f.price,';',f.size_name,';',f.discount,';',f.discount_type,';',
		 (
		  select count(*) from {{view_item_lang_size}}
		  where item_id = a.item_id 
		  and size_uuid = f.size_uuid
		  and CURDATE() >= discount_start and CURDATE() <= discount_end
		 ),';',f.item_size_id
		)
		
		from {{view_item_lang_size}} f
		where 
		item_id = a.item_id
		and language IN('',".q($lang).")
		) as prices,
		
		(
		select GROUP_CONCAT(cat_id)
		from {{item_relationship_category}}
		where item_id = a.item_id
		) as group_category
	    	    
	    ";		
	    $criteria->condition = "merchant_id = :merchant_id 
	    AND status=:status AND available=:available AND b.language=:language";
	    
	    
		$criteria->condition = " 
		merchant_id = :merchant_id AND status=:status AND available=:available
		AND b.language=:language
		AND
		a.item_id IN (
			select item_id from {{item_meta}}
			where 
			meta_name='item_featured'
			and 
			meta_id = ".q($meta_name)."
		)
		";
		    
	    $criteria->params = array (
	       ':merchant_id'=>intval($merchant_id),
	       ':status'=>'publish',
	       ':available'=>1,
	       ':language'=>$lang
	    );		    
	    
	    $criteria->mergeWith(array(
			'join'=>'LEFT JOIN {{item_translation}} b ON a.item_id = b.item_id',				
		));

		$dependency = CCacheData::dependency();	 	  
		if($models = AR_item::model()->cache(Yii::app()->params->cache, $dependency)->findAll($criteria)){		
			$data = array();
        	foreach ($models as $val) {            		
        		
        		$price = array();
        		$prices = explode(",",$val->prices);
        		
        		$group_category = explode(",",$val->group_category);
        		
        		if(is_array($prices) && count($prices)>=1){
					foreach ($prices as $pricesval) {
						$sizes = explode(";",$pricesval);							
						$item_price = isset($sizes[1])?(float)$sizes[1]:0;
						$item_discount = isset($sizes[3])?(float)$sizes[3]:0;
						$discount_type = isset($sizes[4])?$sizes[4]:'';
						$discount_valid = isset($sizes[5])?(integer)$sizes[5]:0;						
												
						$price_after_discount=0;
						if($item_discount>0 && $discount_valid>0){
							if($discount_type=="percentage"){
								$price_after_discount = $item_price - (($item_discount/100)*$item_price);
							} else $price_after_discount = $item_price-$item_discount;
						
						} else $item_discount = 0;
						
						$price[] = array(
						  'size_uuid'=>isset($sizes[0])?$sizes[0]:'',
						  'item_size_id'=>isset($sizes[6])?$sizes[6]:'',
						  'price'=>$item_price,
						  'size_name'=>isset($sizes[2])?$sizes[2]:'',
						  'discount'=>$item_discount,
						  'discount_type'=>$discount_type,
						  'price_after_discount'=>$price_after_discount,
						  'pretty_price'=>Price_Formatter::formatNumber($item_price),
						  'pretty_price_after_discount'=>Price_Formatter::formatNumber($price_after_discount),
						);
					}
				}          
				
				$data[$val['item_id']] = array(  
				  'item_id'=>$val['item_id'],
				  'item_uuid'=>$val['item_token'],
				  'slug'=>stripslashes($val['slug']),
				  'item_name'=>stripslashes(htmlspecialchars_decode($val['item_name'])),
				  'item_description'=>CommonUtility::formatShortText(htmlspecialchars_decode($val['item_short_description']),130),
				  'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image
				  ,CommonUtility::getPlaceholderPhoto('item')),		
				  'category_id'=>$group_category,		  
				  'price'=>$price,					  
				);
				  		
        	}/* end foreach*/

			return $data;
		}
		throw new Exception( 'no results' );
	}

	public static function getSimilarItems($merchant_id='',$lang='en', $limit=20, $q='')
	{
				
		$stmt="
		SELECT a.merchant_id,a.item_id, a.slug, a.item_token,a.photo,a.path,
		b.item_name,a.item_short_description,
		
		(
		select GROUP_CONCAT(f.size_uuid,';',f.price,';',f.size_name,';',f.discount,';',f.discount_type,';',
		 (
		  select count(*) from {{view_item_lang_size}}
		  where item_id = a.item_id 
		  and size_uuid = f.size_uuid
		  and CURDATE() >= discount_start and CURDATE() <= discount_end
		 ),';',f.item_size_id
		)
		
		from {{view_item_lang_size}} f
		where 
		item_id = a.item_id
		and language IN('',".q($lang).")
		) as prices,

		(
		  select cat_id from {{item_relationship_category}}
		  where item_id = a.item_id 
		  limit 0,1
		) as category
		
		
		FROM {{item}} a
		LEFT JOIN {{item_translation}} b
		ON
		a.item_id = b.item_id
		
		WHERE 
		a.merchant_id = ".q($merchant_id)."
		AND a.status ='publish'
		AND a.available=1
		AND b.language = ".q($lang)."					
		";				
		if(!empty($q)){
			$stmt.="
			AND b.item_name LIKE ".q("%$q%")."
			";
		}					
		$stmt.="
		ORDER BY rand()		
		LIMIT 0,$limit
		";		
		
		$dependency = CCacheData::dependency();
		if($res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll()){										
			$data = array();
			foreach ($res as $val) {				
				$price = array();
				$prices = explode(",",$val['prices']);				
				if(is_array($prices) && count($prices)>=1){
					foreach ($prices as $pricesval) {
						$sizes = explode(";",$pricesval);							
						$item_price = isset($sizes[1])?(float)$sizes[1]:0;
						$item_discount = isset($sizes[3])?(float)$sizes[3]:0;
						$discount_type = isset($sizes[4])?$sizes[4]:'';
						$discount_valid = isset($sizes[5])?(integer)$sizes[5]:0;						
												
						$price_after_discount=0;
						if($item_discount>0 && $discount_valid>0){
							if($discount_type=="percentage"){
								$price_after_discount = $item_price - (($item_discount/100)*$item_price);
							} else $price_after_discount = $item_price-$item_discount;
						
						} else $item_discount = 0;
						
						$price[] = array(
						  'size_uuid'=>isset($sizes[0])?$sizes[0]:'',
						  'item_size_id'=>isset($sizes[6])?$sizes[6]:'',
						  'price'=>$item_price,
						  'size_name'=>isset($sizes[2])?$sizes[2]:'',
						  'discount'=>$item_discount,
						  'discount_type'=>$discount_type,
						  'price_after_discount'=>$price_after_discount,
						  'pretty_price'=>Price_Formatter::formatNumber($item_price),
						  'pretty_price_after_discount'=>Price_Formatter::formatNumber($price_after_discount),
						);
					}
				}
				$data[$val['item_id']] = array(  
				   'cat_id'=>intval($val['category']),
				  'item_id'=>$val['item_id'],
				  'item_uuid'=>$val['item_token'],
				  'slug'=>$val['slug'],
				  'item_name'=>CHtml::decode(htmlspecialchars_decode($val['item_name'])),
				  'item_description'=>CommonUtility::formatShortText(htmlspecialchars_decode($val['item_short_description']),130),
				  'url_image'=>CMedia::getImage($val['photo'],$val['path'],Yii::app()->params->size_image
				  ,CommonUtility::getPlaceholderPhoto('item')),
				  'price'=>$price,				  
				  'qty'=>0
				);
			}
			return $data;
		}
		throw new Exception( 'no results' );
	}		
	
	public static function getCategoryList($merchant_id=0)
	{
		$stmt="
		SELECT a.merchant_id,a.cat_id,
		IFNULL((
		 select GROUP_CONCAT(DISTINCT item_id SEPARATOR ',')
		 from {{item_relationship_category}}
		 where merchant_id = a.merchant_id
		 and cat_id = a.cat_id
		 and item_id in (
		    select item_id from {{item}}
		    where status='publish'
		    and available = 1
		 )
		),'') as items
		
		FROM {{category}} a				
		WHERE a.merchant_id = ".q($merchant_id)."
		AND a.status='publish'				
		";					
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$data = array();
			foreach ($res as $val) {
				$items = explode(",",$val['items']);
				$data[]=[
					'cat_id'=>$val['cat_id'],
					'items'=>$items
				];
			}			
			return $data;
		}
		throw new Exception( 'no results' );		
	}
		
}
/*end class*/