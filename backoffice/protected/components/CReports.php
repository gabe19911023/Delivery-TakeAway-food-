<?php
class CReports {
	
	public static function ItemSales($merchant_id=0, $period=30)
	{
	    $data = array(); $category = array(); $sales = array();
		
		$date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		$date_end = date("Y-m-d");
		
		$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
		
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select="sum(a.qty) as total_sold, DATE_FORMAT(b.date_created, '%c/%d') as date_sold";
		$criteria->join='LEFT JOIN {{ordernew}} b on  a.order_id=b.order_id ';
		
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id";
			$criteria->params = array(':merchant_id'=>intval($merchant_id));
		}
		
		$criteria->addBetweenCondition("DATE_FORMAT(b.date_created,'%Y-%m-%d')", $date_start , $date_end );
		if(is_array($status_completed) && count($status_completed)>=1){			
		   $criteria->addInCondition('b.status', (array) $status_completed );
	    }	
		
		$criteria->group="DATE_FORMAT(b.date_created, '%c/%d')";
		$criteria->order = "b.date_created ASC";	
				
		if($model = AR_ordernew_item::model()->findAll($criteria)){			
			foreach ($model as $item) {
				$category[$item->date_sold]=$item->date_sold;
				$sales[$item->date_sold] = floatval($item->total_sold);
			}		
			
			$new_category = array(); $new_sales=array();
			for ($i = 0; $i < $period; $i++){
				 $timestamp = time();
				 $tm = 86400 * $i;
				 $tm = $timestamp - $tm;
				 $the_date = date("n/d", $tm);
				 
				 if(array_key_exists($the_date,(array)$category)){
				 	$new_category[]=$category[$the_date];
				 } else $new_category[] = $the_date; 
				 
				 if(array_key_exists($the_date,(array)$sales)){
				 	$new_sales[]=$sales[$the_date];
				 } else $new_sales[] = 0; 
			}
			
			$new_category = array_reverse($new_category);
			$new_sales = array_reverse($new_sales);
			
			$data = array(
			  'category'=>$new_category,
			  'data'=>$new_sales
			);
			return $data;
		} 
		throw new Exception( "You don't have sales yet" );
	}
	
	public static function ItemSalesSummary($merchant_id=0, $period=0)
	{
		$data = array(); $category = array(); $sales = array(); $colors = array();
		$period = intval($period);		
		
		$date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		$date_end = date("Y-m-d");
		
		$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
		
		$item_name_list = CommonUtility::getDataToDropDown("{{item_translation}}",'item_id','item_name',
    	"
    	where language=".q(Yii::app()->language)." 
    	and item_id IN (
    	 select item_id from {{item}}
    	 where merchant_id=".q($merchant_id)."
    	 and item_name IS NOT NULL AND TRIM(item_name) <> ''
    	)
    	"
    	);    	
		
		$criteria=new CDbCriteria();
		$criteria->alias="a";
		$criteria->select="a.item_id,sum(a.qty) as total_sold,
		c.item_name, c.color_hex
		";
		$criteria->join='LEFT JOIN {{ordernew}} b on  a.order_id = b.order_id 
		LEFT JOIN {{item}} c on  a.item_id = c.item_id
		';
		
		$criteria->condition = "b.merchant_id=:merchant_id 
		 AND c.item_name IS NOT NULL AND TRIM(c.item_name) <> ''		 
		";
		$criteria->params = array(':merchant_id'=>$merchant_id);
		
		if($period>0){
		  $criteria->addBetweenCondition("DATE_FORMAT(b.date_created,'%Y-%m-%d')", $date_start , $date_end );
		}
		if(is_array($status_completed) && count($status_completed)>=1){			
		   $criteria->addInCondition('b.status', (array) $status_completed );
	    }	
		
		$criteria->group="a.item_id";		
		$criteria->order="sum(a.qty) DESC";
		
		//dump($criteria);
		if($model = AR_ordernew_item::model()->findAll($criteria)){
			foreach ($model as $item) {				
				
				$item_name = $item->item_name;
		        if(array_key_exists($item->item_id,(array)$item_name_list)){
		        	$item_name = $item_name_list[$item->item_id];		        	
		        }
				
				$category[] = $item_name;
				$sales[] = intval($item->total_sold);	
				$colors[] = !empty($item->color_hex) ? $item->color_hex : CommonUtility::generateRandomColor() ;
			}
						
			$data = array(
			  'category'=>$category,
			  'data'=>$sales,
			  'colors'=>$colors
			);
			return $data;			
		}
		throw new Exception( "You don't have sales yet" );
	}
	
	public static function popularItems($merchant_id=0, $period=30)
	{
		    $data = array();
		    $period = intval($period);
		    
		    $date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		    $date_end = date("Y-m-d");
					
			$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));						
			
			$criteria=new CDbCriteria();
			$criteria->alias = "a";
			$criteria->select="a.item_id, a.cat_id, sum(qty) as total_sold,
			b.photo , b.path,
			(
			  select item_name from {{item_translation}}
			  where item_id = a.item_id and language=".q(Yii::app()->language)."
			) as item_name,
			(
			  select category_name from {{category_translation}}
			  where cat_id=a.cat_id and language=".q(Yii::app()->language)."
			) as category_name
			";
			$criteria->join='LEFT JOIN {{item}} b on  a.item_id = b.item_id 
			LEFT JOIN {{ordernew}} c on a.order_id = c.order_id 
			';						
			
			if($merchant_id>0){
				$criteria->condition = "c.merchant_id=:merchant_id AND item_name is not null";
				$criteria->params = array( 
				   ':merchant_id'=>intval($merchant_id)			   
				);
			} else {
				$criteria->condition = "item_name is not null";				
			}
			
			if(is_array($status_completed) && count($status_completed)>=1){			
			   $criteria->addInCondition('c.status', (array) $status_completed );
		    }
		    
		    if($period>0){
		       $criteria->addBetweenCondition("DATE_FORMAT(c.date_created,'%Y-%m-%d')", $date_start , $date_end );		
		    }
			
			$criteria->group="a.item_id";
			$criteria->order = "sum(qty) DESC";	
														
		    if($model = AR_ordernew_item::model()->findAll($criteria)){
		    	foreach ($model as $item) {
		    		$data[]=array(
		    		  'item_id'=>$item->item_id,
		    		  'item_name'=>$item->item_name,
		    		  'total_sold'=>$item->total_sold,
		    		);
		    	}
		    	return $data;
		    }
		    throw new Exception( "You don't have sales yet" );
	}
	
	public static function SalesThisWeek($merchant_id, $period=7 , $status_completed=array() )
	{
		$data = array();		   
	    $date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
	    $date_end = date("Y-m-d");					    	    
		$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));
		
		$criteria=new CDbCriteria();
		$criteria->select="sum(total) as total_sold";
		
		$criteria->condition = "merchant_id=:merchant_id";
		$criteria->params = array( 
		   ':merchant_id'=>$merchant_id			   
		);
		if(is_array($status_completed) && count($status_completed)>=1){			
		   $criteria->addInCondition('status', (array) $status_completed );
	    }
	    
	    $criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );		
	    
	    if($model = AR_ordernew::model()->find($criteria)){
	    	return floatval($model->total_sold);
	    }
	    return 0;
	}
	
	public static function EarningThisWeek($card_id, $period=7 , $transaction_type='credit' , $status='paid')
	{
		$data = array();		   
	    $date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
	    $date_end = date("Y-m-d");						
		
		$criteria=new CDbCriteria();
		$criteria->select="sum(transaction_amount) as total_earning";
		
		$criteria->condition = "card_id=:card_id AND transaction_type=:transaction_type AND status=:status ";
		$criteria->params = array( 
		   ':card_id'=>$card_id,
		   ':transaction_type'=>$transaction_type,
		   ':status'=>$status
		);
		
		$criteria->addBetweenCondition("DATE_FORMAT(transaction_date,'%Y-%m-%d')", $date_start , $date_end );		
			    				
	    if($model = AR_wallet_transactions::model()->find($criteria)){	    		    	
	    	return floatval($model->total_earning);
	    }
	    return 0;
	}
	
	public static function WalletEarnings($card_id, $period=7 , $transaction_type='credit' , $status='paid' )
	{
		$data = array();		   
	    
	    if($period==30){
			$date_start = date("Y-m-01", strtotime(date("c")) );
		    $date_end = date("Y-m-t");							   
		} else {
		    $date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		    $date_end = date("Y-m-d");							   
		}				
		
		$criteria=new CDbCriteria();		
		
		$criteria->condition = "card_id=:card_id AND transaction_type=:transaction_type AND status=:status ";
		$criteria->params = array( 
		   ':card_id'=>$card_id,		 
		   ':transaction_type'=>$transaction_type,
		   ':status'=>$status  
		);
				
		$criteria->addBetweenCondition("DATE_FORMAT(transaction_date,'%Y-%m-%d')", $date_start , $date_end );		
		$criteria->order = "transaction_id DESC";
			    							
	    if($model = AR_wallet_transactions::model()->find($criteria)){	  	    	
	    	return floatval($model->running_balance);
	    }
	    return 0;
	}
	
	public static function totalSales($status=array())
	{
		$draft = AttributesTools::initialStatus();
		$criteria=new CDbCriteria();
		$criteria->select = "sum(total) as total_sold";
		$criteria->addInCondition('status', (array) $status );				
		if($model = AR_ordernew::model()->find($criteria)){
			return $model->total_sold;
		}
		return 0;
	}
	
	public static function totalMerchant($status=array())
	{	
		$criteria=new CDbCriteria();
		$criteria->select = "count(*) as total";
		$criteria->addInCondition('status', (array)$status);				
		if($model = AR_merchant::model()->find($criteria)){
			return $model->total;
		}
		return 0;
	}
	
	public static function totalCustomer($status=array())
	{	
		$criteria=new CDbCriteria();
		$criteria->select = "count(*) as total";
		$criteria->addInCondition('status', (array)$status);		
		if($model = AR_client::model()->find($criteria)){			
			return $model->total;
		}
		return 0;
	}
	
	public static function PlansEarning($period=7, $status = array('paid') )
	{		
		$data = array();		   
		if($period==30){
			$date_start = date("Y-m-01", strtotime(date("c")) );
		    $date_end = date("Y-m-t");							   
		} else {
		    $date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		    $date_end = date("Y-m-d");							   
		}
		
		$criteria=new CDbCriteria();
		$criteria->select="sum(amount) as total";
		if(is_array($status) && count($status)>=1){			
		   $criteria->addInCondition('status', (array) $status );
	    }
		$criteria->addBetweenCondition("DATE_FORMAT(created,'%Y-%m-%d')", $date_start , $date_end );						
		if($model = AR_plans_invoice::model()->find($criteria)){	  	    				
	    	return floatval($model->total);
	    }
	    return 0;
	}
	
	public static function totalSubscriptions($status = array('paid') )
	{
		$criteria=new CDbCriteria();
		$criteria->select="sum(amount) as total";
		if(is_array($status) && count($status)>=1){			
		   $criteria->addInCondition('status', (array) $status );
	    }		
		if($model = AR_plans_invoice::model()->find($criteria)){	  	    				
	    	return floatval($model->total);
	    }
	    return 0;
	}
	
	public static function PopularMerchant($limit = 5 )
	{
		$data = array();
		$status_completed = AOrderSettings::getStatus(array('status_delivered','status_completed'));						
		$thousand_sep = Price_Formatter::$number_format['thousand_separator'];
		$cuisine_list = array();
		try {
		   $cuisine_list = AttributesTools::cuisineGroup(Yii::app()->language);
		} catch (Exception $e) {
			
		}
		
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select="a.merchant_id, count(*) as total_sold,
		b.restaurant_name, b.logo, b.path,
		
		(
        select concat(review_count,';',ratings) as ratings from {{view_ratings}}
        where merchant_id = a.merchant_id
        ) as ratings
        
		
		";
		
		$criteria->join='
		LEFT JOIN {{merchant}} b on  a.merchant_id = b.merchant_id 
		';
		
		if(is_array($status_completed) && count($status_completed)>=1){			
		   $criteria->addInCondition('a.status', (array) $status_completed );
	    }		
	    $criteria->group="a.merchant_id";
	    $criteria->order = "count(*) DESC";			
	    $criteria->limit = intval($limit);
	    
		$model = AR_ordernew::model()->findAll($criteria); 		
		if($model){
			foreach ($model as $item) {
				$image_url = CMedia::getImage($item->logo,$item->path,'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('merchant'));
		         
		        $ratings = array();
				if($rate = explode(";",$item->ratings)){
				   $ratings = array(
				     'review_count'=>isset($rate[0])?intval($rate[0]):0,
				     'rating'=>isset($rate[1])?intval($rate[1]):0,
				   );
				}		
				 
				$data[] = array(
				  'merchant_id'=>$item->merchant_id,
				  'image_url'=>$image_url,
				  'restaurant_name'=>Yii::app()->input->xssClean($item->restaurant_name),
				  'total_sold'=>intval($item->total_sold),
				  'total_sold_pretty'=> t("{{sold}} sold",array('{{sold}}'=>Price_Formatter::convertToRaw($item->total_sold,0,false,$thousand_sep))) ,
				  'ratings'=>$ratings,			
				  'view_merchant'=>Yii::app()->createAbsoluteUrl('/vendor/edit',array('id'=>$item->merchant_id))
				);
			}
			return array(
			  'data'=>$data,
			  'cuisine_list'=>$cuisine_list,
			);
		}
		throw new Exception( "No merchant has sold anything yet" );
	}
	
	public static function PopularMerchantByReview()
	{
		$data = array();
		$stmt = "
		SELECT a.*,
		b.restaurant_name , b.logo , b.path
		
		FROM {{view_ratings}} a
		LEFT JOIN {{merchant}} b
		ON
		a.merchant_id = b.merchant_id
		ORDER BY ratings DESC
		";
		
		$dependency = CCacheData::dependency();		
		if($res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll()){
			foreach ($res as $item) {				
				
				$image_url = CMedia::getImage($item['logo'],$item['path'],'@thumbnail',
		         CommonUtility::getPlaceholderPhoto('merchant'));
		         
				$data[] = array(
				  'merchant_id'=>$item['merchant_id'],
				  'review_count'=>intval($item['review_count']),
				  'ratings'=>intval($item['ratings']),
				  'restaurant_name'=>Yii::app()->input->xssClean($item['restaurant_name']),
				  'image_url'=>$image_url,
				);
			}
			return $data;
		}		
		throw new Exception( "No review yet" );
	}
	
	public static function OrderTotalByStatus($merchant_id=0, $status=array(),  $period=0)
	{		
		$date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		$date_end = date("Y-m-d");
		
		$criteria=new CDbCriteria();
		
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id";
			$criteria->params = array(':merchant_id'=>intval($merchant_id));
		}
		
		$criteria->addInCondition('status', (array) $status );
		$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
				
		$count = AR_ordernew::model()->count($criteria); 
		return intval($count);
	}
	
	public static function CustomerTotalByStatus($period=0)
	{		
		$date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		$date_end = date("Y-m-d");
		
		$criteria=new CDbCriteria();	
		$criteria->condition = "status=:status";
		$criteria->params = array(':status'=>'active');					
		$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
				
		$count = AR_client::model()->count($criteria); 
		return intval($count);
	}
	
	public static function TotalRefund($merchant_id=0,$period=0)
	{		
		$total = 0;
		$date_start = date("Y-m-d", strtotime(date("c")." -$period days"));
		$date_end = date("Y-m-d");
		
		$criteria=new CDbCriteria();
		$criteria->select = "sum(trans_amount) as total";
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id";
			$criteria->params = array(':merchant_id'=>intval($merchant_id));
		}
		$criteria->addInCondition('transaction_name', array('partial_refund','refund') );
		$criteria->addInCondition('status', array('paid') );
		$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );		
		if($model = AR_ordernew_transaction::model()->find($criteria)){
			$total = $model->total;
		}
		return floatval($total);
	}
	
	public static function MerchantTotal($merchant_type=0, $status=array()) 
	{		
		
		$criteria=new CDbCriteria();	
		if($merchant_type>0){
			$criteria->condition = "merchant_type=:merchant_type";
			$criteria->params = array(':merchant_type'=>intval($merchant_type));		
		}
		if(is_array($status) && count($status)>=1){			
			$criteria->addInCondition('status', (array) $status );
		}		
		$count = AR_merchant::model()->count($criteria); 
		return intval($count);
	}
	
	public static function EarningTotalCount($status=array(), $date_start='', $date_end='')
	{
		$criteria=new CDbCriteria();			
		if(is_array($status) && count($status)>=1){			
			$criteria->addInCondition('status', (array) $status );
		}		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}		
		$count = AR_ordernew::model()->count($criteria); 
		return intval($count);
	}
	
	public static function EarningByOrder($account_type='', $status=array() , $date_start='', $date_end='' )
	{
		$criteria=new CDbCriteria();			
		if($account_type=="admin"){
			$criteria->select = "sum(commission) as commission";
		} elseif ( $account_type=="merchant"){
			$criteria->select = "sum(merchant_earning) as commission";
		} elseif ( $account_type=="sales"){
			$criteria->select = "sum(total) as commission";			
		}
		
		if(is_array($status) && count($status)>=1){			
			$criteria->addInCondition('status', (array) $status );
		}		
		if(!empty($date_start) && !empty($date_end)){
			$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $date_start , $date_end );
		}		
		$model = AR_ordernew::model()->find($criteria); 
		if($model){			
			return floatval($model->commission);
		}
		return 0;
	}
		
}
/*end class*/