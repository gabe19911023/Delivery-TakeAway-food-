<?php
/*
  Author name : basti
  Website : http://bastisapp.com
  Item buy link : https://codecanyon.net/item/karenderia-multiple-restaurant-system/9118694
*/

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
require_once dirname(__FILE__).'/../k-config.php';
$config=dirname(__FILE__).'/protected/config/backend_main.php';

// remove the following line when in production mode
 defined('YII_DEBUG') or define('YII_DEBUG',true); 

require_once($yii);
Yii::createWebApplication($config)->run();
