<?php
class AR_plans extends CActiveRecord
{	
	   		
	public $multi_language,$title_trans,$description_trans;
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
		return '{{plans}}';
	}
	
	public function primaryKey()
	{
	    return 'package_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'title'=>t("Title"),
		    'description'=>t("Description"),
		    'price'=>t("Price"),
		    'promo_price'=>t("Promo Price"),		
		    'item_limit'=>t("Item limit"),
		    'order_limit'=>t("Order limit"),
		    'trial_period'=>t("Trial Period"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('title,status,package_period', 
		  'required','message'=> CommonUtility::t( Helper_field_required ) ),
		  array('title,description,status', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),       		  
		  
          array('price,promo_price', 'numerical', 'integerOnly' => false,
		    'message'=>t(Helper_field_numeric)),
		    
		   array('item_limit,order_limit,ordering_enabled,trial_period', 'numerical', 'integerOnly' => true,
		    'message'=>t(Helper_field_numeric)), 
		    
          array('title_trans,description_trans,trial_period','safe'),   
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
				$this->package_uuid = CommonUtility::createUUID("{{plans}}",'package_uuid');
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
				
		/*if($this->multi_language){			
			$name  = $this->title_trans;
			if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			   $name[KMRS_DEFAULT_LANGUAGE] = !empty($name[KMRS_DEFAULT_LANGUAGE])?$name[KMRS_DEFAULT_LANGUAGE]:$this->title;
		    }			
		    
		    $description  = $this->description_trans;
		    if(isset($name[KMRS_DEFAULT_LANGUAGE])){
			   $description[KMRS_DEFAULT_LANGUAGE] = !empty($description[KMRS_DEFAULT_LANGUAGE])?$description[KMRS_DEFAULT_LANGUAGE]:$this->description;
		    }								
		} else {
			$name[KMRS_DEFAULT_LANGUAGE] = $this->title;						
			$description[KMRS_DEFAULT_LANGUAGE] = $this->description;						
		}*/
		
		$name = array(); $description = array();
				
		$name = $this->title_trans;
		$description = $this->description_trans;
		
		$name[KMRS_DEFAULT_LANGUAGE] = $this->title;
		$description[KMRS_DEFAULT_LANGUAGE] = $this->description;
		
		Item_translation::insertTranslation( 
			(integer) $this->package_id ,
			'package_id',
			'title',
			'description',
			array(	                  
			  'title'=>$name,
			  'description'=>$description
			),"{{plans_translation}}");
			
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
		Item_translation::deleteTranslation($this->package_id,'package_id','plans_translation');
		
		AR_admin_meta::model()->deleteAll('meta_name=:meta_name AND meta_value1=:meta_value1 ',array(
		   ':meta_name'=>'plan_features',
		   ':meta_value1'=>$this->package_id
		));
		
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}	
		
}
/*end class*/
