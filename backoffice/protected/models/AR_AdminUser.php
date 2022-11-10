<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class AR_AdminUser extends CActiveRecord
{	
	 public $old_password;	 	 
	 public $new_password;
	 public $repeat_password;
	 public $email_address;
	 public $image;
	 //public $contact_number;
	 
	 //public $admin_id='';
	 
	 /*$this->admin_id
     Yii::app()->user->id*/
	   
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
		return '{{admin_user}}';
	}
	
	public function primaryKey()
	{
	    return 'admin_id';
	}
	
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		    'first_name'=>CommonUtility::t("First Name"),
		    'last_name'=>CommonUtility::t("Last Name"),
		    'email_address'=>CommonUtility::t("Email address"),		    
		    'new_password'=>CommonUtility::t("New Password"),
		    'repeat_password'=>CommonUtility::t("Confirm New Password"),
		    'contact_number'=>CommonUtility::t("Contact number"),
		    'image'=>t("Profile Photo"),
		    'username'=>t("Username"),
			'old_password'=>t("Old Password"),
		);
	}
		
	
	/**
	 * Declares the validation rules.	 
	 */
	public function rules()
	{
		 return array(
		      array('first_name,last_name,email_address', 'required', 'on'=>'profile_update'),  
		      array('email_address', 'email', 'message'=> CommonUtility::t(Helper_field_email) ),       
		      		      
		      array('first_name,last_name,email_address,role,username,status', 
              'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),     
		      
		      array('old_password,new_password,repeat_password', 'required', 'on'=>'update_password'),  
		      
		      array('new_password', 'compare', 'compareAttribute'=>'repeat_password',
              'message'=> t(Helper_password_compare) ),
              
              array('old_password', 'findPasswords', 'on'=>"update_password" ),
              
              array('new_password, repeat_password', 'length', 'min'=>Helper_password_min, 'max'=>Helper_password_max,
              'tooShort'=>t(Helper_password_toshort) ,
              ),
              
              array('image','safe'),
              
              array('image', 'file', 'types'=>Helper_imageType, 'safe' => false,
			  'maxSize'=>Helper_maxSize,
			  'tooLarge'=>t(Helper_file_tooLarge),
			  'wrongType'=>t(Helper_file_wrongType),
			  'allowEmpty' => true
			),      
			
			array('first_name,last_name,email_address,username,new_password,repeat_password,
		      status,role
		      ', 'required', 'on'=>'create'),  
			
			array('email_address,contact_number,username','unique','message'=>t(Helper_field_unique),
			'on'=>'create,update,profile_update'
			),
			
			array('first_name,last_name,email_address,status,role
		      ', 'required', 'on'=>'update'),  
            
			array('new_password,repeat_password', 'required', 'on'=>'update_user_password'),  
			
			array('email_address', 'required', 'on'=>'forgot_password'),  
			array('email_address', 'findEmailAddress', 'on'=>"forgot_password" ),
         );
	}
			
	public function findPasswords($attribute, $params)
	{				
		if(!empty($this->old_password)){					   
		   if(!empty($this->new_password) && !empty($this->repeat_password) ){		   	
			   $user = AR_AdminUser::model()->findByPk($this->admin_id);			   
			   if ($user->password != md5($this->old_password)){
			   		$this->addError('old_password', CommonUtility::t("Old password does not match with current password") );	
			   }
		   } else {
		   	    $this->addError('new_password', CommonUtility::t( Helper_field_required ) );	
		   	    $this->addError('repeat_password', CommonUtility::t(  Helper_field_required ) );	
		   }
		}		
	}
	
	public function findEmail($attribute, $params)
	{				
		$user = AR_AdminUser::model()->findBySql("SELECT email_address FROM {{admin_user}}
		WHERE email_address=:email_address AND admin_id NOT IN (:admin_id)", array(
		  ':email_address'=>$this->email_address,
		  ':admin_id'=>$this->admin_id
		));
		if($user){
			$this->addError('email_address', CommonUtility::t( Helper_field_email_exist ) );	
		}		
	}
	
	public function findEmailAddress($attribute, $params)
	{		
		if(!empty($this->email_address)){
			$user = AR_AdminUser::model()->find('email_address=:email_address', array(':email_address'=>$this->email_address));			
			if(!$user){
				$this->addError('email_address', t("Email address not found") );	
			}
		}
	}

	protected function beforeSave()
	{
		if(parent::beforeSave()){
						
			if(DEMO_MODE && $this->main_account==1){				
		        return false;
		    }
			
			if($this->isNewRecord){
				$this->date_created = CommonUtility::dateNow();				
				$this->admin_id_token = CommonUtility::generateToken("{{admin_user}}",'admin_id_token');				
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
		if($this->image){
			$params = array(
			  'title'=>$this->image->name,
			  'filename'=>$this->profile_photo,
			  'path'=>CommonUtility::uploadPath(false),
			  'size'=>$this->image->size,
			  'media_type'=>$this->image->type,
			  'date_created'=>CommonUtility::dateNow(),
			  'ip_address'=>CommonUtility::userIp()
			);
			Yii::app()->db->createCommand()->insert("{{media_files}}",$params);
		}		
		
		
		if($this->scenario=="create"){
			try {
			    CWallet::getCardID( Yii::app()->params->account_type['admin']);
			} catch (Exception $e) {
				$wallet = new AR_wallet_cards;
				$wallet->account_type = Yii::app()->params->account_type['admin'] ;
				$wallet->save();
			}	
		} elseif ( $this->scenario =="send_forgot_password"){
			Yii::import('ext.runactions.components.ERunActions');	
			$cron_key = CommonUtility::getCronKey();		
			$get_params = array( 
			   'admin_token'=> $this->admin_id_token,
			   'key'=>$cron_key,
			);								
			CommonUtility::runActions( CommonUtility::getHomebaseUrl()."/task/adminpassword?".http_build_query($get_params) );					
		}
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
				
		$media = AR_media::model()->find( "filename=:filename" ,array(
		 ':filename'=>$this->profile_photo
		));
		if($media){
		   $media->delete(); 
		}
	}
		
}
/*end class*/
