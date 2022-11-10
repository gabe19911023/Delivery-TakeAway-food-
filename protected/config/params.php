<?php
define("Helper_field_required", '{attribute} is required' );
define("Helper_field_email", 'Invalid email address' );
define("Helper_field_repeat_password", 'Initial Password must be repeated exactly.' );
define("Helper_field_email_exist", 'Email address already exist.' );
define("Helper_field_unique", '{attribute} "{value}" has already been taken.' );
define("Helper_field_added", '{attribute} "{value}" has already been added.' );
define("Helper_field_url", '{attribute} is not a valid URL.' );
define("Helper_field_numeric", 'This field must be a number.' );
define("Helper_field_numeric_tooSmall", '{attribute} is too small (minimum is {min}).' );
define("Helper_field_numeric_tooBig", '{attribute} is too big (maximum is {max}).' );
define("Helper_field_time", 'this field must be time example hh:mm.' );
define("Helper_not_found", 'record not found' );
define("Helper_success", 'Succesful' );
define("Helper_update", 'Succesfully updated' );
define("Helper_created", 'Succesfully created' );
define("Helper_settings_saved", 'Settings saved' );
define("Helper_failed_update", 'Failed cannot update' );
define("Helper_failed_save", 'Failed cannot save' );

define("Helper_file_tooLarge",'The file was larger than 10MB. Please upload a smaller file.');
define("Helper_file_wrongType",'The file "{file}" cannot be uploaded. Only files with these extensions are allowed: {extensions}.');
define("Helper_file_allowEmpty",'This field cannot be blank.');
define("Helper_maxSize",1024 * 1024 * 10);
define("Helper_imageType",'jpg,jpeg,gif,png,webp');

define("Helper_password_min",1);
define("Helper_password_max",40);
define("Helper_password_compare","New Password must be repeated exactly");
define("Helper_password_toshort","this field is too short (minimum is {min} characters).");
define("Helper_toolong","this field is too long (maximum is {max} characters).");

define("HELPER_RECORD_NOT_FOUND","Record not found.");
define("HELPER_NO_RESULTS","No Results.");
define("HELPER_CORRECT_FORM","Please correct the forms");
define("HELPER_ACCESS_DENIED","Access Denied");
define("HELPER_NOT_VALID_URL","This field is not a valid URL");

// this contains the application parameters that can be maintained via GUI
return array(	
	'title'=>'GeaMenu',
	'list_limit'=>10,		
	'db_cache_enabled'=>false,
	'cache'=>1000,
	'cache_count'=>1,
	'map_credentials'=>array(),
	'upload_type'=>Helper_imageType,
	'upload_size'=>Helper_maxSize,
	'local_id'=>'kmrs_local_id',
	'local_transtype'=>'transaction_type',
	'image'=>array(
	  'driver'=>"gd",
	  'sizes'=>array(
	    '@thumbnail'=>"150x150",
	    '@1x'=>"240x192",
	    '@2x'=>"550x440",	    
	  ),
	  'sizes_avatar'=>array(
	    '@thumbnail'=>"150x150",
	    '@1x'=>"240x192",	    
	  )
	),
	'account_type'=>array(
	   'admin'=>"admin",
	   'merchant'=>"merchant",
	   'customer'=>"customer",
	),
	'size_image'=>"@1x",
	'size_image_medium'=>"@2x",
	'size_image_thumbnail'=>"@thumbnail",	
	'realtime'=>array(
	  'admin_channel'=>'admin-channel',
	  'merchant_channel'=>'merchant-channel-',
	  'notification_event'=>'notification-event',	  
	  'event_tracking_order'=>'event-tracking-order',
	),	
);

defined('DEMO_MERCHANT') or define('DEMO_MERCHANT', array());

function t($text='',$args=array(),$language='front')
{
	return Yii::t($language,$text,(array)$args);
}

function dump($data=array()){
	echo '<pre>';print_r($data);echo '</pre>';
}

function q($data='')
{
	return Yii::app()->db->quoteValue($data);
}

function websiteUrl()
{
	return Yii::app()->getBaseUrl(true);
}

function websiteDomain()
{
	return Yii::app ()->request->hostinfo;
}