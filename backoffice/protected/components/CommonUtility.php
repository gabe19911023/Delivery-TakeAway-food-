<?php
class CommonUtility
{
	
	public static function dateNow()
	{
		return date("Y-m-d G:i:s");;
	}
	
	public static function userIp()
	{
		return Yii::app()->request->getUserHostAddress();
	}
	
	public static function t($text='',$args=array(),$language='backend')
	{
		return Yii::t($language,$text,(array)$args);
	}
	
	public static function q($data='')
	{
		return Yii::app()->db->quoteValue($data);
	}
	
	public static function dataTablesLocalization()
	{
		return array(
    	  'decimal'=>'',
    	  'emptyTable'=> t('No data available in table'),
    	  'info'=> t('Showing [start] to [end] of [total] entries',array(
    	    '[start]'=>"_START_",
    	    '[end]'=>"_END_",
    	    '[total]'=>"_TOTAL_",
    	  )),
    	  'infoEmpty'=> t("Showing 0 to 0 of 0 entries"),
    	  'infoFiltered'=>t("(filtered from [max] total entries)",array(
    	    '[max]'=>"_MAX_"
    	  )),
    	  'infoPostFix'=>'',
    	  'thousands'=>',',
    	  'lengthMenu'=> t("Show [menu] entries",array(
    	    '[menu]'=>"_MENU_"
    	  )),
    	  'loadingRecords'=>t('Loading...'),    	  
    	  'processing'=>'<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">'.t("Loading...").'..n.</span>',
    	  'search'=>t("Search:"),
    	  'zeroRecords'=>t("No matching records found"),
    	  'paginate' =>array(
    	    'first'=>t("First"),
    	    'last'=>t("Last"),
    	    'next'=>t("Next"),
    	    'previous'=>t("Previous")
    	  ),
    	  'aria'=>array(
    	    'sortAscending'=>t(": activate to sort column ascending"),
    	    'sortDescending'=>t(": activate to sort column descending")
    	  )
    	);    	
	}
	
	public static function getDataToDropDown($table_name='', $primary_fields='', $fields_value='',$where='',$orderby='',$limit='')
	{
		$data = array();
		$stmt="
		SELECT $primary_fields,$fields_value
		FROM $table_name
		$where
		$orderby
		$limit
		";						
		$dependency = CCacheData::dependency();
		$res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll();
		if($res){
			foreach ($res as $val) {				
				$data[ $val[$primary_fields] ] = Yii::app()->input->stripClean($val[$fields_value]);
			}
		}
		return $data;
	}
	
