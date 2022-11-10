<?php
class AttributesTools
{
	
	public static function initialStatus()
	{
		return 'draft';
	}
	
	public static function PosCode()
	{
		return 'pos';
	}
	
	public static function refundStatus()
	{
		return array('partial_refund','refund');
	}
	
	public static function unit()
	{
		return array(
		  'mi'=>t("Miles"),
		  'km'=>t("Kilometers"),
		);
	}
	
	public static function mapsProvider()
	{
		return array(
		  'google.maps'=>t("Google Maps (default)"),
	      'mapbox'=>t("Mapbox"),
		);
	}
	
	public static function verificationType()
	{
		return array(
		  'email'=>t("Using Email verification"),
	      'sms'=>t("Using SMS verification"),
		);
	}
	
	public static function reviewType()
	{
		return array(
		   2=>t("Review per order"),
		   1=>t("Review merchant"),           
		);
	}
	
	public static function SearchType()
	{
		return array(
		   'address'=>t("Address using map provider"),
		   'zone'=>t("Zone"),
		   'postcode'=>t("Location using define address"),           
		);
	}
	
	public static function locationNickName()
	{
		return array(
		   'home'=>t("Home"),
		   'work'=>t("Work"),           
		   'other'=>t("Other"),           
		);
	}
	
	public static function statusGroup()
	{
		return array(
		   'customer'=>t("customer"),
		   'post'=>t("post"),	
		   'booking'=>t("booking"),
		   'payment'=>t("payment"),
		   'transaction'=>t("transaction"),
		   'gateway'=>t("gateway"),
		);
	}
	
	public static function soldOutOptions()
	{
		return array(
		  // 'substitute'=>t("Go with merchant recommendation"),
		  // 'refund'=>t("Refund this item"),
		  // 'contact'=>t("Contact me"),
		  // 'cancel'=>t("Cancel the entire order")
		  'substitute'=>t("Contact me"),
		  'contact'=>t("Substitute with something similar"),
		  'refund'=>t("Refund this item")
		);
	}
	
	public static function orderButtonsActions()
	{
		return array(
		 'reject_form'=>t("Rejection form")
		);		
	}
	
	public static function transactionTypeList($standard=false)
	{
		if($standard){
			return array(
			  'credit'=>t("Credit"),
			  'debit'=>t("Debit"),			  
			);
		} else {
			return array(
			  'credit'=>t("Credit"),
			  'debit'=>t("Debit"),
			  'payout'=>t("Payout"),
			  'cashin'=>t("Cash In"),
			);
		}
	}
	
	public static function signupTypeList()
	{
		return array(
		   'standard'=>t("Standard signup"),
		   'mobile_phone'=>t("Mobile phone signup"),
		);
	}
	
	public static function paymentStatus()
	{
		return array(
		   'unpaid'=>t("Unpaid"),
		   'paid'=>t("Paid"),
		);
	}
	
	public static function commissionBased()
	{
		return array(
		   'subtotal'=>t("Sub total"),
		   'total'=>t("Total"),
		);
	}

	public static function JwtTokenID(){
		return 'jwt_token';
	}

	public static function JwtMainTokenID(){
		return 'website_jwt_token';
	}
		
	public static function StatusManagement($group_name='',$lang = KMRS_DEFAULT_LANGUAGE)
	{
		/*$cuisine = CommonUtility::getDataToDropDown("{{status_management}}",'status','title',"
		WHERE group_name=".q($group_name)." ","ORDER BY title ASC");
		return $cuisine;*/
		
		$data = array();
		$criteria=new CDbCriteria();
		$criteria->alias = "a";
		$criteria->select = "a.status_id, a.title , b.status ";
		$criteria->condition = "a.language=:language AND b.group_name=:group_name 
		and a.title IS NOT NULL AND TRIM(a.title) <> ''
		";
		$criteria->join='
		LEFT JOIN {{status_management}} b on  a.status_id = b.status_id 		
		';
		$criteria->params = array(
		 ':language'=>$lang,
		 ':group_name'=>$group_name
		);		
		$criteria->order = "a.title ASC";
		
		if($model = AR_status_management_translation::model()->findAll($criteria)){
			foreach ($model as $item) {
				$data[$item->status] = $item->title;
			}
		}
		return $data;
	}
	
