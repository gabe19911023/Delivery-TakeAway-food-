<?php
class AR_tax extends CActiveRecord
{	

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
		return '{{tax}}';
	}
	
	public function primaryKey()
	{
	    return 'tax_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'size_name'=>t("Size Name"),		    
		    'active'=>t("Active"),	
		);
	}
	
	public function rules()
	{
		return array(
		  array('tax_name', 
		  'required','message'=> t("Tax name is required" ) ),
		  
		  array('tax_rate', 
		  'required','message'=> t("Tax rate is required" ) ),
		  
		  array('tax_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('date_created,date_modified,ip_address','safe'),
		 		  
		  array('tax_rate', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),

		  array('tax_name','length','max'=>100),	  
		  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){

			if(DEMO_MODE && !$this->isNewRecord && in_array($this->merchant_id,DEMO_MERCHANT)){		
				throw new Exception("Not allowed to change in demo", 1);						
				return false;
			}

			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();		
				$this->tax_uuid = CommonUtility::createUUID("{{tax}}",'tax_uuid');
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
		
		if($this->tax_type=="standard" || $this->tax_type=="euro"){
		if($this->default_tax==1){
			Yii::app()->db->createCommand("UPDATE {{tax}} set default_tax='0' 
			WHERE
			merchant_id=".q($this->merchant_id)."
			AND 
			tax_id NOT IN (".q($this->tax_id).")
			")->query();
		}
		}
		
		CCacheData::add();
	}

	protected function beforeDelete()
	{				
		if(DEMO_MODE && in_array($this->merchant_id,DEMO_MERCHANT)){				
			throw new Exception("Not allowed to change in demo", 1);						
		    return false;
		}
		return true;
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
