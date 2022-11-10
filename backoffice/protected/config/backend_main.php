<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

define('IS_FRONTEND',false);

$frontend = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'protected';
$frontend_base = dirname(dirname(dirname(dirname(__FILE__))));
$upload_dir = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'upload';
$home_dir = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR;
Yii::setPathOfAlias('frontend',$frontend);

define('HOME_FOLDER', basename($frontend_base) );

$modules_dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'protected/modules';

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Back Office',

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
		'application.migrations.*',
		'application.controllers.*',		
		'application.vendor.*',
		'application.extensions.*',
		'application.extensions.EHttpClient.*',
		'frontend.models.*',
		'frontend.components.*',
	),
	
	'modules'=>array(
        'cod'=>array(),
        'ocr'=>array(),
        'paypal'=>array(),
        'stripe'=>array(),
        'razorpay'=>array(),
        'mercadopago'=>array(),
    ),

	'defaultController'=>'admin',
	
	'theme'=>'classic',
	
	'language'=>KMRS_DEFAULT_LANGUAGE,
	
	'sourceLanguage'=>"en_us",
	
	'timezone'=>"Asia/Manila",

	// application components
	'components'=>array(
	
	    // use language file in database
	    'messages'=>array(
	      'class'=>'CDbMessageSource',
	      'cacheID'=>'cache',
	      'cachingDuration'=>1,
	      'sourceMessageTable'=>'{{sourcemessage}}',
	      'translatedMessageTable'=>'{{message}}'
	    ),
	    	    
	    'request'=>array(
            'enableCsrfValidation'=>false,
            'enableCookieValidation'=>false
        ),
	    
		'user'=>array(			
			'allowAutoLogin'=>true,			
			'class'=>"WebUser",
			'loginUrl'=>array('/login'),
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
			'schemaCachingDuration'=>100,
			/*'attributes'=>array(
			  PDO::MYSQL_ATTR_LOCAL_INFILE
			)*/
		),		
		'errorHandler'=>array(			
			'errorAction'=>'admin/error',
		),
		'urlManager'=>array(			
			'urlFormat'=>'path',			
			'showScriptName'=>false,
			'caseSensitive'=>false,
			'rules'=>array(												
			    'login'=>"login/index",			    
			    'admin'=>"admin/index",
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
		
		'reCaptcha' => array(
		   'name' => 'reCaptcha',
		   'class' => 'ext.yiiReCaptcha.ReCaptcha',
		   'key'=>'KEY',
		   'secret'=>'SECRET'		   
		)
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);