	public static function ListSelectCuisine()
	{
		$cuisine = CommonUtility::getDataToDropDown("{{cuisine}}",'cuisine_id','cuisine_name',"
		WHERE status = 'publish'","ORDER BY cuisine_name ASC");
		return $cuisine;
	}
	
	public static function ListSelectTags()
	{
		$tags = CommonUtility::getDataToDropDown("{{tags}}",'tag_id','tag_name',"","ORDER BY tag_name ASC");
		return $tags;
	}
	
	public static function ListSelectServices()
	{
		$services = CommonUtility::getDataToDropDown("{{services}}",'service_code','service_name',
		"WHERE status='publish' ","ORDER BY service_name ASC");
		return $services;
	}
		
	public static function ListMerchantType($lang = KMRS_DEFAULT_LANGUAGE)
	{
		/*$list = CommonUtility::getDataToDropDown("{{merchant_type}}",'type_id','type_name',
		"WHERE status='publish' ","ORDER BY type_id ASC");
		return $list;*/
		
		$data = CommonUtility::getDataToDropDown("{{merchant_type_translation}}",'type_id','type_name',
    	"where language=".q($lang)."","ORDER BY type_name ASC" 	
    	);
    	return $data;
	}
	
	public static function ListPlans($plant_type='membership')
	{
		/*$list = CommonUtility::getDataToDropDown("{{packages}}",'package_id','title',
		"","ORDER BY package_id ASC");
		return $list;*/
		$list = CommonUtility::getDataToDropDown("{{plans}}",'package_id','title',
		"WHERE plan_type=".q($plant_type)." ","ORDER BY package_id ASC");
		return $list;
	}
	
	public static function PaymentProvider()
	{
		$list = CommonUtility::getDataToDropDown("{{payment_gateway}}",'payment_code','payment_name',
		"WHERE status='active'","ORDER BY sequence ASC");
		return $list;
	}
	
	public static function PaymentPayoutProvider()
	{
		$model = AR_payment_gateway::model()->findAll("status=:status AND is_payout=:is_payout",array(
		  ':status'=>"active",
		  ':is_payout'=>1,
		));
		if($model){
			$data = array();
			foreach ($model as $val) {
				$logo_image = '';
		   	   if(!empty($val['logo_image'])){
		   	      $logo_image = CMedia::getImage($val['logo_image'],$val['path'],Yii::app()->params->size_image_thumbnail,
				  CommonUtility::getPlaceholderPhoto('item'));
		   	   }
				
		   	   $data[] = array(
		   	    'payment_name'=>$val['payment_name'],
		   	    'payment_code'=>$val['payment_code'],
		   	    'logo_type'=>$val['logo_type'],
		   	    'logo_class'=>$val['logo_class'],
		   	    'logo_image'=>$logo_image,
		   	  );
			}			
			return $data;
		}
		throw new Exception( 'no results' );
	}
	
	public static function PaymentPlansProvider()
	{
		$model = AR_payment_gateway::model()->findAll("status=:status AND is_plan=:is_plan",array(
		  ':status'=>"active",
		  ':is_plan'=>1,
		));
		if($model){
			$data = array();
			foreach ($model as $val) {
				$logo_image = '';
		   	   if(!empty($val['logo_image'])){
		   	      $logo_image = CMedia::getImage($val['logo_image'],$val['path'],Yii::app()->params->size_image_thumbnail,
				  CommonUtility::getPlaceholderPhoto('item'));
		   	   }
				
		   	   $data[] = array(
		   	    'payment_name'=>$val['payment_name'],
		   	    'payment_code'=>$val['payment_code'],
		   	    'logo_type'=>$val['logo_type'],
		   	    'logo_class'=>$val['logo_class'],
		   	    'logo_image'=>$logo_image,
		   	  );
			}			
			return $data;
		}
		throw new Exception( 'no available payment method' );
	}
	
	public static function PaymentProviderByMerchant($merchant_id='')
	{
		$data = array();
		$stmt="
		SELECT a.payment_id,a.payment_name
		FROM {{payment_gateway}} a	
		WHERE a.payment_code IN (
		  select meta_value from {{merchant_meta}}
		  where meta_name='payment_gateway'
		  and meta_value = a.payment_code
		  and merchant_id = ".q($merchant_id)."
		)	
		AND a.status='active'
		ORDER BY a.sequence ASC
		";		
		if( $res = CCacheData::queryAll($stmt,'merchant')){
		   $data = array();
		   foreach ($res as $val) {
		   	   $data[$val['payment_id']] = Yii::app()->input->xssClean($val['payment_name']);
		   }
		   return $data;
		} 
		return false;
	}

	public static function paymentProviderDetails($payment_code='')
	{
		$provider = AR_payment_gateway::model()->find("payment_code=:payment_code",array(
	      ':payment_code'=>$payment_code
	    ));
	    if($provider){
	    	return array(
	    	  'payment_code'=>$provider->payment_code,
	    	  'payment_name'=>$provider->payment_name,
	    	  'is_online'=>$provider->is_online,
	    	  'logo_type'=>$provider->logo_type,
	    	  'logo_class'=>$provider->logo_class,
	    	  'logo_image'=>$provider->logo_image,
	    	  'path'=>$provider->path,
	    	);
	    }
	    return false;
	}
	
	public static function MerchantList()
	{
		$list = CommonUtility::getDataToDropDown("{{merchant}}",'merchant_id','restaurant_name',
		"WHERE status='active'","ORDER BY restaurant_name ASC");
		return $list;
	}
	
	public static function StatusList()
	{
		$list = CommonUtility::getDataToDropDown("{{order_status}}",'description','description',
		"WHERE 1","ORDER BY description ASC");
		return $list;
	}
	
	public static function CurrencyList()
	{
		$list = CommonUtility::getDataToDropDown("{{currency}}",'currency_code','description',
		"WHERE is_hidden=0","ORDER BY currency_code ASC");
		return $list;
	}
	
	public static function defaultCurrency($all=false)
	{
		$model = AR_currency::model()->find("as_default=:as_default",array(
		  ':as_default'=>1
		));
		if($model){
			if($all){
				return array(
				  'currency_code'=>$model->currency_code,
				  'currency_symbol'=>$model->currency_symbol,
				  'description'=>$model->description,
				);
			} else return $model->currency_code;			
		}
		return false;
	}
	
	public static function getLanguage()
	{
		$list = CommonUtility::getDataToDropDown("{{language}}",'code','title',
		"WHERE status='publish' AND CODE NOT IN (".q(KMRS_DEFAULT_LANGUAGE).") ","ORDER BY sequence ASC");
		return $list;
	}
	
	public static function getLanguageAll()
	{
		$list = CommonUtility::getDataToDropDown("{{language}}",'code','title',
		"WHERE status='publish'","ORDER BY sequence ASC");
		return $list;
	}
	
	public static function SMSProvider()
	{
		$list = CommonUtility::getDataToDropDown("{{sms_provider}}",'provider_id','provider_name',
		"WHERE 1","ORDER BY provider_name ASC");
		return $list;
	}
		
	public static function Dish()
	{
		$list = CommonUtility::getDataToDropDown("{{dishes}}",'dish_id','dish_name',"
		WHERE status = 'publish'","ORDER BY dish_name ASC");
		return $list;
	}
	
	public static function Subcategory($merchant_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{subcategory}}",'subcat_id','subcategory_name',"
		WHERE status = 'publish' AND merchant_id=".q($merchant_id)." ",
		"ORDER BY subcategory_name ASC");
		return $list;
	}
	
