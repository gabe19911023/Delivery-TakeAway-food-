<?php
class AR_subcategory extends CActiveRecord
{	

	public $subcategory_translation,$subcategory_description_translation,
	$multi_language,$image;	
	
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
		return '{{subcategory}}';
	}
	
	public function primaryKey()
	{
	    return 'subcat_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'subcategory_name'=>t("Name"),		    
		    'subcategory_description'=>t("Description"),
		    'status'=>t("Status"),	
		    'image'=>t("Featured Image"),		
		);
	}
	
	public function rules()
	{
		return array(
		  array('subcategory_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('subcategory_name,subcategory_description,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('featured_image,subcategory_translation,subcategory_description_translation','safe'),
		  
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
		
		if($this->scenario=="remove_image"){
			return true;
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
		
		if($this->scenario=="remove_image"){
			return true;
		}
				
		$name = $this->subcategory_translation;
		$description = $this->subcategory_description_translation;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->subcategory_name;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->subcategory_description;
		
						
		Item_translation::insertTranslation( 
		(integer) $this->subcat_id ,
		'subcat_id',
		'subcategory_name',
		'subcategory_description',
		array(	                  
		  'subcategory_name'=>$name,	  
		  'subcategory_description'=>$description,
		),"{{subcategory_translation}}");
		
		
		/*MEDIA*/
		if($this->image){
			$media = new AR_media;
			$media->merchant_id = (integer) $this->merchant_id;
			$media->title = $this->image->name;
			$media->filename = $this->featured_image;
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
	    if(DEMO_MODE && in_array($this->merchant_id,DEMO_MERCHANT)){				
	        return false;
	    }
	    return true;
	}	

	protected function afterDelete()
	{
		parent::afterDelete();		
		Item_translation::deleteTranslation($this->subcat_id,'subcat_id','subcategory_translation');		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
