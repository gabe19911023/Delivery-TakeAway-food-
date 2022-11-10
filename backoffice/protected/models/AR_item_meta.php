<?php
class AR_item_meta extends CActiveRecord
{	

	public $file_name,$file_size,$file_type;
	public $merchant_tax;
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
		return '{{item_meta}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		
	}
	
	public function rules()
	{
		return array(
		  
		  array('merchant_id,item_id,meta_name,meta_id', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('merchant_id,item_id,meta_name,meta_id', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  		  
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		/*MEDIA*/
		/*if($this->scenario=="item_gallery"){
			if($this->meta_id){				
				$media = new AR_media;
				$media->merchant_id = (integer) $this->merchant_id;
				$media->title = $this->file_name;
				$media->filename = $this->meta_id;
				$media->path = CommonUtility::uploadPath(false);
				$media->size = $this->file_size;
				$media->media_type = $this->file_type;
				$media->date_created = CommonUtility::dateNow();
				$media->ip_address = CommonUtility::userIp();
				$media->save();					
			}
		}*/
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
	public static function getMeta($merchant_id=0, $item_id=0, $meta_name=array())
	{
		  $data = array();
     	  $criteria=new CDbCriteria();
     	  $criteria->condition="merchant_id=:merchant_id AND item_id=:item_id";
     	  $criteria->params = array(':merchant_id'=>intval($merchant_id),'item_id'=>intval($item_id));
     	  $criteria->addInCondition('meta_name', (array) $meta_name );     	
     	  
     	  $dependency = new CDbCacheDependency('SELECT MAX(date_modified) FROM {{cache}}');
     	  $model = AR_item_meta::model()->cache( Yii::app()->params->cache , $dependency )->findAll($criteria);    
     	  
     	  if($model){
     	  	  foreach ($model as $item) {     	  	  	 
     	  	  	 $data[] = array(
     	  	  	   'meta_id'=>$item->meta_id,
     	  	  	   'meta_value'=>$item->meta_value,
     	  	  	 );
     	  	  }
     	  }
     	  return $data;
	}
	
}
/*end class*/
