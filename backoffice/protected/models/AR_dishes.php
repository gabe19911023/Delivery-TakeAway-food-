<?php
class AR_dishes extends CActiveRecord
{	
	   		
	public $image;
	public $dish_name_trans;
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
		return '{{dishes}}';
	}
	
	public function primaryKey()
	{
	    return 'dish_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'dish_name'=>t("Dish Name"),
		    'image'=>t("Featured Image"),
		    'status'=>t("Status")
		);
	}
	
	public function rules()
	{		
		return array(
		  array('dish_name,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('dish_name,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('dish_name_trans','safe'),
		  /*array('image', 'file', 'types'=> Helper_imageType, 'safe' => false,
			  'maxSize'=> Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),			  
			  'allowEmpty' => false,'on'=>'new','message'=>t("Image is required")
			),      */
		  array('photo','safe'),
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
			$name  = $this->dish_name_trans;
		    if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			  $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->dish_name;
		    }				
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->dish_name;
		}*/
		
		$name = array(); $description = array();
				
		$name = $this->dish_name_trans;				
		$name[KMRS_DEFAULT_LANGUAGE] = $this->dish_name;		
		
		Item_translation::insertTranslation( 
		(integer) $this->dish_id ,
		'dish_id',
		'dish_name',
		'',
		array(	                  
		  'dish_name'=>$name,	  
		),"{{dishes_translation}}");
		
		
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
				
		Item_translation::deleteTranslation($this->dish_id,'dish_id','dishes_translation');
				
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
