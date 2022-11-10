<?php
class CReviews
{
	public static function reviewsCount($merchant_id='')
	{		
		$criteria=new CDbCriteria();
			
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id AND status='publish' and parent_id=0";
			$criteria->params = array(
			  ':merchant_id'=>$merchant_id,		  
			);
		} else {
			$criteria->condition = "status='publish' and parent_id=0";			
		}
		
		$dependency = CCacheData::dependency();	
		$count = AR_review::model()->cache(Yii::app()->params->cache, $dependency)->count($criteria); 
		return intval($count);		
	}
	
	public static function totalCountByRange($merchant_id=0, $start='', $end='')
	{
		$criteria=new CDbCriteria();
		
		if($merchant_id>0){
			$criteria->condition = "merchant_id=:merchant_id AND status=:status ";
			$criteria->params = array( 
			    ':merchant_id'=>intval($merchant_id),
			    ':status'=>'publish'
			 );		
		} else {
			$criteria->condition = "status='publish'";			
		}
		
		$criteria->addBetweenCondition("DATE_FORMAT(date_created,'%Y-%m-%d')", $start , $end );
				
		$dependency = CCacheData::dependency();	
		$count = AR_review::model()->cache(Yii::app()->params->cache, $dependency)->count($criteria); 
		return intval($count);
	}
	
	public static function userAddedReview($merchant_id=0,$limit=3)
	{
		$data = array();
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "a.client_id, count(*) as total_review,
		b.first_name,b.last_name,b.date_created, b.avatar as logo, b.path
		";
		$criteria->join='LEFT JOIN {{client}} b on  a.client_id=b.client_id ';
		
		if($merchant_id>0){
			$criteria->condition = "a.merchant_id=:merchant_id and b.client_id IS NOT NULL";
			$criteria->params = array(':merchant_id'=>$merchant_id);
		} else {
			$criteria->condition = "b.client_id IS NOT NULL";			
		}
		
		$criteria->group="a.client_id";
		$criteria->order = "count(*) DESC";	
		$criteria->limit = intval($limit);		
				
		$dependency = CCacheData::dependency();			
		$model = AR_review::model()->cache(Yii::app()->params->cache, $dependency)->findAll($criteria);    
		
		if($model){
			foreach ($model as $item) {				
				$data[]=array(
				  'client_id'=>$item->client_id,
				  'first_name'=>$item->first_name,
				  'last_name'=>$item->last_name,
				  'image_url'=>CMedia::getImage($item->logo,$item->path,'@thumbnail',CommonUtility::getPlaceholderPhoto('customer')),
				);
			}
			return $data;
		}
		return false;
	}
	
	public static function summaryCount($merchant_id=0,$grand_total=0)
	{
		$review_list = array(5,4,3,2,1); $data = array();
		foreach ($review_list as $count) {
			$criteria=new CDbCriteria();
			$criteria->select ="count(*) as total";
			
			if($merchant_id>0){
				$criteria->condition = "merchant_id=:merchant_id AND status=:status AND rating=:rating";
			    $criteria->params = array(
			      ':merchant_id'=>$merchant_id,
			      ':status'=>'publish',
			      'rating'=>$count
			    );			
			} else {
				$criteria->condition = "status=:status AND rating=:rating";
			    $criteria->params = array(			      
			      ':status'=>'publish',
			      'rating'=>$count
			    );			
			}
		    		    
			$dependency = CCacheData::dependency();				
			$model = AR_review::model()->cache(Yii::app()->params->cache, $dependency)->find($criteria);
		    $total = isset($model->total)?$model->total:0;
		    
		    if($total>0){
			    $percent = round(($total/$grand_total)*100);
			    $data[] = array(
			      'count'=>$count,
			      'review'=>$percent,
			      'in_percent'=>"$percent%"
			    );
		    } else {
		    	$data[] = array(
			      'count'=>0,
			      'review'=>0,
			      'in_percent'=>"0%"
			    );
		    }
		}
		return $data;
	}
	
	public static function reviews($merchant_id='',$page=0, $page_limit=10)
	{
		$stmt="
		SELECT a.review,a.rating,
		concat(b.first_name,' ',b.last_name) as fullname,
		b.avatar, b.path,
		a.date_created,a.as_anonymous,
		(
		 select group_concat(meta_name,';',meta_value)
		 from {{review_meta}}
		 where review_id = a.id
		) as meta,
		
		(
		 select group_concat(upload_uuid,';',filename,';',path)
		 from {{media_files}}
		 where upload_uuid IN (
		      select meta_value from {{review_meta}}
		      where review_id = a.id
		   )
		) as media
			
		FROM {{review}} a
		LEFT JOIN {{client}} b
		ON
		a.client_id = b.client_id
		
		WHERE a.merchant_id=".q($merchant_id)."
		AND a.status ='publish'
		AND parent_id = 0
		ORDER BY a.id DESC
		LIMIT $page,$page_limit
		";
		
		if(Yii::app()->params->db_cache_enabled){
		  $dependency = new CDbCacheDependency("SELECT count(*),MAX(date_modified) FROM {{review}} WHERE merchant_id=".q($merchant_id)."  ");
		  $res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll();		  
		} else $res = Yii::app()->db->createCommand($stmt)->queryAll();	
		
		if($res){
			$data = array();
			foreach ($res as $val) {
				$meta = !empty($val['meta'])?explode(",",$val['meta']):'';
				$media = !empty($val['media'])?explode(",",$val['media']):'';
				
				$meta_data = array(); $media_data=array();
				
				if(is_array($media) && count($media)>=1){
					foreach ($media as $media_val) {
						$_media = explode(";",$media_val);
						$media_data[$_media['0']] = array(
						  'filename'=>$_media[1],
						  'path'=>$_media[2],
						);
					}
				}
							
				if(is_array($meta) && count($meta)>=1){
					foreach ($meta as $meta_value) {
						$_meta = explode(";",$meta_value);						
						if($_meta[0]=="upload_images"){							 
							 if(isset( $media_data[$_meta[1]] )){									 								    
							    $meta_data[$_meta[0]][] = CMedia::getImage(
							      $media_data[$_meta[1]]['filename'],
							      $media_data[$_meta[1]]['path']
							    );
							 }
						} else $meta_data[$_meta[0]][] = $_meta[1];						
					}
				}
				
				$data[]=array(
				  'review'=>Yii::app()->input->xssClean($val['review']),
				  'rating'=>intval($val['rating']),
				  'fullname'=>Yii::app()->input->xssClean($val['fullname']),
				  'hidden_fullname'=>CommonUtility::mask($val['fullname']),				  
				  'url_image'=>CMedia::getImage($val['avatar'],$val['path'],Yii::app()->params->size_image,
				   CommonUtility::getPlaceholderPhoto('customer')),
				  'as_anonymous'=>intval($val['as_anonymous']),
				  'meta'=>$meta_data,
				);
			}			
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function getUuid($uiid='')
	{
		$media = AR_media::model()->findAll(
			    'upload_uuid=:upload_uuid',
			    array(':upload_uuid'=>$uiid)
			);
		if($media){
			$data = array();
			foreach ($media as $val) {
				$data[]=array(
				  'url_image'=>CommonUtility::getPhoto($val->filename,'','/reviews'),			   
			      'id'=>$val->filename,	
			      'upload_uuid'=>$val->upload_uuid
				);
			}
			return $data;
		}
		return false;
	}	
	
	public static function insertMeta( $review_id='', $meta_name='',$meta_array = array())
	{
		if(is_array($meta_array) && count($meta_array)>=1){
			$data = array();
			foreach ($meta_array as $value) {
				$data[] = array(
				  'review_id'=>intval($review_id),
				  'meta_name'=>trim($meta_name),
				  'meta_value'=>$value,
				  'date_created'=>CommonUtility::dateNow()
				);
			}
			$builder=Yii::app()->db->schema->commandBuilder;
		    $command=$builder->createMultipleInsertCommand('{{review_meta}}',$data);
		    $command->execute();
		}
	}
	
	public static function insertMetaImages( $review_id='', $meta_name='',$meta_array = array())
	{
		if(is_array($meta_array) && count($meta_array)>=1){
			$data = array();
			foreach ($meta_array as $value) {
				$data[] = array(
				  'review_id'=>intval($review_id),
				  'meta_name'=>trim($meta_name),
				  'meta_value'=>$value['id'],
				  'date_created'=>CommonUtility::dateNow()
				);
			}
			$builder=Yii::app()->db->schema->commandBuilder;
		    $command=$builder->createMultipleInsertCommand('{{review_meta}}',$data);
		    $command->execute();
		}
	}
		
}
/*end class*/