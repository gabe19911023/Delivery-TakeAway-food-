<?php
class AR_cuisine extends CActiveRecord
{	
	   		
	public $image;
	public $cuisine_name_translation;
	public $multi_language;
	
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
		return '{{cuisine}}';
	}
	
	public function primaryKey()
	{
	    return 'cuisine_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'cuisine_name'=>t("Cuisine Name"),
		    'image'=>t("Featured Image"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('cuisine_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('cuisine_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('cuisine_name_translation,color_hex,font_color_hex','safe'),
		  array('image', 'file', 'types'=>Helper_imageType, 'safe' => false,
			  'maxSize'=>Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => true
			),      
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
						
			if(DEMO_MODE){				
			    return false;
			}
			
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();					
			} else {
				$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();			
		
		/*$name = array();
		if($this->multi_language){			
			$name = $this->cuisine_name_translation;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			    $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->cuisine_name;
		    }	
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->cuisine_name;
		}*/
		
		$name = $this->cuisine_name_translation;		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->cuisine_name;		
				
		Item_translation::insertTranslation( 
		(integer) $this->cuisine_id ,
		'cuisine_id',
		'cuisine_name',
		'',
		array(	                  
		  'cuisine_name'=>$name,	  
		),"{{cuisine_translation}}");
		
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
			
		Item_translation::deleteTranslation($this->cuisine_id,'cuisine_id','cuisine_translation');		
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
