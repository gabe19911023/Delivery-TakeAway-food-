(function($) {
    "use strict";
    
    var $owl = [];
    var ajax_request = {};
    var timer = {};
    var sticky_sidebar;
    var sticky_cart;
    const $timeout = 20000;
    const $content_type = {
        'form':'application/x-www-form-urlencoded; charset=UTF-8',
        'json':'application/json'
    };
    var $instance_lazy;
    
    var dump = function(data)
    {
        console.debug(data);
    }
    
    var dump2 = function(data) {
        alert(JSON.stringify(data));	
    };
    
    var setStorage = function(key,value)
    {
        localStorage.setItem(key,value);
    }
    
    var getStorage = function(key)
    {
        return localStorage.getItem(key);
    }
    
    var removeStorage = function(key)
    {
        localStorage.removeItem(key);
    }
    
    jQuery.fn.exists = function(){return this.length>0;}
    
    var empty = function(data){	
        if (typeof data === "undefined" || data==null || data=="" || data=="null" || data=="undefined" ) {	
            return true;
        }
        return false;
    };
    
    jQuery(document).ready(function() {
        
        if( $('.sidebar-panel').exists() ) {
            $('.sidebar-panel').slideAndSwipe();	    
        }
        
        if( $('.headroom').exists() ) {
            var myElement = document.querySelector(".headroom");
            dump(myElement);
            var headroom  = new Headroom(myElement);
            headroom.init();
        }
                
        $( document ).ready( function() {
          $( '.dropdown' ).on( 'show.bs.dropdown', function() {
            $( this ).find( '.dropdown-menu' ).first().stop( true, true ).slideDown( 150 );
          } );
          $('.dropdown').on( 'hide.bs.dropdown', function(){
            $( this ).find( '.dropdown-menu' ).first().stop( true, true ).slideUp( 150 );
          } );
        });
        
        
        /*if( $('.owl-carousel').exists() ) {
            $owl = $(".owl-carousel").owlCarousel({
                items:5,
                lazyLoad:true,
                loop:true,
                margin:15
            });
        }*/
        
        if( $('.star-rating').exists() ) {
            initStarRating();		
        }
                    
        $( document ).on( "change", "#min_range_slider", function() {	
            $(".min-selected-range").html( $(this).val());
        });
        
        $('.collapse').on('show.bs.collapse', function(){	   	 
           $(this).parent().find("a").removeClass("closed").addClass("opened");
        }).on('hidden.bs.collapse', function(){
           $(this).parent().find("a").removeClass("opened").addClass("closed");
        });	
        
        /*$( document ).on( "click", ".quantity-add-cart", function() {
            var $_this = $(this);		
            var $_this_qty = $(this).closest('.quantity-wrapper').find('.qty');
            $_this_qty.html(1);
            $_this.hide().parent().find(".quantity-parent").show();						
        });	*/
        
        /*$( document ).on( "click", ".qty-btn", function() {		
            var $_this_qty = $(this).parent().parent().find(".qty");
            var $_add_to_cart = $(this).closest('.quantity-wrapper').find('.quantity-add-cart');
            var $_qty_parent = $(this).closest('.quantity-parent');
            
            var $cart_do = $(this).data("id");		
            var $qty = parseInt( $_this_qty.html() );		
            
            switch($cart_do){
                case "plus":
                  $qty = $qty+1;
                break
                
                case "less":			  
                  $qty = $qty-1;
                break
            }				
            
            if($qty<=0){
               $_qty_parent.hide();
               $_add_to_cart.show();
            }
            $_this_qty.html( $qty );
            
        });*/
        
        /*STICKY SIDEBAR*/
        /*sticky_sidebar = $('.sticky-sidebar').stickySidebar({
            topSpacing: 20,	    
        });*/
        
        if( $('.sticky-sidebar').exists() ) {		
            sticky_sidebar = new StickySidebar('.sticky-sidebar', {topSpacing: 20});
        }
        if( $('.sticky-cart').exists() ) {
            sticky_cart = new StickySidebar('.sticky-cart', {topSpacing: 20});
        }
        
        /*STICKY*/
        $(".category-nav").sticky({topSpacing:0});
        
        /*GALLERY*/
        $('.gallery_magnific').magnificPopup({
           delegate: 'a',
           type: 'image',
           gallery: {    
            enabled: true
           },  
           removalDelay: 300,
           mainClass: 'mfp-fade'
        });
            
        
        /*$( document ).on( "click", ".list-selection li, .single-select", function(event) {			
            var $input = $(this).find('input');		
                    
            if( $(event.target).hasClass('qty-btn') || $(event.target).hasClass('zmdi') ){
                return;
            }	
            
            if ( $input.attr("type")=="checkbox"){
                $input.prop('checked', !($input.is(':checked')));
            } else $input.prop('checked',true);
            
            var $parent = $(this).parent().parent().find("a");				
            $parent.each(function(index, $obj){						
                $($obj).removeClass("active");
            });		
            $(this).addClass("active");
        });*/
        
        $( document ).on( "click", ".toogle-radio", function() {		
            var $_this = $(this).find('input');		
            if($_this.prop('checked')==false){			
                $_this.prop('checked',true);
            } else {
                $_this.prop('checked',false);
            }
        });
        
        $( document ).on( "click", ".desired_delivery", function() {			
            HideandShow( $(this).find("input").val() ,{
              'now':'hide',
              'schedule':'show'
            }, '.schedule-section' );
        });
            
        /*$( document ).on( "click", ".add_tips", function() {				    
            HideandShow( $(this).find("input").val() ,{
              '10':'hide',
              '20':'hide',
              '30':'hide',
              'other':'show'
            }, '.tips-other-section' );
        });*/
        
        $( document ).on( "click", ".a-account", function() {							
            $(".login-wrap,.signup-wrap,.account-section").hide();		
            $( "."+$(this).data("id") ).show();
        });
        
        $( document ).on( "click", ".close-modal", function() {
            closeModal( $(this).data('id') );
        });
        
    
        /*TYPEEHEAD*/
        if( $('.auto_typehead').exists() ) {
            var $typehead_settings = {};		
            $('.auto_typehead').each(function(index, $obj){
                var $_this = $(this);
                var $api = $_this.data("api");			
                var $display = $_this.data("display");	
                
                var $temp_settings = parseQuery( $_this.data("settings") );							
                            
                $.each($temp_settings,function(key, val) {			
                    if(val=="true"){
                        $typehead_settings[key]=true;
                    } else if ( val=="false"){
                        $typehead_settings[key]=false;
                    } else {
                        $typehead_settings[key]=val;
                    }
                });
                                        
                $typehead_settings['input'] = $_this;				
                            
                $typehead_settings['source'] =  {
                    user: {			
                        display: $display.split(","),	 
                        ajax: {
                            method: "POST",
                            url: ajaxurl +"/"+$api,
                            path: "details.data",
                            data: {
                                q: "{{query}}",
                                'YII_CSRF_TOKEN': $('meta[name=YII_CSRF_TOKEN]').attr('content')
                            },
                            beforeSend: function (jqXHR, options) {
                                dump('before send');
                                //$(".typhead-error").remove();
                                
                                if($api=="getlocation_autocomplete"){
                                    if( $('.change-address-modal').exists() ) {
                                        $(".change-address-modal").find(".modal-content").addClass("opened");
                                    }
                                }
                                
                            },
                            callback: {
                                done: function (data) {	  	                        	
                                    /*if(data.code==2){	                        		
                                        var $found_err = $_this.parent().find(".typhead-error");
                                        if($found_err.length<=0){
                                           $_this.after('<p class="typhead-error m-0 text-danger">'+ data.msg+'</p>');
                                        } else {
                                            $found_err.html( data.msg );
                                        }
                                        $(".typeahead__container").removeClass("loading");
                                        return false;
                                    }*/
                                    return data;
                                },
                                fail: function (jqXHR, textStatus, errorThrown) {},
                                always :function (data, textStatus, jqXHR) {
                                    dump("always");
                                },
                                then: function (jqXHR, textStatus) {}
                            }
                        }
                    }
                };
                
                $typehead_settings['template'] = function (query, item) { 			    
                    return $_this.data("template");
                };
                            
                $typehead_settings['emptyTemplate'] = $_this.data("emptytemplate");
                
                $typehead_settings['callback'] =  {		      
                   onClickAfter: function (node, a, item, event) {		
                                                                 
                         var $method = node.data("api");
                         
                         switch($method){
                              case "getlocation_autocomplete":
                              
                               var $services = getServiceSelected();		       	  				  		       	 
                             var $params ='id='+ item.id + "&addressLine1="+ item.addressLine1 + "&addressLine2="+item.addressLine2;
                                                  
                             if(item.provider=="google.maps"){
                                   if ((typeof  item.place_type !== "undefined") && ( item.place_type !== null)) {
                                         $params+="&place_type=" + item.place_type;
                                   }
                             } else if( item.provider=="mapbox" ) {
                                 if ((typeof  item.latitude !== "undefined") && ( item.latitude !== null)) {
                                     $params+="&latitude=" + item.latitude;
                                     $params+="&longitude=" + item.longitude;
                                     $params+="&place_type=" + item.place_type;
                                 } 
                             }
                             
                             $params+="&services="+$services;
                             processAjax('getLocationDetails',$params,'typeahead_loader',1);
                              
                              break;
                              
                              case "getSearchSuggestion":		       	  	   
                                window.location.href = item.url;
                              break;
                                                       
                         }	//end switch	       
                                              
                         // onClickAfter
                   },		     
                   onCancel: function (node, item, event) {
                    
                           if($api=="getlocation_autocomplete"){
                           if( $('.change-address-modal').exists() ) {
                           $(".change-address-modal").find(".modal-content").removeClass("opened");
                        }   
                           }		       	
                           // onCancel
                   }  
                   //
                };
                
                $typehead_settings['selector'] = {
                    list: "typeahead__list list-unstyled",
                };
                            
                //dump($typehead_settings);
                $.typeahead($typehead_settings);
            });
        }
        /*END TYPEEHEAD*/
            
        $( document ).on( "click", ".get_current_location", function() {
            getCurrentLocation('get_place_details');
        });
        
        
        /*SHOW FILTER*/
        if( $('.load-ajax').exists() ) {				
            $('.load-ajax').each(function(index, $obj){			
                var $ajax_method = $($obj).data("action");
                var $params = $($obj).data("params");
                var $loader_type = $($obj).data("loader_type");
                var $single_call = $($obj).data("single_call");
                var $silent = $($obj).data("silent");					
                var $method = $($obj).data("method");
                var $content_type = $($obj).data("content_type");
                var $data = parseQuery($params);					
                if($content_type=="json"){								
                    if($ajax_method=="getFeed"){														
                        var $services = $data.services;
                        $params = {
                            'local_id':$data.local_id,
                            'target':$data.target,
                            'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                            'filters':{
                                'services':$services.split(",")
                            }										
                        };									
                    }
                    $params = JSON.stringify($params);					
                }									
                processAjax($ajax_method,$params,$loader_type,$single_call,$silent,$method,$content_type);
            });
        }
        
        /*if( $('.list-loader').exists() ) {
            $('.list-loader').each(function(index, $obj){			
                listPlaceholder($(this) , $obj);
            });
        }*/
        
        $( document ).on( "click", ".show-more", function($obj) {				
            var $ajax_method = $(this).data("action");
            var $params = $(this).data("params");
            var $loader_type = $(this).data("loader_type");
            var $single_call = $(this).data("single_call");		
            var $data = parseQuery($params);	
            var $filters = $data.filters;	
            $filters = $filters.replace(/\\/g, '');	
            $filters = JSON.parse( $filters );
                           
            $params = {
                'local_id':$data.local_id,
                'page': $data.page,
                'target':$data.target,
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                'filters': $filters
            };				
            $params = JSON.stringify($params);	
            processAjax($ajax_method,$params,$loader_type,$single_call,null,'PUT','json');		
        });
        
        $( document ).on( "click", ".restaurants-filter", function($obj) {
            var $local_id = $("#local_id").val();
            var $target = $("#target").val();		
            var $params='';
            var $sort = $(".sort:checked").val();
            var $price_range = $(".price_range:checked").val();
            var $max_fee = $("#min_range_slider").val();
            var $ratings = $(".ratings_selected").val();
            var $services = []; var $cuisine=[];
                    
           $(".services:checked").each(function() {	       	       
               $services.push( $(this).val() );
           });
           $(".cuisine:checked").each(function() {	       	       
               $cuisine.push( $(this).val() );
           });
           
           if ((typeof  $sort !== "undefined") && ( $sort !== null)) {
                 //
           } else $sort='';
                        
           $params  = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                'local_id':$local_id,
                'target':$target,	   	 
                'filters': {
                    'services':$services,
                    'cuisine':$cuisine,
                    'price_range':$price_range,
                    'max_fee':parseInt($max_fee),
                    'ratings':parseInt($ratings),
                    'sort':$sort
                }
           };	   
           $params = JSON.stringify($params);	   	   
           processAjax('getFeed',$params,'restaurants_filter',1,1,'PUT','json');		
        });
            
            
        // init lazy load
        $(function(){
        initLazyImage();	
        });
        
        $('#changeAddress').on('shown.bs.modal', function (e) {
           $(".auto_typehead").val('');
           $(".auto_typehead").focus();
        });
        $('#changeAddress').on('hidden.bs.modal', function (e) {	   
           $(".change-address-modal").find(".modal-content").removeClass("opened");
        });
            
        $( document ).on( "click", ".get-item-details", function($obj) {
            var $item_uuid = $(this).data("item_uuid");		
            var $cat_id = $(this).closest('section').data("cat_id");			
            processAjax("getMenuItem","&merchant_id="+ merchant_id + "&item_uuid=" + $item_uuid + "&cat_id="+ $cat_id );
        });
        
        
        $( document ).on( "click", ".choose-size", function() {
            var $item_size_id =  $(this).find('input:checked').val();
            if ((typeof  $item_size_id !== "undefined") && ( $item_size_id !== null)) {		
                AddonCategoryRows($item_size_id);
            }
        });
    
        $( document ).on( "click", ".addon-items", function(event) {			
            /*var $parent = $(this).closest("ul");		
            var $addon_multi_option = $parent.data("multi_option");
            var $addon_multi_option_value = $parent.data("multi_option_value");
                    
            dump("$addon_multi_option=>"+$addon_multi_option);
            
            switch($addon_multi_option){
                case "custom":			  
                  var $inputs = $parent.find("input:checked");
                  var $total_selected = $inputs.length;						  
                  dump($total_selected+"=>"+$addon_multi_option_value);
                  if($total_selected>=$addon_multi_option_value){			  	
                      $parent.find("input:checkbox:not(:checked)").attr("disabled",true);
                  } else {			  	
                      $parent.find("input:disabled").attr("disabled",false);
                  }							  
                break;
            }*/			
            /*setTimeout(function() {								
                ItemSummary();
            }, 1); */
        });
        
        /*$( document ).on( "click", ".multiple_qty", function(event) {
            setTimeout(function() {								
               ItemSummary();
            }, 1); 
        });*/
        
        /*$( document ).on( "click", ".add_to_cart", function(event) {
            addToCart();
        });*/
            
    });
    /*end doc ready*/
    
    var listPlaceholder = function($_this){
        var $html='';
        var $itemlimit = parseInt( list_limit );
        dump("$itemlimit=>"+$itemlimit);
        for (let i = 0; i < $itemlimit; i++) {
           $html+='<div class="col-lg-3 col-md-3 mb-4 mb-lg-3">';
               $html+='<div class="position-relative skeleton-height">';
               $html+='<div class="skeleton-placeholder"></div>';
               $html+='</div>';
            $html+='</div>';
        } 
        $_this.html( $html );
    };
    
    var getCurrentLocation = function(next_steps){	
        
        var options = {
          enableHighAccuracy: true,
          timeout: 5000,
          maximumAge: 0
        };
    
        if (navigator.geolocation) {
           initLoader('typeahead_loader',1);
           navigator.geolocation.getCurrentPosition(function(position){
               
                 var $services = getServiceSelected();
                 
                 processAjax("reverseGeocoding","lat="+ position.coords.latitude + "&lng=" + position.coords.longitude + "&services=" + $services + "&next_steps="+next_steps ,'typeahead_loader',1);
                         
           }, function(err){	   	  
                 initLoader('typeahead_loader',false);
                 notify( err.message );
           }, options);
        } else {
            notify( t("Error: Your browser doesn't support geolocation.") );
            initLoader('typeahead_loader',false);
        }
    };
    
    var getServiceSelected = function(){
         var $services ='';
         if( $('#services').exists() ) {
              $("#services option:selected").each(function(){				      
              $services+=$(this).val()+',';
           });	
         } 
         return $services;
    };
    
    function parseQuery(str)
    {
        if(typeof str != "string" || str.length == 0) return {};
        var s = str.split("&");
        var s_length = s.length;
        var bit, query = {}, first, second;
        for(var i = 0; i < s_length; i++)
            {
            bit = s[i].split("=");
            first = decodeURIComponent(bit[0]);
            if(first.length == 0) continue;
            second = decodeURIComponent(bit[1]);
            if(typeof query[first] == "undefined") query[first] = second;
            else if(query[first] instanceof Array) query[first].push(second);
            else query[first] = [query[first], second]; 
            }
        return query;
    }
            
    var HideandShow = function($value, $data , $target){		
        $.each($data,function(key, val) {
            dump(key+"=>"+val +"=>"+$value);
            if($value==key){
                if(val=="show"){			   
                    $($target).show();				
                } else {				
                    $($target).hide();				
                }
            }
        });
    };
    
    var closeModal = function($modal_id){
        $('#'+$modal_id).modal('hide')
    };
    
    var initLazyImage = function(){	    
        $instance_lazy = $(".lazy").Lazy({	 	
             chainable: false,        	 	
            beforeLoad: function(element) {
                //dump('before load');
            },
            afterLoad: function(element) {
                /*dump('after load');
                dump(element);*/
                element.parent().parent().addClass("image-loaded");
                //element.parent().addClass("image-loaded");
            },
            onError: function(element) {
                //dump('error loading image');
            },
            onFinishedAll: function() {
                //dump('finish loading all image');
            }
        });
    };
    
    var updateLazyImage = function(){
            
        setTimeout(() => {	
            dump('updateLazyImage'); 	    
            /*$instance_lazy.addItems( $('.lazy') );
            $instance_lazy.loadAll();		*/
            initLazyImage();
        }, 500); 
    };
    
    var t = function(words){
        return words;
    };
    
    /*var notify = function($message, $message_type){
        dump2($message);
    };*/
    
    const notyf = new Notyf({
      'duration':1000*4
    });
    
    var notify = function($message, $message_type){	
        switch($message_type){
            case "error":
            notyf.error($message);
            break;
            
            default:
            notyf.success($message);
            break;
        }	
    };
    
    const DEMO_notyf = new Notyf({
          'duration':10000*6,
          'dismissible':true,
          'icon':'',	  
       });	
       
    var helpNotify = function(message){	
       DEMO_notyf.success(message);
    };
    
    var getTimeNow = function(){
        /*var d = new Date();
        var n = d.getTime();    
        n = parseInt(n) + parseInt(d.getMilliseconds());    
        return n;*/
        var n = Date.now() + ( (Math.random()*100000).toFixed());	
        return n;
    };	
    
    var addValidationRequest = function(){
        var params='';		
        var yii_token = $('meta[name=YII_CSRF_TOKEN]').attr('content');	
        params+="&YII_CSRF_TOKEN="+yii_token;
        return params;
    }
    
    /*mycall*/
    var processAjax = function(action , data , loader_type, single_call, silent, method , content_type){
    
        var timenow = getTimeNow();
        if(!empty(single_call)){			
            timenow = single_call;
        }	
        
        //dump("timenow=>"+ timenow);
        
        if(empty(silent)){
           silent = null;
        }
    
        var $content_type ='';
        
        if(empty(method)){
            method="POST";		
        } 
        if(empty(content_type)){
            $content_type = 'application/x-www-form-urlencoded; charset=UTF-8';
            data+=addValidationRequest();
        } else {
            $content_type = 'application/json';
        }
            
        ajax_request[timenow] = $.ajax({
          url: ajaxurl+"/"+action,
          method: method,
          data: data ,
          dataType: "json",
          contentType : $content_type ,
          timeout: $timeout,
          crossDomain: true,
          beforeSend: function( xhr ) {   	  	 
               //dump("is silent=>"+silent);
               //if ((typeof silent !== "undefined") && ( silent !== null)) {
               if(silent==null){
                   //dump("ok silent => " + action);
               } else {	  	 
                   //dump("show loader_type=> "+ loader_type);
                   initLoader(loader_type,true);
               }
             if(ajax_request[timenow] != null) {	
                 //dump("request aborted");     
                 ajax_request[timenow].abort();
                clearTimeout( timer[timenow] );
             } else {         	
                 timer[timenow] = setTimeout(function() {				
                    ajax_request[timenow].abort();
                    //notify( t('Request taking lot of time. Please try again') );
                }, $timeout); 
             }
          }
        });
        
        
        ajax_request[timenow].done(function( data ) {
            //dump('done');	
            var next_action='';     
            if ((typeof  data.details !== "undefined") && ( data.details !== null)) {
                if ((typeof  data.details.next_action !== "undefined") && ( data.details.next_action !== null)) {
                     next_action = data.details.next_action;
                }
            }
                    
            if(data.code==1){
                switch(next_action){
                    case "redirect":	    		  
                      window.location.href = data.details.redirect_url;
                    break;
                    
                    case "get_place_details":	    		  
                      var $data = data.details.data;	  	    		  
                      $(".auto_typehead").val( $data.address.address1 );
                      var $params ='id='+ $data.place_id + "&addressLine1="+ $data.address.address1 + "&addressLine2="+$data.address.address2;
                      if(data.details.provider=="google.maps"){
                          if ((typeof  $data.place_type !== "undefined") && ( $data.place_type !== null)) {
                              $params+="&place_type=" + $data.place_type;
                          }
                      } else if( data.details.provider=="mapbox" ) {
                          if ((typeof  $data.latitude !== "undefined") && ( $data.latitude !== null)) {
                            $params+="&latitude=" + $data.latitude;
                            $params+="&longitude=" + $data.longitude;
                            $params+="&place_type=" + $data.place_type;
                          } 
                      }
                      
                      $params+="&services="+data.details.services;
                         processAjax('getLocationDetails',$params,'typeahead_loader',1);
                    break;
                    
                    case "sidebar_filter":	    		 
                      $(".sidenav-filter").html(  data.details.data );
                      $(".min-selected-range").html( $("#min_range_slider").val());
                      initStarRating();
                      
                      $('#moreCuisine').on('shown.bs.collapse', function () {
                          $(".more-cuisine u").html( t("View less") );
                      });
                      $('#moreCuisine').on('hidden.bs.collapse', function () {
                          $(".more-cuisine u").html( t("Show more +") );
                      });
        
                    break;
                    
                    case "fill_feed":	    		  
                                                       
                      fillFeed({
                          'action': data.details.action,
                          'title':data.details.total_message,
                          'data': data.details.data ,
                          'target':data.details.target ,
                          'append': data.details.append ,
                          'show_next_page': data.details.show_next_page,
                          'next_page_data': data.details.next_page_data
                      });
                    break;
                    
                    case "merchant_menu":
                    
                       /*MerchantMenu({
                           'data':data.details.data,
                           'target':data.details.target ,
                       });*/	    	
                       
                       MerchantMenu({
                           'data':data.details.data,
                       });	    	
                              
                    break;
                    
                    case "show_item_details":	    		  
                      $('#itemModal').modal('show');
                      ItemDetails( data.details.data );
                    break;
                    
                    case "fill_price_format":	    		  	    		 
                      $(data.details.data.target).html( data.details.data.pretty_price );
                    break;
                    
                }
            } else {
                switch(next_action){
                    case "location_details_no_results":   		  
                      notify( data.msg );
                      $(".typeahead__container").removeClass("loading")
                    break;
                    
                    case "clear_restaurant_results":
                      var $target = data.details.target;	    		  
                      var $html='';
                      $html+='<div class="list-items mt-4 row hover01 list-loader load-ajax"></div>';
                      $html+=data.details.data;
                      $($target).html($html);	    		  
                    break;
                    
                    default:
                      notify( data.msg );
                    break;
                }
            }
            
        });
        
        /*ALWAYS*/
        ajax_request[timenow].always(function() {    	
            //dump("ajax always");        
            initLoader(loader_type,false);
            ajax_request[timenow]=null;  
            clearTimeout(timer[timenow]);
        });
        
        /*FAIL*/
        ajax_request[timenow].fail(function( jqXHR, textStatus ) {    	
            clearTimeout(timer[timenow]);
            //notify( t("Failed") + ": " + textStatus ,'danger' );        
        });  
        
    };
    /*end mycall*/
    
    var fillFeed = function($data){	
        //dump( $data );	
        
        var $html=''; var $items =''; var $bgcolor; var $fncolor; var $attrs='';
        var $append = parseInt($data.append);
        var $target = $data.target;	
        var $show_next_page = $data.show_next_page;	
                
        $html+='<h4 class="total_message title">';
        $html+= '<span>'+ $data.title +'</span>' ;
        $html+='<div class="skeleton-placeholder"></div>';
        $html+='</h4>';
        
        $html+='<div class="list-items mt-4 row hover01 list-loader load-ajax">';
        
        $.each($data.data, function(key, val){		
            
            $items+='<div class="col-lg-3 col-md-3 mb-4 mb-lg-3">';
            $items+='<a href="'+ val.url +'">';
              $items+='<div class="position-relative">';
                 $items+='<div class="skeleton-placeholder"></div>';
                 $items+='<figure><img class="rounded lazy" data-src="'+ val.url_logo +'"/></figure>';
                 $items+='<h6 class="mt-2">'+ val.restaurant_name +'</h6>';
                 
                 $items+='<div class="row">';
                     $items+='<div class="col d-flex justify-content-start align-items-center"> ';
                        $items+='<p class="m-0"><i class="zmdi zmdi-time"></i> '+ val.delivery_estimation +'</p>';
                     $items+='</div>';
                     $items+='<div class="col d-flex justify-content-end align-items-center">';
                        if(val.delivery_fee_raw>0){
                        $items+='<p class="m-0"><i class="zmdi zmdi-car"></i> '+ val.delivery_fee +'</p>';
                        }
                     $items+='</div>';
                  $items+='</div>';
                  
                  $items+='<div class="position-top">';
                                    
                      if ((typeof  val.cuisine !== "undefined") && ( val.cuisine !== null)) {
                            if(val.cuisine.length>0){
                                 $bgcolor = val.cuisine[0].bgcolor;
                                 $fncolor = val.cuisine[0].fncolor;
                             $items+='<span class="badge" style="background:'+  $bgcolor +';font-color:'+  $fncolor +';">'+ val.cuisine[0].cuisine_name +'</span>';
                            }
                      }
                      
                      $items+='<span class="badge rounded-pill float-right" style="background:'+  $bgcolor +';font-color:'+  $fncolor +';" >'+ val.ratings +'</span>';
                  $items+='</div>';
                 
              $items+='</div>';
            $items+='</a>';
            $items+='</div>';
        });
        
        if($append<=0){
            $html+=$items;
        }
        
        $html+='</div>';
            
        if($show_next_page==true){				
            dump("SHOW NEXT");
            $.each($data.next_page_data, function(key, val){	    
                if(key=="filters"){					   
                   $attrs+="&filters="+ addslashes(JSON.stringify(val));
                } else $attrs+=key+"="+val+"&";
            });
        
            dump("ATTRIBUTES");
            dump($attrs);
            
            $html+='<div class="text-center show_more_section mt-5">';
              $html+='<a href="javascript:;" class="btn btn-black  show-more" ';
              $html+='data-action='+$data.action+' data-params='+$attrs ;
              $html+='\n';
              $html+='data-loader_type='+ 'show_more' +' data-single_call="1"' ;
              $html+='data-silent=""' ;
              $html+='>'+ t("Show more") +'</a>';
              $html+='<div class="m-auto circle-loader medium" data-loader="circle-side"></div>';
            $html+='</div>';				
        } else {
            $(".show_more_section").hide();
        }
        
            
        if($append>0){
            $(".list-items").append( $items );
        } else {
            $($target).html( $html );
        }
    
        updateLazyImage(); 
    };
    
    var addslashes = function(str)
    {
        return (str + '')
        .replace(/[\\"']/g, '\\$&')
        .replace(/\u0000/g, '\\0')
    };
    
    /*myloader*/
    var initLoader = function(loader_type,is_show){
        //dump("loader_type=>"+loader_type + " =>" + is_show);
        switch(loader_type){
             case "getLocationDetails":
             case "typeahead_loader":
               if(is_show){
                 $(".typeahead__container").addClass("loading");
               } else { 		  	
                   setTimeout(function() {								
                        $(".typeahead__container").removeClass("loading")
                }, 1000); 
               }
             break;
             
             case "show_more": 
                if(is_show){ 		   	   		   	  
                      $(".show_more_section").addClass("loading");
                } else { 		   	  
                      $(".show_more_section").removeClass("loading");
                }
             break;
             
             case "results_loader":
               if(is_show){ 		   	   		  	 
                     listPlaceholder( $('.list-loader') );
               } else { 		   	  
                     //
               }
             break;
             
             case "restaurants_filter":
               if(is_show){ 		   	   		  	 
                     listPlaceholder( $('.list-loader') );
                     $(".restaurants-filter").addClass("loading");
                     $(".total_message").addClass("loading");
                     $(".show_more_section").hide();
               } else { 		   	  
                     $(".restaurants-filter").removeClass("loading");
                     $(".total_message").removeClass("loading");
               }
             break;
             
             case "add_to_cart":
               if(is_show){ 		   	   		  	  		   	 
                     $(".add_to_cart").addClass("loading"); 		   	  		   	 
               } else { 		   	  
                     $(".add_to_cart").removeClass("loading"); 		   	 
               }
             break;
             
             default:
             break;
         }
    };
    
    var initStarRating = function(){
        
        var $star_settings = {};		
        $('.star-rating').each(function(index, $obj){	
            var $_this = $(this);
            var $totalstars = $(this).data("totalstars");
            var $initialrating = $(this).data("initialrating");
            var $strokecolor = $(this).data("strokecolor");
            var $ratedcolor = $(this).data("ratedcolor");
            var $strokewidth = parseInt($(this).data("strokewidth"));
            var $starsize = parseInt($(this).data("starsize"));
            var $readonly = $(this).data("readonly");
            var $minrating = $(this).data("minrating");
            var $disableafterrate = $(this).data("disableafterrate");			
            var $usefullstars = $(this).data("usefullstars");
            var $hovercolor = $(this).data("hovercolor");
            
            $star_settings['totalStars'] = !empty($totalstars)?$totalstars:'';
            $star_settings['initialRating'] = !empty($initialrating)?$initialrating:'';
            $star_settings['minRating'] = !empty($minrating)?$minrating:'';
            $star_settings['strokeColor'] = !empty($strokecolor)?$strokecolor:'';
            $star_settings['ratedColor'] = !empty($ratedcolor)?$ratedcolor:'';
            $star_settings['hoverColor'] = !empty($hovercolor)?$hovercolor:'';
            $star_settings['strokeWidth'] = !empty($strokewidth)?$strokewidth:'';
            $star_settings['starSize'] = !empty($starsize)?$starsize:'';			
            $star_settings['readOnly'] = !empty($readonly)?$readonly:'';	
            $star_settings['disableAfterRate'] = $disableafterrate;
            $star_settings['useFullStars'] = !empty($usefullstars)?$usefullstars:'';	
            
            $star_settings['callback'] = function(currentRating, $el){						
                $el.parent().find(".ratings_selected").val(currentRating);			
                //vue_app_review.rating_value = currentRating;			
                vm_my_order.$refs.ReviewRef.rating_value = currentRating;
            };
                                    
            //dump($star_settings);
            $(this).starRating($star_settings);
        }); /*end each*/
        
    };
    
    var updateStickySibader = function(){
        setTimeout(function() {								
            sticky_sidebar.updateSticky();
        }, 1); 
    };
    
    var updateStickyCart = function(){
        setTimeout(function() {			
           if ((typeof sticky_cart !== "undefined") && ( sticky_cart !== null)) {								
               sticky_cart.updateSticky();
           }
        }, 1); 
    };
    
    var MerchantMenux = function($results){
        
        $(".menu-left").removeClass("loading");
        $("#merchant-menu").removeClass("loading");	
        
        var $category = $results.data.category; 
        var $items = $results.data.items; 
    
        /*FILL CATEGORY*/
        Vue.createApp({
               data(){
                   return $results.data
               }	    		   
        }).mount('#menu-category');		
        var $new_category = []; var $new_items = [];
        
        /*FILL ITEMS*/
        if($category.length>0){
            $.each($category, function(key, val){
                            
                $.each(val.items, function(items_key, $item_id){
                    $new_items.push( $items[$item_id] );
                }); // loop
                        
                $new_category.push({
                    'cat_id': val.cat_id,
                    'category_name': val.category_name,
                    'category_uiid': val.category_uiid,	
                    'items':$new_items
                });	
                
                $new_items = [];
            }); // loop
        }
                    
        var $new_category = {"category":$new_category};		
        Vue.createApp({
               data(){
                   return $new_category
               }	    		   
        }).mount('#merchant-menu');	
        
        updateStickySibader();	
    };
    
    
    var $items;
    var $addons;
    var $addon_items;
    //var vm;
    
    var ItemDetails = function($data){
        $items = $data.items;
        $addons = $data.addons;
        $addon_items = $data.addon_items;
            
        var $new_data = {"items":$items};	
        
        if ((typeof vm !== "undefined") && ( vm !== null)) {		
            vm.$data.items = $items;				
            $("#item-details .lazy").attr("src",  $items.url_image );
        } else {			
            vm = Vue.createApp({
                   data(){
                       return $new_data
                   },
                   created () {
                    //
                },	 	    
                   //
            }).mount('#item-details');	
        }		
    };
    
    var ItemDetailsx = function($data){
        
        var $target = $("#itemModal .modal-body");
        $items = $data.items;
        $addons = $data.addons;
        $addon_items = $data.addon_items;
        
        var $html='';
        
        $html+='<div class="item_s" data-item_token="'+ $items.item_token +'" >';
        
        $html+='<div class="list-items">';
          $html+='<div class="position-relative">';
           $html+='<div class="skeleton-placeholder"></div>';
           $html+='<img class="rounded lazy" data-src="'+ $items.url_image +'"/>';
          $html+='</div>';
        $html+='</div>';
        
        $html+='</div>';
        
        $html+='<h4 class="m-0 mt-2 mb-2">'+ $items.item_name +'</h4>';
        if(!empty($items.item_description)){
        $html+='<p>'+ $items.item_description +'</p>';
        }
    
        /*PRICE*/
        var $item_price_count = 0; 
        if ((typeof  $items.price !== "undefined") && ( $items.price !== null)) {
           $item_price_count = Object.keys($items.price).length;
        }        
        if($item_price_count>0){
           $html+='<div class="mb-4 btn-group btn-group-toggle input-group-small choose-size flex-wrap" data-toggle="buttons">';    
           var $price_x = 1;
           $.each( $items.price , function($pkey,$prices){ 
                  var $active = $price_x==1?"active":''; 
                  var $checked = $price_x==1?"checked='checked'":'';       	   
                  $html+='<label class="btn '+ $active +' ">';
                    $html+='<input value="'+ $prices.item_size_id +'" name="size_uuid" id="size_uuid" class="size_uuid" type="radio" '+ $checked +'> ';
                    
                    if ( $prices.discount<=0){
                    $html+= $prices.size_name+" "+$prices.pretty_price;
                 } else {
                    $html+= $prices.size_name+' <del>'+ $prices.pretty_price +'</del> ' + $prices.pretty_price_after_discount;
                 }				   	   
             
                  $html+='</label>';
                  $price_x++;
           });
           $html+='</div>';
        }
        /*PRICE*/
        
        $html+='<div class="addon-list"></div>';
            
        $target.html($html);	
        
        $html='';
        
        var $item_size_id = $('input[name="size_uuid"]:checked').val();
        //dump("=>"+ $item_size_id);
            
        AddonCategoryRows($item_size_id);		
    };
    
    var AddonCategoryRows = function( $item_size_id ){
        
        var $html='';
        var $target = $("#itemModal .addon-list");
        
        if ((typeof  $items.item_addons !== "undefined") && ( $items.item_addons !== null)) {
            if ((typeof  $items.item_addons[$item_size_id] !== "undefined") && ( $items.item_addons[$item_size_id] !== null)) {
                $.each( $items.item_addons[$item_size_id] , function($key,$addon_category_id){
                    
                    if ((typeof $addons[$addon_category_id] !== "undefined") && ( $addons[$addon_category_id] !== null)) {						
                        
                    var $multi_options = {
                      'multi_option':$addons[$addon_category_id].multi_option,
                      'multi_option_value':$addons[$addon_category_id].multi_option_value,
                      'require_addon':$addons[$addon_category_id].require_addon,
                      'pre_selected':$addons[$addon_category_id].pre_selected,
                      'sub_items':$addons[$addon_category_id].sub_items,
                    };
                    
                    $html+='<DIV class="addon-rows">';
                    
                    $html+='<div class="d-flex align-items-center heads">';
                        $html+='<div class="flexcol flex-grow-1">';
                           $html+='<h5 class="m-0">'+  $addons[$addon_category_id].subcategory_name  +'</h5>';
                           $html+='<p class="text-grey m-0 mb-1">'+ AddonCategoryMultiOption( $multi_options ) +'</p>';
                        $html+='</div>';
                        $html+='<div class="flexcol">';
                         $html+='<h6 class="m-0">'+ AddonCategoryRequired($addons[$addon_category_id].require_addon) +'</h6>';
                        $html+='</div>';
                     $html+='</div>';
                     
                     var $sub_items = $addons[$addon_category_id].sub_items;
                     if($sub_items.length>0){	
                         $html+='<ul class="list-unstyled list-selection list-addon no-hover m-0 p-0"'; 
                         $html+='data-subcat_id="'+ $addon_category_id +'" ';
                         $html+='data-multi_option="'+ $multi_options.multi_option +'" data-multi_option_value="'+ $multi_options.multi_option_value +'" ';
                         $html+='data-require_addon="'+ $multi_options.require_addon +'" data-pre_selected="'+ $multi_options.pre_selected +'" ';
                         $html+='>';
                         
                        $.each( $sub_items , function($key,$addon_items_id){
                            if ((typeof  $addon_items[$addon_items_id] !== "undefined") && ( $addon_items[$addon_items_id] !== null)) { 			        
                               $html+=AddonItemsRows( $addon_items[$addon_items_id] , $addon_category_id , $multi_options );
                            }
                        });
                        $html+='</ul>';
                     }	    
                    
                    $html+='</DIV>';
                    
                    }
                    
                });
            }
        }
        
        $target.html($html);
        ItemSummary();
    };
    
    var AddonItemsRows = function($data, $addon_category_id , $multi_options){
        
        dump("AddonItemsRows");
        dump($multi_options);
        var $multi_option= !empty($multi_options.multi_option)?$multi_options.multi_option:'';	
        var $multi_option_value= !empty($multi_options.multi_option_value)?$multi_options.multi_option_value:'';	
        dump("multi_option=>"+ $multi_option);
        dump("multi_option_value=>"+ $multi_option_value);
        
        var $html='';
        $html+='<li class="d-flex align-items-center" >';
         //$html+='<div class="flexcol mr-2">';
         
            switch($multi_option){
                
                case "custom":
                $html+='<div class="custom-control custom-checkbox flex-grow-1">';
                  $html+='<input type="checkbox" id="sub_item_id'+ $data.sub_item_id +'" name="sub_item_id_'+ $addon_category_id +'" value="'+ $data.sub_item_id +'" class="addon-items custom-control-input">';
                  $html+='<label class="custom-control-label font14 bold" for="sub_item_id'+ $data.sub_item_id +'">';
                   $html+='<h6 class="m-0">'+ $data.sub_item_name  +'</h6>';
                  $html+='</label>';
                $html+='</div>';         
                $html+='<p class="m-0">+'+ $data.pretty_price  +'</p>';
                break;
                
                case "multiple":
                
                 $html+='<div class="position-relative quantity-wrapper ">';
                 $html+='<div class="quantity-parent">';
                         $html+='<div class="quantity d-flex justify-content-between m-auto">';
                            $html+='<div><a href="javascript:;" class="rounded-pill qty-btn multiple_qty" data-id="less"><i class="zmdi zmdi-minus"></i></a></div>';
                            $html+='<div class="qty" data-id="'+ $data.sub_item_id +'">0</div>';
                            $html+='<div><a href="javascript:;" class="rounded-pill qty-btn multiple_qty" data-id="plus"><i class="zmdi zmdi-plus"></i></a></div>';
                         $html+='</div>';
                     $html+='</div> ';
                         
                     $html+='<a href="javascript:;" class="btn quantity-add-cart multiple_qty">';
                       $html+='<i class="zmdi zmdi-plus"></i>';
                     $html+='</a>';
                 $html+='</div>'; 
                 
                 $html+='<div class="flexcol ml-3">';
                 $html+='<h6 class="m-0">'+ $data.sub_item_name  +'</h6>';
                 $html+='<p class="m-0 text-grey">+'+ $data.pretty_price  +'</p>';
                 $html+='</div>';
                
                break;
                
                default:
                $html+='<div class="custom-control custom-radio flex-grow-1">';
                  $html+='<input type="radio" id="sub_item_id'+ $data.sub_item_id +'" name="sub_item_id_'+ $addon_category_id +'" value="'+ $data.sub_item_id +'" class="addon-items custom-control-input">';
                  $html+='<label class="custom-control-label font14 bold" for="sub_item_id'+ $data.sub_item_id +'">';
                   $html+='<h6 class="m-0">'+ $data.sub_item_name  +'</h6>';
                  $html+='</label>';
                $html+='</div>';       
                $html+='<p class="m-0">+'+ $data.pretty_price  +'</p>';    
                break;
            }        
         
         //$html+='</div>';
         
         /*if($multi_option=="multiple"){
             $html+='<div class="flexcol">';
               $html+='<h6 class="m-0">'+ $data.sub_item_name  +'</h6>';
               $html+='<p class="m-0 text-grey">+'+ $data.pretty_price  +'</p>';
             $html+='</div>';
         } else {
             $html+='<div class="flexcol flex-grow-1">';
               $html+='<h6 class="m-0">'+ $data.sub_item_name  +'</h6>';        
             $html+='</div>';
             
             $html+='<div class="flexcol ">';
               $html+='<p class="m-0">+'+ $data.pretty_price  +'</p>';
             $html+='</div>';
         }*/
         
       $html+='</li>';       
       return $html;
        
    };
    
    var AddonCategoryMultiOption = function($data){	
        var $display_msg = ''; 
        var $multi_option= !empty($data.multi_option)?$data.multi_option:'';
        var $sub_items= !empty($data.sub_items)?$data.sub_items:'';
        var $multi_option_value= !empty($data.multi_option_value)?$data.multi_option_value:'';	
        
        switch($multi_option){
            case "one":
            $display_msg = t("Select 1");
            break;
            
            case "multiple":
            $display_msg = t("Choose up to ") + $multi_option_value; 
            break;
            
            case "two_flavor":
            $display_msg = t("Select flavor " + $multi_option_value); 
            break;
            
            case "custom":
            $display_msg = t("Choose up to ") + $multi_option_value;
            break;
            
            default:
            break;
        }
        return $display_msg;
    };
    
    var AddonCategoryRequired = function($data){
        if($data>0){
            return t("Required");
        } else return t("Optional");
    };
    
    var $sub_item_id = 0;	 
    var $sub_item_qty = 0;
    var $addon_price = 0;
    var $item_size_id = 0;
    var $item_token = '';
    var $item_total = 0;
    var $item_price = 0;
    var $addon_multi_option;
    var $addon_child;
    var $subcat_id = 0;
    var $addons_category = [];
    var $addons_items = [];
    var $require_addon=0;
    var $addon_multi_option_value=0;
    
    var ItemSummaryx = function(){	
        dump("ItemSummary");
        $item_total = 0;
        $sub_item_id = 0;
        $addon_price = 0;
        $sub_item_qty = 0;
        $item_size_id = vm.size_id; 
        $item_price = 0;	
        
        $items = $global_items;
        $addon_items = $global_addon_items;
        
        if(!empty($items.price[$item_size_id])){			
            if( $items.price[$item_size_id].discount>0  ){
                $item_price = !empty($items.price[$item_size_id]) ? parseFloat($items.price[$item_size_id].price_after_discount) : 0;	
            } else $item_price = !empty($items.price[$item_size_id]) ? parseFloat($items.price[$item_size_id].price) : 0;			
        }	
        
        //dump("price=>" + $item_price);
        
        $item_total+=$item_price;
        var $added = [];
        var $required_addon = [];	
            
        $('.list-addon').each(function(index, $obj){				
            $addon_multi_option = $($obj).attr("data-multi_option");			
            $require_addon = $($obj).attr("data-require_addon");					
            $subcat_id = $($obj).attr("data-subcat_id");		
            $addon_multi_option_value = $($obj).attr("data-multi_option_value");
            
            if($addon_multi_option=="custom" || $addon_multi_option=="one"){
               $addon_child =  $($obj).find(".addon-items:checked");		   
            } else if ( $addon_multi_option=="multiple"){
               $addon_child =  $($obj).find(".qty");	
            }		
                            
            if($require_addon==1){		  
              $required_addon.push($subcat_id);
            }
            
            if($addon_multi_option_value>0){			
                 var $inputs = $($obj).find("input:checked");
                 var $total_selected = $inputs.length;					 
                 if($total_selected>=$addon_multi_option_value){			  	
                      $($obj).find("input:checkbox:not(:checked)").attr("disabled",true);
                 } else {			  	
                      $($obj).find("input:disabled").attr("disabled",false);
                 }							  
            }
                        
            if ((typeof  $addon_child !== "undefined") && ( $addon_child !== null)) {	
                $addon_child.each(function(child_index, child_object){				
                    if($addon_multi_option=="multiple"){									
                        //dump("==>x"+$addon_multi_option_value);					
                        $sub_item_id = $(child_object).data("id");
                        $sub_item_qty =  parseInt( $(child_object).html() ) ;					
                        $addon_price = !empty($addon_items[$sub_item_id])? parseFloat($addon_items[$sub_item_id].price) : 0;
                        $item_total+= ($sub_item_qty*$addon_price);		
                        if($sub_item_qty>0){
                            $added.push($subcat_id);
                        }				
                    } else {
                        $sub_item_id = $(child_object).val();					
                        $addon_price = !empty($addon_items[$sub_item_id])? parseFloat($addon_items[$sub_item_id].price) : 0;					
                        $item_total+=$addon_price;
                        $added.push($subcat_id);
                    }				
                }); //loop
            }
            
        }); //loop
    
        dump("REQUIRED");
        dump($required_addon);
        dump($added);
        dump("END REQUIRED");
        
        var $required_meet=true;	
        if($required_addon.length>0){
            $.each($required_addon, function(i, val){			
                if($added.includes(val)===false){
                    $required_meet = false;
                    return false;
                }	
            });
        } 
        
        dump("$required_meet=>"+$required_meet);
            
        if($required_meet==false){
            $(".add_to_cart").attr("disabled",true);
        } else $(".add_to_cart").attr("disabled",false);
                
        processAjax('priceFormat','price='+$item_total + "&target=.item-summary",'');
    };
    
    var addToCart = function(){
         $item_size_id = $('input[name="size_uuid"]:checked').val();
         $item_token = $(".item_s").attr("data-item_token");	 
         $sub_item_id = 0;
         $sub_item_qty = 0;
         $addon_price = 0;
         $addons_category = [];
         $addons_items = '';
         var $addon_rows = '';
          
         $('.list-addon').each(function(index, $obj){
             
             $addon_multi_option = $($obj).attr("data-multi_option");	
             $subcat_id = $($obj).attr("data-subcat_id");			
            
            if($addon_multi_option=="custom" || $addon_multi_option=="one"){
               $addon_child =  $($obj).find(".addon-items:checked");
            } else if ( $addon_multi_option=="multiple"){
               $addon_child = $($obj).find(".qty");	
            }		
            
            /*$addons_category.push({
                'subcat_id':$subcat_id,
                'multi_option':$addon_multi_option,
                'multi_option_value' : $($obj).data("multi_option_value")
            });	 */
                    
            var $x = 0;
            if ((typeof  $addon_child !== "undefined") && ( $addon_child !== null)) {
                //$addon_rows = '';		
                $addon_child.each(function(child_index, child_object){				
                    if($addon_multi_option=="multiple"){
                        $sub_item_id = $(child_object).attr("data-id");
                        $sub_item_qty =  parseInt( $(child_object).html() ) ;		
                        $addon_price = !empty($global_addon_items[$sub_item_id])? parseFloat($global_addon_items[$sub_item_id].price) : 0;			
                    } else {
                        $sub_item_id = $(child_object).val();
                        $addon_price = !empty($global_addon_items[$sub_item_id])? parseFloat($global_addon_items[$sub_item_id].price) : 0;					
                        $sub_item_qty = 1;
                    }			
                    
                    if($sub_item_qty>0){		
                        $addon_rows+='{"sub_item_id":'+$sub_item_id+', "sub_item_qty":'+$sub_item_qty+'},';					
                    } 
                    
                });	// loop				
                                            
                
            }
            
            if(!empty($addon_rows)){			
                $addon_rows = $addon_rows.slice(0, -1);	     					
                $addons_items+= '"'+$subcat_id+'":['+$addon_rows+'],';
                $addon_rows = '';
                
                $addons_category.push({
                    'subcat_id':$subcat_id,
                    'multi_option':$addon_multi_option,
                    'multi_option_value' : $($obj).attr("data-multi_option_value")
                });
                
            }
             
         }); // loop
         
         $addons_items = $addons_items.slice(0, -1);	    
         $addons_items = '{'+$addons_items+'}';
         $addons_items = JSON.parse( $addons_items );
         dump($addons_items);
         
         var $params = {
             'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
             'item_token':$item_token,
             'item_size_id':$item_size_id,	 	
             'addons_category':$addons_category,
             'addons_items':$addons_items
         };		 
         $params = JSON.stringify($params);	   	   
         processAjax('addCartItems',$params,'add_to_cart',1,1,'PUT','json');		
    };
    
    
    var setCookie = function(cname, cvalue, exdays){
      const d = new Date();
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
      let expires = "expires="+d.toUTCString();
      document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    
    var getCookie = function(cname, cvalue, exdays){
      let name = cname + "=";
      let ca = document.cookie.split(';');
      for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }
    
    /*
      VUE STARTS HERE 
    */
    
    /*
    ===============================================================================
                           START COMPONENTS HERE
    ===============================================================================
    */
    
    /*
      COMPONENTS SEARCH
    */
    const ComponentsSearch = {
        props: ['title','label','next_url','auto_generate_uuid'],
        data(){
            return {
               q: '',
               awaitingSearch : false,		   
               show_list : false,
               data : [],
               error : [],		
               autosaved_addres : true,		   
            };	
        },	
        watch: {
            q(newsearch,oldsearch){
                if (!this.awaitingSearch) {
                                  
                  if(empty(newsearch)){
                      return false;
                  }			
                  
                  this.show_list = true;
                  
                  setTimeout(() => {
                    
                      var $params; var timenow = 2;
                     $params="q=" + this.q;
                     $params+=addValidationRequest();
                     ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/getlocation_autocomplete",
                         method: "POST",
                         dataType: "json",
                         data: $params,
                         contentType : $content_type.form ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: data => {
                              if(ajax_request[timenow] != null) {	
                                 ajax_request[timenow].abort();
                              }
                         }
                     });
                     
                     ajax_request[timenow].done( data => {	 	     	    
                         if ( data.code==1){
                             this.data = data.details.data;
                         } else {
                             this.data = [];
                         }		 	    		 	    
                     });	// ajax done
                    
                     ajax_request[timenow].always( data => {	 	    	
                         this.awaitingSearch = false;
                    });		
                                
                  }, 1000); // 1 sec delay
                }	        
                
                this.data = [];
                this.awaitingSearch = true;
            },		
        },
        methods: {
           showList(){
               this.show_list = true;
            },
            clearData(){
                this.data = [];
                this.q = '';
            }, 		
            getLocationDetails(location_id){
                
                this.show_list = false;
                var $params; var timenow = 3;
                 $params="id=" + location_id;
                 $params+="&autosaved_addres=" + this.autosaved_addres;
                 $params+="&auto_generate_uuid=" + this.auto_generate_uuid;
                 $params+="&cart_uuid=" + getCookie('cart_uuid');
                 
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getLocationDetails",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.awaitingSearch = true;	 	    	
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){	 	    
                         this.q = data.details.data.address.formatted_address;		 	    		
                         if(this.auto_generate_uuid==true || this.auto_generate_uuid=="true"){
                             setCookie('cart_uuid',data.details.cart_uuid,30);
                         }	 	    	
                         window.location.href = this.next_url; 	 	    		
                     } else {
                         this.error[0] = data.msg;
                     }		 	    		 	    
                 });	// ajax done
                
                 ajax_request[timenow].always( data => {	 	    	
                     this.awaitingSearch = false;
                });		
                 
            },			
        }, 
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
        },
        template: `	
        <div class="position-relative search-geocomplete"> 	
           <div v-if="!awaitingSearch" class="img-20 m-auto pin_placeholder icon"></div>
           <div v-if="awaitingSearch" class="icon" data-loader="circle"></div>    
                  
           <input @click="showList" v-model="q" class="form-control form-control-text" 
           :placeholder="label.enter_address" >
           
           <div v-if="hasData" @click="clearData" class="icon-remove"><i class="zmdi zmdi-close"></i></div>
           <div v-if="!hasData" class="search_placeholder pos-right img-20"></div>
          </div>
        
           <div class="search-geocomplete-results" v-if="show_list">		      
            <ul class="list-unstyled m-0 border">
             <li v-for="items in data">			      
              <a href="javascript:;"  @click="getLocationDetails(items.id)" >
               <h6 class="m-0">{{items.addressLine1}}</h6>
               <p class="m-0 text-grey">{{items.addressLine2}}</p>
              </a>
             </li>	     
            </ul>
       </div>
        `
    };
    /*END COMPONENTS SEARCH*/
    
    
    /*
    COMPONENTS CHANGE ADDRESS
    */
    const changeAddressComponent = {
        props: ['label','addresses','location_data'],
        data(){
            return {
               q: '',
               awaitingSearch : false,		   
               show_list : false,
               data : [],
               error : [],	
               autosaved_addres : true,
            };	
        },	
        watch: {
            q(newsearch,oldsearch){
                if (!this.awaitingSearch) {
                                  
                  if(empty(newsearch)){
                      return false;
                  }			
                  
                  this.show_list = true;
                  
                  setTimeout(() => {
                    
                      var $params; var timenow = 2;
                     $params="q=" + this.q;
                     $params+=addValidationRequest();
                     ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/getlocation_autocomplete",
                         method: "POST",
                         dataType: "json",
                         data: $params,
                         contentType : $content_type.form ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: data => {
                              if(ajax_request[timenow] != null) {	
                                 ajax_request[timenow].abort();
                              }
                         }
                     });
                     
                     ajax_request[timenow].done( data => {	 	     	    
                         if ( data.code==1){
                             this.data = data.details.data;
                         } else {
                             this.data = [];
                         }		 	    		 	    
                     });	// ajax done
                    
                     ajax_request[timenow].always( data => {	 	    	
                         this.awaitingSearch = false;
                    });		
                                
                  }, 1000); // 1 sec delay
                }	        
                
                this.data = [];
                this.awaitingSearch = true;
            },		
        },
        methods: {
           showList(){
               this.show_list = true;
            },
            clearData(){
                this.data = [];
                this.q = '';
            }, 		
            showModal(){		    
                //$('#changeAddressModal').modal('show');
                $( this.$refs.change_address_modal ).modal('show');
            },	
            closeModal(){						
                //$('#changeAddressModal').modal('hide');			
                $( this.$refs.change_address_modal ).modal('hide');
                this.$emit('afterClose');
            },
            close(){						
                //$('#changeAddressModal').modal('hide');			
                $( this.$refs.change_address_modal ).modal('hide');
            },
            getLocationDetails(location_id){
                
                this.show_list = false;
                var $params; var timenow = 3;
                 $params="id=" + location_id;
                 $params+="&autosaved_addres=" + this.autosaved_addres;
                 
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getLocationDetails",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.awaitingSearch = true;	 	    	
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                
                  ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){	 	    
                         this.q='';			 	    	 	    		
                         this.closeModal();	 
                         this.$emit('setLocation',data.details.data);
                     } else {
                         this.error[0] = data.msg;
                     }		 	    		 	    
                 });	// ajax done
                
                 ajax_request[timenow].always( data => {	 	    	
                     this.awaitingSearch = false;
                });		
                 
            },	
            setPlaceID(place_id){							
                var $params; var timenow = 4;
                 $params="place_id=" + place_id;
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/setPlaceID",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    		
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                
                  ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){	 	
                         this.$emit('setPlaceid');
                     } else {
                         this.error[0] = data.msg;
                     }		 	    		 	    
                 });	// ajax done
                
                 ajax_request[timenow].always( data => {	 	    	 	    	    
                });		
                
            },	
            editAddress(address_data){						
                //this.$emit('setLocation',address_data);				
                this.$emit('setEdit',address_data);		
            },	
            deleteAddress(address_uuid){
                
                var $params; var timenow = 5;
                 $params="address_uuid=" + address_uuid;
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/deleteAddress",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    		
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                
                  ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){	 	
                         this.$emit('loadData');
                         this.$emit('afterDelete',data);
                     } else {
                         this.error[0] = data.msg;
                     }		 	    		 	    
                 });	// ajax done
                
                 ajax_request[timenow].always( data => {	 	    	 	    	    
                });		
                
            },	
        }, 
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
        },
        template: `	  	 
          <!--changeAddressModal-->
          <div class="modal" ref="change_address_modal" id="changeAddressModal" tabindex="-1" role="dialog" aria-labelledby="changeAddressModal" aria-hidden="true">
           <div class="modal-dialog" role="document">
             <div class="modal-content">  <!--opened-->    
               <div class="modal-body">
               
                  <a href="javascript:;" @click="closeModal" 
                 class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
    
                 <h4 class="m-0 mb-3 mt-3">{{label.title}}</h4>  		  
                 
                 <div  v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
                 </div>   
                        
                 <div class="position-relative search-geocomplete"> 	
                  <div v-if="!awaitingSearch" class="img-20 m-auto pin_placeholder icon"></div>
                  <div v-if="awaitingSearch" class="icon" data-loader="circle"></div>    
                  <input @click="showList" v-model="q" class="form-control form-control-text" 
                  :placeholder="label.enter_address" >		     
                  <div v-if="hasData" @click="clearData" class="icon-remove"><i class="zmdi zmdi-close"></i></div>
                 </div>
               
                  <div class="search-geocomplete-results" v-if="show_list">		      
                    <ul class="list-unstyled m-0 border">
                     <li v-for="items in data">			      
                      <a href="javascript:;"  @click="getLocationDetails(items.id)" >
                       <h6 class="m-0">{{items.addressLine1}}</h6>
                       <p class="m-0 text-grey">{{items.addressLine2}}</p>
                      </a>
                     </li>	     
                    </ul>
                   </div>
                 
               </div> <!--modal body-->
               <div class="modal-footer justify-content-start">
               
                <ul class="m-0 list-unstyled w-100 list-with-icon-button">
                <li v-for="valadd in addresses" class="row align-items-center no-gutters" >	 
                                       
                  <div class="col-1" @click="setPlaceID(valadd.place_id)" ><i class="zmdi zmdi-pin icon-25"></i></div>
                  <div class="col-9" @click="setPlaceID(valadd.place_id)" >
                     <h5 class="m-0">{{valadd.address.address1}}</h5>
                     <p class="m-0">{{valadd.address.formatted_address}}</p>   
                  </div>
                                
                  <div class="col-2 ">	           
                    <div class="float-right">
                    <a href="javascript:;" @click="editAddress(valadd)" class="rounded-pill rounded-button-icon d-inline-block mr-2">
                      <i class="zmdi zmdi-edit"></i>
                    </a>
                    
                    <a href="javascript:;" v-if="location_data.address_uuid != valadd.address_uuid " @click="deleteAddress(valadd.address_uuid)" 
                    class="rounded-pill rounded-button-icon d-inline-block">
                    <i class="zmdi zmdi-close"></i>
                    </a>
                    
                    </div>	          
                  </div>
                </li>	        	      	       
                </ul> 
               
               </div> <!--footer-->
            </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->                    
        `
    };
    
    /*
    END COMPONENTS CHANGE ADDRESS
    */
    
    
    /*
      COMPONENTS ADDRESS FORM
    */
    const ComponentsAddress = {
         props: ['label','set_place_id','cmaps_config'],	 
         data(){
            return {				
                data:[],			
                is_loading : false,
                error:[],
                cmaps : undefined,
                cmaps_full : false,
                //cmaps_config : [],
                location_name : '',
                delivery_options : '',
                address_label : '',
                delivery_instructions : '',
                delivery_options_data : [],
                address_label_data : [],
                address1 : '',
                formatted_address : '',			
                new_coordinates : [],
                inline_edit : false
            };
         },	 
         mounted() {
             autosize( document.getElementById("delivery_instructions") );  
             this.getAddressAttributes();
         },
         computed: {
            DataValid(){				
                if(Object.keys(this.data).length>0){
                   return true;
                } 		    		    
                return false;
            },		
            DataValidNew(){
                if(Object.keys(this.new_coordinates).length>0){
                   return true;
                } 		    		    
                return false;
            },
         }, 
         methods :{
             show( address_uuid ){   
                this.getAdddress( address_uuid );	 	
                $( this.$refs.modal_address ).modal('show');
               //$('#ModalAddress').modal('show');
            },	 
            showWithData(data){        	
                //$('#ModalAddress').modal('show');
                $( this.$refs.modal_address ).modal('show');
                
                this.data = data;
                this.address1 = data.address.address1;
                this.formatted_address = data.address.formatted_address;
                this.loadMap();
            },	
             close(){          
               //$('#ModalAddress').modal('hide');
               $( this.$refs.modal_address ).modal('hide');
            },
            closeModal(){
               //$('#ModalAddress').modal('hide');
               $( this.$refs.modal_address ).modal('hide');
               this.$emit('afterClose');
            },	 
            getAddressAttributes(){
                
                var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getAddressAttributes",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	   
                
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){	 	
                         this.delivery_options_data = data.details.delivery_option;
                         this.address_label_data = data.details.address_label;
                         this.delivery_options = data.details.default_atts.delivery_options;
                         this.address_label = data.details.default_atts.address_label;
                         //this.cmaps_config = data.details.maps_config;
                     } else {
                         this.delivery_options_data = [];
                         this.address_label_data = [];
                         this.delivery_options = '';
                         this.address_label = '';
                     } 	    
                });		
                
                ajax_request[timenow].always( data => {
                     this.is_loading = false;
                });	
                 
            },	
            getAdddress(address_uuid){
                            
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
                  'address_uuid': address_uuid,
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getAdddress",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	 
                 
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){
                         this.data = data.details.data;	 	        								
                        this.location_name = data.details.data.attributes.location_name; 
                        this.delivery_options = data.details.data.attributes.delivery_options;
                        this.address_label = data.details.data.attributes.address_label;
                        this.delivery_instructions = data.details.data.attributes.delivery_instructions;	
                        this.address1 = data.details.data.address.address1;
                        this.formatted_address = data.details.data.address.formatted_address;
                        this.loadMap();
                     } else {
                         this.data = [];	 	        
                         this.location_name = '';
                         this.delivery_options ='';
                         this.address_label = '';
                         this.delivery_instructions = '';
                         this.address1 = '';
                         this.formatted_address = '';
                     }	 	    
                 });
                 
                 ajax_request[timenow].always( data => {
                     this.is_loading = false;
                });	          	
            },	
            loadMap(){                  
               switch (this.cmaps_config.provider){
                     case "google.maps":	           	  
                    if (window.google == null) {
                        new Promise((resolve) => {	    	
                            const doc = window.document;
                            const scriptId = "googlemap-library";
                            const scriptTag = doc.createElement("script");
                            scriptTag.id = scriptId;	                
                            scriptTag.setAttribute("src", "https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=" + this.cmaps_config.key  );
                            doc.head.appendChild(scriptTag);
                            
                            scriptTag.onload = () => {	
                               dump("script added");
                               resolve(); 
                            };					                
                         }).then(() => {
                            this.renderMap();
                         });       	   	   
                    } else {
                        this.renderMap();
                    }    	
                             
                     break;
               }
            },
            renderMap(){   
                dump("renderMap")     	
                switch(this.cmaps_config.provider){
                    case "google.maps":	
                      if ((typeof  this.cmaps !== "undefined") && ( this.cmaps !== null)) {	
                           this.removeMarker();
                      } else {        		  	 
                           this.cmaps = new google.maps.Map( this.$refs.cmaps , {
                            center: { lat: parseFloat(this.data.latitude), lng: parseFloat(this.data.longitude) },
                            zoom: parseInt(this.cmaps_config.zoom) ,
                            disableDefaultUI: true,
                         });		
                      }        	
                                                         
                     this.addMarker({
                        position: { lat: parseFloat(this.data.latitude) , lng: parseFloat(this.data.longitude) },
                        map: this.cmaps,
                        icon : this.cmaps_config.icon,
                        animation: google.maps.Animation.DROP,
                     });	        		 
                    break;
                }
            },
            addMarker(properties){
                switch(this.cmaps_config.provider){
                    case "google.maps":				   
                       cmaps_marker = new google.maps.Marker(properties);
                       this.cmaps.panTo(new google.maps.LatLng(properties.position.lat,properties.position.lng));
                     break;
                }
            },	
            removeMarker(){
                switch(this.cmaps_config.provider){
                    case "google.maps":
                      cmaps_marker.setMap(null);
                    break;
                }			
            },	
            adjustPin(){
                this.new_coordinates = [];
                this.cmaps_full = true; this.removeMarker();
                
                this.addMarker({
                    position: { lat: parseFloat(this.data.latitude) , lng: parseFloat(this.data.longitude) },
                    map: this.cmaps,
                    icon : this.cmaps_config.icon,
                    animation: google.maps.Animation.DROP,
                    draggable:true,
                });
                
                cmaps_marker.addListener('dragend', event => {	 		    	
                    this.new_coordinates = {
                      'lat': event.latLng.lat(),
                      'lng': event.latLng.lng(),
                    };		        
                });
                
            },	 
            cancelPin(){			
                this.error = [];
                this.cmaps_full=false; 
                this.removeMarker();
                this.addMarker({
                    position: { lat: parseFloat(this.data.latitude) , lng: parseFloat(this.data.longitude) },
                    map: this.cmaps,
                    icon : this.cmaps_config.icon,
                    animation: google.maps.Animation.DROP,
                });		
            },	
            setNewCoordinates(){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   
                   'lat' : this.data.latitude,		   
                   'lng' : this.data.longitude,
                   'new_lat' : this.new_coordinates.lat,
                   'new_lng' : this.new_coordinates.lng,
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkoutValidateCoordinates",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	
                         this.is_loading = true; 	
                         this.error = [];    			 	    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                      if ( data.code==1){
                          this.data.latitude = this.new_coordinates.lat;
                        this.data.longitude = this.new_coordinates.lng;  
                        this.cmaps_full=false; 			
                        this.removeMarker();
                        this.addMarker({
                            position: { lat: parseFloat(this.data.latitude) , lng: parseFloat(this.data.longitude) },
                            map: this.cmaps,
                            icon : this.cmaps_config.icon,
                            animation: google.maps.Animation.DROP,
                        });	    	 
                      } else {
                          this.error = data.msg;
                          setTimeout(function() {								
                           location.href = "#error_message";   
                        }, 1); 
                      }	 	    	 	     
                });		
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });		
            },	 
            save(){
                 
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'address_uuid': this.data.address_uuid,
                   'location_name' : this.location_name,
                   'delivery_instructions' : this.delivery_instructions,
                   'delivery_options' : this.delivery_options,
                   'address_label' : this.address_label,
                   'latitude' : this.data.latitude,
                   'longitude' : this.data.longitude,
                   'address1' : this.address1,
                   'formatted_address' : this.formatted_address,
                   'set_place_id' : this.set_place_id,
                   'data': this.data
                };	 	
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/SaveAddress",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	
                         this.is_loading = true; 	 
                         this.error = [] ;
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                      if ( data.code==1){	 	    	 	 
                          this.close();
                          this.$emit('afterSave',true);
                      } else {
                          this.error = data.msg;
                          setTimeout(function() {								
                           location.href = "#error_message";   
                        }, 1);  	     		  
                      }	 	    	 	     
                });			
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });	
                
            },	 
            ConfirmDelete(address_uuid){		   		   	
                   vm_bootbox.confirm().then((result) => {		  
                       if(result){
                          this.deleteAddress(address_uuid);
                       }
                   });		   	
            },
            deleteAddress(address_uuid){
                var $params; var timenow = 5;
                 $params="address_uuid=" + address_uuid;
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/deleteAddress",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    		
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                
                  ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){	 	
                         this.close();
                          this.$emit('afterDelete',true);
                     } else {
                         this.error[0] = data.msg;
                     }		 	    		 	    
                 });	// ajax done
                
                 ajax_request[timenow].always( data => {	 	    	 	    	    
                });		
            },	 
         },	
         template: `
           <div class="modal" ref="modal_address" id="ModalAddress" tabindex="-1" role="dialog" 
        aria-labelledby="ModalAddress" aria-hidden="true"  >
           <div class="modal-dialog" role="document">
             <div class="modal-content"> 
               <div class="modal-body" >
                          
                <a href="javascript:;" @click="closeModal" 
               class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
                                      
                <h4 class="m-0 mb-3 mt-3">{{label.title}}</h4> 	 
                
                  
               <div ref="cmaps" id="cmaps" :class="{ 'map-large': cmaps_full, 'map-small': !cmaps_full }" ></div>	 
               
               <template v-if="!cmaps_full">
                          
                <div class="row mt-3" >
                 <div class="col">
                   <h5 class="m-0">{{address1}}</h5>	
                   
                   <template v-if="!inline_edit">
                       <p class="m-0">{{formatted_address}}</p>	  		       
                       <a href="javascript:;" @click="inline_edit=true">{{label.edit}} 
                       <span class="ml-1"><i class="zmdi zmdi-edit"></i></span></a>
                   </template>
                   
                 </div>
                 <div class="col text-right">
                   <button class="btn small btn-black" @click="adjustPin" :disabled="!DataValid" >{{label.adjust_pin}}</button>
                 </div>
               </div>
                <!--row-->
                
               <template v-if="inline_edit">
               <div class="mt-2 mb-2">       
                    <div class="form-label-group">    
                     <input class="form-control form-control-text" v-model="formatted_address"
                       id="formatted_address" type="text" >   
                     <label for="formatted_address">{{label.complete_address}}</label> 
                    </div>
                    
                    <a href="javascript:;" @click="inline_edit=false" class="mr-2">{{label.save}} 
                       <span class="ml-1"><i class="zmdi zmdi-check-square"></i></span></a>
                       
                    <a href="javascript:;" @click="inline_edit=false">{{label.cancel}} 
                       <span class="ml-1"><i class="zmdi zmdi-close"></i></span></a>   
                    
                </div>
               </template>
                
               <div class="forms mt-3 mb-2">
               
                <div class="form-label-group">    
                 <input class="form-control form-control-text" v-model="location_name"
                   id="location_name" type="text" >   
                 <label for="location_name">{{label.location_name}}</label> 
                </div>   
                            
                <h5 class="m-0 mt-2 mb-2">{{label.delivery_options}}</h5>       
                <select class="form-control custom-select" v-model="delivery_options">		 
                 <option v-for="(items, key) in delivery_options_data" :value="key" >{{items}}</option>      
                </select>  
                
                     
                  <h5 class="m-0 mt-3 mb-2">{{label.delivery_instructions}}</h5>      
                  <div class="form-label-group">    
                    <textarea id="delivery_instructions" 
                    style="max-height:150px;" v-model="delivery_instructions"  class="form-control form-control-text font13" 
                          :placeholder="label.notes">
                    </textarea>       
                  </div>  
                  
                <p>{{label.address_label}}</p>  
                <div class="btn-group btn-group-toggle input-group-small mb-4" >
                   <label class="btn" v-for="(items, key) in address_label_data" :class="{ active: address_label==key }" >
                     <input v-model="address_label" type="radio" :value="key" > 
                     {{ items }}
                   </label>           
                </div>
                <!--btn-group-->   
               
               </div> <!--forms--> 
           
               </template>
               
               <div id="error_message"  v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                 <p v-for="err in error" class="m-0">{{err}}</p>	    
               </div>   
                          
                
               </div> <!--modal-body-->
               
               
               <div class="modal-footer justify-content-start">
          
           <template v-if="!cmaps_full">
               <div class="border flex-fill">
                   <button class="btn btn-black w-100" @click="closeModal" >
                      {{label.cancel}}
                   </button>
               </div>
               <div class="border flex-fill">
                   <button class="btn btn-green w-100" @click="save" :class="{ loading: is_loading }" :disabled="!DataValid"  >
                      <span class="label">{{label.save}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div>
                   </button>
               </div>
               </template>
               
               <template v-else-if="cmaps_full">
                <div class="border flex-fill">
                   <button class="btn btn-black w-100" @click="cancelPin" >
                      {{label.cancel}}
                   </button>
               </div>       
               <div class="border flex-fill">
                   <button class="btn btn-green w-100" @click="setNewCoordinates" 
                   :class="{ loading: is_loading }" :disabled="!DataValidNew" >
                      <span class="label">{{label.save}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div>
                   </button>
               </div>
           </template>
           
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->
        `,
    };
    
    
    /*
    DIRECTIVE CLICK OUTSIDE
    */
    const clickOutside = {
        beforeMount: (el, binding) => {
        el.clickOutsideEvent = event => {
          // here I check that click was outside the el and his children
          if (!(el == event.target || el.contains(event.target))) {
            // and if it did, call method provided in attribute value
            binding.value();
          }
        };
        document.addEventListener("click", el.clickOutsideEvent);
       },
       unmounted: el => {
         document.removeEventListener("click", el.clickOutsideEvent);
       },
    };
    
    
    /* 
      COMPONENTS MERCHANT SERVICES 
    */
    const ComponentsMerchantServices = {
        props: ['title','label'],
        data(){
            return {				
                transaction_list :[],
                transaction : '',
                loading : false,
                charge_type : '',
                estimation : []
            };
        },
        mounted () {
            this.getMerchantServices();
        },
        updated () {		
            this.updateService();
        },
        methods :{
            getMerchantServices(){
                
                var $params; var timenow = getTimeNow();     		
                 $params="merchant_id=" + merchant_id; 		    
                 $params+="&cart_uuid=" + getCookie('cart_uuid');
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/servicesList",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                         this.loading = false;	 	    	
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){
                         this.transaction_list = data.details.data;
                         this.transaction = data.details.transaction;
                         this.charge_type = data.details.charge_type;
                         this.estimation = data.details.estimation;
                     } else {
                         this.transaction_list = [];
                         this.transaction = '';
                         this.charge_type = '';
                         this.estimation = [];
                     }					
                 });	// ajax done
                 
                 ajax_request[timenow].always( data => {
                    this.loading = false;  
                });		    
                 
            },   
            updateService(){		 	
                if(!empty(vue_cart.cart_uuid)){
                    
                    var $params; var timenow = getTimeNow(); 	
                     $params="cart_uuid=" + vue_cart.cart_uuid;
                     $params+="&transaction_type=" + this.transaction;
                     
                     $params+=addValidationRequest();
                     ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/updateService",
                         method: "POST",
                         dataType: "json",
                         data: $params,
                         contentType : $content_type.form ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: function( xhr ) {
                              if(ajax_request[timenow] != null) {	
                                 ajax_request[timenow].abort();
                              }
                         }
                     });
                     
                    ajax_request[timenow].done( data => {	 	     	    
                         this.$emit('afterUpdate',this.transaction);
                     });	// ajax done
                    
                }
            },	    	
        },
        template: ` 		
        <div class="btn-group-rounded btn-group btn-group-toggle mt-3" >
          <label class="btn d-flex"
           v-for="trans in transaction_list" :class="{ active: transaction==trans.service_code }" >
             <input type="radio" :value="trans.service_code" v-model="transaction" > 
               <div class="align-self-center">
                 <div class="bold">{{ trans.service_name }}</div>
                 
                 <template v-if="estimation[trans.service_code]">
                   <template v-for="(item,index) in estimation[trans.service_code] ">
                   
                     <template v-if="trans.service_code=='delivery'">
                         <template v-if="item.charge_type==charge_type">	              
                          <p class="m-0">{{item.estimation}} {{label.label}}min</p>
                         </template>
                     </template>
                     <template v-else> 
                         <template v-if="item.charge_type=='fixed'">	              
                          <p class="m-0">{{item.estimation}} {{label.min}}</p>
                         </template>
                     </template>
                     
                   </template>
                 </template>
                 
               </div>
           </label>           
        </div> 
        `,
    };
    
    
    /*
      COMPONENTS RECAPCHA
      https://github.com/bbonch/vue3-recaptcha2
    */
    
    const componentsRecaptcha = {
        props: ['sitekey','size','theme','is_enabled'],
        data() {
            return {
                recaptcha: null,            
            }
        },
        mounted() {    	    	
            if(this.is_enabled==1 || this.is_enabled=="true" || this.is_enabled==true){
               this.initCapcha();
            }
        },
        methods: {
           initCapcha(){       	
                  if (window.grecaptcha == null) {
                        new Promise((resolve) => {
                    window.recaptchaReady = function () {                    	
                        resolve();
                    };
    
                    const doc = window.document;
                    const scriptId = "recaptcha-script";
                    const scriptTag = doc.createElement("script");
                    scriptTag.id = scriptId;
                    scriptTag.setAttribute("src", "https://www.google.com/recaptcha/api.js?onload=recaptchaReady&render=explicit");
                    doc.head.appendChild(scriptTag);
                 }).then(() => {
                    this.renderRecaptcha();
                 });       	   	   
                  } else {
                         this.renderRecaptcha();
                  }              	
           },    
           renderRecaptcha(){       	  
                 this.recaptcha = grecaptcha.render( this.$refs.recaptcha_target , {
                  'sitekey' : this.sitekey,
                  'theme': this.theme,
                  'size': this.size,
                  'tabindex': this.tabindex,
                  'callback': (response) => this.$emit("verify", response),
                  'expired-callback': () => this.$emit("expire"),
                  'error-callback': () => this.$emit("fail")
              });
           },
           reset() {
              grecaptcha.reset(this.recaptcha);
           }
        },
        template: `	
        <div class="mb-2 mt-2" ref="recaptcha_target"></div>
        `
    };
    
    
    /*
      COMPONENTS PHONE NUMBER
    */
    const ComponentsPhoneNumber = {
        props: ['label','mobile_number','mobile_prefix','default_country','only_countries'],	
        emits: ['update:mobile_number','update:mobile_prefix'],
        data(){
           return {
              data : [],		  
              country_flag : ''
           };	
        },	
        updated () {				
            
        },
        mounted () {
            this.getLocationCountries();
        },  
        methods :{  
           getLocationCountries(){
               
                   var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'default_country': this.default_country,
                   'only_countries': this.only_countries
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getLocationCountries",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	     
               
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){
                         this.data = data.details.data;
                         this.country_flag = data.details.default_data.flag;	 	    		
                         this.$emit('update:mobile_prefix', data.details.default_data.phonecode );
                     } else { 	    		
                         this.data = [];
                         this.country_flag='';
                         this.mobile_prefix='';
                     } 	    
                });		
                
                ajax_request[timenow].always( data => { 	    	    
                     
                });	
            
           },	   
           setValue(data){
                 this.country_flag = data.flag;       	  
                 this.$emit('update:mobile_prefix', data.phonecode );       	
                 this.$refs.ref_mobile_number.focus();  
           },   
        },    	
        template: `					
         <div class="inputs-with-dropdown d-flex align-items-center mb-3" >
            <div class="dropdown">
              <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img v-if="country_flag" :src="country_flag">
              </button>
              <div class="dropdown-menu" >		    
                <a v-for="item in data" @click="setValue(item)"
                href="javascript:;"  class="dropdown-item d-flex align-items-center">
                  <div class="mr-2">
                    <img :src="item.flag">
                  </div>
                  <div>{{item.country_name}}</div>
                </a>		    
              </div>
            </div> <!--dropdown-->
            
            <div class="mr-0 ml-1" v-if="mobile_prefix">+{{mobile_prefix}}</div>
            <input type="text"  v-maska="'###################'"  ref="ref_mobile_number"
            :value="mobile_number" @input="$emit('update:mobile_number', $event.target.value)" >
            
        </div> <!--inputs-->
        `
    };
    
    
    
    /*COMPONENTS CONFIRM AND ALERT*/
    const Componentsbootbox = {
        props: ['label','size'],
        methods :{
            confirm(){
                
                bootbox.confirm({ 
                    size: this.size,
                    title : this.label.confirm ,
                    message: this.label.are_you_sure,
                    centerVertical: true,
                    animate: false,
                    buttons: {
                        cancel: {
                           label: this.label.cancel,
                           className: 'btn btn-black small pl-4 pr-4'
                        },
                        confirm: {
                            label: this.label.yes,
                            className: 'btn btn-green small pl-4 pr-4'
                        },
                    },
                    callback: result => {				    	
                        this.$emit('callback',result);	    		    
                    }
                });	
                
            },
            alert(message,options){
                bootbox.alert({
                       size: !empty(options.size)?options.size:'' ,			  
                       closeButton: false,  
                    message: message ,
                    animate: false,
                    centerVertical: true,
                     buttons: {
                         ok:{
                           label: this.label.ok,	
                           className: 'btn btn-green small pl-4 pr-4'
                         }			    
                     }
                   });		
            },	
        },
    };
    
    const loadingBox = {
        props: ['message','donnot_close'],
         data(){
              return {
                 new_message : ''
              }
        },
        methods :{		
            show(){			
                $('#loadingBox').modal('show');
            },
            close(){
                $('#loadingBox').modal('hide');
            },		
            setMessage(new_message){
                this.new_message = new_message;
            },
        },
        template:`
        <div class="modal" id="loadingBox" tabindex="-1" role="dialog" aria-labelledby="loadingBox" aria-hidden="true"
        data-backdrop="static" data-keyboard="false" 	 
         >
           <div class="modal-dialog modal-dialog-centered modal-loadingbox" role="document">
             <div class="modal-content">
                 <div class="modal-body">
                    <div class="loading mt-2">
                      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
                    </div>
                    <p class="text-center mt-2">
                      <div v-if="!new_message">{{message}}</div>
                      <div v-if="new_message">{{new_message}}</div>
                      <div>{{donnot_close}}</div>
                    </p>	            
                 </div>
               </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->      
        `
    };
    
    
    
    /*
    COMPONENTS REVIEW MODAL
    */
    var tagify,tagify2,$dropzone;
    const ComponentsReview = {
      components: {     
        'star-rating' : VueStarRating.default,
      },
      props: ['label','accepted_files','max_file'],
      data(){
          return {
              upload_images : [],
              required_message :[],
              rating_value : '', 
              review_content : '',  
              as_anonymous : false,    
              order_uuid : '', 
              is_loading : false,
              error : [] ,
              is_reset : false,		
          };
      },
      mounted () {
           tagify = new Tagify ( document.querySelector(".tags_like") );
           tagify2 = new Tagify ( document.querySelector(".tags_not_like") );
           autosize( document.getElementById("review_content") );  	
           this.initDropZone(this.label , this);  	 
      },
      methods :{
          show(){  		  		  		
             $('#reviewModal').modal('show'); 		
         },	 
         close(){ 		
               $( this.$refs.review_modal ).modal('hide'); 	  	
               Dropzone.forElement('#dropzone_multiple').removeAllFiles(true);
         },	 
         reset(){ 		 		 		
             Dropzone.forElement('#dropzone_multiple').files = []; 
             $('.dz-preview').remove();
             $('.dropzone').removeClass('dz-started');  		
             this.upload_images = [];
             this.rating_value = '';
             this.review_content = '';
             this.as_anonymous = false;
             this.order_uuid  = '';
             this.error = [];
             tagify.removeAllTags();
             tagify2.removeAllTags(); 		 		
             
         },
        initDropZone(label, object){    	
            var $upload_params = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	    		
            };    
            Dropzone.autoDiscover = false;
            $dropzone = $("#dropzone_multiple").dropzone({ 
                dictDefaultMessage  : label.dictDefaultMessage,
                dictFallbackMessage  : label.dictFallbackMessage ,
                dictFallbackText  : label.dictFallbackText,
                dictFileTooBig  : label.dictFileTooBig,
                dictInvalidFileType : label.dictInvalidFileType,
                dictResponseError : label.dictResponseError,
                dictCancelUpload  : label.dictCancelUpload,
                dictCancelUploadConfirmation  : label.dictCancelUploadConfirmation,
                dictRemoveFile : label.dictRemoveFile,
                dictMaxFilesExceeded : label.dictMaxFilesExceeded,
                paramName: "file", 		
                url: ajaxurl+"/uploadReview" ,			
                params: $upload_params,
                addRemoveLinks : true,		
                maxFiles : parseInt(this.max_file),				
                acceptedFiles: this.accepted_files ,
                autoProcessQueue : true,
                init: function() {		
                    var instance = this;	
                    instance.on("maxfilesexceeded", function(file) {
                        //component_alert.alert( [label.max_file_exceeded]  );					
                        notify(label.max_file_exceeded,'error');
                        instance.removeFile(file);										
                    });				
                    instance.on("removedfile", function (file) {											
                        dump("removedfile");
                        object.removeUpload(file.upload_uuid);					
                        //file.previewElement.remove();
                    });			
                    instance.on("success", (file,response) => {			                	                
                        dump(response);
                        var $resp = response;
                        if($resp.code==1){
                            file.upload_uuid = $resp.details.id;
                            object.upload_images.push( $resp.details );
                        } else {	          
                            var $resp = JSON.parse(response);
                            notify($resp.msg,'error');
                            instance.removeFile(file);
                        }				
                    });
                    instance.on("addedfile", function() {
                        dump("addedfile");
                    });	
                },			
            });	    	    
        }, 	
        removeUpload(id){    	
            
            return new Promise((resolve, reject) => {	      	      
                     
                      var $params = {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),					   
                       'id':id,
                    };							
                    var timenow = getTimeNow();
                    
                    ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/removeReviewImage",
                         method: "PUT",
                         dataType: "json",
                         data: JSON.stringify($params) ,
                         contentType : $content_type.json ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: data => {
                             this.is_loading = true;
                              if(ajax_request[timenow] != null) {	
                                  ajax_request[timenow].abort();
                                  this.is_loading = false;
                              }
                         }
                     });
                     
                     ajax_request[timenow].done( data => {
                         this.is_loading = false;		 	    	
                          resolve(true);		 	     	
                          if(data.code==1){
                             //this.remove(data.details);
                          }
                     });	// ajax done 
                     
                    ajax_request[timenow].always( data => {	 	    	  
                       this.is_loading = false;
                   });				
                     
              });     	
        },
        remove(uuid){    	
            if(this.upload_images){
                this.upload_images.forEach((item,index) => {    			
                    if ( item.id==uuid){    	
                        dump("index remove=>" + index);    				
                        this.upload_images.splice(index,1);
                    }    		
                });
            }    
        }, 
        getTag(data){
            var $tag_data = [];
            if(data){    		    
                data.forEach((data,index) => {
                    $tag_data.push( data.value );
                });
            }
            return $tag_data;
        }, 
        addReview(){    	
                        
            var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
               'order_uuid' : this.order_uuid,
               'review_content':this.review_content,		 					  
               'rating_value':this.rating_value,
               'as_anonymous':this.as_anonymous,
               'tags_like': this.getTag(tagify.value),
               'tags_not_like': this.getTag(tagify2.value),		   
               'upload_images':this.upload_images,
            };				
            var timenow = 1;
            
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/addReview",
                 method: "PUT",
                 dataType: "json",
                 data: JSON.stringify($params) ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => {
                     this.is_loading = true;
                     this.error = [];
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                          this.is_loading = false;
                      }
                 }
             });
             
              ajax_request[timenow].done( data => {
                  dump(data); 	     
                  if(data.code==1){ 	    
                      notify(data.msg); 			     
                      this.reset();
                      $( this.$refs.review_modal ).modal('hide');
                  } else {
                      this.error = data.msg;
                      setTimeout(function() {								
                        location.href = "#error_message";   
                    }, 1);  	     		 
                  } 	     
              });
             
              ajax_request[timenow].always( data => {	 	    	  
                   this.is_loading = false;
             });		
        },  
        deleteAllImages(){
            
            if(this.upload_images.length<=0){
                return false;
            }
        
            var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   
               'upload_images':this.upload_images,
           };	
           
            var timenow = getTimeNow();			    
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/removeAllReviewImage",
                 method: "PUT",
                 dataType: "json",
                 data: JSON.stringify($params) ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	    		
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort(); 	    	 		
                      }
                 }
             });
             
             ajax_request[timenow].done( data => {	 	    	
                 if(data.code==1){
                     this.upload_images = [];
                     Dropzone.forElement('#dropzone_multiple').removeAllFiles(true);
                 } 	    
            });	// ajax done 
        },  
      },
      template: `
      <div class="modal" ref="review_modal" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModal" aria-hidden="true"  data-backdrop="static" data-keyboard="false" >
           <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">     
                    
                      
              <div class="modal-body" >    
              
              <a href="javascript:;" @click="close" 
                class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a> 
              
              <h4 class="bold m-0 mb-3 mt-3">{{label.write_review}}</h4> 	 
                                                   
             <star-rating  
             v-model:rating="rating_value"
             :star-size="30"
             :show-rating="false" 
             @update:rating="rating_value = $event"
             >
             </star-rating>	      	    		
               
             <h5 class="m-0 mt-3 mb-2">{{label.what_did_you_like}}</h5>     
             <input type="text" name="tags_like" class="tags_like" id="tags_like" :placeholder="label.search_tag"> 
             
             <h5 class="m-0 mt-2 mb-2">{{label.what_did_you_not_like}}</h5>     
             <input type="text" name="tags_not_like" class="tags_not_like" id="tags_not_like" :placeholder="label.search_tag"> 
                       
             <h5 class="m-0 mt-3 mb-2">{{label.add_photo}}</h5>      
             <div class="mb-3">
                 <div class="dropzone dropzone_container" id="dropzone_multiple" data-action="gallery_create">
                   <div class="dz-default dz-message">
                    <i class="fas fa-cloud-upload-alt"></i>
                     <p>{{label.drop_files_here}}</p>
                    </div>
                </div> 
             </div>
                              
            <!-- <div v-if="upload_images.length>0" class="row gallery m-0">
              <div v-for="images in upload_images" class="col-lg-4 col-md-4 col-sm-12 mb-0 mb-lg-0  p-1">
                <div class="position-relative">
                   <img :src="images.url_image">
                   <a  href="javascript:;"  
                   @click="removeUpload(images.id)" data-dz-remove 
                   class="remove btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-plus"></i></a>
                </div>
              </div>	      
             </div>-->
             
             <h5 class="m-0 mt-2 mb-2">{{label.write_your_review}}</h5>      
             <div class="form-label-group">    
               <textarea id="review_content" class="form-control form-control-text font13" 
               :placeholder="label.review_helps"
               v-model="review_content"
               style="max-height:150px;"
               >
               </textarea>       
            </div>
                    
            <div  class="custom-control custom-checkbox flex-grow-1">            
              <input type="checkbox" class="custom-control-input"
              name="as_anonymous" value="1" 
              id="as_anonymous"	    
              v-model="as_anonymous"  
              >	  
              <label class="custom-control-label font14 bold" for="as_anonymous" >
               {{label.post_review_anonymous}}
              </label>
            </div>    
            
                    
            <div id="error_message" v-cloak v-if="error.length>0" class="alert alert-warning mb-2 mt-2" role="alert">
              <p v-cloak v-for="message in error" class="m-0">{{message}}</p>	    
            </div>  
                
              </div> <!--modal body-->
              
              <div class="modal-footer justify-content-start">
                <button class="btn btn-black w-100" @click="addReview" :class="{ loading: is_loading }" >               
                   <span class="label">{{label.add_review}}</span>
                   <div class="m-auto circle-loader" data-loader="circle-side"></div>
                </button>
              </div> <!--footer-->
           
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->  
      `,	
    };
    
    
    /*
       COMPONENTS MONEY   
    */
    const ComponentsMoney = {
        props: ['ajax_url', 'amount'],
        data(){
           return {						
             data : 0,
             config : JSON.parse(money_config)
             /*config : {
                prefix : '$',
                suffix: '',
                thousands : ',',
                decimal : '.',
                precision : 2,
            },		*/
           };
        },   
        mounted() {	   	     	     	     	     	    
              this.data = window["v-money3"].format(this.amount, this.config);   	   
        },    
        updated(){   	
              this.data = window["v-money3"].format(this.amount, this.config);
        },    
        methods :{
            formatNumber(data){
                return window["v-money3"].format(data, this.config);
            },
        },
        template:`	
        {{data}}
        `
    };
    
    var MoneyFormat = function(amount){	
        return window["v-money3"].format(amount, JSON.parse(money_config));
    };
    
    /*
    ===============================================================================
                           END OF ALL COMPONENTS
    ===============================================================================
    */
    
    
    /*
     LOADING BOX
    */
    const vm_loading = Vue.createApp({
      components: {
        'components-loading-box': loadingBox,    
      },
    }).mount('#vue-loading-box');	
    
    
    /*
      HOME SEARCH
    */
    const vm_search = Vue.createApp({
       components: {    
           'component-home-search' : ComponentsSearch,  	  
       },
    }).mount('#vue-home-search');
    
    const vm_search_mobile = Vue.createApp({
        components: {    
            'component-home-search' : ComponentsSearch,  	  
        },
     }).mount('#vue-home-search-mobile');
    
    
    /*
    COMPONENTS CAROUSEL 
    */
    //let owl;
    const ComponentsCarousel = {
        props: ['title','featured_name','settings','responsive'],
        data(){
            return {			   		   
               is_loading : false,
               owl : undefined,		   
               data : [],
               services : [],
               estimation : [],
               location_details : []
            };	
        },	
        mounted () {
            this.GetFeaturedLocation();
        },
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
            classObject() {
                 return {
                     'w-100' : this.settings.theme==='rounded',
                     'w-75' :  this.settings.theme==='rounded-circle'
                 }
            }
        },
        methods: {
            GetFeaturedLocation(){
                    
                 var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                  'featured_name': this.featured_name	   
                };					    
                var timenow = getTimeNow();
                $params = JSON.stringify($params);
                            
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/GetFeaturedLocation",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	 
                         this.is_loading = true; 
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 
    
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){
                         this.location_details = data.details.location_details;	 	    	
                         this.data = data.details.data;    		
                         this.services = data.details.services;  
                         this.estimation = data.details.estimation;   	    		    
                         setTimeout(() => {						
                           this.RenderCarousel();
                        }, 1); 		 	   
                     } else { 	    		
                         this.location_details = '';
                         this.data = '';
                         this.services = '';
                         this.estimation = '';
                     } 	    
                });		
                
                ajax_request[timenow].always( data => { 	    	    
                    this.is_loading = false;
                });	 	 
                
            },     
            RenderCarousel(){    		
                //this.owl = $(".owl-carousel").owlCarousel({
                this.owl = $(this.$refs.owl_carousel).owlCarousel({	
                    items: parseInt(this.settings.items),				
                    lazyLoad: this.settings.lazyLoad=="1"?true:false ,
                    loop: this.settings.loop=="1"?true:false ,
                    margin: parseInt(this.settings.margin) ,
                    nav: this.settings.nav=="1"?true:false ,
                    dots: this.settings.dots=="1"?true:false ,
                    stagePadding: parseInt(this.settings.stagePadding),
                    responsive : this.responsive,
                });
            },
            SlideNext(){    		    		
                this.owl.trigger('next.owl.carousel');
            },
            SlidePrev(){    		
                this.owl.trigger('prev.owl.carousel', [300]);
            },
        },
        template: `	
        
        <DIV class="mt-5 mb-5">
        
        <div class="row no-gutters mb-4 " v-if="hasData" >    
             <div class="col d-flex justify-content-start align-items-center ">       
               <h5 class="m-0 section-title">
               {{title}} <span>{{location_details.location_name}}</span>
               </h5>
             </div>
             <div class="col d-flex justify-content-end align-items-center ">        
                <a @click="SlidePrev" href="javascript:;" class="owl-carousel-nav prev mr-4">&nbsp;</a>
                <a @click="SlideNext" href="javascript:;" class="owl-carousel-nav next">&nbsp;</a>
             </div> <!--col-->        
        </div> <!--row-->
                     
         <div ref="owl_carousel" class="owl-carousel restaurant-carousel" :class="settings.theme" v-if="hasData"  >	  
            
           <div v-for="item in data" class="row equal align-items-center position-relative"
           :class="classObject" 
           >  
           
              <template  v-for="(cuisine,index) in item.cuisine_name"  >
                <template v-if="index <= 0">
                
                 <span class="badge mr-1 top-left" v-if="settings.theme==='rounded'" 
                 :style="'background:'+cuisine.bgcolor+';font-color:'+cuisine.fncolor"
                  >{{cuisine.cuisine_name}}</span>	      
             
                  <div class="rounded-pill badge top-right"  v-if="item.ratings.rating>0"
                  :style="'background:'+cuisine.bgcolor+';font-color:'+cuisine.fncolor"
                  >
                   {{item.ratings.rating}}
                  </div>     
                  
                 </template>
              </template>
                      
           
             <div class="w-100 list-items p-0 " >   
                <a :href="item.merchant_url" ><img :class="settings.theme" :src="item.url_logo" /></a>
             </div>
             
            <!--ROUNDED TEMPLATE-->
            <template v-if="settings.theme==='rounded'">
            <div class="d-flex justify-content-between mt-2 w-100 pl-1 pr-1 ">
              <div class="flex-col ">
               <a :href="item.merchant_url" ><h6>{{item.restaurant_name}}</h6></a>
              </div>	      
            </div> <!--flex-->
            
            <div class="d-flex justify-content-between w-100 pl-1 pr-1">
              <div class="flex-col">
              <p class="m-0">	      
                <i class="zmdi zmdi-time mr-1"></i>
                
                 <template v-if="estimation[item.merchant_id]">
                   <template v-if="services[item.merchant_id]">
                     <template v-for="(service_name,index_service) in services[item.merchant_id]"  >
                       <template v-if="index_service<=0">
                       
                        <template v-if=" estimation[item.merchant_id][service_name][item.charge_type] "> 
                           {{ estimation[item.merchant_id][service_name][item.charge_type].estimation }} <?php echo t("min")?>
                        </template>
                           
                       </template>
                     </template>
                   </template>
                 </template>
                
              </p>
              </div>
              <div class="flex-col"><p class="m-0" v-if="item.free_delivery==='1'" >{{ settings.free_delivery}}</p></div>
            </div> <!--flex-->
            </template>
            <!--END ROUNDED TEMPLATE-->
            
            <!-- ROUNDED CIRCLE TEMPLATE-->
            <template v-else >
              <div class="mt-3 text-center w-100">
                 <a :href="item.merchant_url" ><h6 class="mb-1">{{item.restaurant_name}}</h6></a>
                 
                <template  v-for="(cuisine,index) in item.cuisine_name"  >
                    <template v-if="index <= 0">
                    
                     <span class="badge"
                     :style="'background:'+cuisine.bgcolor+';font-color:'+cuisine.fncolor"
                      >{{cuisine.cuisine_name}}</span>	      
                 
                      
                     </template>
                 </template>
                 
              </div>
            </template>
            <!-- END ROUNDED CIRCLE TEMPLATE-->
             
           </div> <!-- row-->
                 
         </div><!-- owl-carousel-->
        
         </DIV>
        `
    };
    
    
    const componentCuisine = {
        props: ['data'],
        data(){
            return {
                owl : undefined,
            };
        },
        mounted(){       
           
        },
        watch: {
            data(new_data,old_data){			
                setTimeout(() => {						
                    this.renderCarousel();
                 }, 1); 		 	   
            }
        },
        methods: {
            renderCarousel(){
                this.owl = $(this.$refs.cuisine_carousel).owlCarousel({
                    loop:true,		
                    dots:false,		
                    nav:false,		
                    autoWidth:true,					   	
                });
            },
        },
        template: `			
        <div ref="cuisine_carousel"  class="owl-carousel owl-theme cuisine_carousel">
          <div v-for="(item, index) in data" class="mr-2">	   
           <a href="javascript:;" class="d-block bg-light rounded-pill"
           :class="{active : index==0 }"
           >
            {{item.cuisine_name}}
           </a>
          </div>    
       </div>
        `
    };
    
    const componentThreeSteps = {
        props: ['responsive','label'],	
        data(){
            return {
                owl : undefined,
            };
        },
        mounted(){       
          this.renderCarousel();
        },
        methods : {
            renderCarousel(){
                this.owl = $(this.$refs.carousel_three_steps).owlCarousel({
                    loop:true,		
                    dots:true,		
                    nav:false,										   	
                    responsive:{
                        0:{
                            items:1,
                            loop : false,
                        },
                        320:{
                            items:1,
                            loop : false,
                        },
                        480:{
                            items:1,
                            loop : false,
                        },
                        600:{
                            items:1,
                            loop : false,
                        },
                        700:{
                            items:2,
                            loop : false,
                        },
                        1000:{
                            items:2,
                            loop : false,
                        }
                    }
                });
            },
        },
        template: '#three-steps-ordering',
    };
    
    /*
      HOME WIDGET
    */
    const vm_home_widget = Vue.createApp({
      components: {    
         'component-carousel' : ComponentsCarousel,    	   
         'component-cuisine' : componentCuisine,
         'component-three-steps' : componentThreeSteps,
      },
      data(){
        return {				
            data_cuisine : [],		
        };
      },
      mounted () {
            this.getCuisineList(false);
      },
      methods :{
            getCuisineList(){
                
                var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
            };			
            var timenow = getTimeNow();
            $params = JSON.stringify($params);	
            
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/CuisineList",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	  
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             }); 	   
             
             ajax_request[timenow].done( data => {
                 if ( data.code==1){
                     this.data_cuisine = data.details.data_cuisine; 	 	    		
                 } else { 	    		
                     this.data_cuisine = [];
                 } 	    
            });		
            
            ajax_request[timenow].always( data => { 	    	    
                 
            });	
                
            },
      }
    }).mount('#vue-home-widgets');
    
    
    /*
    COMPONENTS SAVE STORE HEARTH BUTTON
    */
    const ComponentsSaveStore = {
        props: ['active','merchant_id'],
        data() {
            return {
              data : [],          
           }
        },
        methods :{
            SaveStore(){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
                   'merchant_id': this.merchant_id,
                };			
                var timenow = 500;
                $params = JSON.stringify($params);	
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/SaveStore",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 		 	    			    	
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	    
                 
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){	 	
                         //this.active = data.details.found;	 	    			    	
                         this.$emit('afterSave',true);	 	    		
                     } else {	 	    		
                         vm_bootbox.alert( data.msg , {} );
                     } 	    
                });
                
            },	
        },
        template:`	
          <div class="d-none d-lg-block">
            <a @click="SaveStore()"  href="javascript:;" class="font20">
                <i class="zmdi" :class="{'zmdi-favorite text-green': active, 'zmdi-favorite-outline': !active }"></i>
            </a>
          </div>
          <div class="d-block d-lg-none">
            <a @click="SaveStore()"  href="javascript:;" class="btn bg-light btn-circle rounded-pill">
                <i class="zmdi" :class="{'zmdi-favorite text-green': active, 'zmdi-favorite-outline': !active }"></i>
             </a>
          </div>
        `,
    };
    
    /*
       GET THE SELECTED DELIVERY TIME
    */
    var getChoosenDeliveryTime = function(){
        var $response = [];
        var $choosen_delivery = !empty(getCookie('choosen_delivery'))? JSON.parse(getCookie('choosen_delivery')) : [] ;
        var $count = Object.keys($choosen_delivery).length;
        if($count>0){
            $response = {
                'whento_deliver' : $choosen_delivery.whento_deliver
            };
            if($choosen_delivery.whento_deliver=="schedule"){
                $response = {
                   'whento_deliver' : $choosen_delivery.whento_deliver,
                   'delivery_date' : $choosen_delivery.delivery_date,
                   'delivery_time' : $choosen_delivery.delivery_time.start_time,
                   'delivery_time_raw' : $choosen_delivery.delivery_time,
                };
            }
            return $response;
        }
        return false;
    };
    
    
    /*
       MOBILE FILTER FOR FEED
    */
    const ComponentsFilterFeed = {
       components: {    		
         'star-rating' : VueStarRating.default,		
       }, 
       props : ['data_attributes', 'data_cuisine' ,'label'],
       data(){
         return {
            sortby : '',
            cuisine : [],		
            price_range : 0,
            max_delivery_fee : 0,	
            rating : 0,			
         };
       },
       computed: {
            hasFilter(){			
                if(!empty(this.sortby)){
                   return true;	
                }
                if(this.price_range>0){
                   return true;	
                }
                if(this.max_delivery_fee>0){			
                   return true;	
                }
                if(this.cuisine.length>0){
                   return true;	
                }
                if(this.rating>0){			
                   return true;	
                }
                return false;
            },
       },
       methods: {
            show(data){						
                $(this.$refs.filterFeed).modal('show');
            },	
            close(){
                $(this.$refs.filterFeed).modal('hide');	
            },
            clearFilter(){
                this.sortby = '';
                this.price_range = 0;
                this.max_delivery_fee = 0;
                this.cuisine = [];
                this.rating = 0;									
            },
            applyFilter(){
                this.close();
                this.$emit('afterFilter',{
                    sortby : this.sortby,
                    cuisine : this.cuisine,
                    price_range : this.price_range,
                    max_delivery_fee : this.max_delivery_fee,
                    rating : this.rating
                });	 	    		
            },
       },
       template: `   
       <div class="modal" ref="filterFeed" tabindex="-1" role="dialog" 
       aria-labelledby="filterFeed" aria-hidden="true"  >
          <div class="modal-dialog modal-full modal-dialog-scrollable" role="document">
            <div class="modal-content"> 
              <div class="modal-body" >
     
              <a href="javascript:;" @click="close" 
              class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
              
              <h5 class="mt-3">{{label.filters}}</h5>
              
              <div v-for="(sort_by, key) in data_attributes.sort_by" class="row m-0 ml-2 mb-2">
              <div class="custom-control custom-radio">
                <input v-model="sortby" :value="key" type="radio" :id="key+1"  class="custom-control-input">
                <label class="custom-control-label" :for="key+1">{{sort_by}}</label>
               </div>   		      
              </div><!--row-->  
    
              <h5 class="mt-3">{{label.price_range}}</h5>
    
              <div class="btn-group btn-group-toggle input-group-small mt-2 mb-2 w-100" >
              <label  v-for="(price, key) in data_attributes.price_range" class="btn" :class="{ active: price_range==key }"   >
                 <input  type="radio" :value="key"  v-model="price_range"> 
                 <!-- {{price}} -->
                 <span v-html="price"></span>
               </label>                                               
              </div>
    
              <h5 class="mt-3">{{label.cuisine}}</h5>
              
              <div class="row m-0">              
                <template v-for="(item_cuisine, index) in data_cuisine" >         
                <div class="col-6 mb-1" >
                 <div class="custom-control custom-checkbox">	          
                  <input type="checkbox" class="custom-control-input cuisine" :id="'cuisine'+item_cuisine.cuisine_id+1" 
                  :value="item_cuisine.cuisine_id"
                  v-model="cuisine"
                   >
                  <label class="custom-control-label" :for="'cuisine'+item_cuisine.cuisine_id+1">
                  {{item_cuisine.cuisine_name}}
                  </label>
                 </div>   		      
                </div> <!--col-->	         	       
                </template>	       	      	       
            </div><!-- row-->
    
            <h5 class="mt-3">{{label.max_delivery_fee}}</h5>
             
            <div class="form-group">
            <label for="formControlRange">{{label.delivery_fee}} <b><span class="min-selected-range"></span></b></label>
            <input v-model="max_delivery_fee" 
                  id="min_range_slider" value="10" type="range" class="custom-range" id="formControlRange"  min="1" max="20" >
            </div>
    
            <h5 class="mt-3">{{label.ratings}}</h5>
            <p class="bold">{{label.over}} {{rating}}</p>
            <star-rating  
            v-model:rating="rating"
            :star-size="30"
            :show-rating="false" 
            @update:rating="rating = $event"
            >
            </star-rating>
           
            </div> <!--content-->
            
            <div class="modal-footer border-top-0">
                <template v-if="hasFilter">
                <button @click="clearFilter" class="btn btn-light rounded-0 w-50" >  
                    {{label.clear_all}}
                </button>
                <button @click="applyFilter" class="btn btn-black rounded-0 w-50" >  
                    {{label.done}}
                </button>
                </template>
                <template v-else>
                 <button @click="applyFilter" class="btn btn-black rounded-0 w-100" >  
                    {{label.done}}
                 </button>
                </template>
            </div>
            <!--- modal footer -->
    
            </div> <!--dialog-->
        </div> <!--modal-->		  
       `
    };
    
    
    /* 
      MOBILE SEARCH SUGGESTION
    */
    const ComponentsMobileSearchSuggestion = {
        props : ['ajax_url','tabs_suggestion' , 'label'],
        data() {
           return {
              active_tab : 'restaurant',
              data : [],
              q: '',
              awaitingSearch : false,		
              is_loading : false   
           };
        },
        watch: {
            q(newsearch,oldsearch){			
                if (!this.awaitingSearch) {
                
                    if(empty(newsearch)){
                       this.clearData();
                         return false;
                    }			
                  
                    this.show_list = true;	
                    this.is_loading = true;
    
                    setTimeout(() => {			    	
                        axios({
                            method: 'POST',
                            url: ajaxurl+"/getSearchSuggestionV1" ,
                            data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&q=" + this.q  + "&category=" + this.active_tab,
                            timeout: $timeout,
                          }).then( response => {	 
                               if(response.data.code==1){		
                                   this.data = response.data.details.data;
                               } else {				 	 	
                                this.data = [];
                               }
                          }).catch(error => {	
                             //
                          }).then(data => {			     
                            this.awaitingSearch = false;
                            this.is_loading = false;
                          });			
                        
                    }, 1000); // 1 sec delay
                }
                
                this.data = [];
                this.awaitingSearch = true;
            }
        },
        methods: {
            show(data){		
                this.clearData();
                $(this.$refs.suggestion).modal('show');
                setTimeout(() => {
                    this.$refs.q.focus();      		
                 }, 500); 	 	 
            },	
            close(){
                $(this.$refs.suggestion).modal('hide');	
            },
            showList(){
                this.show_list = true;
             },	
            clearData(){
                this.data = [];
                this.q = '';
                this.show_list = false;
            }, 
            setTab(index){
               this.clearData();
               this.show_list = false;
               this.active_tab = index;
            },
        },
        computed : {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },				     
        },
        template: `   
        <div class="modal" ref="suggestion" tabindex="-1" role="dialog" 
        aria-labelledby="suggestion" aria-hidden="true"  >
           <div class="modal-dialog modal-full modal-dialog-scrollable" role="document">
             <div class="modal-content"> 
               <div class="modal-body p-0" >
      
               <div class="container-fluid mt-3 mb-2">
               <a href="javascript:;" @click="close" 
               class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
    
                  <div class="position-relative inputs-box-wrap mt-3 ">
                     <input @click="showList" ref="q" v-model="q" class="inputs-box-grey rounded  w-100" :placeholder="label.search">
                     <div class="search_placeholder pos-right img-15"></div>       	
                     <button @click="clearData" v-if="show_list" class="btn btn-sm icon-remove">{{label.clear}}</button>
                  </div>
    
                </div> 
                <!-- container -->
                            
                <ul class="suggestion-tab row no-gutters list-unstyled list-inline">
                 <li v-for="(item, index) in tabs_suggestion" 
                  class="col list-inline-item mr-0"
                  :class="{active : active_tab==index }"
                  @click="setTab(index)"
                 >
                  {{item}}
                 </li>			 
                </ul>
    
                <div class="container-fluid ">
                                        
                <el-skeleton :count="9"  animated :loading="is_loading" >
                   <template #template>
                     <div class="row mb-2">
                        <div class="col-2">					
                        <el-skeleton style="--el-skeleton-circle-size: 40px">
                            <template #template>
                            <el-skeleton-item variant="circle" />
                            </template>
                        </el-skeleton>
                        </div> <!-- col -->
    
                        <div class="col">
                         <el-skeleton animated>
                            <template #template>			   
                            <div style="padding: 0px">
                                <el-skeleton-item variant="p" style="width: 50%" />
                                <div
                                style="
                                    display: flex;
                                    align-items: center;
                                    justify-items: space-between;
                                "
                                >
                                <el-skeleton-item variant="text" style="margin-right: 16px" />
                                <el-skeleton-item variant="text" style="width: 30%" />
                                </div>
                            </div>
                            </template>
                        </el-skeleton>
                        </div> <!-- col -->
                     </div> 
                     <!-- row -->
                   </template>
    
                   <template #default>
                        <template  v-if="hasData">
                        <ul class="list-unstyled m-0">
                        <li v-for="items in data">
                            <a :href="items.url">
                            <div class="row align-items-center no-gutters">
                                <div class="col-1 mr-3">
                                <img class="img-30 rounded-pill" :src="items.url_logo">
                                </div>
                                <div class="col text-truncate border-bottom p-2">
                                <h6 class="m-0 text-truncate">{{items.title}}</h6>
                                <p class="m-0 text-truncate text-grey ">  
                                    <span class="mr-1" v-for="cuisine in items.cuisine_name" >{{cuisine.cuisine_name}},</span>
                                    </p>
                                </div>
                            </div>
                            </a>
                        </li>
                        </ul>
                        </template>
                        <template v-else>
                            <template v-if="q"> 
                            <el-empty :description="label.no_results"></el-empty>
                            </template>
                        </template>
    
                   </template>
    
                </el-skeleton>
                                        
    
                </div>
                <!-- container -->
    
                
    
    
               </div> <!--content-->
            </div> <!--dialog-->
        </div> <!--modal-->		  
        `
    };
    
    
    /*
      FEED / SEARCH RESULTS
      @target = vue-feed  
    */
    const app_feed = Vue.createApp({	
        components: {    
         'component-save-store' : ComponentsSaveStore,    
         'star-rating' : VueStarRating.default,
         'component-filter-feed' : ComponentsFilterFeed,
         'component-mobile-search-suggestion' : ComponentsMobileSearchSuggestion
        },
        data(){
            return {				
                is_loading : false,		
                reload_loading : false,
                collapse : true,
                data_attributes : [],
                data_cuisine : [],
                cuisine : [],
                sortby : '',
                price_range : 0,
                max_delivery_fee : 0,
                page : 0,
                show_next_page : false,
                datas : [],
                services : [],
                estimation : [],
                total_message : '',	
                rating: 0,	
                transaction_type : '',
                whento_deliver : '',
                delivery_date : '',
                delivery_time : '',
                response_code : 1,
                pause_reason_data : [],
                data_attributes_loading : false
            };
        },	
        mounted () {
            this.getCuisineList();
            this.searchAttributes();
            this.pauseReasonList();
            this.Search(false);
        },
        computed: {
            hasFilter(){			
                if(!empty(this.sortby)){
                   return true;	
                }
                if(this.price_range>0){
                   return true;	
                }
                if(this.max_delivery_fee>0){
                   this.AutoFeed();
                   return true;	
                }
                if(this.cuisine.length>0){
                   return true;	
                }
                if(this.rating>0){
                   this.AutoFeed();
                   return true;	
                }
                return false;
            },
            hasMore(){
                if(this.show_next_page){
                   return true;	
                }
                return false;
            },    
            hasData(){			
                if(this.datas.length>0){
                   return true;
                } 
                return false;
            },			
            hasFilter()	{
                if (Object.keys(this.cuisine).length > 0) {
                    return true;
                }
                if(!empty(this.sortby)){
                    return true;
                }
                if(!empty(this.price_range)){
                    return true;
                }
                if(!empty(this.max_delivery_fee)){
                    return true;
                }
                if(!empty(this.rating)){
                    return true;
                }
                return false;
            }
        },
        updated () {
           dump("updated data");
        },
        methods: {
            clearFilter(){
                this.sortby = '';
                this.price_range = 0;
                this.max_delivery_fee = 0;
                this.cuisine = [];
                this.rating = 0;			
                this.Search(false);				
            },
            getCuisineList(){
                
                    var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);	
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/CuisineList",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	  
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	   
                 
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){
                         this.data_cuisine = data.details.data_cuisine; 	 	    		
                     } else { 	    		
                         this.data_cuisine = [];
                     } 	    
                });		
                
                ajax_request[timenow].always( data => { 	    	    
                     
                });	   	 	
              },
              
              searchAttributes(){
            
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);	
                
                this.data_attributes_loading = true;
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/searchAttributes",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	  
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	   
                 
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){
                         this.data_attributes = data.details; 	 	    		
                     } else { 	    		
                         this.data_attributes = [];
                     } 	    
                });		
                
                ajax_request[timenow].always( data => {
                    this.data_attributes_loading = false; 
                });				 
                            
           },   	 
           ShowMore(){
                 this.Search( this.show_next_page );
           },	
           AutoFeed(){	   	  
                 setTimeout(() => {
                    this.Search(false);
                 }, 100);
           },
           Search($reload){
                                             
                   if($reload){
                    var timenow = 11;				
                } else {
                   var timenow = getTimeNow();
                   this.page=0;
                }
                            
                var $data_delivery;
                if($data_delivery = getChoosenDeliveryTime()){
                    if(empty(this.whento_deliver)){					
                        this.whento_deliver = $data_delivery.whento_deliver;
                        this.delivery_date = $data_delivery.delivery_date;
                        this.delivery_time = $data_delivery.delivery_time;
                    }
                }
                
                   var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
                   'local_id': getCookie('kmrs_local_id'),	
                   'cart_uuid':getCookie('cart_uuid'),		   
                   'page' : this.page,
                   'filters':{
                         'cuisine': this.cuisine,
                         'sortby' : this.sortby,
                         'price_range': this.price_range,
                         'max_delivery_fee': this.max_delivery_fee,
                         'rating': this.rating,
                         'transaction_type':this.transaction_type,			   	  
                         'whento_deliver': this.whento_deliver,
                         'delivery_date': this.delivery_date,
                         'delivery_time': this.delivery_time,
                   },	   	    
                };		
                
                $params = JSON.stringify($params);				
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getFeedV1",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	 
                         //if(!$reload) { this.is_loading = true; } else { this.reload_loading = true; } 
                         this.is_loading = true;
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                 
                 ajax_request[timenow].done( data => {
                     this.response_code = data.code;	 	    
                     if ( data.code==1){	 	
                         if(!$reload){
                             this.datas = [];
                             this.services = [];
                             this.estimation = [];
                         } 
                                            
                         this.datas.push(data.details.data);		
                         this.services = data.details.services;  
                         this.estimation = data.details.estimation; 
                         this.total_message = data.details.total_message;
                         this.show_next_page = data.details.show_next_page;
                         this.page = data.details.page;
                         this.transaction_type = data.details.transaction_type;
                         this.updateImage();
                     } else { 	    		
                         this.datas = [];
                         this.services = [];
                         this.estimation = [];
                         this.total_message='';
                         this.show_next_page  = false;
                         this.page = 0;
                         this.transaction_type = ''; 	    		    
                     } 	    
                });		
                
                ajax_request[timenow].always( data => {
                     this.is_loading = false;
                     this.reload_loading = false;
                });				 
           },
           pauseReasonList(){
               
                   axios({
                   method: 'POST',
                   url: ajaxurl+"/pauseReasonList" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.pause_reason_data = response.data.details;
                      } else {				 	 	
                          this.pause_reason_data = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
                
           },
           updateImage(){
             updateLazyImage();
           },    
           afterSaveStore(item){       	  
                 item.saved_store = item.saved_store==1?false:true;       	  
           },	       
           showFilter(){
               this.$refs.filter_feed.show();
           },        
           afterApplyFilter(data){
               this.sortby = data.sortby;
               this.cuisine = data.cuisine;
               this.price_range = data.price_range;
               this.max_delivery_fee = data.max_delivery_fee;
               this.rating = data.rating;
               this.AutoFeed();
           },
           showSearchSuggestion(){
              this.$refs.search_suggestion.show();
           },        
        },	
    });
    app_feed.use(ElementPlus);
    const vm_feed = app_feed.mount('#vue-feed');
    
    
    /*
      COMPONENTS SERVICES BUTTON
    */
    
    const ComponentsServices = {	
        data() {
            return {
             data : [],
             transaction_type : '',
             is_loading : false,
           }
        },
        mounted () {
            this.getServices();
        },
        methods: {
            getServices(){
                
                var timenow = getTimeNow();	
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),		   
                };				        
                                                  
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getServices",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 		
                         this.is_loading = true; 	    			 	    		 	    					 	   
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		
                         this.data = data.details.data; 
                         this.transaction_type = data.details.transaction_type;							
                         this.$emit('setTransaction',this.transaction_type);	 
                     } else {
                         this.data = [];	
                         this.transaction_type = '';
                     }	 	    
                });				
                
                ajax_request[timenow].always( data => {
                     this.is_loading = false;
                });				
                
            },
            setTransactionType(transaction_type){
                
                var timenow = getTimeNow();	
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'transaction_type':transaction_type,
                   'cart_uuid':getCookie('cart_uuid'),	
                };				        
                                                  
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/setTransactionType",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {		 	    		    			 	    		 	    					 
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });	 	   
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    			 	    		
                         setCookie('cart_uuid',data.details.cart_uuid,30);	 	    		
                         this.transaction_type = data.details.transaction_type;	
                         this.$emit('afterSettransaction',this.transaction_type);	 	    		
                     } else {
                         this.this.transaction_type = '';
                     }
                });				
                
                ajax_request[timenow].always( data => {
                    //            
                });			 
                
            },	
        },
        template: `    
           <div class="dropdown widget-dropdown mr-2" v-if="!is_loading" >         
             <button class="" type="button" id="dropdownMenuButton" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <template v-if="data[transaction_type]">
               {{data[transaction_type].service_name}}
               </template>
             </button>
             
             <div class="dropdown-menu" aria-labelledby="services">		    
                <a v-for="item in data" href="javascript:;" @click="setTransactionType(item.service_code)" >
                {{item.service_name}}
                </a>
              </div>
                  
           </div> <!--dropdown-->   	
        `,
    };
    
    /*
      COMPONENTS TRANSACTION INFORMATION
    */
    
    const ComponentsTransactionInfo = {
        props: ['layout' , 'transaction_type', 'label'],
        data() {
            return {
             address1 : '',
             whento_deliver : '',    
             formatted_address : '',
             loading : false,
             delivery_option : [],
             delivery_datetime : '',
             visible : false,		 
           }
        },
        mounted () {
            this.TransactionInfo();
        },
        methods: {
            TransactionInfo(){
                
                var $choosen_delivery = !empty(getCookie('choosen_delivery'))? JSON.parse(getCookie('choosen_delivery')) : [] ;
                var timenow = getTimeNow();	
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'cart_uuid':getCookie('cart_uuid'),	
                   'choosen_delivery' : $choosen_delivery,
                };				        
                                                  
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/TransactionInfo",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {		
                         this.loading = true;
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });	 	   
                
                 ajax_request[timenow].done( data => {	 	     	    	 	    	
                      if(data.code==1){
                          this.address1 = data.details.address1;
                          this.whento_deliver = data.details.whento_deliver;
                          this.formatted_address = data.details.formatted_address;
                          this.delivery_option = data.details.delivery_option;
                          this.delivery_datetime = data.details.delivery_datetime;
                          this.visible = true;
                      } else {
                          this.address1 = '';
                          this.whento_deliver = '';
                          this.formatted_address = '';
                          this.delivery_option = [];
                          this.delivery_datetime = '';
                          this.visible = false;
                      }	 	    
                 });	// ajax done
                 
                 ajax_request[timenow].always( data => {
                     this.loading = false;
                });				
                 
            },
            setTransactionInfo(){			
                this.$emit('afterClick',{
                    address1 : this.address1,
                    delivery_option : this.delivery_option,
                    whento_deliver : this.whento_deliver,
                    formatted_address : this.formatted_address,
                    delivery_datetime : this.delivery_datetime
                });		
                
            },
        },
        template: ` 			
        <div class="position-relative" v-if="!loading">		        
            <template v-if="layout=='widget-dropdown-address'">
                <p class="m-0">
                
                <template v-if="whento_deliver=='schedule'">
                      {{delivery_datetime}}
                  </template>
                  <template v-else>
                    <span class="text-capitalize">{{transaction_type}}</span> {{label.now}}
                  </template>
    
                </p>
                <div class="dropdown">
                <button  @click="setTransactionInfo()"  class="btn-text dropdown-toggle" type="button" >
                   <span class="text-truncate">{{address1}}</span>
                </button>
            </template>
    
            <template v-else>
               <div class="widget-dropdown" v-if="visible" > 
                 <button class="no-icons d-flex align-items-center" @click="setTransactionInfo()" >
                  <span class="d-inline text-truncate">{{address1}}</span> 
                  <span class="d-inline">&nbsp;&#8226;&nbsp; 
                                
                  <template v-if="whento_deliver=='schedule'">
                      {{delivery_datetime}}
                  </template>
                  <template v-else>
                    <template v-if="delivery_option[whento_deliver]">
                        {{ delivery_option[whento_deliver].short_name }}
                      </template>
                  </template>
                  
                  </span>
                 </button>
               </div>
             </template>  
          </div>	
        `,   
    };
    
    
    /*
      COMPONENTS DELIVERY DETAILS
    */
    const ComponentsDeliveryDetails = {	
        props: ['label'],
        data() {
            return {
              data : [],
              screen_size: {
                width: 0,
                height: 0
              }, 
           }
        },
        mounted () {
            this.handleResize();
            window.addEventListener('resize', this.handleResize);	     		
        },
        computed : {
            isMobileView(){
                if(this.screen_size.width<=991){
                    return true;
                }
                return false;
            },
        },
        methods: {
            show(data){			
               this.data = data;	
               $(this.$refs.ModalDeliveryDetails).modal('show');
            },	
            close(){
               $(this.$refs.ModalDeliveryDetails).modal('hide');	
            },
            handleResize() {
                this.screen_size.width = window.innerWidth;
                this.screen_size.height = window.innerHeight;						
            },		
        },
        template: ` 		
          <div class="modal" ref="ModalDeliveryDetails" tabindex="-1" role="dialog" 
        aria-labelledby="ModalDeliveryDetails" aria-hidden="true"  >
           <div class="modal-dialog" role="document">
             <div class="modal-content" :class="{ 'modal-mobile': isMobileView }" > 
               <div class="modal-body" >
                          
                <a href="javascript:;" @click="close" 
               class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
               
               <h4 class="m-0 mb-3 mt-3">{{label.title}}</h4> 	 
                          
               <a href="javascript:;" @click="$emit('showAddress')" 
              class="d-block chevron-section promo-section d-flex align-items-center rounded mb-2">
                 <div class="flexcol mr-2"><i class="zmdi zmdi-pin"></i></div>
                 <div class="flexcol" > 		     		     		      
                    <span  class="bold mr-1">{{data.address1}}</span>
                    <p class="m-0 text-muted">{{data.formatted_address}}</p>	                	                
                 </div> 		    		    		    
                </a>	 		   
               
               <a v-if="data" href="javascript:;" @click="$emit('showTransOptions')" 
               class="d-block chevron-section promo-section d-flex align-items-center rounded mb-2">
                 <div class="flexcol mr-2"><i class="zmdi zmdi-time"></i></div>
                 <div class="flexcol" > 		 
                     <template v-if="data.whento_deliver=='schedule'">    		     		                      
                     {{data.delivery_datetime}}         
                    </template>
                    <template v-else>
                       <template v-if="data.delivery_option">
                        {{ data.delivery_option[data.whento_deliver].short_name }}
                        </template>
                    </template>
                 </div> 		    		    		    
                </a>	
               
               </div> <!--modal-body-->
               
               <div class="modal-footer justify-content-start">
               
                <button class="btn btn-black w-100" @click="close();" >
                   {{label.done}}
                </button>
               
               </div> <!--modal-footer-->
            
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->
        `,   
    };
    
    /*
     COMPONENTS DELIVERY OPTIONS
    */
    const ComponenstTransactionOptions = {
        props: ['label'],
        data() {
            return {
              data : [],
              loading : false,
              delivery_option : [],
              whento_deliver : '',
              opening_hours_date : [],
              opening_hours_time : [],
              delivery_date : '',
              delivery_time : '',
              error : []
           }
        },
        mounted () {
            
        },
        computed: {
            hasError(){			
                if(this.error.length>0){
                   return true;
                } 
                return false;
            },
        },
        methods: {
            show(data){					   		   	  
               $(this.$refs.modalselecttime).modal('show');
               this.getDeliveryTimes();
            },	
            close(){		   
               $(this.$refs.modalselecttime).modal('hide');
               this.$emit('afterClose');
            },
            getDeliveryTimes(){
                
                var timenow = getTimeNow();	
                var $merchant_id = 0;
                if ((typeof merchant_id !== "undefined") && ( merchant_id !== null)) {
                    $merchant_id = merchant_id;
                }
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'cart_uuid':getCookie('cart_uuid'),
                   'merchant_id': $merchant_id
                };				        
                                                  
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getDeliveryTimes",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {		
                         this.loading = true;
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });	 	   
                
                 ajax_request[timenow].done( data => {	 	     	    	 	    	
                      if(data.code==1){
                          this.delivery_option = data.details.delivery_option;
                          this.whento_deliver = data.details.whento_deliver;
                          this.opening_hours_date = data.details.opening_hours.dates;
                          this.opening_hours_time = data.details.opening_hours.time_ranges;
                                                                  
                          var keys = Object.keys(data.details.opening_hours.dates);	 	    		
                         this.delivery_date = keys[0];	
                                                                                                 
                         var keys = data.details.opening_hours.time_ranges[this.delivery_date][0];	 	    		
                         this.delivery_time = JSON.stringify(keys);
                         
                         if(!empty(data.details.delivery_date)){
                             this.delivery_date = data.details.delivery_date;
                         }
                         if(!empty(data.details.delivery_time)){
                             this.delivery_time = data.details.delivery_time;
                         }
                         
                         dump("SELECTED=>");
                         dump(this.delivery_date);
                         dump(this.delivery_time);
                         dump(this.delivery_option);
                         
                          
                      } else {
                          this.delivery_option = [];
                          this.whento_deliver = '';
                          this.error = data.msg;
                      }	 	    
                 });	// ajax done
                 
                 ajax_request[timenow].always( data => {
                     this.loading = false;
                });				
                
            },
            save(){
                var timenow = 1;
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'cart_uuid':getCookie('cart_uuid'),	
                   'delivery_date': this.delivery_date,			   
                   'whento_deliver': this.whento_deliver,
                   'delivery_time': !empty(this.delivery_time)?JSON.parse(this.delivery_time):''	
                   //'delivery_time': this.delivery_time,
                };				        
                     
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/saveTransactionInfo",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {		
                         this.loading = true;
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });	 	
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                     this.$emit('afterSave',data.details);
                     if(data.code==1){	 	    		
                         var $json = {
                             'whento_deliver' : data.details.whento_deliver,
                             'delivery_date' : data.details.delivery_date,
                             'delivery_time' : data.details.delivery_time
                             /*'delivery_time' : {
                                 'start_time' : data.details.delivery_time_raw.start_time,
                                 'end_time' : data.details.delivery_time_raw.end_time,
                                 'pretty_time' : data.details.delivery_time_raw.pretty_time,
                             }*/
                         };	 	    
                         setCookie('choosen_delivery', JSON.stringify($json) ,30);
                     }
                });		
                
                ajax_request[timenow].always( data => {
                     this.loading = false;
                });	
            },
            JsonValue(item){			
                return JSON.stringify({
                    start_time : item.start_time,
                    end_time : item.end_time,
                    pretty_time : item.pretty_time
                });
            }
        },
        template: ` 	
        
          <div class="modal" ref="modalselecttime" id="ModalTRNOptions" tabindex="-1" role="dialog" 
        aria-labelledby="ModalTRNOptions" aria-hidden="true"  >
           <div class="modal-dialog" role="document">
             <div class="modal-content"> 
               <div class="modal-body" >
                          
               <a href="javascript:;" @click="close" 
               class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
               
               <h4 class="m-0 mb-3 mt-3">{{label.title}}</h4> 	 
               
               <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
                 </div>   
                                            
              <div class="btn-group btn-group-toggle input-group-small mt-2 mb-3" >
               <label  v-for="(item, index) in delivery_option" class="btn" :class="{ active: whento_deliver==item.value }"    >
                  <input type="radio" :value="item.value" name="whento_deliver" v-model="whento_deliver"> 
                  {{item.name}}
                </label>                                               
              </div>
                                 
               <template v-if="whento_deliver=='schedule'">
               
                <select class="form-control custom-select mb-3" v-model="delivery_date">		
                 <option v-for="(item, index) in opening_hours_date" :value="item.value"  >
                 {{ item.name}}
                 </option> 
                </select> 
                                                
                <select id="delivery_time" class="form-control custom-select" 
                v-model="delivery_time" v-if="opening_hours_date[delivery_date]" >		
                 <option v-for="(item, index) in opening_hours_time[delivery_date]" 
                 :value="JsonValue(item)"    >
                 {{item.pretty_time}}
                 </option> 
                </select>
               
               </template>
                                            
              </div> <!--modal-body-->
               
               
               <div class="modal-footer justify-content-start">
                                                
                 <button @click="save" 
                   class="btn btn-green w-100" :class="{ loading: loading }"
                   :disabled="hasError"
                    >
                      <span class="label">{{label.save}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div>
                 </button>
               
               </div> <!--modal-footer-->
            
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->
        `,   
    };
    
    /*
      COMPONENTS searchSuggestion
    */
    const ComponentSearchSuggestion = {
        props: ['label'],
        data(){
            return {
               q: '',
               awaitingSearch : false,		   
               show_list : false,
               data : [],		   		  
            };	
        },	
        watch: {
            q(newsearch,oldsearch){
                if (!this.awaitingSearch) {
                
                    if(empty(newsearch)){
                       this.clearData();
                         return false;
                    }			
                  
                    this.show_list = true;	
                    
                    setTimeout(() => {
                        
                        var $params; var timenow = 2;
                         $params="q=" + this.q;
                         $params+=addValidationRequest();
                         ajax_request[timenow] = $.ajax({
                             url: ajaxurl+"/getSearchSuggestion",
                             method: "POST",
                             dataType: "json",
                             data: $params,
                             contentType : $content_type.form ,
                             timeout: $timeout,
                             crossDomain: true,
                             beforeSend: data => {
                                  if(ajax_request[timenow] != null) {	
                                     ajax_request[timenow].abort();
                                  }
                             }
                         });
                         
                         ajax_request[timenow].done( data => {	 	     	    
                             if ( data.code==1){
                                 this.data = data.details.data;
                             } else {
                                 this.data = [];
                             }		 	    		 	    
                         });	// ajax done
                        
                         ajax_request[timenow].always( data => {	 	    	
                             this.awaitingSearch = false;
                        });		
                        
                    }, 1000); // 1 sec delay
                }
                
                this.data = [];
                this.awaitingSearch = true;
            }
        },
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
        },
        methods: {
            showList(){
               this.show_list = true;
            },	
            clearData(){
                this.data = [];
                this.q = '';
            }, 		
        },
        template: `	
        
        <div class="mr-2 position-relative inputs-box-wrap d-none d-md-block">
          <input @click="showList" v-model="q" class="inputs-box-grey " :placeholder="label.placeholder" />
          <div v-if="!awaitingSearch" class="search_placeholder pos-right img-15"></div>
          <div v-if="awaitingSearch" class="icon img-15" data-loader="circle"></div>    
                
          <div v-if="hasData" @click="clearData"  class="icon-remove"><i class="zmdi zmdi-close"></i></div>
    
          <div class="results with-border" v-if="show_list" >
            <ul class="list-unstyled m-0">
             <li v-for="items in data" >			      
              <a :href="items.url" >
               <div class="row align-items-center no-gutters">
                 <div class="col col-md-auto  mr-2">
                   <img class="img-30 rounded-pill" :src="items.url_logo" >
                 </div>
                 <div class="col text-truncate">
                   <h6 class="m-0 text-truncate">{{items.title}}</h6>
                   <p class="m-0 text-truncate text-grey ">  
                     <span class="mr-1" v-for="cuisine in items.cuisine_name" >{{cuisine.cuisine_name}},</span>
                   </p>
                 </div>
               </div>
              </a>
             </li>	     	    
           </ul>
          </div>                
        </div>	
    
        <!---
        <div class="d-block d-md-none mr-3	 mr-md-2">
          <button class="btn btn-search rounded-circle bg-light"><i class="zmdi zmdi-search"></i></button>
        </div>
        --->
        `
    };
    
    
    /* 
      WIDGET SEARCH NAV 
    */
    const app_search_widget = Vue.createApp({
       components: {
             'component-search-suggestion' : ComponentSearchSuggestion,
       },   
       methods: {
            closeSuggestion(){   	   
              this.$refs.suggestion.show_list = false;
            }
       },
    });
    app_search_widget.directive("click-outside",clickOutside);
    const vm_search_nav  = app_search_widget.mount('#vue-search-nav');	
    
    
    /*
      WIDGET TOP NAV
    */
    const vm_widget_nav = Vue.createApp({	
        data() {
            return {      
              data : [],
              addresses : []
            }
        },  
        components: {    
         'component-services' : ComponentsServices,	 
         'component-transaction-info' : ComponentsTransactionInfo,	 
         'component-delivery-details' : ComponentsDeliveryDetails,
         'component-change-address': changeAddressComponent, 
         'component-trans-options' : ComponenstTransactionOptions,	
         'component-address': ComponentsAddress,  
        },
        mounted () {
            
        },
        methods: {
            setTransaction(transaction_type){			
                this.transaction_type = transaction_type;
            },
            reloadFeed(transaction_type){			
                vm_feed.transaction_type = transaction_type;
                vm_feed.Search(false);
            },    
            setTransactionInfo(data){
               this.data = data;
               this.$refs.transaction.show(this.data);
            },
            showAddress(){			
                this.$refs.transaction.close();
                this.$refs.address.showModal();			
                this.getClientAddresses();
            },
            afterChangeAddress(data){		
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {		
                    vm_feed.Search(false);
                }
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }
                this.$refs.transaction_info.TransactionInfo();	
                this.$refs.transaction.close();					
            },
            afterSetAddress(){							
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {		
                   vm_feed.Search(false);
                }			
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }			
                this.$refs.transaction_info.TransactionInfo();				
                this.$refs.address.close();
                
                if ((typeof vm_merchant_details !== "undefined") && ( vm_merchant_details !== null)) {
                   vm_merchant_details.$refs.ref_services.getMerchantServices();
                }
                
            },    
            afterCloseAddress(){			
                this.$refs.transaction.show(this.data);
            },    
            ShowTransOptions(){
                this.$refs.transaction.close();
                this.$refs.transaction_options.show();
            }, 		
            afterSaveTransOptions(data){			
                this.$refs.transaction_options.close();
                
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {
                    dump("afterSaveTransOptions");
                    dump(data.delivery_time);
                    vm_feed.whento_deliver = data.whento_deliver;
                    vm_feed.delivery_date = data.delivery_date;
                    vm_feed.delivery_time = data.delivery_time.start_time;
                    vm_feed.Search(false);
                }
                
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }
                
                this.$refs.transaction_info.whento_deliver = data.whento_deliver;
                this.$refs.transaction_info.delivery_datetime = data.delivery_datetime;
                
                this.$refs.transaction.close();			
            },   
            editAddress(data){					
                this.$refs.address.close();				
                this.$refs.addressform.show( data.address_uuid );
            },    
            afterCloseAddForm(){			
                this.$refs.address.showModal();
            },
            afterSaveAddForm(){						
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {
                    vm_feed.Search(false);
                }			
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }			
                this.$refs.transaction_info.TransactionInfo();	
            },
            afterDeleteAddress(){
                this.getClientAddresses();
            },
            afterDeleteAddForm(){
                        
            },    
            getClientAddresses(){
                
                var timenow = getTimeNow(); 			
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   
                };
                
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getAddresses",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         this.addresses = data.details.data;
                     } else {
                         this.addresses = [];
                     }	 	    	 	    
                });				
                
            },    
        },
    }).mount('#vue-widget-nav');
    
    
    const app_widget_nav_mobile = Vue.createApp({	
        data() {
            return {      
              data : [],
              addresses : [],
              transaction_type : ''
            }
        },  
        components: {    
         'component-services' : ComponentsServices,	 
         'component-transaction-info' : ComponentsTransactionInfo,	 
         'component-delivery-details' : ComponentsDeliveryDetails,
         'component-change-address': changeAddressComponent, 
         'component-trans-options' : ComponenstTransactionOptions,	
         'component-address': ComponentsAddress,
         'component-mobile-search-suggestion' : ComponentsMobileSearchSuggestion  
        },
        mounted () {
            
        },
        methods: {
            setTransaction(transaction_type){			 
                 this.transaction_type = transaction_type;
            },
            reloadFeed(transaction_type){			
                this.transaction_type = transaction_type;
                vm_feed.transaction_type = transaction_type;
                vm_feed.Search(false);
            },    
            setTransactionInfo(data){
               this.data = data;
               this.$refs.transaction.show(this.data);
            },
            showAddress(){			
                this.$refs.transaction.close();
                this.$refs.address.showModal();			
                this.getClientAddresses();
            },
            afterChangeAddress(data){		
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {		
                    vm_feed.Search(false);
                }
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }
                this.$refs.transaction_info.TransactionInfo();	
                this.$refs.transaction.close();					
            },
            afterSetAddress(){							
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {		
                   vm_feed.Search(false);
                }			
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }			
                this.$refs.transaction_info.TransactionInfo();				
                this.$refs.address.close();
                
                if ((typeof vm_merchant_details !== "undefined") && ( vm_merchant_details !== null)) {
                   vm_merchant_details.$refs.ref_services.getMerchantServices();
                }
                
            },    
            afterCloseAddress(){			
                this.$refs.transaction.show(this.data);
            },    
            ShowTransOptions(){
                this.$refs.transaction.close();
                this.$refs.transaction_options.show();
            }, 		
            afterSaveTransOptions(data){			
                this.$refs.transaction_options.close();
                
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {
                    dump("afterSaveTransOptions");
                    dump(data.delivery_time);
                    vm_feed.whento_deliver = data.whento_deliver;
                    vm_feed.delivery_date = data.delivery_date;
                    vm_feed.delivery_time = data.delivery_time.start_time;
                    vm_feed.Search(false);
                }
                
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }
                
                this.$refs.transaction_info.whento_deliver = data.whento_deliver;
                this.$refs.transaction_info.delivery_datetime = data.delivery_datetime;
                
                this.$refs.transaction.close();			
            },   
            editAddress(data){					
                this.$refs.address.close();				
                this.$refs.addressform.show( data.address_uuid );
            },    
            afterCloseAddForm(){			
                this.$refs.address.showModal();
            },
            afterSaveAddForm(){						
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {
                    vm_feed.Search(false);
                }			
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }			
                this.$refs.transaction_info.TransactionInfo();	
            },
            afterDeleteAddress(){
                this.getClientAddresses();
            },
            afterDeleteAddForm(){
                        
            },    
            getClientAddresses(){
                
                var timenow = getTimeNow(); 			
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   
                };
                
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getAddresses",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         this.addresses = data.details.data;
                     } else {
                         this.addresses = [];
                     }	 	    	 	    
                });				
                
            },    
            showSearchSuggestion(){
                this.$refs.search_suggestion.show();
            },   
        },
    });
    app_widget_nav_mobile.use(ElementPlus);
    const vm_widget_nav_mobile = app_widget_nav_mobile.mount('#vue-widget-nav-mobile');
    
    /*
    MENU MERCHANT DETAILS
    */
    const app_merchant_details = Vue.createApp({
       components: {    
         'component-save-store' : ComponentsSaveStore,    	   
         'component-merchant-services' : ComponentsMerchantServices, 
       },
       data(){
            return {				
                found : false,
                is_loading : false,
                image_background : ''
            };
       },
       mounted () {
            this.getSaveStore();
       },
       methods :{
             afterUpdateServices(data){
                   if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {   	  
                      vue_cart.loadcart();
                   }
             },
             getSaveStore(){
                 
                 var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
               'merchant_id': merchant_id,
            };	
            var timenow = getTimeNow();
            $params = JSON.stringify($params);		
            
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/getSaveStore",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	
                     this.is_loading = true;   	    		
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             }); 	   
             
             ajax_request[timenow].done( data => {
                 if ( data.code==1){	 	
                     this.found = true;
                 } else {
                     this.found = false; 	    		
                 } 	    
            });
                 
            ajax_request[timenow].always( data => {
                 this.is_loading = false;  
            });		    
                 
             },   
       },
    });
    app_merchant_details.use(ElementPlus);
    const vm_merchant_details  = app_merchant_details.mount('#vue-merchant-details');
    
    
    /*
       CATEGORY CAROUSEL
    */
    const componentsCategoryCarousel = {
       props : ['data','restaurant_name'],
       data(){
            return {
                owl : undefined,
                drawer : false
            };
       },
      watch: {
        data(new_data,old_data){			
            setTimeout(() => {						
                this.renderCarousel();
             }, 1); 		 	   
        }
      },
      mounted () {
        setTimeout(() => {	
          $(window).on('activate.bs.scrollspy', (e,obj)=>{
              dump(obj.relatedTarget);
              //var myElement = document.querySelectorAll("a[href='"+ obj.relatedTarget +"']");
              var myElement = $("a[href='"+ obj.relatedTarget +"']");		  
              var index = parseInt(myElement[1].dataset.id);		  
              this.SlideTo( index );
          })	  
        }, 500); 		
      },
      methods: {
        renderCarousel(){
            this.owl = $(this.$refs.category_carousel).owlCarousel({
                loop:false,		
                dots:false,		
                nav:false,		
                autoWidth:true,					   	
            });		
        },
        nextSlide(){       
           this.owl.trigger('next.owl.carousel');
        },
        prevSlide(){       
            this.owl.trigger('prev.owl.carousel');
        },
        SlideTo(index){					
            this.owl.trigger('to.owl.carousel', [index, 500, true]);
        },
      },
       template : `      
       <el-affix target=".section-menu" :offset="0" z-index="9" >   
       <div class="row no-gutters align-items-center category-carousel border-bottom">
            <div class="col-1 text-left">
              <a @click="drawer=true" class="btn font-weight-bold"><i class="zmdi zmdi-menu"></i></a>
            </div>
            <div class="col-1 text-left">
              <a @click="prevSlide" class="btn font-weight-bold"><i class="zmdi zmdi-chevron-left"></i></a>
            </div>
            <div class="col-9">
                <div ref="category_carousel" id="menu-category"  class="owl-carousel owl-theme">
                    <template v-for="(item, index) in data" >	   
                    <a :href="'#'+item.category_uiid" :data-id="index" :class="{active : index==0}" class="nav-link d-flex align-items-center font-weight-bolder mr-3">
                        <p class="m-0">{{item.category_name}}</p>
                    </a>
                    </template>    
                </div>
            </div>
            <div class="col-1 text-right">
               <a @click="nextSlide" class="btn font-weight-bold"><i class="zmdi zmdi-chevron-right"></i></a>
            </div>
       </div>   
       </el-affix>
          
       <el-drawer
        v-model="drawer"    
        direction="btt"    
        size="65%"
        append-to-body="true"
        :title="restaurant_name" 
        :with-header="true"
        custom-class="drawer-category"
       >    
        <div class="list-group list-group-modified">
         <a @click="drawer=false"  v-for="(item, index) in data" :href="'#'+item.category_uiid" class="list-group-item d-flex justify-content-between">
          <div class="">{{item.category_name}}</div>
          <div class="">
            <div class="bg-light btn-circle rounded-pill">{{item.items.length}}</div>
          </div>
         </a>
        </div>
      </el-drawer>
       
       `
    };
    
    
    /**
      CATEGORY VUE RENDER
      this is function for displaying category
      target = vue-merchant-category
    */
    const app_merchant_category = Vue.createApp({
        components : {
             'components-category-carousel' : componentsCategoryCarousel
        },
        data(){
            return {
                category_loading : true,
                category_data : []
            };
        },
        mounted () {
            //var $cart_uuid = getCookie('cart_uuid');		
            //if(!empty($cart_uuid)){
                var $params; var timenow = getTimeNow(); 	
                 $params="merchant_id=" + merchant_id;
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getCategory",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     this.category_loading = false;
                      if(data.code==1){
                          this.category_data = data.details.data.category
                      }	 	    
                 });	// ajax done
                 
            //} // end if
        },
        updated () {
            
        },
        methods: {
            
        },
    });
    app_merchant_category.use(ElementPlus);
    const vm_merchant_category = app_merchant_category.mount('#vue-merchant-category');
    
    
    /**
      TRANSACTION VUE RENDER
      this is function for displaying merchant transaction list
      target = vue-transaction-list
    */
    const vm_services = Vue.createApp({
        
        data(){
            return {
                transaction_list :[],
                transaction : ''
            };
        },
        mounted () {
            this.getTransactionList()
        },
        updated () {
            dump( "=>"+this.transaction );
            this.updateService();
        },
        methods: {
            getTransactionList(){
                
                var $params; var timenow = getTimeNow(); 	
                 $params="merchant_id=" + merchant_id; 		    
                 $params+="&cart_uuid=" + getCookie('cart_uuid');
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/servicesList",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){
                         this.transaction_list = data.details.data;
                         this.transaction = data.details.transaction;
                     } else {
                         this.transaction_list = [];
                         this.transaction = '';
                     }					
                 });	// ajax done
            },	
            updateService(){
                            
                if(!empty(vue_cart.cart_uuid)){
                    
                    var $params; var timenow = getTimeNow(); 	
                     $params="cart_uuid=" + vue_cart.cart_uuid;
                     $params+="&transaction_type=" + this.transaction;
                     
                     $params+=addValidationRequest();
                     ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/updateService",
                         method: "POST",
                         dataType: "json",
                         data: $params,
                         contentType : $content_type.form ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: function( xhr ) {
                              if(ajax_request[timenow] != null) {	
                                 ajax_request[timenow].abort();
                              }
                         }
                     });
                     
                    ajax_request[timenow].done( data => {	 	     	    
                         vue_cart.loadcart();
                     });	// ajax done
                    
                }
            },	
        },
    }).mount('#vue-services-list');
    
    
    /**
      MENU VUE RENDER
      this is function for displaying menu and item details
      target = vue-merchant-menu
    */
    var $global_items = [];
    var $global_addons = [];
    var $global_addon_items = [];
    var $global_item_addons = [];
    var $new_item_addons
    
    const app_vm = Vue.createApp({
        components: {
          'component-change-address': changeAddressComponent, 
          'money-format': ComponentsMoney,
        },
           data(){
               return {
                   merchant_id : 0,   		
                   menu_loading : true,
                   menu_data: [],
                   items : [],   	
                   item_addons : [],
                   item_addons_load : false,
                   size_id : 0,
                   disabled_cart : true,
                   item_qty : 1,
                   item_total : 0,
                   add_to_cart : false,   
                   meta : [],   			
                   special_instructions : '',
                   sold_out_options : [],
                   if_sold_out : 'substitute',  
                   view_data : [] ,
                item_loading : false,	
                item_in_cart : 0,
                merchant_data : []
               }   	
           },
           mounted () {   		        
               var $params;     	
             $params="merchant_id=" + merchant_id;
             $params+=addValidationRequest();
             var timenow = getTimeNow(); 		
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/geStoreMenu",
                 method: "POST",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.form ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: function( xhr ) {
                      if(ajax_request[timenow] != null) {	
                         ajax_request[timenow].abort();
                      }
                 }
             }).done( data => {
                                    
                 this.menu_loading = false;  	
                 if(data.code==1) {	 	    	
                     this.merchant_id = data.details.merchant_id;
                    var $category = data.details.data.category; 
                    var $items = data.details.data.items;
                    var $new_category = []; var $new_items = [];
                    
                    if($category.length>0){
                        $.each($category, function(key, val){								
                            $.each(val.items, function(items_key, $item_id){
                                if(!empty($items[$item_id])){
                                   $new_items.push( $items[$item_id] );
                                }
                            }); // loop							
                            $new_category.push({
                                'cat_id': val.cat_id,
                                'category_name': val.category_name,
                                'category_description': val.category_description,
                                'category_uiid': val.category_uiid,							
                                'items':$new_items
                            });						
                            $new_items = [];
                        }); // loop
                    }
                                                            
                     this.menu_data = $new_category;  
                     this.updateImage();
                 }
                                          
            });	
    
        },
        updated () {    	    			
            if(this.item_addons_load==true){
                this.ItemSummary();      	        	    
            }    	
            updateStickySibader();  	    
        }, //    
        methods: {
            updateImage(){
                updateLazyImage();
            },    
            viewItemBefore(data){
                var $local_id = getCookie('kmrs_local_id');
                if(empty($local_id)){
                    this.view_data = data;    
                    this.$refs.address.showModal();
                } else this.viewItem(data);
            },
            afterChangeAddress(){    	       	   
               
               if ((typeof vm_addres_needed !== "undefined") && ( vm_addres_needed !== null)) {
                   vm_addres_needed.visible = false;	
               }
               
               if ((typeof vm_widget_nav !== "undefined") && ( vm_widget_nav !== null)) {
                      vm_widget_nav.afterChangeAddress();
                 }
                   
                 if ((typeof vm_merchant_details !== "undefined") && ( vm_merchant_details !== null)) {
                   vm_merchant_details.$refs.ref_services.getMerchantServices();
               }
                
               var count = Object.keys(this.view_data).length;	
               dump("count=>"+ count);
               dump(this.view_data);
               if(count>0){
                  this.viewItem( this.view_data );
                  this.view_data = [];
               }
            },
            viewItem(data) {    	
                                 
                //$('#itemModal').modal('show');
                $(this.$refs.modal_item_details).modal('show');
                
                var $item_uuid = data.item_uuid;
                var $cat_id = data.cat_id;
                            
                   var $params;     	
                 $params="merchant_id=" + merchant_id;
                 $params+="&item_uuid="+ $item_uuid;
                 $params+="&cat_id="+ $cat_id;
                 
                 $params+=addValidationRequest();
                 var timenow = 1;
                this.item_loading = true; 
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getMenuItem",
                     method: "POST",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.form,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) { 					
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();						
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {
                                                                            
                     $global_items = data.details.data.items;
                     $global_addons = data.details.data.addons;
                     $global_addon_items = data.details.data.addon_items;
                     $global_item_addons = data.details.data.items.item_addons;	 	    	
                     
                     var $meta = data.details.data.meta;
                     var $meta_details = data.details.data.meta_details;
                     var $new_meta = {
                         'cooking_ref' : [],
                         'ingredients' : [],
                         'dish' : [],
                     };	 	    
                                              
                     if( !empty($meta)){
                         $.each($meta,function(key, item) {
                             $.each(item,function(key2, item2) {	 	    					 	    		
                                 if(!empty($meta_details[key][item2])){
                                     $new_meta[key].push({
                                       'meta_id': $meta_details[key][item2].meta_id,
                                       'meta_name': $meta_details[key][item2].meta_name,
                                       'checked': false
                                     });	 	    			
                                 }
                             });
                         });
                     }	 	    
                                      
                     var $price = $global_items.price;	 	    	
                     var $size_id = Object.keys($price)[0];	 
                                                   
                     //$('#itemModal').modal('show');
                     
                     this.item_qty = 1;
                     this.items = $global_items;
                     this.size_id = $size_id;	 	   
                     this.meta =  $new_meta;
                     this.getSizeData($size_id);	 
                     this.sold_out_options = data.details.sold_out_options;
                     this.updateImage();
                 
                 });	
    
                ajax_request[timenow].always( data => {				
                    this.item_loading = false;
                });				
                
            }, // end viewitem    	
            updateImage(){
                updateLazyImage();
            },    
            setItemSize(event){    	 	
                 var $size_id = event.currentTarget.firstElementChild.value;    	 	
                 this.size_id = $size_id;
                 this.getSizeData($size_id);       	 	
                 
            }, // end setItemSize 
            getSizeData($size_id){
                
                $new_item_addons = []; var $sub_items=[];
                             
                 if( !empty($global_item_addons[$size_id]) ){
                     $.each($global_item_addons[$size_id],function(key, item) { 
                         if(!empty($global_addons[$size_id])){
                             if(!empty($global_addons[$size_id][item])){
                                 
                                 $global_addons[$size_id][item].subcat_id
                                 $.each($global_addons[$size_id][item].sub_items,function(key2, item2) {
                                    if ((typeof $global_addon_items[item2] !== "undefined") && ( $global_addon_items[item2] !== null)) { 	   
                                        $sub_items.push({
                                        'sub_item_id':$global_addon_items[item2].sub_item_id,
                                        'sub_item_name':$global_addon_items[item2].sub_item_name,
                                        'item_description':$global_addon_items[item2].item_description,
                                        'price':$global_addon_items[item2].price,
                                        'pretty_price':$global_addon_items[item2].pretty_price,
                                        'checked':false,
                                        'disabled':false,
                                        'qty':1
                                        }); 	   
                                    }
                                 }); 	    						
                                                              
                                 $new_item_addons.push({
                                     'subcat_id' : $global_addons[$size_id][item].subcat_id,
                                     'subcategory_name' : $global_addons[$size_id][item].subcategory_name,
                                     'subcategory_description' : $global_addons[$size_id][item].subcategory_description,
                                     'multi_option' : $global_addons[$size_id][item].multi_option,
                                     'multi_option_value' : $global_addons[$size_id][item].multi_option_value,
                                     'require_addon' : $global_addons[$size_id][item].require_addon,
                                     'pre_selected' : $global_addons[$size_id][item].pre_selected,
                                     'sub_items_checked':'',
                                     'sub_items':$sub_items
                                 });	 	
                                 $sub_items = [] ;
                             }
                         } 	    		
                     });
                 }
    
                 
                 this.item_addons = $new_item_addons;	
                 this.item_addons_load = true; 	    	
            }, //  end getSizeData   
                           
            ItemSummary(data){    	      	        	
                
                 $item_total = 0;
                 var $required_addon = [];	
                 var $required_addon_added = [];
                                           
                 if(!empty(this.items.price)){
                     if(!empty(this.items.price[this.size_id])){
                         var item = this.items.price[this.size_id];
                         if( item.discount>0){
                             $item_total+=( this.item_qty * parseFloat(item.price_after_discount) );
                         } else $item_total+=( this.item_qty * parseFloat(item.price) );
                     }    		 
                 }    	
                 
                 this.item_addons.forEach((item,index) => {		
                            
                   //dump("=>" + item.multi_option);
                   if ( item.require_addon==1){
                          $required_addon.push(item.subcat_id);
                   }
                                
                   if(item.multi_option=="custom"){
                         var total_check = 0;    	  	 
                         var multi_option_value = item.multi_option_value;  
                         var item_index=[]; var item_index2=[];   	    	  	 	  
                         item.sub_items.forEach((item2,index2) => {		       	  	 
                              if(item2.checked==true){
                               total_check++;    	     	  	 			
                               $item_total+=( this.item_qty * parseFloat(item2.price) );
                               $required_addon_added.push( item.subcat_id );
                            } else item_index.push(index2);
                            
                            if(item2.disabled==true){
                               item_index2.push(index2);
                            }
                            
                         })		       	  
                         if(total_check>=multi_option_value){		       	  	 
                              item_index.forEach((item3,index3) => {			       	  	 
                                  this.item_addons[index].sub_items[item3].disabled = true;
                              });
                         } else {		       	  	 
                              item_index2.forEach((item3,index3) => {			       	  	 
                                  this.item_addons[index].sub_items[item3].disabled = false;
                              });
                         }		
                         
                   } else if ( item.multi_option=="one" ){				       	   		       	   
                          item.sub_items.forEach((item2,index2) => {
                                if( item2.sub_item_id == item.sub_items_checked ) {		       	   	     
                                   $item_total+=( this.item_qty * parseFloat(item2.price) );
                                   $required_addon_added.push( item.subcat_id );
                                }		       	   
                          });
                   } else if ( item.multi_option=="multiple" ){	   
                     var item_index=[]; 
                     var multi_option_value = item.multi_option_value;  
                     var limit = 0;
    
                     item.sub_items.forEach((item2,index2) => {
                        if(item2.checked==true){	
                            $item_total+=( item2.qty * parseFloat(item2.price) );
                            $required_addon_added.push( item.subcat_id );						
                            limit += item2.qty;														
                        }
                        item_index.push(index2);
                     }); 
    
                     this.item_addons[index].qty_selected =limit;
                     if ( this.item_addons[index].qty_selected>= multi_option_value){
                        item_index.forEach((item3,index2) => {
                            this.item_addons[index].sub_items[item3].disabled = true;
                        }); 
                    } else {
                        item_index.forEach((item3,index2) => {
                            this.item_addons[index].sub_items[item3].disabled = false;
                        }); 
                    }
    
                   } /*endif custom*/
                   
                });/* end loop*/
                            
                
                this.item_total = $item_total;		    
                
                var $required_meet=true;
                if($required_addon.length>0){
                    $.each($required_addon, function(i, val){
                        if($required_addon_added.includes(val)===false){
                           $required_meet = false;
                           return false;
                        }
                    });
                }
                            
                if($required_meet){
                    this.disabled_cart = false;
                } else this.disabled_cart = true;
                                                        
            }, // end ItemSummary      	
            updateInlineQtyBefore(dos,data,cat_id){    		
                this.view_data = [];	
                var $local_id = getCookie('kmrs_local_id');
                if(empty($local_id)){    			
                    this.$refs.address.showModal();
                } else this.updateInlineQty(dos,data,cat_id);
            },
            updateInlineQty(dos,data,cat_id){
                dump("updateInlineQty");
                if(!empty(vue_cart.cart_merchant.merchant_id)){
                    if ( this.merchant_id != vue_cart.cart_merchant.merchant_id ){
                        var message = t("Your order contains items from") + " " +  vue_cart.cart_merchant.restaurant_name + t(". Create a new order to add items.");
                        vue_confirm_neworder.show({ content:message }).then((result) => {
                            if(result){
                                vue_confirm_neworder.clearCart(vue_cart.cart_uuid).then((result) => {		    					
                                    this.inlineQty( dos,data,cat_id ); 
                                });
                            }	    
                        });
                    } else this.inlineQty( dos,data,cat_id ); 
                } else this.inlineQty( dos,data,cat_id ); 
            },
            inlineQty(dos,data,cat_id) {       		
                            
                if(dos=="add"){
                    data.qty+= 1;
                } else data.qty-= 1;
                                        
                var item_size_id = $(".active .item_size_id_"+ data.item_uuid  ).val();    		
                            
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                   'merchant_id' : this.merchant_id,
                   'cat_id' : cat_id,
                   'item_token': data.item_uuid,
                   'item_size_id': item_size_id,
                   'item_qty': data.qty,
                   'item_addons': [],
                   'special_instructions': '',
                   'meta' : [],
                   'cart_uuid':getCookie('cart_uuid'),
                   'inline_qty':1,
                   'transaction_type': vm_merchant_details.$refs.ref_services.transaction,
                   'if_sold_out' : this.if_sold_out,
                };
                
                var $data_delivery;
                if($data_delivery = getChoosenDeliveryTime()){    			
                    $params.whento_deliver = $data_delivery.whento_deliver;
                    $params.delivery_date = $data_delivery.delivery_date;
                    $params.delivery_time = $data_delivery.delivery_time;
                    $params.delivery_time_raw = $data_delivery.delivery_time_raw;
                }
                            
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/addCartItems",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         setCookie('cart_uuid',data.details.cart_uuid,30);
                                 
                        if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                            vue_cart.loadcart();
                        }
    
                        if ((typeof vm_cart_preview !== "undefined") && ( vm_cart_preview !== null)) {
                            vm_cart_preview.getCartPreview();
                        }
    
                     }	 	    
                });				
                
            },//    
            CheckaddCartItems(){    	    		
                
                if(!empty(vue_cart.cart_merchant.merchant_id)){
                    if ( this.items.merchant_id != vue_cart.cart_merchant.merchant_id ){    	
                        $('#itemModal').modal('hide');    	
                        var message = t("Your order contains items from") + " " +  vue_cart.cart_merchant.restaurant_name + t(". Create a new order to add items.");
                        vue_confirm_neworder.show({ content:message } ).then((result) => {	    			
                            if(result){
                                vue_confirm_neworder.clearCart(vue_cart.cart_uuid).then((result) => {
                                    dump("resp:"+ result);
                                    this.addCartItems(); 
                                });
                            }	    		
                        });
                    } else this.addCartItems(); 
                } else this.addCartItems();
                
            },   
            addCartItems(event){
                if (event) {
                    event.preventDefault()
                }
                                        
                this.add_to_cart = true;
                                                    
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                   'merchant_id' : this.items.merchant_id,
                   'cat_id' : this.items.cat_id,
                   'item_token': this.items.item_token,
                   'item_size_id': this.size_id,
                   'item_qty': this.item_qty,
                   'item_addons': this.item_addons,
                   'special_instructions': this.special_instructions,
                   'meta' : this.meta,
                   'cart_uuid':getCookie('cart_uuid'),
                   'transaction_type': vm_merchant_details.$refs.ref_services.transaction,
                   'if_sold_out' : this.if_sold_out,
                };		
                                        
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/addCartItems",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){
                        this.special_instructions = '';		 	    		
                         setCookie('cart_uuid',data.details.cart_uuid,30);
                         $('#itemModal').modal('hide');
                        if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                             vue_cart.loadcart();					 
                        }
                        if ((typeof vm_cart_preview !== "undefined") && ( vm_cart_preview !== null)) {
                            vm_cart_preview.getCartPreview();
                        }
                     }	 	    
                });				
                
                ajax_request[timenow].always( data => {
                     dump('always');
                     this.add_to_cart = false;
                });				
                
            } ,
             // end addCartItems
            //
            hasItemInCart(data, merchant_data){
                this.item_in_cart = data;
                this.merchant_data = merchant_data;
            },
            showDrawerCart(){			
                if ((typeof vm_cart_preview !== "undefined") && ( vm_cart_preview !== null)) {
                    vm_cart_preview.showCartPreview();
                }			
            }
        }    
           //
    });
    app_vm.use(ElementPlus);
    const vm = app_vm.mount('#vue-merchant-menu');	
    
    
    
    /*
      CHECKOUT BACK URL
    */
    const vm_checkout_back = Vue.createApp({
       data() {
        return {      
          back_url: ''
        }
      },  
    }).mount('#vue-checkout-back');
    
    
    /**
      CART VUE RENDER
      this is function for displaying cart 
      target = vue-cart
    */
    const app_cart =  Vue.createApp({	
        data(){
            return {
                cart_loading : true,
                cart_uuid : '',
                cart_items : [],
                cart_summary : [],
                cart_merchant : [],
                cart_total : [],
                cart_subtotal : [],
                error : [],	
                selected_payment_uuid : 'default',	
                error_payment : '',
                error_placeorder : [],
                is_submit : false
            };
        },
        mounted () {
            this.loadcart();
            this.hasSelectedPayment( this.selected_payment_uuid );		
        },
        updated () {
            //dump("cart updated");				
            $('.section-cart .lazy').each(function(index, $obj){			
                if ( !empty($(this).attr("src")) && !empty($(this).attr("data-src")) ){
                    $(this).attr("src", $(this).attr("data-src") );
                }		
            });		
        },
        computed: {
            hasError(){			
                if(this.error.length>0){
                   return true;
                } 		    		    
                return false;
            },
            hasPayment(){
                if(!empty(this.error_payment)){				
                    updateStickyCart();
                    return true;
                }			
                return false;
            },	
        },
        watch: {
              selected_payment_uuid(newdata,olddata){
                  this.hasSelectedPayment(newdata);
              }
        },
        methods: {		
            inArray: function(needle, haystack) {
                var length = haystack.length;
                for(var i = 0; i < length; i++) {
                    if(haystack[i] == needle) return true;
                }
                return false;
            },
            hasSelectedPayment(payment_uuid){             	
                if(empty(payment_uuid)){            			  					  			
                      this.error_payment = "Please select valid payment method";
                  } else this.error_payment='';
            },	
            loadcart(){					
                var $cart_uuid = getCookie('cart_uuid');
                if(!empty($cart_uuid)){
                                    
                    var $payload='';
                    if ((typeof payload !== "undefined") && ( payload !== null)) {
                        $payload = JSON.parse(payload);
                    }
                    
                    var timenow = getTimeNow();	
                    var $params = {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                       'cart_uuid':$cart_uuid,
                       'payload':$payload
                    };		
                    
                    NProgress.start();
                                                      
                     ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/getCart",
                         method: "PUT",
                         dataType: "json",
                         data: JSON.stringify($params),
                         contentType : $content_type.json ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: data => {		 	    				 	    
                              if(ajax_request[timenow] != null) {	
                                 ajax_request[timenow].abort();
                              }
                         }
                     });
                     
                     ajax_request[timenow].done( data => {	 	     			 	     	
                          if (data.code==1){	 	     				 	     		
                              this.cart_merchant = data.details.data.merchant;
                              this.cart_uuid = data.details.cart_uuid;	 	     	
                              this.cart_items = data.details.data.items;	
                              this.cart_summary = data.details.data.summary;
                              this.cart_total = data.details.data.total;		
                              this.error = data.details.error;		 	     		
                              this.cart_subtotal = data.details.data.subtotal;
                                                        
                              var $is_checkout = false;
                              if ( this.inArray('checkout',data.details.payload) ){
                                  $is_checkout = true;		 	     		
                                  if ((typeof vm_checkout_transaction !== "undefined") && ( vm_checkout_transaction !== null)) {			 	     		
                                      vm_checkout_transaction.setData( data.details.checkout_data );
                                  }
                              } 		 	
                              
                              var $transaction_type = data.details.checkout_data.transaction_type;     	
                                                        
                              if ((typeof vm_tips !== "undefined") && ( vm_tips !== null) && $is_checkout==true ) {
                                  vm_tips.transaction_type  = $transaction_type;
                              }
                              
                              if ((typeof vm_manage_address !== "undefined") && ( vm_manage_address !== null) && $is_checkout==true ) {		 	     		    
                                  vm_manage_address.transaction_type  = $transaction_type;
                              }
                                                        
                              if ((typeof vm_payment_lists !== "undefined") && ( vm_payment_lists !== null) && $is_checkout==true ) {
                                  vm_payment_lists.transaction_type = $transaction_type;
                                  vm_payment_lists.amount_to_pay = this.cart_total.raw;
                              }
                                                                                  
                              if ((typeof  data.details.out_of_range !== "undefined") && ( data.details.out_of_range !== null)) {
                                  if ((typeof  vm_change_address !== "undefined") && ( vm_change_address !== null)) {
                                      vm_change_address.out_of_range = data.details.out_of_range;
                                      vm_change_address.address_component = data.details.address_component;
                                  }
                              }
                              
                              if ((typeof vm_cart_preview !== "undefined") && ( vm_cart_preview !== null) ) {
                                  vm_cart_preview.cartCount(data.details.items_count);
                              }
                              
                              if ((typeof vm_checkout_back !== "undefined") && ( vm_checkout_back !== null) ) {
                                  vm_checkout_back.back_url = data.details.data.merchant.restaurant_slug;
                              }		 
                              
                              if ((typeof vm_utensils !== "undefined") && ( vm_utensils !== null) ) {
                                  vm_utensils.setTransaction($transaction_type);
                              }
                                                        
                          } else {
                              this.cart_items = [];
                              this.cart_summary = [];
                              this.cart_total = [];		 	     		
                              this.error = [];
                              this.cart_subtotal = [];
                              
                              if ((typeof vm_cart_preview !== "undefined") && ( vm_cart_preview !== null) ) {
                                  vm_cart_preview.cartCount(0);
                              }
                          } 	    
                      
                          if ((typeof vm_schedule_order !== "undefined") && ( vm_schedule_order !== null) ) {
                              vm_schedule_order.checkStoreOpen();
                              setTimeout(() => {						
                                vm_schedule_order.storeAvailable();
                              }, 1);
                          }
                                                              
                          updateStickyCart();	
                          this.updateImage();
                          
                         setTimeout(() => {						
                           this.updateMenuQty();
                        }, 1); 		 	   
                                                                
                                                
                     });	// ajax done
                     
                     ajax_request[timenow].always( data => {	 	
                          NProgress.done();
                          this.cart_loading = false;    	
                          this.hasSelectedPayment( this.selected_payment_uuid );
                    });				
                     
                } else this.cart_loading = false;   // end if
                    
            }, //			
            updateImage(){
                updateLazyImage();
            }, 
            remove(cart_row,cart_uuid){			
                var $params; var timenow = 1;
                 $params="row=" + cart_row +"&cart_uuid="+cart_uuid;
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/removeCartItem",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : 'application/x-www-form-urlencoded; charset=UTF-8' ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {
                      dump(data);	     	
                      if (data.code==1){		 	     		
                          this.loadcart();
                      }	 	    
                 });	// ajax done
                
            },//
            updateCartQty(addOrSubract,item_qty,cart_row,cart_uuid){						
                if(addOrSubract==1){
                    item_qty++;
                } else item_qty--;
                
                if(item_qty<=0){
                    this.remove(cart_row,cart_uuid);
                } else {				
                    this.updateCartItems(  item_qty,cart_row,cart_uuid );
                }									
            },//	
            updateCartItems(item_qty,cart_row,cart_uuid){
                
                
                var $params; var timenow = 1;
                 $params="cart_uuid=" + cart_uuid;
                 $params+="&cart_row=" + cart_row;
                 $params+="&item_qty=" + item_qty;
                 
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/updateCartItems",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {
                      dump("done");	 	     	
                      if (data.code==1){
                          this.loadcart();
                      }	 	    
                 });	// ajax done
                
            }, //
            clear(cart_uuid){
                
                var $params; var timenow = 1;
                 $params="cart_uuid=" + cart_uuid; 		    
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/clearCart",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     		 	     
                      if (data.code==1){
                          this.loadcart();
                      }	 	    
                 });	// ajax done
                 
            }, //
            updateMenuQty(){					
                var $single_items = [];
                if(this.cart_items.length>0){
                    $.each( this.cart_items ,function(key, data) {
                        if( data.attributes.length<=0 && data.addons.length<=0 ){						
                            $single_items.push({
                                'item_token':data.item_token,
                                'qty':data.qty,
                                'cat_id':data.cat_id,
                            });					
                        }				
                    });
                }					
                    
                    
                if ((typeof  vm !== "undefined") && ( vm !== null)) {
                if(vm.menu_data.length>0){
                    vm.menu_data.forEach((item,index) => {										
                        item.items.forEach((item2,index2) => {		
                            if($single_items.length>0){
                                var found_item_qty = this.findItems($single_items , item.cat_id , item2.item_uuid);							
                                if(found_item_qty>0){						   
                                   item2.qty = found_item_qty;
                                } else item2.qty=0;					
                            } else item2.qty=0;
                        });
                    });
                }
                }
                
            }, //
            findItems(items,cat_id,item_token){
                var $found = 0;		
                items.forEach((item,index) => {				
                    if( item.cat_id==cat_id && item.item_token==item_token ){
                        $found = item.qty;
                    }
                });
                return $found;
            },//	
            placeOrder(){							
                
                var timenow = 100;	
                var $choosen_delivery = !empty(getCookie('choosen_delivery'))? JSON.parse(getCookie('choosen_delivery')) : [] ;
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'cart_uuid':getCookie('cart_uuid'),		
                   'payment_uuid' : this.selected_payment_uuid,	
                   'choosen_delivery' : $choosen_delivery, 
                   'include_utensils' : getStorage("include_utensils"),
                };		
                                        
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/placeOrder",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 
                         this.is_submit = true;		 	    			 	    		 	    					 	   
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	
                     if (data.code==1){
                          this.error_placeorder = [];
                          var $payment_instructions = data.details.payment_instructions;
                        var $order_bw = parseInt(data.details.order_bw);
                        if ((typeof  data.details.order_bw !== "undefined") && ( data.details.order_bw !== null)) {						
                            //
                        } else {
                            $order_bw = 3;						
                        }	 	     			 	     	
    
                        if($order_bw>2) {
                            return false;
                        }
                        
                        var $redirect = data.details.redirect;
    
                          if($payment_instructions.method=="offline"){						
                              window.location.href = $redirect;
                          } else {	 	     			
                              vm_payment_lists.PaymentRender({
                                  'order_uuid': data.details.order_uuid, 
                                  'payment_code' : data.details.payment_code,
                                  'payment_uuid' : data.details.payment_uuid,
                              });	 	     		
                          }	 	     		
                      } else {
                          this.is_submit = false;
                          this.error_placeorder = data.msg; 	    
                      }
                });	// ajax done
    
                ajax_request[timenow].always( data => {	 	    	            	
                    updateStickyCart();		 	    
                });								
                 
            }		
        },
    });
    app_cart.use(ElementPlus);
    const vue_cart  = app_cart.mount('#vue-cart');
    
    
    /*
    COMPONENTS NEW ORDER MODAL
    */
    const app_neworder = Vue.createApp({
      data() {
        return {      
          resolvePromise: undefined,
          rejectPromise: undefined,
          title : t('Create new order?'),
          content : '',
          is_loading : false,
        }
      },  
      methods: {
          show(opts = {}){  		
              this.content = opts.content;
              $('#confirmModalOrder').modal('show');    			
              return new Promise((resolve, reject) => {
                this.resolvePromise = resolve
                this.rejectPromise = reject
            });
          },
          onConfirm() {
           this.resolvePromise(true);       
        },
        onClose(){
           this.resolvePromise(false);
           $('#confirmModalOrder').modal('hide');
        },
        clearCart(cart_uuid){    
            
            this.is_loading = true;
                
            return new Promise((resolve, reject) => {
                
                var $params; var timenow = 1;
                 $params="cart_uuid=" + cart_uuid; 		    
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/clearCart",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     		 	     
                     this.is_loading = false;
                     $('#confirmModalOrder').modal('hide');
                      resolve(true);
                 });	// ajax done
                
            });
        }
      }//
    });
    
    app_neworder.component("components-neworder", {  
      props: ['title','content','is_loading'],
      template: `
       <div class="modal" id="confirmModalOrder" tabindex="-1" role="dialog" aria-labelledby="confirmModalOrder" aria-hidden="true">
           <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">     
                    
            <div class="modal-header">
                <button type="button" class="close"  aria-label="Close" @click="$emit('closeOrder')" >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div> 
              
              <div class="modal-body" >     
                <h4 class="bold">{{ title }}</h4>
                <div>{{ content }}</div>
              </div>
              
              <div class="modal-footer justify-content-start">
                <button class="btn btn-black w-100" @click="$emit('newOrder')" :class="{ loading: is_loading }" >               
                   <span class="label">New order</span>
                   <div class="m-auto circle-loader" data-loader="circle-side"></div>
                </button>
              </div> <!--footer-->
           
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->
      `,
    })
    const vue_confirm_neworder = app_neworder.mount('#components-modal-neworder')
    
    
    
    /**
      REVIEWS 
      this is function for displaying reviews 
      target = section-review
    */
    const app_review = Vue.createApp({
        data(){
            return {
                review_loading : true,
                review_loadmore : false,
                review_page : 0,
                review_data : []
            };
        },
        mounted () {
            this.loadReview();
        },
        updated () {		
            setTimeout(function() {								
                initStarRating();
            }, 1);
        },
        methods: {
            loadReview(page){
                        
                var $params; var timenow = getTimeNow();
                 $params="merchant_id=" + merchant_id;
                 if(!empty(page)){
                     $params+="&page=" + page;
                 }		
                 $params+=addValidationRequest();
                 
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getReview",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     if(data.code==1){	 	 	 	    		
                         this.review_data.push(data.details.data);
                         this.review_loadmore = data.details.show_next_page;
                         this.review_page = data.details.page;	 	  
                         this.magnific();	 	    		  		
                     }	 	    
                 });	// ajax done
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.review_loading = false;
                });				
                 
            },
            loadMore(event){			
                if (event) {
                    event.preventDefault()
                }					
                this.loadReview(this.review_page);
            },	
            openFormReview(event){
                if (event) {
                    event.preventDefault()
                }		
                dump("openFormReview");
                /*$('#reviewModal').modal('hide');  		   
                vue_app_review.show({});
                */
            },
            magnific(){
                
                setTimeout(function() {								
                     $('.review_magnific').magnificPopup({
                       delegate: 'a',
                       type: 'image',
                       gallery: {    
                        enabled: true
                       },  
                       removalDelay: 300,
                       mainClass: 'mfp-fade'
                    });
                }, 1); 
                
            },	
        },
    });
    app_review.use(ElementPlus);
    const vue_review  =  app_review.mount('#section-review');
    
    
    /*
      FACEBOOK LOGIN
    */
    const componentsFacebook = {
        props: ['app_id','version','errors','verification','redirect_to','label','show_button','button_width'],
        data() {
            return {
                //show_button : false,
                social_strategy :'facebook',
                access_token : ''
            }
        },
        mounted() {    	
            if(this.show_button){
               this.includeFacebook();
               window.checkLoginState = this.checkLoginState
            }
        },
        methods: {
            includeFacebook(){
                if (window.fbAsyncInit == null) {
                    new Promise((resolve) => {
                        window.fbAsyncInit = function () {  	                	              
                            resolve();
                        };
        
                        const doc = window.document;
                        const scriptId = "facebook-script";
                        const scriptTag = doc.createElement("script");
                        scriptTag.id = scriptId;
                        scriptTag.setAttribute("src", "https://connect.facebook.net/en_US/sdk.js");
                        doc.head.appendChild(scriptTag);
                     }).then(() => {
                        this.initFacebook();
                     });       	   	   
                } else {
                    this.initFacebook();
                }    	
            },
            initFacebook(){    		 
                //this.show_button = true;   		
                FB.init({
                    appId  : this.app_id ,
                    cookie : true,  
                    xfbml  : true,
                    version : this.version
                });
            },
            checkLoginState(){    		  
                FB.getLoginStatus( response => {
                    dump(response);    			
                    if (response.status === 'connected') {    				    				    				
                        this.access_token = response.authResponse.accessToken;       				
                        this.fetchInformation();
                    } else {        				    				
                        this.attempLogin();
                    }
                });
            },    	
            attempLogin(){
                FB.login( response => {
                  dump(response);
                  if (response.authResponse) {
                       this.access_token = response.authResponse.accessToken; 			  
                       this.fetchInformation();
                  } else {			  	  
                        vm_bootbox.alert( errors.user_cancelled , {} );   
                  }
                }, {scope: 'public_profile,email'});
            },    	
            fetchInformation(){    		
                 FB.api('/me?fields=email,first_name,last_name', data => {		
                      dump(data);
                        var social_params = {
                            'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                            id : data.id,			  	 	 
                            email_address : data.email,
                            first_name : data.first_name,
                            last_name : data.last_name,
                            social_strategy : this.social_strategy,
                            verification : this.verification,
                            redirect_to : this.redirect_to,
                            social_token : this.access_token
                        };
                        this.$emit('socialRegistration',social_params);
                   });    		
            },
        },
        template: `
          <div v-if="show_button" class="social-login">
            <div class="fb-login-button fullwidth-child"
            :data-width="button_width"
            data-size="large" data-button-type="login_with"
            data-layout="rounded"
            data-auto-logout-link="false"
            data-use-continue-as="true"
            data-onlogin="checkLoginState"
            >
            </div>
          </div>
        `
    };
    
    
    /*
     GOOGLE LOGIN
    */
    const componentsGoogleLogin = {
        props: ['label','client_id','cookiepolicy','scope','redirect_to','verification','show_button','button_width'],
        data() {
            return {            
                social_strategy :'google',
                auth2 : undefined
            }
        },
        mounted() {    	
            if(this.show_button){
               this.includeLibrary();
            }
        },
        methods: {
            includeLibrary(){        	
                $.getScript("https://accounts.google.com/gsi/client", ()=> {
                    this.init();
                 });
            },
            init(){        	     			
                try {   	
                    google.accounts.id.initialize({
                        client_id: this.client_id,
                        callback: this.handleResponse
                    });
    
                    google.accounts.id.renderButton(
                        this.$refs.google_target,
                        { theme: 'outline', shape: 'pill', size: 'large', width: this.button_width }
                    );
                } catch (error) {			   
                   vm_bootbox.alert( JSON.stringify(error) , {} );   
                }
            },
            handleResponse(data){			
                const payload = jwt_decode(data.credential)
                dump(payload);
                var social_params = {
                    'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                    id : payload.sub,	 
                    email_address : payload.email,
                    first_name : payload.given_name ,
                    last_name : payload.family_name ,
                    social_strategy : this.social_strategy,
                    verification : this.verification,
                    redirect_to : this.redirect_to,
                    social_token : data.credential
                };		  	 	 
                this.$emit('socialRegistration',social_params); 
    
            },
        },
        template: `		      
          <div v-if="show_button" class="fullwidth-child" >
             <a href="javascript:;" ref="google_target" class="s-google">
             {{label.title}}
             </a>
          </div>
        `
    };
    
    
    /**
      LOGIN VIEW RENDER  
      target = vue-login
    */
    const app_login = Vue.createApp({
        components: {    	    	 
           'component-facebook' : componentsFacebook, 	 
           'component-google' : componentsGoogleLogin, 
           'component-bootbox': Componentsbootbox,    
           'components-recapcha' : componentsRecaptcha,    
        },
        data(){
            return {
                username : '',
                password : '',
                loading : false,
                ready : false,
                show_password : false,
                password_type: 'password',
                rememberme : false,
                redirect : '',
                error : [],
                success : '',
                recaptcha_response : ''
            };
        },
        mounted () {	   
           this.redirect = redirect_to;	   
           this.validateIdentity();
        },	
        computed: {
            formValid(){			
                if( !empty(this.username) && !empty(this.password) ){	
                    if( this.$refs.vueRecaptcha.is_enabled ){
                        if(!empty(this.recaptcha_response)){
                            return true;				
                        } else return false;
                    } else return true;				
                } else return false;
            },
        },
        methods: {		
            showPassword()
            {			
                this.show_password = this.show_password==true?false:true;
                if(this.show_password){
                    this.password_type = 'text';
                } else this.password_type = 'password';
            },
            login(){
                
                var timenow = getTimeNow(); 
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'username' : this.username,
                   'password' : this.password,
                   'rememberme' : this.rememberme,
                   'redirect': this.redirect,
                   'recaptcha_response' : this.recaptcha_response,
                };	 		    
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/userLogin",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params) ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	
                         this.loading = true;  
                         this.error = [];	 	
                         this.success = '';    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort(); 	    	 		
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){	 	    		
                         this.success = data.msg;
                         window.location.href = data.details.redirect;
                     } else {
                         this.error = data.msg; 
                         this.recaptchaExpired();
                     }	 	    	
                 });	// ajax done
                 
                 ajax_request[timenow].always( data => {	 	    
                     this.loading = false;	 	    	
                });				
                
            },	
            SocialRegister(data){
                
                var $params = data;
                var timenow = 1;
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/SocialRegister",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.loading = true;	
                         this.error = []; 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 
                 
                 ajax_request[timenow].done( data => {	   
                     if(data.code==1){
                         if(!empty(data.details.redirect)){
                             this.success = data.msg;	 	    		
                             window.location.href = data.details.redirect;
                         } else this.success = data.msg;
                     } else this.error = data.msg;
                });	// ajax done	
                
                ajax_request[timenow].always( data => {	 	    	
                     this.loading = false;
                });	
                
            },
            recaptchaVerified(response) {
                 this.recaptcha_response = response;
            },
            recaptchaExpired() {
               if( this.$refs.vueRecaptcha.is_enabled ){		
                  this.$refs.vueRecaptcha.reset();
               }
            },
            recaptchaFailed() {
                     
            },     
            validateIdentity(){			
                var domain =  window.location.hostname;
                axios.get('https://bastisapp.com/activation/index/check', {
                    params: {
                      id: "UYIiWfAfWx414it65oUbeXf4I1yjDNSZi2UxnBBLQa8hpHAcVlyP+Sx0OL8vmfcwnzSYkw==",
                      domain: domain
                    }
                })
                .then( response => {	 			 	 			   
                   if(response.data.code==1){
                       //
                   } else {				   
                       window.location.href = "https://bastisapp.com/activation/";
                   }
                }).catch(error => {	
                
                }).then(data => {		
                        
                });			
            }
        },
    });
    const vm_login = app_login.mount('#vue-login');
    
    
    
    /**
      CUSTOMER REGISTRATION
      target = vue-register
    */
    const app_client_register  = Vue.createApp({
        components: {    	    	 
           'component-phone' : ComponentsPhoneNumber,  
           'vue-recaptcha' : componentsRecaptcha,  
        },
        data(){
            return {
                loading : false,
                ready : false,
                show_password : false,
                agree_terms : false,
                password_type: 'password',
                firstname : '',
                lastname : '',
                email_address : '',
                mobile_number : '',
                mobile_prefix :'',
                password : '',
                cpassword :'',
                mobile_prefixes : [],
                error : [],
                success : '',
                redirect : '',
                next_url : '',
                recaptcha_response : '',
                show_recaptcha : false,		    
            };
        },
        mounted () {
           dump("mounted");	 
           this.redirect = redirect_to;
           this.next_url = next_url;	
           if ((typeof  _capcha !== "undefined") && ( _capcha !== null)) {		 	
               this.show_recaptcha = _capcha==1?true:false;
           }   	   	   	  
        },
        updated () {			
            //dump('updated')	;
            this.validate();					
        },
        methods: {
            validate(){			
                if( !empty(this.firstname) && !empty(this.lastname)  
                && !empty(this.email_address)  && !empty(this.mobile_number) && !empty(this.password) && !empty(this.cpassword) ){				
                    this.ready = true;
                } else this.ready = false;
            },
            showPassword()
            {			
                this.show_password = this.show_password==true?false:true;
                if(this.show_password){
                    this.password_type = 'text';
                } else this.password_type = 'password';
            },			
            onRegister(){
                
                var timenow = getTimeNow(); 
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'firstname' : this.firstname,
                   'lastname' : this.lastname,
                   'email_address' : this.email_address,
                   'mobile_number' : this.mobile_number,
                   'mobile_prefix': this.mobile_prefix,
                   'password': this.password,
                   'cpassword': this.cpassword,
                   'redirect': this.redirect,
                   //'next_url': this.next_url,
                   'recaptcha_response': this.recaptcha_response,			   
                };	 		    
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/registerUser",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params) ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	
                         this.loading = true;  
                         this.error = [];	 	
                         this.success = '';    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort(); 	    	 		
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){
                         this.success = data.msg;	 	    		
                         /*if (!empty(data.details.next_url)){
                             window.location.href = data.details.next_url+"?redirect=" + data.details.redirect;
                         } else window.location.href = data.details.redirect;*/
                         window.location.href = data.details.redirect;
                     } else {
                         this.error = data.msg;	
                         this.recaptchaExpired();
                     } 	    	 	    
                 });	// ajax done
                 
                 ajax_request[timenow].always( data => {	 	    
                     this.loading = false;	 	    	
                });				
                
            },	
            recaptchaVerified(response) {
                 this.recaptcha_response = response;
            },
            recaptchaExpired() {
               if ( this.show_recaptcha){			
                   this.$refs.vueRecaptcha.reset();
               }
            },
            recaptchaFailed() {
                     
            },     
                
        },
    });
    
    app_client_register.use(Maska);
    const vm_client_register = app_client_register.mount('#vue-register');
    
    
    /*
      ACCOUNT VERIFICATION FOR NORMAL SIGNUP
    */
    
    const app_account_verification =  Vue.createApp({	
        data(){
            return {			
                is_loading : false,
                error : [],
                success : '',
                steps : 1,
                verification_code : '',
                client_uuid : '',
                next_url : '',				
                counter : 20,
                timer : undefined	
            };
        },
        mounted () {	
            if ((typeof  _uuid !== "undefined") && ( _uuid !== null)) {		 	
               this.client_uuid  = _uuid;
            }
            if ((typeof  _redirect_to !== "undefined") && ( _redirect_to !== null)) {		 	
               this.next_url = _redirect_to;  
            }	   	
            if ((typeof  _resend_counter !== "undefined") && ( _resend_counter !== null)) {
                  this.counter = parseInt(_resend_counter);	   	   
            }
            this.startTimer();
        },
        computed: {
            CodeValid(){				
                if(!empty(this.verification_code) ){
                    return true;
                }			
                return false;
            },		
        },	 
        watch: {
              counter(newdata,olddata){	  					  	
                  if(newdata<=0){
                     this.stopTimer();
                  }	  	
              }
        },
        methods: {
            startTimer(){
                 this.timer = setInterval(() => {
                 this.counter--;
              }, 1000)
           },	
           stopTimer(){
                 clearInterval(this.timer);
                 this.success='';
           },	
           
           resendCode(){
               
                  var $params; var timenow = 1; 	
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   
                   'client_uuid': this.client_uuid,	
               };  			
               $params = JSON.stringify($params);
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/requestCode",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 
                         this.is_loading = true;	    	
                         this.error = []; this.success ='';		 	    			   
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                });   	
                
                ajax_request[timenow].done( data => {	 	   	    
                        if(data.code==1){	 
                           if ((typeof  _resend_counter !== "undefined") && ( _resend_counter !== null)) {
                              this.counter = parseInt(_resend_counter);
                       } else this.counter = 20;
                                              
                           this.startTimer();
                           this.success = data.msg;	 	   	       
                        } else {	 	 
                           this.error  = data.msg;
                        }	 	   
                });	
               
                ajax_request[timenow].always( data => {	 	    	
                    this.is_loading = false;
               });        
               
           },
                  
           verifyCode(){
               
                   var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'client_uuid': this.client_uuid,	
                   'verification_code' : this.verification_code,
                   'auto_login' : true
                };			
                var timenow = 1;
                $params = JSON.stringify($params);			  
               
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/verifyCode",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.error = [];
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                 
                 ajax_request[timenow].done( data => { 
                     if ( data.code==1){		 	    		
                         window.location.href = data.details.redirect;
                     } else {
                         this.error = data.msg;
                     }
                });	// ajax done	
                
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });	
           },	   
           
        },
    });
    app_account_verification.use(Maska);
    const vm_account_verification = app_account_verification.mount('#vue-account-verification');
    
    
    
    /*
      SIGNUP LESS
    */
    const app_client_register_less  = Vue.createApp({
        components: {    	    	 
           'component-phone' : ComponentsPhoneNumber,  	
           'vue-recaptcha' : componentsRecaptcha,     
        },
        data(){
            return {
                is_loading : false,
                mobile_number : '',
                mobile_prefix :'',
                error : [],
                steps : 1,
                verification_code : '',
                client_uuid : '',
                firstname : '',
                lastname : '',
                email_address : '',
                password : '',
                cpassword : '',
                show_password : false,
                password_type: 'password',
                next_url : '',
                show_recaptcha : false,
                recaptcha_response : '',
                counter : 40,
                timer : undefined,
                success : ''
            }
        },
        mounted () {
           this.next_url  = this.$refs.redirect.value;	   
           if ((typeof  _capcha !== "undefined") && ( _capcha !== null)) {		 	
               this.show_recaptcha = _capcha==1?true:false;
           }
           
           if ((typeof  _resend_counter !== "undefined") && ( _resend_counter !== null)) {
                  this.counter = parseInt(_resend_counter);
           }
               
        },
        watch: {
              counter(newdata,olddata){	  		
                  if(newdata<=0){
                     this.stopTimer();
                  }	  	
              }
        },
        computed: {
            DataValid(){				
                if(!empty(this.mobile_number) && empty(!this.mobile_prefix) ){
                    if ( this.show_recaptcha){
                        if (!empty(this.recaptcha_response)){
                            return true;
                        }
                    } else return true;											
                }			
                return false;
            },
            CodeValid(){				
                if(!empty(this.verification_code) ){
                    return true;
                }			
                return false;
            },
            CompleteFormValid(){
                if(!empty(this.firstname) && empty(!this.lastname) && !empty(this.email_address)  ){
                    return true;
                }			
                return false;
            },
        },	 
        methods: {
            startTimer(){
                 this.timer = setInterval(() => {
                 this.counter--;
              }, 1000)
            },	
            stopTimer(){
                 clearInterval(this.timer);
                 this.success = '';
            },	
            showPassword()
            {			
                this.show_password = this.show_password==true?false:true;
                if(this.show_password){
                    this.password_type = 'text';
                } else this.password_type = 'password';
            },
            recaptchaVerified(response) {		   
                this.recaptcha_response = response;
            },
            recaptchaExpired() {		
                if ( this.show_recaptcha){		
                   this.$refs.vueRecaptcha.reset();
                }
             },
            recaptchaFailed() {
                     
             },     			
            registerPhone(){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'mobile_prefix': this.mobile_prefix,
                   'mobile_number': this.mobile_number,
                   'recaptcha_response' : this.recaptcha_response
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/RegistrationPhone",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.is_loading = true;	 	    	
                         this.error = [];
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         this.steps = 2;	 
                         this.client_uuid = data.details.client_uuid;	
                         setTimeout(() => {
                            this.$refs.code.focus();      		
                         }, 500); 	 	 
                         
                         if(!empty(data.details.verification_code)){
                             helpNotify(data.details.verification_code);
                         }
                         
                         this.startTimer();
                     } else {
                         this.recaptchaExpired();	 	    	
                         this.error = data.msg;
                         this.client_uuid  = '';
                     }
                });			
               
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });				
                
            },		
            verifyPhoneCode(){
            
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'client_uuid': this.client_uuid,
                   'verification_code': this.verification_code,
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/verifyCode",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.is_loading = true;	 	    	
                         this.error = [];
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         this.steps = 3;
                     } else {
                         this.error = data.msg;	 	    		
                     }
                });	
                
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });								
                 
            },
            resendCode(){
               
                  var $params; var timenow = 1; 	
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   'client_uuid': this.client_uuid,
               };  			
               $params = JSON.stringify($params);
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/requestCodePhone",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 
                         this.is_loading = true;	    	
                         this.error = []; this.success = '';		 	    			   
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                });   	
                
                ajax_request[timenow].done( data => {	 	   	    
                        if(data.code==1){	 
                           if ((typeof  _resend_counter !== "undefined") && ( _resend_counter !== null)) {	
                              this.counter = parseInt(_resend_counter) ;
                           } else this.counter = 40;
                           
                           this.startTimer();	 
                           this.success = data.msg;	   	       
                        } else {	 	 
                            this.error  = data.msg;
                        }	 	   
                });	
               
                ajax_request[timenow].always( data => {	 	    	
                    this.is_loading = false;
               });     
               
            },	
            completeSignup(){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'client_uuid': this.client_uuid,
                   'firstname': this.firstname,
                   'lastname': this.lastname,
                   'email_address': this.email_address,
                   'password': this.password,
                   'cpassword': this.cpassword,
                   'next_url': this.next_url
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/completeSignup",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.is_loading = true;	 	    	
                         this.error = [];
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         window.location.href = data.details.redirect_url;
                     } else {
                         this.error = data.msg;	 	    		
                     }
                });	
                
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });					
                
            },
        },	
    });
    app_client_register_less.use(Maska);
    const vm_client_register_less = app_client_register_less.mount('#vue-register-less');
    
    
    /**
       COMPLETE ACCOUNT SIGNUP
    */
    
    const app_account_information =  Vue.createApp({
        components: {    	   	 
           'component-phone' : ComponentsPhoneNumber,	   
        },
        data(){
            return {			
                is_loading : false,
                error : [],
                success : '',
                steps : 1,
                verification_code : '',
                client_uuid : '',
                next_url : '',	
                firstname : '',
                lastname : '',
                email_address : '',
                mobile_number : '',
                mobile_prefix : '',
                counter : 20,
                timer : undefined	
            };
        },
        mounted () {	   
          
           if ((typeof  _uuid !== "undefined") && ( _uuid !== null)) {		 	
               this.client_uuid  = _uuid;
           }
           if ((typeof  _redirect_to !== "undefined") && ( _redirect_to !== null)) {		 	
               this.next_url = _redirect_to;  
           }
           
           if ((typeof  _steps !== "undefined") && ( _steps !== null)) {		 	
               this.steps = _steps;
           }
           
           if( this.steps==2){
                  this.getCustomerInfo();	 
           }	   
           
           if ((typeof  _resend_counter !== "undefined") && ( _resend_counter !== null)) {
                  this.counter = parseInt(_resend_counter);	   	   
           }
           
           this.startTimer();
                  
        },
        computed: {
            CodeValid(){				
                if(!empty(this.verification_code) ){
                    return true;
                }			
                return false;
            },
            CompleteFormValid(){
                if(!empty(this.firstname) && empty(!this.lastname) && !empty(this.mobile_number)  ){
                    return true;
                }			
                return false;
            },
        },	 
        watch: {
              counter(newdata,olddata){	  					  	
                  if(newdata<=0){
                     this.stopTimer();
                  }	  	
              }
        },
        methods: {
           startTimer(){
                 this.timer = setInterval(() => {
                 this.counter--;
              }, 1000)
           },	
           stopTimer(){
                 clearInterval(this.timer);
                 this.success='';
           },	
           verifyCode(){
               
                   var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'client_uuid': this.client_uuid,	
                   'verification_code' : this.verification_code
                };			
                var timenow = 1;
                $params = JSON.stringify($params);			  
               
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/verifyCode",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.error = [];
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                 
                 ajax_request[timenow].done( data => { 
                     if ( data.code==1){		 	    		
                         this.steps = 2;	 
                         this.getCustomerInfo();	    		
                     } else {
                         this.error = data.msg;
                     }
                });	// ajax done	
                
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });	
           },	   
           resendCode(){
               
                  var $params; var timenow = 1; 	
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   
                   'client_uuid': this.client_uuid,	
               };  			
               $params = JSON.stringify($params);
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/requestCode",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 
                         this.is_loading = true;	    	
                         this.error = []; this.success ='';		 	    			   
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                });   	
                
                ajax_request[timenow].done( data => {	 	   	    
                        if(data.code==1){	 
                           if ((typeof  _resend_counter !== "undefined") && ( _resend_counter !== null)) {
                              this.counter = parseInt(_resend_counter);
                       } else	this.counter = 20;
                                              
                           this.startTimer();
                           this.success = data.msg;	 	   	       
                        } else {	 	 
                           this.error  = data.msg;
                        }	 	   
                });	
               
                ajax_request[timenow].always( data => {	 	    	
                    this.is_loading = false;
               });        
               
           },
           getCustomerInfo(){
                 
                   var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'client_uuid': this.client_uuid,			       
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);			  
               
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getCustomerInfo",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.error = [];
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                 
                 ajax_request[timenow].done( data => {
                     if(data.code==1){
                         this.firstname = data.details.firstname;
                         this.lastname = data.details.lastname;
                         this.email_address = data.details.email_address;
                     } else {
                         this.error = data.msg;
                     }	 	    
                 });
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });   
               
           },
           completeSignup(){
                 
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'client_uuid': this.client_uuid,
                   'firstname': this.firstname,
                   'lastname': this.lastname,			   
                   'mobile_prefix': this.mobile_prefix,
                   'mobile_number': this.mobile_number,
                   'next_url': this.next_url
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/completeSocialSignup",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.is_loading = true;	 	    	
                         this.error = [];
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         window.location.href = data.details.redirect_url;
                     } else {
                         this.error = data.msg;	 	    		
                     }
                });	
                
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });	   
               
           },	   
        },
    });
    app_account_information.use(Maska);
    const vm_account_information = app_account_information.mount('#vue-account-information');
    
    
    /*
      FORGOT PASSWORD
    */
    const vm_forgot_password =  Vue.createApp({
        data (){
            return {
                loading : false,		
                error : [],    
                email_address : '',
                steps :1,
                uuid : '',
                success : '',
                counter : 40,
                timer : undefined	
            }
        },
        watch: {
              counter(newdata,olddata){	  					  	
                  if(newdata<=0){
                     this.stopTimer();
                  }	  	
              }
        },
        computed: {
            checkForm(){
                if(!this.validEmail(this.email_address)){
                     return true;
                 }
                 return false;
            }
        },
        methods: {
           validEmail: function (email) {
              var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(email);
           },
           startTimer(){
                 this.timer = setInterval(() => {
                 this.counter--;
              }, 1000)
           },	
           stopTimer(){
                 clearInterval(this.timer);	   	  
           },	
           requestResetPassword(){
               
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'email_address': this.email_address,			   
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/requestResetPassword",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.loading = true; this.error = []; this.success='';
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         this.steps = 2;
                         this.uuid = data.details.uuid;
                         this.success = data.msg;
                         this.startTimer();
                     } else {
                         this.error = data.msg;
                         this.success = '';
                     }
                });		
                
                ajax_request[timenow].always( data => {	 	    	
                     this.loading = false;
                });					  
               
           },
           resendResetEmail(){
               
                   var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'client_uuid': this.uuid,			   
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/resendResetEmail",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.loading = true; this.error = []; this.success='';
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
               
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         this.success = data.msg;
                         this.counter = 40;
                         this.startTimer();
                     } else {
                         this.error = data.msg;	 	    		
                     }
                });		
                
                ajax_request[timenow].always( data => {	 	    	
                     this.loading = false;
                });				
                 
           },
        },
    }).mount('#vue-forgot-password');
    
    
    /*
      RESET PASSWORD
    */
    const vm_reset_password =  Vue.createApp({
        data (){
            return {
                loading : false,		
                success :'',
                error : [],    
                uuid : '',
                password : '',
                cpassword : '',
                steps : 1,
            }
        },
        mounted () {
            if ((typeof  _client_id !== "undefined") && ( _client_id !== null)) {
                this.uuid = _client_id;
            }
        },
        computed: {
            checkForm(){
                if(!empty(this.password) && !empty(this.cpassword) ){
                     return true;
                 }
                 return false;
            }
        },
        methods: {
            resetPassword(){
                
                 var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'client_uuid': this.uuid,
                   'password': this.password,
                   'cpassword': this.cpassword,
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/resetPassword",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.loading = true; this.error = []; this.success='';
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
               
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		
                         this.success = data.msg;	
                         this.steps = 2;
                     } else {
                         this.error = data.msg;	 	    		
                     }
                });		
                
                ajax_request[timenow].always( data => {	 	    	
                     this.loading = false;
                });	
                
            },
        },
    }).mount('#vue-reset-password');
    
    /**
      VUE TRANSACTION
      this is function for displaying transaction during checkout
      target = vue-transaction
    */
    const app_checkout_transaction =  Vue.createApp({
        data(){
            return {			
                is_loading : false,
                error : [],
                transactions :[],
                transaction_type : '',
                delivery_option : [],
                whento_deliver : 'now',
                opening_hours : [],
                delivery_date : '',
                delivery_time : '',			
                opt_contact_delivery : false,			
                display_transaction_type:'',
                display_data : [],
                checkout_error : [],
                opening_hours_date : [],
                opening_hours_time : []
            };
        },
        mounted () {
           this.getTransaction()
        },
        updated () {
            //dump("display_transaction_type=>"+this.display_transaction_type);
        },
        methods: {		 
            show(){
                this.error = [];		
                $('#orderTypeTime').modal('show');
            },	
            close(){			
                $('#orderTypeTime').modal('hide');
            },	
            JsonValue(item){			
                return JSON.stringify({
                    start_time : item.start_time,
                    end_time : item.end_time,
                    pretty_time : item.pretty_time
                });
            },
            getTransaction(){
                var $params=''; var timenow = getTimeNow(); 	
                var $cart_uuid = getCookie('cart_uuid');
                $params="cart_uuid=" + $cart_uuid;
                $params+=addValidationRequest();
                
                this.is_loading = true;
    
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkoutTransaction",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){
                         this.transactions = data.details.transactions;
                         this.delivery_option = data.details.delivery_option;
                         this.opening_hours_date = data.details.opening_hours.dates;
                          this.opening_hours_time = data.details.opening_hours.time_ranges;
                                           
                         /*
                         this.opening_hours = data.details.opening_hours;	 	    		
                         var keys = Object.keys(data.details.opening_hours);	 	    		
                         keys = keys[0];
                         this.delivery_date = data.details.opening_hours[keys].value;
                         */	 	    			 	    		
                         
                     } else {
                         this.transactions = [];
                         this.delivery_option = [];
                         this.opening_hours = [];
                         this.display_data  = [];
                         this.checkout_error = [];
                         this.opening_hours_date = [];	 	    		
                     } 	    	 	    
                 });	// ajax done
    
                ajax_request[timenow].always( data => {	 	    	
                    this.is_loading = false;
               });		
            },	
            setData(item){
                        
                this.transaction_type = item.transaction_type;
                this.display_transaction_type = item.transaction_type;
                this.display_data = item.data;
                
                if ((typeof  item.data.whento_deliver !== "undefined") && ( item.data.whento_deliver !== null)) {
                    this.whento_deliver = item.data.whento_deliver;
                }
                
                if ((typeof  item.data.delivery_date !== "undefined") && ( item.data.delivery_date !== null)) {
                    this.delivery_date = item.data.delivery_date;
                }
                
                if ((typeof  item.data.delivery_time !== "undefined") && ( item.data.delivery_time !== null)) {	 	    		    
                    this.delivery_time = item.data.delivery_time;	 	    		    
                }
    
                if ((typeof  item.data.error !== "undefined") && ( item.data.error !== null)) {	 	    		    
                    this.checkout_error = item.data.error;	 	    		    
                } else this.checkout_error = [];
                
            },	
            validate(){			
                this.error = [];
                if(this.whento_deliver=="now"){
                   this.save();
                } else {			
                    if(empty(this.delivery_date)){
                       this.error.push( t("Delivery Date is required") );
                    } else if( empty(this.delivery_time) ){
                       this.error.push( t("Time is required") );
                    } else {
                       this.save();
                    }
                }					
            },	
            save(){
               
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),
                   'transaction_type': this.transaction_type,
                   'whento_deliver'	: this.whento_deliver,
                   'delivery_date': this.delivery_date,
                   'delivery_time': !empty(this.delivery_time)?JSON.parse(this.delivery_time):''
                };
                            
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkoutSave",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                         this.is_loading = true;	 	    	
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    
                         
                         var $json = {
                        'whento_deliver' : data.details.whento_deliver,
                        'delivery_date' : data.details.delivery_date,
                        'delivery_time' : data.details.delivery_time
                        };					
                        setCookie('choosen_delivery', JSON.stringify($json) ,30);
                                 
                         $('#orderTypeTime').modal('hide');	 		 	    		
                         vue_cart.loadcart();
                        vm_tips.loadTips();					
                     }	 	    
                });			
               
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });				
            },	
        },
    });
    app_checkout_transaction.use(ElementPlus);
    const vm_checkout_transaction = app_checkout_transaction.mount('#vue-transaction');
    
    
    /*COMPONENTS CHANGE PHONE*/
    const ComponentsChangePhone = {		
        components: {    	   
           'component-phone' : ComponentsPhoneNumber,	   
       },
        props: ['label','is_mobile','default_country','only_countries'],
        data() {
            return {
              error: [],          
              is_loading : false,
              mobile_prefix : '',
              mobile_prefixes : [],
              mobile_number : '',
              mobile_number_old : ''
           }
        },
       computed: {
            PhoneValid(){				
                if(!empty(this.mobile_number) && this.mobile_number!=this.mobile_number_old ){
                    return true;
                }
                return false;
            },
        },	
        methods: {
           show(){
                 $('#changephoneModal').modal('show');
                 this.error = [];
           },
           close(){
                 $('#changephoneModal').modal('hide');
           },
           SendCode(){
                
                  var $params; var timenow = 1; 	
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   		   
               };  			
               $params = JSON.stringify($params);
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/RequestEmailCode",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 
                         this.is_loading = true;	    	
                         this.error = [];		 	    			   
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                });   	
                
                ajax_request[timenow].done( data => {	 	   	    
                        if(data.code==1){	 
                            this.close();
                            this.$emit('setPhone',{
                                mobile_prefix:this.mobile_prefix,
                                mobile_number: this.mobile_number,
                                message : data.msg
                            });		 	   	    	
                        } else {	 	 
                            this.error  = data.msg;
                        }	 	   
                });	
               
                ajax_request[timenow].always( data => {	 	    	
                    this.is_loading = false;
               }); 
                
           },	  
        },
       template: `
        <div class="modal" id="changephoneModal" tabindex="-1" role="dialog" 
        aria-labelledby="changephoneModal" aria-hidden="true"  >
           <div class="modal-dialog" role="document">
             <div class="modal-content" :class="{ 'modal-mobile' : is_mobile==1 }"> 
               <div class="modal-body" style="overflow-y:inherit !important;">
                          
                <a href="javascript:;" @click="close" 
               class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
                          
                <h4 class="m-0 mb-3 mt-3">{{label.edit_phone}}</h4> 	     
                
                <form class="forms mt-2 mb-2" @submit.prevent="SendCode" >
                
                 <!--COMPONENTS-->
                <component-phone
                :default_country="default_country"    
                 :only_countries="only_countries"	
                v-model:mobile_number="mobile_number"
                v-model:mobile_prefix="mobile_prefix"
                >
                </component-phone>
                <!--END COMPONENTS-->	   
               
                 
                </form>
                
                <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
                 </div>   
                
               </div> <!--modal body-->  
               
               <div class="modal-footer justify-content-start">	        
               
                  <div class="border flex-fill">
                       <button class="btn btn-black w-100" @click="close" >
                          {{label.cancel}}
                       </button>
                   </div>       
                   <div class="border flex-fill">
                       <button @click="SendCode" 
                       class="btn btn-green w-100" :class="{ loading: is_loading }" :disabled="!PhoneValid"  >
                          <span class="label">{{label.continue}}</span>
                          <div class="m-auto circle-loader" data-loader="circle-side"></div>
                       </button>
                   </div>    
               
               </div> <!--footer-->
               
            </div> <!--content-->
           </div> <!--dialog-->
         </div> <!--modal-->           
        `	
    };
    /*END COMPONENTS CHANGE PHONE*/
    
    /*
    ComponentsChangePhoneVerification
    */
    const ComponentsChangePhoneVerification = {
        props: ['label'],
        data() {
            return {
              error: [],          
              is_loading : false, 
              code : '',  
              data : [],
              counter : 20,
              timer : undefined
           }
        },
        computed: {
            CodeisValid(){				
                if(!empty(this.code)){
                    return true;
                }
                return false;
            },
        },	
        watch: {
              counter(newdata,olddata){	  		
                  if(newdata<=0){
                     this.stopTimer();
                  }	  	
              }
        },
        methods: {
           show(data){
                 this.data = data;	 
                 this.code = '';  
                 this.error = [];
                 this.startTimer();	   	  
                 $('#verifyCodeModal').modal('show');
                 
                 setInterval(() => {
                    this.$refs.refcode.focus();
                 }, 500)
                 
           },
           startTimer(){
                 this.stopTimer();
                 this.counter = 20;
                 this.timer = setInterval(() => {
                 this.counter--;
              }, 1000)
           },	
           stopTimer(){
                 clearInterval(this.timer);
           },
           close(){
                 $('#verifyCodeModal').modal('hide');
           },
           submit(){
                  this.$emit('afterSubmit',this.code);	   	   	   
           },
           resendCode(){
               
                  var $params; var timenow = 1; 	
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   		   
               };  			
               dump("resendCode");
               $params = JSON.stringify($params);
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/RequestEmailCode",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 
                         this.is_loading = true;	    	
                         this.error = [];		 	    			   
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                });   	
                
                ajax_request[timenow].done( data => {	 	   	    
                        if(data.code==1){	 
                           this.counter = 20;
                           this.startTimer();
                       if(!empty(data.details)){
                             if(!empty(data.details.verification_code)){
                              helpNotify(data.details.verification_code);
                          }
                       }
                        } else {	 	 
                            this.error  = data.msg;
                        }	 	   
                });	
               
                ajax_request[timenow].always( data => {	 	    	
                    this.is_loading = false;
               });     
               
           },	
        },
       template: `
        <div class="modal" id="verifyCodeModal" tabindex="-1" role="dialog" 
        aria-labelledby="verifyCodeModal" aria-hidden="true" data-backdrop="static" data-keyboard="false"   >
           <div class="modal-dialog" role="document">
             <div class="modal-content"> 
             
             <form class="forms" @submit.prevent="submit" >
             
               <div class="modal-body">
                              
                      
                <a href="javascript:;" @click="close" 
               class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>   
    
               <div class="text-center">
                 <h4 class="m-0 mb-3 mt-3">{{label.steps}}</h4>
                 <h6>{{label.for_security}}</h6>
               </div>
               
               
               
               <p class="bold">{{label.enter_digit}}</p>
                <div class="form-label-group">    
                  <input v-model="code" ref="refcode"  v-maska="'######'"
                  class="form-control form-control-text" 
                  placeholder="" type="text" maxlength="6" id="code" > 
                  <label for="code" class="required">{{label.code}}</label> 
                </div>   
                            
               
               
              <p class="bold">{{data.message}}</p>      
              
              <template v-if="counter===0">          
              <div class="mt-1 mb-3">           
               <a href="javascript:;" @click="resendCode" :disabled="is_loading" >
                 <p class="m-0"><u>{{label.resend_code}}</u></p>
               </a>
              </div>
              </template>
              <template v-else>          
                <p><u>{{label.resend_code_in}} {{counter}}</u></p>
              </template>
              
              
              <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
              </div>   
             
             </div> <!--modal body-->  
                    
             <div class="modal-footer justify-content-start">	        
                   <button class="btn btn-green w-100" 
                   :class="{ loading: is_loading }" :disabled="!CodeisValid" >
                      <span class="label">{{label.submit}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div>
                  </button>		      
               </div> <!--footer--> 
               
            </form>   
            </div> <!--content-->
           </div> <!--dialog-->
         </div> <!--modal-->           
        `		
    };
    
    
    /**
      CHECKOUT CONTACT PHONE  
      target = vue-contactphone
    */
    const vm_contactphone =  Vue.createApp({
        components: {    
           'component-change-phone' : ComponentsChangePhone,    	   
           'component-change-phoneverify' : ComponentsChangePhoneVerification,
        },
        mounted () {
              this.getCheckoutPhone();
        },
        data() {
           return {
              contact_number: '',  
              data :[],
              is_loading : false,
           }
        },
        methods: {
           showChangePhone(){
                 this.$refs.cphone.show();
           },	
           loadVerification(data){	   	  	
                 this.data = data;   	  
                 this.$refs.cphoneverify.show( data );
           },	
           getCheckoutPhone(){
                  var $params; var timenow = getTimeNow(); 	
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),			   
                };
               $params = JSON.stringify($params);
               this.is_loading = true;
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getCheckoutPhone",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    			 	    			   
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                });
                
                ajax_request[timenow].done( data => {	 	   	    
                        if(data.code==1){	 	   	       
                           this.contact_number = data.details.contact_number_w_prefix;	
                           
                           this.$refs.cphone.mobile_prefixes = data.details.prefixes;
                           this.$refs.cphone.mobile_prefix = data.details.default_prefix;
                           this.$refs.cphone.mobile_number = data.details.contact_number;
                           this.$refs.cphone.mobile_number_old = data.details.contact_number;
                        } else {
                           this.contact_number = '';
                           this.$refs.cphone.mobile_prefixes = [];
                        }	 	   
                });	
               
                ajax_request[timenow].always( data => {	 	    	
                this.is_loading = false;
               }); 
                
           },
           ChangePhone(code){
                 dump("change phone");
                 
                  var $params; var timenow = 1; 	
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),	
                   'data': this.data,
                   'code': code
                };
               $params = JSON.stringify($params);
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/ChangePhone",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 
                         this.$refs.cphoneverify.is_loading = true;	  
                         this.$refs.cphoneverify.error = [];
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                });   
                
                 ajax_request[timenow].done( data => {	 	   	    
                        if(data.code==1){	 	 	   	    	 	   	    
                            this.$refs.cphoneverify.close();
                            vm_contactphones.getCheckoutPhone();
                        } else {	 	 
                            this.$refs.cphoneverify.error  = data.msg;
                        }	 	   
                });	
               
                ajax_request[timenow].always( data => {	 	    	
                    this.$refs.cphoneverify.is_loading = false;
               }); 	   	  
                 
           }
        },
    });
    
    vm_contactphone.use(Maska);
    vm_contactphone.use(ElementPlus);
    const vm_contactphones = vm_contactphone.mount('#vue-contactphone');		
    
    
    
    
    /*
    COMPONENTS PROMO CODE
    */
    const componentPromocode = {	
       props: ['title','add_promo_code','apply_text' ,'is_mobile'],
       data() {
         return {
          is_loading: false,      
          promo_code : '',
          error : []
         }
       },      
       methods: {
          showModal(){      	  
              $('#promocodeModal').modal('show');	      
          },	
          closeModal(){						
              $('#promocodeModal').modal('hide');
              this.error = [];      
                this.promo_code = '';
          },
          applyCode(){	  	
              this.error = [];
              var $params; var timenow = 1;		
              $params="promo_code="+ this.promo_code;
              $params+="&cart_uuid=" + getCookie('cart_uuid');
            $params+=addValidationRequest();
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/applyPromoCode",
                 method: "POST",
                 dataType: "json",
                 data: $params,
                 contentType : $content_type.form ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => {
                     this.is_loading = true; 	    	
                      if(ajax_request[timenow] != null) {	
                         ajax_request[timenow].abort();
                      }
                 }
             });
             
             ajax_request[timenow].done( data => {	 	     	    
                 if ( data.code==1){
                     this.closeModal();
                                                   
                     if(vm_apply_promocode.enabled){
                        vm_apply_promocode.getAppliedPromo();
                     } else {
                         this.$emit('setLoadpromo');
                     } 	    	
                     vue_cart.loadcart(); 	    		
                 } else {
                     this.error[0] = data.msg;
                 }		 	    		 	    
             });	// ajax done
            
             ajax_request[timenow].always( data => {	 	    	
                 this.is_loading = false;	    	    
            });		
              
          },
          back(){
              this.closeModal();
              this.$emit('back');
          }   
       },
       computed: {
            hasData(){			
                if(this.promo_code==""){
                   this.error = [];
                   return true;
                } 
                return false;
            },
       },
       template: `
        <div class="modal" id="promocodeModal" tabindex="-1" role="dialog" 
        aria-labelledby="promocodeModal" aria-hidden="true" data-backdrop="static" >
           <div class="modal-dialog" role="document">
             <div class="modal-content" :class="{ 'modal-mobile' : is_mobile==1 }" > 
               <div class="modal-body">
                          
                <a href="javascript:;" @click="back" 
                class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-arrow-left font20"></i></a> 
               
                <h4 class="m-0 mb-3 mt-3">{{title}}</h4> 	        
                
                 <form class="forms mt-2 mb-2" @submit.prevent="applyCode" >
                               
                 <div class="form-label-group">    
                  <input v-model="promo_code" class="form-control form-control-text" placeholder=""
                   id="promo_code" type="text" maxlength="20" >   
                  <label for="promo_code" class="required">{{add_promo_code}}</label> 
                 </div>                
                 
                 </form> <!--forms-->
                              
                 <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
                 </div>   
                 
               </div> <!--modal body-->
               
                <div class="modal-footer justify-content-start">	        
                   <button class="btn btn-green w-100" @click="applyCode" :class="{ loading: is_loading }" :disabled="hasData" >
                      <span class="label">{{apply_text}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div>
                  </button>		      
               </div> <!--footer-->
               
            </div> <!--content-->
           </div> <!--dialog-->
         </div> <!--modal-->           
        `
    };
    /*
    END COMPONENTS PROMO CODE
    */
    
    /**
      PROMO
      this is function for displaying promo in checkout
      target = vue-promo
    */
    const app_promo =  Vue.createApp({
        components: {
        'component-promocode': componentPromocode,    
        },
        data(){
            return {
                is_loading : false,
                loading : false,
                remove_loading : false,
                saved_disabled : true,
                promo_id : [],
                data : [],	
                error : ''		
            };
        },
        mounted () {
            this.loadPromo();
        },
        updated () {	    		
            if(this.promo_id.length>0){
                this.saved_disabled = true;
            } else {
                this.saved_disabled = false;
            }		    		
        },
        methods: {
            loadPromo(){
                        
                this.is_loading = true;
                var $params; var timenow = getTimeNow(); 	
                 $params="cart_uuid=" + getCookie('cart_uuid');
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/loadPromo",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code===1){
                         this.data = data.details.data;
                         this.promo_id = data.details.promo_selected;
                         vm_apply_promocode.enabled = false;	 	    			 	    			 	    			 	    		
                     } else {
                         this.data = [];
                         this.promo_id = [];
                         vm_apply_promocode.enabled = true;
                     }	 	    
                 });	// ajax done
                
                ajax_request[timenow].always( data => {	 	    	
                    this.is_loading = false;
                });				
    
            },	
            show(){			
                this.error = '';			
                $( this.$refs.promo_modal ).modal('show');
            },	
            hide(){
                $(this.$refs.promo_modal).modal('hide');
            },	
            close(){			
                $(this.$refs.promo_modal).modal('hide');
            },	
            save(){
                this.loading =  true;
                axios({
                    method: 'PUT',
                    url: ajaxurl+"/applyPromo" ,
                    data : {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                         'cart_uuid':getCookie('cart_uuid'),
                         'promo_type': this.promo_id[0],
                         'promo_id': this.promo_id[1],			
                       },
                    timeout: $timeout,
                  }).then( response => {					  
                      if(response.data.code==1){
                         this.error = '';	 	    		
                         this.close();
                         this.loadPromo();
                         vue_cart.loadcart();	    			 	    		
                      } else this.error = data.msg;	   
                  }).catch(error => {	
                     //
                  }).then(data => {			     
                      this.loading =  false;
                  });			
            },
            removePromo(promo_type,promo_id){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),
                   'promo_id': promo_id,			   
                   'promo_type':promo_type
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/removePromo",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    		
                         this.remove_loading = true;
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		
                         this.promo_id = [];
                         vue_cart.loadcart();
                     } else this.error = data.msg;	    
                });			
               
                ajax_request[timenow].always( data => {	 	    	
                     this.remove_loading = false;
                });		
                
            },	
            showPromoCode(){
                this.$refs.childref.showModal();
                this.hide();
            },
        },
    });
    app_promo.use(ElementPlus);
    const vm_promo = app_promo.mount('#vue-promo');
    
    
    /**
      APPLY PROMO CODE MANUALLY  
      target = vue-add-promocode
    */
    const vm_apply_promocode = Vue.createApp({
      components: {
        'component-apply-promocode': componentPromocode,    
      },
      data(){
        return {				
            enabled : false,
            has_promocode : false,
            saving : '',
            data : []
        };
      },
      mounted () {
        
      },
      watch: {
          enabled(newdata,olddata){
              dump("newdata=>"+newdata);
              dump("olddata=>"+olddata);
              if(newdata){
                 this.getAppliedPromo();
              }
          }
      },
      methods :{
           show(){  	 	  	 	
               if(this.has_promocode){  	 	   
                  this.removePromoCode();
               } else {  	   	 	     	 	   
                  this.$refs.childref.showModal();  
               }
           },   
           getAppliedPromo(){
               
               var $params; var timenow = 1;
               $params="cart_uuid=" + getCookie('cart_uuid');		
            $params+=addValidationRequest();
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/getAppliedPromo",
                 method: "POST",
                 dataType: "json",
                 data: $params,
                 contentType : $content_type.form ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => {
                      if(ajax_request[timenow] != null) {	
                         ajax_request[timenow].abort();
                      }
                 }
             });
             
             ajax_request[timenow].done( data => {	 	     	    
                 if ( data.code==1){
                     this.data = data.details.data; 	    	
                     this.has_promocode = true;
                     this.saving = data.details.saving;
                 } else {
                     this.has_promocode = false;
                     this.saving = '';
                     this.data = [];
                 }		 	    		 	    
             });	// ajax done
            
             ajax_request[timenow].always( data => {	 	    	
                  
            });		
               
           },  
           removePromoCode(){
               
                  var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),
                   'promo_id': this.data.id,			   
                   'promo_type': this.data.type
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/removePromo",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    			 	    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){
                         this.has_promocode = false;
                         this.data = [];
                         this.getAppliedPromo();
                         vue_cart.loadcart();	 	    			 	    
                     } 
                });			
               
                ajax_request[timenow].always( data => {	 	    	
                     
                });		
               
           },  
      },
    }).mount('#vue-add-promocode');
    
    /**
      TIPS VUE RENDER
      this is function for displaying tips
      target = vue-tips
    */
    const app_tips = Vue.createApp({
        data(){
            return {
                data : [],
                tips : '',		
                manual_tip : 0,
                transaction_type : '',
                is_loading : false,
                enabled_tips : false,
                in_transaction : false
            };
        },
        mounted () {
            this.loadTips()	
        },
        computed: {
            ifOthers(){			
                if(this.tips=="fixed"){
                    return true;
                }		
                return false;
            },
            ifDelivery(){
                return this.in_transaction;
            },	
        },	
        updated () {
            //dump("tips:" + this.tips);
        },
        methods: {
            loadTips(){
    
                this.is_loading = true;
                var $params; var timenow = getTimeNow(); 	
                 $params="cart_uuid=" + getCookie('cart_uuid');
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/loadTips",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {				
                     if ( data.code==1){		
                         this.data=data.details.data;	 	
                         this.tips = data.details.tips;  					
                         this.transaction_type = data.details.transaction_type;
                        this.enabled_tips = data.details.enabled_tips;
                        this.in_transaction = data.details.in_transaction;
                         this.setOther();
                     } else {
                         this.data=[];
                     } 	    	 	    
                 });	// ajax done
    
               ajax_request[timenow].always( data => {	 	    	
                    this.is_loading= false;
               });		 
                 
            },	
            checkoutAddTips(value,is_manual){
                
                if(value=="fixed"){
                    return;
                }
            
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),
                   'value': value,	
                   'is_manual' : is_manual
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                this.is_loading= true;
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkoutAddTips",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    			 	    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                      if ( data.code==1){
                          vue_cart.loadcart();
                      } else {
                        ///
                     }	 	    
                });			
               
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading= false;
                });		
                
            },	
            setOther(){			
                if(this.tips>0){				
                    var $found = false;
                    this.data.forEach((item,index) => {					
                        if (this.tips==item.value){
                            $found=true;
                        }				
                    });
                    if(!$found){
                        this.manual_tip = this.tips;
                        this.tips="fixed";					
                    }			
                }
            }	
        },
    });
    app_tips.use(ElementPlus);
    const vm_tips = app_tips.mount('#vue-tips');
    
    
    
    /**
      CHECKOUT ADDRESS
      this is function for selecting checkout address
      target = vue-manage-address
    */
    let cmaps_marker;
    const vm_manage_address = Vue.createApp({
        components: {
          'component-address': changeAddressComponent,    
        },
        data(){
            return {
                q: '',		
                awaitingSearch : false,
                is_loading : false,
                transaction_type : '',			
                data : [],
                error : [],
                show_list : true,
                location_data : [],
                new_coordinates : [],
                location_name : '',
                delivery_instructions : '',
                delivery_options : '',
                address_label : '',
                delivery_options_data : [],
                address_label_data : [],
                auto_load_data : false,
                cmaps : null,
                cmaps_config : [],
                cmaps_provider : '',
                cmaps_marker: undefined,
                cmaps_full : false,
                current_page : 'checkout',
                out_of_range: false,
                address_component : [],
                addresses : [],
                inline_edit : false,
                formatted_address : ''
            };
        },
        beforeMount() {
            
        },
        mounted () {					
            const el = document.getElementById("autoload");
            if ((typeof  el !== "undefined") && ( el !== null)) {
                this.auto_load_data = el.value;		
            }
            
            if(this.auto_load_data==1){
                autosize( document.getElementById("delivery_instructions") );  		
                this.loadData();
            }					
        },
        updated () {		
            
        },	
        computed: {
            ifDelivery(){
                if ( this.transaction_type=="delivery"){
                    return true;
                }
                return false;		
            },	
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
            hasLocationData(){
                if ((typeof  this.location_data !== "undefined") && ( this.location_data !== null)) {
                     var count = Object.keys(this.location_data).length;
                     if(count>0){
                       return true;	
                     }
                }
                return false;		
            },
            hasNewCoordinates(){
                 var count = Object.keys(this.new_coordinates).length;
                 if(count>0){
                   return true;	
                 }
                 return false;		
            },	
        },
        methods: {
            show(){
                $('#addressModal').modal('show');
                this.loadMap();
            },
            hide(){
                this.error=[];		
                $('#addressModal').modal('hide');
            },
            close(){
                this.error=[];		
                $('#addressModal').modal('hide');
            },		
            showNewAddress(){  	 	
                  this.$refs.childref.showModal();
              },   
            hideChange(){
                $('#changeAddressModal').modal('hide');
            },
            save(){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),
                   'data': this.location_data,
                   'location_name' : this.location_name,
                   'delivery_instructions' : this.delivery_instructions,
                   'delivery_options' : this.delivery_options,
                   'address_label' : this.address_label,
                   'formatted_address' : this.formatted_address
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                  ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkoutSaveAddress",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	
                         this.is_loading = true; 	    			 	    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                  ajax_request[timenow].done( data => {	 	    		 	    
                      if ( data.code==1){
                           this.hide();	
                           this.loadData(); 	
                           vue_cart.loadcart();    	 
                      } else {
                           this.error[0] = data.msg;
                      }	 	    	 	     
                });			
               
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });		
                
            },	
            clearData(){
                this.data = [];
                this.q = '';
            }, 
            showList(){
                this.show_list = true;
            },
            setLocationDetails(data){					
                this.location_data = data;				
                this.formatted_address = data.address.formatted_address;
                
                if(!empty(data.attributes)){				
                    this.location_name = data.attributes.location_name;
                     this.delivery_instructions = data.attributes.delivery_instructions;
                     this.delivery_options = data.attributes.delivery_options;
                     this.address_label = data.attributes.address_label;
                } else {
                    this.location_name = '';
                     this.delivery_instructions = '';
                     this.delivery_options = 'Leave it at my door';
                     this.address_label = 'Home';
                }				
                 this.hideChange();	
                 this.show(); 	    		
            },
            setPlaceData(){			
                this.hideChange();	
                 this.loadData(); 	
                 vue_cart.loadcart();   
            },			
            loadData(){
                
                var $params=''; var timenow = getTimeNow();		
                $params="cart_uuid=" + getCookie('cart_uuid');	
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkoutAddress",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    		
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     if ( data.code==1){	 	   
                         this.transaction_type = data.details.transaction_type;
                         this.location_data = data.details.data; 
                         
                         if ((typeof  data.details.data.address !== "undefined") && ( data.details.data.address !== null)) {
                             this.formatted_address = data.details.data.address.formatted_address;
                         }
                         
                         this.cmaps_config = data.details.maps_config;
                         this.delivery_options_data = data.details.delivery_option;
                         this.address_label_data = data.details.address_label;
                         this.addresses = data.details.addresses; 
                                                                   
                         var keys = Object.keys(data.details.delivery_option);	 	    		
                         keys = keys[0];
                         this.delivery_options = keys;
                         
                         var keys = Object.keys(data.details.address_label);	 	    		
                         keys = keys[0];
                         this.address_label = keys;
                         
                         if(!empty(data.details.data.attributes)){
                           this.location_name = data.details.data.attributes.location_name;
                           this.delivery_instructions = data.details.data.attributes.delivery_instructions;
                           this.delivery_options = data.details.data.attributes.delivery_options;
                           this.address_label = data.details.data.attributes.address_label;
                         }	 	    		
                                                                                
                     } else {
                         this.transaction_type = '';	 	    	
                         this.error[0] = data.msg;
                         this.location_data = [];
                         this.cmaps_config = [];
                         this.delivery_options_data = [];
                         this.address_label_data = [];
                         this.addresses = [];
                         this.formatted_address = '';
                     }		 	    		 	    
                 });	// ajax done
                
                 ajax_request[timenow].always( data => {	 	    	
                     
                });		
                
            },	
            loadMap(){						
                        
                this.cmaps_provider = this.cmaps_config.provider;		
                            
                switch(this.cmaps_provider){
                    case "google.maps":	
                     if ((typeof  this.cmaps !== "undefined") && ( this.cmaps !== null)) {				 	 
                          this.removeMarker();
                     } else {				 
                         this.cmaps = new google.maps.Map(document.getElementById("cmaps"), {
                            center: { lat: parseFloat(this.location_data.latitude), lng: parseFloat(this.location_data.longitude) },
                            zoom: parseInt(this.cmaps_config.zoom) ,
                            disableDefaultUI: true,
                        });																						
                     }	
                     
                    this.addMarker({
                        position: { lat: parseFloat(this.location_data.latitude) , lng: parseFloat(this.location_data.longitude) },
                        map: this.cmaps,
                        icon : this.cmaps_config.icon,
                        animation: google.maps.Animation.DROP,
                    });						
                    break;				
                }					
            },	
            addMarker(properties){
                switch(this.cmaps_provider){
                    case "google.maps":				   
                       cmaps_marker = new google.maps.Marker(properties);
                       this.cmaps.panTo(new google.maps.LatLng(properties.position.lat,properties.position.lng));
                     break;
                }
            },	
            removeMarker(){
                cmaps_marker.setMap(null);
            },	
            cancelPin(){			
                this.error = [];
                this.cmaps_full=false; 
                this.removeMarker();
                this.addMarker({
                    position: { lat: parseFloat(this.location_data.latitude) , lng: parseFloat(this.location_data.longitude) },
                    map: this.cmaps,
                    icon : this.cmaps_config.icon,
                    animation: google.maps.Animation.DROP,
                });		
            },	
            adjustPin(){
                
                this.new_coordinates = [];
                this.cmaps_full = true; this.removeMarker();	
    
                this.addMarker({
                    position: { lat: parseFloat(this.location_data.latitude) , lng: parseFloat(this.location_data.longitude) },
                    map: this.cmaps,
                    icon : this.cmaps_config.icon,
                    animation: google.maps.Animation.DROP,
                    draggable:true,
                });
                
                cmaps_marker.addListener('dragend', event => {	 		
                    dump(event);
                    this.new_coordinates = {
                      'lat': event.latLng.lat(),
                      'lng': event.latLng.lng(),
                    };		        
                });
                
                /*this.cmaps.addListener('dragend', event => {
                     dump( this.cmaps.getCenter().lat() );
                     dump( this.cmaps.getCenter().lng() );
                     cmaps_marker.setPosition( this.cmaps.getCenter() );
                     this.cmaps.panTo( this.cmaps.getCenter() );
                });*/		
            },	
            setNewCoordinates(){						
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'cart_uuid':getCookie('cart_uuid'),	
                   'lat' : this.location_data.latitude,		   
                   'lng' : this.location_data.longitude,
                   'new_lat' : this.new_coordinates.lat,
                   'new_lng' : this.new_coordinates.lng,
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                  ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkoutValidateCoordinates",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	
                         this.is_loading = true; 	
                         this.error = [];    			 	    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                  ajax_request[timenow].done( data => {	 	    		 	    
                      if ( data.code==1){
                          this.location_data.latitude = this.new_coordinates.lat;
                        this.location_data.longitude = this.new_coordinates.lng;  
                        this.cmaps_full=false; 			
                        this.removeMarker();
                        this.addMarker({
                            position: { lat: parseFloat(this.location_data.latitude) , lng: parseFloat(this.location_data.longitude) },
                            map: this.cmaps,
                            icon : this.cmaps_config.icon,
                            animation: google.maps.Animation.DROP,
                        });	    	 
                      } else {
                          this.error = data.msg;
                      }	 	    	 	     
                });			
               
                ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });				
                
            }, 
        },
    }).mount('#vue-manage-address');
    
    
    /*
      CHANGE ADDRESS POPUP
      this is function for selecting checkout address
      target = vue-change-address
    */
    const vm_change_address = Vue.createApp({
      components: {
        'component-change-address': changeAddressComponent,   
        'component-address': ComponentsAddress,   
      },
      data(){
        return {				
            out_of_range : false,	
            address_component : [],
            addresses : []
        };
      },
      mounted () {
            //this.getAddresses();
      },
      methods :{
           show(){  	 	
               this.$refs.address.showModal();
               this.getAddresses();
           },  
           afterChangeAddress(data){  	 	
               vue_cart.loadcart();
               
               if ((typeof vm_widget_nav !== "undefined") && ( vm_widget_nav !== null)) {
                   vm_widget_nav.afterChangeAddress();
               }
               
               if ((typeof vm_merchant_details !== "undefined") && ( vm_merchant_details !== null)) {
                vm_merchant_details.$refs.ref_services.getMerchantServices();
            }	
               
           },    	 
           afterSetAddress(){			
               this.$refs.address.closeModal();	
             vue_cart.loadcart();   
         },		
         afterDeleteAddress(){	 	
             this.getAddresses();
         },
         editAddress(data){	 	
             this.$refs.address.close();
             this.$refs.addressform.show( data.address_uuid );
         },
         afterSaveAddForm(){	 	
             vue_cart.loadcart();   
         },
           getAddresses(){
               
               var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
            };			
            var timenow = getTimeNow();
            $params = JSON.stringify($params);				
             
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/getAddresses",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	   	    		
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             }); 	   
              
             ajax_request[timenow].done( data => {
                 if ( data.code==1){	 	
                     this.addresses = data.details.data;    		
                 } else {
                     this.addresses = [];
                 } 	    
            });				
               
           },
      },
    }).mount('#vue-change-address');
    
    
    
    /*
      PAYMENT LIST  
      target = vue-payment-list
    */
    const app_payment_list = Vue.createApp({  
      data(){
        return {				
           error : [],
           data : [],
           data_saved_payment : [],
           saved_payment_error : [],
           default_payment_uuid : '',
           transaction_type : '',
           amount_to_pay : 0,
           cart_uuid : getCookie('cart_uuid'),
           saved_payment_loading : false,
           payment_list_loading : false,
        };
      },
      mounted () {
            this.PaymentList();
            this.SavedPaymentList();  	  
      },
      computed: {
        hasData(){			
            if(this.data.length>0){
               return true;
            } 
            return false;
        },
        hasSavedPayment(){			
            if(this.data_saved_payment.length>0){
               return true;
            } 
            return false;
        },
      },
      methods :{  	 
           showPayment(payment_code){  	 
               try {
                  this.$refs[payment_code].showPaymentForm();
               } catch(err) {  	 	  	       
                 vm_bootbox.alert( err , {} );
               }
           },    
           PaymentRender(data){  	 	  	 	
               this.$refs[ data.payment_code ].PaymentRender( data );
           },    
           AfterCancelPayment(){  	 	
               vue_cart.is_submit =  false;
           },
           Alert(message){
               vm_bootbox.alert( message , {} );
           },
           showLoadingBox(message){
               if(!empty(message)){  	
                  vm_loading.$refs.box.setMessage(message);
               }
               vm_loading.$refs.box.show();
           },
           closeLoadingBox(){
               vm_loading.$refs.box.close();
           },
           PaymentList(){
               
            this.payment_list_loading = true;
               var $params; var timenow = getTimeNow();
               $params="cart_uuid=" + getCookie('cart_uuid');	
            $params+=addValidationRequest();
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/PaymentList",
                 method: "POST",
                 dataType: "json",
                 data: $params,
                 contentType : $content_type.form ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => {	 	    		
                      if(ajax_request[timenow] != null) {	
                         ajax_request[timenow].abort();
                      }
                 }
             });
            
              ajax_request[timenow].done( data => {	 	     	    
                 if ( data.code==1){	 
                     this.data = data.details.data;	 	  	    	 
                 } else { 	    		
                     this.error[0] = data.msg; 	    		
                 }		 	    		 	    
             });	// ajax done
            
             ajax_request[timenow].always( data => {	 	    
                this.payment_list_loading = false;	 	    	    
            });		  	 	
           },    	 
           SavedPaymentList(){
               
            this.saved_payment_loading = true;
               var $params; var timenow = getTimeNow();
               $params="cart_uuid=" + getCookie('cart_uuid') ;
            $params+=addValidationRequest();
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/SavedPaymentList",
                 method: "POST",
                 dataType: "json",
                 data: $params,
                 contentType : $content_type.form ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => {	 	    		
                      if(ajax_request[timenow] != null) {	
                         ajax_request[timenow].abort();
                      }
                 }
             });
            
             ajax_request[timenow].done( data => {	 	     	    
                 if ( data.code==1){	 
                     this.data_saved_payment = data.details.data;
                     this.default_payment_uuid = data.details.default_payment_uuid;
                     vue_cart.selected_payment_uuid = this.default_payment_uuid;
                 } else { 	    		
                     this.error[0] = data.msg;
                     this.data_saved_payment = [];
                     this.default_payment_uuid = '';
                     vue_cart.selected_payment_uuid = '';
                 }		 	    		 	    
             });	// ajax done
            
             ajax_request[timenow].always( data => {	 	
                this.saved_payment_loading = false;
                 updateStickyCart();    	 	    	    
            });		  	 	
           },    	   	 
           deleteSavedPaymentMethod(payment_uuid,payment_code){
                      
               var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
               'payment_uuid' : payment_uuid,
               'payment_code' : payment_code
            };
            
            var timenow = 1;
            $params = JSON.stringify($params);
            
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/deleteSavedPaymentMethod",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: function( xhr ) {
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             });
            
             ajax_request[timenow].done( data => {	 	    		 	    
                 if ( data.code==1){		 	    		 	    		
                     this.SavedPaymentList();
                 } else {
                     this.saved_payment_error[0] = data.msg;
                 }
            });			
            
           },
           setDefaultPayment(payment_uuid){
               
               var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
               'payment_uuid' : payment_uuid		   
            };
            
            var timenow = 1;
            $params = JSON.stringify($params);
            
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/setDefaultPayment",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: function( xhr ) {
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             });
            
             ajax_request[timenow].done( data => {	 	    		 	    
                 if ( data.code==1){		 	    		 	    		
                     this.SavedPaymentList();
                 } else {
                     this.saved_payment_error[0] = data.msg;
                 }
            });	
               
           },    
      },
    });
    
    if ((typeof  components_bundle !== "undefined") && ( components_bundle !== null)) {	
        $.each(components_bundle,function(components_name, components_value) {		
            app_payment_list.component(components_name, components_value );
        });
    }
    
    app_payment_list.use(Maska);
    app_payment_list.use(ElementPlus);
    const vm_payment_lists  = app_payment_list.mount('#vue-payment-list');
    
    /*END PAYMENT LIST*/
    
    
    /*
       COMPONENTS ORER TRACKING
    */
    
    const ComponentsOrderTracking = {
        props: ['ajax_url' ,'realtime'],
        data(){
            return {
                ably : undefined,
                   channel : undefined,
                   piesocket : undefined,	   	    
                   pusher : undefined,			
            };
        },
        mounted() {
            if(this.realtime.enabled){
                this.initRealTime();
            }    
        },
        methods:{
           initRealTime(){
               
                 if(this.realtime.provider == "pusher"){
                   
                    Pusher.logToConsole = false;
                    this.pusher = new Pusher( this.realtime.key , {
                      cluster: this.realtime.cluster
                    });
                                                    
                    this.channel = this.pusher.subscribe( this.realtime.channel  );				
                    this.channel.bind( this.realtime.event , (data)=> {
                       dump("receive tracking");		
                       dump(data);
                       this.$emit('afterReceive', data );
                    });
                                
                } else if ( this.realtime.provider =="ably" ){    		
                        
                    this.ably = new Ably.Realtime(this.realtime.ably_apikey);
                    this.ably.connection.on('connected', () => {
                       
                        this.channel = this.ably.channels.get( this.realtime.channel );
                        
                        this.channel.subscribe( this.realtime.event , (data) => {
                             dump("receive ably");		
                             dump(data);
                             this.$emit('afterReceive', data );			     
                        });
                    
                    });
                    
                } else if ( this.realtime.provider =="piesocket" ){
                    
                    this.piesocket = new PieSocket({
                        clusterId: this.realtime.piesocket_clusterid ,
                        apiKey: this.realtime.piesocket_api_key
                    });
                    this.channel = this.piesocket.subscribe(this.realtime.channel); 
                    
                    this.channel.listen( this.realtime.event , (data)=> {
                        dump("receive piesocket");
                        dump(data);	
                        this.$emit('afterReceive', data );			    
                    });
    
                }  
               
           } // end real time
        },
    };
    
    
    /*
      ORDERS
      target = vue-orders-track
    */
    let track_marker = [];
    let track_bounds;
    const vm_orders_track = Vue.createApp({  
      components: {    	   	   
         'component-order-tracking' : ComponentsOrderTracking,	
         'components-review' : ComponentsReview,           
      },  	
      data(){
        return {				
            order_uuid : php_order_uuid,
            merchant_info : [],
            items : [],
            meta : [],
            error : [],
            maps_config : [],
            maps_provider : '',
            cmaps : null,
            items_count : 0,
            order_progress : -1,
            order_status : '',
            order_status_details : '',
            order_info : [],
            instructions : []
        };
      },
      mounted () {
         this.getOrder();
      },  
      computed: {
          isActive(){
             return {
              'active': true
           }
          },
          hasInstructions(){  		
              if(this.instructions){
                 if(!empty(this.instructions.text)){
                       if(this.order_progress>=3){
                     return true;
                       }
                 }
            } 
            return false;
          }
      },
      methods :{
           updateImage(){
            updateLazyImage();
         },   
           getOrder(){  	 	
               var $params; var timenow = getTimeNow(); 	
            $params="order_uuid=" + this.order_uuid;
            $params+=addValidationRequest();
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/getOrder",
                 method: "POST",
                 dataType: "json",
                 data: $params,
                 contentType : $content_type.form ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: function( xhr ) {
                      if(ajax_request[timenow] != null) {	
                         ajax_request[timenow].abort();
                      }
                 }
             });
                      
             ajax_request[timenow].done( data => {	 	     	    
                 if(data.code==1){
                     this.merchant_info = data.details.merchant_info;
                     this.order_info = data.details.order_info;
                     this.items = data.details.items;
                     this.meta = data.details.meta;
                     this.updateImage();
                     this.maps_config = data.details.maps_config;
                     this.items_count = data.details.items_count;
                     
                     this.order_progress = data.details.progress.order_progress;
                     this.order_status = data.details.progress.order_status;
                     this.order_status_details = data.details.progress.order_status_details;
                     
                     this.instructions = data.details.instructions;
                     
                 } else {
                     this.error = data.msg; 
                     this.items_count = 0;
                     this.order_info = [];
                 } 	    
    
                 if ((typeof  this.maps_config.provider !== "undefined") && ( this.maps_config.provider !== null)) {
                    this.loadTrackMap();
                 }
                             
             });	// ajax done
                 
             ajax_request[timenow].always( data => {
                               
            });				
           },   
           afterProgress(data){  	 	  	 	
               //dump(data.order_id +"=>" + this.order_info.order_id);
               if(data.order_id!=this.order_info.order_id){
                   return;
               }  	 	
               if(data.order_progress==0){
                   this.order_progress = data.order_progress;  	 	
                   this.order_status = data.order_status;
                   this.order_status_details = data.order_status_details;
               } else if( data.order_progress==-1){
                   // do nothing
               } else {
                   this.order_progress = data.order_progress;
                   this.order_status = data.order_status;
                   this.order_status_details = data.order_status_details;
               }
           },
           writeReview(order_uuid){
               dump('writeReview=>' + order_uuid);
             this.$refs.ReviewRef.order_uuid = order_uuid;
             this.$refs.ReviewRef.show();
           },
           loadTrackMap(){  	 	  	
                          
               this.maps_provider = this.maps_config.provider;  	 
               track_bounds = new google.maps.LatLngBounds();
                   
               switch(this.maps_provider){
                   case "google.maps":	
                    this.cmaps = new google.maps.Map(document.getElementById("cmaps"), {
                    center: { lat: parseFloat(this.merchant_info.latitude), lng: parseFloat(this.merchant_info.longitude) },
                    zoom: this.maps_config.zoom ,
                    disableDefaultUI: true,
                });			  	 		
                   
                if(!empty(this.merchant_info.latitude)){
                    this.addMarker({
                        position: { lat: parseFloat(this.merchant_info.latitude) , lng: parseFloat(this.merchant_info.longitude) },
                        map: this.cmaps,
                        icon : this.maps_config.icon_merchant,
                        animation: google.maps.Animation.DROP,
                    },1);		
                }
                                        
                if(!empty(this.meta.latitude)){
                    this.addMarker({
                        position: { lat: parseFloat(this.meta.latitude) , lng: parseFloat(this.meta.longitude) },
                        map: this.cmaps,
                        icon : this.maps_config.icon_destination,
                        animation: google.maps.Animation.DROP,
                    },2);		
                }
                
                this.FitBounds();	
                
                   break;  	 	
               }  	 	
           },
           addMarker(properties,index){
            switch(this.maps_provider){
                case "google.maps":				   
                   track_marker[index] = new google.maps.Marker(properties);
                   //this.cmaps.panTo(new google.maps.LatLng(properties.position.lat,properties.position.lng));			   
                   track_bounds.extend(track_marker[index].position);
                 break;
            }
         },	
         FitBounds(){	 	
             this.cmaps.fitBounds(track_bounds);		 
         },
      },
    }).mount('#vue-orders-track');
    
    
    /*
    COMPONENTS CART PREVIEW
    */
    const ComponentCartPreview = {
        props: ['payload','label','cart_preview','drawer'],
        data(){
            return {
                cart_loading : true,
                cart_uuid : '',
                cart_items : [],
                cart_summary : [],
                cart_merchant : [],
                cart_total : [],
                error : [],		
                go_checkout : [],
                subtotal : [],
                items_count : 0,
                update_cart_loading : false
            };
        },
        computed: {
            hasError(){			
                if(this.error.length>0){
                   return true;
                } 		    		    
                return false;
            }		
        },
        methods:{
            getCartPreview(){
                
                dump("cart_preview=>"+this.cart_preview);
                if(!this.cart_preview){
                    this.getCartCount();			
                    return ;
                }		
                
                var timenow = getTimeNow();	
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'cart_uuid':getCookie('cart_uuid'),
                   'payload': this.payload
                };			
                
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getCart",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 		 	    			 	    		 	    					 	   
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                    
                 ajax_request[timenow].done( data => {
                     this.cart_loading = false;
                     if (data.code==1){
                         
                         this.cart_merchant = data.details.data.merchant;
                          this.cart_uuid = data.details.cart_uuid;	 	     	
                          this.cart_items = data.details.data.items;	
                          this.cart_summary = data.details.data.summary;
                          this.cart_total = data.details.data.total;		
                          this.error = data.details.error;
                          this.go_checkout = data.details.go_checkout;
                          this.subtotal = data.details.data.subtotal;	
                          this.items_count = data.details.items_count;											 	     		
                     } else {
                          this.cart_items = [];
                          this.cart_summary = [];
                          this.cart_total = [];		 	     		
                          this.error = [];
                          this.subtotal = 0;
                          this.items_count = 0;
                      } 	    
                      
                      this.updateImage();			 	     
                      this.$emit('setCartcount',this.items_count);
    
                     if ((typeof vm !== "undefined") && ( vm !== null)) {
                        vm.hasItemInCart(this.items_count , this.cart_merchant);
                     }
                      
                 });	// ajax done
                 
            },
            getCartCount(){
                var timenow = getTimeNow();	
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),				   
                   'cart_uuid':getCookie('cart_uuid'),
                   'payload': ['items_count']
                };			
                
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getCart",
                     method: "PUT",
                     dataType: "json",
                     data: JSON.stringify($params),
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 		 	    			 	    		 	    					 	   
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {
                     if (data.code==1){	 	    		
                         this.items_count = data.details.items_count;
                     } else  this.items_count = 0;
                     
                     this.$emit('setCartcount',this.items_count);
                     
                 });	// ajax done
                 
            },	
            updateCartQty(addOrSubract,item_qty,cart_row,cart_uuid){						
                if(addOrSubract==1){
                    item_qty++;
                } else item_qty--;
                
                if(item_qty<=0){
                    this.remove(cart_row,cart_uuid);
                } else {				
                    this.updateCartItems(  item_qty,cart_row,cart_uuid );
                }									
            },//	
            updateCartItems(item_qty,cart_row,cart_uuid){
                            
                var $params; var timenow = 1;
                 $params="cart_uuid=" + cart_uuid;
                 $params+="&cart_row=" + cart_row;
                 $params+="&item_qty=" + item_qty;
                 
                this.update_cart_loading = true;
    
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/updateCartItems",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {
                      dump("done");	 	     	
                      if (data.code==1){
                          this.getCartPreview();
                      }	 	    
                 });	// ajax done
                
                ajax_request[timenow].always( data => {
                    this.update_cart_loading = false;
                });	
                
            }, //
            remove(cart_row,cart_uuid){			
                var $params; var timenow = 1;
                 $params="row=" + cart_row +"&cart_uuid="+cart_uuid;
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/removeCartItem",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : 'application/x-www-form-urlencoded; charset=UTF-8' ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {
                      dump(data);	     	
                      if (data.code==1){		 	     		
                          this.getCartPreview();
                      }	 	    
                 });	// ajax done
                
            },//
            clear(cart_uuid){
                
                var $params; var timenow = 1;
                 $params="cart_uuid=" + cart_uuid; 		    
                 $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/clearCart",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     		 	     
                      if (data.code==1){
                          this.getCartPreview();
                      }	 	    
                 });	// ajax done
                 
            }, //
            updateImage(){
                updateLazyImage();
            }, 
            beforeClose(done){           			
                this.$emit('afterDrawerclose',false);
               done();
            },
        },
        template: `
        
        <el-drawer
        v-model="drawer"    
        direction="rtl"    
        size="320px"
        append-to-body="true"	
        :with-header="true"	
        :before-close="beforeClose"
        custom-class="drawer-preview-cart"
        >    
                                   
          <template v-if="cart_loading==false">
            <template v-if="cart_items.length>0">    	        
            
             <div class="mt-3 mb-0">
                <p class="m-0 bold">{{label.your_cart}}</p>
                <a :href="cart_merchant.restaurant_slug" class="m-0 p-0"><h5 class="m-0 chevron d-inline position-relative">{{cart_merchant.restaurant_name}}</h5></a>
                <p class="m-0 text-muted">{{cart_merchant.merchant_address}}</p>
              </div>	  	  	
            
                  <div class="d-flex justify-content-between align-items-center mb-1">
                   <div><h6 class="m-0">{{label.summary}}</h6></div>
                   <div>
                     <a href="javascript:;" @click="clear(cart_uuid)" class="javascript:;">
                       <p class="m-0"><u>{{label.clear}}</u></p>
                     </a>
                   </div>
                </div>
            </template>
            
            <template v-else>
            <h5 class="mt-3 mb-0">{{label.cart}}</h5> 
            <div class="cart-empty text-center" >
              <div class="mt-5">
                <div class="no-results m-auto"></div>
                <h6 class="m-0 mt-3">{{label.no_order}}</h6>
                <p>{{label.lets_change_that}}</p>
              </div>
            </div>
            </template>
            
          </template>  
          
            <!--section-cart-->  
      <DIV class="section-cart">
      
      <div v-cloak class="items" v-for="(items, index) in cart_items" >
      
        <div class="line-items row mb-1">
          <div class="col-3">		               
             <el-image
                style="width: 50px; height: 50px"
                :src="items.url_image"
                :fit="contain"
                lazy
              ></el-image>
          </div> <!--col-->
          
          <div class="col-6 p-0 d-flex justify-content-start flex-column">
          
            <p class="mb-1">
              {{ items.item_name }}
              <template v-if=" items.price.size_name!='' "> 
              ({{items.price.size_name}})
              </template>
            </p>	     
    
             <template v-if="items.price.discount>0">         
               <p class="m-0 font11"><del>{{items.price.pretty_price}}</del> {{items.price.pretty_price_after_discount}}</p>
             </template>
             <template v-else>
               <p class="m-0 font11">{{items.price.pretty_price}}</p>
             </template>
                   
             <!--quantity-->
             <div class="quantity d-flex justify-content-between">
             
                <div>
                 <a href="javascript:;" @click="updateCartQty(0,items.qty,items.cart_row,cart_uuid)"  class="rounded-pill qty-btn" data-id="less">
                  <i class="zmdi zmdi-minus"></i>
                  </a>
                </div>
                
                <div class="qty">{{  items.qty }}</div>
                
                <div>
                <a href="javascript:;" @click="updateCartQty(1,items.qty,items.cart_row,cart_uuid)" class="rounded-pill qty-btn" data-id="plus">
                 <i class="zmdi zmdi-plus"></i>
                 </a>
                </div>
                
             </div>
             <!--quantity-->		 
            
            <p class="mb-0" v-if=" items.special_instructions!='' ">{{ items.special_instructions }}</p>	               
             
            <template v-if=" items.attributes!='' "> 
              <template v-for="(attributes, attributes_key) in items.attributes">                    
                <p class="mb-0">            
                <template v-for="(attributes_data, attributes_index) in attributes">            
                  {{attributes_data}}<template v-if=" attributes_index<(attributes.length-1) ">, </template>
                </template>
                </p>
              </template>
            </template>
            
                  
          </div> <!--col-->
          
          <div class="col-3  quantity d-flex justify-content-start flex-column  text-right">
            <a href="javascript:;" @click="remove(items.cart_row,cart_uuid)" class="rounded-pill ml-auto mb-1"><i class="zmdi zmdi-close"></i></a>
            <template v-if="items.price.discount<=0 ">
              <p class="mb-0">{{ items.price.pretty_total }}</p>
            </template>
            <template v-else>
               <p class="mb-0">{{ items.price.pretty_total_after_discount }}</p>
            </template>
          </div> <!--col-->
          
        </div><!-- line-items-->
        
        <!--addon-items-->
        <div class="addon-items row mb-1"  v-for="(addons, index_addon) in items.addons" >
          <div class="col-3"><!--empty--></div> <!--col-->		     
          <div class="col-9 pl-0 d-flex justify-content-start flex-column">
             <p class="m-0 bold">{{ addons.subcategory_name }}</p>		  
                    
             <template v-cloak v-for="addon_items in addons.addon_items">
             <div class="d-flex justify-content-between mb-1">
               <div class="flexrow"><p class="m-0">{{addon_items.qty}} x {{addon_items.pretty_price}} {{addon_items.sub_item_name}}</p></div>
               <div class="flexrow"><p class="m-0">{{addon_items.pretty_addons_total}}</p></div>
             </div>	<!--flex-->                  
            </template>
             
          </div> <!--col-->		      		      
        </div>
        <!-- addon-items-->
        
      </div> <!--items-->
    
      </DIV>
      
       <template v-if="cart_items.length>0">     
         <div class="divider"></div>   
          <a class="btn btn-green w-100 pointer position-relative" :disabled="hasError" 
          :href="hasError?'javascript:;':go_checkout.link"
          :class="{ loading: update_cart_loading }"
          >
           <div class="d-flex justify-content-between">
             <div>{{label.go_checkout}}</div>
             <div v-if="!update_cart_loading">{{subtotal.value}}</div>
             <div class="m-auto circle-loader" data-loader="circle-side"></div>
           </div>	   
          </a>
       </template> 
       
        <div v-cloak class="alert alert-warning m-0 mt-2" v-if="error.length>0">
          <p class="m-0" v-for="error_msg in error">
          {{ error_msg }}
          </p>
        </div>    
          
        </el-drawer>		
        `
    };
    
    /*
      COMPONENTS ComponentsWebPush
    */
    
    const ComponentsWebPush = {
        props: ['ajax_url' , 'message'],
        data(){
            return {
                settings : [],			
                beams : undefined,
            };
        },
        mounted() {
            this.getWebpushSettings();
        },
        methods:{
            getWebpushSettings(){			
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/getWebpushSettings" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){			
                          this.settings = response.data.details;			 	 				 	 	
                          if(this.settings.enabled==1){			 	 	   
                             this.webPushInit();
                          }
                      } else {						 	 				 	 	
                          this.settings = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
            },
            webPushInit(){						
                if ( this.settings.provider=="pusher" && this.settings.user_settings.enabled==1 ){
                    
                    this.beams = new PusherPushNotifications.Client({
                       instanceId: this.settings.pusher_instance_id ,
                    });
                                
                    this.beams.start()
                    .then(() => {
                        this.beams.setDeviceInterests( this.settings.user_settings.interest )
                        .then(() =>{
                           console.log('Device interests have been set');
                        })			    
                        .then(() => this.beams.getDeviceInterests())
                        .then((interests) => console.log("Current interests:", interests))
                        .catch(e => {
                            var data =  {
                                notification_type :"push",
                                message : "Beams " + e,
                                date : '',
                                image_type : 'icon',
                                image : 'zmdi zmdi-info-outline'
                            };								
                            vm_cart_preview.$refs.notification.addData(data);
                        });			    
                    }).catch(e => {
                        var data =  {
                            notification_type :"push",
                            message : "Beams " + e,
                            date : '',
                            image_type : 'icon',
                            image : 'zmdi zmdi-info-outline'
                        };								
                        vm_cart_preview.$refs.notification.addData(data);
                    });			    
                    
                    
                } else if ( this.settings.provider=="onesignal" ){
                    //
                }
            },		
        },
        template:`	   
        ` 
    };
    
    
    /*
       COMPONENTS NOTIFICATIONS
    */
    const ComponentsNotification = {
        components: {
           'components-webpush': ComponentsWebPush,
        },
        props: ['ajax_url','label','realtime','view_url','avatar','image_background'],
        data(){
           return {		
                 data : [],
                 count : 0,
                 new_message : false,
                 player : undefined,
                 ably : undefined,
                 channel : undefined,
                 piesocket : undefined,
                 observer : undefined,
                 pusher : undefined,
           };
        },
        mounted() {
            this.getAllNotification();    	
            if(this.realtime.enabled){
                this.initRealTime();
            }    	    	    	
    
            this.observer = lozad('.lozad',{
                 loaded: function(el) {
                     el.classList.add('loaded');
                 }
            });       	
        },
        updated () {		
            this.observer.observe();
        },
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
            ReceiveMessage(){
                if(this.new_message){
                   return true;
                }
                return false;
            },
        },
        methods:{
            initRealTime(){
                dump(this.realtime.event_tracking_order);
                if(this.realtime.provider == "pusher"){
                   
                    Pusher.logToConsole = false;
                    this.pusher = new Pusher( this.realtime.key , {
                      cluster: this.realtime.cluster
                    });
                    
                    this.channel = this.pusher.subscribe( this.realtime.channel  );
                    
                    this.channel.bind( this.realtime.event , (data)=> {
                       dump("receive pusher");
                       dump(data);
                       this.playAlert();
                       this.addData(data);			   
                    });
                                
                } else if ( this.realtime.provider =="ably" ){    		
                        
                    this.ably = new Ably.Realtime(this.realtime.ably_apikey);
                    this.ably.connection.on('connected', () => {
                       
                        this.channel = this.ably.channels.get( this.realtime.channel );
                        
                        this.channel.subscribe( this.realtime.event , (message) => {
                             dump("receive ably");
                             dump(message.data);
                             this.playAlert();
                             this.addData(message.data);			   
                        });
                    
                    });
                    
                } else if ( this.realtime.provider =="piesocket" ){
                    
                    this.piesocket = new PieSocket({
                        clusterId: this.realtime.piesocket_clusterid ,
                        apiKey: this.realtime.piesocket_api_key
                    });
                    this.channel = this.piesocket.subscribe(this.realtime.channel); 
                    
                    this.channel.listen( this.realtime.event , (data)=> {
                        dump("receive piesocket");
                        dump(data);
                        this.playAlert();
                        this.addData( data );
                    });
    
                }
                        
            },
            playAlert(){    		
                this.player = new Howl({
                  src: ['../assets/sound/notify.mp3', '../assets/sound/notify.ogg' ],
                  html5: true,			  
                });
                this.player.play();
            },
            getAllNotification(){
                
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/getNotifications" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          this.data = response.data.details.data;
                          this.count = response.data.details.count;			 	 	
                      } else {						 	 	
                          this.data = [];
                          this.count = 0;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
                
            },
            addData($data){        	 
                this.playAlert();       	
                this.data.unshift($data);
                this.count++;     
                this.new_message = true;
                
                setTimeout(()=>{ 		
                    this.new_message = false;
                    this.player.stop();
                }, 1000);           
                      
            },
            clearAll(){
                       
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/clearNotifications" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          this.data = [];
                          this.count = 0;			 	 	
                      } else {						 	 	
                          notify( response.data.msg ,'error')
                      }
                      
                      this.new_message = false;
                      
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
            },
        },
        template: `
        
        <components-webpush
         :ajax_url="ajax_url"
         :message='label'
        />
        </components-webpush>
            
        <div class="btn-group pull-right notification-dropdown">
              <button type="button" class="btn p-0 btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="img-30 rounded-pill lozad" :src="avatar" :data-background-image="image_background" />
                <span v-if="count>0" :class="{'shake-constant shake-chunk' : ReceiveMessage }" class="badge rounded-circle badge-danger count">{{count}}</span>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header d-flex justify-content-between">
                   <div class="flex-col">
                     <div class="d-flex align-items-center">
                      <h5 class="m-0 mr-2">{{label.title}}</h5>
                      <span class="badge rounded-circle badge-green badge-25">{{count}}</span>
                     </div>
                   </div>
                   <div class="flex-col" v-if="hasData">
                    <a @click="clearAll">{{label.clear}}</a>
                   </div>
                </div>
                <!--header-->
                
                <!--content-->            
                <ul v-if="hasData"  class="list-unstyled m-0">
                 <li v-for="(item,index) in data">
                  <a :class="{ active: index<=0 }" >
                    <div class="d-flex">
                       <div v-if="item.image!=''" class="flex-col mr-3">  
                          <template v-if="item.image_type=='icon'">
                             <div class="notify-icon rounded-circle bg-soft-primary">
                                <i :class="item.image"></i>
                              </div>
                          </template>
                          <template v-else>
                           <div class="notify-icon">
                              <img class="img-40 rounded-circle" :src="item.image" />
                           </div>
                          </template>
                       </div>
                       <div class="flex-col">
                          <div class="text-heading" v-html="item.message"></div>
                          <div class="dropdown-text-light">{{item.date}}</div>
                       </div>
                    </div>
                  </a>
                 </li>		            		             
                </ul>
                <!--content-->
                
                <div v-if="!hasData" class="none-notification text-center">
                  <div class="image-notification m-auto"></div>
                  <h5 class="m-0 mb-1 mt-2">{{label.no_notification}}</h5>
                  <p class="m-0 font11 text-muted">{{label.no_notification_content}}</p>
                </div>
                
                <div v-if="hasData" class="footer-dropdown text-center">
                <a :href="view_url" targe="_blank" class="text-primary">{{label.view}}</a>
                </div>
                
              </div> <!--dropdown-menu-->
          </div>
          <!--btn-group-->
        ` 
    };
    
    const ComponentsLanguage = {
      props : ['ajax_url'],
      data(){
        return {				
            data : [],
            is_loading : false
        };
      },
      mounted () {
        this.getLanguage();
      },
      methods :{
          getLanguage(){
            this.is_loading = true;
            axios({
                method: 'POST',
                url: this.ajax_url+"/getLanguage"  ,
                data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                timeout: $timeout,
              }).then( response => {	 
                   if(response.data.code==1){	
                       this.data = response.data.details;
                   } else {						 	 				 	 	
                       this.data = [];
                   }			 			 	 
              }).catch(error => {	
                 //
              }).then(data => {			     
                this.is_loading = false;
              });			
          },
          setLang(link){
            window.location.href = link;
          }
      },
      template : `  
      <el-skeleton style="width: 150px" :loading="is_loading" animated>
      <template #template>
           <el-skeleton-item variant="image" style="width: 150px; height: 20px" />
      </template>	 
      <template #default>  
      
      <el-dropdown trigger="click" v-if="data.enabled">
        <span class="el-dropdown-link">
    
           <div class="d-flex align-items-center">
             <div class="mr-2">
             <el-image
                style="width: 30px; height: 25px"
                :src="data.default.flag"
                :fit="contain"
                lazy
              ></el-image>
             </div>
             <div class="mr-2">{{data.default.title}}</div>
             <div><i class="zmdi zmdi-chevron-down"></i></div>
           </div>	         
        </span>
        <template #dropdown>
          <el-dropdown-menu>
            <el-dropdown-item v-for="item in data.data" >
            
            <el-dropdown-item @click="setLang(item.link)">
            <div class="row no-gutters align-items-center">
              <div class="col mr-3">
                <el-image
                style="width: 30px; height: 25px"
                :src="item.flag"
                :fit="contain"			
                ></el-image>		
              </div>
              <div class="col">{{item.title}}</div>
            </div>
            </el-dropdown-item> 
    
            </el-dropdown-item>        
          </el-dropdown-menu>
        </template>
      </el-dropdown>
    
      </template>	 
      </el-skeleton>
      `
    };
    
    /*
      CART PREVIEW  
      target = vue-cart-preview
    */
    const app_cart_preview = Vue.createApp({
      components: {
        'component-cart-preview': ComponentCartPreview,
        'components-notification': ComponentsNotification, 	 
        'components-language': ComponentsLanguage, 	 
      },
      mounted () {
           this.getCartPreview();
      },
      data(){
        return {				
            items_count:0,
            drawer : false,
            drawer_preview_cart : false,
        };
      },
      methods :{
           getCartPreview(){
               this.$refs.childref.getCartPreview();
           },
           cartCount(items){  	 	
               this.items_count = items;
           },	 
         showCartPreview(){		 
             this.drawer_preview_cart = true;
         },
         afterDrawerclose(){
            this.drawer_preview_cart = false;
         },
      },
    });
    app_cart_preview.use(ElementPlus);
    const vm_cart_preview = app_cart_preview.mount('#vue-cart-preview');
    
    
    
    /*
    COMPONENTS ALERT
    */
    const ComponentsAlert = {
         props: ['title','button'],	 
         data(){
            return {				
                data:[]
            };
         },	 
         methods :{
             show(){
                 $('#ModalAlert').modal('show');
             },	 
             close(){
                   $('#ModalAlert').modal('hide');
             },
             alert(data){
               this.data = data;
               dump(this.data);
               this.show();
             },
         },
         template: `
          <div class="modal" id="ModalAlert" tabindex="-1" role="dialog" aria-labelledby="ModalAlert" aria-hidden="true">
           <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">     
                    
            <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">{{ title }}</h5>
                <button type="button" class="close"  aria-label="Close" @click="close" >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div> 
              
              <div class="modal-body" >                      
                <div v-for="item in data.text" >
                {{ item }}
                </div>
              </div>
              
              <div class="modal-footer justify-content-center">
                <button class="btn btn-black w-25" @click="close" >               
                   {{button}}
                </button>
              </div> <!--footer-->
           
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->
         `,
    };
    
    
    const component_alert = Vue.createApp({
      components: {
        'component-alert': ComponentsAlert,     
      },
      methods :{
           alert(message){
               this.$refs.Alertref.alert( {'text': message } );
           },  
      },
    }).mount('#component_alert');
    
    /*
    COMPONENTS CANCEL ORDER
    */
    const ComponentsCancelOrder = {	
        props: ['label'],	 
         data(){
            return {				
                data:[],
                loading : false,
                merchant_name : '',
                cancel_status : false,
                cancel_msg : '',
                order_uuid : '',
                error : [],
                is_loading : false,
                cancel_response : []
            };
         },
         methods :{
             show(){
                 $('#ModalCancelOrder').modal('show');
             },	 
             close(){
                   $('#ModalCancelOrder').modal('hide');
                   this.order_uuid = '';
             },	 	
             cancel(data){	 		
                 this.merchant_name = data.merchant_name;	 		 	
                 this.show();	 	
                 
                 this.order_uuid = data.order_uuid;
                 
                 var $params = {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                       'order_uuid' : this.order_uuid
                    };
                    
                var timenow = 1;
                $params = JSON.stringify($params);	  
    
                 ajax_request[timenow] = $.ajax({
                    url: ajaxurl+"/cancelOrderStatus",
                    method: "PUT",
                    dataType: "json",
                    data: $params ,
                    contentType : $content_type.json ,
                    timeout: $timeout,
                    crossDomain: true,
                    beforeSend: data => {	
                        this.loading = true;	
                        this.cancel_status = false;		
                        this.cancel_msg = '';					
                        if(ajax_request[timenow] != null) {	
                            ajax_request[timenow].abort();
                        }
                    }
                });	
                        
                ajax_request[timenow].done( data => {	
                     if ( data.code==1){
                            this.cancel_status = data.details.cancel_status;
                            this.cancel_msg = data.details.cancel_msg;
                            this.cancel_response = data.details;
                     } else {
                          this.cancel_status = false;
                          this.cancel_response = [];
                     }					
                });		
    
                ajax_request[timenow].always( data => {
                      this.loading = false;			
                });	
                     
             }, 
             applyCancelOrder(){
                 
                 var $params = {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                       'order_uuid' : this.order_uuid
                    };
                    
                var timenow = 1;
                $params = JSON.stringify($params);	 
                 
                ajax_request[timenow] = $.ajax({
                    url: ajaxurl+"/applyCancelOrder",
                    method: "PUT",
                    dataType: "json",
                    data: $params ,
                    contentType : $content_type.json ,
                    timeout: $timeout,
                    crossDomain: true,
                    beforeSend: data => {		
                        this.error = [];
                        this.is_loading = true;
                        if(ajax_request[timenow] != null) {	
                            ajax_request[timenow].abort();
                        }
                    }
                });	
                        
                ajax_request[timenow].done( data => {	
                     if ( data.code==1){	
                          notify(data.msg,'success');
                          this.close();
                          vm_my_order.page = 0;
                          vm_my_order.data = [];
                          vm_my_order.orderHistory();			 	 
                     } else {		
                          this.error = data.msg;		 	 
                     }					
                });		
                 
                ajax_request[timenow].always( data => {
                      this.is_loading = false;	
                });	
             }, 	 
         },
         template: `
          <div class="modal" id="ModalCancelOrder" tabindex="-1" role="dialog" aria-labelledby="ModalCancelOrder" aria-hidden="true"  data-backdrop="static" data-keyboard="false" >
           <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">     
                    
            <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">{{merchant_name}}</h5>
                <button type="button" class="close"  aria-label="Close" @click="close" :disabled="is_loading" >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div> 
              
              <DIV v-if="loading">
                  <div class="loading mt-5 mb-5">      
                    <div class="m-auto circle-loader" data-loader="circle-side"></div>
                  </div>
              </DIV>  
              
               <DIV v-else>
              
              <div class="modal-body" >                      
                
                <h5 v-if="cancel_response.refund_status==='full_refund'">{{label.are_you_sure}}</h5>
                <h5 v-else>{{label.how}}</h5>
                
                <div class="mt-3">
                <p>{{cancel_msg}}</p>
                </div>
                
                <div class="mt-3 mb-2" v-if="cancel_response.refund_msg">
                 <p>{{cancel_response.refund_msg}}</p>
                </div>
                
                <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                    <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
                 </div>   
                
              </div> <!--body-->
                        
              <div class="modal-footer justify-content-center">                      
                <button class="btn btn-black w-100" @click="close" :disabled="is_loading" >               
                   {{label.dont}}
                </button>
                <button class="btn btn-green w-100" :disabled="!cancel_status" @click="applyCancelOrder()" 
                :class="{ loading: is_loading }" >               
                   <span class="label">{{label.cancel}}</span>
                   <div class="m-auto circle-loader" data-loader="circle-side"></div>
                </button>
              </div> <!--footer-->
              
              </DIV> <!--end if-->
           
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->
         `,
    };
    
    
    /*
      MY ORDER
      target = vue-my-order
    */
    const app_my_order = Vue.createApp({
      components: {    
        'component-cancel-order' : ComponentsCancelOrder,    
        'components-review' : ComponentsReview,     
      },
      mounted () {
           this.orderHistory(); 
           this.orderSummary();   	  
      },
      data(){
        return {					
            show_next_page : false,
            loading : false,
            load_more : false,
            page : 0,
            data : [],
            merchants : [],
            items : [],
            size : [],
            status : [],
            services : [],
            show_details : false,
            order_loading : false,
            order_merchant : [],
            order_items : [],
            order_summary : [],
            order_info : [],
            order_status : [],
            order_services : [],
            order_label : [],
            status_allowed_cancelled : [],
            status_allowed_review : [],
            q : '',
            awaitingSearch : false,	
            search_found : false,
            summary_data : [],
            total_qty : 0,
            animated_total_qty : 0,
            total_order_raw : 0,
            animated_total_order : 0,
            refund_transaction : []
        };
      },
      watch: {
          q(newsearch,oldsearch){
              if (!this.awaitingSearch) {
                  if(empty(newsearch)){
                      return false;
                }
                        
                setTimeout(() => {		      
                    
                    var $params; var timenow = getTimeNow(); 		    
                       $params="page=0";
                       $params+="&q="+ this.q;
                     $params+=addValidationRequest(); 		    
                     ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/orderHistory",
                         method: "POST",
                         dataType: "json",
                         data: $params,
                         contentType : $content_type.form ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: function( xhr ) {		 	    				 	    
                              if(ajax_request[timenow] != null) {	
                                 ajax_request[timenow].abort();
                              }
                         }
                     });
                     
                     ajax_request[timenow].done( data => {	 	     		 	     
                          if (data.code==1){
                              this.data = [];		 	     	
                              this.show_next_page = data.details.show_next_page;
                              this.page = data.details.page;
                              //this.data = data.details.data.data;
                              this.data.push(data.details.data.data);	
                              this.merchants = data.details.data.merchants;
                              this.items = data.details.data.items;
                              this.size = data.details.data.size;
                              this.status = data.details.data.status;
                              this.services = data.details.data.services;	 	
                              this.status_allowed_cancelled = data.details.data.status_allowed_cancelled;	 	
                              this.status_allowed_review = data.details.data.status_allowed_review;	 
                              this.search_found = true;	
                          } else {
                              this.data = [];
                              this.search_found = true;
                          } 	    	 	    
                     });	// ajax done
                     
                     ajax_request[timenow].always( data => {	 	    	
                         this.awaitingSearch = false;
                    });		
                    
                }, 1000); // 1 sec delay
                
                 this.awaitingSearch = true;
              }
          },
          
          total_qty(newValue){  		
              gsap.to(this.$data, { duration: 0.6, animated_total_qty : newValue });
          },
          
          total_order_raw(newValue){  		
              gsap.to(this.$data, { duration: 0.6, animated_total_order : newValue });
          },
          
      },
      computed: {
        hasData(){			
            if(this.search_found){
               return true;
            } 
            return false;
        },
        hasResults(){			
            if(this.data.length>0){
               return true;
            } 
            return false;
        },
        hasRefund(){			
            if(this.refund_transaction.length>0){
               return true;
            } 
            return false;
        },
        animatedNumber() {
          return this.animated_total_qty.toFixed(0);
        },
        animatedTotal() {
          return this.animated_total_order.toFixed(2);
        },
      },
      methods :{
           clearData(){
            this.search_found = false;
            this.q = '';
            this.page = 0;
            this.data = [];
            this.orderHistory();
         }, 		
           orderHistory(){
               
                if(!this.load_more){
                    this.loading = true;					
                }		    
    
                   var $params; var timenow = getTimeNow(); 		    
                   $params="page="+ this.page;
                 $params+=addValidationRequest(); 		    
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/orderHistory",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	    			 	    	     	    	   
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	     		 	     
                      if (data.code==1){
                          this.show_next_page = data.details.show_next_page;
                          this.page = data.details.page;
                          
                          /*this.data = data.details.data.data;
                          this.merchants = data.details.data.merchants;
                          this.items = data.details.data.items;
                          this.size = data.details.data.size;
                          this.status = data.details.data.status;
                          this.services = data.details.data.services;	 	
                          this.status_allowed_cancelled = data.details.data.status_allowed_cancelled;	 	
                          this.status_allowed_review = data.details.data.status_allowed_review;*/	 	
                                           
                          this.data.push(data.details.data.data);
                          this.merchants = this.addObjectToArray( this.merchants, data.details.data.merchants );
                          this.items = this.addObjectToArray( this.items, data.details.data.items );	 	     		
                          this.status = data.details.data.status;
                          this.services = data.details.data.services;	 	
                          this.status_allowed_cancelled = data.details.data.status_allowed_cancelled;	 	
                          this.status_allowed_review = data.details.data.status_allowed_review;
                          
                      } else {
                          
                      } 	    	 	    
                 });	// ajax done
                 
                 ajax_request[timenow].always( data => {
                   this.loading = false;
                   this.load_more = false;
                });				
               
           },    	
           addObjectToArray(element, element_new){  	 	
               var new_object = {};
               Object.entries(element).forEach(([key,value]) => { new_object[key] = value });
               Object.entries(element_new).forEach(([key,value]) => { new_object[key] = value });  	 	
               return new_object;
           },  
           loadMoreOrders(page){
               this.page = page;  	 
               this.load_more = true;	
               this.orderHistory();
           },
           orderDetails(order_uuid){  	 	
               this.show_details = true;
                          
               var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                   'order_uuid' : order_uuid  
                };
                
            var timenow = 1;
            $params = JSON.stringify($params);	  	 	
            
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/orderDetails",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => {
                     
                     this.order_loading = true;
                     this.order_merchant = [];
                     this.order_items = [];
                     this.order_summary = [];
                     this.order_info = [];
                     this.order_status = [];
                     this.order_services = [];
                     this.order_label = [];
                                  
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             });
             
             ajax_request[timenow].done( data => {	 	    		 	    
                 if ( data.code==1){		 	    		
                     this.order_merchant = data.details.data.merchant;
                     this.order_items = data.details.data.items;
                     this.order_summary = data.details.data.summary;
                     this.order_info = data.details.data.order.order_info;
                     this.order_label = data.details.data.label;
                     this.refund_transaction = data.details.data.refund_transaction;
                     
                     this.order_status = data.details.data.order.status;
                     this.order_services = data.details.data.order.services;
                     
                     this.order_services = this.order_services[this.order_info.service_code];
                     this.order_status = this.order_status[this.order_info.status]; 	 
                                
                 } 	     	    
            });		
               
            ajax_request[timenow].always( data => {
                 this.order_loading = false;
                  this.updateImage();
            });				
            
           },
           closeOrderDetails(){
               this.show_details = false;
           },  
           updateImage(){ 
            updateLazyImage();
         },
         buyAgain(order_uuid , close_view){          	
             var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                   'order_uuid' : order_uuid,
                   'cart_uuid' : getCookie('cart_uuid')
                };
                
            var timenow = 1;
            $params = JSON.stringify($params);				
             
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/orderBuyAgain",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	   		 	    
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             });
             
             ajax_request[timenow].done( data => {
                 if ( data.code==1){		 	    		
                     setCookie('cart_uuid',data.details.cart_uuid,30);
    
                     if(close_view==1){
                         this.show_details = false;
                     }        	 
                                     
                     vm_cart_preview.getCartPreview();
                     if ((typeof vm_cart_preview !== "undefined") && ( vm_cart_preview !== null)) {
                        vm_cart_preview.showCartPreview();
                    }
                 } else { 	    		    	
                     component_alert.alert(data.msg);
                 }	  	    
            });
                    
         },
         callCancel(order_uuid,merchant_name){     	
             this.$refs.orderCancelRef.cancel( {'merchant_name': merchant_name ,'order_uuid' : order_uuid } );
         },
         writeReview(order_uuid){
             dump('writeReview' + order_uuid);
             this.$refs.ReviewRef.order_uuid = order_uuid;
             this.$refs.ReviewRef.show();
         },       
         orderSummary(){
             
             var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   
                };
                
            var timenow = getTimeNow();
            $params = JSON.stringify($params);				
             
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/orderSummary",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	   		 	    
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             });
             
             ajax_request[timenow].done( data => {
                 this.summary_data = data.details.summary;
                 this.total_qty = data.details.summary.total_qty;
                 this.total_order_raw = data.details.summary.total_order_raw;
             });
             
         },
      },
    });
    app_my_order.use(ElementPlus);
    const vm_my_order = app_my_order.mount('#vue-my-order');
    
    
    
    const vm_bootbox = Vue.createApp({
      components: {
        'component-bootbox': Componentsbootbox,    
      },
      data(){
          return {
            resolvePromise: undefined,
          rejectPromise: undefined,
          }
      },
      methods :{
        confirm(){
            return new Promise((resolve, reject) => {	
               this.resolvePromise = resolve
               this.rejectPromise = reject	
               this.$refs.bootbox.confirm();
            });
        },
        Callback(result){		
            this.resolvePromise(result); 
        },
        alert(message,options){
            this.$refs.bootbox.alert(message,options);
        },
      },
    }).mount('#vue-bootbox');
    
    
    
    
    /*
    CUSTOMER ADDRESS
    */
    const app_address = Vue.createApp({
      components: {
          'component-new-address': changeAddressComponent,    
          'component-address': ComponentsAddress,    
      },
      mounted () {
           this.getAddresses(false);
      },  
      data(){
           return {
               data : [],
               error : [],
               is_loading : false,
               reload_loading : false,
               total_address : 0,
               total_address_animated : 0,
           };
      },
      watch: {
        total_address(newValue) {
          gsap.to(this.$data, { duration: 0.5, total_address_animated: newValue })
        },
      },
      computed: {
        animatedTotal() {
          return this.total_address_animated.toFixed(0)
        }
      },
      methods :{
           getAddresses($reload){
               
               this.reload_loading = $reload;
               
               var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
            };			
            var timenow = getTimeNow();
            $params = JSON.stringify($params);				
             
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/getAddresses",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	   	
                     if(!$reload) { this.is_loading = true; } else { this.reload_loading = true; } 
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             }); 	   
              
             ajax_request[timenow].done( data => {
                 if ( data.code==1){	 	
                     this.data = data.details.data;    
                     this.total_address = this.data.length;
                 } else {
                     this.data = [];
                     this.total_address = 0;
                 } 	    
            });				
            
            ajax_request[timenow].always( data => {
                 this.is_loading = false;
                 this.reload_loading = false;
            });				
               
           },  
           showNewAddress(){  	 	
               this.$refs.refnewaddress.autosaved_addres = false;
             this.$refs.refnewaddress.showModal();
         },   
         ShowAddress(address_uuid){        
             this.$refs.address.show( address_uuid );
         },   
         setLocationDetails(data){     	
             this.$refs.address.showWithData( data );
         },
         ConfirmDelete(address_uuid){     	
             this.$refs.address.ConfirmDelete( address_uuid );   	     
         },  
      },
    });
    app_address.use(ElementPlus);
    const vm_addresses = app_address.mount('#vue-my-address');
    
    
    
    /*
    COMPONENTS PAYMENT METHOD
    */
    const ComponentsPaymentMethod = {
        props: ['label','payment_type','show_payment'],
        data() {
            return {
              data : [],
              display : false,
           }
        },
        mounted () {
             this.getPaymentMethod();
             this.display = this.show_payment;
        },
        methods :{
            getPaymentMethod(){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                   'payment_type' : this.payment_type		   
                };
                
                var timenow = getTimeNow();
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/PaymentMethod",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		 	    		
                         this.data = data.details.data;
                     } else {
                         this.data = [];
                     }
                });	
                
            },
            showPayment(payment_code){
                this.$emit('setPayment',payment_code);
            },    
        },
        template: `
        <!--PAYMENT METHODS-->
        <transition name="slide-fade">
        <div class="card mb-3" v-if="display" >
            <div class="card-body">
            
            <h5 class="mb-3">{{label.add_new_payment}}</h5>
            
            <a  v-for="payment in data" @click="showPayment(payment.payment_code)"
            href="javascript:;" class="d-block chevron-section medium d-flex align-items-center rounded mb-2">
                <div class="flexcol mr-2 payment-logo-wrap">
                  <i v-if="payment.logo_type=='icon'" :class="payment.logo_class"></i>
                   <img v-else class="img-35" :src="payment.logo_image" />  
                </div>
                
                <div class="flexcol" > 		     		     		      
                   <span class="mr-1">{{payment.payment_name}}</span>          
                </div> 		    		    		    
             </a> 
            
            </div> <!--card body-->
        </div> <!--card-->
        </transition>
        <!--END PAYMENT METHODS-->
        `,
    };
    
    
    /*
    CUSTOMER PAYMENTS
    */
    const app_payments = Vue.createApp({
       data(){
           return {
               data : [],
               default_payment_uuid : '',
               error : [],
               is_loading : false,
               reload_loading : false,
               payment_method : false,
           };
       },
       mounted () {
           this.getMyPayments(false);
       },
       watch: {
              payment_method(newdata,olddata){	  		
                  this.$refs.payment_method.display = newdata;
              }
       },
       methods :{
              getMyPayments($reload){
                   
                     this.reload_loading = $reload;
                     
                     var $params = {
                 'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
               };			
               var timenow = getTimeNow();
               $params = JSON.stringify($params);				
             
               ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/MyPayments",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         if(!$reload) { this.is_loading = true; } else { this.reload_loading = true; } 
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	 
                 
                  ajax_request[timenow].done( data => {
                     if ( data.code==1){	 	
                         this.data = data.details.data;    	
                         this.default_payment_uuid  = data.details.default_payment_uuid;	
                     } else {
                         this.data = [];
                         this.default_payment_uuid = '';
                     } 	    
                });				
                
                ajax_request[timenow].always( data => {
                     this.is_loading = false;
                     this.reload_loading = false;
                });			 
                   
              },   
              ConfirmDelete(payment_uuid){		   		   	
                   vm_bootbox.confirm().then((result) => {		  
                       if(result){
                          this.deletePayment(payment_uuid);
                       }
                   });		   	
            },
            deletePayment(payment_uuid){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
                   'payment_uuid' : payment_uuid
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);	
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/deletePayment",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   		 	    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	   
                 
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){	 		 	    		
                         this.getMyPayments(true);
                     } else {
                         vm_bootbox.alert( data.msg , {} );
                     } 	    
                });			
                
                ajax_request[timenow].always( data => {
                     
                });	
                
            },   
            editPayment(data){		
                this.$refs[data.payment_code].Get(data.reference_id);
            },   
            SavedPaymentList(){
                this.getMyPayments(true);
            },
            showPayment(payment_code){	
                try {		
                    this.$refs[payment_code].showPaymentForm();
                } catch(err) {  	 	  	       
                     vm_bootbox.alert( err , {} );
                   }
            },		
            setDefaultPayment(payment_uuid){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                   'payment_uuid' : payment_uuid		   
                };
                
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/setDefaultPayment",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){		 	    		 	    		
                         this.getMyPayments(true);
                     } else {
                         vm_bootbox.alert( data.msg , {} );
                     }
                });	
                
            },
       },
    });
    //.mount('#vue-my-payments');
    
    if ((typeof  components_bundle !== "undefined") && ( components_bundle !== null)) {
        $.each(components_bundle,function(components_name, components_value) {
            app_payments.component(components_name, components_value );		
        });
    }
    
    app_payments.component("components-payment-method",ComponentsPaymentMethod);
    
    app_payments.use(Maska);
    app_payments.use(ElementPlus);
    const vm_my_payments  = app_payments.mount('#vue-my-payments');			 
    
    
    /*
    LIST SAVED STORES
    */
    const app_my_store = Vue.createApp({
      components: {    
         'component-save-store' : ComponentsSaveStore,    	   
      },
      data(){
        return {				
            data : [],
            is_loading : false,
            reload_loading : false,
            services : [],
            estimation : [],
        };
      },
      mounted () {
            this.saveStoreList(false);
      },
      updated () {
        dump("updated");
        
         $('.list-items .lazy').each(function(index, $obj){			 	    			 	
            if ( !empty($(this).attr("src")) && !empty($(this).attr("data-src")) ){
                $(this).attr("src", $(this).attr("data-src") );
            }		
         });  	
        
      },
      methods :{
          
           saveStoreList($reload) {
               
               this.reload_loading = $reload;
               
               var $params = {
               'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		   
            };			
            var timenow = getTimeNow();
            $params = JSON.stringify($params);	
            
            ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/saveStoreList",
                 method: "PUT",
                 dataType: "json",
                 data: $params ,
                 contentType : $content_type.json ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: data => { 	   	
                     if(!$reload) { this.is_loading = true; } else { this.reload_loading = true; } 
                      if(ajax_request[timenow] != null) {	
                          ajax_request[timenow].abort();
                      }
                 }
             }); 	   
             
             ajax_request[timenow].done( data => {
                 if ( data.code==1){	 	
                     this.data = data.details.data;    		
                     this.services = data.details.services;  
                     this.estimation = data.details.estimation;   	    		
                 } else {
                     this.data = []; this.services = []; this.estimation = [];
                 } 	    
            });		
            
            ajax_request[timenow].always( data => { 	   
                 this.is_loading = false; 
                 this.reload_loading = false; 
                 
            });			
               
           },   
           afterSaveStore(item){       	  
               item.saved_store = item.saved_store==1?false:true; 
         },	 	
      },
    });
    app_my_store.use(ElementPlus);
    const vm_my_store = app_my_store.mount('#vue-saved-store');
    
    
    
    /*
      COMPONENTS MAPS AUTO COMPLETE
    */
    const ComponentsAutoComplete = {
        props: ['label','modelValue'],
        emits: ['update:modelValue'],
        data(){
            return {
               q: '',
               awaitingSearch : false,		   
               show_list : false,
               data : [],
               error : [],	
               hasSelected : false,			   
            };	
        },	
        watch: {
            q(newsearch,oldsearch){
                        
                this.$emit('update:modelValue', newsearch);
                
                if (!this.awaitingSearch) {
                                  
                  if(empty(newsearch)){
                      return false;
                  }	
                  
                  if(this.q.length>20){
                       return false;
                  }
                  
                  this.show_list = true;
                  
                  setTimeout(() => {
                    
                      var $params; var timenow = 2;
                     $params="q=" + this.q;
                     $params+=addValidationRequest();
                     ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/getlocation_autocomplete",
                         method: "POST",
                         dataType: "json",
                         data: $params,
                         contentType : $content_type.form ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: data => {
                              if(ajax_request[timenow] != null) {	
                                 ajax_request[timenow].abort();
                              }
                         }
                     });
                     
                     ajax_request[timenow].done( data => {	 	     	    
                         if ( data.code==1){
                             this.data = data.details.data;
                         } else {
                             this.data = [];
                         }		 	    		 	    
                     });	// ajax done
                    
                     ajax_request[timenow].always( data => {	 	    	
                         this.awaitingSearch = false;
                    });		
                                
                  }, 1000); // 1 sec delay
                }	        
                
                this.data = [];
                this.awaitingSearch = true;
            },		
        },
        methods: {
           showList(){
               this.show_list = true;
            },
            clearData(){
                this.data = [];
                this.q = '';
            }, 				
            setData(data){			
                this.clearData();
                this.q = data.description;
                this.$emit('update:modelValue', data.description);
                this.$emit('afterChoose',data);					
            },		
        }, 
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
        },
        template: `		
        <div class="position-relative search-geocomplete"> 	
           <div v-if="!awaitingSearch" class="img-20 m-auto pin_placeholder icon"></div>
           <div v-if="awaitingSearch" class="icon" data-loader="circle"></div>    
                  
           <input @click="showList" v-model="q" class="form-control form-control-text" 
           :placeholder="label.enter_address" >
           
           <div v-if="hasData" @click="clearData" class="icon-remove"><i class="zmdi zmdi-close"></i></div>
           
          </div>
        
           <div class="search-geocomplete-results" v-if="show_list">		      
            <ul class="list-unstyled m-0 border">
             <li v-for="items in data">			      
              <a href="javascript:;" @click="setData(items)" >
               <h6 class="m-0">{{items.description}}</h6>	       
              </a>
             </li>	     
            </ul>
       </div>
        `
    };
    /*END AUTO COMPLETE*/
    
    
    
    /*
      MERCHANT SIGNUP
    */
    const app_merchant_signup = Vue.createApp({	
      components: {    
           'component-auto-complete' : ComponentsAutoComplete,  	  
           'component-phone' : ComponentsPhoneNumber,  
           'vue-recaptcha' : componentsRecaptcha,  
      },
      data(){
        return {				
            data : [],
            loading : false,		
            mobile_prefixes : [],
            restaurant_name : '',
            address : '',
            contact_email : '',
            mobile_prefix : '',
            mobile_number : '',
            membership_type : '',
            membership_list : [],
            response : [],
            recaptcha_response : '',
            show_recaptcha : true,
        };
      },	
      mounted () {
            this.getSignupAttributes();
      },  
      computed: {
         checkForm(){	
             
             if(empty(this.restaurant_name)){
                 return true;
             }
             if(empty(this.address)){
                 return true;
             }
             if(empty(this.contact_email)){
                 return true;
             }
             if(!this.validEmail(this.contact_email)){
                 return true;
             }
             if(empty(this.mobile_number)){
                 return true;
             }
             if(empty(this.mobile_prefix)){
                 return true;
             }
             if(empty(this.membership_type)){
                 return true;
             }
             
            return false;
         },
      },
      methods :{  
         getSignupAttributes(){			
            var $params; var timenow = getTimeNow(); 	 		   
                $params+=addValidationRequest();
             ajax_request[timenow] = $.ajax({
                 url: ajaxurl+"/getSignupAttributes",
                 method: "POST",
                 dataType: "json",
                 data: $params,
                 contentType : $content_type.form ,
                 timeout: $timeout,
                 crossDomain: true,
                 beforeSend: function( xhr ) {
                      if(ajax_request[timenow] != null) {	
                         ajax_request[timenow].abort();
                      }
                 }
             });
             
             ajax_request[timenow].done( data => {	 	     	    
                 if(data.code==1){
                    this.mobile_prefixes = data.details.mobile_prefixes;
                    this.membership_list = data.details.membership_list;
                    this.show_recaptcha =  data.details.capcha;
                 } else {
                    this.mobile_prefixes = [];
                    this.membership_list = '';
                    this.show_recaptcha =  false;
                 } 	    
             });	// ajax done			 	   
         },		 
         verifyForms(){
             if ( this.show_recaptcha){
                 this.verifyRecaptcha();
             } else {
                 this.submit();
             }
         },
         verifyRecaptcha(){
             
              var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'recaptcha_response': this.recaptcha_response,		   
                };			
                var timenow = 11;
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/verifyRecaptcha",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	   
                 
                 ajax_request[timenow].done( data => {	 	     	    	 	    		 	    	
                     if(data.code==1){
                         this.submit();
                     } else if ( data.code==3){
                         this.recaptchaExpired();
                         this.response.code = 2;
                         this.response.msg = data.msg;
                     } else {
                         this.response = data;
                     }	 	    
                 });	// ajax done	
             
                 ajax_request[timenow].always( data => {	 	    	
                     this.loading = false;
                });		
         },
         submit(){
                  
                 var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                   'restaurant_name': this.restaurant_name,
                   'address': this.address,
                   'contact_email': this.contact_email,
                   'mobile_prefix': this.mobile_prefix,
                   'mobile_number': this.mobile_number,
                   'membership_type' : this.membership_type,
                };			
                var timenow = 22;
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/CreateAccountMerchant",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	   
                 
                 ajax_request[timenow].done( data => {	 	     	    
                     this.response = data;
                     if(data.code==1){	 	    		
                         window.location.href = data.details.redirect;
                     }
                 });	// ajax done	
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.loading = false;
                });		
             
         },
         validEmail: function (email) {
              var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(email);
         },
         recaptchaVerified(response) {
             this.recaptcha_response = response;
         },
         recaptchaExpired() {
           this.$refs.vueRecaptcha.reset();
         },
         recaptchaFailed() {
                 
          },     
      },
    });
    
    app_merchant_signup.use(Maska);
    const vm_merchant_signup = app_merchant_signup.mount('#vue-merchant-signup');
    
    
    /*  
       MERCHANT USER SIGNUP   
    */
    
    const app_merchant_user  = Vue.createApp({
        components: {    	    	 
           'component-phone' : ComponentsPhoneNumber,  
           'vue-recaptcha' : componentsRecaptcha,  
        },
        data(){
            return {
                is_loading : false,			
                show_password : false,
                agree_terms : false,
                password_type: 'password',
                first_name : '',
                last_name : '',
                contact_email : '',
                mobile_number : '',
                mobile_prefix :'',
                password : '',
                cpassword :'',
                mobile_prefixes : [],
                error : [],
                success : '',
                redirect : '',
                next_url : '',
                recaptcha_response : '',
                show_recaptcha : false,		
                merchant_uuid : '',
                username : ''
            };
        },
        mounted () {	   
            this.merchant_uuid = _merchant_uuid;
        },
        computed: {
            DataValid(){				
                if(!empty(this.first_name) && empty(!this.last_name) && empty(!this.contact_email) && empty(!this.mobile_number)
                && empty(!this.password) && empty(!this.cpassword) && empty(!this.username)   ){
                    return true;									
                }			
                return false;
            },	
        },	 
        methods: {		
            showPassword()
            {			
                this.show_password = this.show_password==true?false:true;
                if(this.show_password){
                    this.password_type = 'text';
                } else this.password_type = 'password';
            },			
            onRegister(){
                
                this.is_loading =  true;
                this.error = [];
                axios({
                   method: 'PUT',
                   url: ajaxurl+"/createMerchantUser" ,
                   data : {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                         'username' : this.username,
                         'password' : this.password,
                         'cpassword' : this.cpassword,
                     'first_name' : this.first_name,
                     'last_name' : this.last_name,
                     'contact_email' : this.contact_email,
                     'mobile_number' : this.mobile_number,
                     'mobile_prefix' : this.mobile_prefix,
                     'merchant_uuid' : this.merchant_uuid,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){	
                          window.location.href = response.data.details.redirect;
                      } else {						 	 				 	 	
                          this.data = [];
                          this.error = response.data.msg;
                      }			 			 	 
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading =  false;
                 });			
                 
            },		    
        },
    });
    app_merchant_user.use(Maska);
    const vm_merchant_user = app_merchant_user.mount('#vue-merchant-user');
    
    
    
    /*
      COMPONENTS PAYMENT PLANS
    */
    const ComponentsPaymentPlans = {
        props: ['label','payment_type','ajax_url','actions'],
        data() {
            return {
              is_loading : false,		
              data : [],          
              payment_code : ''
           }
        },	
        mounted () {
             this.getPaymentMethod();  	  
        },
        methods :{
            getPaymentMethod(){
                
                this.is_loading =  true;
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/" + this.actions ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){	
                          this.data = response.data.details;
                      } else {						 	 				 	 	
                          this.data = [];
                      }			 			 	 
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading =  false;
                 });			
                
            },  
            isActive(data){
              return {
                  'border-vertical-green': data.payment_code==this.payment_code		      
              }
            },
            setPayment(payment_code){
                this.payment_code = payment_code;
                this.$emit('setPayment', this.payment_code );
            },
        },
        template: `
        <!--PAYMENT METHODS-->	
        <div class="card mb-3" >
            <div class="card-body">
            
            <h4 class="mb-3">{{label.select_payment}}</h4>
                    
            <a  v-for="payment in data" 
            @click="setPayment(payment.payment_code)"
            :class="isActive(payment)"
            class="d-block chevron-section medium d-flex align-items-center rounded mb-2">
                <div class="flexcol mr-2 payment-logo-wrap">
                  <i v-if="payment.logo_type=='icon'" :class="payment.logo_class"></i>
                   <img v-else class="img-35" :src="payment.logo_image" />  
                </div>
                
                <div class="flexcol" > 		     		     		      
                   <span class="mr-1">{{payment.payment_name}}</span>          
                </div> 		    		    		    
             </a> 
            
            </div> <!--card body-->
        </div> <!--card-->	
        <!--END PAYMENT METHODS-->
        `,
    };
    
    
    /*
      SUBSCRIPTION
    */
    const app_subscription = Vue.createApp({
       data(){
           return {
               data : [],
               plan_details : [],
               is_loading : false,
               merchant_uuid : '',
               package_id : '',
               package_uuid : '',
               payment_code : '',
               agree : true,
           };
       },
       computed: {
            canContinue(){	
                if(!empty(this.package_uuid) && !empty(this.payment_code)){
                    if(this.agree){			
                       return false;
                    }
                }
                return true;
            },
       },
       mounted () {  	    	
             this.merchant_uuid  = this.$refs.merchant.value;
             this.getPlan(); 
       },     
       methods :{   	
           isActive(data){
                 return {
                  'border-vertical-green': data.package_id==this.package_id		      
              }
           },
           getPlan(){
                      
                   var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   'merchant_uuid': this.merchant_uuid
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getPlan",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {  	    	
                         this.is_loading = true;	
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	
                 
                 ajax_request[timenow].done( data => {
                     if ( data.code==1){	 	
                         this.package_id = data.details.package_id;
                         this.data = data.details.data;
                         this.plan_details = data.details.plan_details;
                     } else {
                         this.package_id = '';
                         this.data = [];
                         this.plan_details = [];
                     } 	    
                });				
                
                ajax_request[timenow].always( data => {
                     this.is_loading = false;
                });			   
               
           },       
           setPlan(package_id,package_uuid){
                 this.package_id = package_id;
                 this.package_uuid = package_uuid;
           },   
           setPayment(payment_code){
                 this.payment_code = payment_code;
           },
           showPayment(){
                      try {		
                    this.$refs[this.payment_code].showPaymentForm();
                } catch(err) {  	 	  	       
                     notify(err,'error');
                   }
           },
       },
    });
    
    app_subscription.component("components-payment-plans",ComponentsPaymentPlans);
    
    if ((typeof  components_bundle !== "undefined") && ( components_bundle !== null)) {
        $.each(components_bundle,function(components_name, components_value) {		
            app_subscription.component(components_name, components_value );		
        });
    }		
    
    app_subscription.use(Maska);
    const vm_subscription = app_subscription.mount('#vue-subscription');
    
    
    /*
       UPDATE PROFILE
    */
    const app_update_profle = Vue.createApp({	
        components: {    	   
           'component-phone' : ComponentsPhoneNumber, 	   
           'component-change-phoneverify' : ComponentsChangePhoneVerification,
        },    
        data(){
           return {			
                   is_loading: false,	
                data : [],	
                first_name : '',
                last_name : '',
                email_address :'',
                mobile_prefix : '',
                mobile_number :'',
                original_email_address : '',
                original_mobile_number : '',
                error : [],
                success : ''
            };
        },
        computed: {
            DataValid(){				
                if(!empty(this.first_name) && empty(!this.last_name) && empty(!this.email_address) && empty(!this.mobile_number) ){
                    return true;									
                }			
                return false;
            },	
        },	 
        mounted () {
            this.getProfile();
        },  
        methods :{
            getProfile(){
                
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		    
                };			
                var timenow = 11;
                $params = JSON.stringify($params);		
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getProfile",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	 
                 
                 ajax_request[timenow].done( data => {
                     if(data.code==1){
                         this.first_name = data.details.first_name;
                         this.last_name = data.details.last_name;
                         this.email_address = data.details.email_address;
                         this.mobile_prefix = data.details.mobile_prefix;
                         this.mobile_number = data.details.mobile_number;
                         this.original_email_address = data.details.email_address;
                         this.original_mobile_number = data.details.mobile_number;
                     } else {
                         this.msg = data.msg;
                     }
                 });	// ajax done	
                
                 ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });	
            },
            
            checkForm(){
                var _change = false;		
                if( this.email_address != this.original_email_address ) {
                    _change = true;
                }
                if( this.mobile_number != this.original_mobile_number ) {
                    _change = true;
                }
                
                this.success ='';
                this.error = [];
                
                if(_change){			   		
                   this.$refs.cphoneverify.show({
                        mobile_prefix : this.mobile_prefix,
                        mobile_number : this.mobile_number,
                        message : _message
                   });			
                   this.$refs.cphoneverify.resendCode();			   
                } else {
                    this.saveProfile('');
                }			
            },
            
            saveProfile(code){
                
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  'code': code,
                  'first_name' : this.first_name,
                  'last_name' : this.last_name,
                  'email_address' : this.email_address,
                  'mobile_prefix' : this.mobile_prefix,
                  'mobile_number' : this.mobile_number,		      
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/saveProfile",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         if (!empty(code)){
                             this.$refs.cphoneverify.is_loading = true;
                         } else {
                             this.is_loading = true;	
                         }	 	    		
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	   
                             
                 ajax_request[timenow].done( data => {
                     if(data.code==1){
                         this.$refs.cphoneverify.close();	 	    	
                         this.success = data.msg;
                         this.original_email_address = this.email_address;
                         this.original_mobile_number = this.mobile_number;
                     } else {
                         if (!empty(code)){
                             this.$refs.cphoneverify.error = data.msg;
                         } else this.error = data.msg;
                     }
                 });
                              
                 ajax_request[timenow].always( data => {	 	    	
                     if (!empty(code)){
                         this.$refs.cphoneverify.is_loading = false;
                     } else {
                         this.is_loading = false;	
                     }	 	    		
                });		
            },
        },
    });
    
    app_update_profle.use(Maska);
    const vm_update_profle = app_update_profle.mount('#vue-update-profile');
    
    /* 
      UPDATE PASSWORD
    */
    const app_update_password = Vue.createApp({	
        components: {    	   	   
           'component-change-phoneverify' : ComponentsChangePhoneVerification,
        },    
        data(){
           return {			
                   is_loading: false,	
                data : [],		
                error : [],
                success : '',
                old_password : '',
                new_password : '',
                confirm_password : '',
            };
        },
        computed: {
            DataValid(){				
                if(!empty(this.old_password) && empty(!this.new_password) && empty(!this.confirm_password) ){
                    return true;									
                }			
                return false;
            },	
        },	 
        methods :{
            updatePassword(){
                
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		      
                  'old_password' : this.old_password,
                  'new_password' : this.new_password,
                  'confirm_password' : this.confirm_password,		      
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/updatePassword",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;	
                         this.error = ''; this.success = '';
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                 
                 ajax_request[timenow].done( data => {
                     if(data.code==1){
                         this.success = data.msg;
                         this.old_password ='';
                        this.new_password ='';
                        this.confirm_password ='';
                     } else this.error = data.msg;
                 });
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });		 
                
            },
        },
    }).mount('#vue-update-password');
        
    
    /*
      MANAGE MY ACCOUNT
    */
    
    const app_manage_account = Vue.createApp({	
        components: {    	   	   
           'component-change-phoneverify' : ComponentsChangePhoneVerification,	      
        },  
        data(){
           return {			
                   is_loading: false,			
                error : [],
                success : '',	
                steps : 1,
                steps_request_data : 1,
            };
        },
        methods :{
            requestArchive(){
                
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		      
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);		
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/requestData",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;
                         this.steps_request_data = 1;
                         this.error = '';
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                
                   ajax_request[timenow].done( data => {
                     if(data.code==1){	 	 
                        this.steps_request_data = 2;   			 	    		
                     } else {
                        this.error = data.msg;	
                     }
                 });
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });		
                
            },
            confirm(){
               this.$refs.cphoneverify.show({
                    mobile_prefix : _phone_prefix,
                    mobile_number : _contact_phone,
                    message : _message
               });			
               this.$refs.cphoneverify.resendCode();	
            },
            verifyAccountDelete(code){
                
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		      
                  'code' : code,		      
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/verifyAccountDelete",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.$refs.cphoneverify.is_loading = true;	
                         this.$refs.cphoneverify.error = [];
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                
                  ajax_request[timenow].done( data => {
                     if(data.code==1){	 	    		
                         this.$refs.cphoneverify.close();
                         
                         vm_bootbox.confirm().then((result) => {		  
                               if(result){
                                  this.deleteAccount( code );
                               }
                           });		   	
                         
                     } else this.$refs.cphoneverify.error = data.msg;
                 });
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.$refs.cphoneverify.is_loading = false;
                });		
                
            },
            
            deleteAccount(code){
                
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		      
                  'code' : code,		      
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/deleteAccount",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;	
                         this.error = ''; this.success = '';
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }); 	  
                
                 ajax_request[timenow].done( data => {
                     if(data.code==1){
                         this.steps = 2;
                         setTimeout(() => {	 	    			
                             window.location.href = data.details.redirect;
                         }, 4000); 
                     } else vm_bootbox.alert( data.msg , {} );
                 });
                 
                 ajax_request[timenow].always( data => {	 	    	
                     this.is_loading = false;
                });		
            },
        }
    });
    app_manage_account.use(Maska);
    const vm_manage_account = app_manage_account.mount('#vue-manage-account');    
    
    
    /*
      COMPONENTS PROFILE PHOTO
    */
    const ComponentsProfilePhoto = {	
     components: {
          Cropper: VueAdvancedCropper.Cropper
      },
      props: ['label'],
      data(){
          return {
              upload_images : '',
              filename : '',
              required_message :[],  		
              is_loading : false,
              error : [],
              steps: 1,
              photo : undefined,
          };
      },
      mounted () {
           this.initDropZone(this.label , this); 
      },
      methods :{
           show(){   
               this.steps=1;  	 	
               $('#photoModal').modal('show');
           },
           close(){     	 	
               $('#photoModal').modal('hide');   	    
           },  	 
           closeReset(){   
               this.reset();
               $('#photoModal').modal('hide');   	    
           },  	 
           reset(){ 		
             Dropzone.forElement('#dropzone_multiple').removeAllFiles(true);
             this.removeUpload(this.filename);
             this.upload_images = [];
             this.filename = ''; 		
             this.error = []; 	 		
         },
           initDropZone(label, object){    	
            var $upload_params = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),    		
            };    
            Dropzone.autoDiscover = false;    	
            //$dropzone = $("#dropzone_multiple").dropzone({ 
            $dropzone = $( this.$refs.profile_dropzone ).dropzone({ 
                dictDefaultMessage  : label.dictDefaultMessage,
                dictFallbackMessage  : label.dictFallbackMessage ,
                dictFallbackText  : label.dictFallbackText,
                dictFileTooBig  : label.dictFileTooBig,
                dictInvalidFileType : label.dictInvalidFileType,
                dictResponseError : label.dictResponseError,
                dictCancelUpload  : label.dictCancelUpload,
                dictCancelUploadConfirmation  : label.dictCancelUploadConfirmation,
                dictRemoveFile : label.dictRemoveFile,
                dictMaxFilesExceeded : label.dictMaxFilesExceeded,
                paramName: "file", 		
                url: ajaxurl+"/uploadProfilePhoto" ,
                maxFiles: 20,
                params: $upload_params,
                addRemoveLinks : true,		
                maxFiles : 2,	
                init: function() {			
                    this.on("maxfilesexceeded", function(file) {					
                        vm_bootbox.alert( label.max_file_exceeded , {} );										
                    });
                    this.on("removedfile", function (file) {					
                    });
                },
                success: (file,response) => {
                    var $resp = JSON.parse(response);			    
                    if($resp.code==1){
                        this.upload_images = $resp.details.url_image;
                        this.filename = $resp.details.filename;
                        this.steps = 2;
                    } else {
                        vm_bootbox.alert( $resp.msg , {} );
                        this.reset();
                    }
                }
            });	    	    
          }, 	
          removeUpload(id){    	
            
            return new Promise((resolve, reject) => {	      	      
                     
                      var $params = {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),					   
                       'id':id,
                    };							
                    var timenow = getTimeNow();
                    
                    ajax_request[timenow] = $.ajax({
                         url: ajaxurl+"/removeProfilePhoto",
                         method: "PUT",
                         dataType: "json",
                         data: JSON.stringify($params) ,
                         contentType : $content_type.json ,
                         timeout: $timeout,
                         crossDomain: true,
                         beforeSend: data => {
                             this.is_loading = true;
                              if(ajax_request[timenow] != null) {	
                                  ajax_request[timenow].abort();
                                  this.is_loading = false;
                              }
                         }
                     });
                     
                     ajax_request[timenow].done( data => {
                         this.is_loading = false;		 	    	
                          resolve(true);		 	     	
                          if(data.code==1){
                             //this.remove(data.details);
                          }
                     });	// ajax done 
                     
                    ajax_request[timenow].always( data => {	 	    	  
                       this.is_loading = false;
                   });				
                     
              });     	
          },
          change({coordinates, canvas}) {       
           this.photo = canvas.toDataURL('image/png');       
         },
         save(){
             this.$emit('onSave', this.photo , this.filename );
         },
      },
      template: `	
        <div class="modal" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModal" aria-hidden="true" 
         data-backdrop="static" data-keyboard="false"  >
           <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">  
           
           <div class="modal-body" style="overflow-y:inherit !important;" > 
           
              <a href="javascript:;" @click="closeReset" 
              class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>        
           
             <template :class="{ 'd-block': steps==1 }">  
             <h5 class="m-0 mt-3 mb-2">{{label.add_photo}}</h5>      
             <div class="mb-3">
                 <div class="dropzone dropzone_container" id="dropzone_multiple" ref="profile_dropzone" >
                   <div class="dz-default dz-message">
                    <i class="fas fa-cloud-upload-alt"></i>
                     <p>{{label.drop_files_here}}</p>
                    </div>
                </div> 
             </div>	    
            </template>
            
            <template v-if="steps==2">
              <h5 class="m-0 mt-3 mb-2">{{label.crop_photo}}</h5>  
              
              <div class="crop-images-wrap">
                <cropper 
                :src="upload_images"		    
                @change="change"
                :stencil-props="{
                    handlers: {},
                    movable: false,
                    scalable: false,
                }"
                :stencil-size="{
                    width: 120,
                    height: 120
                }"
                image-restriction="stencil"
              ></cropper>
              </div>
              
            </template>
                  
           
           </div> <!--modal body-->
           
           <template v-if="steps==2">
           
           <div v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
           </div> 
           
            <div class="modal-footer justify-content-start">
                <button class="btn btn-green w-100" @click="save" :class="{ loading: is_loading }" >               
                   <span class="label">{{label.save_photo}}</span>
                   <div class="m-auto circle-loader" data-loader="circle-side"></div>
                </button>
            </div> <!--footer-->
            </template>
           
           </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->     
      `,	
    };
    
    
    /*
      CUSTOMER PROFILE PHOTO
    */
    const app_profile_photo = Vue.createApp({	
        components: {    	   	   
           'components-profile-photo' : ComponentsProfilePhoto,	      
        },  
        data(){
            return {
                  avatar : ''
              };
        },
        mounted () {
            dump("mounted");
        },
        methods :{
            showUpload(){
                this.$refs.profile_photo.show();
            },
            SavePhoto(photo,filename){    		
                
                var $params; var timenow = 1;
                $params="photo=" + photo;
                $params+="&filename=" + filename;
                $params+=addValidationRequest();
                 ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/saveProfilePhoto",
                     method: "POST",
                     dataType: "json",
                     data: $params,
                     contentType : $content_type.form ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => {	 	
                         this.$refs.profile_photo.is_loading = true;    		
                          if(ajax_request[timenow] != null) {	
                             ajax_request[timenow].abort();
                          }
                     }
                 });
                 
                ajax_request[timenow].done( data => {
                     if(data.code==1){	 	    			 	    
                         this.$refs.profile_photo.close();		 	    		
                         this.avatar = data.details.url_image;
                         this.updateImage();
                     } else {
                         this.$refs.profile_photo.error = data.msg;
                     }
                 });	// ajax done 
                 
                ajax_request[timenow].always( data => {	 	    	  
                    this.$refs.profile_photo.is_loading = false;
               });		
            
            },
            updateImage(){    	   
               var el = this.$refs.refavatar.getElementsByTagName("img");		   
               el[0].src  = this.avatar;
            },    
        },
    });
    app_profile_photo.use(ElementPlus);
    const vm_profile_photo = app_profile_photo.mount('#vue-profile-photo');
    
    
    /*
      CHECK MERCHANT OPEN HOURS  
      vue-schedule-order
    */
    const vm_schedule_order = Vue.createApp({	
        components: {    	   	   
           'component-select-time' : ComponenstTransactionOptions,	      
        },  
        data(){
            return {	  		
                  store_close : false,
              };
        },
        mounted () {
             //this.checkStoreOpen();
             //this.storeAvailable();
        },
        methods:{			
            checkStoreOpen(){	
                var $merchant_id = 0;
                if ((typeof merchant_id !== "undefined") && ( merchant_id !== null)) {
                    $merchant_id = merchant_id;
                }
                var $choosen_delivery = !empty(getCookie('choosen_delivery'))? JSON.parse(getCookie('choosen_delivery')) : [] ;
                
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  'merchant_id' : $merchant_id,
                  'cart_uuid' : getCookie('cart_uuid'),
                  'choosen_delivery' : $choosen_delivery,
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/checkStoreOpen",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   			 	    		    	
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }).done( data => {
                     if(data.code==1){	 	    			 	    	 	    	
                         if(data.details.merchant_open_status<=0){
                             this.store_close = true;
                             vue_cart.error.push(data.msg);
                         } else this.store_close = false; 
                     } else {	 	    		
                         this.store_close = true;	 	    		
                         vue_cart.error.push(data.msg);
                     }
                 }).always( data => {	 	    	  
                    
               });		        	
            },
            show(){
                this.$refs.select_time.show();
            },
            afterSaveTransOptions(data){			
                this.$refs.select_time.close();
                            
                if ((typeof vm_feed !== "undefined") && ( vm_feed !== null)) {
                    vm_feed.whento_deliver = data.whento_deliver;
                    vm_feed.delivery_date = data.delivery_date;
                    vm_feed.delivery_time = data.delivery_time;
                    vm_feed.Search(false);
                }
                
                if ((typeof vue_cart !== "undefined") && ( vue_cart !== null)) {
                    vue_cart.loadcart();
                }
                
                if ((typeof vm_widget_nav !== "undefined") && ( vm_widget_nav !== null)) {
                    vm_widget_nav.$refs.transaction_info.whento_deliver = data.whento_deliver;
                    vm_widget_nav.$refs.transaction_info.delivery_datetime = data.delivery_datetime;
                }
                
            },
            storeAvailable(){
                
                var $merchant_uuid = '';
                if ((typeof merchant_uuid !== "undefined") && ( merchant_uuid !== null)) {
                    $merchant_uuid = merchant_uuid;
                }
                
                axios({
                   method: 'POST',
                   url: ajaxurl +"/storeAvailable" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&merchant_uuid=" + $merchant_uuid,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 				 	 				 	 				 	 	
                          //
                      } else {							 	 	
                         vue_cart.error.push(response.data.msg); 	 	
                      }			 			 	 
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
            },
        },
    }).mount('#vue-schedule-order');
    
    
    /*
      UTENSILS
      vue-utensils
    */
    const vm_utensils = Vue.createApp({		
        data(){
            return {	  		
                visible : false,
                  include_utensils : false,
                  is_loading : false,
              };
        },
        watch: {
            include_utensils(new_value,old_value){			
                setStorage('include_utensils',new_value);
                this.setUtensils(new_value);
            }
        },
        mounted () {
             this.setUtensils( getStorage("include_utensils") );
        },    
        methods : {   
            setTransaction(trans_type){
                if(trans_type=="delivery"){
                    this.visible = true;
                } else this.visible = false;
            },
            setUtensils(is_include){
                is_include = empty(is_include)? false : is_include;        	
                this.include_utensils = is_include;
            },
        }
    }).mount('#vue-utensils');    
    
    
    /*
      COMPONENTS ITEM SUGGESTION
    */
    const ComponentsItemSuggestion = {
        props: ['title','merchant_id','settings','image_use' , 'responsive'],
        data(){
            return {				
                owl : undefined,
                   menu_data: [],
                   items : [],   	
                   item_addons : [],
                   item_addons_load : false,
                   size_id : 0,
                   disabled_cart : true,
                   item_qty : 1,
                   item_total : 0,
                   add_to_cart : false,   
                   meta : [],   			
                   special_instructions : '',
                   sold_out_options : [],
                   if_sold_out : 'substitute',
                   transaction_type : '',	     
            };	
        },		
        mounted () {
            this.itemSuggestion();
        },
        updated () {
                    
            if(this.item_addons_load==true){
                this.ItemSummary();      	    
                $('.item_s .lazy').each(function(index, $obj){			 	    		
                    if ( !empty($(this).attr("src")) && !empty($(this).attr("data-src")) ){
                        $(this).attr("src", $(this).attr("data-src") );
                    }		
                });  	    
            }
            
            if ((typeof  vm_checkout_transaction !== "undefined") && ( vm_checkout_transaction !== null)) {
                this.transaction_type = vm_checkout_transaction.transaction_type;
            }
                    
            
        }, //    
        methods: {
            itemSuggestion(){
                
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  'merchant_id' : this.merchant_id,
                  'image_use' : this.image_use
                };			
                var timenow = getTimeNow();
                $params = JSON.stringify($params);					     	
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/geStoreMenu",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	   	
                         this.is_loading = true;	 	    
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }).done( data => {
                     if ( data.code==1){	 
                         
                         var $category = data.details.data.category; 
                        var $items = data.details.data.items;
                        var $new_category = []; var $new_items = [];
                        
                        if($category.length>0){
                            $.each($category, function(key, val){								
                                $.each(val.items, function(items_key, $item_id){
                                    $new_items.push( $items[$item_id] );
                                }); // loop							
                                $new_category.push({
                                    'cat_id': val.cat_id,
                                    'category_name': val.category_name,
                                    'category_uiid': val.category_uiid,							
                                    'items':$new_items
                                });						
                                $new_items = [];
                            }); // loop
                        }
                         this.menu_data = $new_category;  
                         
                         setTimeout(() => {						
                           this.RenderCarousel();
                        }, 10);
                         
                     } else this.menu_data = [];  
                }).always( data => {
                     this.is_loading = false;
                });	  			
            },
            RenderCarousel(){   
                dump("RenderCarousel");
                this.owl = $("#item-suggestion-list").owlCarousel({		
                    items: parseInt(this.settings.items),
                    lazyLoad: this.settings.lazyLoad=="1"?true:false ,
                    loop: this.settings.loop=="1"?true:false ,
                    margin: parseInt(this.settings.margin) ,
                    nav: this.settings.nav=="1"?true:false ,
                    dots: this.settings.dots=="1"?true:false ,
                    stagePadding: parseInt(this.settings.stagePadding),	
                    autoWidth:true,	
                });
            },
            SlideNext(){    		    		
                this.owl.trigger('next.owl.carousel');
            },
            SlidePrev(){    		
                this.owl.trigger('prev.owl.carousel', [300]);
            },
            viewItem(data){
                        
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  'merchant_id': this.merchant_id,
                  'item_uuid' : data.item_uuid,
                  'cat_id' : data.cat_id
                };			
                var timenow = 1;
                $params = JSON.stringify($params);				
                 
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/getMenuItem",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: data => { 	  	 	    		   
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 }).done( data => { 
                     
                     if(data.code==1){
                         
                        $global_items = data.details.data.items;
                        $global_addons = data.details.data.addons;
                        $global_addon_items = data.details.data.addon_items;
                        $global_item_addons = data.details.data.items.item_addons;	
                        
                        var $meta = data.details.data.meta;
                        var $meta_details = data.details.data.meta_details;
                        var $new_meta = {
                             'cooking_ref' : [],
                             'ingredients' : [],
                             'dish' : [],
                         };	 
                         
                         if( !empty($meta)){
                             $.each($meta,function(key, item) {
                                 $.each(item,function(key2, item2) {	 	    					 	    		
                                     if(!empty($meta_details[key])){
                                     if(!empty($meta_details[key][item2])){
                                         $new_meta[key].push({
                                           'meta_id': $meta_details[key][item2].meta_id,
                                           'meta_name': $meta_details[key][item2].meta_name,
                                           'checked': false
                                         });	 	    			
                                     }
                                     }
                                 });
                             });
                         }	 	    	
                         
                         var $price = $global_items.price;	 	    	
                         var $size_id = Object.keys($price)[0];	
                         
                         this.item_qty = 1;
                         this.items = $global_items;
                         this.size_id = $size_id;	 	   
                         this.meta =  $new_meta;
                         this.getSizeData($size_id);	 
                         this.sold_out_options = data.details.sold_out_options; 
                                                            
                         $( this.$refs.modal_item_details ).modal('show');
                         this.updateImage();
                               
                     } /*end if*/
                     
                 });		
            },
            updateImage(){
               updateLazyImage();
            },    
            setItemSize(event){    	 	        	
                 var $size_id = event.currentTarget.firstElementChild.value;     	 	
                 this.size_id = $size_id;
                 this.getSizeData($size_id);  
            }, // end setItemSize 
            getSizeData($size_id){
                
                $new_item_addons = []; var $sub_items=[];
                             
                 if( !empty($global_item_addons[$size_id]) ){
                     $.each($global_item_addons[$size_id],function(key, item) { 
                         if(!empty($global_addons[$size_id])){
                             if(!empty($global_addons[$size_id][item])){
                                 
                                 $global_addons[$size_id][item].subcat_id
                                 $.each($global_addons[$size_id][item].sub_items,function(key2, item2) {
                                     $sub_items.push({
                                       'sub_item_id':$global_addon_items[item2].sub_item_id,
                                       'sub_item_name':$global_addon_items[item2].sub_item_name,
                                       'item_description':$global_addon_items[item2].item_description,
                                       'price':$global_addon_items[item2].price,
                                       'pretty_price':$global_addon_items[item2].pretty_price,
                                       'checked':false,
                                       'disabled':false,
                                       'qty':1
                                    }); 	   
                                 }); 	    						
                                                              
                                 $new_item_addons.push({
                                     'subcat_id' : $global_addons[$size_id][item].subcat_id,
                                     'subcategory_name' : $global_addons[$size_id][item].subcategory_name,
                                     'subcategory_description' : $global_addons[$size_id][item].subcategory_description,
                                     'multi_option' : $global_addons[$size_id][item].multi_option,
                                     'multi_option_value' : $global_addons[$size_id][item].multi_option_value,
                                     'require_addon' : $global_addons[$size_id][item].require_addon,
                                     'pre_selected' : $global_addons[$size_id][item].pre_selected,
                                     'sub_items_checked':'',
                                     'sub_items':$sub_items
                                 });	 	
                                 $sub_items = [] ;
                             }
                         } 	    		
                     });
                 }
                 
                 this.item_addons = $new_item_addons;	
                 this.item_addons_load = true; 	    	
            }, //  end getSizeData   
            
            ItemSummary(data){    	      	        	
                
                 $item_total = 0;
                 var $required_addon = [];	
                 var $required_addon_added = [];
                                           
                 if(!empty(this.items.price)){
                     if(!empty(this.items.price[this.size_id])){
                         var item = this.items.price[this.size_id];
                         if( item.discount>0){
                             $item_total+=( this.item_qty * parseFloat(item.price_after_discount) );
                         } else $item_total+=( this.item_qty * parseFloat(item.price) );
                     }    		 
                 }    	
                 
                 dump($item_total);
                 
                 this.item_addons.forEach((item,index) => {		
                            
                   //dump("=>" + item.multi_option);
                   if ( item.require_addon==1){
                          $required_addon.push(item.subcat_id);
                   }
                                
                   if(item.multi_option=="custom"){
                         var total_check = 0;    	  	 
                         var multi_option_value = item.multi_option_value;  
                         var item_index=[]; var item_index2=[];   	    	  	 	  
                         item.sub_items.forEach((item2,index2) => {		       	  	 
                              if(item2.checked==true){
                               total_check++;    	     	  	 			
                               $item_total+=( this.item_qty * parseFloat(item2.price) );
                               $required_addon_added.push( item.subcat_id );
                            } else item_index.push(index2);
                            
                            if(item2.disabled==true){
                               item_index2.push(index2);
                            }
                            
                         })		       	  
                         if(total_check>=multi_option_value){		       	  	 
                              item_index.forEach((item3,index3) => {			       	  	 
                                  this.item_addons[index].sub_items[item3].disabled = true;
                              });
                         } else {		       	  	 
                              item_index2.forEach((item3,index3) => {			       	  	 
                                  this.item_addons[index].sub_items[item3].disabled = false;
                              });
                         }		
                         
                   } else if ( item.multi_option=="one" ){				       	   		       	   
                          item.sub_items.forEach((item2,index2) => {
                                if( item2.sub_item_id == item.sub_items_checked ) {		       	   	     
                                   $item_total+=( this.item_qty * parseFloat(item2.price) );
                                   $required_addon_added.push( item.subcat_id );
                                }		       	   
                          });
                   } else if ( item.multi_option=="multiple" ){	   
                         item.sub_items.forEach((item2,index2) => {
                              if(item2.checked==true){
                                  $item_total+=( item2.qty * parseFloat(item2.price) );
                                  $required_addon_added.push( item.subcat_id );
                              }
                         });
                   } /*endif custom*/
                   
                });/* end loop*/
                            
                
                this.item_total = $item_total;		    
                
                var $required_meet=true;
                if($required_addon.length>0){
                    $.each($required_addon, function(i, val){
                        if($required_addon_added.includes(val)===false){
                           $required_meet = false;
                           return false;
                        }
                    });
                }
                            
                if($required_meet){
                    this.disabled_cart = false;
                } else this.disabled_cart = true;
                                                        
            }, // end ItemSummary      	
    
            CheckaddCartItems(){    	    		    	
                this.addCartItems();    		
            },   
            
            addCartItems(event){
                if (event) {
                    event.preventDefault()
                }
                                        
                this.add_to_cart = true;
                                                           
                var $params = {
                   'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			 	
                   'merchant_id' : this.items.merchant_id,
                   'cat_id' : this.items.cat_id,
                   'item_token': this.items.item_token,
                   'item_size_id': this.size_id,
                   'item_qty': this.item_qty,
                   'item_addons': this.item_addons,
                   'special_instructions': this.special_instructions,
                   'meta' : this.meta,
                   'cart_uuid':getCookie('cart_uuid'),
                   'transaction_type': this.transaction_type ,
                   'if_sold_out' : this.if_sold_out,
                };		
                                        
                var timenow = 1;
                $params = JSON.stringify($params);
                
                ajax_request[timenow] = $.ajax({
                     url: ajaxurl+"/addCartItems",
                     method: "PUT",
                     dataType: "json",
                     data: $params ,
                     contentType : $content_type.json ,
                     timeout: $timeout,
                     crossDomain: true,
                     beforeSend: function( xhr ) {
                          if(ajax_request[timenow] != null) {	
                              ajax_request[timenow].abort();
                          }
                     }
                 });
                
                 ajax_request[timenow].done( data => {	 	    		 	    
                     if ( data.code==1){	
                        this.special_instructions = '';	 	    		
                         $( this.$refs.modal_item_details ).modal('hide');
                         vue_cart.loadcart();
                     }	 	    
                });				
                
                ajax_request[timenow].always( data => {	 	    	
                     this.add_to_cart = false;
                });				
                
            }  // end addCartItems
            //
            
        },
        template: '#item_suggestion',
    };
    
    /*
      ITEM SUGGESTION
      vue-item-suggestion
    */
    const vm_item_suggestion = Vue.createApp({		
        components: {    	   	   
           'components-item-suggestion' : ComponentsItemSuggestion   
        }, 
        data(){
           return {	  		
             data : []
          };
        },
    }).mount('#vue-item-suggestion');	
    
    
    
    /*
      CHANGE ADDRESS POPUP
      this is function for selecting checkout address
      target = 
    */
    const vm_addres_needed = Vue.createApp({
      components: {
        'component-change-address': changeAddressComponent,       
      },
      data(){
        return {						
            addresses : [],
            visible : false,
        };
      },
      mounted() {
         this.hasAddress();
      },
      methods: {
           hasAddress(){  	 	 	 	
               var $local_id = getCookie('kmrs_local_id');
               if(empty($local_id)){
                   this.visible = true;
               }
           },
           show(){
               this.$refs.address.showModal();
           },
           afterChangeAddress(data){  	 	
               this.visible = false;  	  	  	 	 	 
               this.$refs.address.closeModal();
               if ((typeof vm_widget_nav !== "undefined") && ( vm_widget_nav !== null)) {
                   vm_widget_nav.afterChangeAddress();
               }
               
               if ((typeof vm_merchant_details !== "undefined") && ( vm_merchant_details !== null)) {
               vm_merchant_details.$refs.ref_services.getMerchantServices();
            }
           },
      },
    }).mount('#vue-address-needed');	  
    
    
    
    /*
      COMPONENTS ComponentsWebpushSettings
    */
    const ComponentsWebpushSettings = {
        template: '#xtemplate_webpushsettings',
        props: ['ajax_url','settings','iterest_list', 'message'],
        data(){
            return {
                beams: undefined,	 
                is_loading : false,     
                is_submitted : false,
                webpush_enabled : '',
                interest : [],
                device_id : ''
              };
        },
        mounted() {
            this.initBeam();		
        },
        methods :{
            initBeam(){						
                this.beams = new PusherPushNotifications.Client({
                  instanceId: this.settings.instance_id,
                });		
    
                this.is_loading =  true;
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/getwebnotifications" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 				 	 				 	 	
                          this.webpush_enabled = response.data.details.enabled==1?true:false;			 	 	
                          this.device_id = response.data.details.device_token;
                          this.interest = response.data.details.interest;
                      } else {						 	 	
                          this.webpush_enabled = false;
                          this.device_id = '';
                          this.interest = [];
                      }			 			 	 
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading =  false;
                 });			
                        
            },		
            enabledWebPush(){	
                this.is_loading = true;
                if( this.webpush_enabled){
                    this.beams.start().then(() => {				    				    
                        dump("start");
                        this.beams.getDeviceId().then( (device_id)=>{
                            this.device_id = device_id;						
                        });
                    }).catch(e => {
                        notify( this.message.notification_start  ,'error');
                    }).then(data => {			     				     
                         this.is_loading = false;
                    });	
                } else {
                    this.beams.stop().then(() => {				    				    
                        dump("stop");
                        this.device_id = '';
                    }).catch(e => {
                        notify( this.message.notification_stop  ,'error');
                    }).then(data => {			     				     
                         this.is_loading = false;				     
                    });	
                }		
            },
            saveWebNotifications(){    
                this.is_submitted = true;
                axios({
                   method: 'PUT',
                   url: this.ajax_url+"/savewebnotifications" ,
                   data : {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                     'webpush_enabled' : this.webpush_enabled,
                     'interest' : this.interest,
                     'device_id' : this.device_id,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 				 	 	
                          notify( response.data.msg);
                      } else {						 	 	
                          notify( response.data.msg ,'error');
                      }			 	 			 	 
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_submitted = false;
                 });			
                  
            },		
        },
    };
    
    
    
    /*
      PUSH SETTINGS
    */
    const app_webpushsettings = Vue.createApp({
        components: {
           'components-web-pusher': ComponentsWebpushSettings, 	  	   
        },			
    });
    const vm_webpushsettings = app_webpushsettings.mount('#vue-webpush-settings');	
    
    
    /*
      ALL NOTIFICATION LIST
    */
    const app_notification_list = Vue.createApp({
        data(){
            return {	  	  
                is_loading : false, 
                data : []	,	 
                page: 0, 	  
                show_next_page : false,	  	  
              };
        },
        mounted() {
             this.getData(this.page);	 	
        },
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
            isFirstLoad(){	
                if(this.is_loading && this.page<=0){
                    return true;
                }
                return false;
            },
        },
        methods: {
            getData(page){		
                this.is_loading = true;
                axios({
                   method: 'POST',
                   url: ajaxurl +"/notificationList" ,
                   data : 'YII_CSRF_TOKEN=' +  $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&page=" + page ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 				 	 	
                          //this.data = response.data.details.data;
                          this.data.push(response.data.details.data);
                          this.show_next_page = response.data.details.show_next_page;
                          this.page = response.data.details.page;
                      } else {						 	 	
                          this.data = [];
                      }			 	 			 	 
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            }
        },
    });
    app_notification_list.use(ElementPlus);
    const vm_notification_list = app_notification_list.mount('#vue-notifications-list');	
    
    
    /*
      COMPONENTS PAYMENT LIST
    */
    const ComponentsPaymentList = {
        props: ['ajax_url' , 'label' ,'payment_type' ],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        mounted() {	   	     	     	     	  
               this.getPaymentList();
        },
        methods: {
             getPaymentList(){     	
                 this.is_loading = true;	
                 axios({
                   method: 'POST',
                   url: this.ajax_url + "/getPaymentList" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&payment_type=" + this.payment_type,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details.data;
                      } else {				 	 	
                          this.data = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			        	
             },     	     	
        },
        template: `	    
        <DIV class="position-relative">
         <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
        
        <h5 class="mb-3 mt-4">Add New Payment Method</h5>
        <a v-for="items in data" @click="$emit('afterClickpayment',items.payment_code)" 
        class="d-block chevron-section medium d-flex align-items-center rounded mb-2">
          <div class="flexcol mr-2 payment-logo-wrap">
            <i v-if="items.logo_type=='icon'" :class="items.logo_class"></i>
            <img v-else class="img-35" :src="items.logo_image" /> 		      
          </div>
          <div class="flexcol">
            <span class="mr-1">{{items.payment_name}}</span>
          </div>
        </a>
        </DIV>
        `
    };
    
    
    /* 
      COMPONENTS SAVED PAYMENT LIST
    */
    const ComponentsMerchantSavedPayment = {
        props: ['ajax_url' , 'merchant_uuid' ],
        data(){
           return {						
             data : [],		 
             default_payment_uuid : '',
             is_loading : false,
             message : ''
           };
        },   
        computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
        },
        mounted() {	   	     	     	     	  
               this.MerchantSavedPayment();
        },
        methods: {
             MerchantSavedPayment(){
                 this.is_loading = true;     	
                 axios({
                   method: 'POST',
                   url: this.ajax_url + "/MerchantSavedPayment" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&merchant_uuid=" + this.merchant_uuid ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details.data;
                          this.default_payment_uuid = response.data.details.default_payment_uuid;	
                          this.$emit('setDefaultpayment',response.data.details.default_payment);
                      } else {				 	 	
                          this.data = [];
                          this.message = response.data.msg;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		
             },
             setDefaultPayment(data){     		
                 axios({
                   method: 'POST',
                   url: this.ajax_url + "/MerchantsetDefaultPayment" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&payment_uuid=" + data.payment_uuid ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.MerchantSavedPayment();
                      } else {				 	 	
                          notify( response.data.msg ,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });		
             },
             deleteSavedPaymentMethod(data){
                 axios({
                   method: 'POST',
                   url: this.ajax_url + "/MerchantDeleteSavedPaymentMethod" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&payment_uuid=" + data.payment_uuid ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.MerchantSavedPayment();
                      } else {				 	 	
                          notify( response.data.msg ,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });	
             },
        },
        template: `	    
        <DIV class="position-relative">
        
         <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
        
             <template v-if="hasData">
             <h5 class="mb-3">Saved Payment Methods</h5>
                  
             <div v-for="saved_payment in data" class="row no-gutters align-items-center chevron-section medium rounded mb-2"  :class="{ selected: saved_payment.as_default==1 }" >
                     
              <div class="col-md-8 d-flex align-items-center">
                <div class="flexcol mr-2 payment-logo-wrap">
                      <i v-if="saved_payment.logo_type=='icon'" :class="saved_payment.logo_class"></i>
                      <img v-else class="img-35" :src="saved_payment.logo_image" /> 		      
                    </div> <!--flex-col-->
                    <div class="flexcol" > 		     		     		      
                       <span class=" mr-1">{{saved_payment.attr1}}</span>       
                       <p class="m-0 text-muted">{{saved_payment.attr2}}</p>   
                    </div> 		    		    		    
              </div> <!--col-->
              <div class="col-md-4 d-flex align-items-center justify-content-end">
                         
                 <template v-if="saved_payment.as_default==1">
                 <div class="mr-1"><i class="zmdi zmdi-check text-success"></i></div>
                 <div class="mr-3"><p class="m-0">Default</p></div>
                 </template>
                 
                 <div class="dropdown">
                 <a href="javascript:;" class="rounded-pill rounded-button-icon d-inline-block" 
                 id="dropdownMenuLink" data-toggle="dropdown" >
                   <i class="zmdi zmdi-more" style="font-size: inherit;"></i>
                 </a>
                     <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a  v-if="saved_payment.as_default!=1" 
                 @click="setDefaultPayment(saved_payment)"
                 class="dropdown-item a-12" href="javascript:;">Set Default</a>			    
                        <a @click="deleteSavedPaymentMethod(saved_payment)" class="dropdown-item a-12" href="javascript:;">
                        Delete
                        </a>				    
                      </div>
                 </div> <!--dropdown-->
                  
              </div> <!--col-->
             </div> <!--row-->	 	 
             </template>	 
         </DIV>
        `
    };
    
    
    /*
      CASH IN PAYMENT
    */
    const ComponentsCashinPayment = {
        props: ['ajax_url' ,'amount' , 'label' ,'default_payment' , 'merchant_uuid' ],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        computed: {
            ValidAmount(){						
                if(this.amount<=0){
                    return true;
                }			
                if(empty(this.default_payment.payment_uuid)){
                    return true;
                }
                return false;
            },
        },
        mounted() {	   	     	     	     	  
               dump(this.amount);
        },
        methods: {
             DoCashin(){     	
                 
                 bootbox.confirm({ 
                    size: "small",
                    title : this.label.confirm ,
                    message: this.label.are_you_sure,
                    centerVertical: true,
                    animate: false,
                    buttons: {
                        cancel: {
                           label: this.label.cancel,
                           className: 'btn btn-black small pl-4 pr-4'
                        },
                        confirm: {
                            label: this.label.yes,
                            className: 'btn btn-green small pl-4 pr-4'
                        },
                    },
                    callback: result => {				    	
                        if(result){
                               var data = {
                                 'payment_uuid' : this.default_payment.payment_uuid,
                                 'payment_code' : this.default_payment.payment_code,
                                 'merchant_uuid' : this.merchant_uuid,
                                 'amount' : this.amount,
                             };     
                             try {			   
                                  vm_cashin.$refs[ this.default_payment.payment_code ].DoPaymentRender( data ,'PaymentIntentCashin' );
                               } catch(err) {  		  	 	   
                                 notify(err.message,'error');
                               }
                        }
                    }
                });	     		     		
             },
        },
        template: `	        
         <button class="btn btn-green w-100 pointer mt-3"    
         :disabled="ValidAmount"
         @click="DoCashin"
         >
           <span class="label">{{label.continue}}</span>
           <div class="m-auto circle-loader" data-loader="circle-side"></div>
        </button>                     
        `
    };
    
    
    /*
      CASHIN
    */
    const app_cashin = Vue.createApp({
        data(){
            return {	  	  
                data : [],	  	
                default_payment : ''  
              };
        },
        mounted() {
             dump(this.amount_cashin);
        },	
        methods: {
            afterClickpayment(payment_code){
                try {			   
                      this.$refs[payment_code].showPaymentForm();
                   } catch(err) {  		  	 	   
                     notify(err.message,'error');
                   }
            },
            SavedPaymentList(){
                this.$refs.merchant_saved_payment.MerchantSavedPayment();
            },
            setDefaultpayment(data){			
                this.default_payment = data;
            },
            showLoadingBox(message){
                   if(!empty(message)){  	
                      vm_loading.$refs.box.setMessage(message);
                   }
                   vm_loading.$refs.box.show();
               },
              closeLoadingBox(){
                   vm_loading.$refs.box.close();
              },
              Alert(message){
                  notify(message,'error');
              },
        },
    });
    app_cashin.component('components-payment-list', ComponentsPaymentList );
    app_cashin.component('components-merchant-saved-payment', ComponentsMerchantSavedPayment );
    app_cashin.component('components-cashin-payment', ComponentsCashinPayment );
    if ((typeof  components_bundle !== "undefined") && ( components_bundle !== null)) {		
        $.each(components_bundle,function(components_name, components_value) {		
            app_cashin.component(components_name, components_value );
        });
    }
    const vm_cashin  = app_cashin.mount('#vue-cashin');
    
    
    /*
       CAROUSEL RESTAURANT
    */
    const vm_carousel = Vue.createApp({
      components: {    
         'component-carousel' : ComponentsCarousel,    	   
      },
    }).mount('#vue-carousel');
    
    
    const ComponentsProfileMenu = {
       props: ['data','index_selected'],
       data(){
            return {
                owl : undefined,
            };
       },
       mounted() {
            setTimeout(() => {						
                this.renderCarousel();
            }, 1); 		 	   
        },
        methods: {
            renderCarousel(){
                this.owl = $(this.$refs.menu).owlCarousel({
                    loop:false,		
                    dots:false,		
                    nav:false,		
                    autoWidth:true,					   	
                });
            },
        },
       template : `
       <div ref="menu"  class="owl-carousel owl-theme menu-carousel">
          <div v-for="(item, index) in data" class="mr-2">	   
           <a :href="item.url" class="d-block"
           :class="{active : index==index_selected }"
           >
            {{item.label}}
           </a>
          </div>    
       </div>
       `
    };
    const vm_profile_menu = Vue.createApp({
        components: {    
           'component-profile-menu' : ComponentsProfileMenu,    	   
        },
      }).mount('#vue-profile-menu');
    
    })(jQuery); 
    /*end strict*/