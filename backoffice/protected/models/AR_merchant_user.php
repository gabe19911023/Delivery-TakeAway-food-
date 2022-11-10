<?php
class AR_merchant_user extends CActiveRecord
{	
	   	
	public $image,$merchant_id,$new_password,$repeat_password,$old_password;
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
		return '{{merchant_user}}';
	}
	
	public function primaryKey()
	{
	    return 'merchant_user_id';	 
	}
		
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{		
		return array(
		    'first_name'=>t("First Name"),
		    'last_name'=>t("Last Name"),
		    'username'=>t("Username"),
		    'password'=>t("Password"),
		    'contact_email'=>t("Email address"),
		    'contact_number'=>t("Contact number"),
		    'repeat_password'=>t("Confirm Password"),
		    'new_password'=>t("New Password"),
			'old_password'=>t("Old Password"),			
		);
	}
	
			
	/**
	 * Declares the validation rules.	 
	 */
	public function rules()
	{
		 return array(
            array('username,first_name,last_name,contact_email,role', 
            'required'),   

            array('username,first_name,contact_email,last_name,contact_number',
            'filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),         
            
            array('contact_email','email'),              
            
            array('username,contact_email,contact_number','unique'),
            
            array('last_name,status,main_account','safe'),   
            
            //array('password', 'required', 'on'=>'register'),  
            
            array('password, new_password, repeat_password', 'length', 'min'=>1, 'max'=>40),    
              
           array('new_password,repeat_password','required' , 'on'=>'register'),
           
           array('repeat_password', 'compare', 'compareAttribute'=>'new_password','on'=>'register,update,reset_password' ),                        
           
           array('old_password,new_password,repeat_password', 'required', 'on'=>'update_password'),               
           array('old_password', 'findPasswords', 'on'=>"update_password" ),
           
           // Create user condition
                         
            /*array('new_password,repeat_password', 'required', 'on'=>'create_user,change_password'), 
            
            array('new_password', 'compare', 'compareAttribute'=>'repeat_password',
               'on'=>'create_user,change_password' ),
                          
            array('old_password,new_password,repeat_password', 'required', 'on'=>'update_password'),   
            
            array('old_password', 'findPasswords', 'on'=>"update_password" ),*/
                         
         );
	}
		
	public function findPasswords($attribute, $params)
	{				
		if(!empty($this->old_password)){					   
		   if(!empty($this->new_password) && !empty($this->repeat_password) ){		   	
			   $user = AR_merchant_user::model()->findByPk($this->merchant_user_id);			   			   
			   if ($user->password != md5($this->old_password)){
			   		$this->addError('old_password', CommonUtility::t("Old password does not match with current password") );	
			   }
		   } else {
		   	    $this->addError('new_password', CommonUtility::t( Helper_field_required ) );	
		   	    $this->addError('repeat_password', CommonUtility::t(  Helper_field_required ) );	
		   }
		}		
	}
	
	protected function beforeSave()
	{
		if(!parent::beforeSave()){
			return false;
		} 
		
		if(DEMO_MODE && !$this->isNewRecord && in_array($this->merchant_id,DEMO_MERCHANT)){				
		    return false;
		}
		
		if($this->isNewRecord){
			$this->password = md5($this->password);
			$this->user_uuid = CommonUtility::generateToken("{{merchant_user}}",'user_uuid');
			$this->date_created = CommonUtility::dateNow();								
		} else {
			$this->date_modified = CommonUtility::dateNow();											
			if(empty($this->user_uuid)){
				$this->user_uuid = CommonUtility::generateToken("{{merchant_user}}",'user_uuid');
			}
		}
		$this->ip_address = CommonUtility::userIp();	
			
		return true;
	}
	
	protected function afterSave()
	{
		parent::afterSave();
		
		/*MEDIA*/
		if($this->profile_photo && isset($this->image)){
			$media = new AR_media;
			$media->merchant_id = (integer) $this->merchant_id;
			$media->title = $this->image->name;
			$media->filename = $this->profile_photo;
			$media->path = CommonUtility::uploadPath(false);
			$media->size = $this->image->size;
			$media->media_type = $this->image->type;
			$media->date_created = CommonUtility::dateNow();
			$media->ip_address = CommonUtility::userIp();
			$media->save();
		}
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
				
	}
		
}
/*end class*/
