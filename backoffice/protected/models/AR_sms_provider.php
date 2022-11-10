<?php
class AR_sms_provider extends CActiveRecord
{	

	public $providerid;
	
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
		return '{{sms_provider}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'provider_id'=>t("Provider ID"),
		    'provider_name'=>t("Provider name"),
		    'as_default'=>t("Default")		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('provider_id,provider_name', 
		  'required','message'=> t( Helper_field_required ) ),
		  array('provider_id,provider_name', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		  
		  		  
		  array('as_default,key1,key2,key3,key4,key5,key6,key7','safe'),
		  
		);
	}

    protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		if($this->as_default==1){			
			self::updateDefault($this->id);
		}			
	}

	protected function afterDelete()
	{
		parent::afterDelete();					
	}
	
	public function updateDefault($id='')
	{
		 $stmt="
		 UPDATE {{sms_provider}}			
		 SET as_default=0
		 WHERE id NOT IN (".q($id).")
		 ";					 
		 Yii::app()->db->createCommand($stmt)->query();
	}
	
	public static function Bhash_smstype()
	{
		return array(
		  'normal'=>t("normal"),
		  'flash'=>t("flash"),
		  'unicode'=>t("unicode"),
		);
	}
	
	public static function Bhash_priority()
	{
		return array(
		  'ndnd'=>t("ndnd"),
		  'dnd'=>t("dnd"),		  
		);
	}
	
	public static function Spothit_smstype()
	{
		return array(
		  'premium'=>t("premium"),
		  'lowcost'=>t("lowcost"),		  
		);
	}
		
}
/*end class*/
