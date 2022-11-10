<?php
class AR_item_seo extends CActiveRecord
{	

	public $image;
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
		return '{{item}}';
	}
	
	public function primaryKey()
	{
	    return 'item_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'meta_title'=>t("Meta Title"),		    
		    'meta_description'=>t("Meta Description"),
		    'meta_keywords'=>t("Keywords"),	
		    'image'=>t("Meta Image"),
		);
	}
	
	public function rules()
	{
		return array(
		  
		  array('meta_title,meta_description,meta_keywords,meta_image', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('meta_title,meta_description,meta_keywords,meta_image','safe'),
		  		  
		  array('image', 'file', 'types'=> Helper_imageType, 'safe' => false,
			  'maxSize'=> Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => false,'on'=>'new','message'=>t(Helper_file_allowEmpty)
			),      
		  		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if(DEMO_MODE && !$this->isNewRecord && in_array($this->merchant_id,DEMO_MERCHANT)){				
		    return false;
		}

		
		if($this->isNewRecord){
			$this->date_created = CommonUtility::dateNow();					
		} else {
			$this->date_modified = CommonUtility::dateNow();											
		}
		$this->ip_address = CommonUtility::userIp();
		
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		/*MEDIA*/
		if($this->image){			
			$media = new AR_media;
			$media->merchant_id = (integer) $this->merchant_id;
			$media->title = $this->image->name;
			$media->filename = $this->meta_image;
			$media->path = CommonUtility::uploadPath(false);
			$media->size = $this->image->size;
			$media->media_type = $this->image->type;
			$media->date_created = CommonUtility::dateNow();
			$media->ip_address = CommonUtility::userIp();
			$media->save();
		}
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
	
    protected function beforeDelete()
	{				
	    if(DEMO_MODE){				
		    return false;
		}
	    return true;
	}
	
	protected function afterDelete()
	{
		parent::afterDelete();		
				
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
