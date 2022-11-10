<?php
class AssetsBundle
{	
	public static function registerBundle($bundle=array())
	{
		$cs = Yii::app()->clientScript;
		$cs->packages = array(
            'core' => array(                
                'baseUrl' => Yii::app()->baseUrl ,
                'js' => array(
                  'assets/vendor/jquery-3.6.0.min.js',
                  'assets/vendor/popper.min.js',
                  'assets/vendor/bootstrap/js/bootstrap.min.js'
                ),
                'css' => array(
                   'assets/vendor/bootstrap/css/bootstrap.min.css',
                   'assets/vendor/bootstrap/css/floating-labels.css',
                   'assets/vendor/material-design-iconic-font/css/material-design-iconic-font.min.css',
                ),
            ),
            'google-font'=>array(
			    'baseUrl'=>'/',
			    'css'=>array(
			      "/fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,300&display=swap",
			      "/fonts.googleapis.com/css2?family=Petrona:ital,wght@0,100;0,200;0,400;0,500;1,100;1,200&display=swap",			      
			    ),
			    'js'=>array(			      
			    )
			),			
			'login-css'=>array(
			   'baseUrl' => Yii::app()->theme->baseUrl,
			   'css'=>array(
			      "assets/css/login.css?time=".time(),
			   ),
			   'depends'=>array('core','google-font')
			),			
			'install-css'=>array(
			   'baseUrl' => Yii::app()->baseUrl,
			   'css'=>array(
			      "assets/css/install.css?time=".time(),
			   ),
			   'depends'=>array('core','google-font')
			),			
			'backend-css'=>array(
			   'baseUrl' => Yii::app()->theme->baseUrl,
			   'css'=>array(
			      "assets/css/style.css?time=".time(),
			      "assets/css/responsive.css?time=".time(),
			   ),			   
			),
			'responsive-css'=>array(
			   'baseUrl' => Yii::app()->theme->baseUrl,
			   'css'=>array(			      
			      "assets/css/responsive.css?time=".time(),
			   ),			   
			),
			'backend-core'=>array(
			   'baseUrl' => Yii::app()->baseUrl,
			   'css'=>array(			
			       "assets/vendor/datetimepicker/tempusdominus-bootstrap-4.min.css",
			       "assets/vendor/daterangepicker/daterangepicker.css",
			       "assets/vendor/datatables/datatables.min.css",
			       "assets/vendor/select2/css/select2.min.css",
			       "assets/vendor/summernote/summernote-bs4.min.css",	
			       "assets/vendor/bootstrap-select/css/bootstrap-select.min.css",	
			       "assets/vendor/fontawesome/css/fontawesome.css",			  
			       "assets/vendor/fontawesome/css/solid.min.css",	
			       "assets/vendor/spectrum/spectrum.min.css",	     
			       "assets/vendor/hamburgers.min.css",	 
			       "assets/vendor/dropzone/dropzone.css",	 
			       "assets/vendor/sidebarjs/sidebarjs.css",
			       "assets/vendor/notyf/notyf.min.css",
			       "assets/vendor/csshake.min.css",
				   "assets/vendor/element-plus/index.css",          
			   ),
			   'js'=>array(			      
			      "assets/vendor/datetimepicker/moment-with-locales.min.js",
			      "assets/vendor/datetimepicker/tempusdominus-bootstrap-4.min.js",		  
			      "assets/vendor/daterangepicker/daterangepicker.js",
			      "assets/vendor/jquery.translate.js",
			      "assets/vendor/datatables/datatables.min.js",			      
			      "assets/vendor/select2/js/select2.min.js",	
			      "assets/vendor/select2/js/i18n/en_us.js",		  	  
			      "assets/vendor/jquery.mask.js",	
			      "assets/vendor/summernote/summernote-bs4.min.js",	
			      "assets/vendor/bootstrap-select/js/bootstrap-select.min.js",
			      "assets/vendor/spectrum/spectrum.min.js",
			      "assets/vendor/jquery-nicescroll/jquery.nicescroll.min.js",				      
			      "assets/vendor/slide-and-swipe-menu/jquery.touchSwipe.min.js",	
			      "assets/vendor/slide-and-swipe-menu/jquery.slideandswipe.min.js",
			      "assets/vendor/vue/vue.global.prod.js", 				      
			      "assets/vendor/notyf/notyf.min.js",
			      "assets/vendor/axios.min.js",
			      "assets/vendor/maska.js", 			      
			      "assets/vendor/bootbox.min.js",
			      "assets/vendor/dropzone/dropzone.js",		
			      "assets/vendor/headroom.min.js",
			      "assets/vendor/autosize.min.js",			      			      
			      "assets/vendor/jquery.sticky.js",
			      "assets/vendor/lozad.min.js",
			      "assets/vendor/printThis.js",
			      "assets/vendor/countUp.min.js",			      
			      "assets/vendor/sidebarjs/umd/sidebarjs.min.js",
			      "assets/vendor/howler/howler.min.js",		
			      "assets/vendor/luxon.min.js",	
			      "assets/vendor/v-money3.umd.js",	
			      "assets/vendor/Sortable.min.js",	
			      "assets/vendor/vuedraggable.umd.min.js",			      
				  "assets/vendor/element-plus/element-plus.js",
			      "assets/vendor/StarWebPrintBuilder.js",
			      "assets/vendor/StarWebPrintTrader.js",
			      "assets/vendor/custom.js",
			   ), 
			   'depends'=>array('core','google-font','infinite-scroll','owl-carousel')
			),			
			'infinite-scroll'=>array(
			    'baseUrl' => Yii::app()->baseUrl,
			    'js'=>array(			      
			      "/assets/vendor/infinite-scroll.pkgd.min.js",	
			    ),
			),		
			'admin-js'=>array(
			    'baseUrl' => Yii::app()->baseUrl,
			    'js'=>array(			      			      
			      "/assets/js/admin.bundle.js",			      
			    ),
			),		
			'merchant-js'=>array(
			    'baseUrl' => Yii::app()->baseUrl,
			    'js'=>array(			      			      
			      "/assets/js/merchant.bundle.js",			      
			    ),
			),		
			'login-js'=>array(
			    'baseUrl' => Yii::app()->baseUrl,
			    'js'=>array(			      
			      "/assets/js/login.js?time=".time(),
			    ),
			),		
			'owl-carousel'=>array(
			   'baseUrl' => Yii::app()->baseUrl,
			   'css'=>array(			
                    "assets/vendor/owl-carousel/owl.carousel.min.css",
                    "assets/vendor/owl-carousel/owl.theme.default.min.css",
			   ),
			   'js'=>array(			      
			      "assets/vendor/owl-carousel/owl.carousel.min.js",	
			      "assets/vendor/owl-carousel/owl.lazyload.js",	
			   ),
			),	
			'pusher'=>array(
			    'baseUrl'=>'/',			    
			    'js'=>array(			      
			      '/js.pusher.com/7.0/pusher.min.js'	      
			    )
			),						
			'ably'=>array(
			    'baseUrl'=>'/',			    
			    'js'=>array(			      
			      '/cdn.ably.com/lib/ably.min-1.js'	      
			    )
			),			
			'piesocket'=>array(
			    'baseUrl'=>'/',			    
			    'js'=>array(			      			      
			      '/unpkg.com/piesocket-js@1'	      
			    )
			),	
			'webpush_pusher'=>array(
			    'baseUrl'=>'/',			    
			    'js'=>array(			      
			      '/js.pusher.com/beams/1.0/push-notifications-cdn.js'	      
			    )
			),					
			'webpush_onesignal'=>array(
			    'baseUrl'=>'/',			    
			    'js'=>array(			      
			      '/cdn.onesignal.com/sdks/OneSignalSDK.js'	      
			    )
			),					
			//
        );
        
        Yii::app()->clientScript->coreScriptPosition=CClientScript::POS_END;        
        
        if(is_array($bundle) && count($bundle)>=1){
        	foreach ($bundle as $bundle_name) {       
        		if(isset($cs->packages[$bundle_name])) {
        		   $cs->registerPackage($bundle_name);
        		}
        	}
        } 
        		
	}
	
}
/*end class*/