	public static function Category($merchant_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{category}}",'cat_id','category_name',"
		WHERE status = 'publish' AND merchant_id=".q($merchant_id)."
		","ORDER BY category_name ASC");
		return $list;
	}
	
	public static function Size($merchant_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{size}}",'size_id','size_name',"
		WHERE status = 'publish' AND merchant_id=".q($merchant_id)."
		","ORDER BY size_name ASC");
		
		$none[''] = t("Select Unit");		
		$list = $none + $list;		
		return $list;
	}
	
	public static function Supplier($merchant_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{inventory_supplier}}",'supplier_id','supplier_name',"
		WHERE merchant_id=".q($merchant_id)."
		","ORDER BY supplier_name ASC");
		
		$none[''] = t("Select Supplier");		
		$list = $none + $list;		
		return $list;
	}
	
	public static function Cooking($merchant_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{cooking_ref}}",'cook_id','cooking_name',"
		WHERE merchant_id=".q($merchant_id)." AND status='publish'
		","ORDER BY cooking_name ASC");
				
		return $list;
	}
	
	public static function Ingredients($merchant_id='')
	{
		$list = CommonUtility::getDataToDropDown("{{ingredients}}",'ingredients_id','ingredients_name',"
		WHERE merchant_id=".q($merchant_id)." AND status='publish'
		","ORDER BY ingredients_name ASC");
				
		return $list;
	}
	
	public static function ItemSize($merchant_id='',$item_id='')
	{
		$list = array();
		$stmt="SELECT item_size_id,
		size_name,price
		FROM {{view_item_size}}
		WHERE
		merchant_id=".q($merchant_id)."
		AND item_id = ".q($item_id)."
		ORDER BY sequence ASC
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
				$list[ $val['item_size_id'] ] = t("[price] [size_name]",array(
				  '[price]'=>Price_Formatter::formatNumberNoSymbol($val['price']),
				  '[size_name]'=>Yii::app()->input->stripClean($val['size_name']),
				));
			}
		}
		return $list;
	}
	
	public static function CommissionType()
	{
		return array(
		  'fixed'=>t("Fixed"),
		  'percentage'=>t("percentage"),
		);
	}
	
	public static function InvoiceTerms()
    {
    	return array(
    	  1=>t("Daily"),
    	  7=>t("Weekly"),
    	  15=>t("Every 15 Days"),
    	  30=>t("Every 30 Days"),
    	);
    }
    
    public static function ExpirationType()
    {
    	return array(
    	 'days'=>t("Days"),
    	 'year'=>t("Year")
    	);
    }
    
    public static function ListlimitedPost()
    {
    	return array(
    	  2=>t("Unlimited"),
    	  1=>t("Limited")
    	);
    }
    
    public static function PlanPeriod()
    {
    	return array(
    	 'daily'=>t("Daily"),
    	 'weekly'=>t("Weekly"),
    	 'monthly'=>t("Monthly"),
    	 'anually'=>t("Anually")
    	);
    }
    
    public static function getDishes($dish_id=0)
	{
		$data = array();
		$stmt = "
		SELECT 
		a.dish_id,
		a.dish_name,
		a.photo,
		a.status,
		IFNULL(b.language,'default') as language,
		IFNULL(b.dish_name,'') as  dish_name_trans
		
		FROM {{dishes}} a		
		LEFT JOIN {{dishes_translation}} b
		ON
		a.dish_id = b.dish_id
		
		WHERE a.dish_id = ".q($dish_id)."
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {				
				$data[$val['language']] = $val['language']=="default"?$val['dish_name']:$val['dish_name_trans'];
			}
			return $data;
		}
		return false;
	}	    
	
	public static function timezoneList()
	{		
		$version=phpversion();				
		if ($version<=5.2){
			return array();
		}		
		$list[''] = t("Please Select");
		$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
		if (is_array($tzlist) && count($tzlist)>=1){
			foreach ($tzlist as $val) {
				$list[$val]=$val;
			}
		}			
		return $list;		
	}
	
	public static function DateFormat()
	{
		/*return array(
		  'dd MMM yyyy'=>Date_Formatter::date(date('c'),'dd MMM yyyy'),		  
		  'dd/MMM/yyyy'=>Date_Formatter::date(date('c'),'dd/MMM/yyyy'),		  
		  'yyyy MMM dd'=>Date_Formatter::date(date('c'),'yyyy MMM dd'),		  		  
		  'yyyy-MMM-dd'=>Date_Formatter::date(date('c'),'yyyy-MM-dd'),		  
		  'MMM dd yyyy'=>Date_Formatter::date(date('c'),'MMM dd yyyy'),		  
		  'MMM/dd/yyyy'=>Date_Formatter::date(date('c'),'MMM/dd/yyyy'),		  
		  'MMM-dd-yyyy'=>Date_Formatter::date(date('c'),'MMM-dd-yyyy'),		  
		);*/		
		return array(
		  'EEEE, MMMM d, y'=>Date_Formatter::date(date('c'),'EEEE, MMMM d, y',true),		  	
		  'EEE, MMMM d, y'=>Date_Formatter::date(date('c'),'EEE, MMMM d, y',true),	
		  'EEE, MMM d, y'=>Date_Formatter::date(date('c'),'EEE, MMM d, y',true),	
		  'MMMM EEEE d, y'=>Date_Formatter::date(date('c'),'MMMM EEEE d, y',true),		  	
		  'MMMM EEE d, y'=>Date_Formatter::date(date('c'),'MMMM EEE d, y',true),		  	
		  'MMM EEE d, y'=>Date_Formatter::date(date('c'),'MMM EEE d, y',true),		  	
		  
		  'MMM d, y'=>Date_Formatter::date(date('c'),'MMM d, y',true),
		  'M/d/yy'=>Date_Formatter::date(date('c'),'M/d/yy',true),
		  'dd/MMM/yyyy'=>Date_Formatter::date(date('c'),'dd/MMM/yyyy',true),	
		  'yyyy MMM dd'=>Date_Formatter::date(date('c'),'yyyy MMM dd',true),		  
		);
	}
	
	public static function TimeFormat()
	{
		/*return array(
		  'h:mm a'=>Date_Formatter::date(date('c'),'h:mm a'),		  
		  'h:mm'=>Date_Formatter::date(date('c'),'h:mm'),		  
		  'hh:mm:ss a'=>Date_Formatter::date(date('c'),'hh:mm:ss a'),		  
		  'hh:mm:ss'=>Date_Formatter::date(date('c'),'hh:mm:ss'),		  
		  'HH:mm:ss'=>Date_Formatter::date(date('c'),'HH:mm:ss'),		  
		  'HH:mm'=>Date_Formatter::date(date('c'),'HH:mm'),	
		);*/
		/*return array(
		  'h:mm:ss a'=>Date_Formatter::Time(date('c'),'h:mm:ss a'),
		  'h:mm a'=>Date_Formatter::Time(date('c'),'h:mm a'),
		  'h:mm:ss a zzzz'=>Date_Formatter::Time(date('c'),'h:mm:ss a zzzz'),
		  'h:mm:ss a z'=>Date_Formatter::Time(date('c'),'h:mm:ss a z'),		  
		);*/
		return array(
		  'h:mm:ss a'=>'h:mm:ss a',
		  'h:mm a'=>'h:mm a',
		  'h:mm:ss a zzzz'=>'h:mm:ss a zzzz',
		  'h:mm:ss a z'=>'h:mm:ss a z',
		  'H:m'=>'H:m',
		  'H:m:s'=>'H:m:s',
		  'HH:mm'=>'HH:mm',
		  'HH:mm:ss'=>'HH:mm:ss',
		);
	}
	
	public static function CountryList($key='shortcode')
	{
		$list = CommonUtility::getDataToDropDown("{{location_countries}}",$key,'country_name',
		"WHERE 1","ORDER BY country_name ASC");
		return $list;
	}
	
	public static function CurrencyPosition()
	{
	   return array(
	     'left'=>t("Left $11"),
	     'right'=>t("Right 11$"),
	     'left_space'=>t("Left with space $ 11"),
	     'right_space'=>t("Right with space 11 $")
	   );
	}
	
	public static function MenuStyle()
	{
		return array( 
		  1=>t("Menu 1"),
		  2=>t("Menu 2"),
		  3=>t("Menu 3"),
		);
	}
	
	public static function LocationSearchType()
	{		
		return array(
		  1=>t("City / Area"),
		  2=>t("State / City"),
		  3=>t("PostalCode/ZipCode"),	
		);
	}
	
	public static function currencyListSelection()
	{
		$data = array();
		$data['']=t("Please select");
		$stmt="
		SELECT currency_name,symbol,code
		FROM {{multicurrency_list}}
		ORDER BY code ASC		
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
				$string = '[code] - [name]';
				if(!empty($val['symbol'])){
					$string = '[code] - [name] ([symbol])';
				}
				$data[$val['code']]= t($string,array(
				  '[code]'=>$val['code'],
				  '[name]'=>$val['currency_name'],
				  '[symbol]'=>$val['symbol'],
				));
			}			
		}
		return $data;
	}
	
	public static function CurrencyDetails($code='')
	{
		$stmt="
		SELECT currency_name,symbol,code FROM {{multicurrency_list}}
		WHERE code=".q($code)."
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryRow()){
			return $res;
		}
		return false;
	}
	
	public static function couponType()
    {
    	return array(
    	  'fixed amount'=>t("fixed amount"),
    	  'percentage'=>t("percentage")
    	);
    }
    
    public static function couponOoptions()
    {
    	 return array(
		    1=>t("Unlimited for all user"),
		    2=>t("Use only once"),
		    3=>t("Once per user"),
		    4=>t("Once for new user first order"),   
		    5=>t("Custom limit per user"),
		    6=>t("Only to selected customer")
		  );
    }
    
    public static function dayList()
    {
    	return array(
    	  'monday'=>t("monday"),
    	  'tuesday'=>t("tuesday"),
    	  'wednesday'=>t("wednesday"),
    	  'thursday'=>t("thursday"),
    	  'friday'=>t("friday"),
    	  'saturday'=>t("saturday"),
    	  'sunday'=>t("sunday")
    	);
    }
    
    public static function dayWeekList()
    {
    	return array(
    	  1=>t("monday"),
    	  2=>t("tuesday"),
    	  3=>t("wednesday"),
    	  4=>t("thursday"),
    	  5=>t("friday"),
    	  6=>t("saturday"),
    	  7=>t("sunday")
    	);
    }
    
    public static function pagesTranslation($page_id=0)
	{
		$data = array();
		$stmt = "
		SELECT 
		a.page_id,
		a.title,
		a.long_content,		
		IFNULL(b.language,'default') as language,
		IFNULL(b.title,'') as  title_trans,
		IFNULL(b.long_content,'') as  long_content_trans,
		IFNULL(b.meta_title,'') as  meta_title_trans,
		IFNULL(b.meta_description,'') as  meta_description_trans,
		IFNULL(b.meta_keywords,'') as  meta_keywords_trans
		
		FROM {{pages}} a		
		LEFT JOIN {{pages_translation}} b
		ON
		a.page_id = b.page_id
		
		WHERE a.page_id = ".q($page_id)."
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {								
				$data['title'][$val['language']] = $val['language']=="default"?$val['title']:$val['title_trans'];
				$data['long_content'][$val['language']] = $val['language']=="default"?$val['long_content']:$val['long_content_trans'];
			}
			return $data;	
		}
		return false;
	}	        
	
    public static function smsPackageTranslation($sms_package_id=0)
	{
		$data = array();
		$stmt = "
		SELECT 
		a.sms_package_id,
		a.title,
		a.description,		
		IFNULL(b.language,'default') as language,
		IFNULL(b.title,'') as  title_trans,
		IFNULL(b.description,'') as  description_trans		
		
		FROM {{sms_package}} a		
		LEFT JOIN {{sms_package_translation}} b
		ON
		a.sms_package_id = b.sms_package_id
		
		WHERE a.sms_package_id = ".q($sms_package_id)."
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {								
				$data['title'][$val['language']] = $val['language']=="default"?$val['title']:$val['title_trans'];
				$data['description'][$val['language']] = $val['language']=="default"?$val['description']:$val['description_trans'];
			}
			return $data;	
		}
		return false;
	}	        	
	
	public static function SecureConnection()
	{
		return array(
		  'tls'=>t("TLS"),
		  'ssl'=>t("SSL"),
		);
	}
	
	public static function ContactFields()
	{
		return array(
		  'name'=>t("Name"),
		  'email'=>t("Email Address"),
		  'phone'=>t("Phone"),
		  'country'=>t("Country"),
		  'message'=>t("Message"),
		);
	}
	
    public static function GetFromTranslation($id=0, $table1='',$table2='',$primary='',$fields1=array(), $fields2=array())
	{
		$data = array();
		$stmt_field1=''; $stmt_field2='';

		foreach ($fields1 as $fields1_val) {			
			$stmt_field1.="a.$fields1_val,\n";
		}
		
		foreach ($fields2 as $key=>$fields2_val) {
			$stmt_field2.="IFNULL(b.$key, a.$key ) as  $fields2_val,\n";
		}
		
		$stmt_field1 = substr($stmt_field1,0,-1);
		$stmt_field2 = substr($stmt_field2,0,-2);
			
		$stmt = "
		SELECT 
		$stmt_field1
		
		IFNULL(b.language,'default') as language,
		$stmt_field2
		
		FROM $table1 a		
		LEFT JOIN $table2 b
		ON
		a.$primary = b.$primary
		
		WHERE a.$primary = ".q($id)."
		";											
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			foreach ($res as $val) {		
				foreach ($fields2 as $fields2_key=>$fields2_val) {				   
				   //$data[$fields2_key][$val['language']] = $val['language']=="default"?$val[$fields2_key]:$val[$fields2_val];
				   if(isset($val[$fields2_key])){
				      $data[$fields2_key][$val['language']] = $val['language']=="default"?$val[$fields2_key]:$val[$fields2_val];
				   }
				}
			}					
			return $data;	
		}
		return false;
	}	        	

	public static function getLocaleLanguages()
	{
		$locale = Yii::app()->localeDataPath."/en.php";
		if(file_exists($locale)){
			$localy = require $locale;
			return $localy['languages'];
		}
		return false;
	}
	
	public static function foodOptionsListing()
	{		
		return array(
		  0=>t("Please select..."),
		  1=>t("Hide"),
		  2=>t("Disabled"),		  
		);
	}
	
	public static function twoFlavorOptions()
	{		
		return array(
		  0=>t("Please select..."),
		  1=>t("Highest price"),
		  2=>t("Sumup and divided by 2"),		  
		);
	}
	
	public static function Tips()
	{						
		return CommonUtility::getDataToDropDown("{{admin_meta}}",'meta_value','meta_value',"
		WHERE meta_name='tips'
		","ORDER BY meta_value ASC");
	}
	
    public static function transportType()
	{
		return array(		  
		  'truck'=>t("Truck"),
		  'car'=>t("Car"),
		  'bike'=>t("Bike"),
		  'bicycle'=>t("Bicycle"),
		  'scooter'=>t("Scooter"),
		  'walk'=>t("Walk"),
		);
	}	
	
	public static function MultiOption()
	{
		return array(
		  'one'=>t("Can Select Only One"),
		  'multiple'=>t("Can Select Multiple"),
		  'two_flavor'=>t("Two Flavors"),
		  'custom'=>t("Custom"),		  
		);
	}
	
	public static function TwoFlavor()
	{
		return array(
		  'left'=>t("left"),
		  'right'=>t("Right"),
		);
	}
	
	public static function ItemFeatured()
	{
		return array(
		  'new'=>t("New Items"),
		  'trending'=>t("Trending"),		  
		  'best_seller'=>t("Best Seller"),
		  'recommended'=>t("Recommended"),
		);
	}
	
	public static function MerchantFeatured()
	{
		return array(
		  'new'=>t("New Restaurant"),
		  'popular'=>t("Popular"),		  
		  'best_seller'=>t("Best Seller"),
		  'recommended'=>t("Recommended"),
		);
	}
	
	public static function DeliveryChargeType()
	{
		return array(
		  'fixed'=>t("Fixed Charge"),
		  'dynamic'=>t("Dynamic Rates"),
		);
	}
	
	public static function ShippingType()
	{
		return array(
		  'standard'=>t("Standard"),
		  'priority'=>t("Priority"),
		  'no_rush'=>t("No rush"),
		);
	}
	
	public static function metaMedia()
	{
		return 'merchant_gallery';
	}
	
	public static function metaReview()
	{
		return 'review';
	}
	
	public static function metaProfile()
	{
		return 'profile_photo';
	}
	
	public static function SMSBroadcastType()
	{
		return array(
		  1=>t("Send to All Subscriber"),
		  2=>t("Send to Customer Who already buy your products"),
		  3=>t("Send to specific mobile numbers")
		);
	}
	
	public static function ItemPromoType()
	{
		return array(
		  'buy_one_get_free'=>t("Buy (qty) to get the (qty) item free"),
		  'buy_one_get_discount'=>t("Buy (qty) and get 1 at (percen)% off"),
		);
	}
	
	public static function SortMerchant()
	{
		return array(
		  'sort_most_popular'=>t("Most popular"),
		  'sort_rating'=>t("Rating"),		  
		  'sort_promo'=>t("Promo"),
		  'sort_free_delivery'=>t("Free delivery first order"),
		);
	}
	
	public static function SortPrinceRange()
	{
		return array(
		  1=>t("$"),
		  2=>t("$$"),		  
		  3=>t("$$$"),
		  4=>t("$$$$"),
		);
	}
		
	public static function countryMobilePrefix()
	{
		$stmt="
		SELECT shortcode,phonecode
		FROM {{location_countries}}
		ORDER BY shortcode ASC
		";
		
		if(Yii::app()->params->db_cache_enabled){			
			$dependency = new CDbCacheDependency("SELECT count(*) FROM {{location_countries}}");
			$res = Yii::app()->db->cache(Yii::app()->params->cache, $dependency)->createCommand($stmt)->queryAll();		  
		} else $res = Yii::app()->db->createCommand($stmt)->queryAll();
		
		if($res){
			foreach ($res as $val) {						
				$data[] = array(
				 'name'=>t("+[phonecode] ([shortcode])",array(
					 '[phonecode]'=>$val['phonecode'],
					 '[shortcode]'=>$val['shortcode'],
					)),
				  'value'=>$val['phonecode']
				);
			}
			return $data;
		}		
		return false;
	}

	public static function countryMobilePrefixWithFilter($countrycode_list=array())
	{		
		$criteria=new CDbCriteria();			
		if(is_array($countrycode_list) && count($countrycode_list)>=1){
			$criteria->addInCondition('shortcode', (array) $countrycode_list );		
		}		
		$criteria->order="shortcode ASC";
		
		$model = AR_location_countries::model()->findAll($criteria); 
		if($model){
			foreach ($model as $item) {
				$data[] = array(
					'label'=>t("+[phonecode] ([shortcode])",array(
						'[phonecode]'=>$item->phonecode,
						'[shortcode]'=>$item->shortcode
					   )),
					 'value'=>$item->phonecode
				   );
			}
			return $data;
		}		
		return false;
	}

	public static function getMobileByShortCode($shortcode='')
	{
		$default_prefix_array = array();
		$dependency = CCacheData::dependency();			
		$model = AR_location_countries::model()->cache( Yii::app()->params->cache , $dependency  )->find("shortcode=:shortcode",array(
			':shortcode'=>$shortcode
		));
		if($model){
			$default_prefix_array = [
				'label'=>t("+[phonecode] ([shortcode])",array(
					'[phonecode]'=>$model->phonecode,
					'[shortcode]'=>$model->shortcode
					)),
					'value'=>$model->phonecode
			];			
		}	
		return $default_prefix_array;					
	}

	public static function getMobileByPhoneCode($phonecode='')
	{
		$default_prefix_array = array();
		$dependency = CCacheData::dependency();			
		$model = AR_location_countries::model()->cache( Yii::app()->params->cache , $dependency  )->find("phonecode=:phonecode",array(
			':phonecode'=>$phonecode
		));
		if($model){
			$default_prefix_array = [
				'label'=>t("+[phonecode] ([shortcode])",array(
					'[phonecode]'=>$model->phonecode,
					'[shortcode]'=>$model->shortcode
					)),
					'value'=>$model->phonecode
			];			
		}	
		return $default_prefix_array;					
	}
	
	public static function getOrderStatusList($lang=KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.stats_id,
		b.description 
		FROM {{order_status}} a
		LEFT JOIN {{order_status_translation}} b
		ON 
		a.stats_id = b.stats_id
		WHERE b.language=".q($lang)."	
		";	
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){					
			return $res;			
		}
		return false;
	}
	
	public static function getOrderStatus($lang=KMRS_DEFAULT_LANGUAGE)
	{
		$stmt="
		SELECT a.stats_id,a.description as status,
		b.description 
		FROM {{order_status}} a
		LEFT JOIN {{order_status_translation}} b
		ON 
		a.stats_id = b.stats_id
		WHERE b.language=".q($lang)."	
		";	
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){					
			$data = array();
			foreach ($res as $val) {
				$data[$val['status']] = $val['description'];
			}
			return $data;
		}
		return false;
	}
	
	public static function formatAsSelect2($data=array())
	{
		$results = array();
		if(is_array($data) && count($data)>=1){			
			foreach ($data as $items) {				
				$results[] = array(
				 'id'=>intval($items['stats_id']),
				 'text'=>$items['description']
				);
			}			
		}
		return $results;
	}
	
	public static function delayedMinutes()
	{
		$time = 5; $times = array();
		for ($x = 1; $x <= 6; $x++) {
		   $times[]= array(
		     'id'=>($time*$x),
		     'value'=>t("{{mins}} min(s)",array('{{mins}}'=>($time*$x)))
		   );
		} 
		return $times;
	}
	
	public static function statusManagementTranslationList($group_name='' , $lang = KMRS_DEFAULT_LANGUAGE )
	{
		$criteria=new CDbCriteria();
		$criteria->alias = "a";			
		$criteria->select = "a.status,b.title";
		$criteria->join='LEFT JOIN {{status_management_translation}} b on  a.status_id=b.status_id ';
		$criteria->condition = "a.group_name=:group_name AND language=:language ";
		$criteria->params = array(
		  ':group_name'=>$group_name,
		  ':language'=>$lang
		);
		$model=AR_status_management::model()->findAll($criteria);
		if($model){
			$data = array();
			foreach ($model as $item) {
				$data[$item->status] = $item->title;
			}
			return $data;
		}
		return false;
	}
	
	public static function orderSortList()
	{
		/*return array(
		  'order_id_asc'=>t("Order ID - Ascending"),
		  'order_id_desc'=>t("Order ID - Descending"),
		  'delivery_time_asc'=>t("Delivery Time - Ascending"),
		  'delivery_time_desc'=>t("Delivery Time - Descending"),
		);*/
		return array(
		  'order_id_asc'=>array(		    
		    'text'=>t("Order ID - Ascending"),
		    'icon'=>'fas fa-sort-alpha-down',
		  ),
		  'order_id_desc'=>array(
		   'text'=>t("Order ID - Descending"),
		   'icon'=>'fas fa-sort-alpha-up',
		  ),
		  'delivery_time_asc'=>array(
		    'text'=>t("Delivery Time - Ascending"),
		    'icon'=>'fas fa-sort-alpha-down',
		  ),
		  'delivery_time_desc'=>array(
		    'text'=>t("Delivery Time - Descending"),
		    'icon'=>'fas fa-sort-alpha-up',
		  ),
		);
	}
	
	public static function pushInterestList()
	{
		return array(  
		   'order_update'=>t("Order updates"),
		   'customer_new_signup'=>t("Customer new signup"),
		   'merchant_new_signup'=>t("Merchant new signup"),
		   'payout_request'=>t("Payout request"),		   
		);
	}
	
	public static function pushInterest()
	{
		return array(  
		   'order_update'=>'order_update',
		   'customer_new_signup'=>'customer_new_signup',
		   'merchant_new_signup'=>'merchant_new_signup',
		   'payout_request'=>'payout_request',		   
		);
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
	
	public static function getSetSpecificCountry()
	{
	    $country = Yii::app()->params['settings']['merchant_specific_country'];
	    $country = !empty($country)?json_decode($country,true):false;
	    $country_params = '';
	    if(is_array($country) && count($country)>=1){
	   	   foreach ($country as $key=> $item) {		   	  	 
	   	   	  if($key<=0){
	   	  	  	 $country_params.="$item|";
	   	  	  } else $country_params.="country:$item|";		   	  	 
	   	  }
	   	   $country_params = substr($country_params,0,-1);	   	   
	   }
	   return $country_params;		   		  
	}
	
	public static function getSetSpecificCountryArray()
	{
	    $country = Yii::app()->params['settings']['merchant_specific_country'];
	    $country = !empty($country)?json_decode($country,true):false;
	    $country_params = array();
	    if(is_array($country) && count($country)>=1){
	   	   $country_params = $country;
	   }
	   return $country_params;		   		  
	}
	
	public static function dashboardOrdersTab()
	{
		return array(
		  'all'=>t("All"),
		  'order_processing'=>t("Processing"),
		  'order_ready'=>t("Ready"),
		  'completed_today'=>t("Completed"),		  
		);
	}	
	
	public static function dashboardItemTab()
	{
		return array(
		  'item_overview'=>array(
		    'title'=>t("Popular items"),
		    'sub_title'=>t("latest popular items"),
		  ),		  
		  'sales_overview'=>array(
		    'title'=>t("Last 30 days sales"),
		    'sub_title'=>t("sales for last 30 days"),
		  ),		  		  
		);
	}
		
	public static function dashboardPopularMerchantTab()
	{
		return array(
		  'popular'=>array(
		    'title'=>t("Popular merchants"),
		    'sub_title'=>t("best selling restaurant"),
		  ),		  
		  'review'=>array(
		    'title'=>t("Popular by review"),
		    'sub_title'=>t("most reviewed"),
		  ),		  		  
		);
	}
	
	public static function sizeList($merchant_id=0, $lang='')
	{
		$data = CommonUtility::getDataToDropDown("{{size_translation}}",'size_id','size_name',
    	"where language=".q($lang)." 
    	and size_name IS NOT NULL AND TRIM(size_name) <> ''
    	and size_id IN (
    	  select size_id from {{size}}
    	  where merchant_id = ".q(intval($merchant_id))."
    	)
    	"
    	);
    	return $data;
	}
	
	public static function itemNameList($merchant_id=0, $lang='')
	{
		$data = CommonUtility::getDataToDropDown("{{item_translation}}",'item_id','item_name',
    	"
    	where language=".q(Yii::app()->language)." 
    	and item_id IN (
    	 select item_id from {{item}}
    	 where merchant_id=".q(intval($merchant_id))."
    	 and item_name IS NOT NULL AND TRIM(item_name) <> ''
    	)
    	"
    	);    	
    	return $data;
	}
	
	public static function cuisineGroup($lang='')
	{
		$data = array();
		$criteria=new CDbCriteria();
		$criteria->alias ="a";
		$criteria->select = "
		a.merchant_id, 
		(
		 select GROUP_CONCAT(cuisine_name)
		 from {{cuisine_translation}}
		 where language=".q($lang)."		 		 
		 and cuisine_name IS NOT NULL AND TRIM(cuisine_name) <> ''
		 and cuisine_id in (
		   select cuisine_id from {{cuisine_merchant}}
		   where merchant_id = a.merchant_id
		 )		 
		) as cuisine_group
		";
		$criteria->condition = "a.status=:status";
		$criteria->params = array(':status'=>'active');
		
		if($model = AR_merchant::model()->findAll($criteria)){
			foreach ($model as $item) {								
				if(!empty($item->cuisine_group)){
					$cuisine_group = explode(",",$item->cuisine_group);
					$data[$item->merchant_id] = $cuisine_group;
				}				
			}
			return $data;
		}
		throw new Exception( "No cuisine" );
	}
	
	public static function priceFormat()
	{
		return array(
		   'symbol'=>Price_Formatter::$number_format['currency_symbol'],
            'decimals'=>Price_Formatter::$number_format['decimals'],
            'decimal_separator'=>Price_Formatter::$number_format['decimal_separator'],
            'thousand_separator'=>Price_Formatter::$number_format['thousand_separator'],
            'position'=>Price_Formatter::$number_format['position'],
		);
	}
	
	public static function CategoryResponsiveSettings($size="full")
	{
		$responsive_data = array();
		if($size=="half"){
			$responsive_data[0] = array('items'=>1,'nav'=>true,'loop'=>false);
			$responsive_data[320] = array('items'=>3,'nav'=>true,'loop'=>false);
			$responsive_data[480] = array('items'=>4,'nav'=>true,'loop'=>false);
			$responsive_data[600] = array('items'=>5,'nav'=>true,'loop'=>false);
			$responsive_data[1000] = array('items'=>5,'nav'=>true,'loop'=>false);
			$responsive_data[1200] = array('items'=>5,'nav'=>true,'loop'=>false);		
		} elseif ( $size=="full" ){
			$responsive_data[0] = array('items'=>1,'nav'=>true,'loop'=>false);
			$responsive_data[320] = array('items'=>3,'nav'=>true,'loop'=>false);
			$responsive_data[480] = array('items'=>4,'nav'=>true,'loop'=>false);
			$responsive_data[600] = array('items'=>3,'nav'=>true,'loop'=>false);
			$responsive_data[1000] = array('items'=>8,'nav'=>true,'loop'=>true);
			$responsive_data[1200] = array('items'=>11,'nav'=>true,'loop'=>false);		
		}
		return $responsive_data;
	}

	public static function FrontCarouselResponsiveSettings($size="full")
	{
		$responsive_data = array();
		if($size=="half"){
			$responsive_data[0] = array('items'=>1,'nav'=>true,'loop'=>false);
			$responsive_data[320] = array('items'=>2,'nav'=>true,'loop'=>false);
			$responsive_data[480] = array('items'=>3,'nav'=>true,'loop'=>false);
			$responsive_data[600] = array('items'=>4,'nav'=>false,'loop'=>false);
			$responsive_data[1000] = array('items'=>5,'nav'=>false,'loop'=>false);			
		} elseif ( $size=="full" ){			
			$responsive_data[0] = array('items'=>1,'nav'=>false,'loop'=>false);
			$responsive_data[320] = array('items'=>2,'nav'=>false,'loop'=>false);
			$responsive_data[480] = array('items'=>3,'nav'=>false,'loop'=>false);
			$responsive_data[600] = array('items'=>4,'nav'=>false,'loop'=>true);
			$responsive_data[1000] = array('items'=>5,'nav'=>false,'loop'=>true);		
		}
		return $responsive_data;
	}
	
	public static function MoneyConfig()
	{	
		$prefix = ''; $suffix='';	
		$settings = Price_Formatter::$number_format;		
				
		if($settings['position']=="right"){
			$suffix=$settings['currency_symbol'];
		} else $prefix = $settings['currency_symbol'];
		
		$data = array(
		  'prefix'=>$prefix,
		  'suffix'=>$suffix,
		  'thousands'=>!empty($settings['thousand_separator'])?$settings['thousand_separator']:",",
		  'decimal'=>$settings['decimal_separator'],
		  'precision'=>intval($settings['decimals']),
		);
		return json_encode($data);
	}
	
	public static function CashinAmount()
	{
		return array(
		  10=>Price_Formatter::formatNumber(10),
		  20=>Price_Formatter::formatNumber(20),
		  30=>Price_Formatter::formatNumber(30),
		);
	}
	
	public static function CashinMinimumAmount()
	{
		return 10;
	}
	
	public static function translationVendor()
	{
		return array(
		  'the_results_could_loaded'=>t("The results could not be loaded."),
		  'no_results'=>t("No results"),
		  'searching'=>t("Searching..."),
		  'the_results_could_not_found'=>t("The results could not be loaded."),
		  'loading_more_results'=>t("Loading more results"),
		  'remove_all_items'=>t("Remove all items"),
		  'remove_item'=>t("Remove item"),
		  'search'=>t("Search"),
		  'today'=>t("Today"),
		  'Yesterday'=>t("Yesterday"),
		  'last_7_days'=>t("Last 7 Days"),
		  'last_30_days'=>t("Last 30 Days"),
		  'this_month'=>t("This Month"),
		  'last_month'=>t("Last Month"),
		  'custom_range'=>t("Custom Range"),
		  'su'=>t("Su"),
		  'mo'=>t("Mo"),
		  'tu'=>t("Tu"),
		  'we'=>t("We"),
		  'th'=>t("Th"),
		  'fr'=>t("Fr"),
		  'sa'=>t("Sa"),
		  'january'=>t("January"),
		  'february'=>t("February"),
		  'march'=>t("March"),
		  'april'=>t("April"),
		  'may'=>t("May"),
		  'june'=>t("June"),
		  'july'=>t("July"),
		  'august'=>t("August"),
		  'september'=>t("September"),
		  'october'=>t("October"),
		  'november'=>t("November"),
		  'december'=>t("December"),
		);
	}
	
	public static function suggestionTabs()
	{
		return array(
           //'all'=>t("All"),
		   'restaurant'=>t("Restaurant"),
		   'food'=>t("Food"),
		);		
	}

	public static function BannerType()
	{
		return array(
		   'food'=>t("Food"),		   
		);
	}

}
/*end class*/