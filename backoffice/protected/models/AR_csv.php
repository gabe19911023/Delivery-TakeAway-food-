<?php
class AR_csv extends CActiveRecord
{
	public $image;	
	
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
		return '{{merchant_csv}}';
	}
	
	public function primaryKey()
	{
	    return 'csv_id';	 
	}

	public function rules()
	{
		return array(
			array('image', 'file', 'types'=>'csv', 'safe' => false,
			  'maxSize'=>Helper_maxSize,
			  'tooLarge'=>t("Helper_file_tooLarge"),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => false
			),
		);
	}
	
	public function attributeLabels()
	{
		return array(
		    'image'=>t("Choose CSV File"),		    
		);
	}
	
	protected function beforeSave()
	{
		if(parent::beforeSave()){
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
		if(file_exists($this->file_path)){
			$stmt="LOAD DATA INFILE '".$this->file_path."'
	        INTO TABLE {{merchant_csv_details}}
	        FIELDS
	            TERMINATED BY ','
	            ENCLOSED BY '\"'
	        LINES
	            TERMINATED BY '\n'
	         IGNORE 1 LINES		
	        (restaurant_name,restaurant_slug,restaurant_phone,contact_name,contact_phone,contact_email,cuisine,service,
	        tags,delivery_distance_covered,distance_unit,is_ready,status,
	        merchant_type,street,city,state,post_code,country_code
	        )	 
	        SET csv_id = ".q($this->csv_id).";       
	        ";
			$connection=Yii::app()->db;
			$transaction=$connection->beginTransaction();
			try {
				$connection->createCommand($stmt)->execute();
				$transaction->commit();
			} catch(Exception $e) {
				 //echo $e->getMessage();die();
				$transaction->rollBack();
			}
		} 		
	}

	protected function afterDelete()
	{
		parent::afterDelete();
		$this->csv_id;
		$stmt = "
		DELETE FROM {{merchant_csv_details}}
		WHERE 
		csv_id = ".q($this->csv_id)."
		";
		Yii::app()->db->createCommand($stmt)->query();
		if(file_exists($this->file_path)){
			@unlink($this->file_path);
		}
	}
	
}
/*end class*/