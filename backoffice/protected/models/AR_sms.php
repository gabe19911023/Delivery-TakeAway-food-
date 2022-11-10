<?php
class AR_sms extends CActiveRecord
{	

	public $multi_language, $title_translation, $description_translation;
	
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
		return '{{sms_package}}';
	}
	
	public function primaryKey()
	{
	    return 'sms_package_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'title'=>t("Title"),
		    'description'=>t("Description"),
		    'price'=>t("Price"),
		    'promo_price'=>t("Promo Price"),
		    'sms_limit'=>t("SMS Credit"),
		    'sequence'=>t("Sequence"),
		    'status'=>t("Status"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('title,description,price,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('title,description,price', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('price,promo_price,', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('sms_limit,sequence,', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('title_translation,description_translation','safe'),
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
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
		
		$name = array(); $description =array();
		/*if($this->multi_language){								
			$name  = $this->title_translation;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
				$name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->title;
			}		
			$description  = $this->description_translation;
			if(isset($description[KMRS_DEFAULT_LANGUAGE])){
				$description[KMRS_DEFAULT_LANGUAGE] = !empty($description[KMRS_DEFAULT_LANGUAGE])?$description[KMRS_DEFAULT_LANGUAGE]:$this->description;
			}		
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->title;
			$description[KMRS_DEFAULT_LANGUAGE] = $this->description;
		}*/
		
		$name = $this->title_translation;
		$description = $this->description_translation;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->title;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->description;
		
		Item_translation::insertTranslation( 
		(integer) $this->sms_package_id ,
		'sms_package_id',
		'title',
		'description',
		array(	                  
		  'title'=>$name,
		  'description'=>$description
		),"{{sms_package_translation}}");
			
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}

	protected function afterDelete()
	{
		parent::afterDelete();					
		Item_translation::deleteTranslation($this->sms_package_id,'sms_package_id','sms_package_translation');	
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();	
	}
		
}
/*end class*/
