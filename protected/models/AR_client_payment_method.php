<?php
class AR_client_payment_method extends CActiveRecord
{	

	public $method_meta;
	
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
		return '{{client_payment_method}}';
	}
	
	public function primaryKey()
	{
	    return 'payment_method_id';	 
	}
		
	public function attributeLabels()
	{
		return array(
		  'client_id'=>t("client_id")
		);
	}
	
	public function rules()
	{
		return array(

		     array('client_id,payment_code,as_default', 
            'required','message'=> t(Helper_field_required) ),   
            
            array('attr1,attr2,payment_uuid,reference_id','safe'),
            
            array('payment_code', 'findRecords', 'on'=>"insert" ),
		
		);
	}
	
	public function findRecords($attribute,$params)
	{				
		$stmt="
		SELECT payment_method_id FROM {{client_payment_method}}
		WHERE payment_code=".q($this->payment_code)."
		AND client_id = ".q( (integer) $this->client_id)."
		AND attr2 = " .q($this->attr2). "
		AND merchant_id = ".q( intval($this->merchant_id) )."
		";				
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			$this->addError('payment_code', t("Payment already exist") );
		} 
	}

    protected function beforeSave()
	{
		if(parent::beforeSave()){
			if($this->isNewRecord){				
				$this->date_created = CommonUtility::dateNow();		
				$this->payment_uuid = CommonUtility::generateUIID();			
			} else {
				$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
			
			Yii::app()->db->createCommand("UPDATE {{client_payment_method}}
		    SET as_default = 0
		    WHERE client_id=".q(intval($this->client_id))."
		    AND merchant_id=".q(intval($this->merchant_id))."
		    ")->query();	
			
			return true;
		} else return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		$data = array();
		if(is_array($this->method_meta) && count($this->method_meta)>=1 ){
			foreach ($this->method_meta as $item) {
				$item['payment_method_id'] = $this->payment_method_id;
				$data[] = $item;
			}
			$builder=Yii::app()->db->schema->commandBuilder;
		    $command=$builder->createMultipleInsertCommand('{{payment_method_meta}}',$data);
		    $command->execute();
		}
	}

	protected function afterDelete()
	{
		parent::afterDelete();	
		
		switch ($this->payment_code) {
			case "ocr":	
			  $model_cc = AR_client_cc::model()->find('client_id=:client_id AND cc_id=:cc_id', 
		        array(
		         ':client_id'=>$this->client_id,
		         ':cc_id'=>$this->reference_id
		        )); 		
		        if($model_cc){			        	
		        	$model_cc->delete();
		        }
			break;
			
			case "cod":		
			  //
			break;
			
			default:	
			  if(Yii::app()->getModule($this->payment_code)){
			     Yii::app()->getModule($this->payment_code)->delete($this);
			  }
			break;
			
		}			
	}
		
}
/*end class*/
