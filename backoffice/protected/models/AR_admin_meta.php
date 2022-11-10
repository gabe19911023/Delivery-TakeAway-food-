<?php
class AR_admin_meta extends CActiveRecord
{	

	public $status_new_order, $status_cancel_order, $status_delivered, $status_rejection, 	
	$tracking_status_receive, $tracking_status_process, $tracking_status_in_transit, $tracking_status_delivered,
	$tracking_status_delivery_failed,
	$meta_value_trans,
	$receipt_logo, $path , $receipt_thank_you, $receipt_footer,
	$receipt_thank_you_trans,$receipt_footer_trans,
	$invoice_create_template_id, $refund_template_id , $partial_refund_template_id, $delayed_template_id,
	$payout_new_payout_template_id,$payout_paid_template_id, $payout_cancel_template_id, 
	$payout_request_auto_process,$payout_request_enabled, $payout_minimum_amount,$payout_process_days
	;
	
	public $realtime_app_enabled,$realtime_provider, $pusher_app_id, $pusher_key, $pusher_secret, $pusher_cluster;
	public $webpush_provider ,$pusher_instance_id, $pusher_primary_key;
	public $onesignal_app_id,$onesignal_rest_apikey;
	public $ably_apikey;
	public $piesocket_clusterid, $piesocket_api_key, $piesocket_api_secret, $piesocket_websocket_api;
	public $webpush_app_enabled, $tracking_status_ready, $status_completed, $status_delivery_fail, $status_failed,
	$tracking_status_completed, $tracking_status_failed, $payout_number_can_request , $payment_plan_id,
	$zone
	;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return static the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_meta}}';
	}
	
	public function primaryKey()
	{
	    return 'meta_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'meta_name'=>t("meta_name"),
		    'meta_value'=>t("Description"),	
		    'payout_minimum_amount'=>t("Payout minimum amount"),   
		    'payout_process_days'=>t("Payout number of days to process"),
		    'pusher_instance_id'=>t("Instance ID"),
		    'pusher_primary_key'=>t("Primary key"),
		    'onesignal_app_id'=>t("App ID"),
		    'onesignal_rest_apikey'=>t("Rest API Key"),
		    'ably_apikey'=>t("Private API Key"),
		    'piesocket_clusterid'=>t("Cluster ID"),
		    'piesocket_api_key'=>t("API Key"),
		    'piesocket_api_secret'=>t("API secret"),
		    'piesocket_websocket_api'=>t("WebSocket API endpoint"),
		    'payout_number_can_request'=>t("Number of payouts"),	
		    'pusher_app_id'	=>t("Pusher App Id"),
		    'pusher_key'=>t("Pusher Key"),
		    'pusher_secret'=>t("Pusher Secret"),
		    'pusher_cluster'=>t("Pusher Cluster"),
		    'piesocket_api_key'=>t("API secret"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('meta_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('meta_name,meta_value,meta_value1,', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('meta_name,meta_value1,meta_value_trans','safe'),
		  
		  array('payout_minimum_amount,payout_number_can_request', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
			} else {				
			}
			$this->date_modified = CommonUtility::dateNow();			
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		if($this->scenario=="with_translation"):
			$name = $this->meta_value_trans;					
			$name[KMRS_DEFAULT_LANGUAGE] = $this->meta_value;			
						
			Item_translation::insertTranslation( 
			(integer) $this->meta_id ,
			'meta_id',
			'meta_value',
			'',
			array(	                  
			  'meta_value'=>$name,			  
			),"{{admin_meta_translation}}");									
		endif;
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
		
		if($this->scenario=="with_translation"){
		   Item_translation::deleteTranslation($this->meta_id,'meta_id','admin_meta_translation');		
		}
	}
	
	public static function saveMeta($meta_name='', $meta_value='', $meta_value1='',$scenario='')
	{		
		$model=AR_admin_meta::model()->find("meta_name=:meta_name",array(
		  ':meta_name'=>$meta_name
		));
		if($model){			
			$model->scenario = $scenario;
			$model->meta_value=$meta_value;
			if(!empty($meta_value1)){
				$model->meta_value1=$meta_value1;
			}
			if(isset($_POST['AR_admin_meta'][ $meta_name."_trans" ])){
				$model->meta_value_trans = $_POST['AR_admin_meta'][ $meta_name."_trans" ];
			}
			$model->save();
		} else {			
			$model = new AR_admin_meta;
			$model->scenario = $scenario;
			$model->meta_name = $meta_name;
			$model->meta_value = $meta_value;
			if(!empty($meta_value1)){
				$model->meta_value1=$meta_value1;
			}
			$model->save();
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
		
		return true;
	}
	
	public static function saveMetaWithID($meta_name='', $meta_value='', $meta_value1='',$scenario='')
	{		
		$model=AR_admin_meta::model()->find("meta_name=:meta_name AND meta_value1=:meta_value1 ",array(
		  ':meta_name'=>$meta_name,
		  ':meta_value1'=>$meta_value1
		));
		if($model){			
			$model->scenario = $scenario;
			$model->meta_value=$meta_value;
			if(!empty($meta_value1)){
				$model->meta_value1=$meta_value1;
			}
			if(isset($_POST['AR_admin_meta'][ $meta_name."_trans" ])){
				$model->meta_value_trans = $_POST['AR_admin_meta'][ $meta_name."_trans" ];
			}
			$model->save();
		} else {			
			$model = new AR_admin_meta;
			$model->scenario = $scenario;
			$model->meta_name = $meta_name;
			$model->meta_value = $meta_value;
			if(!empty($meta_value1)){
				$model->meta_value1=$meta_value1;
			}
			$model->save();
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
		
		return true;
	}
	
	public static function getValue($meta_name='')
    {
     	$model  = AR_admin_meta::model()->find("meta_name=:meta_name",array(
		 ':meta_name'=>trim($meta_name)
		));			
		if($model){
			return array(
			  'meta_value'=>$model->meta_value,
			  'meta_value1'=>$model->meta_value1,
			);
		}
		return false;
     }
     
     public static function getMeta($meta_name=array())
     {
     	  $data = array();
     	  $criteria=new CDbCriteria();
     	  $criteria->addInCondition('meta_name', (array) $meta_name );
     	  
     	  $dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{cache}}');
     	  $model = AR_admin_meta::model()->cache( Yii::app()->params->cache , $dependency , Yii::app()->params->cache_count )->findAll($criteria);  
     	  if($model){
     	  	  foreach ($model as $item) {
     	  	  	 $data[$item->meta_name] = array(
     	  	  	   'meta_value'=>$item->meta_value,
     	  	  	   'meta_value1'=>$item->meta_value1,
     	  	  	 );
     	  	  }
     	  }
     	  return $data;
     }
     
     public static function getMetaWithID($meta_name=array(),$meta_value1='')
     {
     	  $data = array();
     	  $criteria=new CDbCriteria();
     	  $criteria->condition="meta_value1=:meta_value1";
     	  $criteria->params = array(':meta_value1'=>intval($meta_value1));
     	  $criteria->addInCondition('meta_name', (array) $meta_name );
     	  
     	  $dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{cache}}');
     	  $model = AR_admin_meta::model()->cache( Yii::app()->params->cache , $dependency , Yii::app()->params->cache_count )->findAll($criteria);  
     	  if($model){
     	  	  foreach ($model as $item) {
     	  	  	 $data[$item->meta_name] = array(
     	  	  	   'meta_value'=>$item->meta_value,
     	  	  	   'meta_value1'=>$item->meta_value1,
     	  	  	 );
     	  	  }
     	  }
     	  return $data;
     }     
		
}
/*end class*/