	public static function generateToken($table="", $field_name='' ,$token='')
	{
		$token = empty($token)? sha1(uniqid(mt_rand(), true)) : $token;
		
		$stmt="SELECT * FROM $table
		WHERE $field_name=".q($token)."
		";			
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			return self::generateToken($table,$field_name,$token);
		}
		return $token;
	}
	
	public static function toSeoURLOLD($string){
	    $string = str_replace(array('[\', \']'), '', $string);
	    $string = preg_replace('/\[.*\]/U', '', $string);
	    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
	    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
	    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
	    return strtolower(trim($string, '-'));
	}   

	public static function toSeoURL($string)
	{
		try {
			require_once 'WSanitize.php';
			return sanitize_title_with_dashes($string);
		} catch (Exception $e) {
			return self::toSeoURLOLD($string);
		}		
	}
	
	public static function uploadPath($full_path=true,$upload_folder='upload')
	{
		if($full_path){
		    //return Yii::getPathOfAlias('webroot')."/../$upload_folder";		
		    return Yii::getPathOfAlias('upload_dir');		
		} else {
			return "/$upload_folder";
		}
	}
	
	public static function homePath()
	{
		return Yii::getPathOfAlias('home_dir');		
	}
		
	public static function uploadDestination($folder='')
	{
		$path = self::homePath()."/$folder";
		if(!file_exists($path)){
			@mkdir($path,0777);
		} 			
		return $path;
	}
	
	public static function uploadURL()
	{
		if(IS_FRONTEND){
			return Yii::app()->createAbsoluteUrl("/upload");		
		} else return websiteDomain().Yii::app()->request->baseUrl."/../upload";
	}
	
	public static function formatShortText($value,$limit=90) {
		$CHtmlPurifier = new CHtmlPurifier();
		$CHtmlPurifier->options = array('HTML.Allowed'=>'');		
		$value = stripslashes($CHtmlPurifier->purify($value));
		
        if(strlen($value)>$limit) {
            $retval=CHtml::tag('span',array('title'=>$value),CHtml::encode(mb_substr($value,0,$limit-3,Yii::app()->charset).'...'));
        } else {
            $retval=CHtml::encode($value);
        }
        return $retval;
    }
	
    public static function setMenuActive($parent='.membership',$class_name='.plans_create',$scriptname='menu_active')
	{
		ScriptUtility::registerScript(array(
		  '$(".siderbar-menu li'.$parent.'").addClass("active")',		 
		  '$(".siderbar-menu li'.$parent.' ul li'.$class_name.'").addClass("active")',		 
		),$scriptname,CClientScript::POS_END);
				
	}
	
	public static function setSubMenuActive($parent='.siderbar-menu',$child='.membership')
	{
		ScriptUtility::registerScript(array(
		  '$("'.$parent.' li'.$child.'").addClass("active")',		  
		),'sub_menu_active',CClientScript::POS_END);
				
	}
	
	public static function getSiteLogo()
	{
		$opts = OptionsTools::find(array('website_logo'));		
		return CMedia::getImage(isset($opts['website_logo'])?$opts['website_logo']:'',"/upload/all",
		Yii::app()->params->size_image
		,'logo@2x.png');
	}
	
	public static function getPhotox($filename='',$default='sample-merchant-logo@2x.png',$folder='')
	{					
		$upload_path = CommonUtility::uploadPath();		
		$url = websiteDomain().Yii::app()->theme->baseUrl."/assets/images/$default";		
		if(empty($folder)){
			if ( file_exists($upload_path."/$filename") &&  !empty($filename)){	
				$url = CommonUtility::uploadURL()."/$filename";
			}		
		} else {			
			$folder = str_replace("/upload",'',$folder);			
			if ( file_exists($upload_path.$folder."/$filename") &&  !empty($filename)){					
				$url = CommonUtility::uploadURL().$folder."/$filename";
			}
		}
		return $url;
	}
	
	public static function getPlaceholderPhoto($type='customer',$default='sample-merchant-logo@2x.png')
	{
		switch ($type) {
			case "customer":
				return 'user@2x.png';
				break;
				
			case "merchant_logo":	
			    return 'sample-merchant-logo@2x.png';
				break;
				
			case "item_photo":	 								   	
			case "item":
			   return 'default-image.png';
			   break;	
			   
			case "logo":
				return 'logo@2x.png';
				break;   
			case "icon";
			return 'default-icons.png';
			    break;  
			default:
				return $default;
				break;
		}
	}
	
	public static function validatePhoto($filename='')
	{
		$upload_path = CommonUtility::uploadPath();		
		if ( file_exists($upload_path."/$filename") &&  !empty($filename)){
			return true;
		}
		return false;
	}
	
	/*public static function deletePhoto($filename='',$folder='')
	{
		dump($filename);dump($folder);
		$upload_path = CommonUtility::uploadPath();				
		if(!empty($folder)){
			$folder = str_replace("/upload",'',$folder);			
			$upload_path.=$folder;
		}		
		dump($upload_path);dump($filename);die();
		if ( file_exists($upload_path."/$filename") &&  !empty($filename)){
			@unlink($upload_path."/$filename");
		}
	}*/
	public static function deletePhoto($filename='',$folder='')
	{		
		$home_path = CMedia::homeDir();
		$upload_path = $home_path.DIRECTORY_SEPARATOR.$folder;
		
		if(empty($folder)){
			$upload_path = CommonUtility::uploadPath();
		}
		
		if ( file_exists($upload_path."/$filename") &&  !empty($filename)){
			@unlink($upload_path."/$filename");
		}
	}
		
	public static function MultiLanguage()
	{
		/*if($res = OptionsTools::find(array('enabled_multiple_translation_new'))){
			$enabled = isset($res['enabled_multiple_translation_new'])?$res['enabled_multiple_translation_new']:'';
			if($enabled==1){
				return true;
			}
		}
		return false;*/
		return true;
	}
	
	public static function getMessages($aslist=true)
	{		
    	$path=Yii::getPathOfAlias('webroot')."/protected/messages";    	    	
    	$res=scandir($path);
    	if(is_array($res) && count($res)>=1){
    		foreach ($res as $val) {       			
    			if($val=="."){    				
    			} elseif ($val==".."){  
    			} elseif ($val=="default"){  
    			} elseif ( strpos($val,".") ){      			
    			} else {
    				$list[$val]=$val;
    			}
    		}    		
    		return $list;
    	}
    	return false;		
	}
	
	public static function pagePath()
	{
		return Yii::app()->controller->id."/".Yii::app()->controller->action->id;
	}
	
	public static function SeoURL($string){
	    $string = str_replace(array('[\', \']'), '', $string);
	    $string = preg_replace('/\[.*\]/U', '', $string);
	    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
	    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
	    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
	    return strtolower(trim($string, '-'));
	}   
	
	public static function beautifyFilename($filename) {
	    // reduce consecutive characters
	    $filename = preg_replace(array(
	        // "file   name.zip" becomes "file-name.zip"
	        '/ +/',
	        // "file___name.zip" becomes "file-name.zip"
	        '/_+/',
	        // "file---name.zip" becomes "file-name.zip"
	        '/-+/'
	    ), '-', $filename);
	    $filename = preg_replace(array(
	        // "file--.--.-.--name.zip" becomes "file.name.zip"
	        '/-*\.-*/',
	        // "file...name..zip" becomes "file.name.zip"
	        '/\.{2,}/'
	    ), '.', $filename);
	    // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
	    $filename = mb_strtolower($filename, mb_detect_encoding($filename));
	    // ".file-name.-" becomes "file-name"
	    $filename = trim($filename, '.-');
	    return $filename;
	}

	public static function createLanguageFolder($folder_name='')
	{
		$path = Yii::getPathOfAlias('webroot')."/protected/messages/$folder_name";		
		if(!file_exists($path)){			
			@mkdir($path);
		}
	}
	
	public static function generateAplhaCode($length = 8)
	{
	   $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghijklmnopqrstuvwxyz';
	   $ret = '';
	   for($i = 0; $i < $length; ++$i) {
	     $random = str_shuffle($chars);
	     $ret .= $random[0];
	   }
	   return $ret;
	}
	
	public static function generateNumber($range=10) 
    {
	    $chars = "0123456789";	
	    srand((double)microtime()*1000000);	
	    $i = 0;	
	    $pass = '' ;	
	    while ($i <= $range) {
	        $num = rand() % $range;	
	        $tmp = substr($chars, $num, 1);	
	        $pass = $pass . $tmp;	
	        $i++;	
	    }
	    return $pass;
    }
    
    public static function uuid($prefix = '')
	{
		$chars = md5(uniqid(mt_rand(), true));
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);
		return $prefix . $uuid;
	}
    
    public static function uploadNewFilename($filename='',$ext='')
    {
    	$extension='';
    	if(!empty($ext)){    	
    		 $extension = strtolower($ext);
    	} else {
    		if($explode = explode(".",$filename)){    			
    			$count = count($explode)-1;
    			$extension = isset($explode[$count])?$explode[$count]:'png';    			
    		} else $extension = strtolower(substr($filename,-3,3));    		
    	}
    	
    	$new_filename = self::generateAplhaCode(50).".$extension";
    	return self::generateToken("{{media_files}}",'filename',$new_filename);
    }

    public static function MobileDetect()
    {
    	require_once 'Mobile_Detect.php';
		$detect = new Mobile_Detect;
		return $detect;
    }
    
    public static function deleteMediaFile($filename='')
    {
    	$media = AR_media::model()->find("filename=:filename",array(
		  ':filename'=>$filename,		  
		));		
		if($media){
			$media->delete(); 			
		}
    }
    
    public static function maskCardnumber($cardnumber='')
    {
    	if ( !empty($cardnumber)){
    		$cardnumber = str_replace(" ",'',$cardnumber);
    		return substr($cardnumber,0,4)."XXXXXXXX".substr($cardnumber,-4,4);
    	}
    	return '';
    }
    
    public static function mask($string='', $mask='*')
    {    	
    	if(strlen($string)>1){    		
    	   return str_repeat($mask,strlen($string)-4) . substr($string, -4);    	
    	}
    	return '';
    }
        
    public static function maskEmail($email='', $mask='*')
    {    	
    	if(strlen($email)>1){    		
    	    /*$prefix = substr($email, 0, strrpos($email, '@'));
		    $suffix = substr($email, strripos($email, '@'));
		    $len  = floor(strlen($prefix)/2);		
		    return substr($prefix, 0, $len) . str_repeat('*', $len) . $suffix;*/
    	    preg_match('/^.?(.*)?.@.+$/', $email, $matches);
            return str_replace($matches[1], str_repeat('*', strlen($matches[1])), $email);
    	}
    	return '';
    }
    
    public static function HumanFilesize($size, $precision = 2) {
	    $units = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
	    $step = 1024;
	    $i = 0;
	    while (($size / $step) > 0.9) {
	        $size = $size / $step;
	        $i++;
	    }
	    return round($size, $precision).t($units[$i]);
	}
	
	public static function WriteCookie($cookie_name='', $value='',$is_expired_long=true)
	{
		$cookie = new CHttpCookie($cookie_name, $value);
		if($is_expired_long){
           $cookie->expire = time()+60*60*24*180; 
		}
        Yii::app()->request->cookies[$cookie_name] = $cookie;  		
	}
	
	public static function getCookie($cookie_name='')
	{
		$value = (string)Yii::app()->request->cookies[$cookie_name];
		if (is_string($value) && strlen($value) > 0){
			return $value;
		}
		return false;
	}
	
	public static function deleteCookie($cookie_name='')
	{
		unset(Yii::app()->request->cookies[$cookie_name]);
	}
	
	public static function clearALlCookie()
	{
		Yii::app()->request->cookies->clear();
	}
	
	public static function highlightWord( $content, $word ) {
	    $replace = '<span class="highlight">' . $word . '</span>'; // create replacement
	    $content = str_ireplace( $word, $replace, $content ); // replace content	
	    return $content; 
    }
    
    public static function generateUIID()
    {
    	if($res = Yii::app()->db->createCommand("select UUID() as UUID")->queryRow()){
    		return $res['UUID'];
    	}
    	return false;
    }
    
    public static function createUUID($table="", $field_name='' )
	{
		$token = self::generateUIID();
		
		$stmt="SELECT * FROM $table
		WHERE $field_name=".q($token)."
		";			
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			return self::createUUID($table,$field_name,$token);
		}
		return $token;
	}
    
    public static function MapCredentials($keys='maps')
	{
		$api_keys = ''; $map_provider='';
		$options=OptionsTools::find(array(
		  'map_provider','google_geo_api_key','google_maps_api_key','mapbox_access_token'
		));		
		if($options){
			$map_provider = isset($options['map_provider'])?$options['map_provider']:'';
			switch ($map_provider) {
				case "google.maps":
					if($keys=="maps"){
						$api_keys= isset($options['google_maps_api_key'])?$options['google_maps_api_key']:'';
					} else $api_keys= isset($options['google_geo_api_key'])?$options['google_geo_api_key']:'';
					break;
			
				default:
					$api_keys= isset($options['mapbox_access_token'])?$options['mapbox_access_token']:'';
					break;
			}
			return array(
			  'map_provider'=>$map_provider,
			  'api_keys'=>$api_keys
			);
		}
		return false;
	}	    
	
	public static function checkEmail($email) {
    	$version = phpversion();
        if($version>=7){
            if(!filter_var($email,FILTER_VALIDATE_EMAIL) === false){
                return true;
            } else
                return false;
        } else {
            if (@eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
                return true;
            } else
                return false;            
        }        
    }
    
    public static function parseError($data)
    {    	
    	$error = array();
    	if(is_array($data) && count($data)>=1){
    		foreach ($data as $key=>$val) {    			    			
    			foreach ($val as $value) {
    				$key = str_replace("_"," ",$key);    				
    				$error[] = $value;
    			}
    		}
    		return $error;
    	}
    	return false;
    }
    
    public static function parseModelError($model)
    {
    	$error = array();
    	foreach ($model->errors as $err) {
			foreach ($err as $error) {
				$error[] = $error;
			}				
		}			
		return $error;
    }
    
    public static function parseModelErrorToString($model_error=array(),$line_break="\n")
    {
    	$error = '';
    	if(is_array($model_error) && count($model_error)>=1){
    		foreach ($model_error as $item) {
    			foreach ($item as $val) {
    				$error.="$val".$line_break;
    			}
    		}
    	}
    	return $error;
    }
    
    public static function arrayToQueryParameters($data=array())
    {
    	$query_params = '';
    	if(is_array($data) && count($data)>=1){
    		foreach ($data as $value) {
    			$query_params.=q($value).",";
    		}
    		$query_params = substr($query_params,0,-1);
    		return $query_params;
    	}
    	return false;
    }
    
    public static function arrayToString($data=array(),$separator=',')
    {
    	$string ='';
    	if(is_array($data) && count($data)>=1){
    		foreach ($data as $value) {    			
    			$string.=t($value)."$separator ";
    		}
    		$string = substr($string,0,-2);
    	}
    	return $string;
    }
    
    public static function cutString($string='', $limit=255)
	{
		if(!empty($string)){
			if(strlen($string)>$limit){
				return substr($string,0,$limit);
			} 
		} 
		return $string;
	}
	
	public static function dateDifference($start, $end )
    {
        $uts['start']=strtotime( $start );
		$uts['end']=strtotime( $end );
		if( $uts['start']!==-1 && $uts['end']!==-1 )
		{
		if( $uts['end'] >= $uts['start'] )
		{
		$diff    =    $uts['end'] - $uts['start'];
		if( $days=intval((floor($diff/86400))) )
		    $diff = $diff % 86400;
		if( $hours=intval((floor($diff/3600))) )
		    $diff = $diff % 3600;
		if( $minutes=intval((floor($diff/60))) )
		    $diff = $diff % 60;
		$diff    =    intval( $diff );            
		return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
		}
		else
		{			
		return false;
		}
		}
		else
		{			
		return false;
		}
		return( false );
     }    
     
     public static function prettyMobile($mobile='')
     {     	
     	if(!empty($mobile)){
     		if (!preg_match("/\+\b/i",$mobile)) {
     			return "+$mobile";
     		}
     	}
     	return $mobile;
     }
     
     public static function getCronKey()
     {     	
     	return CRON_KEY;
     }
     
     public static function getHomebaseUrl()
     {
     	 if(IS_FRONTEND){
     	 	return Yii::app()->createAbsoluteUrl("/");
     	 } else {
	     	 //$url = websiteDomain();
	     	 /*if(!empty(HOME_FOLDER) && strlen(HOME_FOLDER)>1){
	     	 	return $url."/".HOME_FOLDER;
	     	 }*/	     	 
	     	 $url = Yii::app()->getBaseUrl(true);	     	 
	     	 $url = str_replace(BACKOFFICE_FOLDER,"",$url);
	     	 if(!empty($url)){
	     	 	if(substr($url,-1,1)=="/"){	     	 		
	     	 		$url = substr($url,0,-1);
	     	 	}
	     	 }
	     	 return $url;
     	 }
     }
     
     public static function sendEmail($email='', $toname='', $subject='', $body='')
     {
     	  try {
		      CEmailer::init();
			  CEmailer::setTo( $email );
			  CEmailer::setName( $toname );
			  CEmailer::setSubject( $subject );
			  CEmailer::setBody( $body );
			  $resp = CEmailer::send();
			  return $resp;
		  } catch (Exception $e) {
		  	  return false;
		  }
     }
     
     public static function sendSMS($to='', $body='', $client_id='', $merchant_id='', $name='' )
     {     	  
     	  try {
	          CSMSsender::init();
		      CSMSsender::setTo($to);
		      CSMSsender::setBody($body);
		      CSMSsender::setClientID( $client_id );
		      CSMSsender::setMerchantID( $merchant_id );
		      CSMSsender::setName( $name );
		      $resp = CSMSsender::send();	
		      return $resp;
	      } catch (Exception $e) {
		  	  return false;
		  }
     }
     
    public static function cleanString($text='', $lower=true)
	{
		if(!empty($text)){
			if($lower){
				return trim( strtolower($text) );
			} else return trim($text);			
		}
		return $text;
	}
	
	public static function removeSpace($text='', $replace_with='')
	{
		if(!empty($text)){
			return str_replace(" ",$replace_with,$text);
		}
		return $text;
	}
	
	public static function arrayToMustache($data=array())
	{
		$return_data = array();
		if(is_array($data) && count($data)>=1){
			foreach ($data as $key=> $items) {
				$return_data["{{{$key}}}"]=$items;
			}
		}
		return $return_data;
	}
	
	public static function taxPriceList()
	{
		return array(
		  1=>t("Tax in prices (prices include taxes)"),
		  0=>t("Tax not in prices (prices does not include tax)"),
		);
	}
	
	public static function taxType()
	{
		return array(
		  'standard'=>t("Standard"),
		  'multiple'=>t("Multiple tax"),
		  //'euro'=>t("Euro tax"),
		);
	}
	
	public static function generateRandomColor()
	{
		return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
	}

	public static function escapedJson($data=array())
	{
        $escaped_data = json_encode($data, JSON_HEX_QUOT | JSON_HEX_APOS);
        $escaped_data = str_replace(['\u0022', '\u0027'], ["\\\"", "\\'"], $escaped_data);
		return $escaped_data;
	}

	public static function dataToRow($data=array(), $column=4)
	{		
		$total = count($data);
		$new_data = array(); $x=1; $i=1; $datas = [];
		foreach ($data as $item) {				
			$datas[] = $item;
			if($x>=$column){
				$new_data[] = $datas;
				$x=0;
				$datas = [];				
			} else {			    
				if($i>=$total){
					$new_data[] = $datas;
				}
			}
			$x++; $i++;
		}				
		return $new_data;	
	}

	public static function removeHttp($url) {
		$disallowed = array('http://', 'https://');
		foreach($disallowed as $d) {
		   if(strpos($url, $d) === 0) {
			  return str_replace($d, '', $url);
		   }
		}	   
		if(!empty($url)){
			$url = str_replace("www.",'',$url);			
		}		
		return $url;
	}

	public static function validateDomain($domain_registered='', $domain_from='')
	{
		if (preg_match("/localhost/i", $domain_registered) && preg_match("/localhost/i", $domain_from) ) {
			return true;
		}
		if($domain_registered==$domain_from){			
			return true;
		}
		return false;
	}

	public static function createSlug($slug='',$table='',$field='slug')
	{
		$stmt="SELECT count(*) as total FROM $table
		WHERE $field=".q($slug)."
		";					
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){	
			if($res['total']>0){
				$new_slug = $slug.$res['total'];					
				return self::createSlug($new_slug);
			}
		}
		return $slug;
	}

	public static function runActions($url='')
	{
		Yii::import('ext.runactions.components.ERunActions');
		$options = OptionsTools::find(['runactions_method']);
		$method = isset($options['runactions_method'])?$options['runactions_method']:'';		
		if($method==="touchUrlExt"){
			ERunActions::touchUrlExt($url);
		} else {
			ERunActions::touchUrl($url);
		}
	}

	public static function getAddonStatus($uuid='')
	{
		$enabled = false;
		$model_addon = AR_addons::model()->find("uuid=:uuid",[':uuid'=>trim($uuid) ]);
		if($model_addon){
		    $enabled = $model_addon->activated==1?true:false;
		}
		return $enabled;
	}
			
}
/*end class*/