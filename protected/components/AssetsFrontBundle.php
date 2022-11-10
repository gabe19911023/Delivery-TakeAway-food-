<?php
class AssetsFrontBundle
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
                  'assets/vendor/bootstrap/js/bootstrap.bundle.js',
                  "assets/vendor/bootstrap-select/js/bootstrap-select.min.js",
                  "assets/vendor/jquery.lazy/jquery.lazy.min.js",
                  "assets/vendor/star-rating-svg/jquery.star-rating-svg.js",                  
                  "assets/vendor/sticky-sidebar/sticky-sidebar.js",                                                      
                  "assets/vendor/magnific-popup/jquery.magnific-popup.min.js",  
                  "assets/vendor/garand-sticky/jquery.sticky.js",                   
                  "assets/vendor/jquery-typeahead/jquery.typeahead.min.js",
                  "assets/vendor/maska.js", 
                  "assets/vendor/vue/vue.global.prod.js", 
                  "assets/vendor/axios.min.js",                  
                  "assets/vendor/slide-and-swipe-menu/jquery.touchSwipe.min.js",	
                  "assets/vendor/slide-and-swipe-menu/jquery.slideandswipe.js",
			      "assets/vendor/bootbox.min.js",
			      "assets/vendor/gsap.min.js",
			      "assets/vendor/headroom.min.js",
			      "assets/vendor/VueStarRating.umd.min.js",
			      "assets/vendor/vue-advanced-cropper/index.global.js",
			      "assets/vendor/notyf/notyf.min.js",
			      "assets/vendor/lozad.min.js",
			      "assets/vendor/howler/howler.min.js",
			      "assets/vendor/v-money3.umd.js",
			      "assets/vendor/nprogress/nprogress.js",
				  "assets/vendor/element-plus/element-plus.js",				  
                ),
                'css' => array(
                   'assets/vendor/bootstrap/css/bootstrap.min.css',
                   'assets/vendor/bootstrap/css/floating-labels.css',
                   'assets/vendor/material-design-iconic-font/css/material-design-iconic-font.min.css',
                   "assets/vendor/bootstrap-select/css/bootstrap-select.min.css",	
                   "assets/vendor/star-rating-svg/css/star-rating-svg.css",
                   "assets/vendor/magnific-popup/magnific-popup.css",
                   "assets/vendor/placeholder-loading.min.css",                                   
                   "assets/vendor/vue-advanced-cropper/style.css",
                   "assets/vendor/csshake.min.css",
                   "assets/vendor/notyf/notyf.min.css",
                   "assets/vendor/nprogress/nprogress.css",
				   "assets/vendor/hamburgers.min.css",
				   "assets/vendor/element-plus/index.css",                   
                ),
            ),    
            'google-font'=>array(
			    'baseUrl'=>'/',
			    'css'=>array(
			      "/fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,300&display=swap",				  
			    ),
			    'js'=>array(					  
			    )
			),		
            'front-core'=>array(
			   'baseUrl' => Yii::app()->baseUrl,
			   'css'=>array(			
                    "assets/vendor/fontawesome/css/fontawesome.css",	
                    "assets/vendor/fontawesome/css/solid.min.css",			  
			   ),
			   'js'=>array(			     
			   ), 
			   'depends'=>array('core','google-font','review')
			),			
			'infinite-scroll'=>array(
			    'baseUrl' => Yii::app()->baseUrl,
			    'js'=>array(			      
			      "assets/vendor/infinite-scroll.pkgd.min.js",	
			    ),
			),		  
			'front-css'=>array(
			   'baseUrl' => Yii::app()->theme->baseUrl,
			   'css'=>array(
			      "assets/css/style.css?time=".time(),
			      "assets/css/responsive.css?time=".time(),
			   ),			   
			),
			'front-js'=>array(
			    'baseUrl' => Yii::app()->baseUrl,
			    'js'=>array(			      			      			      
			      "assets/js/front.bundle.js",			      			      
			      "assets/js/custom.js"			  
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
			'review'=>array(
			   'baseUrl' => Yii::app()->baseUrl,
			   'css'=>array(			
                    "assets/vendor/dropzone/dropzone.css",	 
                    "assets/vendor/tagify/tagify.css",    
			   ),
			   'js'=>array(			      
			      "assets/vendor/dropzone/dropzone.js",	
			      "assets/vendor/autosize.min.js", 
                  "assets/vendor/tagify/tagify.min.js", 
			   ),
			),			     
			'google-maps'=>array(
			   'baseUrl'=>'/',			   
			   'js'=>array(			      			      
                  '/maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key='	      
			   ),
			   'position'=>CClientScript::POS_HEAD
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
        		$cs->registerPackage($bundle_name);
        	}
        } 
        		
	}
	
	public static function includeMaps()
	{
		$maps_config = CMaps::config();
		if($maps_config){
			$provider = isset($maps_config['provider'])?$maps_config['provider']:'';	
			$key = isset($maps_config['key'])?$maps_config['key']:'';	
			if($provider=="google.maps"){
				$include  = array(
				  '//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key='.urlencode($key)
				);
			} 
			ScriptUtility::registerJS($include,CClientScript::POS_HEAD);
		}
	}
}
/*end class*/