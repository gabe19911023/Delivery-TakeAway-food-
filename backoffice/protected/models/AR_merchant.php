<?php
class AR_merchant extends CActiveRecord
{	
	   
	public $tags;	
	public $cuisine2;
	public $service2;
	public $featured;
	
	public $merchant_master_table_boooking;
	public $merchant_master_disabled_ordering;
	public $disabled_single_app_modules;
	
	public $payment_gateway ;
	public $image;
	public $image2;
	
	public $balance;
	public $trial_end;
	public $total;
	public $cuisine_group;
	
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
		return '{{merchant}}';
	}
	
	public function primaryKey()
	{
	    return 'merchant_id';	 
	}
	
	public function relations()
	{
		 return array(
		   'meta'=>array(self::BELONGS_TO, 'AR_merchant_meta', 'merchant_id'),
		   'option'=>array(self::BELONGS_TO, 'AR_option', 'merchant_id'),		   
		 );
	}
	
	/**
	 * Declares the validation rules.	 
	 */
	public function rules()
	{
		 return array(
            array('restaurant_slug,restaurant_name,contact_phone,contact_email,
            delivery_distance_covered,distance_unit,contact_name,status', 
            'required','on'=>'information',
            'message'=> CommonUtility::t(Helper_field_required) ), 
            
            array('restaurant_slug,restaurant_name,contact_phone,contact_email,contact_name,restaurant_phone,
            ,description',
            'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),         
            
            array('contact_email','email','message'=> t(Helper_field_email) ),              
            
            array('delivery_distance_covered','numerical','integerOnly'=>true ,'on'=>'information'),
            array('delivery_distance_covered','length','min'=>1, 'max'=>14 ,'on'=>'information' ),
            
            array('restaurant_slug,contact_email,contact_phone,merchant_uuid','unique','message'=>t(Helper_field_unique)),
            
            array('tags,restaurant_phone,is_ready,payment_gateway,featured,description,
            short_description,percent_commision,commision_based,path,commision_type','safe'),   
            
            array('short_description','length','max'=>1000),
            
            //array('merchant_type', 'required', 'on'=>'membership'),
            //array('merchant_type', 'validateMerchantType', 'on'=>'membership'),                                      
            /*array('package_id,membership_expired,commision_type,percent_commision,invoice_terms
            ','safe','on'=>'membership'),*/
            
            array('merchant_type,package_id','safe'),
            
            array('is_featured','safe','on'=>'featured'),              
            array('disabled_ordering,close_store','safe'),
            
            //array('street,city,state,post_code,country_code,latitude,lontitude','required','on'=>'address'),              
            array('address,latitude,lontitude','required','on'=>'address'),                  
            // array('facebook,instagram,ticktok','required','on'=>'social_media'),
            array('facebook,instagram,ticktok','length','max'=>191),              
            
            array('percent_commision,delivery_distance_covered', 'numerical', 'integerOnly' => false,		    		    
		    'message'=>t(Helper_field_numeric)),
		    
		    array('image,image2', 'file', 'types'=>Helper_imageType, 'safe' => false,
			  'maxSize'=>Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => true
			),      
          
			array('restaurant_name,address,contact_email,contact_phone,merchant_type', 
            'required','on'=>'website_registration',
            'message'=> CommonUtility::t(Helper_field_required) ), 
                        
            array('merchant_type', 'match', 'not' => false, 'pattern' => '/[^a-zA-Z0]/', 
            'message' => t("Select membership program") , "on"=>"website_registration"),
            
         );
	}
		
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		    'restaurant_name'=>t("Restaurant name"),
		    'street'=>t("Street address"),
		    'image'=>t("Merchant Logo"),
		    'image2'=>t("Header Background"),
		    'restaurant_slug'=>t("Restaurant Slug"),
		    'contact_name'=>t("Contact Name"),
		    'contact_phone'=>t("Contact Phone"),
		    'contact_email'=>t("Contact email"),
		    'delivery_distance_covered'=>t("Delivery Distance Covered"),
		    'latitude'=>t("Latitude"),
		    'lontitude'=>t("Lontitude"),
		    'percent_commision'=>t("Percent Commision"),
		    'facebook'=>t("Facebook"),
		    'instagram'=>t("Instagram"),
		    'ticktok'=>t("Ticktok"),
		);
	}
	
	protected function beforeSave()
	{
		if(parent::beforeSave()){
						
			if(DEMO_MODE && !$this->isNewRecord && in_array($this->merchant_id,DEMO_MERCHANT)){				
				return false;
			}
			
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();						
			} else {
				$this->date_modified = CommonUtility::dateNow();											
			}
			$this->ip_address = CommonUtility::userIp();	
			
			if(empty($this->restaurant_slug) && !empty($this->restaurant_name) ){
			   $this->restaurant_slug = $this->generateSlug($this->restaurant_name);
			}
			
			if(empty($this->merchant_uuid)){
				$this->merchant_uuid = CommonUtility::createUUID("{{merchant}}",'merchant_uuid');
			}
			
			return true;
		} else return true;
	}
	
	protected function beforeValidate()
	{
		parent::beforeValidate();
		if(!empty($this->restaurant_slug)){
		   $this->restaurant_slug = CommonUtility::toSeoURL($this->restaurant_slug);			
		}
		return true;
	}
	public function generateSlug($restaurant_name='')
	{
		$slug = CommonUtility::toSeoURL($restaurant_name);
		
		$stmt="SELECT * FROM {{merchant}}
		WHERE restaurant_slug=".q($slug)."
		";			
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			$restaurant_name.="-".CommonUtility::generateAplhaCode(2);
			return self::generateSlug($restaurant_name);
		}
		return $slug;
	}
	
	public function validateMerchantType($attribute,$params)
	{
	/*	switch ($this->merchant_type) {
			case 1:					   
				if($_POST['AR_merchant']['package_id']<=0){
					$this->addError('package_id',t("Plan is required") );
				}
				if(empty($_POST['AR_merchant']['membership_expired'])){
					$this->addError('membership_expired',t("Membership expiration is required") );
				}
				break;
				
			case 2:	
			   if(empty($_POST['AR_merchant']['commision_type'])){
					$this->addError('commision_type',t("Commission type is required") );
			   }
			   if($_POST['AR_merchant']['percent_commision']<=0){
					$this->addError('percent_commision',t("Percent Commision must be an integer.") );
			   }
			   break;
			   
			case 3:  
			   if($_POST['AR_merchant']['invoice_terms']<=0){
					$this->addError('invoice_terms',t(Helper_field_required) );
			   } 
			   break;
		
			default:
				break;
		}		*/
	
	    /*switch ($this->merchant_type) {
	    	case 1:	    		
	    	    
	    		break;
	    
	    	case 2:
	    	    if(empty($this->commision_based)){
					$this->addError('commision_based',t("Commission type is required") );
			    }
			    if($this->percent_commision<=0){
					$this->addError('percent_commision',t("Percent Commision must be an integer.") );
			    }
	    		break;
	    }*/
	
	}
	
	protected function afterSave()
	{
		parent::afterSave();		
		if(is_array($this->payment_gateway) && count($this->payment_gateway)>=1){			
			$data = array();
			foreach ($this->payment_gateway as $val) {								
				if($val!="0"){
				   $data[]=$val;
				}
			}			
			MerchantTools::saveMerchantMeta($this->merchant_id,$data,'payment_gateway');
		}
						
		if(is_array($this->service2) && count($this->service2)>=1){
			MerchantTools::saveMerchantMeta($this->merchant_id,$this->service2,'services');	
		}		
		
		if(is_array($this->featured) && count($this->featured)>=1){
			MerchantTools::saveMerchantMeta($this->merchant_id,$this->featured,'featured');	
		}		
		
		if(is_array($this->cuisine2) && count($this->cuisine2)>=1){
			MerchantTools::insertCuisine($this->merchant_id,$this->cuisine2);
		}		
		
		if(is_array($this->tags) && count($this->tags)>=1){
			MerchantTools::insertTag($this->merchant_id,$this->tags);	
		}		
			
		Yii::import('ext.runactions.components.ERunActions');	
		$cron_key = CommonUtility::getCronKey();		
		$get_params = array( 
		   'merchant_uuid'=> $this->merchant_uuid,
		   'key'=>$cron_key,
		);		
				
				
		switch ($this->scenario) {
			case "website_registration":			
				CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/aftermerchantsignup?".http_build_query($get_params) );			
				break;		
				
			case "after_payment_validate":				
			    CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/aftermerchantpayment?".http_build_query($get_params) );			
				break;		
				
			case "trial_will_end":	
			    $get_params['trial_end'] = $this->trial_end;
			    CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/merchant_trial_end?".http_build_query($get_params) );							
			    break;		
			    
		    case "plan_past_due":				
			    CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/after_plan_past_due?".http_build_query($get_params) );			
				break;			

		    case "information":	   
		       try {
				    CWallet::getCardID( Yii::app()->params->account_type['merchant'] , $this->merchant_id );
				} catch (Exception $e) {
				    $wallet = new AR_wallet_cards;	
				    $wallet->account_type = Yii::app()->params->account_type['merchant'] ;
			        $wallet->account_id = intval($this->merchant_id);
			        $wallet->save();
				}	
		       break;
		}
				
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
	
	protected function beforeDelete()
	{				
		if(DEMO_MODE && in_array($this->merchant_id,DEMO_MERCHANT)){				
		    return false;
		}
		return true;
	}
	
	protected function afterDelete()
	{
	    parent::afterDelete();	    
	    MerchantTools::MerchantDeleteALl($this->merchant_id);
	    	    
		/*ADD CACHE REFERENCE*/
		CCacheData::add();
	}
		
}
/*end class*/
