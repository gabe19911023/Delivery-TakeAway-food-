<?php
class AR_offers extends CActiveRecord
{	

	public $applicable_selected;
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
		return '{{offers}}';
	}
	
	public function primaryKey()
	{
	    return 'offers_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'offer_percentage'=>t("Offer Percentage"),
		  'offer_price'=>t("Orders Over"),
		  'valid_from'=>t("Valid From"),
		  'valid_to'=>t("Valid To"),		  
		);
	}
	
	public function rules()
	{
		return array(
		  array('offer_percentage,offer_price,valid_from,valid_to,status', 
		  'required','message'=> t( Helper_field_required ) ),
		  
		  array('offer_percentage,offer_price,valid_from,valid_to,status', 
		  'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  
		  array('applicable_selected','safe'),
		  
		  array('offer_percentage,offer_price', 'numerical', 'integerOnly' => false,	
		  'min'=>1,
		  'message'=>t(Helper_field_numeric)),
		  		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		$this->offer_percentage = (float) $this->offer_percentage;
		$this->offer_price = (float) $this->offer_price;
		
		if($this->applicable_selected){
			$this->applicable_to = json_encode($this->applicable_selected);
		} else $this->applicable_to='';
		
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
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
		
}
/*end class*/
