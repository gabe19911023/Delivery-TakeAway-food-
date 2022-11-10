<?php
class AR_currency extends CActiveRecord
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
		return '{{currency}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'currency_code'=>t("Currency"),
		    'exchange_rate'=>t("Rate"),
		    'exchange_rate_fee'=>t("+ Exchange fee"),
		    'number_decimal'=>t("Decimals"),
		    'decimal_separator'=>t("Decimal Separator"),
		    'thousand_separator'=>t("Thousand Separator"),
		);
	}
	
	public function rules()
	{
		return array(
		  array('currency_code,', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('currency_code,decimal_separator,thousand_separator', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
		  
		  array('as_default,currency_position,number_decimal,decimal_separator,thousand_separator','safe'),
		  
		  array('decimal_separator,thousand_separator','length' , 'min'=>1,'max'=>1,
		  'tooLong'=>t("this fields is too long (maximum is 1 characters).")
		  ),
		  
		  array('number_decimal', 'numerical', 'integerOnly' => true,		  
		  'message'=>t(Helper_field_numeric)),
		  		 
		  array('exchange_rate,exchange_rate_fee', 'numerical', 'integerOnly' => false,		  
		  'message'=>t(Helper_field_numeric)),
		  
		  array('exchange_rate_fee,exchange_rate_fee','safe'),
		  
		  array('currency_code','unique','message'=>t(Helper_field_added)),
		  
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
		if($this->as_default==1){			
			self::updateDefault($this->id);
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
	
	public function updateDefault($id='')
	{
		 $stmt="
		 UPDATE {{currency}}			
		 SET as_default=0
		 WHERE id NOT IN (".q($id).")
		 ";					 
		 Yii::app()->db->createCommand($stmt)->query();
	}
		
}
/*end class*/
