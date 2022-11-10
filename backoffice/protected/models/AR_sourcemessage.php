<?php
class AR_sourcemessage extends CActiveRecord
{	

	public $translation;
	
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
		return '{{sourcemessage}}';
	}
	
	public function primaryKey()
	{
	    return 'id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		    'id'=>t("id"),		    
		);
	}
	
	public function rules()
	{
		return array(
		  array('id,category,message', 
		  'required','message'=> t( Helper_field_required ) ),
		  		  
		  array('message', 'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')), 		 
		  
		  array('category','ext.UniqueAttributesValidator','with'=>'message',
		   'message'=>t("Key already exist")." (".$this->message.")"
		  ),
		  	  
		);
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
	}

	protected function afterDelete()
	{
		parent::afterDelete();		
	}
	
	public static function getlastID()
	{
		$stmt="SELECT max(id)+1 as last_id FROM {{sourcemessage}}";
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			return $res['last_id'];
		}		
		return 1;
	}
		
}
/*end class*/