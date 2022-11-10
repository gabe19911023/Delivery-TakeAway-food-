<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

define('IS_FRONTEND',true);

$backend = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.BACKOFFICE_FOLDER.DIRECTORY_SEPARATOR."protected";
$backend_webroot = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.BACKOFFICE_FOLDER.DIRECTORY_SEPARATOR;
$upload_dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'upload';
$home_dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR;
Yii::setPathOfAlias('backend',$backend);
Yii::setPathOfAlias('backend_webroot',$backend_webroot);

$modules_dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'protected/modules';

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'GeaMenu',
	
	'aliases' => array(
       'upload_dir' => $upload_dir,   
       'modules_dir'=> $modules_dir,
       'home_dir' => $home_dir,
    ),

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',		
		'application.controllers.*',		
		'application.vendor.*',
		'application.extensions.*',
		'application.extensions.EHttpClient.*',
		'backend.components.*',		
		'backend.vendor.*',
		'backend.models.*',
		'ext.YiiMailer.YiiMailer'
	),
	
	'modules'=>array(
        'cod'=>array(),
        'ocr'=>array(),
        'paypal'=>array(),
        'stripe'=>array(),
        'razorpay'=>array(),
        'mercadopago'=>array(),
		'bank'=>array(),
		'jad'=>array(),
		'voguepay'=>array(),
		'paymongo'=>array(),
		'flutterwave'=>array(),
		'paystack'=>array(),
		'scanpay'=>array(),
		'billplz'=>array(),
		'toyyibpay'=>array(),
		'sofort'=>array(),
		'sofort_ideal'=>array(),
		'payhere'=>array(),
		'iyzipay'=>array(),
		'qmoney'=>array(),
		'braintree'=>array(),
		'payumoney'=>array(),
		'stripe_connect'=>array(),
    ),

	'defaultController'=>'store',
	
	'theme'=>'karenderia_v2',

	'language'=>KMRS_DEFAULT_LANGUAGE,
	
	'sourceLanguage'=>"en_us",
	
	'timezone'=>"Europe/Rome",

	// application components
	'components'=>array(
		
	   'cache'=>array( 
		    'class'=>'system.caching.CDbCache'
		),
		
		 // use language file in database
	    'messages'=>array(
	      'class'=>'CDbMessageSource',
	      'cacheID'=>'cache',
	      'cachingDuration'=>1,
	      'sourceMessageTable'=>'{{sourcemessage}}',
	      'translatedMessageTable'=>'{{message}}'
	    ),
		
	    'request'=>array(
	        'class'=>'HttpRequest',
            'enableCsrfValidation'=>true,
            'enableCookieValidation'=>true,
            'noCsrfValidationRoutes'=>array(
                'stripe/webhooks',
				'app/*',
				'partnerapi/*',
				'pv1/*',
				'interface/*'
             ),
        ),
	   
		'user'=>array(			
			'allowAutoLogin'=>true,			
			'class'=>"WebUserCustomer",
			'loginUrl'=>array('/account/login'),
		),

		'merchant'=>array(			
			'allowAutoLogin'=>true,			
			'class'=>"WebUserMerchant",
			'loginUrl'=>array('/auth/login'),
		),	
		
		'db'=>array(
			'connectionString' => 'mysql:host='.DB_HOST.';dbname='.DB_NAME,
			'emulatePrepare' => true,
			'username' => DB_USER,
			'password' => DB_PASSWORD,
			'charset' => DB_CHARSET,
			'tablePrefix' => DB_PREFIX,
			'schemaCachingDuration'=>60
		),		
		'errorHandler'=>array(			
			'errorAction'=>'store/pagenotfound',
		),
		'urlManager'=>array(
			'urlFormat'=>'path',			
			'showScriptName'=>false,
			'caseSensitive'=>false,
			'rules'=>array(
			    array(
			        'class' => 'application.components.CustomUrlRule',
			        'connectionID' => 'db',
			    ),
			    //'<action:\w+>'=>"store/<action>",
			    ''=>'store/index',
			    'account/notifications-list'=>"account/notificationslist",
			    'merchant/user-signup'=>"merchant/usersignup",
			    'merchant/payment-processing'=>"merchant/paymentprocessing",
			    'merchant/signup-failed'=>"merchant/signupfailed",
			    'merchant/cashin-successful'=>"merchant/cashin_successful",
			    '<action:(restaurants|offers|pagenotfound|feed)>' => 'store/<action>',
			    '<controller:\w+>/<action:\w+>/id/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',				 
			),
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
		'input'=>array(
		   'class'=>'CmsInput',
		   'cleanPost'=>true,
		   'cleanGet'=>true
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);