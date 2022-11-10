<?php
class AR_merchant_type extends CActiveRecord
{	
	   			
	public $multi_language,$type_name_trans;
	
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
		return '{{merchant_type}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'type_id'=>t("ID"),
		    'type_name'=>t("Name"),
		    'status'=>t("Status"),
		    'commission'=>t("Commission"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('type_id,type_name,type_name,commision_type', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('type_name,type_name,color_hex,font_color_hex,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 
		  array('color_hex,font_color_hex,type_name_trans','safe'),
		  array('type_id','unique','message'=>t(Helper_field_unique)),
		  
		  array('type_id', 'numerical', 'integerOnly' => true,	'min'=>1,	  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('commission,based_on','safe'),
		  
		  array('commission', 'numerical', 'integerOnly' => false,
		  'message'=>t(Helper_field_numeric)),
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
						
		$name = array();
		/*if($this->multi_language){						
			$name  = $this->type_name_trans;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
				$name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->type_name;
			}			
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->type_name;						
		}*/
		$name = $this->type_name_trans;		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->type_name;		
		
		Item_translation::insertTranslation( 
		(integer) $this->type_id ,
		'type_id',
		'type_name',
		'',
		array(	                  
		  'type_name'=>$name,			  
		),"{{merchant_type_translation}}");
		
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
		Item_translation::deleteTranslation($this->type_id,'type_id','merchant_type_translation');
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
