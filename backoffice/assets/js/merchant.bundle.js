(function($) {
    "use strict";
    
    /* ===========================  UTILITY STARTS HERE  ===========================  */
    var data_tables;
    var $is_mobile;
    var data_tables_row;
    var data_tables_tr;
    
    const $timeout = 20000;
    const $content_type = {
        'form':'application/x-www-form-urlencoded; charset=UTF-8',
        'json':'application/json'
    };
    
    var dump = function(data)
    {
        console.debug(data);
    }
    
    var dump2 = function(data) {
        alert(JSON.stringify(data));	
    };
    
    jQuery.fn.exists = function(){return this.length>0;}
    
    var empty = function(data){	
        if (typeof data === "undefined" || data==null || data=="" || data=="null" || data=="undefined" ) {	
            return true;
        }
        return false;
    };
    
    jQuery(document).ready(function() {
        
        /*MENU*/
        if( $('.sidebar-nav').exists() ) {     
           $("ul.sidebar-nav ul > li.active").parent().addClass("open");	 
           
           //$('ul li a').click(function(){	             
           $( document ).on( "click", "ul li a", function() {
              $(this).parent().find(".sidebar-nav-sub-menu").slideToggle("fast");          
              
              if( $('.nice-scroll').exists() ) {		
                  setTimeout(function(){ 		
                    $(".nice-scroll").getNiceScroll().resize();   
                  }, 100);           
              }
            
           });
               
        }
        
        $( document ).ready( function() {
          $( '.dropdown' ).on( 'show.bs.dropdown', function() {
            $( this ).find( '.dropdown-menu' ).first().stop( true, true ).slideDown( 150 );
          } );
          $('.dropdown').on( 'hide.bs.dropdown', function(){
            $( this ).find( '.dropdown-menu' ).first().stop( true, true ).slideUp( 150 );
          } );
        });
        
        if( $('.top-container').exists() ) {
            /*var topnav = document.querySelector('.top-container');
            if(window.pageYOffset>0){
                topnav.classList.add('scrolled')
            }
            window.onscroll = function() {		  
              if (window.pageYOffset > 0) {
                topnav.classList.add('scrolled')
              } else {
                topnav.classList.remove('scrolled')
              }
            }*/
        }
        
        if( $('.headroom').exists() ) {
            var myElement = document.querySelector(".headroom");		
            var headroom  = new Headroom(myElement);
            headroom.init();
        }
        if( $('.headroom2').exists() ) {
            var myElement = document.querySelector(".headroom2");		
            var headroom  = new Headroom(myElement);
            headroom.init();
        }
        
        if( $('.select_two').exists() ) {
            $('.select_two').select2({
                allowClear: false,
                templateResult: hideSelected,
                theme: "classic"
            });
        }
        
        if( $('.select_two_ajax').exists() ) {
            var select2_action  = $(".select_two_ajax").attr("action");
            $('.select_two_ajax').select2({		
             theme: "classic",
                language: {
                searching: function() {
                    return "Searching...";
                },
                noResults: function (params) {
                  return "No results";
                }
             },
              ajax: {
                  delay: 250,		  	 
                url: ajaxurl+"/"+ select2_action,
                type: 'POST',
                data: function (params) {
                  var query = {
                    search: params.term,		        
                    'YII_CSRF_TOKEN': $('meta[name=YII_CSRF_TOKEN]').attr('content')
                  }			     
                  return query;
                }
              }
            });				
        }
        
        if( $('.select_two_ajax2').exists() ) {
            var select2_action  = $(".select_two_ajax2").attr("action");
            $('.select_two_ajax2').select2({
                language: {
                searching: function() {
                    return "Searching...";
                },
                noResults: function (params) {
                  return "No results";
                }
             },
              ajax: {
                  delay: 250,		  	 
                url: ajaxurl+"/"+ select2_action,
                type: 'POST',
                data: function (params) {
                  var query = {
                    search: params.term,		        
                    'YII_CSRF_TOKEN': $('meta[name=YII_CSRF_TOKEN]').attr('content')
                  }			     
                  return query;
                }
              }
            });				
        }
        
        
        if( $('.datepick').exists() ) {		
            $('.datepick').each(function(index, $obj){		    			
                $($obj).daterangepicker({
                    "singleDatePicker": true,	
                    "autoUpdateInput": false,		
                    "locale" : {
                      format: 'YYYY-MM-DD'
                    },
                    "autoApply": true,
                }, function(start, end, label) {			
                    $($obj).val( start.format('YYYY-MM-DD') );		
                });	
            });
        }
            
        if( $('.date_range_picker').exists() ) {
            var $this = $('.date_range_picker');
            var separator = $this.data("separator");
            $('.date_range_picker').daterangepicker({	
                "autoUpdateInput": false,				
                "showWeekNumbers": true,
                "alwaysShowCalendars": true,
                "autoApply": true,
                "locale" : {
                  format: 'YYYY-MM-DD'
                },
                 ranges: {
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, function(start, end, label) {			
                $this.val( start.format('YYYY-MM-DD') +" "+ separator +" "+   end.format('YYYY-MM-DD') );		
            });			
        }
            
        if( $('.timepick').exists() ) {
            $('.timepick').datetimepicker({
                  format: 'hh:mm A',			  
                  //locale: "ru"	
                  //format: 'HH:mm',		 
            });	
        }
        
        if( $('.tool_tips').exists() ) {
           $('.tool_tips').tooltip();
        }
        
        if( $('.colorpicker').exists() ) {
            $('.colorpicker').spectrum({
              type: "component",
              showAlpha: false
            });
        }
        
        $( "#frm_search" ).submit(function( event ) {						
            data_tables.search($(".search").val()).draw();	    	    	    
            $(".search_close").show();
        });	
        $( document ).on( "click", ".search_close", function() {
             $( "#frm_search").find(".search").val('')	;	   	     
             data_tables.search('').draw();
             $(".search_close").hide();
        });
        
        $( "#frm_search_app" ).submit(function( event ) {						
            lazyDestroy();
            initLazyLoad();
            $(".search_close_app").show();
        });
        $( document ).on( "click", ".search_close_app", function() {
             $( "#frm_search_app").find(".search").val('')	;		     
             $(".search_close_app").hide();	     
             lazyDestroy(true);
             initLazyLoad();
        });
        
        $( ".frm_search_filter" ).submit(function( event ) {			     
             $(".search_close_filter").show();
             data_tables.destroy(); data_tables.clear();
             initTable(  $(".table_datatables"), $(".frm_datatables")  );
        });
        $( document ).on( "click", ".search_close_filter", function() {		 
             $( ".frm_search_filter").find(".search,.date_range_picker").val('')	;	   	     	     
             $(".search_close_filter").hide();
             
             data_tables.destroy(); data_tables.clear();
             initTable(  $(".table_datatables"), $(".frm_datatables")  );
        });
                
        
        $(".image_file").on("change", function() {
          var fileName = $(this).val().split("\\").pop();
          $(this).siblings(".image_label").addClass("selected").html(fileName);
        });
        
        $(".image2_file").on("change", function() {
          var fileName = $(this).val().split("\\").pop();
          $(this).siblings(".image2_label").addClass("selected").html(fileName);
        });
            
        if( $('.mask_time').exists() ) {
             $('.mask_time').mask('00:00');
        }
    
        if( $('.mask_minutes').exists() ) {
             $('.mask_minutes').mask('00');
        }
        
        if( $('.mask_phone').exists() ) {
             $('.mask_phone').mask('(00) 0000-0000');
        }
        
        if( $('.mask_mobile').exists() ) {
             $('.mask_mobile').mask('+000000000000');
        }
        
        if( $('.card_number').exists() ) {
             $('.card_number').mask('0000 0000 0000 0000');
        }	
        if( $('.card_expiration').exists() ) {
             $('.card_expiration').mask('00/00');
        }	
        if( $('.card_cvv').exists() ) {
             $('.card_cvv').mask('000');
        }
    
        if( $('.estimation').exists() ) {
             $('.estimation').mask('00-00');
        }
        
        if( $('.summernote').exists() ) {
            $('.summernote').summernote({
                 height: 200,
                  toolbar: [
                      ["font", ["bold", "underline", "italic", "clear"]],
                      ["para", ["ul", "ol", "paragraph"]],
                      ["style", ["style"]],
                      ["color", ["color"]],
                      ["table", ["table"]],
                      ["insert", ["link", "picture", "video"]],
                      ["view", ["fullscreen", "undo", "redo"]],
                  ]
            });
        }
            
        if( $('.copy_text_to').exists() ) {
            $('.copy_text_to').keyup(function(e) {
              var txtVal = $(this).val();	 
              var textTo = $(this).data("id");	  
              txtVal = cleanURL(txtVal);
              $(textTo).val(txtVal);
           }); 
        }
            
        if ((typeof  is_mobile !== "undefined") && ( is_mobile !== null)) {
            $is_mobile = is_mobile;
        }
                            
        if(!$is_mobile){
            if( $('.nice-scroll').exists() ) {				
                $(".nice-scroll").niceScroll({
                    autohidemode:true,
                    horizrailenabled:false,
                }); 	
            }
         }
        
         $('.sidebar-panel').slideAndSwipe();
         
         $( document ).on( "click", ".hamburger", function() {
              $(this).toggleClass("is-active");
         });
              
         $(document).on('click', function (e) {
              if ($(e.target).closest(".hamburger").length === 0) {
                if ( $(".hamburger").hasClass("is-active") ){
                      $(".hamburger").removeClass("is-active");
                }
             } 
         });
         
         $( document ).on( "click", ".checkbox_select_all", function() {
             $(this).toggleClass("checked");
             if( !$(this).hasClass("checked") ){		 
                 $('.checkbox_child').prop('checked',false);	
             } else {
                 $('.checkbox_child').prop('checked',true);	
             }		 	 
        });
        
        $(".item_multi_options").on("change", function() {		
            ItemSwitchMultiOption( $(this).val() );
        });
        
        if ( $(".item_multi_options").exists()){
            ItemSwitchMultiOption( $(".item_multi_options").val() );
        }
        
        if( $("#lazy-start").exists() ) {		
          initLazyLoad();
        }
        
        $(".broadcast_send_to").on("change", function() {		
            SwitchBroadcast( $(this).val() );
        });
        
        if ( $(".broadcast_send_to").exists()){
            SwitchBroadcast( $(".broadcast_send_to").val() );
        }
                 
          $('.table_review tbody').on('click', 'td', function () {	  	  
                 data_tables_tr = $(this).closest('tr');
                 data_tables_row = data_tables.row( data_tables_tr );
                 var $parent_id =  $(this).find("a.review_viewcomments").data("id");	
                 if($parent_id>0){
                    processAjax("customer_reply","parent_id="+$parent_id);	   	  	   
                 }
          });
          
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        });
        
    });
    /*end doc ready*/
    
    function ItemSwitchMultiOption($selected)
    {	
        switch($selected){
            case "custom":
              $(".multi_option_value_text").show();
              $(".multi_option_value_selection").hide();
            break;
            
            case "multiple":
                $(".multi_option_value_text").show();
                $(".multi_option_value_selection").hide();
                break;
                
            case "two_flavor":
              $(".multi_option_value_text").hide();
              $(".multi_option_value_selection").show();
            break;
            
            default:
              $(".multi_option_value_text").hide();
              $(".multi_option_value_selection").hide();
            break;
        } 
    }
    
    function SwitchBroadcast($selected)
    {		
        switch($selected){
            case 3:
            case "3":
              $(".broadcast_list_mobile").show();
            break;
            
            default:
              $(".broadcast_list_mobile").hide();
            break;
        }
    }
    
    function cleanURL(s) {
        return s.toString().normalize('NFD').replace(/[\u0300-\u036f]/g, "") //remove diacritics
                .toLowerCase()
                .replace(/\s+/g, '-') //spaces to dashes
                .replace(/&/g, '-and-') //ampersand to and
                .replace(/[^\w\-]+/g, '') //remove non-words
                .replace(/\-\-+/g, '-') //collapse multiple dashes
                .replace(/^-+/, '') //trim starting dash
                .replace(/-+$/, ''); //trim ending dash
    }
    
    function hideSelected(value) {
      if (value && !value.selected) {
        return $('<span>' + value.text + '</span>');
      }
    }
    
    var t = function(words){
        return words;
    };
    
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
    
    var initTable = function($table, $form)
    {		
        $.fn.dataTable.ext.errMode = 'none';
        var frm_table = $form.serializeArray();	
        var extra_data = {};
        /*$.each(frm_table,function(i, v) {		
            extra_data[v.name] = v.value;
        });*/
        
        $.each(frm_table, function() {
            if (extra_data[this.name]) {
                if (!extra_data[this.name].push) {
                    extra_data[this.name] = [extra_data[this.name]];
                }
                extra_data[this.name].push(this.value || '');
            } else {
                extra_data[this.name] = this.value || '';
            }
        });
        
        var $action_name = '';
        if ((typeof  action_name !== "undefined") && ( action_name !== null)) {
            $action_name = action_name ;
        }
        
        /*dump(frm_table);
        dump(extra_data);*/
                
        data_tables = $table.on('preXhr.dt', function ( e, settings, data ) {
            dump('loading');                
         }).on('xhr.dt', function ( e, settings, json, xhr ) {
             dump('done');     	       	
             setTimeout(function(){ 		
                $('.tool_tips').tooltip();   	
            }, 100); 
         }).on( 'error.dt', function ( e, settings, techNote, message ) {
             dump('error');     	
         }).DataTable( {
             "aaSorting": [[ 0, "DESC" ]],	
            "processing": true,
            "serverSide": true,
            "bFilter":true, 
            "dom":'<"top">rt<"row"<"col-md-6"i><"col-md-6"p>><"clear">',                        
            "pageLength": 10,                          
             "ajax": {
                "url": ajaxurl+"/"+$action_name,
                 "type": "POST",
                "data": extra_data
            },
            language: {
                url: ajaxurl+"/DatableLocalize"
            }
         });
                   
    }
    
    /* ===========================  UTILITY END HERE  ===========================  */
    
    var $item_id = '';
    var ajax_request = {};
    var timer = {};
    var $infinite_scroll ;
    var $dropzone;
    
    
    jQuery(document).ready(function() {
        
        if ( $(".table_datatables").exists()){
            initTable(  $(".table_datatables"), $(".frm_datatables")  );
        }
        
        $( document ).on( "click", ".datatables_delete", function() {
            $item_id = $(this).data("id");		
            $(".delete_confirm_modal").modal('show');
        });	
        
        $('.delete_confirm_modal').on('shown.bs.modal', function () {
            if ((typeof  delete_custom_link !== "undefined") && ( delete_custom_link !== null)) {
                $(".item_delete").attr('href',delete_custom_link+"&id="+$item_id);
            } else {
               $(".item_delete").attr('href',delete_link+"?id="+$item_id);
            }
        });
        
        $( document ).on( "click", ".delete_image", function() {		
            $item_id = $(this).data("id");	
            $(".delete_image_confirm_modal").modal('show');
        });	
        
        $('.delete_image_confirm_modal').on('shown.bs.modal', function () {
            $(".item_delete").attr('href',$item_id);
        });
        
        $( document ).on( "click", ".order_history", function() {
            $item_id = $(this).data("id");				
            $(".order_history_modal").modal('show');
        });	
        $('.order_history_modal').on('show.bs.modal', function () {
            processAjax("order_history","id="+$item_id);
        });
        
        if( $('#dropzone_multiple').exists() ) {
            DropzoneMultipleInit();
        }
        
        $( document ).on( "change", ".set_item_available", function(event) {
            var $id = $(event.target).val();
            var $checked = $(this).is(':checked'); 
            $checked = $checked==true?1:0;
            setTimeout(function(){			
                processAjax("update_item_available","id="+ $id +  "&checked="+ $checked  )
            }, 100);
        });
        
        $( document ).on( "change", ".set_payment_provider", function() {		
            var $id = $(this).val();		
            var $active = $(this).prop('checked');
            $active = $active==true?"active":"inactive";
            setTimeout(function(){			
                processAjax("set_payment_provider","id="+ $id  + "&status=" + $active )
            }, 100);
        });	
    
        $( document ).on( "change", ".set_banner_status", function(event) {
            var $id = $(event.target).val();
            var $checked = $(this).is(':checked'); 
            $checked = $checked==true?1:0;
            setTimeout(function(){			
                processAjax("set_banner_status","id="+ $id +  "&checked="+ $checked  )
            }, 100);
        });
        
        /*$(".merchant_delivery_charges_type").on("change", function() {		
            DeliveryChargesSwitchOption( $(this).val() );
        });
        
        if ( $(".merchant_delivery_charges_type").exists()){
            DeliveryChargesSwitchOption( $(".merchant_delivery_charges_type").val() );
        }*/
        
        $( ".coupon_options" ).change(function() {
            CouponToggle( $(this).val() );
        });
        
        if( $('.coupon_options').exists() ) {
            CouponToggle( $(".coupon_options").val() );
        };
        
    });
    /*end ready*/
    
    
    var CouponToggle = function($value){
        $(".coupon_customer").hide();
        $(".coupon_max_number_use").hide();
        if($value==6){
            $(".coupon_customer").show();
        } else if ( $value==5) {
            $(".coupon_max_number_use").show();
        } else {
            //
        }
    };
    
    /*var DeliveryChargesSwitchOption = function($value){
        switch($value)
        {
            case "fixed":
              $(".fixed_charge").show();
            break;
            
            case "use_charges_rates":
              $(".fixed_charge").hide();
            break;
        }
    };*/
    
    var getTimeNow = function(){
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
    var processAjax = function(action , data , single_call, silent, method ){
        
        timenow = getTimeNow();
        if(!empty(single_call)){			
            var timenow = single_call;
        }	
        
        if(empty(method)){
            method="POST";
            data+=addValidationRequest();		
        } 
        
        ajax_request[timenow] = $.ajax({
          url: ajaxurl+"/"+action,
          method: method,
          data: data ,
          dataType: "json",
          timeout: 20000,
          crossDomain: true,
          beforeSend: function( xhr ) {   
               if ((typeof silent !== "undefined") && (silent !== null)) {	  	    
               } else {
                   //loader(1); 
               }
             if(ajax_request[timenow] != null) {	
                 dump("request aborted");     
                 ajax_request[timenow].abort();
                clearTimeout( timer[timenow] );
             } else {         	
                 timer[timenow] = setTimeout(function() {				
                    ajax_request[timenow].abort();
                    notify( t('Request taking lot of time. Please try again') );
                }, 20000); 
             }
          }
        });
        
        ajax_request[timenow].done(function( data ) {
             dump('done');	
             var next_action='';     
             if ((typeof  data.details.next_action !== "undefined") && ( data.details.next_action !== null)) {
                 next_action = data.details.next_action;
             }
             if (data.code==1){
                 switch(next_action){	     		
                     
                     case "csv_continue":	     		
                       $(".csv_progress_"+ data.details.id ).html( data.msg );
                       setTimeout(function(){ 		
                         processCSV( data.details.id );
                       }, 1*1000); 
                     break;
                     
                     case "csv_done":
                        $(".csv_progress_"+ data.details.id ).html( data.msg );
                        $('a.view_delete_process[data-id="'+ data.details.id +'"]').html('<i class="zmdi zmdi-mail-send"></i>');
                     break;
                                                   
                     case "order_history":
                        fillHistory(data.details.data,'.order_history_modal table tbody');
                     break;
                     
                     case "review_reply":
                        fillReviewReply( data.details.data );
                      break;
                     
                     case "silent":  
                        break;
                        
                     default:
                       notify(data.msg,'success');
                     break;
                 }
             } else {
                 //FAILED
                 switch(next_action){
                     
                     case "clear_order_history":	     		   
                        $('.order_history_modal table tbody').html('');
                       break;
                       
                     case "silent":  
                        break;
                        
                     default:
                       notify(data.msg,'danger');
                     break;
                 }	     	
             }
        });    	
        /*end done*/
        
        /*ALWAYS*/
        ajax_request[timenow].always(function() {
            //loader(2);
            dump("ajax always");
            ajax_request[timenow]=null;  
            clearTimeout(timer[timenow]);
        });
        
        /*FAIL*/
        ajax_request[timenow].fail(function( jqXHR, textStatus ) {    	
            clearTimeout(timer[timenow]);
            notify( t("Failed") + ": " + textStatus ,'danger' );        
        });  
        
    };
    /*end mycall*/
    
    var fillHistory = function($data,$target){
        var $html='';
        $.each($data,function(key, val) {		
            $html+='<tr>';
            $html+='<td>'+val.date_created+'</td>';
            $html+='<td>'+val.status+'</td>';
            $html+='<td>'+val.remarks+'</td>';
            $html+='</tr>';
        });
        $($target).html( $html );
    };
    
    var DropzoneMultipleInit = function(){	
        if ((typeof  upload_params !== "undefined") && ( upload_params !== null)) {
            var $upload_params = JSON.parse(upload_params);	
        } else var $upload_params = {};
    
        var $dropzone_action = $("#dropzone_multiple").data("action");	
        
        $dropzone = $("#dropzone_multiple").dropzone({ 
            paramName: "file", 		
            url: upload_ajaxurl+"/" +  $dropzone_action  ,
            maxFiles: 20,
            params: $upload_params,
            addRemoveLinks : false,		
            success: function (file, response) {
                file.previewElement.innerHTML = "";
                var $resp = JSON.parse(response);
                dump($resp);		
                            
                var $next_action='';     
                if ((typeof  $resp.details !== "undefined") && ( $resp.details !== null)) {
                if ((typeof  $resp.details.next_action !== "undefined") && ( $resp.details.next_action !== null)) {
                     $next_action = $resp.details.next_action;
                }
                }
                            
                if($resp.code==1){				
                    switch($next_action){
                        case "display_image":
                          var $file_url = $resp.details.file_url;
                          var $remove_url = $resp.details.remove_url;
                          AddToGallery( ".item_gallery_preview .row" , $file_url,  $remove_url);
                        break;
                        
                        default:
                          notify($resp.msg,'success');
                        break;
                    }			
                } else {				
                    switch($next_action){
                        default:					
                        notify($resp.msg,'danger');
                        break;
                    }
                }				
            }
        });
    };
    
    var AddToGallery = function($target,$file_url, $remove_url){
        var html='';
        html+='<div class="col-lg-4 mb-4 mb-lg-0 preview-image">';
          html+='<a type="button" class="btn btn-black btn-circle delete_image" href="javascript:;" data-id="'+$remove_url+'"><i class="zmdi zmdi-plus"></i></a>';	
          html+='<img src="'+$file_url+'" class="img-fluid mb-2">';    
        html+='</div>';
        $($target).append( html );
    };
    
    
    var initLazyLoad = function(){
            
        $infinite_scroll = $('#lazy-start').infiniteScroll({
          path: function() {
              
              var frm_table = $(".frm_search").serializeArray();	
            var extra_data = {};
            var $params ='';
            
            $.each(frm_table, function() {
                if (extra_data[this.name]) {
                    if (!extra_data[this.name].push) {
                        extra_data[this.name] = [extra_data[this.name]];
                    }
                    extra_data[this.name].push(this.value || '');
                } else {
                    extra_data[this.name] = this.value || '';
                }
            });	  	
            
              $.each(extra_data,function(extra_key, extra_val) {	  		
                  $params+="&"+extra_key+"="+extra_val;
              });
              
              $params+= '&page='+ this.pageIndex ;	 	  	
            return ajaxurl  + '/'+ action_name +'/?'+$params;
          },
          responseBody: 'json',
          history: false,
          status: '.lazy-load-status',	  
        });
        
        $infinite_scroll.on( 'load.infiniteScroll', function( event, body ) {	 	    
            if(body.code==1){	       
               $(".page-no-results").hide();
               if(body.details.is_search){
                     dump("search==");
                     $infinite_scroll.html('');
               } 	   
               
               var $next_action='';     
               if ((typeof  body.details.next_action !== "undefined") && ( body.details.next_action !== null)) {
                     $next_action = body.details.next_action;
               }
               
               dump(body);
               
               switch($next_action){
                   
                     case "display_gallery":		   	     
                        $("#lazy-start").addClass("row");
                        setLazyGallery( body.details.data );
                       break;
                       
                     default:
                       setLazyData( body.details.data );
                     break;
               }
                          
            } else {		       
               var $page = parseInt(body.details.page);	 
                if($page<=0){
                      $infinite_scroll.html('');
                   $(".page-no-results").show();
                  } else {
                       $infinite_scroll.infiniteScroll( 'option', {
                   loadOnScroll: false,
                  });	
                  }	       	      
            }	
        });
        
        $infinite_scroll.infiniteScroll('loadNextPage');
        
    };
    
    
    var setLazyData = function(data){	
        var $html='';
        $.each(data,function(key, val) {
            $html+='<div class="kmrs-row">';
              $html+='<div class="d-flex bd-highlight">';
              
                  $html+='<div class="p-2 bd-highlight">';
                  $html+=val[0];
                  $html+='</div>';
                  
                  $html+='<div class="p-2 bd-highlight flex-grow-1">';
                  $html+=val[1];
                  $html+='</div>';		  
                  
              $html+='</div>';
                            
              /*ACTIONS*/
              $html+='<div class="d-flex justify-content-end">';	 
                if ($.isArray(val[2]) ){	        
                   $.each(val[2],function($row) {
                         $html+='<div class="p-2" >';
                      $html+=val[2][$row];
                      $html+='</div>';
                   });
                }
              $html+='</div>';
              
            $html+='</div>';
        });	
        $infinite_scroll.append( $html );
    };
    
    var lazyDestroy = function($clear){
        try {		
            if($clear){
                $infinite_scroll.html('');
            }
            $infinite_scroll.infiniteScroll('destroy');
            $infinite_scroll.removeData('infiniteScroll');
            $infinite_scroll.off('load.infiniteScroll');
        }catch(err){
            dump(err.message);
        }		
    };
    
    var setLazyGallery = function(data){
        var $html='';
        
        $.each(data,function(key, val) {
        $html+='<div class="col-lg-3 col-md-12 mb-4 mb-lg-3">';
          $html+='<div class="card" >';
            $html+=val[0];
            $html+='<div class="card-body">';	       
               $html+=val[1];
               
               $html+='<div class="d-flex justify-content-end">';
               $html+='<div class="btn-group btn-group-actions" role="group">';
               $html+=val[2][1];
               $html+='</div>';
               $html+='</div>';
               
            $html+='</div>';
          $html+='</div>';
        $html+='</div>';
        });	
        
        $infinite_scroll.append( $html );
    };
    
    var fillReviewReply = function(data){
        var $html='';		
        $.each(data,function(key, val) {				
            $html+='<div class="d-flex">';
              $html+='<div class="w-100 ml-5"><h6>'+val.reply_from+'</h6> <p>'+val.review+'</p>';
              
               $html+='<div class="btn-group btn-group-actions mr-4" role="group">'; 
                   
                   $html+='<a href="'+val.edit_link+'" class="btn btn-light tool_tips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Update">';
                    $html+='<i class="zmdi zmdi-border-color"></i>';
                   $html+='</a>';
                   
                   $html+='<a href="javascript:;" data-id="'+ val.id+'" class="btn btn-light datatables_delete tool_tips" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">';
                    $html+='<i class="zmdi zmdi-delete"></i>';
                   $html+='</a>';
                   
                   $html+='</div>'; /*group*/   
              
              $html+='</div>';	  	      
            $html+='</div>';
        });	
        
        if ( data_tables_row.child.isShown() ) {
          data_tables_row.child.hide();
          data_tables_tr.removeClass('shown'); 	
        } else {
          data_tables_row.child( $html ).show();
          data_tables_tr.addClass('shown');
        }
    
    };
    
    
    /*
       VUE STARTS HERE
    */
    
    
    /*
       ==================================================================================================
       START OF ALL COMPONENTS HERE
       ==================================================================================================
    */
    
    /*
      COMPONENTTS WEB PUSH
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
                            vm_notifications.$refs.notification.addData(data);
                        });			    
                    }).catch(e => {
                        var data =  {
                            notification_type :"push",
                            message : "Beams " + e,
                            date : '',
                            image_type : 'icon',
                            image : 'zmdi zmdi-info-outline'
                        };								
                        vm_notifications.$refs.notification.addData(data);
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
        props: ['ajax_url','label','realtime','view_url'],
        data(){
           return {		
                 data : [],
                 count : 0,
                 new_message : false,
                 player : undefined,
                 ably : undefined,
                 channel : undefined,
                 piesocket : undefined
           };
        },
        mounted() {
            this.getAllNotification();    	
            if(this.realtime.enabled){
                this.initRealTime();
            }    	    	    	
            //dump(this.realtime);
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
                if(this.realtime.provider == "pusher"){
                   
                    Pusher.logToConsole = false;
                    var pusher = new Pusher( this.realtime.key , {
                      cluster: this.realtime.cluster
                    });
                                    
                    var channel = pusher.subscribe( this.realtime.channel  );
                    channel.bind( this.realtime.event , (data)=> {
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
                  src: ['../assets/sound/notify.mp3', '../assets/sound/notify.org' ],
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
                this.data.unshift($data);
                this.count++;     
                this.new_message = true;
                
                setTimeout(()=>{ 		
                    this.new_message = false;
                    //this.player.stop();
                }, 1000);           
                
                /*REFRESH SIDEBAR*/
                if ((typeof  vm_siderbar_menu !== "undefined") && ( vm_siderbar_menu !== null)) {
                    vm_siderbar_menu.getOrdersCount();
                }
                if ((typeof  vm_order_management !== "undefined") && ( vm_order_management !== null)) {
                    vm_order_management.$refs.orderlist.getList();
                }
                if ((typeof  vm_dashboard !== "undefined") && ( vm_dashboard !== null)) {
                    vm_dashboard.refreshLastOrder();
                }		    	  		    
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
                <i class="zmdi zmdi-notifications-none"></i>
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
    
    /*
      COMPONENTS MERCHANT STATUS
    */
    const componentsMerchantStatus = {
        props: ['label','ajax_url','tpl'],
        data(){
            return {		
             is_loading : false,
             data : [],		 
           };
        },
        mounted() {
           this.merchantPlanStatus();	   
        },
        methods : {
            merchantPlanStatus(){
                
               this.is_loading = true;
                  axios({
                   method: 'POST',
                   url: this.ajax_url+"/merchantPlanStatus" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout , 
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
        },
        template: `		
          <div v-if="tpl==='1'" class="card m-auto">
          
            <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
                <div>
                  <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
                </div>
            </div>
          
             <div class="card-body">
                <h5 class="mb-1">{{data.restaurant_name}}</h5>
                
                 <div class="d-flex justify-content-between">
                   <div class="flex-col">{{label.current_status}}</div>
                   <div class="flex-col"><span class="badge customer" :class="data.status_raw">{{data.status}}</span></div>
                 </div>
                
             </div>  <!-- body -->
          </div> <!-- card -->
          
         <template v-else-if="tpl==='2'" >
         <div v-if="data.status_raw=='expired'" class="p-2 align-self-center">
          <i class="zmdi zmdi-alarm text-danger"></i><span class="ml-2"><b>{{label.trial_ended}}</b></span>
         </div>
         </template>
        `
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
       ==================================================================================================
       END OF ALL COMPONENTS
       ==================================================================================================
    */
    
    
    /*
      COMPONENTS CONFIRM AND ALERT
    */
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
      COMPONENTS MODAL UPLOADER
    */
    const ComponentsUploader = {
       props: ['label','max_file','select_type','field','field_path','selected_file',
       'selected_multiple_file','max_file_size','inline','upload_path','save_path'],
       components: {
         'component-bootbox': Componentsbootbox,    
       },
       data(){
               return {		   
               data : [],	
               q: '',
               page_count : 0,
               current_page : 0,
               preview : false,	  
               dropzone : undefined,		   
               tab : 1,
               is_loading : false,
               page : 1,		   
               item_selected : [],
               added_files : [],		 
               awaitingSearch : false,  
               data_message : '',
            };	
       },
       mounted() {
            this.getMedia();
            this.getMediaSeleted();
            this.getMediaMultipleSeleted();
            this.initDropzone();    	 
       }, 
       updated(){   	
             dump("inline=>" + this.inline);
       },
       watch: {   	  
              q(newsearch,oldsearch){
                   if (!this.awaitingSearch) {
                                      
                       if(empty(newsearch)){
                          this.getMedia();   	   	 	
                   return false;
                }		
                
                setTimeout(() => {
                    
                     var $params = {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		    
                        'page' : this.page,
                        'q' : this.q
                     };		
                     var timenow = getTimeNow();
                     $params = JSON.stringify($params);				
                     
                     ajax_request[timenow] = $.ajax({
                         url: upload_ajaxurl+"/getMedia",
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
                         this.data_message = data.msg;		 	    
                         if ( data.code==1){	 
                             this.data = data.details.data;
                             this.page_count = data.details.page_count;
                             this.current_page = data.details.current_page;
                         } else {	 	    		
                             this.data = [];
                             this.page_count = 0;
                             this.current_page = 0;
                         } 	    
                    }).always( data => {		 	    	
                         this.awaitingSearch = false;
                    });	   		 	 
                    
                }, 1000); // 1 sec delay
                
                this.data = [];
                this.awaitingSearch = true;
                   }
              }
       },
       computed: {
            hasData(){			
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
            hasSelected(){			
                if(this.item_selected.length>0){
                   return true;
                } 
                return false;
            },
            totalSelected(){
                return this.item_selected.length;
            },
            hasAddedFiles(){
                if(this.added_files.length>0){
                   return true;
                } 
                return false;
            },
            noFiles(){
                if(this.data.length>0){
                    return false;
                }
                if(this.awaitingSearch){
                    return false;
                }
                return true;
            },
            hasSearch(){
                if(!empty(this.q)){
                    return true;
                }
                return false;
            },
       },
       methods: {
            show(){    	  	 	  	
                 $( this.$refs.modal_uplader ).modal('show');
            },
            close(){  	  	 
                 $( this.$refs.modal_uplader ).modal('hide');
            },
            previewTemplate(){
                 var tpl=`
                  <div class="col-lg-3 col-md-12 mb-4 mb-lg-3">
                     <div class="card">
                         <div class="image"><img data-dz-thumbnail /></div>
                         
                         <div class="p-2 pt-0">
                         <p class="m-0 name" data-dz-name></p>
                         <p class="m-0 size" data-dz-size></p>  	
                         
                         <div class="progress">
                          <div class="progress-bar" role="progressbar" aria-valuenow="0" 
                          style="width:0%;" data-dz-uploadprogress
                          aria-valuemin="0" aria-valuemax="100"></div>
                     </div>  	     
                     </div>
                     </div> 
                  </div> <!-- col -->
                 `
                 return tpl;
            },
            initDropzone(){    	
                                          
                 var $params = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                'upload_path' : this.upload_path
             };				
                                   
                 this.dropzone = new Dropzone( this.$refs.dropzone , { 
                     paramName: "file", 
                     maxFilesize: parseInt(this.max_file_size), // MB
                     url: upload_ajaxurl+"/file",  	
                     maxFiles: this.max_file,
                params: $params,  	  	  	 	 
                     clickable: this.$refs.fileinput,
                     previewsContainer: this.$refs.ref_preview,  	  	 	
                     previewTemplate: this.previewTemplate() ,
                     acceptedFiles: "image/*",  	  	 	
                 });
                 
                 this.dropzone.on("addedfile", file => {  	  	 	
                     this.preview = true;   	  	   	  	 
                     dump("added file=>" + file.type);
                     switch(file.type){
                         case "image/jpeg":
                         case "image/png":
                         case "image/svg+xml":
                         case "image/webp":
                         case "image/apng":  	  	 		  
                         break;
                         
                         default:
                           this.dropzone.removeFile(file);
                         break;
                     }  	  	 	
                 }); 	  
                 
                 this.dropzone.on("queuecomplete", file => {
                      dump("All files have uploaded ");
                      this.getMedia();
                 });
                 
                 this.dropzone.on("success", (file, response) => {
                      dump("success");  	  	 	 
                      response = JSON.parse(response);
                      dump(response);
                      if(response.code==2){
                          notify(response.msg);
                          this.dropzone.removeFile(file);
                      }
                 });
                 
            },
            getMedia(){
                 var $params = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		    
                'page' : this.page,		    
                'selected_file' : this.selected_multiple_file,
                'item_selected' : this.item_selected
             };		
             var timenow = getTimeNow();
             $params = JSON.stringify($params);				
             
             ajax_request[timenow] = $.ajax({
                 url: upload_ajaxurl+"/getMedia",
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
                 this.data_message = data.msg;
                 if ( data.code==1){	 
                     this.data = data.details.data;
                     this.page_count = data.details.page_count;
                     this.current_page = data.details.current_page;
                 } else {	 	    		
                     this.data = [];
                     this.page_count = 0;
                     this.current_page = 0;
                 } 	    
            }).always( data => {
                 this.is_loading = false;
            });	   
                  
            },
            addMore(){
                 this.preview = false;
            },
            pageNum(page){
                this.page = page;
                this.getMedia();
            },
            pageNext(){
                 this.page = parseInt(this.page)+1;   	  	 
                 if(this.page>=this.page_count){  	  	 	
                     this.page = this.page_count;  	  	 	
                 }  	  	 
                 this.getMedia();
            },
            pagePrev(){
                 this.page = parseInt(this.page)-1;   
                 dump(this.page +"=>"+this.page_count )	  	 
                 if(this.page<=1){  	  	 	
                     this.page = 1;  	  	 	
                 } 
                 this.getMedia();
            },
            itemSelect(item,index){  	  	  	  	  	  	 
                 item.is_selected = !item.is_selected;  
                 
                 if(this.select_type=="single"){
                    this.removeAllSelected(item.id);
                    if(item.is_selected){    	  	 
                         this.item_selected[0]={
                             //id : item.id,
                             filename : item.filename,
                             image_url : item.image_url,
                             path : item.path,
                         };  	  	 
                     } else {
                         this.item_selected.splice(0,1); 
                     }
                 } else {
                      if(item.is_selected){  
                         this.item_selected.push({
                           //id : item.id,
                           filename : item.filename,
                           image_url : item.image_url,
                           path : item.path,
                         });  	  	 
                     } else {  	 	  	  	 	
                         this.item_selected.forEach((data,index) => {  	  	 		
                             //if(data.id==item.id){  	  	 			
                             if(data.filename==item.filename){  
                                 this.item_selected.splice(index,1);  	  	 			
                             }
                         });
                     }   	  	 
                 }
            },
            removeAllSelected(id){
                this.data.forEach((data,index) => {  	  	
                    if(data.id!=id){  	  	 	 		
                      data.is_selected = false;
                    }
               });
            },
            addFiles(){
                 var $item_selected = [];
                 this.item_selected.forEach((data,index) => {  	  	  	 	
                     $item_selected[index] = {
                         id : data.id,
                         filename : data.filename,
                         image_url : data.image_url,
                         path : data.path,
                     };
                 });
                     
                 this.added_files = $item_selected;
                 this.close();
            },
            removeAddedFiles(index){
                this.added_files.splice(index,1); 
            },
            getMediaSeleted(){
                
                 if(empty(this.selected_file)){
                     return ;
                 }
                 
                 var $params = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		    
                'selected_file' : this.selected_file,
                'save_path' : this.save_path,
             };		
             var timenow = getTimeNow();
             $params = JSON.stringify($params);				
             
             ajax_request[timenow] = $.ajax({
                 url: upload_ajaxurl+"/getMediaSeleted",
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
                 if ( data.code==1){	  	  
                     this.added_files = data.details;  		
                 } else {	 	    		
                     
                 } 	    
            });
                
            },
            
          getMediaMultipleSeleted(){
                
                 if(empty(this.selected_multiple_file)){
                     return ;
                 }
                 
                 var $params = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		    
                'selected_file' : this.selected_multiple_file,
                'save_path' :this.save_path
             };		
             var timenow = getTimeNow();
             $params = JSON.stringify($params);				
             
             ajax_request[timenow] = $.ajax({
                 url: upload_ajaxurl+"/getMediaMultipleSeleted",
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
                 if ( data.code==1){	  	  
                     this.added_files = data.details;  	
                     
                     var $item_selected = [];
                        this.added_files.forEach((data,index) => {  	  	  	 	
                             $item_selected[index] = {		  	  	 	    
                                 filename : data.filename,
                                 image_url : data.image_url,
                             };
                        });
                             
                        this.item_selected = $item_selected;
                     
                 } else {	 	    		
                     this.added_files = [];
                     this.item_selected = [];
                 } 	    
            });
                
            },  	    	 
            clearData(){
                this.q = '';
                this.getMedia();
            },
            beforeDeleteFiles(){  
                vm_bootbox.confirm().then((result) => {		  
                   if(result){
                      this.deleteFiles();
                   }
               });			  	   	  	
            },
            deleteFiles(){
                
                 var $params = {
                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		    
                'item_selected' : this.item_selected
             };		
             var timenow = getTimeNow();
             $params = JSON.stringify($params);				
             
             ajax_request[timenow] = $.ajax({
                 url: upload_ajaxurl+"/deleteFiles",
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
                     this.item_selected = []; 	    		
                     this.getMedia(); 	    	
                 } else {	 	    
                     vm_bootbox.alert( data.msg , {} );		 	    		
                 } 	    
            }).always( data => {		 	    	
                 this.is_loading = false;
            });	   		 	 
              
            },
            clearSelected($data){  	  	
                if(this.item_selected.length>0){
                    this.item_selected  = [];  	  		
                    $data.forEach((data,index) => {
                        data.is_selected = false;
                    });
                } else {
                    if( this.select_type=="multiple") { 	  	
                        var $item_selected = [];  	  	
                        $data.forEach((data,index) => {
                            data.is_selected = true;
                            $item_selected[index] = {		  	  	 	    
                                 filename : data.filename,
                                 image_url : data.image_url,
                             };
                        });
                        this.item_selected  = $item_selected;
                    }
                }
            },
       }, 
       template: `   
    
       
       <!---
       <div v-if="inline=='false'" class="input-group">  
          <div class="custom-file">         
             <label @click="show" class="custom-file-label image_label" for="upload_image">
             {{label.upload_button}}
             </label>  
          </div>      	  
       </div>
       -->
       
    
       <div v-if="inline=='false'" class="mb-2">
         <div class="border bg-white  rounded">	 
            <div class="row justify-content-between align-items-center">
              <div class="col-4 ml-3">{{label.upload_button}}</div>
              <div class="col-4 text-right">
                 <button @click="show"  type="button" class="btn btn-info" style="padding:.375rem .75rem;">
                   <template v-if="label.browse">{{label.browse}}</template>
                   <template v-else>Browse</template>
                 </button>
              </div>
            </div>
         </div>
       </div>
             
       <template v-for="item in added_files" >
          <template v-if="select_type=='single'">
            <input :name="field" type="hidden" :value="item.filename" />
            <input :name="field_path" type="hidden" :value="item.path" />
          </template>
          <template v-else >
            <input :name="field+'[]'" type="hidden" :value="item.filename" />
            <input :name="field_path+'[]'" type="hidden" :value="item.path" />
          </template>
       </template>
                
       <div v-if="hasAddedFiles" class="file_added_container pr-2">   
           <div class="row pt-3">	   
           <div v-for="(item, index) in added_files" class="col-md-2 mb-3 position-relative">
             <a @click="removeAddedFiles(index)" class="btn-remove btn btn-black btn-circle" href="javascript:;" >
              <i class="zmdi zmdi-close"></i>
             </a>  	  		 
              <img class="rounded" :src="item.image_url" />
           </div>
           </div>
       </div>
       <!--  file_added_container  -->
              
        <div ref="modal_uplader" :class="{'modal fade':this.inline=='false'}" 
        id="modalUploader" data-backdrop="static" 
        tabindex="-1" role="dialog" aria-labelledby="modalUploader" aria-hidden="true">
        
           <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
             <div class="modal-content"> 
             
               <div class="modal-header pb-1 bg-light">	        
                <ul class="nav nav-pills">
                  <li class="nav-item">
                    <a @click="tab=1" href="javascript:;" class="nav-link" :class="{ 'active': tab==1 }" >
                    {{label.select_file}}
                    </a>
                  </li>
                  <li class="nav-item">
                    <a @click="tab=2" href="javascript:;" class="nav-link" :class="{ 'active': tab==2 }"  >
                    {{label.upload_new}}
                    </a>
                  </li>
                </ul>
               
                <button v-if="inline=='false'" type="button" class="close"  aria-label="Close" @click="close" >
                  <span aria-hidden="true" style="font-size:1.5rem;">&times;</span>
                </button>
              </div> 
             
               <div class="modal-body">	 
    
               <!-- file list wrapper  -->
               <template v-if="tab=='1'" >
               
               <div class="row">
                  <div class="col">
                     <button type="button" class="send btn-upload-count" 
                     @click="clearSelected(data)"
                     :class="{selected : item_selected.length>0}"
                     :data-counter="totalSelected">&#10004;</button>
                  </div>
                  <div class="col">
                    <div class="form-group has-search">
                      <span v-if="!awaitingSearch" class="fa fa-search form-control-feedback"></span>
                      <span v-if="awaitingSearch" class="img-15 form-control-feedback" data-loader="circle"></span>
                      <div  v-if="hasSearch"  @click="clearData" class="img-15 clear_data">
                        <i class="zmdi zmdi-close"></i>
                      </div>
                      <input v-model="q" type="text" class="form-control" :placeholder="label.search" >
                    </div>
                  </div>
                </div>
                            
                
                <DIV class="file_wrapper">	
                
                 <div v-if="is_loading" class="cover-loader d-flex align-items-center justify-content-center">
                    <div>
                      <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
                    </div>
                 </div>
                              
                 <div v-if="noFiles" class="d-flex justify-content-center align-items-center file_upload_inner">
                   <div class="text-center">
                     <h5>{{data_message}}</h5>	             
                   </div>
                 </div> 	         
                          
                 <ul class="list-unstyled">			  
                  <li v-for="item in data"
                   :class="{ selected: item.is_selected }"	     
                   @click="itemSelect(item,index)"
                   >
                    <img :src="item.image_url" />
                    <p class="m-0"><strong>{{item.title}}</strong></p>
                    <p class="m-0"><small>{{item.size}}</small></p>
                  </li>			  
                </ul>
                
                </DIV>
                <!-- file_wrapper -->
               
               </template>	       
               <!-- end file list wrapper  -->
               
               <!-- file_preview_container -->
               
               <div :class="{'d-block': tab=='2' }" class="file_upload_container rounded position-relative">
                 <div ref="dropzone" class="d-flex justify-content-center align-items-center file_upload_inner">
                   <div class="text-center">
                      <h5>{{label.drop_files}} <br/> {{label.or}}</h5>
                     <a ref="fileinput" class="btn btn-green fileinput-button" href="javascript:;">
                     {{label.select_files}}
                     </a>
                   </div>
                 </div> 	         
                 
                 <!-- file_preview_container -->
                 <div :class="{ 'd-block': preview }" class="file_preview_container">	 
                      <nav class="navbar bg-light d-flex justify-content-end">
                         <button @click="addMore" type="button" class="btn">
                         + 
                         <template v-if="label.add_more">
                            {{label.add_more}}
                         </template>
                         <template v-else>
                            Add more
                         </template>
                         </button>
                      </nav>
                      
                      <div ref="ref_preview" class="row p-2">		          		            
                      </div> <!-- row -->
                      
                 </div>
                 <!-- file_preview_container -->
                 
               </div>
               
               <!-- end file_upload_container -->
               
               </div> <!--modal body-->
               
              <div class="modal-footer justify-content-start">
                <div class="row no-gutters w-100">
                  <div class="col">
                                
                   <!-- current page {{current_page}} page {{page}}  page_count {{page_count}} -->
                   <nav aria-label="Page navigation" v-if="hasData" >
                      <ul class="pagination">
                      
                        <li class="page-item" :class="{disabled: current_page=='1'}" >
                          <a @click="pagePrev()" class="page-link" href="javascript:;">{{label.previous}}</a>
                        </li>				    
                        
                        <!--
                        <li v-for="n in page_count" class="page-item" :class="{ active: current_page==n }" >
                          <a @click="pageNum(n)" class="page-link" href="javascript:;">{{n}}</a>
                        </li>
                        -->
                        
                        <li class="page-item" :class="{disabled: page_count==current_page}">
                           <a @click="pageNext()" class="page-link" href="javascript:;">{{label.next}}</a>
                        </li>
                      </ul>
                    </nav>
                  
                  </div> <!-- col -->
                  <div class="col text-right">
                   
                   <template v-if="inline=='false'" >
                       <button @click="addFiles" type="button" class="btn btn-green" :disabled="!hasSelected" >  
                        <span class="label">{{label.add_file}}</span>                 
                       </button>
                   </template>
                   <template v-else>
                      <button @click="beforeDeleteFiles" type="button" class="btn btn-green" :disabled="!hasSelected" >  
                        <span class="label">{{label.delete_file}}</span>                 
                       </button>
                   </template>
                  
                  </div>
                </div> <!-- row -->
              </div> <!--footer-->
               
            </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->     	       
       `
    };
    
    /*
      VUE UPLOADER 
    */
    const vm_uploader = Vue.createApp({
      components: {
        'component-uploader': ComponentsUploader,       
      },
      data(){
        return {						
            data : [],
        };
      },	
      mounted() {
        
      },
      methods: {
          
      },
    }).mount('#vue-uploader');
    
    
    
    /* ============================================================
        ORDER MANAGEMENT CODE STARTS HERE
       ============================================================*/
    
    
    /*
      COMPONENTS ORDER LIST 
    */
    const ComponentsOrderList = {
        props: ['order_status','ajax_url','label','show_critical','show_status','schedule'],
        data() {
            return {
              error: [],          
              is_loading : false,           
              data : [],
              meta : [],
              status : [],
              services : [],
              total : 0,
              order_uuid : '',
              order_type : '',
              response_code : 0,
              count_up : undefined,	
           }
        },
        mounted() {
            this.getList();
        },
        methods: {      
           getList($filter){       
                this.is_loading = true;       	 
                axios({
               method: 'put',
               url: this.ajax_url+"/orderList",
               data : {
                     'order_status' : this.order_status,
                     'schedule': this.schedule,
                 'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                 'filter' : $filter
                  },
               timeout: $timeout,
             }).then( response => {	 
                  this.response_code = response.data.code;
                 if(response.data.code==1){
                     this.data = response.data.details.data;		     	
                     this.total = response.data.details.total;
                     this.meta = response.data.details.meta;
                     this.status = response.data.details.status;
                     this.services = response.data.details.services;		     	
                     this.order_uuid = response.data.details.data[0].order_uuid;				     	
                     this.order_type = response.data.details.data[0].service_code;
                     this.$emit('afterSelect',this.order_uuid , this.order_type );		     	
                 } else {		   
                     this.error = response.data.msg;
                     this.data = [];
                     this.meta = [];
                     this.status = [];
                     this.services = [];
                     this.total = 0;
                     this.$emit('afterSelect','');
                 }
                 
                 var $total = new countUp.CountUp(this.$refs.total, this.total , {							
                    decimalPlaces : 0,
                    separator : ",",
                    decimal : "."
                 });						
                 $total.start();	 	
                 
             }).catch(error => {	
                //dump(error);
             }).then(data => {			     
                 this.updateScroll();
                 this.is_loading = false;
             });
           },
           updateScroll(){
                setTimeout(function(){ 		
                $(".nice-scroll").getNiceScroll().resize(); 	                
              }, 100);
           },
           select(data){
                this.order_uuid = data.order_uuid;
                this.order_type = data.service_code;
                data.is_view = 1;
                this.$emit('afterSelect',data.order_uuid, this.order_type );
           },
        },
        template:`			
        
        <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
        </div>
        
        <div class="make-sticky d-flex align-items-center justify-content-between bg-white">
            <div><h5 class="head m-0">{{label.title}}</h5></div>
            <div>
              <div ref="total" class="ronded-green">0</div>
            </div>
        </div> 
        
        <template v-if="response_code==1">
        <ul class="list-unstyled m-0 grey-list-chevron">
        <li v-for="(item, index) in data" class="chevron" 
        :class="{selected:item.order_uuid == order_uuid}" @click="select(item)" >
        
        <div class="row align-items-start">      
          <div class="col">
            <div class="d-flex justify-content-between align-items-center">
             <div><p class="m-0" v-if="meta[item.order_id]"><b>{{meta[item.order_id].customer_name}}</b></p></div>
             <div>
             <span v-if="status[item.status]" class="ml-2 badge" 
              :style="{background:status[item.status].background_color_hex,color:status[item.status].font_color_hex}"
              >          
              {{status[item.status].status}} 
             </span>  
             <span v-else class="ml-2 badge badge-info"  >
               {{item.status}}
             </span>
             </div>
            </div>
            <p class="m-0">{{item.total_items}}
            
             <span v-if="services[item.service_code]" class="ml-2 badge services" 
             :style="{background:services[item.service_code].background_color_hex,color:services[item.service_code].font_color_hex}"
              >          
              {{services[item.service_code].service_name}} 
             </span>      
             <span v-else class="ml-2 badge badge-info"  >
               {{item.service_code}}
             </span>
            
            </p>
            
            <div class="d-flex align-items-center">
              <div v-if="item.is_view==0" class="mr-1"><div class="blob green"></div></div>
              <div><p class="m-0">{{item.order_name}}</p></div>
            </div>
            
            <div class="d-flex align-items-center">
              <template v-if="show_critical">
              <div v-if="item.is_critical==1" class="mr-1"><div class="blob red"></div></div>
              </template>
              <div><p class="m-0"><u>{{item.delivery_date}}</u></p></div>
            </div>        
          </div> <!--col-->
        </div>
        <hr class="m-0">
       </li>    
       </ul>
       </template>
       <template v-else>
        <div class="fixed-height40 text-center justify-content-center d-flex align-items-center">
        
        <div v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
            <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
         </div>      
        
        </div>
       </template>
        `
    };
    
    
    /*
      COMPONENTS REJECTION FORM
    */
    const ComponentsRejectionForms = {
        props: ['ajax_url','label','order_uuid'],
        data(){
            return {						
                data : [],
                reason : '',	
                resolvePromise: undefined,
                rejectPromise: undefined,		
                //order_uuid : ''		
            };
        },
        computed: {
            hasData(){			
                if(!empty(this.reason)){
                   return true;
                } 
                return false;
            },
        },
        mounted() {
           this.orderRejectionList();	   
           autosize( this.$refs.reason );
        },
        methods :{
            confirm(){
                $( this.$refs.rejection_modal ).modal('show');
                return new Promise((resolve, reject) => {
                    this.resolvePromise = resolve
                    this.rejectPromise = reject
                });
            },
            close(){
                $( this.$refs.rejection_modal ).modal('hide');
            },
            orderRejectionList(){
                axios({
                   method: 'put',
                   url: this.ajax_url+"/orderRejectionList",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                      },
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
                     
                 });		
            },
            submit(){			
                this.close();	
                this.resolvePromise(this.reason); 		
                //this.$emit('afterSubmit',this.reason);			
            },
        },
        template: `	
        <div ref="rejection_modal" class="modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
                
          <form @submit.prevent="submit" >
            <div class="form-label-group mt-2"> 
            <textarea ref="reason" v-model="reason" id="reason" class="form-control form-control-text" :placeholder="label.reason">
            </textarea>
            </div>
                            
            <div class="list-group list-group-flush">
             <a v-for="item in data" @click="reason=item" 
             :class="{active:reason==item}"
             class="text-center list-group-item list-group-item-action">
             {{item}}
             </a>
            </div>
            
          </form>
          
          </div>      
          <div class="modal-footer">            
            <button type="button" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
             :disabled="!hasData"
             >
              <span>{{label.reject_order}}</span>
              <div class="m-auto circle-loader" data-loader="circle-side"></div> 
            </button>
          </div>
          
        </div>
      </div>
    </div>            
        `
    };
    
    const ComponentsRefund = {
        props: ['ajax_url','label','order_uuid'],
        data(){
            return {						
                amount : 0,
                refund_type : 'full',
                resolvePromise: undefined,
                rejectPromise: undefined,
                is_loading : false,		
                data : []			
            };
        },
        methods :{
            confirm($data){
                this.data = $data;
                this.refund_type = $data.refund_type;
                $( this.$refs.refund_modal ).modal('show');
                return new Promise((resolve, reject) => {
                    this.resolvePromise = resolve
                    this.rejectPromise = reject
                });
            },
            close(){
                $( this.$refs.refund_modal ).modal('hide');
            },
            submit(){			
                this.close();	
                this.resolvePromise(true); 			
            },
        },
        template: `	
        
        <div ref="refund_modal" class="modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <div class="modal-body">                
            <p>{{label.refund_full}} {{data.pretty_total}}</p>        
          </div> <!-- body -->
          
           <div class="modal-footer">
              <button type="buttton" class="btn btn-black" data-dismiss="modal" aria-label="Close" >
              <span class="pl-2 pr-2" >{{label.cancel}}</span>
              </button>
              <button type="button" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"           
              >
              <span>{{label.refund}}</span>
              <div class="m-auto circle-loader" data-loader="circle-side"></div> 
            </button>
          </div>
          
          </div>
         </div>
         </div>      
        `
    };
    
    
    /*
      COMPONENTS ORDER INFORMATION
    */
    const ComponentsOrderInfo = {
        props: ['ajax_url','group_name','refund_label','remove_item','out_stock_label',
        'manual_status','modify_order','update_order_label','filter_buttons','enabled_delay_order'],
        components: {
           'components-rejection-forms': ComponentsRejectionForms, 	
           'components-refund-forms': ComponentsRefund, 
        },	 
        data(){
          return {			
               is_loading : false,	
               loading : true,		
             order_uuid : '',
             merchant :[],
             order_info : [],
             items :[],
             order_summary : [],
             summary_changes : [],
             summary_transaction : [],
             summary_total : 0,
             merchant_direction :'',
             delivery_direction :'',
             order_status : [],
             services : [],
             payment_status :[],
             response_code : 0,
             customer : [],
             buttons : [],
             status_data : [],
             stats_id : '',
             sold_out_options : [],
             out_stock_options : '',
             item_row : [],
             additional_charge : 0,
             additional_charge_name : '',
             customer_name : '',
             contact_number : '',
             delivery_address : '',
             latitude : '',
             longitude : '',	
             error : [],
             link_pdf : [],		
             payment_history : [],
             screen_size: {
                width: 0,
                height: 0
            },
            show_as_popup : false,
            load_count : 0,
          };
        },
        computed: {
            hasData(){			
                if(this.stats_id>0){
                   return true;
                } 
                return false;
            },
            outStockOptions(){
                if(this.out_stock_options>0){
                   return true;
                } 
                return false;
            },
            hasValidCharge(){
                if(this.additional_charge>0){
                   return true;
                } 
                return false;
            },
            refundAvailable(){
                if(this.order_info.payment_status=="paid"){
                    return true;
                }
                return false;
            },
            hasRefund(){
                if(this.summary_changes){		
                    if(this.summary_changes.method==='total_decrease'){		
                    if(this.summary_changes.refund_due>0){
                        return true;
                    }				
                    }
                }
                return false;
            },
            hasAmountToCollect(){
                if(this.summary_changes){		
                    if(this.summary_changes.method==='total_increase'){		
                    if(this.summary_changes.refund_due>0){
                        return true;
                    }				
                    }
                }
                return false;
            },
            hasTotalDecrease(){
                if(this.summary_changes){
                    if(this.summary_changes.method==='total_decrease'){
                        return true;
                    }				
                }
                return false;
            },
            hasTotalIncrease(){
                if(this.summary_changes){
                    if(this.summary_changes.method==='total_increase'){
                        return true;
                    }				
                }
                return false;
            },
            summaryTransaction(){
                if(this.summary_transaction){
                    if ((typeof  this.summary_transaction.summary_list !== "undefined") && ( this.summary_transaction.summary_list !== null)) {
                    if(this.summary_transaction.summary_list.length>0){
                        return true;
                    }
                    }
                }
                return false;
            },
            hasInvoiceUnpaid(){
                if(this.summary_changes.unpaid_invoice){
                    return true;
                } 
                if(this.summary_changes.paid_invoice){
                    return true;
                }
                return false;
            },		
        },
        watch: {
            load_count(newvalue,oldvalue){
                if ((typeof  vm_order_management !== "undefined") && ( vm_order_management !== null)) {
                    if(newvalue>=2){				
                        if( this.screen_size.width<=576){
                            vm_order_management.show_as_popup = true;
                        } else { vm_order_management.show_as_popup = false; }
                    }  else { vm_order_management.show_as_popup = false; }
                }
            },		
        },
        mounted() {
           this.getOrderStatusList();	   
           this.handleResize();
           window.addEventListener('resize', this.handleResize);
        },
        methods: {
            handleResize() {
                this.screen_size.width = window.innerWidth;
                this.screen_size.height = window.innerHeight;			
            },
            orderDetails(order_uuid , $update_summary ){
                this.order_uuid = order_uuid;
                this.is_loading = true;
                this.loading = true;
                
                var $payload = ['payment_history','print_settings','buttons'];
                            
                axios({
                   method: 'put',
                   url: this.ajax_url+"/orderDetails",
                   data : {
                         'order_uuid' : this.order_uuid,
                         'group_name' : this.group_name,	       	 	 
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                     'payload' : $payload,
                     'modify_order': this.modify_order,
                     'filter_buttons' : this.filter_buttons,
                      },
                   timeout: $timeout,
                 }).then( response => {	 	
    
                    this.load_count++;
                     this.response_code = response.data.code;		     
                     if(response.data.code==1){
                         
                         this.merchant = response.data.details.data.merchant;
                         this.order_info = response.data.details.data.order.order_info;
                         
                         this.customer_name = this.order_info.customer_name;
                         this.contact_number = this.order_info.contact_number;
                         this.delivery_address = this.order_info.delivery_address;
                         this.latitude = this.order_info.latitude;
                         this.longitude = this.order_info.longitude;
                         
                         this.customer = response.data.details.data.customer;	
                         
                         if ((typeof  vm_order_management !== "undefined") && ( vm_order_management !== null)) {		 		
                            vm_order_management.client_id = this.customer.client_id;
                         }
                         
                         this.order_status = response.data.details.data.order.status;
                         this.services = response.data.details.data.order.services;
                         this.payment_status = response.data.details.data.order.payment_status;
                         
                         this.items = response.data.details.data.items;
                         this.order_summary = response.data.details.data.summary;
                         this.summary_total = response.data.details.data.summary_total;
                         this.summary_changes = response.data.details.data.summary_changes;
                         this.summary_transaction = response.data.details.data.summary_transaction;
                         
                         this.merchant_direction = 'https://www.google.com/maps/dir/?api=1&destination=';
                         this.merchant_direction+=this.merchant.latitude +",";
                         this.merchant_direction+=this.merchant.longitude;
                         
                         // this.delivery_direction = 'https://www.google.com/maps/dir/?api=1&destination=';
                         // this.delivery_direction+=this.order_info.latitude +",";
                         // this.delivery_direction+=this.order_info.longitude;
                        this.delivery_direction = this.order_info.delivery_direction;
                         
                         this.buttons = response.data.details.data.buttons;
                         this.sold_out_options = response.data.details.data.sold_out_options;
                         this.link_pdf = response.data.details.data.link_pdf;
                         this.payment_history = response.data.details.data.payment_history;
                         
                     } else {
                         this.merchant_direction = '';
                         this.delivery_direction = '';
                         
                         this.merchant = [];
                         this.order_info = [];
                         this.items = []
                         this.order_summary = [];
                         this.buttons = [];
                         this.sold_out_options = [];
                         this.link_pdf = [];
                         this.payment_history = [];
                     }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                     this.loading = false;
                 });
                
            },
            doUpdateOrderStatus( $uuid, $order_uuid, $do_actions){    		
                dump($do_actions);
                console.log(this.summary_changes.method);
                //return ;
                if($do_actions=="reject_form"){     			    		
                    this.$refs.rejection.confirm().then((result) => {		  
                         if(result){			   		   			   		  
                            dump("rejection reason =>" + result);
                            this.updateOrderStatus($uuid,$order_uuid,result);
                         }
                       });    			
                } else {    			    	 
                    var $content ='';
                    if( this.summary_changes.method=="total_decrease" ){
                        var $content = this.update_order_label.content;
                        $content = $content.replace("{{amount}}", this.summary_changes.refund_due_pretty );
                        
                        bootbox.confirm({ 
                            size: "small",
                            title : "" ,
                            message: '<h5>'+this.update_order_label.title+'</h5>' + '<p>'+ $content +'</p>',
                            centerVertical: true,
                            animate: false,
                            buttons: {
                                cancel: {
                                   label: this.update_order_label.cancel,
                                   className: 'btn btn-black small pl-4 pr-4'
                                },
                                confirm: {
                                    label: this.update_order_label.confirm,
                                    className: 'btn btn-green small pl-4 pr-4'
                                },
                            },
                            callback: result => {				    	
                                if(result){
                                    this.createRefund($uuid,$order_uuid);
                                }
                            }
                        });				
                        
                    } else if ( this.summary_changes.method=="total_increase" ){
                        var $content = this.update_order_label.content_collect;
                        $content = $content.replace("{{amount}}", this.summary_changes.refund_due_pretty );
    
                        if(this.summary_changes.unpaid_invoice) {
                                                
                            var dialog = bootbox.dialog({
                                title: '',
                                message: '<h5>'+this.update_order_label.title_increase+'</h5>' + '<p>'+ this.update_order_label.content_payment +'</p>',
                                size: 'medium',
                                centerVertical: true,
                                buttons: {
                                    cancel: {
                                        label: this.update_order_label.close,
                                        className: 'btn btn-black small pl-4 pr-4',
                                        callback: ()=>{
                                            //console.log('Custom cancel clicked');
                                        }
                                    },						       
                                }
                            });
                            
                        } else if ( this.summary_changes.paid_invoice ){
                            this.updateOrderStatus($uuid,$order_uuid);
                        } else {
                                          
                            var dialog = bootbox.dialog({
                                title: '',
                                message: '<h5>'+this.update_order_label.title_increase+'</h5>' + '<p>'+ $content +'</p>',
                                size: 'medium',
                                centerVertical: true,
                                buttons: {
                                    cancel: {
                                        label: this.update_order_label.cancel,
                                        className: 'btn btn-black small pl-4 pr-4',
                                        callback: ()=>{
                                            //console.log('Custom cancel clicked');
                                        }
                                    },
                                    account: {
                                        label: this.update_order_label.less_acccount,
                                        className: 'btn btn-yellow small',
                                        callback: ()=>{
                                            this.AcceptOrder($uuid,$order_uuid,'lessOnAccount');			                
                                        }
                                    },
                                    ok: {
                                        label: this.update_order_label.send_invoice,
                                        className: 'btn btn-green small pl-4 pr-4',
                                        callback: ()=>{
                                            this.AcceptOrder($uuid,$order_uuid,'createInvoice');
                                        }
                                    }
                                }
                            });
                        
                        }
                    
                    } else if ( this.summary_changes.method=="less_on_account" ){    					
                        var $content = this.update_order_label.collect_cash;    			    
                        $content = $content.replace("{{amount}}", MoneyFormat(this.order_info.commission) );    			     
                        
                        var dialog = bootbox.dialog({
                            title: '',
                            message: '<h5>Accept Order</h5>' + '<p>'+ $content +'</p>',
                            size: 'medium',
                            centerVertical: true,
                            buttons: {
                                cancel: {
                                    label: this.update_order_label.cancel,
                                    className: 'btn btn-black small pl-4 pr-4',
                                    callback: ()=>{
                                        
                                    }
                                },					     
                                ok: {
                                    label: this.update_order_label.less_acccount,
                                    className: 'btn btn-green small pl-4 pr-4',
                                    callback: ()=>{
                                        this.AcceptOrder($uuid,$order_uuid,'lessCashOnAccount');
                                    }
                                }
                            }
                        });
                        
                    } else {
                        this.updateOrderStatus($uuid,$order_uuid);    				
                    }    			
                }
            },
            updateOrderStatus($uuid, $order_uuid , $reason){   	    		    		
                this.is_loading = true;   	    		
                axios({
                   method: 'put',
                   url: this.ajax_url+"/updateOrderStatus",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'uuid' : $uuid,			
                        'order_uuid' : $order_uuid,	
                        'reason': $reason,		     
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.$emit('afterUpdate');
                      } else {			 	 	
                          //vm_bootbox.alert( response.data.msg , {size:'small'} );
                          notify( response.data.msg , 'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			 	    		
            },
            createRefund($uuid,$order_uuid){
                
                this.is_loading = true;   	    		
                axios({
                   method: 'put',
                   url: this.ajax_url+"/createRefund",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'uuid' : $uuid,
                        'order_uuid' : $order_uuid,			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.$emit('afterUpdate');
                      } else {			 	 				 	 	
                          notify( response.data.msg , 'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			 	   
                
            },
            AcceptOrder($uuid,$order_uuid,$action){
                
                this.is_loading = true;   	    		
                axios({
                   method: 'put',
                   url: this.ajax_url+"/"+ $action ,
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'uuid' : $uuid,
                        'order_uuid' : $order_uuid,			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.$emit('afterUpdate');
                      } else {			 	 			 	 	
                          notify( response.data.msg , 'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			 	   
                
            },
            getOrderStatusList(){
                
                axios({
                   method: 'put',
                   url: this.ajax_url+"/getOrderStatusList",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.status_data = response.data.details;
                      } else {			 	 	
                          this.status_data = false;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			 	   
                             
            },
            manualStatusList($order_uuid){   
                this.stats_id = ''; 			
                $( this.$refs.manual_status_modal ).modal('show');
            },
            confirm(){
                this.is_loading = true;
                axios({
                   method: 'put',
                   url: this.ajax_url+"/updateOrderStatusManual",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                        'order_uuid' : this.order_uuid,
                        'stats_id' : this.stats_id,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          $( this.$refs.manual_status_modal ).modal('hide');
                          this.$emit('afterUpdate');
                      } else {			 	 	
                          //vm_bootbox.alert( response.data.msg , {size:'small'} );
                          notify( response.data.msg , 'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });	
            },
            cancelOrder(){    		
                //this.$emit('rejectionOrderform' , this.order_uuid );    		
                //this.$refs.rejection.confirm();
                 this.$refs.rejection.confirm().then((result) => {		  
                       if(result){			   		   			   		  
                           
                           this.is_loading = true;
                        axios({
                           method: 'put',
                           url: this.ajax_url+"/cancelOrder",
                           data : {
                                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	
                                'order_uuid' : this.order_uuid,
                                'reason' : result,
                              },
                           timeout: $timeout,
                         }).then( response => {	 
                              if(response.data.code==1){					 	 	
                                  this.$emit('afterUpdate');					 	 	
                                  notify(response.data.msg);
                              } else {			 	 						 	 	
                                  notify(response.data.msg,'error');
                              }
                         }).catch(error => {	
                            //
                         }).then(data => {			     
                             this.is_loading = false;
                         });	
                           
                       }
                    });
            },    	
            delayOrder(){        		    	
                this.$emit('delayOrderform' , this.order_uuid );
            },
            contactCustomer(){    		    		
                //document.location.href = "tel:"+this.order_info.contact_number;
                vm_bootbox.alert( this.order_info.contact_number , {size:'small'} );
            },
            orderHistory(){
                //vm_order_management.orderHistory(this.order_uuid);
                this.$emit('order-history' , this.order_uuid );
            },
            markItemOutStock($items){        	    	    
                this.item_row = $items;
                $( this.$refs.out_stock_modal ).modal('show');
            },
            setOutOfStocks(){    	    		    			
                
                $( this.$refs.out_stock_modal ).modal('hide');
                
                bootbox.confirm({ 
                    size: "medium",
                    title : "" ,
                    message: '<h5>'+this.out_stock_label.title+'</h5>' + '<p>'+ this.refund_label.content +'</p>',
                    centerVertical: true,
                    animate: false,
                    buttons: {
                        cancel: {
                           label: this.refund_label.go_back,
                           className: 'btn btn-black small pl-4 pr-4'
                        },
                        confirm: {
                            label: this.refund_label.complete,
                            className: 'btn btn-green small pl-4 pr-4'
                        },
                    },
                    callback: result => {				    	
                        if(result){
                            this.itemChanges('out_stock');
                        } else {
                            $( this.$refs.out_stock_modal ).modal('show');
                        }
                    }
                });	
                
            },
            itemChanges($item_changes){
                        
                this.is_loading = true;
                axios({
                   method: 'put',
                   url: this.ajax_url+"/itemChanges",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'order_uuid' : this.order_uuid,
                        'item_row' : this.item_row.item_row,
                        'item_changes': $item_changes,
                        'out_stock_options': this.out_stock_options,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          switch($item_changes){
                              default:
                                $( this.$refs.out_stock_modal ).modal('hide');
                                this.orderDetails( this.order_uuid , true);
                              break;
                          }			 	 	
                      } else {			 	 	
                          //vm_bootbox.alert( response.data.msg , {size:'small'} );
                          notify( response.data.msg , 'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		 
                
            },
            adjustOrder($items){
                this.item_row = $items;    	
                $( this.$refs.adjust_order_modal ).modal('show');	
            },
            refundItem(){
                $( this.$refs.adjust_order_modal ).modal('hide');    	
                bootbox.confirm({ 
                    size: "medium",
                    title : "" ,
                    message: '<h5>'+this.refund_label.title+'</h5>' + '<p>'+ this.refund_label.content +'</p>',
                    centerVertical: true,
                    animate: false,
                    buttons: {
                        cancel: {
                           label: this.refund_label.go_back,
                           className: 'btn btn-black small pl-4 pr-4'
                        },
                        confirm: {
                            label: this.refund_label.complete,
                            className: 'btn btn-green small pl-4 pr-4'
                        },
                    },
                    callback: result => {				    	
                        if(result){
                            this.doItemRefund();
                        } else {
                            $( this.$refs.adjust_order_modal ).modal('show');    	
                        }
                    }
                });	
            },
            doItemRefund(){
                this.itemChanges('refund');    	
            },
            cancelEntireOrder(){
                $( this.$refs.adjust_order_modal ).modal('hide');	
                this.cancelOrder();
            },
            removeItem(){
                
                $( this.$refs.adjust_order_modal ).modal('hide');
                bootbox.confirm({ 
                    size: "medium",
                    title : "" ,
                    message: '<h5>'+this.remove_item.title+'</h5>' + '<p>'+ this.remove_item.content +'</p>',
                    centerVertical: true,
                    animate: false,
                    buttons: {
                        cancel: {
                           label: this.remove_item.go_back,
                           className: 'btn btn-black small pl-4 pr-4'
                        },
                        confirm: {
                            label: this.remove_item.confirm,
                            className: 'btn btn-green small pl-4 pr-4'
                        },
                    },
                    callback: result => {				    	
                        if(result){
                            this.doRemoveItem();
                        } else {
                            $( this.$refs.adjust_order_modal ).modal('show');    	
                        }
                    }
                });	
                
            },
            doRemoveItem(){
                 this.itemChanges('remove');
            },
            additionalCharge($items){
                this.item_row = $items;    	
                $( this.$refs.additional_charge_modal ).modal('show');	
            },    	
            doAdditionalCharge(){
                
                this.is_loading = true;
                axios({
                   method: 'put',
                   url: this.ajax_url+"/additionalCharge",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'order_uuid' : this.order_uuid,
                        'item_row' : this.item_row.item_row,			   	 
                        'additional_charge': this.additional_charge,
                        'additional_charge_name': this.additional_charge_name,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          $( this.$refs.additional_charge_modal ).modal('hide');				 	 	
                          this.orderDetails( this.order_uuid , true );
                      } else {			 	 	
                          //vm_bootbox.alert( response.data.msg , {size:'small'} );
                          notify( response.data.msg , 'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		 
                 
            },
            updateOrderSummary(){
                            
                axios({
                   method: 'put',
                   url: this.ajax_url+"/updateOrderSummary",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'order_uuid' : this.order_uuid,			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                      } else {			 	 				 	 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });	
                
            },
            replaceItem(){    		
                $( this.$refs.adjust_order_modal ).modal('hide');	
                this.$emit('showMenu',this.item_row);	
            },
            editOrderInformation(){    		
                $( this.$refs.update_info_modal ).modal('show');
            },
            updateOrderDeliveryInformation(){
                
                this.is_loading = true; this.error = [];
                axios({
                   method: 'put',
                   url: this.ajax_url+"/updateOrderDeliveryInformation",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'order_uuid' : this.order_uuid,
                        'customer_name' : this.customer_name,
                        'contact_number' : this.contact_number,
                        'delivery_address' : this.delivery_address,
                        'latitude' : this.latitude,
                        'longitude' : this.longitude,
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){		
                          notify(response.data.msg);			 	 	
                          $( this.$refs.update_info_modal ).modal('hide');		 	 	
                          this.$emit('refreshOrder', this.order_uuid);	 	    		
                      } else {			 	 	
                          this.error = response.data.details;	 	 				 	 
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });	
                 
            },
            showCustomer(){    	   
               this.$emit('viewCustomer');
            },
            printOrder(){
                this.$emit('to-print' , this.order_uuid );
            },
            refundFull(){
                 var $data = {
                     refund_type : 'full',
                     order_uuid : this.order_info.order_uuid,
                     total : this.order_info.total,
                     pretty_total : this.order_info.pretty_total,
                 };  	    	
                 this.$refs.refund.confirm($data).then((result) => {	
                      dump(result);
                 });
            },
            refundPartial(){
                dump('refundPartial');
            },
            updateOrder(){    		
                dump(this.summary_changes.method);
                if(this.summary_changes.method=="total_decrease"){
                    var $content = this.update_order_label.content;
                    $content = $content.replace("{{amount}}", this.summary_changes.refund_due_pretty );
                    bootbox.confirm({ 
                        size: "small",
                        title : "" ,
                        message: '<h5>'+this.update_order_label.title+'</h5>' + '<p>'+ $content +'</p>',
                        centerVertical: true,
                        animate: false,
                        buttons: {
                            cancel: {
                               label: this.update_order_label.cancel,
                               className: 'btn btn-black small pl-4 pr-4'
                            },
                            confirm: {
                                label: this.update_order_label.confirm,
                                className: 'btn btn-green small pl-4 pr-4'
                            },
                        },
                        callback: result => {				    	
                            if(result){
                                dump(result);
                            }
                        }
                    });				
                }
            },
        },
        template: '#xtemplate_order_details',
    };
    
    
    /*
      COMPONENTS DELAY FORM
    */
    const ComponentsDelayForm = {
        props: ['ajax_url','label','order_uuid'],
        data(){
            return {	
               data : [],
               is_loading : false,	
               time_delay : '',		 
               //order_uuid : ''
            };
        },
        mounted(){
            this.getDelayedMinutes();
        },
        computed: {
            hasData(){			
                if(!empty(this.time_delay)){
                   return true;
                } 
                return false;
            },
        },
        methods :{
            show(){
                this.time_delay = '';		
                $( this.$refs.delay_modal ).modal('show');
            },
            close(){
                $( this.$refs.delay_modal ).modal('hide');
            },
            getDelayedMinutes(){
                axios({
                   method: 'put',
                   url: this.ajax_url+"/getDelayedMinutes",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                      },
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
                     
                 });		
            },
            confirm(){
                /*this.close();			
                this.$emit('afterConfirm',this.time_delay);	*/
                
                this.is_loading = true;
                axios({
                   method: 'put',
                   url: this.ajax_url+"/setDelayToOrder",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'time_delay' : this.time_delay,
                        'order_uuid' : this.order_uuid
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          //this.$emit('afterUpdate');
                          //vm_bootbox.alert( response.data.msg , {size:'small'} );
                          this.close();
                          notify(response.data.msg);
                      } else {			 	 	
                          //vm_bootbox.alert( response.data.msg , {size:'small'} );
                          notify(response.data.msg,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		 
                
            },
        },
        template: `	
        <div ref="delay_modal" class="modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          
           <p class="m-0">{{label.sub1}}</p>
           <p class="m-0">{{label.sub2}}</p>
                         
           <div class="w-75 m-auto">
           <div class="row mt-4">
             <div v-for="item in data" class="col-lg-4 col-md-4 col-sm-6 col-4  mb-2">
               <button 
               :class="{active:time_delay==item.id}" 
               @click="time_delay=item.id"
               class="btn btn-light delay-btn">
               {{item.value}}
               </button>
             </div>
           </div>
           </div>
          
           </div>      
          <div class="modal-footer">            
            <button type="button" @click="confirm" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
             :disabled="!hasData"
             >
              <span>{{label.confirm}}</span>
              <div class="m-auto circle-loader" data-loader="circle-side"></div> 
            </button>
          </div>
          
        </div>
      </div>
    </div>        
        `
    };
    
    /*
      ORDER HISTORY
    */
    const ComponentsOrderHistory = {
        props: ['ajax_url','label','order_uuid'],
        data(){
            return {			   
               is_loading : false,	
               //order_uuid : '',
               data : [],
               order_status : [],
               error : []
            };
        },
        methods :{
            show(){			
                this.data = []; this.order_status = [];
                $( this.$refs.history_modal ).modal('show');
                this.getHistory();
            },
            close(){
                $( this.$refs.history_modal ).modal('hide');
            },
            getHistory(){
                this.is_loading = true;
                axios({
                   method: 'put',
                   url: this.ajax_url+"/getOrderHistory",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'order_uuid' : this.order_uuid,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.data = response.data.details.data;
                          this.order_status = response.data.details.order_status;
                          this.error = [];
                      } else {
                          this.error = response.data.msg;
                          this.data = [];
                          this.order_status = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });
            },
        },
        template: `	
        <div ref="history_modal" class="modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body position-relative">
          
          <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
          </div>
          
          <ul class="timeline m-0 p-0 pl-5">
            <li  v-for="item in data" >
              <div class="time">{{item.created_at}}</div>
               <p v-if="order_status[item.status]" class="m-0">{{order_status[item.status]}}</p>
               <p v-else class="m-0">{{item.status}}</p>
               <p class="m-0 text-muted">{{item.remarks}}</p>
               <p v-if="item.change_by" class="m-0 text-muted">{{item.change_by}}</p>
            </li>        
          </ul>
          
          <div id="error_message" v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
            <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
          </div>  
          
          </div>      
          <div class="modal-footer">            
            <button type="button" class="btn btn-green pl-4 pr-4"  data-dismiss="modal">
              <span>{{label.close}}</span>          
            </button>
          </div>
          
        </div>
      </div>
    </div>        
        `
    };
    
    /*
      COMPONENTS MENU
    */
    const ComponentsMenu = {
        props: ['ajax_url','label','image_placeholder','merchant_id','responsive' ],
        data(){
            return {			   
               is_loading : false,
               category_list : [],
               active_category : 'all',
               item_list : [],	
               observer : undefined,
               total_results : '', 
               current_page : 0,
               page_count : 0,
               page : 0,
               awaitingSearch : false,
               q : '',
               owl : undefined,
               replace_item : []
            };
        },
        mounted(){
            this.getCategory();
            
            setTimeout(() => {						
               this.categoryItem(0);
            }, 500); 		 	
            
            this.observer = lozad('.lozad',{
                 loaded: function(el) {
                     el.classList.add('loaded');
                 }
            });    	
        },
        updated () {		
            this.observer.observe();
        },
        watch: {
            q(newsearch,oldsearch){
                if (!this.awaitingSearch) {
                            
                    if(empty(newsearch)){
                         return false;
                    }
                    
                    setTimeout(() => {
                        
                        axios({
                           method: 'put',
                           url: this.ajax_url+"/categoryItem",
                           data : {
                                'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                                'merchant_id' : this.merchant_id,
                                'cat_id': 0,
                                'page' : 0,
                                'q': this.q
                              },
                           timeout: $timeout,
                         }).then( response => {	 
                              if(response.data.code==1){					 	 	
                                  this.item_list = response.data.details.data;
                                  this.total_results = response.data.details.total_records;
                                  this.current_page = response.data.details.current_page;
                                  this.page_count = response.data.details.page_count;
                              } else {			 	 	
                                  this.item_list = [];
                                  this.current_page = 0;
                                  this.page_count = 0
                                  this.total_results = response.data.msg;
                              }
                         }).catch(error => {	
                            //
                         }).then(data => {			     					    
                             this.awaitingSearch = false;
                         });
                        
                    }, 1000);
                }
                
                this.item_list = [];
                this.awaitingSearch = true;
            }
        },
        methods :{
            show(){							
                this.q = '';
                $( this.$refs.menu_modal ).modal('show');					    
            },
            close(){							
                $( this.$refs.menu_modal ).modal('hide');					    
            },
            getCategory(){
                
                axios({
                   method: 'put',
                   url: this.ajax_url+"/getCategory",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		
                        'merchant_id' : this.merchant_id
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.category_list = response.data.details.data.category;
                      } else {			 	 	
                          this.category_list = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {				 				 	
                     
                 });
                
            },		
            pageNext(){
                     this.page = parseInt(this.page)+1;   	  	 
                     if(this.page>=this.page_count){  	  	 	
                         this.page = this.page_count;  	  	 	
                     }  	  	 
                     this.categoryItem( this.active_category );
              },
              pagePrev(){
                     this.page = parseInt(this.page)-1;   	  	  	 
                     if(this.page<=0){  	  	 	
                         this.page = 0;  	  	 	
                     } 
                     this.categoryItem( this.active_category );
              },
            pageWithID(page){
                this.page = page;
                this.categoryItem( this.active_category );
            },
            categoryItem($cat_id){			
                this.active_category = $cat_id;
                this.is_loading = true;
                axios({
                   method: 'put',
                   url: this.ajax_url+"/categoryItem",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'merchant_id' : this.merchant_id,
                        'cat_id':$cat_id,
                        'page' : this.page,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          this.item_list = response.data.details.data;
                          this.total_results = response.data.details.total_records;
                          this.current_page = response.data.details.current_page;
                          this.page_count = response.data.details.page_count;
                      } else {			 	 	
                          this.item_list = [];
                          this.current_page = 0;
                          this.page_count = 0
                          this.total_results = response.data.msg;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                     this.RenderCarousel();
                 });
            },
            itemShow($items){			
                var $_params = {
                    'merchant_id' : this.merchant_id,			
                    'item_uuid' : $items.item_uuid,
                    'category_id' : $items.category_id[0],
                    'replace_item': this.replace_item
                };		
                this.close();	
                this.$emit('showItem',$_params);	
            },
            RenderCarousel(){			
                this.owl = $( this.$refs.carousel ).owlCarousel({				
                    nav:true,
                    dots:false,
                    responsive : this.responsive
                    /*responsive:{
                        0:{
                            items:1,
                            nav:true
                        },
                        600:{
                            items:3,
                            nav:false
                        },
                        1000:{
                            items:8,
                            nav:true,
                            loop:false
                        },
                        1200:{
                            items:parseInt($items),
                            nav:true,
                            loop:false
                        }
                    }			*/
                });
            },
        },
        template: '#xtemplate_menu',
    };
    
    /*
      COMPONENTS ITEM POPUP
    */
    var $global_items = [];
    var $global_addons = [];
    var $global_addon_items = [];
    var $global_item_addons = [];
    var $new_item_addons
    var $item_total = 0;
    
    const ComponentsItem = {	
        components: {
              'money-format': ComponentsMoney,
        },
        props: ['ajax_url','label','image_placeholder','merchant_id','order_type','order_uuid'],
        data(){
            return {			
                is_loading : false,				   			
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
                   observer : undefined,    			
                   old_item : []
            };	
        },		
        mounted(){		
            $( this.$refs.modal_item_details ).on('hide.bs.modal', data => { 			
                this.$emit('goBack',this.old_item);	
            });
            
            this.observer = lozad('.lozad',{
                 loaded: function(el) {
                     dump("image loaded");
                     el.classList.add('loaded');
                 }
            });    	
        },
        updated () {
            if(this.item_addons_load==true){
                this.ItemSummary();  
            }						
        },
        methods :{
            show($data){					
                $( this.$refs.modal_item_details ).modal('show');		    
                this.old_item = $data.replace_item;
                this.viewItem($data);
            },
            close(){
                this.items = [];
                this.item_addons = [];
                this.meta = [];
                $( this.$refs.modal_item_details ).modal('hide');			
                this.$emit('goBack',this.old_item);
            },
            viewItem(data){			
                var $params = {
                  'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  'merchant_id': this.merchant_id,
                  'item_uuid' : data.item_uuid,
                  'cat_id' : data.category_id
                };			
                var timenow = 1;
                $params = JSON.stringify($params);				
                             
                ajax_request[timenow] = $.ajax({
                     url: this.ajax_url+"/getMenuItem",
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
                                                            
                         //$( this.$refs.modal_item_details ).modal('show');		 	    	
                               
                     } /*end if*/
                     
                 }).always( data => {		 	    	
                     this.is_loading = false;	 	 	    	
                     this.observer.observe();
                });	   		 	 
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
                                    if( !empty($global_addon_items[item2]) )  {
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
                   'order_uuid':this.order_uuid,
                   'transaction_type': this.order_type ,
                   'if_sold_out' : this.if_sold_out,			   
                };		
                if(!empty(this.old_item)){
                    $params.old_item_token = this.old_item.item_token;
                    $params.item_row = this.old_item.item_row;
                }			
                                        
                var timenow = 1;
                $params = JSON.stringify($params);
                            
                ajax_request[timenow] = $.ajax({
                     url: this.ajax_url+"/addCartItems",
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
                         this.$emit('refreshOrder', this.order_uuid);	 	    			 	    		
                         notify(data.msg,"success");
                         
                         if(!empty(this.old_item)){	 	    			
                             this.$emit('close-menu');
                         }
                         
                     } else {	 	    			    
                         notify(data.msg,"error");
                     }	 	    
                });				
                
                ajax_request[timenow].always( data => {	 	    	
                     this.add_to_cart = false;
                });				
                
            }  // end addCartItems
            //
            
        },
        template: '#xtemplate_item',
    };
    
    /*
       CUSTOMER COMPONENTS
    */
    const ComponentsCustomer = {
        props: ['label','ajax_url','client_id','image_placeholder' ,'page_limit','merchant_id'],
        data(){
            return {			
                is_loading : false,
                customer : [],
                addresses : [],
                block_from_ordering : false,
                datatables : undefined,
                count_up : undefined,	
            };	
        },			
        mounted() {	 
             this.observer = lozad('.lozad',{
                 loaded: function(el) {
                     el.classList.add('loaded');
                     dump("image loaded");
                 }
            });    		    
                    
        },
        computed: {
            hasData(){				
                if(Object.keys(this.customer).length>0){
                   return true;
                } 
                return false;
            },
        },
        updated () {		
            this.observer.observe();
        },
        methods :{
            show(){									
                this.getCustomerDetails();
                this.getCustomerOrders();
                this.getCustomerSummary();
                $( this.$refs.customer_modal ).modal('show');					    
            },
            close(){							
                $( this.$refs.customer_modal ).modal('hide');			    			   
            },
            getCustomerDetails(){
                this.is_loading = true;   	    		
                axios({
                   method: 'put',
                   url: this.ajax_url+"/getCustomerDetails",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'client_id' : this.client_id,	 
                        'merchant_id': this.merchant_id,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.customer = response.data.details.customer;
                          this.addresses = response.data.details.addresses;
                          this.block_from_ordering = response.data.details.block_from_ordering;
                      } else {			 	 	
                          this.customer = [];
                          this.addresses = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			 	
            },
            getCustomerOrders(){					 
                var $params = {
                    'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		    
                    'client_id' : this.client_id,			    
                };						
                //this.datatables = $('.order_table').DataTable({		    			    
                this.datatables = $(  this.$refs.order_table ).DataTable({	
                    ajax: {
                       url : this.ajax_url+"/getCustomerOrders",
                       "contentType": "application/json",
                       "type": "PUT",
                       data: d => {				    	   	  		    	  
                             d.YII_CSRF_TOKEN = $('meta[name=YII_CSRF_TOKEN]').attr('content');
                             d.client_id = this.client_id;	
                             d.merchant_id = this.merchant_id;	    	   	    	  
                          return JSON.stringify(d);
                       },
                    },
                    language: {
                        url: ajaxurl+"/DatableLocalize",			        
                    },
                    serverSide: true,
                    processing : true,
                    pageLength : parseInt(this.page_limit),     
                    destroy: true,
                    lengthChange : false,
                    "order": [[ 0, "desc" ]],
                    //pagingType: 'full_numbers',
                    columns: [
                        { data: 'order_id' },
                        { data: 'total' },
                        { data: 'status' },
                        { data: 'order_uuid' },			        
                    ],
                });		
                            
            },
            blockCustomerConfirmation(){				
                if(!this.block_from_ordering){
                    bootbox.confirm({ 
                        size: "small",
                        title : "" ,
                        message: '<h5>'+this.label.block_customer+'</h5>' + '<p>'+ this.label.block_content +'</p>',
                        centerVertical: true,
                        animate: false,
                        buttons: {
                            cancel: {
                               label: this.label.cancel,
                               className: 'btn btn-black small pl-4 pr-4'
                            },
                            confirm: {
                                label: this.label.confirm,
                                className: 'btn btn-green small pl-4 pr-4'
                            },
                        },
                        callback: result => {				    	
                            if(result){
                                this.blockOrUnlockCustomer(1);
                            }
                        }
                    });				
                } else {
                    this.blockOrUnlockCustomer(0);
                }
            },
            blockOrUnlockCustomer($block){
                this.is_loading = true;			    	
                axios({
                   method: 'put',
                   url: this.ajax_url+"/blockCustomer",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'client_id' : this.client_id,	 
                        'merchant_id': this.merchant_id,
                        'block' : $block,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          if(response.data.details==1){
                              this.block_from_ordering = true;
                          } else this.block_from_ordering = false;
                      } else {						 	 	
                          this.block_from_ordering = false;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            },
            getCustomerSummary(){
                
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/getCustomerSummary",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + '&client_id=' + this.client_id + "&merchant_id=" + this.merchant_id ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          var $options = {							
                            decimalPlaces : 0,
                            separator : ",",
                            decimal : "."
                        };					
                        var $summary_orders = new countUp.CountUp(this.$refs.summary_orders, response.data.details.orders , $options);	
                        $summary_orders.start();
                          
                        var $summary_cancel = new countUp.CountUp(this.$refs.summary_cancel,  response.data.details.order_cancel , $options);	
                        $summary_cancel.start();
                                         
                        var $options = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        };		   
                        var $summary_total = this.count_up = new countUp.CountUp(this.$refs.summary_total,  response.data.details.total , $options);	
                        $summary_total.start();
                        
                        var $total_refund = this.count_up = new countUp.CountUp(this.$refs.summary_refund,  response.data.details.total_refund , $options);	
                        $total_refund.start();
                
                      } else {						 	 	
                          
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
                
            },
        },
        template: '#xtemplate_customer',
    };
    
    
    
    /*
      COMPONENTS ORDER TO PRINT
    */
    const ComponentsOrderPrint = {	
        components: {
            'money-format': ComponentsMoney,	
        },		
        props: ['label','ajax_url','order_uuid','mode','line'],
        template: '#xtemplate_print_order',
        data(){
            return {			
                is_loading : false,
                merchant : [],
                order_info : [],
                order_status : [],
                services : [],
                payment_status : [],
                items :[],
                order_summary : [],
                print_settings : [],
                payment_list : [],			
            };	
        },			
        computed: {
            hasData(){			
                if(this.order_summary.length>0){
                   return true;
                } 
                return false;
            },
        },	
        methods :{
            show(){												
                this.orderDetails();
                $( this.$refs.print_modal ).modal('show');					    
            },
            close(){							
                $( this.$refs.print_modal ).modal('hide');			    			   
            },
            orderDetails(){
                this.order_summary = [];
                this.is_loading = true;
                
                var $payload = ['print_settings'];
                
                axios({
                   method: 'put',
                   url: this.ajax_url+"/orderDetails",
                   data : {
                         'order_uuid' : this.order_uuid,	       	 	 
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                     'payload' : $payload,
                      },
                   timeout: $timeout,
                 }).then( response => {	 	
                     this.response_code = response.data.code;		     
                     if(response.data.code==1){
                         
                         this.merchant = response.data.details.data.merchant;
                         this.order_info = response.data.details.data.order.order_info;
                         this.payment_list  = response.data.details.data.order.payment_list;
                                              
                         this.order_status = response.data.details.data.order.status;
                         this.services = response.data.details.data.order.services;
                         this.payment_status = response.data.details.data.order.payment_status;
                         
                         this.items = response.data.details.data.items;
                         this.order_summary = response.data.details.data.summary;		
                         this.print_settings = response.data.details.data.print_settings;		
                     } else {
                         
                         notify(response.data.msg,'error');
                         
                         this.merchant_direction = '';
                         this.delivery_direction = '';
                         
                         this.merchant = [];
                         this.order_info = [];
                         this.items = []
                         this.order_summary = [];
                         this.print_settings = [];			 		
                     }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                     this.loading = false;
                 });
            },
            print(){				
                $(".printhis").printThis(); 
                this.$refs.print_button.disabled = true; 
                setTimeout(() => {
                   this.$refs.print_button.disabled = false; 
                }, 1000); 
            },
        },
    };
    
    const ComponentsOrderSearchNav = {
        props: ['label','ajax_url','filter_status'],
        data(){
            return {			
                is_loading : false,		
                status_list : [],	
                order_type_list : [],
                payment_status_list : [],
                sort_list : [],
                status : [],
                order_type : [],
                payment_status : [],
                sort : '',		
                search_filter : '',	
                awaitingSearch : false,		  
                search_toggle : false, 
            };	
        },
        mounted() {	 
             this.getOrderFilterSettings();
             this.selectPicker();
        },
        watch: {
            search_filter(newsearch,oldsearch){
                if (!this.awaitingSearch) {    	
                    
                    if(empty(newsearch)){				   
                         return false;
                    }		
                    
                    setTimeout(() => {    		       
                       this.search_toggle = true;
                       this.$emit('afterFilter', {
                            search_filter : this.search_filter,		
                            order_type : this.order_type,    			
                            payment_status : this.payment_status,
                            sort : this.sort,				
                        }); 			
                       
                       this.awaitingSearch = false;
                    }, 1000); // 1 sec delay
                }
                this.awaitingSearch = true;
            }
        },
        methods :{
            getOrderFilterSettings(){
                
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/getOrderFilterSettings",
                   data : 'YII_CSRF_TOKEN='+ $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.status_list = response.data.details.status_list;				 	 	
                          this.order_type_list = response.data.details.order_type_list;				 	 	
                          this.payment_status_list = response.data.details.payment_status_list;
                          this.sort_list = response.data.details.sort_list;
                          setTimeout(() => {
                           $('.selectpicker').selectpicker('refresh');	 	 
                         }, 1);
                      } else {						 	 				 	 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
                
            },
            selectPicker(){
                /*$( this.$refs.status_list ).on('changed.bs.select', (e, clickedIndex, isSelected, previousValue)=>{    			
                    this.status = $( this.$refs.status_list  ).selectpicker('val');    	    			
                    this.$emit('afterFilter', {
                        status : this.status,					
                    }); 			
                });*/            
                $( this.$refs.order_type_list ).on('changed.bs.select', (e, clickedIndex, isSelected, previousValue)=>{    			
                    this.order_type = $( this.$refs.order_type_list  ).selectpicker('val');    	    			
                    this.$emit('afterFilter', {
                        order_type : this.order_type,					
                        payment_status : this.payment_status,
                        sort : this.sort,
                    }); 			
                });
                
                $( this.$refs.payment_status_list ).on('changed.bs.select', (e, clickedIndex, isSelected, previousValue)=>{    			
                    this.payment_status = $( this.$refs.payment_status_list  ).selectpicker('val');    	    			
                    this.$emit('afterFilter', {
                        order_type : this.order_type,    			
                        payment_status : this.payment_status,
                        sort : this.sort,
                    }); 			
                });
                
                $( this.$refs.sort_list ).on('changed.bs.select', (e, clickedIndex, isSelected, previousValue)=>{    			
                    this.sort = $( this.$refs.sort_list  ).selectpicker('val');    	    			
                    this.$emit('afterFilter', {
                        order_type : this.order_type,    			
                        payment_status : this.payment_status,
                        sort : this.sort,
                    }); 			
                });
                
            },
            clearFiler(){
                this.search_toggle = false;
                this.search_filter = '';
                this.$emit('afterFilter', {
                    search_filter : '',
                    order_type : this.order_type,    			
                    payment_status : this.payment_status,
                    sort : this.sort,				
                }); 			
            },
        },
        template: `
        <div class="order-search-nav p-2">
        
          <div class="row">
            <div class="col-md-6 pl-2 mb-2 mb-lg-0">
            
             <div class="input-group mr-2">
                <input v-model="search_filter" class="form-control py-2 border-right-0 border" type="search" 
                :placeholder="label.placeholder"
                >
                <span class="input-group-append">
                    <div v-if="!awaitingSearch" class="input-group-text bg-transparent"><i class="zmdi zmdi-search"></i></div>
                    <div v-if="awaitingSearch" class="input-group-text bg-transparent"><i class="fas fa-circle-notch fa-spin"></i></div>
                    <div v-if="search_toggle" @click="clearFiler" class="input-group-text bg-transparent"><a class="m-0 link font12">Clear</a></div>
                </span>
              </div>  
            
            </div> <!--col-->
            
            <div class="col-md-6">	   	  
            
               <div class="d-flex selectpicker-group rounded">
                 <div v-if="filter_status" class="flex-col">
                 <select ref="status_list" data-width="fit" class="selectpicker" multiple="multiple"  :title="label.status" data-selected-text-format="static" >		    		    
                   <option v-for="(item, key) in status_list" :value="key">{{item}}</option>		    
                 </select>	
                 </div>
                 <div class="flex-col">
                  <select ref="order_type_list"  data-width="fit" class="selectpicker" multiple="multiple" :title="label.order_type_list" data-selected-text-format="static" >		    		    
                   <option v-for="(item, key) in order_type_list" :value="key">{{item}}</option>		    
                  </select>		
                 </div>
                 <div class="flex-col">
                  <select ref="payment_status_list"  data-width="fit" class="selectpicker" multiple="multiple" :title="label.payment_status_list" data-selected-text-format="static" >		    		    
                   <option v-for="(item, key) in payment_status_list" :value="key">{{item}}</option>		    
                  </select>		
                 </div>
                 <div class="flex-col">	          
                  <select ref="sort_list"  data-width="fit" class="selectpicker" :title="label.sort" data-selected-text-format="static" >		    		    
                   <option v-for="(item, key) in sort_list" :value="key" :data-icon="item.icon" >{{item.text}}</option>		    
                  </select>		
                 </div>
               </div>		  	    
            </div> <!--col-->
            
          </div>
          <!--flex-->
        
        </div>
        <!--order searcb-nav-->		
        `
    };
    
    
    /*
        COMPONENTS STOP ORDERING
    */
    const ComponentsPauseModal = {	
        props : {
            label : Array,
            ajax_url:String,
            pause_interval : {
                type: Number,
                 default: 10
            },
        },
        data(){
          return {					
               is_loading : false,     
               data : [] ,
               time_delay : '',	
               steps : 1,      
               pause_reason : [],
               reason : '',
               pause_hours : 0,
               pause_minutes : 0,    
               //pause_interval : 10,  	       	 
          };	
        },
        mounted(){
            this.getDelayedMinutes();		
        },
         computed: {
            hasData(){			
                if(this.time_delay=="other"){
                    if(this.pause_hours>0){
                        return true;
                    }
                    if(this.pause_minutes>0){
                        return true;
                    }
                } else {
                    if(!empty(this.time_delay)){
                       return true;
                    } 
                }
                return false;
            },
            hasReason(){			
                if(!empty(this.reason)){
                   return true;
                } 
                return false;
            },
        },
        methods: {
            show(){
                $( this.$refs.modal_pause_order ).modal('show');
            },		
            close(){
                $( this.$refs.modal_pause_order ).modal('hide');
            },		
            getDelayedMinutes(){
                axios({
                   method: 'post',
                   url: this.ajax_url+"/getPauseOptions",
                   data : 'YII_CSRF_TOKEN='+$('meta[name=YII_CSRF_TOKEN]').attr('content'),
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
                     
                 });		
            },		
            submit(){
                
                this.is_loading = true;
                axios({
                   method: 'put',
                   url: this.ajax_url+"/setPauseOrder",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'time_delay' : this.time_delay,
                        'reason' : this.reason,
                        'pause_hours': this.pause_hours,
                        'pause_minutes': this.pause_minutes,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.steps=1;
                          this.time_delay='';
                          this.close();
                          this.$emit('afterPause', response.data.details );
                      } else {
                          notify(response.data.msg);
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
                
            },
            addMins(){			
                if(this.pause_minutes>=60){
                    this.pause_minutes = 0;
                    this.pause_hours+=1;
                } else {
                   this.pause_minutes+= this.pause_interval;			
                }			
            },
            lessMins(){
                if(this.pause_minutes<=0){
                    if(this.pause_hours>0){
                       this.pause_minutes = 60;		
                       this.pause_hours-=1;
                    } else {
                       this.pause_minutes = 0;			
                    }
                } else {
                    this.pause_minutes-= this.pause_interval;			
                }			
            },
            cancel(){			
                if(this.time_delay=="other"){
                    this.steps = 1; this.time_delay = '';
                } else {
                    this.close();
                }
            },
        },
        template: `	
           <div ref="modal_pause_order" class="modal"
            id="modal_pause_order"  data-backdrop="static" 
            tabindex="-1" role="dialog" aria-labelledby="modal_pause_order" aria-hidden="true">
                   
               <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                 <div class="modal-content pt-2">
                                                       
                 <template v-if="steps==1">
                  <div class="modal-body">	
                                    
                   <div class="w-75 m-auto">
                        <h5>{{label.pause_new_order}}</h5>
                        <p>{{label.how_long}}</p>
                                            
                       <template v-if="time_delay=='other'">				   
                       <div class="d-flex justify-content-center align-items-center text-center">
                         <div class="flex-col mr-3"><button @click="lessMins" class="btn rounded-button-icon rounded-circle"><i class="zmdi zmdi-minus"></i></button></div>
                         <div class="flex-col mr-1"><h1>{{pause_hours}}</h2> <p class="m-0 font9 font-weight-bold">{{label.hours}}</p></div>
                         <div class="flex-col mr-1"><h1>:</h2><p  class="m-0 font11">&nbsp;</p></div>
                         <div class="flex-col m-0"><h1>{{pause_minutes}}</h2> <p class="m-0 font9 bold font-weight-bold">{{label.minute}}</p></div>
                         <div class="flex-col ml-3"><button @click="addMins" class="btn rounded-button-icon rounded-circle"><i class="zmdi zmdi-plus"></i></button></div>
                       </div>				   				   				  
                       </template>
                       
                       <template v-else>				   
                       <div class="row mt-4">
                         <div v-for="item in data.times" class="col-lg-4 col-md-4 col-sm-6 col-4 mb-2 mb-2 ">
                           <button 
                           :class="{active:time_delay==item.id}" 
                           @click="time_delay=item.id"
                           class="btn btn-light">
                           {{item.value}}
                           </button>
                         </div>
                       </div>
                       </template>
                        
                   </div> <!-- w-75 -->
                    
                  </div> <!-- body -->
                  
                  <div class="modal-footer">         
                   <button type="button" class="btn btn-black" @click="cancel">
                    <span class="pl-3 pr-3">{{label.cancel}}</span>
                   </button>	           
                    <button type="submit"  class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"   
                    :disabled="!hasData"
                    @click="steps=2"
                     >
                      <span>{{label.next}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                    </button>
                 </div>
                 </template>
                 
                 <template v-else-if="steps==2">
                 <div class="modal-body">		 
                 
                  <div class="w-75 m-auto">
                    <h5>{{label.reason}}</h5>
                  </div>
                    
                   <div class="list-group list-group-flush mt-4">
                     <a v-for="item in data.pause_reason" @click="reason=item" 
                     :class="{active:reason==item}"
                     class="text-center list-group-item list-group-item-action">
                     {{item}}
                     </a>
                   </div>
                 
                  </div> <!-- body -->
                                
                  <div class="modal-footer">               
                   <button type="button" class="btn btn-black"
                   @click="steps=1"
                    >
                    <span class="pl-3 pr-3">{{label.cancel}}</span>
                   </button>
                       
                    <button type="submit"  class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"   
                    :disabled="!hasReason"
                    @click="submit"
                     >
                      <span>{{label.confirm}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                    </button>
                 </div>
                 </template>
                                  
                 </div> <!--content-->		     
              </div> <!--dialog-->		  
            </div> <!--modal-->     	
        `
    };
    
    
    /*
      COMPONENTS TIMER COUNTDOWN
    */
    const ComponentsTimerCountDown = {
        props : {
            endDate : {  // pass date object till when you want to run the timer
              type : Date,
              default(){
                return new Date()
              }
            },
            negative : {  // optional, should countdown after 0 to negative
              type : Boolean,
              default : false
            }
        },
        data(){
            return{
              now : new Date(),
              timer : null
            }
        },
        computed:{
            hour(){
              let h = Math.trunc((this.endDate - this.now) / 1000 / 3600);
              return h>9?h:'0'+h;
            },
            min(){
              let m = Math.trunc((this.endDate - this.now) / 1000 / 60) % 60;
              return m>9?m:'0'+m;
            },
            sec(){	      
              let s = Math.trunc((this.endDate - this.now)/1000) % 60
              return s>9?s:'0'+s;
            }
        },
        watch : {
            endDate : {
              immediate : true,
              handler(newVal){
                if(this.timer){
                  clearInterval(this.timer)
                }
                this.timer = setInterval(()=>{
                  this.now = new Date()
                  if(this.negative)
                    return
                  if(this.now > newVal){
                    this.now = newVal
                    this.$emit('endTime')
                    clearInterval(this.timer)
                  }
                }, 1000)
              }
            }
        },
        beforeUnmount(){
            clearInterval(this.timer)
        },
        template: `	
          <p class="m-0 mt-1 text-center font-weight-bold"><slot></slot> {{hour}}:{{min}}:{{sec}} </p>
        `
    };
    
    
    /*
          COMPONENTS PAUSE ORDER
    */
    const ComponentsPauseOrder = {
        props: ['label','ajax_url'],
        components: {      
          'components-timer-countdown': ComponentsTimerCountDown,       
        },	
        data(){
          return {					
               is_load : false,
               accepting_order : false,			 
             data : [],
             pause_time : undefined,
             luxon : undefined
          };	
        },
        mounted() {	 
             this.getMerchantOrderingStatus();  	   
             this.luxon = luxon.DateTime;  
             //var DateTime = luxon.DateTime;  	
             //this.pause_time = DateTime.fromObject({year: 2021, month: 12, day: 19, hour: 10, minute: 45});  	   
             //this.pause_time = DateTime.fromISO('2021-12-19T10:50')
        }, 
        methods: {
            getMerchantOrderingStatus(){
                this.is_load = true;
                axios({
                   method: 'post',
                   url: this.ajax_url +"/MerchantOrderingStatus",
                   data : 'YII_CSRF_TOKEN='+$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){
                          this.data = response.data.details;
                          this.accepting_order = response.data.details.accepting_order;			 	 	  
                          this.pause_time  = this.luxon.fromISO(response.data.details.pause_time);
                          dump(this.pause_time);
                      } else {
                          this.data = [];
                          this.pause_order = false;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_load = false;
                 });			
                 
            },
            PauseOrdering(){    	      	   
               this.$emit('afterClickpause',this.accepting_order);
            },
            pauseOrderEnds(){    		
                axios({
                   method: 'put',
                   url: this.ajax_url +"/UpdateOrderingStatus",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'accepting_order' : true			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){			
                          this.accepting_order = response.data.details.accepting_order;
                      } else {			 	 	
                          this.accepting_order = false;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_load = false;
                 });			
            },    	
            updateStatus($data){
                dump($data);
                this.accepting_order = $data.accepting_order;    		
                this.pause_time  = this.luxon.fromISO($data.pause_time);
            },
        },
        template: `
         <div class="position-relative">
          <div v-if="is_load" class="skeleton-placeholder" style="height:50px;width:100%;"></div>
          
           <button @click="PauseOrdering" class="btn" :class="{'btn-green' :accepting_order, 'btn-yellow': accepting_order==false}">   
             <div class="d-flex justify-content-between align-items-center">
               <template v-if="accepting_order" >
                   <div class="mr-0 mr-lg-2"><i style="font-size:20px;" class="zmdi zmdi-check-circle"></i></div>
                   <div class="xd-none xd-lg-block" >{{label.accepting_orders}}</div>       
               </template>
               <template v-else>  
                   <div class="mr-2"><i style="font-size:20px;" class="zmdi zmdi-pause"></i></div>
                   <div>{{label.not_accepting_orders}}</div>
               </template>
             </div>
           </button>
                  
           <template v-if="!is_load">
           <template v-if="!accepting_order">
           <components-timer-countdown
            :endDate='pause_time'
            @end-time="pauseOrderEnds"
             >
             {{label.store_pause}}
           </components-timer-countdown>
           </template>
           </template>
           
          
          </div>          
        `
    };
    
    
    const ComponentsResumeOrder = {	
        props: ['label','ajax_url'],
        data(){
          return {					
               is_loading : false,      	 
          };	
        },
        methods: {
            show(){
                $( this.$refs.modal ).modal('show');
            },		
            close(){
                $( this.$refs.modal ).modal('hide');
            },		
            submit(){
                this.is_loading = true;		
                axios({
                   method: 'put',
                   url: this.ajax_url +"/UpdateOrderingStatus",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                        'accepting_order' : true			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){			
                          this.$emit('afterPause', response.data.details );
                          this.close();
                      } else {			 	 	
                          notify(response.data.msg);
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            },
        },
        template: `	
            <div ref="modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static"  >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
              <div class="modal-body">	            	     
                
                <div class="w-75 m-auto">
                    <h5>{{label.store_pause}}</h5>
                    <p>{{label.resume_orders}}</p>
                </div>  
              
              </div>      
              <div class="modal-footer">            
              
                    <button type="button" class="btn btn-black" data-dismiss="modal">
                    <span class="pl-3 pr-3">{{label.cancel}}</span>
                   </button>
                       
                    <button type="submit"  class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"   		        		        
                    @click="submit"
                     >
                      <span>{{label.confirm}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                    </button>  
              
              </div>	      
            </div>
          </div>
        </div>            
        `	
    };
    
    /*
      ORDER LIST MANAGEMENT  
      target = vue-order-management
    */
    const app_order_management = Vue.createApp({
      components: {
        'components-orderlist': ComponentsOrderList,       
        'components-orderinfo': ComponentsOrderInfo, 
        //'components-rejection-forms': ComponentsRejectionForms, 
        'components-delay-order': ComponentsDelayForm, 
        'components-order-history': ComponentsOrderHistory, 
        'components-menu': ComponentsMenu, 
        'components-item-details': ComponentsItem, 
        'components-customer-details': ComponentsCustomer, 
        'components-order-print': ComponentsOrderPrint, 
        'components-order-search-nav': ComponentsOrderSearchNav, 
        'components-pause-order': ComponentsPauseOrder, 
        'components-pause-modal': ComponentsPauseModal,       
        'components-resume-order-modal': ComponentsResumeOrder,  
        'components-notification': ComponentsNotification, 	
      },
      data(){
        return {						
            order_uuid : '',
            order_type : '',
            client_id : '',
            resolvePromise: undefined,
            rejectPromise: undefined,
            show_as_popup : false,
        };
      },
      mounted() {	 
          
      },
      methods: {
         afterSelectOrder($order_uuid, $order_type){  
             this.order_uuid = $order_uuid;
             this.order_type = $order_type;
             this.$refs.orderinfo.orderDetails($order_uuid);
         },
         refreshOrderInformation($order_uuid){
             this.$refs.orderinfo.orderDetails($order_uuid);
         },  
         afterUpdateStatus(){
             this.$refs.orderlist.getList();     	
             if ((typeof  vm_siderbar_menu !== "undefined") && ( vm_siderbar_menu !== null)) {
                vm_siderbar_menu.getOrdersCount();
             }
         },
         orderReject($order_uuid){     	     	
             this.$refs.rejection.confirm();
         },     
         delayOrder($order_uuid){     	     	
            this.$refs.delay.show();     	
         },     
         orderHistory($order_uuid){     	
             this.$refs.history.show();
         },
         showMerchantMenu($item_row){     	     	
             this.$refs.menu.replace_item = $item_row;
             this.$refs.menu.show();
         },
         hideMerchantMenu(){
             this.$refs.menu.close();
         },
         showItemDetails($data){     	
             this.$refs.item.show($data);
         },
         viewCustomer(){     	     	
             this.$refs.customer.show();
         },
         toPrint(){     	
             this.$refs.print.show();
         },
         afterFilter($filter){     	
             this.$refs.orderlist.getList($filter);
         },
         afterClickpause($accepting_order){
             dump($accepting_order);    
             if($accepting_order){
                 this.$refs.pause_modal.show();
             } else {     
                 this.$refs.resume_order.show();
             }     	
         },
         afterPause($data){
            this.$refs.pause_order.updateStatus($data);
         },
         closeOrderModal(){
             this.show_as_popup = false;
         },
      },
    });
    
    app_order_management.use(Maska);
    const vm_order_management = app_order_management.mount('#vue-order-management');
    
    
    /* ============================================================
        ORDER MANAGEMENT CODE END HERE
       ============================================================*/
    
    /*
      VIEW ORDER
    */
    const app_order_view = Vue.createApp({	
        components: {
           'components-orderinfo': ComponentsOrderInfo, 
           'components-delay-order': ComponentsDelayForm, 
           'components-rejection-forms': ComponentsRejectionForms, 
           'components-order-history': ComponentsOrderHistory, 
           'components-order-print': ComponentsOrderPrint, 
           'components-menu': ComponentsMenu, 
           'components-item-details': ComponentsItem, 
           'components-customer-details': ComponentsCustomer, 
       },
       data(){
        return {						
            order_uuid : '',	
            client_id : '',	
            merchant_id : '',	
            is_loading : false,
            group_name : '',
            manual_status : false,
            modify_order : false,
            filter_buttons : false,
        };
       },
       mounted() {	   	     	  
             this.order_uuid = _order_uuid;
             this.getGroupname();  	  
       },
       methods: {   	 
            afterUpdateStatus(){     	     	
             this.getGroupname();
             if ((typeof  vm_siderbar_menu !== "undefined") && ( vm_siderbar_menu !== null)) {
                vm_siderbar_menu.getOrdersCount();
             }
         },
         refreshOrderInformation($order_uuid){
             this.$refs.orderinfo.orderDetails( this.order_uuid );
         },  
         loadOrderDetails(){     	
             this.$refs.orderinfo.orderDetails( this.order_uuid );
         },
         delayOrder($order_uuid){     	     	     	
            this.$refs.delay.show();     	
         },
         orderReject($order_uuid){     	     	
             this.$refs.rejection.confirm();
         },
         orderHistory($order_uuid){     	
             this.$refs.history.show();
         },
         toPrint(){     	     	     
             this.$refs.print.show();
         },
         showMerchantMenu($item_row){     	     	
             this.$refs.menu.replace_item = $item_row;
             this.$refs.menu.show();
         },
         showItemDetails($data){     	
             this.$refs.item.show($data);
         },
         viewCustomer(){     	     	
             this.$refs.customer.show();
         },
         getGroupname(){
             
                this.is_loading = true;			    	
                axios({
                   method: 'POST',
                   url: _ajax_url +"/getGroupname",			   
                      data : 'YII_CSRF_TOKEN='+$('meta[name=YII_CSRF_TOKEN]').attr('content') + "&order_uuid=" + this.order_uuid,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.group_name = response.data.details.group_name;
                          this.manual_status = response.data.details.manual_status;
                          this.modify_order = response.data.details.modify_order;		
                          this.client_id = response.data.details.client_id;
                          this.merchant_id = response.data.details.merchant_id;
                          this.filter_buttons = response.data.details.filter_buttons;
                      } else {						
                          this.client_id  = ''; 	 	
                          this.group_name = '';
                          this.manual_status = false;
                          this.modify_order = false;
                          this.filter_buttons = false;
                      }			 	 
                      
                      setTimeout(() => {
                         this.loadOrderDetails();
                      }, 1);
                      
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
                 
         },
       },
    });
    app_order_view.use(Maska);
    const vm_order_view = app_order_view.mount('#vue-order-view');
    
    
    /* 
       SIDE MENU
    */
    const vm_siderbar_menu = Vue.createApp({	
        mounted() {	   	     	     	     	  
             this.getOrdersCount();
       },
       data(){
         return {						
            data : [],
            is_load : false,
         };
       },
       methods: {
             getOrdersCount(){
                  axios({
               method: 'POST',
               url: apibackend +"/getOrdersCount",
               data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){
                                                                
                      if(this.is_load){
                          $( this.$refs.orders_new ).find(".badge-notification").remove();
                      }
                                        
                      this.data = response.data.details;
                      if (this.data.not_viewed>0){
                          $( this.$refs.orders_new ).append('<div class="blob green badge-pill pull-right badge-notification bg-new">'+this.data.new_order+'</div>');
                      } else {
                          $( this.$refs.orders_new ).append('<div class="badge-pill pull-right badge-notification bg-new">'+this.data.new_order+'</div>');
                      }
                      $( this.$refs.orders_processing ).append('<div class="badge-pill pull-right badge-notification bg-processing">'+this.data.order_processing+'</div>');
                      $( this.$refs.orders_ready ).append('<div class="badge-pill pull-right badge-notification bg-ready">'+this.data.order_ready+'</div>');
                      $( this.$refs.orders_completed ).append('<div class="badge-pill pull-right badge-notification bg-completed">'+this.data.completed_today+'</div>');		 	 
                      $( this.$refs.orders_scheduled ).append('<div class="badge-pill pull-right badge-notification bg-scheduled">'+this.data.scheduled+'</div>');
                      $( this.$refs.orders_history ).append('<div class="badge-pill pull-right badge-notification bg-history">'+this.data.all_orders+'</div>');

                        var shouldPlayAlertSound = this.data.not_viewed>0?true:false;
                        var notif = new Audio('../assets/sound/notify.mp3');
                        if (shouldPlayAlertSound) {
                            notif.play();
                        }

                  }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_load = true;
             });			
             },
       },
    }).mount('#vue-siderbar-menu');
    
    /* 
      DATATABLES FILTER
    */
    
    const ComponentsDataTablesFilter = {
        props: ['ajax_url','settings'],
        template: '#xtemplate_order_filter',
        mounted() {
           this.initeSelect2();
           this.getFilterData();
        },
        data(){
           return {						
              status_list : [],
              order_type_list : [],
              order_status : '',
              order_type : '',
              client_id : ''
           };
        },
        methods: {
            initeSelect2(){
                 $('.select2-single').select2({
                     width : 'resolve',		 	
                 });	
                 $('.select2-customer').select2({
                      width : 'resolve',	
                      language: {
                        searching: ()=> {
                          return this.settings.searching;
                        },
                        noResults: ()=> {
                          return this.settings.no_results;
                        }
                     },
                      ajax: {
                          delay: 250,		  	 
                        url: this.ajax_url +"/searchCustomer",
                        type: 'PUT',
                        contentType : 'application/json',
                        data: function (params) {
                          var query = {
                            search: params.term,		        
                            'YII_CSRF_TOKEN': $('meta[name=YII_CSRF_TOKEN]').attr('content')
                          }			     
                          return JSON.stringify(query);
                        }
                      }	 	
                 });	
            },
            getFilterData(){
                
                axios({
                   method: 'put',
                   url: this.ajax_url +"/getFilterData",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){
                          this.status_list = response.data.details.status_list;
                          this.order_type_list = response.data.details.order_type_list;
                      } else {
                          this.status_list = [];
                          this.order_type_list = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
            },
            clearFilter(){
                $( this.$refs.order_status ).val(null).trigger('change');
                $( this.$refs.order_type ).val(null).trigger('change');
                $( this.$refs.client_id ).val(null).trigger('change');
            },
            submitFilter(){
                this.order_status = $( this.$refs.order_status ).find(':selected').val();
                this.order_type = $( this.$refs.order_type ).find(':selected').val();
                this.client_id = $( this.$refs.client_id ).find(':selected').val();
                this.$emit('afterFilter', {
                    order_status : this.order_status,
                    order_type : this.order_type,
                    client_id : this.client_id
                }); 			
            },
            closePanel(){
                this.$emit('closePanel');
            },
        },
    };
    
    const ComponentsDataTables = {
        components: {
           'components-order-filter': ComponentsDataTablesFilter,
        },
        props: ['settings','actions','ajax_url','table_col','columns', 'page_limit', 'transaction_type_list','filter'],
        mounted() {
            this.getTableData();
            this.initDateRange();
            this.selectPicker();
            this.initSiderbar();
        },
        data(){
           return {						
              datatables : undefined,
              date_range : '',
              date_start : '',
              date_end : '',
              transaction_type : [],
              sidebarjs : undefined,
              filter_by : []
           };
        },
        methods: {
            initDateRange(){
                
                $( this.$refs.date_range ).daterangepicker({	
                    "autoUpdateInput": false,				
                    "showWeekNumbers": true,
                    "alwaysShowCalendars": true,
                    "autoApply": true,
                    "locale" : {
                      format: 'YYYY-MM-DD'
                    },
                     ranges: {
                       'Today': [moment(), moment()],
                       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                       'This Month': [moment().startOf('month'), moment().endOf('month')],
                       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }			
                }, (start,end,label) => {				
                    this.date_range = start.format('YYYY-MM-DD') +" "+ this.settings.separator +" "+   end.format('YYYY-MM-DD') ;
                    this.date_start = start.format('YYYY-MM-DD');
                    this.date_end = end.format('YYYY-MM-DD');				
                    this.getTableData();
                });			
                
            },
            selectPicker(){    		
                $( this.$refs.transaction_type ).on('changed.bs.select', (e, clickedIndex, isSelected, previousValue)=>{    			
                    this.transaction_type = $( this.$refs.transaction_type  ).selectpicker('val');    	
                    this.getTableData();				
                });
            },
            initSiderbar(){    		
                if(this.filter==1){
                    this.sidebarjs = new SidebarJS.SidebarElement({
                       position: "right",    
                    });
                }
            },
            getTableData(){    		 
                 var $datatables; var $vue = this;
                 this.datatables = $(  this.$refs.vue_table ).DataTable({	
                    ajax: {
                       url : this.ajax_url+"/"+ this.actions,
                       "contentType": "application/json",
                       "type": "PUT",
                       data: d => {				    	   	  		    	  
                             d.YII_CSRF_TOKEN = $('meta[name=YII_CSRF_TOKEN]').attr('content');		    	   	  
                             d.date_start = this.date_start;
                             d.date_end = this.date_end;
                             d.transaction_type = this.transaction_type;
                             d.filter = this.filter_by;
                          return JSON.stringify(d);
                       },
                    },
                    language: {
                        url: ajaxurl+"/DatableLocalize",			        
                    },
                    serverSide: true,
                    processing : true,
                    pageLength : (this.actions == 'orderHistory') ? 30:parseInt(this.page_limit),     
                    destroy: true,
                    lengthChange : false,
                    bFilter : this.settings.filter,
                    ordering : this.settings.ordering, 
                    order : [[ this.settings.order_col , this.settings.sortby ]],
                    //order : [[ 0, "desc" ]],	
                    columns : this.columns	    	
                 });		
                 
                 $datatables = this.datatables;
                 
                 $('.vue_table tbody').on( 'click', '.ref_invoice', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 		     	 		     	 
                      if(!empty(data)){		     	 			     	    
                         $vue.$emit('viewInvoice', data.invoice_ref_number , data.payment_code ); 
                      }
                 });
                 
                 $('.vue_table tbody').on( 'click', '.ref_tax_edit', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 		     	 		     	 		     	 
                      if(!empty(data)){		     	 			     	    
                         $vue.$emit('editTax', data.tax_uuid ); 
                      }
                 });
                 
                 $('.vue_table tbody').on( 'click', '.ref_tax_delete', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 		     	 		     	 		     	 
                      if(!empty(data)){		     	 			     	    
                         $vue.$emit('deleteTax', data.tax_uuid ); 
                      }
                 });
                 
                    $('.vue_table tbody').on( 'click', '.ref_edit', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 
                      if(!empty(data)){
                          window.location.href = data.update_url;
                      }
                 });
    
                 $('.vue_table tbody').on( 'click', '.ref_view_url', function () {			     	 
                    var data = $datatables.row( $(this).parents('tr') ).data();		     	 
                    if(!empty(data)){
                        window.location.href = data.view_url;
                    }
                });
                 
                 $('.vue_table tbody').on( 'click', '.ref_delete', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 
                      if(!empty(data)){
                          
                          bootbox.confirm({ 
                            size: "small",
                            title : "" ,
                            message: '<h5>Delete Confirmation</h5>' + '<p>Are you sure you want to permanently delete the selected item?</p>',
                            centerVertical: true,
                            animate: false,
                            buttons: {
                                cancel: {
                                   label: 'Cancel',
                                   className: 'btn'
                                },
                                confirm: {
                                    label: 'Delete',
                                    className: 'btn btn-green small pl-4 pr-4'
                                },
                            },
                            callback: result => {				    	
                                if(result){
                                    window.location.href = data.delete_url;
                                }
                            }
                        });				
                          
                      } /*end if*/
                 });
                 
            },
            openFilter(){
                this.sidebarjs.toggle();
            },
            afterFilter($filter){    	    		
                this.sidebarjs.toggle();
                this.filter_by = $filter;
                this.getTableData();
            },
            closePanel(){
                this.sidebarjs.toggle();
            },    	
        },
        template: `			
        
         <div class="row mb-3">
          <div class="col">
              
              <div class="d-flex">
                        
              <div v-if="!settings.filter_date_disabled" class="input-group fixed-width-field mr-2">
                <input ref="date_range" v-model="date_range" class="form-control py-2 border-right-0 border" type="search"         
                :placeholder="settings.placeholder" :data-separator="settings.separator"
                >
                <span class="input-group-append">
                    <div class="input-group-text bg-transparent"><i class="zmdi zmdi-calendar-alt"></i></div>
                </span>
              </div>
              
              
              <select v-if="transaction_type_list" ref="transaction_type" data-style="selectpick" class="selectpicker" multiple="multiple" :title="settings.all_transaction" >		    
                <option v-for="(item, key) in transaction_type_list" :value="key">{{item}}</option>		    
              </select>
              
              <button v-if="filter==1" class="btn btn-yellow normal" @click="openFilter" >		   		   
               <div class="d-flex">
                 <div class="mr-2"><i class="zmdi zmdi-filter-list"></i></div>
                 <div>{{settings.filters}}</div>
               </div>
              </button>
              
              </div> <!-- flex -->
              
          </div>	  
          <div class="col"></div>
        </div> <!--row-->
        
        <div class="table-responsive">
        <table ref="vue_table" class="table vue_table">
        <thead>
        <tr>
         <th v-for="(col, key) in table_col" :width="col.width">{{col.label}}</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
        </table>
        </div>
        
        <components-order-filter
        ref="filter"
        :ajax_url="ajax_url"
        :settings="settings"
        @after-filter="afterFilter"
        @close-panel="closePanel"
        >
        </components-order-filter>
        `
    };
    
    const componentsMerchantBalance = {
        props: ['ajax_url'],
        data(){
           return {			
                 is_loading : false,				
           };
        },
        mounted() {
            this.getGetMerchantBalance();
        },		
        methods: {
            getGetMerchantBalance(){		
                this.is_loading = true;	
                axios({
                   method: 'put',
                   url: this.ajax_url +"/getGetMerchantBalance",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance;
                      if(response.data.code==1){				 	 	
                        $balance = this.count_up = new countUp.CountUp(this.$refs.balance, response.data.details.balance ,  {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        });	
                        $balance.start();			 	 						        
                        this.$emit('afterBalance', response.data.details.balance ); 	
                      } 	 				 	
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            },
        },
        template: `
        <span ref="balance"></span>
        `
    };
    
    
    const ComponentsRequestPayout = {
        props: ['ajax_url','label','balance'],
        data(){
            return {
               is_loading : false,		  
               error : [],
               amount : 0,		   
            }
        },
        computed: {
            hasData(){						
                var $pass = true;
                if(this.amount<=0){
                    $pass = false;
                }			
                if( parseFloat(this.amount) >= parseFloat(this.balance) ){
                    $pass = false;
                }			
                return $pass;
                
            },
        },	
        methods: {
          show(){    	  	 	  	
                 $( this.$refs.modal_payout ).modal('show');
                 this.$refs.amount.focus(); 
             },
             close(){  	  	 
                 $( this.$refs.modal_payout ).modal('hide');
             },  
             submit(){
                 
                     this.error = [];
                    this.is_loading = true;	
                axios({
                   method: 'put',
                   url: this.ajax_url +"/requestPayout",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        amount : this.amount
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance;
                      if(response.data.code==1){		
                         notify(response.data.msg);
                         this.close();
                         this.$emit('afterRequestpayout');	    		    			 	    
                      } else {
                          this.error = response.data.msg;			 	 	
                          location.href = "#error_message";   
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		
                  
             },
        },
        template: `
        <div ref="modal_payout" class="modal"
        id="modal_payout" data-backdrop="static" 
        tabindex="-1" role="dialog" aria-labelledby="modal_payout" aria-hidden="true">
        
           <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
             <div class="modal-content"> 
             
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              
             <div class="modal-body">
             
             <div class="form-label-group">    
               <input type="text" v-model="amount" id="amount"
               v-maska="'#*.##'" 
               ref="amount"
               placeholder=""
               class="form-control form-control-text" >
               <label for="amount">{{label.amount}}</labe>
             </div>
                 
             
             <div id="error_message" v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
             </div>
             
             </div> <!-- body -->
             
              <div class="modal-footer">               
               <button type="button" class="btn btn-black" data-dismiss="modal">
                <span class="pl-3 pr-3">{{label.close}}</span>
               </button>
                   
                <button type="submit" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
                 :disabled="!hasData"
                 >
                  <span>{{label.submit}}</span>
                  <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                </button>
             </div>
    
          </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->     	
        `
    };
    
    
    /*
       COMPONENTS CASH IN
    */
    const ComponentsCashin = {
        props: ['cash_link' , 'label' ,'amount_selection' , 'minimum_cashin' ],
        data(){
           return {						
             data : [],		 		
             cashin_amount : 0,
             is_loading : false,
           };
        },   
        computed: {
            hasData(){			
                if(this.cashin_amount<this.minimum_cashin){
                    return false;
                }
                return true;
            },
        },
        methods: {
          show(){    	  	 	  	
                 $( this.$refs.modal_cashin ).modal('show');
            },
            close(){  	  	 
                 $( this.$refs.modal_cashin ).modal('hide');
            },  	 
            ContinueToPayment(){  	  	  
                  window.location.href = this.cash_link + "&amount=" + parseFloat(this.cashin_amount);
            },
        },
        template: `	
       <div ref="modal_cashin" class="modal" tabindex="-1" role="dialog" data-backdrop="static"  >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">      
        
          <div class="modal-body">      
            <a @click="close" href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>      
            
            <div class="d-flex justify-content-between align-items-center">
              <div><h4 class="m-0 mb-3 mt-3">{{label.add_to_balance}}</h4></div>
              <div class="text-muted font11">{{label.minimum_amount}}</div>
            </div>
            
            <p>{{label.how_much}}</p>
            
            <div  class="menu-categories medium mb-3 d-flex">
              <a v-for="(item, key) in amount_selection" 
              @click="cashin_amount=key"
              :class="{active:key==cashin_amount}"  
              class="text-center rounded align-self-center text-center">		    
                <h5 class="m-0">{{item}}</h5>
                <p class="m-0 mt-1 text-truncate">{{label.cashin}}</p>
              </a>		  
            </div>
            
            <div class="mt-1 mb-2">
                <div class="form-label-group">    
                   <input type="text" v-model="cashin_amount" id="cashin_amount"
                   placeholder=""
                   v-maska="'#*.##'" 
                   class="form-control form-control-text" >
                   <label for="cashin_amount">{{label.enter_amount}}</labe>
                 </div>
            </div>
            
                                                             
          </div>  <!-- modal body -->      
          
          <div class="modal-footer border-0">                            
          
            <button type="button" class="btn btn-black" data-dismiss="modal">
                <span class="pl-3 pr-3">{{label.cancel}}</span>
               </button>
                   
            <button type="button"  class="btn btn-green pl-4 pr-4" 
             :disabled="!hasData"
             @click="ContinueToPayment"
             >
              <span>{{label.continue}}</span>          
            </button>  
          
          </div> <!-- modal footer -->
          
        </div> <!--content-->
       </div> <!--dialog-->
       </div> <!--modal-->     	              
    
        `
    };
    
    /*
      COMMISSION
    */
    const app_commission = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,
           'components-merchant-balance': componentsMerchantBalance,	   
           'components-request-payout': ComponentsRequestPayout,
           'components-cash-in': ComponentsCashin,
        },		
        data(){
           return {		
                 is_loading : false,	   	  
                 balance : 0
           };
        },
        methods:{
            requestPayout(){
                this.$refs.payout.show();
            },
            afterRequestpayout(){			
                this.$refs.datatable.getTableData();
                this.$refs.balance.getGetMerchantBalance();
            },
            afterBalance($balance){			
                this.balance = $balance;
            },		
            showCashin(){    	  	 	  	
                   this.$refs.cashin.show();
              },  	  
        },
    });
    app_commission.use(Maska);
    const vm_commission = app_commission.mount('#vue-commission-statement');
    
    
    /*
      SET PAYOUT ACCOUNT
    */
    const componentsSetAccount = {
        props: ['ajax_url','label'],
        data(){
           return {			
                 is_loading : false,	   
                 payment_provider_list : [],	 	   	  
                 account_type : [],
                 payment_provider : '',
                 country_list : [],
                 email_address : '',
                 account_number : '',
                 account_holder_name : '',
                 account_holder_type : 'individual',
                 currency : '',
                 routing_number : '',
                 country : '',
                 error : [],
                 currency_list : [],		   	  
           };
        },    
        computed: {
            hasData(){			
                var $pass = true;
                if(empty(this.payment_provider)){
                    $pass = false;
                }			
                return $pass;
            },
        },
        mounted() {
            this.getPayoutSettings();
        },		
        methods: {
          show(){    	  	 	  	
                 $( this.$refs.modal_payout_account ).modal('show');
            },
            close(){  	  	 
                 $( this.$refs.modal_payout_account ).modal('hide');
            },  	 
            getPayoutSettings(){
                
                    this.is_loading = true;	
                axios({
                   method: 'put',
                   url: this.ajax_url +"/getPayoutSettings",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance;
                      if(response.data.code==1){		
                         this.payment_provider_list = response.data.details.provider;
                         this.country_list = response.data.details.country_list;
                         this.account_type = response.data.details.account_type;
                         this.currency_list = response.data.details.currency_list;
                         this.currency = response.data.details.default_currency;
                         this.country = response.data.details.default_country;
                      } else {
                          this.payment_provider_list = []
                          this.country_list = [];
                          this.currency_list = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
                
            },
            submit(){
                 
                 var $params = {
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                     payment_provider : this.payment_provider,
                 };  	 
                 
                 switch(this.payment_provider){
                      case "paypal":
                        $params.email_address = this.email_address;
                      break;
                      
                      case "stripe":
                        $params.account_number = this.account_number;
                        $params.account_holder_name = this.account_holder_name;
                        $params.account_holder_type = this.account_holder_type;
                        $params.currency = this.currency;
                        $params.routing_number = this.routing_number;
                        $params.country = this.country;
                      break;
                      
                      case "bank":
                        $params.full_name = this.full_name;
                        $params.billing_address1 = this.billing_address1;
                        $params.billing_address2 = this.billing_address2;
                        $params.city = this.city;
                        $params.state = this.state;
                        $params.post_code = this.post_code;
                        $params.country = this.country;
                        $params.account_name = this.account_name;
                        $params.account_number_iban = this.account_number_iban;
                        $params.swift_code = this.swift_code;
                        $params.bank_name = this.bank_name;
                        $params.bank_branch = this.bank_branch;  	  	 	   
                      break;
                 }  	  	
                 
                    this.error = [];
                    this.is_loading = true;	
                axios({
                   method: 'put',
                   url: this.ajax_url +"/SetPayoutAccount",
                   data : $params,
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance;
                      if(response.data.code==1){		
                         notify(response.data.msg);
                         this.close();
                         this.$emit('afterSave');	    		    			 	    
                      } else {
                          this.error = response.data.msg;			 	 	
                          location.href = "#error_message";   
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
                 
            },
        },
        template: `	
        <div ref="modal_payout_account" class="modal"
        id="modal_payout_account" data-backdrop="static" 
        tabindex="-1" role="dialog" aria-labelledby="modal_payout_account" aria-hidden="true">
        
           <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
             <div class="modal-content"> 
             
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              
             <div class="modal-body">
                      
             <form @submit.prevent="submit" >
                      
             <div class="menu-categories medium mb-3 d-flex">
                         
               <a v-for="(item, key) in payment_provider_list"  class="text-center"
               @click="payment_provider=item.payment_code"	      
               :class="{active:payment_provider==item.payment_code}"  
                class="rounded align-self-center text-center"
               >
               
               <template v-if="item.logo_type=='icon'">
                 <i :class="item.logo_class"></i>
               </template>
               <template v-else>
                 <img class="rounded lozad" 		        
                    :src="item.logo_image" 
                    class="rounded-pill lozad"
                 >    
               </template>
               
               <p class="m-0 mt-1 text-truncate">{{item.payment_name}}</p>
               </a>   
               
             </div>
            <!-- menu -->
              
              
              <!-- PAYPAL -->
              <div v-if="payment_provider==='paypal'">
                 <div class="form-label-group">    
                   <input type="text" v-model="email_address" id="email_address"
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="email_address">{{label.email_address}}</labe>
                 </div>
              </div>
              <!-- PAYPAL --> 
              
              <!-- STRIPE -->		  
              <div v-if="payment_provider==='stripe'">
                 <div class="form-label-group">    
                   <input type="text" v-model="account_number" id="account_number" 
                   v-maska="'#################'" 
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="account_number">{{label.account_number}}</labe>
                 </div>
                 
                 <div class="form-label-group">    
                   <input type="text" v-model="account_holder_name" id="account_holder_name"
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="account_holder_name">{{label.account_holder_name}}</labe>
                 </div>
                              
                 <select ref="account_holder_type" v-model="account_holder_type"
                 class="form-control custom-select form-control-select mb-3"  >		    
                 <option v-for="(account_type_name, account_type_key) in account_type" :value="account_type_key">{{account_type_name}}</option>		    
                 </select>
                              
                 <select ref="currency" v-model="currency"
                 class="form-control custom-select form-control-select mb-3"  >		    
                 <option v-for="(currency_name, currency_key) in currency_list" :value="currency_key">{{currency_name}}</option>		    
                 </select>
                              
                 <div class="form-label-group">    
                   <input type="text" v-model="routing_number" id="routing_number"
                   v-maska="'#################'" 
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="routing_number">{{label.routing_number}}</labe>
                 </div>
                 
                 <select ref="country" v-model="country"
                 class="form-control custom-select form-control-select mb-3"  >		    
                 <option v-for="(country_name, country_key) in country_list" :value="country_key">{{country_name}}</option>		    
                 </select>
                 
              </div>
              <!-- STRIPE -->
              
              <!-- BANK -->
              <div v-if="payment_provider==='bank'">
              
                 <h6>{{label.account_information}}</h6>      
                 
                 <div class="form-label-group">    
                   <input type="text" v-model="account_name" id="account_name"
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="account_name">{{label.account_name}}</labe>
                 </div>
                 
                 <div class="form-label-group">    
                   <input type="text" v-model="account_number_iban" id="account_number_iban"
                   v-maska="'#################'" 
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="account_number_iban">{{label.account_number_iban}}</labe>
                 </div>
                 
                 <div class="form-label-group">    
                   <input type="text" v-model="swift_code" id="swift_code"		       
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="swift_code">{{label.swift_code}}</labe>
                 </div>
                 
                 <div class="form-label-group">    
                   <input type="text" v-model="bank_name" id="bank_name"
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="bank_name">{{label.bank_name}}</labe>
                 </div>
                 
                  <div class="form-label-group">    
                   <input type="text" v-model="bank_branch" id="bank_branch"
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="bank_branch">{{label.bank_branch}}</labe>
                 </div>
                 
              </div>
              <!-- BANK --> 
              
              <div id="error_message" v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
               </div>      
              
              </div> <!-- body -->
              
               <div class="modal-footer">     
    
               <button type="button" class="btn btn-black" data-dismiss="modal">
                <span class="pl-3 pr-3">{{label.close}}</span>
               </button>
                   
                <button type="submit" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
                 :disabled="!hasData"
                 >
                  <span>{{label.submit}}</span>
                  <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                </button>
              </div>
             
              </form>
              </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->     	       
        
        `
    };
    
    
    /*
      withdrawals
    */
    const app_withdrawals = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,
           'components-merchant-balance': componentsMerchantBalance,
           'components-set-account': componentsSetAccount,
           'components-request-payout': ComponentsRequestPayout,
        },	
        data(){
           return {		
                 is_loading : false,	
                 payout_account : '',
                 provider : '',
                 balance : 0
           };
        },
        mounted() {
            this.getPayoutAccount();
        },		
        methods: {
            showAccountForm(){
                this.$refs.account.show();
            },
            afterSave(){		   
               this.getPayoutAccount();
            },
            afterBalance($balance){			
                this.balance = $balance;
            },
            getPayoutAccount(){
                
                this.is_loading = true;	
                axios({
                   method: 'put',
                   url: apibackend +"/getPayoutAccount",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			   	 
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance;
                      if(response.data.code==1){			
                          this.payout_account = response.data.details.account;		 	    
                          this.provider = response.data.details.provider;		 	    
                      } else {			 	 	
                          this.payout_account = '';
                          this.provider = '';
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
                
            },
            requestPayout(){
                this.$refs.payout.show();
            },
            afterRequestpayout(){			
                this.$refs.datatable.getTableData();
                this.$refs.balance.getGetMerchantBalance();
            },
        },
    });
    app_withdrawals.use(Maska);
    const vm_withdrawals = app_withdrawals.mount('#vue-withdrawals');
    
    
    /*
      ORDER HISTORY
    */
    const app_order_history = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,	   
        },	
        data(){
           return {		
                 is_loading : false,	   	  
           };
        },
        mounted(){
            this.getOrderSummary();
        },
        methods : {
            getOrderSummary(){
                
                this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: apibackend +"/getOrderSummary",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          var $options = {							
                            decimalPlaces : 0,
                            separator : ",",
                            decimal : "."
                        };					
                        var $summary_orders = new countUp.CountUp(this.$refs.summary_orders, response.data.details.orders , $options);	
                        $summary_orders.start();
                          
                        var $summary_cancel = new countUp.CountUp(this.$refs.summary_cancel,  response.data.details.order_cancel , $options);	
                        $summary_cancel.start();
                                         
                        var $options = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        };		   
                        var $summary_total = this.count_up = new countUp.CountUp(this.$refs.summary_total,  response.data.details.total , $options);	
                        $summary_total.start();
                        
                        var $total_refund = this.count_up = new countUp.CountUp(this.$refs.total_refund,  response.data.details.total_refund , $options);	
                        $total_refund.start();
                
                      } else {						 	 	
                          
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
                 
            },
        },
    });
    const vm_order_history = app_order_history.mount('#vue-order-history');	
    
    
    
    /*
       NOTIFICATIONS
    */
    /*const app_notifications = Vue.createApp({
        components: {
           'components-notification': ComponentsNotification, 	  	   
           'components-merchant-status': componentsMerchantStatus, 
        },		
    });
    const vm_notifications = app_notifications.mount('#vue-top-container');	
    */
    
    /*
      COMPONENTS WEB SETTINGS
    */
    const ComponentsWebPusher = {
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
           'components-web-pusher': ComponentsWebPusher, 	  	   
        },			
    });
    const vm_webpushsettings = app_webpushsettings.mount('#vue-webpush-settings');	
    
    
    /*
      LOADING BOX
    */
    const componentLoadingBox = {
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
           <div class="modal-dialog modal-dialog-centered modal-sm modal-loadingbox" role="document">
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
      COMPONENTS PLANT LIST
    */
    const componentsPlanlist = {
        props: ['label','ajax_url'],
        data(){
            return {		
             is_loading : false,		
             error : [],	  	   
             data : [],		
             package_uuid : '',
             current_package_uuid : '',
             payment_code : '',
             methods : '',
             agree : false,
             payment_list : [],
             payment_code : ''
           };
        },
        mounted() {
           this.getPlanList();	   
        },
        computed: {
            hasPlan(){			
                if(empty(this.package_uuid)){
                    return false;
                }
                if(this.current_package_uuid==this.package_uuid){
                    return false;
                }			
                return true;
            },
        },
        methods : {
          show(){    	  	 	  	      	 
                 $( this.$refs.modal_plan ).modal('show');
            },
            close(){  	  	 
                 $( this.$refs.modal_plan ).modal('hide');
            },
            getPlanList(){
                   this.is_loading = true;
                  axios({
                   method: 'POST',
                   url: this.ajax_url+"/getPlanList" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout ,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details.data;
                          this.current_package_uuid = response.data.details.package_uuid;
                          this.package_uuid = response.data.details.package_uuid;
                          this.payment_code = response.data.details.payment_code;
                      } else {						 	 				 	 	
                          this.data = [];
                          this.package_uuid = '';
                          this.payment_code = '';
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            },
            confirmPlan(){  	  	 
                 this.$emit('changePlan',this.package_uuid,this.payment_code);
            },  	 
        },
        template: `	
        <div ref="modal_plan" class="modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.subscription_plan}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <div class="modal-body">
          
           <div class="menu-categories medium mb-3 d-flex">
             <a v-for="(item, key) in data"  class="rounded align-self-center text-center"
             @click="package_uuid=item.package_uuid"	      
             :class="{active:package_uuid==item.package_uuid}"  
             >
             <h5 class="text-truncate">{{item.title}}</h5>
             <p class="m-0 mt-1 text-truncate">
             
             <template v-if="item.promo_price_raw>0" >
               {{item.promo_price}}
             </template>
             <template v-else >
               {{item.price}}
             </template>
             
             /{{item.package_period}}</p>         
             </a>
           </div>
                                                                                      
          </div>  <!-- modal body -->      
          
          <div class="modal-footer">        
                  
            <button type="button" class="btn btn-black" data-dismiss="modal">
              <span class="pl-3 pr-3">Cancel</span>
            </button>
                        
            <button type="button" @click="confirmPlan" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
             :disabled="!hasPlan"
             >
              <span>Continue</span>
              <div class="m-auto circle-loader" data-loader="circle-side"></div> 
            </button>
            
          </div> <!-- modal footer -->
          
        </div> <!--content-->
       </div> <!--dialog-->
       </div> <!--modal-->     	              
        `
    };
    
    
    /*
      MANAGE PLANS
    */
    const app_manage_plan = Vue.createApp({ 
      data(){
        return {				
           error : [],	
           payment_code : '',   		   
        };
      },
      created() {
         this.defaultPaymentGateway();
      },
      methods: {
           defaultPaymentGateway(){
               
                   axios({
                   method: 'POST',
                   url: apibackend +"/defaultPaymentGateway" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){						 	 	
                          this.payment_code = response.data.details.payment_code;
                      } else {						 	 				 	 	
                          this.payment_code = '';
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
                 
           },
           showPlan(methods){
               this.$refs.planlist.show(methods);
           }, 
           changePlan(package_uuid,payment_code){  	 	
               try {
                   this.$refs.planlist.close();  	 	
                   this.$refs[payment_code].changePlan(package_uuid);  	 	
               }catch(err){		  
                notify(err.message,'danger');
            }		
           },
           notify(message,message_type){
               notify(message,message_type);
           },
           showLoading(){
               this.$refs.box.show();  	 	
           },
           closeLoading(){
               this.$refs.box.close();  	 	
           },
           afterChangeplan(){
               this.$refs.merchant_status.merchantPlanStatus();
           },
           afterCancelplan(){
               this.$refs.merchant_status.merchantPlanStatus();
           },
           viewInvoice(invoice_number, payment_code){  	 	  	 	  	 	  	 	
               try {
                   this.$refs[this.payment_code].invoiceDetails(invoice_number);  	 	
               }catch(err){		  
                notify(err.message,'danger');
            }		
           },
           refreshDatatables(){
               this.$refs.datatable.getTableData();
           },
      },
    });
    
    app_manage_plan.component('components-datatable',ComponentsDataTables);
    app_manage_plan.component('components-planlist',componentsPlanlist);
    app_manage_plan.component('components-loading-box',componentLoadingBox);
    app_manage_plan.component('components-merchant-status',componentsMerchantStatus);
    
    if ((typeof  components_bundle !== "undefined") && ( components_bundle !== null)) {	
        $.each(components_bundle,function(components_name, components_value) {		
            app_manage_plan.component(components_name, components_value );
        });
    }
    const vm_manage_plan  = app_manage_plan.mount('#vue-manage-plan');
    
    
    /*
      CATEGORY AVAILABILITY 
    */
    const vm_availability = Vue.createApp({  
      data(){
          return {
            available_at_specific : null
          }
      },
      mounted() {  	
          this.available_at_specific = this.$refs.available_at_specific.value==1?true:false;
      },
      methods :{	
          
      },
    }).mount('#vue-availability');
    
    
    /* 
      COMPONENTS TAX
    */
    const ComponentsTax = {
        props: ['ajax_url','label','tax_in_price_list','tax_type'],
        data(){
           return {		
                 tax_uuid : 0, 
                 tax_name : '',
                 tax_in_price : 0,
                 tax_rate : 0,	   	  
                 active : true, 
                 default_tax : true,
                 error : [],
                 is_loading : false,
           };
        },
        methods:{		
          show(){    	  	 
               this.clearData();     	 
                 $( this.$refs.modal_tax ).modal('show');
            },
            close(){  	  	 
                 $( this.$refs.modal_tax ).modal('hide');
            },    	
            submit(){
             this.is_loading = true;
             this.error = [];
                 axios({
               method: 'PUT',
               url: this.ajax_url+"/saveTax" ,
               data : {
                     'tax_uuid' : this.tax_uuid,
                     'tax_name' : this.tax_name,
                     'tax_rate': this.tax_rate,
                     'default_tax': this.default_tax,
                     'active': this.active,
                     'tax_in_price' : this.tax_in_price,
                     'tax_type': this.tax_type,
                 'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			     
                  },
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){		
                      this.close();		 	 	
                      notify(response.data.msg,'success');			 	 	
                      this.$emit('afterSave');	    		    	 	 	
                  } else {						 	 				 	 	
                      this.error = response.data.msg;
                  }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_loading = false;
             });			  	  	
          },
          getTax(tax_uuid){
               this.show();
               this.is_loading = true;		 
                 axios({
               method: 'POST',
               url: this.ajax_url+"/getTax" ,
               data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&tax_uuid=" + tax_uuid ,
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){			
                      this.tax_uuid = response.data.details.tax_uuid;
                      this.tax_name = response.data.details.tax_name;
                      this.tax_in_price = response.data.details.tax_in_price;
                      this.tax_rate = response.data.details.tax_rate;
                      this.default_tax = response.data.details.default_tax;		 	 	
                      this.active = response.data.details.active;		 	 
                      
                      this.default_tax = this.default_tax==1?true:false;	
                      this.active = this.active==1?true:false;
                  } else {			
                      notify(data.msg,'danger');			 	 				 	 			 	 	
                  }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_loading = false;
             });			  	  
          },
          clearData(){
              this.tax_uuid = '';
              this.tax_name = '';
              this.tax_rate = 0;
              this.default_tax = true;
              this.active = true;
          },
          deleteTax(tax_uuid){
              
               bootbox.confirm({ 
                size: "small",
                title : "" ,
                message: '<h5>'+this.label.confirmation+'</h5>' + '<p>'+ this.label.content +'</p>',
                centerVertical: true,
                animate: false,
                buttons: {
                    cancel: {
                       label: this.label.cancel,
                       className: 'btn btn-black small pl-4 pr-4'
                    },
                    confirm: {
                        label: this.label.confirm,
                        className: 'btn btn-green small pl-4 pr-4'
                    },
                },
                callback: result => {				    	
                    if(result){
                        this.taxDelete(tax_uuid);
                    }
                }
            });			
              
          },
          taxDelete(tax_uuid){
                         
               this.is_loading = true;		 
                 axios({
               method: 'POST',
               url: this.ajax_url+"/taxDelete" ,
               data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&tax_uuid=" + tax_uuid ,
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){					 	 	
                      notify(response.data.msg,'success');			 	 	
                      this.$emit('afterSave');
                  } else {			
                      notify(data.msg,'danger');			 	 				 	 			 	 	
                  }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_loading = false;
             });			  	  
              
          },
        },
        template: `	
        
        <div ref="modal_tax" class="modal" tabindex="-1" role="dialog" data-backdrop="static"  > 
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
                
          <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
                <div>
                  <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
                </div>
            </div>
          
          <div class="modal-body">          
          
          <form @submit.prevent="submit" >
              <div class="form-group">
                <label for="tax_name">{{label.tax_name}}</label>
                <input v-model="tax_name" type="text" class="form-control form-control-text" id="tax_name" >		    
              </div>  
              
              
              <template v-if="tax_type=='standard'">
              <div v-if="default_tax" class="input-group mb-3">  
               <select  v-model="tax_in_price" class="custom-select form-control-select" id="tax_in_price">	
                <option v-for="(item, index) in tax_in_price_list" :value="index" >{{item}}</option>	    
               </select>
              </div>
              </template>
              
              <div class="form-group">
                <label for="tax_rate">{{label.rate}}</label>
                <input v-model="tax_rate" type="text" class="form-control form-control-text" id="tax_rate" >		    
              </div>  
                      
              
             <template v-if="tax_type=='standard' || tax_type=='euro'">
             <div class="row">
               <div class="col">
               
                 <div class="custom-control custom-switch">
                  <input v-model="default_tax" type="checkbox" class="custom-control-input" id="default_tax">
                  <label class="custom-control-label" for="default_tax">{{label.default_tax}}</label>
                </div>
               
               </div>
               <div class="col">
               
                 <div class="custom-control custom-switch">
                  <input v-model="active" type="checkbox" class="custom-control-input" id="active">
                  <label class="custom-control-label" for="active">{{label.active}}</label>
                </div> 
               
               </div>
             </div>
             </template>
             <template v-else>
               <div class="custom-control custom-switch">
                  <input v-model="active" type="checkbox" class="custom-control-input" id="active">
                  <label class="custom-control-label" for="active">{{label.active}}</label>
                </div> 
             </template>
             
          </form>
          
            <div v-if="error.length>0" class="alert alert-warning mb-2 mt-2" role="alert">
            <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
            </div>  
          
          </div> <!-- body -->
          
           <div class="modal-footer">
              <button type="buttton" class="btn btn-black" data-dismiss="modal" aria-label="Close" >
              <span class="pl-2 pr-2" >{{label.cancel}}</span>
              </button>
              
              <button type="button" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"           
              >
              <span>{{label.save}}</span>
              <div class="m-auto circle-loader" data-loader="circle-side"></div> 
            </button>
          </div>
          
          </div>
         </div>
         </div>      
        `
    };
    
    const app_tax = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,	   
           'components-tax': ComponentsTax,	   
        },		
        data(){
           return {		
                 data :[]
           };
        },
        mounted() {    	
            this.$refs.datatable.transaction_type = this.$refs.tax.tax_type;
        },
        methods:{		
            newTax(){
                this.$refs.tax.show();
            },    	
            afterSave(){
                this.$refs.datatable.getTableData();
            },
            editTax(tax_uuid){
                this.$refs.tax.getTax(tax_uuid);
            },
            deleteTax(tax_uuid){
                this.$refs.tax.deleteTax(tax_uuid);
            }
        },
        
    });
    const vm_tax = app_tax.mount('#vue-tax');    
    
    
    /* 
       VUE MAIN APP 
    */
    const app_top_nav = Vue.createApp({	
        components: {
           'components-notification': ComponentsNotification, 	  	   
           'components-merchant-status': componentsMerchantStatus, 
           'components-pause-order': ComponentsPauseOrder, 
           'components-pause-modal': ComponentsPauseModal,       
           'components-resume-order-modal': ComponentsResumeOrder,  
       },	
       mounted() {	   	     	     	     	  
             //this.getOrdersCount();
       },
       data(){
         return {						
            data : [],
            is_load : false,
         };
       },
       methods: {
             getOrdersCount(){
                  axios({
               method: 'POST',
               url: apibackend +"/getOrdersCount",
               data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){
                                                                
                      if(this.is_load){
                          $( this.$refs.orders_new ).find(".badge-notification").remove();
                      }
                                        
                      this.data = response.data.details;
                      if (this.data.not_viewed>0){
                          $( this.$refs.orders_new ).append('<div class="blob green badge-pill pull-right badge-notification bg-new">'+this.data.new_order+'</div>');
                      } else {
                          $( this.$refs.orders_new ).append('<div class="badge-pill pull-right badge-notification bg-new">'+this.data.new_order+'</div>');
                      }
                      $( this.$refs.orders_processing ).append('<div class="badge-pill pull-right badge-notification bg-processing">'+this.data.order_processing+'</div>');
                      $( this.$refs.orders_ready ).append('<div class="badge-pill pull-right badge-notification bg-ready">'+this.data.order_ready+'</div>');
                      $( this.$refs.orders_completed ).append('<div class="badge-pill pull-right badge-notification bg-completed">'+this.data.completed_today+'</div>');		 	 
                      $( this.$refs.orders_scheduled ).append('<div class="badge-pill pull-right badge-notification bg-scheduled">'+this.data.scheduled+'</div>');
                      $( this.$refs.orders_history ).append('<div class="badge-pill pull-right badge-notification bg-history">'+this.data.all_orders+'</div>');
                  }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_load = true;
             });			
             },
             afterClickpause($accepting_order){
             dump($accepting_order);    
             if($accepting_order){
                 this.$refs.pause_modal.show();
             } else {     
                 this.$refs.resume_order.show();
             }     	
         },
         afterPause($data){
            this.$refs.pause_order.updateStatus($data);
         },
       },
    });
    const vm_top_nav = app_top_nav.mount('#vue-top-nav');	
    
    
    /*
      COMPONENTS LATEST ORDERS
    */
    const ComponentsLastOrders = {
        props: ['ajax_url' , 'label', 'orders_tab','limit'],
        data(){
           return {						
             data : [],
             active_tab : 'all',
             is_loading : false,		 
             data_failed : []
           };
        },   
        mounted() {	   	     	     	     	  
               this.getLastOrder(false);
               setInterval(() => this.getLastOrder(true) , 60000);
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
            setTab(tab){
                this.active_tab = tab;
                this.getLastOrder();
            },
            getLastOrder(silent){
                if(!silent){  
                  this.is_loading = true;    	
                }
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/getLastTenOrder" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&filter_by=" + this.active_tab + "&limit=" + this.limit ,
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){						 	 	
                          this.data = response.data.details;
                      } else {						 	 				 	 	
                          this.data = [];
                          this.data_failed = response.data;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {		
                      if(!silent){  	     
                        this.is_loading = false;
                      }
                 });			
            },
            viewCustomer(client_id){    		
                this.$emit('viewCustomer',client_id);	    		    
            },
        },
        template: `	
        
         <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
        
        <div class="card ">
        <div class="card-body">
         
        <div class="row align-items-center">
            <div class="col col-lg-6 col-md-6 col-9">        
              <h5 class="m-0">{{label.title}}</h5>   
              <p class="m-0 text-muted">{{label.sub_title}}</p>        
            </div>
            <div class="col col-lg-6 col-md-6 col-3 ">
    
              <div class="d-none d-sm-block">
              <ul class="nav nav-pills justify-content-md-end justify-content-sm-start">			  
                  <li v-for="(item, key) in orders_tab" class="nav-item">
                    <a @click="setTab(key)" :class="{active : active_tab==key}" class="nav-link py-1 px-3">{{item}}</a>
                  </li>			  
              </ul>
              </div>
    
              <div class="d-block d-sm-none text-right">
              
              <div class="dropdown btn-group dropleft">
              <button class="btn btn-sm dropdown-togglex dropleft" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="zmdi zmdi-more-vert"></i>
              </button>
               <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                 <template v-for="(item, key) in orders_tab" >
                 <a class="dropdown-item"  @click="setTab(key)" :class="{active : active_tab==key}"  >{{item}}</a>			
                 </template>
                </div>
              </div>
    
              </div> <!-- small -->
    
            </div>
          </div>  
          <!--row-->	
                     
        
         <div class="mt-3 table-orders table-responsive">
             <table class="table">
              <thead>
               <tr>
                 <th class="p-0 mw-200"></th>
                 <th class="p-0 mw-200"></th>
                 <th class="p-0 mw-200"></th>
                 <th class="p-0 mw-200"></th>
                 <th class="p-0 mw-200"></th>
               </tr>
              </thead>
              <tbody>
              <tr v-for="item in data">
                 <td class="pl-0 align-middle">             
                 
                     <div class="d-flex align-items-center">
                        <div class="mr-2">
                          <div  v-if="item.is_view==0" class="blob green mb-1"></div>
                          <div  v-if="item.is_critical==1" class="blob red"></div>
                        </div>
                        <div>
                           <div><a :href="item.view_order" class="font-weight-bold hover-text-primary mb-1">{{item.order_id}}</a></div>
                           <div><a @click="viewCustomer(item.client_id)" class="text-muted font-weight-bold hover-text-primary" href="javascript:;">{{item.customer_name}}</a></div>
                           <div class="text-muted font11">{{item.date_created}}</div>
                        </div>
                     </div>
                      
                    <!--- <div>                    
                        <a @click="viewCustomer(item.client_id)" class="text-muted font-weight-bold hover-text-primary" href="javascript:;">{{item.customer_name}}</a>
                    </div> -->
                </td>
               <td class="text-right align-middle">
                    <span class="font-weight-bold d-block">{{item.total}}</span>
                    <span class="badge payment"  :class="item.payment_status_raw" >{{item.payment_status}}</span>
                </td> 
                <td class="text-right align-middle">
                    <span class="text-muted font-weight-500">{{item.payment_code}}</span>
                </td>
                <td class="text-right align-middle">
                    <span class="badge order_status " :class="item.status_raw">{{item.status}}</span>
                </td>
                <td class="text-right align-middle pr-0">
                    <a :href="item.view_order" class="btn btn-sm text-muted btn-light hover-bg-primary hover-text-secondary py-1 px-3 mr-2">
                        <i class="zmdi zmdi-eye"></i>
                    </a>
                    <a :href="item.print_pdf" target="_blank" class="btn btn-sm text-muted btn-light hover-bg-primary hover-text-secondary py-1 px-3">
                        <i class="zmdi zmdi-download"></i>
                    </a>
                </td>
              </tr>
              </tbody>
             </table>
         </div>          
         
         <div v-if="!hasData" class="fixed-height40 text-center justify-content-center d-flex align-items-center">
             <div class="flex-col">
              <img v-if="data_failed.details" class="img-300" :src="data_failed.details.image_url" />
              <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
             </div>     
          </div>  
         
         </div><!-- card body -->
        </div> <!--card-->
        `
    };
    
    
    /*
      COMPONENTS ITEM CHART
    */
    const ComponentsItemSales = {
        props: ['ajax_url' , 'label' ,'period'],
        data(){
           return {						
             data : [],		 
             data_items : [],
             data_failed : [],
             is_loading : false,
             code : null
           };
        },   
        mounted() {	   	     	     	     	  
               this.itemSales();
        },
        methods: {
             itemSales(){
                 this.is_loading = true;     	
                 axios({
                   method: 'POST',
                   url: this.ajax_url +"/itemSales",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&period=" + this.period  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      this.code = response.data.code;
                      if(response.data.code==1){		
                          this.data = response.data.details.sales;			 	 			 	 
                          this.data_items = response.data.details.items;
                          this.initChart();
                      } else {		 	 	
                          this.data = [];
                          this.data_items = [];
                          this.data_failed = response.data;
                          this.is_loading = false;
                      }
                 }).catch(error => {	
                    this.is_loading = false;
                 }).then(data => {			     			     
                     
                 });			     		
             },
             initChart(){     		     		
                 if (window.Highcharts == null) {
                     new Promise((resolve) => {                
                      const doc = window.document;
                      const scriptId = "chart-script";
                      const scriptTag = doc.createElement("script");
                      scriptTag.id = scriptId;
                      scriptTag.setAttribute("src", "https://code.highcharts.com/highcharts.js");
                      doc.head.appendChild(scriptTag);                
                      
                      scriptTag.onload = () => {				     
                         resolve(); 
                      };		
                    }).then(() => {
                      this.renderChart();
                    });       	   	   
                } else {
                   this.renderChart();
                }              	   		
             },     	
             renderChart(){     	     		
                 Highcharts.chart( this.$refs.chart, {
                    chart: {
                        type: 'column',
                        height: '60%',
                        events: {
                             load: ()=> {			        	 				        	 	
                                 setTimeout(()=>{ 		
                                    this.is_loading = false;
                                 },500);           
                             }
                        },
                    },
                    title: {
                        text: ''
                    },    
                    xAxis: {
                        categories: this.data.category,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0"></td>' +
                            '<td style="padding:0"><b>{point.y:.1f} '+ this.label.sold +'</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'Sales',
                        showInLegend: false,      
                        color:"#3ecf8e",
                        data: this.data.data
                
                    }]
                });
                 
             },
        },
        template: `	
        
        <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
             
        <div class="card ">
        <div class="card-body">
               
            <div ref="chart"></div>
                    
            <div class="row ml-2 mt-4">
              <div v-for="item in data_items" class="col-md-4 mb-2">
                <div class="d-flex">
                  <div class="mr-1 w-25"><b>{{item.total_sold}}</b></div>
                  <div>{{item.item_name}}</div>
                </div>
              </div> <!-- col -->
            </div> <!-- row -->
            
            <div v-if="code==2" class="fixed-height40 text-center justify-content-center d-flex align-items-center">
                <div class="flex-col">
                 <img v-if="data_failed.details" class="img-300" :src="data_failed.details.image_url" />
                 <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
                </div>     
             </div> 
      
        </div> <!--card-body-->
        </div> <!--card--> 
        
        `
    };
    
    /*
      COMPONENTS POPULAR ITEMS
    */
    const ComponentsPopularItems = {
        components: {
           'components-item-sales': ComponentsItemSales,	
       },	 
        props: ['ajax_url' , 'label','limit' ,'item_tab'],
        data(){
           return {						
             data : [],		 		  
             is_loading : false,
             data_failed : [],
             currentTab : 'item_overview'
           };
        },   
        mounted() {	   	     	     	     	  
               this.mostPopularItems();
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
            mostPopularItems(){    	
                this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/mostPopularItems",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&limit=" + this.limit  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;			 	 			 	 
                      } else {		 	 	
                          this.data = [];
                          this.data_failed = response.data;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			     		
            },
        },
        template: `	    
        
         <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
         
        <div class="card">
        <div class="card-body">
        
        <div class="row">
            <div class="col col-lg-6 col-md-6 col-9">        
            <h5 v-if="item_tab[currentTab]" class="m-0">{{item_tab[currentTab].title}}</h5>   
            <p  v-if="item_tab[currentTab]" class="m-0 text-muted">{{item_tab[currentTab].sub_title}}</p>        
            </div>
    
            <div class="col col-lg-6 col-md-6 col-3">        
            <div class="d-none d-sm-block">
            <ul class="nav nav-pills justify-content-end">			  
                <li v-for="(item, key) in item_tab" class="nav-item">
                    <a @click="currentTab=key"  :class="{active : currentTab==key}" class="nav-link py-1 px-3">{{item.title}}</a>
                </li>			  
            </ul>
            </div>
    
            <div class="d-block d-sm-none text-right">
            
            <div class="dropdown btn-group dropleft">
            <button class="btn btn-sm dropdown-togglex dropleft" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="zmdi zmdi-more-vert"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <template v-for="(item, key) in item_tab"  >
                <a class="dropdown-item" @click="currentTab=key"  :class="{active : currentTab==key}"  >{{item.title}}</a>			
                </template>
                </div>
            </div>
    
            </div>
            <!-- dropdown -->
    
            </div>
        </div>  
        <!--row-->	
                          
          
          <template v-if="currentTab=='item_overview'">
          <div class="mt-3 table-item table-responsive">
            <table class="table">
              <thead>
               <tr>
                 <th class="p-0 mw-200"></th>
                 <th class="p-0 mw-200"></th>             
               </tr>
              </thead>
              <tbody>
                <tr v-for="item in data">
                 <td class="text-left align-middle">
                   <div class="d-flex  align-items-center"> 
                     <div class="mr-3">
                        <a :href="item.item_link"><img :src="item.image_url" class="img-60 rounded-circle"></a>
                     </div>
                     <div class="flex-col">
                        <a :href="item.item_link" class="font-weight-bold hover-text-primary mb-1">{{item.item_name}}</a>
                        <p class="m-0 text-muted">{{item.category_name}}</p>
                     </div>
                   </div> <!--flex-->
                 </td>
                 <td class="text-right align-middle">
                   <p class="m-0 text-muted">{{item.total_sold}}</p>
                 </td>
                 </tr>            
              </tbody>
            </table>
           </div> 
           
            <div v-if="!hasData" class="fixed-height40 text-center justify-content-center d-flex align-items-center">
                <div class="flex-col">
                 <img v-if="data_failed.details" class="img-300" :src="data_failed.details.image_url" />
                 <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
                </div>     
             </div>         
           
           </template>
           
           <template v-else>
           <components-item-sales
             :ajax_url="ajax_url"
             period="30"
             :label="label"
           >
           </components-item-sales>
           </template>            
                    
        </div> <!--card body-->
       </div> 
       <!--card-->
        
        `
    };
    
    /*
      COMPONENTS POPULAR CUSTOMER
    */
    const ComponentsPopularCustomer = {
        props: ['ajax_url' , 'label' ,'limit'],
        data(){
           return {						
             data : [],		 
             is_loading : false,
             data_failed : [],
           };
        },   
        mounted() {	   	     	     	     	  
               this.mostPopularCustomer();
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
             mostPopularCustomer(){
                this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/mostPopularCustomer",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&limit=" + this.limit  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;			 	 			 	 
                      } else {		 	 	
                          this.data = [];
                          this.data_failed = response.data;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		
             },
             viewCustomer(client_id){    		
                this.$emit('viewCustomer',client_id);	    		    
            },
        },
        template: `	
         
         <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
         
          <div class="card">
            <div class="card-body">
               <h5 class="m-0 mb-3">{{label.title}}</h5>    
               
                <table class="table">
                  <thead>
                   <tr>
                     <th class="p-0 mw-200"></th>
                     <th class="p-0 mw-200"></th>             
                   </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in data">
                     <td class="text-left align-middle">
                       <div class="d-flex  align-items-center"> 
                         <div class="mr-3">
                           <a @click="viewCustomer(item.client_id)">
                           <img :src="item.image_url" class="img-60 rounded-circle">
                           </a>
                         </div>
                         <div class="flex-col">
                            <a @click="viewCustomer(item.client_id)" href="javascript:;" class="font-weight-bold hover-text-primary mb-1">{{item.first_name}} {{item.last_name}}</a>
                            <div><small class="text-muted">{{item.member_since}}</small></div>
                         </div>
                       </div> <!--flex-->
                     </td>
                     <td class="text-right align-middle">
                       <p class="m-0 text-muted">{{item.total_sold}}</p>
                     </td>
                     </tr>             
                  </tbody>
                </table>
                
                <div v-if="!hasData" class="fixed-height15 text-center justify-content-center d-flex align-items-center">
                    <div class="flex-col">			     
                     <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
                    </div>     
                 </div>  
               
            </div> <!--card body-->
           </div> 
           <!--card-->
        `
    };
    
    /*
      COMPONENTS CHARTS SALES
    */
    const ComponentsChartSalesOverview = {
        props: ['ajax_url' , 'label' ,'limit','months'],
        data(){
           return {						
             data : [],		 
             is_loading : false,		 
             data_failed : [],
             code : null
           };
        },   
        mounted() {	   	     	     	     	  
               this.salesOverview();
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
             salesOverview(){     		
                 this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/salesOverview",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&months=" + this.months  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      this.code = response.data.code;
                      if(response.data.code==1){		
                          this.data = response.data.details;
                          this.initChart();
                      } else {		 	 	
                          this.data = [];
                          this.is_loading = false;			 	 	
                          this.data_failed = response.data;
                      }			 	 
                 }).catch(error => {	
                    this.is_loading = false;
                 }).then(data => {			     
                     
                 });		
             },
             initChart(){     		
                 if (window.Highcharts == null) {
                     new Promise((resolve) => {                
                      const doc = window.document;
                      const scriptId = "chart-script";
                      const scriptTag = doc.createElement("script");
                      scriptTag.id = scriptId;
                      scriptTag.setAttribute("src", "https://code.highcharts.com/highcharts.js");
                      doc.head.appendChild(scriptTag);                
                      
                      scriptTag.onload = () => {				     
                         resolve(); 
                      };		
                    }).then(() => {
                      this.renderChart();
                    });       	   	   
                } else {
                   this.renderChart();
                }              	   		
             },     	
             renderChart(){     		
                 Highcharts.chart( this.$refs.chart_sales, {
                    chart: {
                        type: 'column',
                        height: (18 / 16 * 100) + '%',
                        events: {
                             load: ()=> {			        	 				        	 	
                                 setTimeout(()=>{ 		
                                    this.is_loading = false;
                                 },500);           
                             }
                        },
                    },
                    title: {
                        text: ''
                    },    
                    xAxis: {
                        categories: this.data.category,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} '+ this.label.sales +'</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: 'Sales',
                        showInLegend: false,      
                        data: this.data.data
                
                    }]
                });
                 
             },
        },
        template:`	
         <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>       	
         
        <div class="card mb-3">
        <div class="card-body">
        
           <h5 class="m-0 mb-3">{{label.sales_overview}}</h5>    
            
            <div ref="chart_sales"></div>
                    
            <div v-if="code==2" class="fixed-height15 text-center justify-content-center d-flex align-items-center">
                <div class="flex-col">
                 <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
                </div>     
             </div> 
      
        </div> <!--card-body-->
        </div> <!--card--> 
        `
    };
    
    
    /*
      COMPONENTS LATEST REVIEW
    */
    const ComponentsLatestReview = {
        props: ['ajax_url' , 'label' ,'limit'],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        mounted() {	   	     	     	     	  
               this.latestReview();
        },
        methods: {
             latestReview(){
                 
                 this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/OverviewReview",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&limit=" + this.limit  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;			 	 	
                      } else {		 	 	
                          this.data = [];			 	 	
                      }			 	 
                 }).catch(error => {	
                    
                 }).then(data => {			     
                    this.is_loading = false; 
                 });		
                 
             },
             viewCustomer(client_id){
                      this.$emit('viewCustomer',client_id);
               },
        },
        template: `	
          <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
               
          <div class="card">
            <div class="card-body">
            
               <h5 class="m-0 mb-3">{{label.title}}</h5>   
                           
               <h2 class="font-medium mt-2 mb-0">{{data.total}}</h2>
               <span class="text-muted">{{data.this_month_words}}</span>
               
               <div class="image-box mt-3 mb-3"> 
                 <a v-for="user in data.user"  @click="viewCustomer(user.client_id)" class="mr-2" data-toggle="tooltip" data-placement="top" :title="user.first_name" :data-original-title="user.first_name">
                   <img :src="user.image_url" class="rounded-circle img-40" alt="user">
                 </a> 	         
               </div>
               
               <div class="graph-rating-body mb-3">	        
               
                <div v-for="item in data.review_summary" class="rating-list mb-1">  
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-col font11">{{item.count}} {{label.star}}</div>
                    <div class="flex-col font11">{{item.in_percent}}</div>
                  </div>
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" 
                    :style="{ width: item.review + '%' }"
                    :aria-valuenow="item.review" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div> <!-- rating-list -->
                            
               </div> <!-- graph-rating-body -->
                          
              
              <a :href="data.link_to_review" class="w-100 btn btn-lg btn-info waves-effect waves-light mb-3">
              {{label.all_review}}
              </a> 	      
               
           </div> <!--card body-->
          </div> <!--card-->      
        `
    };
    
    
    /* 
      COMPONETS SALES OVERVIEW
    */
    const ComponentsSalesSummary = {
        props: ['ajax_url' , 'label' ,'merchant_type' ],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        mounted() {	   	     	    
            this.salesSummary();
        },
        methods: {
             salesSummary(){
                 
                 axios({
                   method: 'POST',
                   url: this.ajax_url+"/salesSummary" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance, $sales_week, $earning_week;
                      if(response.data.code==1){						 	 	
                          $balance = this.count_up = new countUp.CountUp(this.$refs.your_balance, response.data.details.balance ,  {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        });	
                        $balance.start();
                        
                        $sales_week = this.count_up = new countUp.CountUp(this.$refs.sales_this_week, response.data.details.sales_week ,  {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        });	
                        $sales_week.start();
                        
                        $earning_week = this.count_up = new countUp.CountUp(this.$refs.earning_this_week, response.data.details.earning_week ,  {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        });	
                        $earning_week.start();
                        
                      } else {				 	 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });		
             },
        },
        template: `	    
        <div class="row">
            <div class="col mb-3 mb-xl-0">
              <div class="card">
                <div class="card-body" style="padding:10px;">
                
                  <div id="boxes" class="d-flex align-items-center">
                    <div class="mr-2"><div class="rounded box box-1 d-flex align-items-center  justify-content-center"><i class="zmdi zmdi-money-box"></i></div></div>
                    <div>
                       <h6 class="m-0 text-muted font-weight-normal">{{label.sales_this_week}}</h6>
                       <h6 class="m-0 position-relative" ref="sales_this_week">0
                          <div class="skeleton-placeholder" style="height:17px;width:100%;"></div>
                       </h6>
                    </div>
                  </div><!--flex-->       
                  
                </div> <!--card body-->       
              </div> <!--card-->
            </div> <!-- col -->
            
            <div class="col mb-3 mb-xl-0">
              <div class="card">
                <div class="card-body" style="padding:10px;">
                
                  <div id="boxes" class="d-flex align-items-center">
                    <div class="mr-2"><div class="rounded box box-2 d-flex align-items-center  justify-content-center"><i class="zmdi zmdi-money-box"></i></div></div>
                    <div>
                       <h6 class="m-0 text-muted font-weight-normal">{{label.earning_this_week}}</h6>
                       <h6 class="m-0 position-relative" ref="earning_this_week">0
                         <div class="skeleton-placeholder" style="height:17px;width:100%;"></div>
                       </h6>
                    </div>
                  </div><!--flex-->       
                  
                </div> <!--card body-->       
              </div> <!--card-->
            </div> <!-- col -->
            
             <div class="col mb-3 mb-xl-0">
              <div class="card">
                <div class="card-body" style="padding:10px;">
                
                  <div id="boxes" class="d-flex align-items-center">
                    <div class="mr-2"><div class="rounded box box-3 d-flex align-items-center  justify-content-center"><i class="zmdi zmdi-money"></i></div></div>
                    <div>
                       <h6 class="m-0 text-muted font-weight-normal">{{label.your_balance}}</h6>
                       <h6 class="m-0 position-relative" ref="your_balance">0
                          <div class="skeleton-placeholder" style="height:17px;width:100%;"></div>
                       </h6>
                    </div>
                  </div><!--flex-->       
                  
                </div> <!--card body-->       
              </div> <!--card-->
            </div> <!-- col -->
            
          </div> <!--row--> 
        `	
    };
    
    /*
      COMPONENTS DAILY STATISTIC
    */
    const ComponentsDailyStatistic = {
        props: ['ajax_url' , 'label' ,'limit'],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        mounted() {	   	     	     	     	  
               this.DailyStatistic();
        },
        methods: {
             DailyStatistic(){
                 this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/DailyStatistic",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;	
    
                          var $options = {							
                            decimalPlaces : 0,
                            separator : ",",
                            decimal : "."
                        };					
                        
                        var $count_up = new countUp.CountUp(this.$refs.stats_order_received,  response.data.details.order_received , $options);	
                        $count_up.start();
                        
                        $count_up = this.count_up = new countUp.CountUp(this.$refs.stats_today_delivered,  response.data.details.today_delivered , $options);	
                        $count_up.start();		
                                                                           
                        var $options = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        };		   
                        
                        $count_up = this.count_up = new countUp.CountUp(this.$refs.stats_today_sales,  response.data.details.today_sales , $options);	
                        $count_up.start();		
                        
                        $count_up = this.count_up = new countUp.CountUp(this.$refs.stats_total_refund,  response.data.details.total_refund , $options);	
                        $count_up.start();		
                                            
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
        template: `	
        
          <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
        
        <div class="row mb-3">
              <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                         <div class="flex-col mr-3"><h1 class="m-0"><i class="zmdi zmdi-store"></i></h1></div>
                         <div class="flex-col">
                           <h3 class="mb-1 text-danger" ref="stats_order_received">0</h3>
                           <h5 class="m-0 text-secondary">{{label.order_received}}</h5>
                         </div>
                      </div>
                    </div>
                  </div>
              </div> <!--col-->
    
               <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                         <div class="flex-col mr-3"><h1 class="m-0"><i class="zmdi zmdi-truck"></i></h1></div>
                         <div class="flex-col">
                           <h3 class="mb-1 text-green" ref="stats_today_delivered">0</h3>
                           <h5 class="m-0 text-secondary">{{label.today_delivered}}</h5>
                         </div>
                      </div>
                    </div>
                  </div>
              </div> <!--col-->
              
            </div> <!--row-->
            
          </div> <!--relative-->
          
           <div class="dashboard-statistic position-relative mb-3">
            
            <div class="row">
              <div class="col  mb-3 mb-xl-0">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                         <div class="flex-col mr-3"><h1 class="m-0"><i class="zmdi zmdi-money-box"></i></h1></div>
                         <div class="flex-col">
                           <h3 class="mb-1 text-violet" ref="stats_today_sales">0</h3>
                           <h5 class="m-0 text-secondary">{{label.today_sales}}</h5>
                         </div>
                      </div>
                    </div>
                  </div>
              </div> <!--col-->
    
               <div class="col  mb-3 mb-xl-0">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                         <div class="flex-col mr-3"><h1 class="m-0"><i class="zmdi zmdi-money-box"></i></h1></div>
                         <div class="flex-col">
                           <h3 class="mb-1 text-orange" ref="stats_total_refund">0</h3>
                           <h5 class="m-0 text-secondary">{{label.total_refund}}</h5>
                         </div>
                      </div>
                    </div>
                  </div>
              </div> <!--col-->
              
            </div> <!--row-->
        
        `
    };
    
    
    /*
      VUE DASHBOARD
    */
    const app_dashboard = Vue.createApp({	   
       components: {
           'components-last-orders': ComponentsLastOrders,
           'components-customer-details': ComponentsCustomer, 
           'components-popular-items': ComponentsPopularItems, 
           'components-popular-customer': ComponentsPopularCustomer, 
           'components-chart-sales': ComponentsChartSalesOverview, 
           'components-latest-review': ComponentsLatestReview, 
           'components-sales-summary': ComponentsSalesSummary, 
           'components-daily-statistic': ComponentsDailyStatistic,
       },	 
       mounted() {	   	     	     	     	  
             this.getMerchantSummary();
       },
       data(){
         return {						
            data : [],
            is_load : false,
            client_id : null,
         };
       },   
       methods: {
             getMerchantSummary(){
                 
                 this.is_loading = true;	
            axios({
               method: 'POST',
               url: apibackend +"/getOrderSummary",
               data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){					 	 	
                      var $options = {							
                        decimalPlaces : 0,
                        separator : ",",
                        decimal : "."
                    };					
                    var $summary_orders = new countUp.CountUp(this.$refs.summary_orders, response.data.details.orders , $options);	
                    $summary_orders.start();
                      
                    var $summary_cancel = new countUp.CountUp(this.$refs.summary_cancel,  response.data.details.order_cancel , $options);	
                    $summary_cancel.start();
                                     
                    var $options = {							
                        decimalPlaces : response.data.details.price_format.decimals,
                        separator : response.data.details.price_format.thousand_separator,
                        decimal :  response.data.details.price_format.decimal_separator,
                        prefix : response.data.details.price_format.symbol,
                    };		   
                    var $summary_total = this.count_up = new countUp.CountUp(this.$refs.summary_total,  response.data.details.total , $options);	
                    $summary_total.start();
                    
                    var $total_refund = this.count_up = new countUp.CountUp(this.$refs.total_refund,  response.data.details.total_refund , $options);	
                    $total_refund.start();
            
                  } else {						 	 	
                      
                  }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_loading = false;
             });			 
                 
             },
             viewCustomer(client_id){
                  this.client_id = client_id;   	  	 
                  setTimeout(()=>{ 		
                this.$refs.customer.show();
             },1);           
             },
             refreshLastOrder(){
                 this.$refs.last_order.getLastOrder();
                 this.$refs.daily_statistic.DailyStatistic();
             },
             refreshDailyStatistic(){
                 this.$refs.daily_statistic.DailyStatistic();
             },
       },
    });
    const vm_dashboard = app_dashboard.mount('#vue-dashboard');	
    
    
    
    /*
      VUE TABLES
    */
    const app_tables = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,	   	   
        },		
        data(){
           return {		
                 is_loading : false,		   	  
           };
        },
        methods:{		
        },
    });
    const vm_tables = app_tables.mount('#vue-tables');
    
    
    /*
       REPORT SALES SUMMARY CHART
    */
    /*
      COMPONENTS ITEM CHART
    */
    const ComponentsReportSalesChart = {
        props: ['ajax_url' , 'label'],
        data(){
           return {						
             data : [],		 
             data_items : [],
             data_failed : [],
             is_loading : false,
             code : null,
             period : null
           };
        },   
        mounted() {	   	     	     	     	  
               this.itemSales();
        },
        methods: {
             itemSales(){
                 this.is_loading = true;     	
                 axios({
                   method: 'POST',
                   url: apibackend +"/itemSalesSummary",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&period=" + this.period  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      this.code = response.data.code;
                      if(response.data.code==1){		
                          this.data = response.data.details.sales;			 	 			 	 
                          this.data_items = response.data.details.items;
                          this.initChart();
                      } else {		 	 	
                          this.data = [];
                          this.data_items = [];
                          this.data_failed = response.data;
                          this.is_loading = false;
                      }
                 }).catch(error => {	
                    this.is_loading = false;
                 }).then(data => {			     			     
                     
                 });			     		
             },
             initChart(){     		     		
                 if (window.Highcharts == null) {
                     new Promise((resolve) => {                
                      const doc = window.document;
                      const scriptId = "chart-script";
                      const scriptTag = doc.createElement("script");
                      scriptTag.id = scriptId;
                      scriptTag.setAttribute("src", "https://code.highcharts.com/highcharts.js");
                      doc.head.appendChild(scriptTag);                
                      
                      scriptTag.onload = () => {				     
                         resolve(); 
                      };		
                    }).then(() => {
                      this.renderChart();
                    });       	   	   
                } else {
                   this.renderChart();
                }              	   		
             },     	
             renderChart(){     	     	     		
                 Highcharts.chart( this.$refs.chart, {
                    chart: {
                        type: 'column',
                        height: '60%',
                        events: {
                             load: ()=> {			        	 				        	 	
                                 setTimeout(()=>{ 		
                                    this.is_loading = false;
                                 },500);           
                             }
                        },
                    },
                    title: {
                        text: ''
                    },    
                    xAxis: {
                        categories: this.data.category,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: ''
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0"></td>' +
                            '<td style="padding:0"><b>{point.y:.1f} '+ this.label.sold +'</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },			    
                    colors: this.data.colors,
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0,
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Sales',
                        showInLegend: false,      
                        color:"#3ecf8e",
                        data: this.data.data
                
                    }]
                });
                 
             },
        },
        template: `	
        
        <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>   
        
        <div class="card ">
        <div class="card-body">
           
            <div ref="chart"></div>
                    
            <div class="row ml-2 mt-4">
              <div v-for="item in data_items" class="col-md-4 mb-2">
                <div class="d-flex">
                  <div class="mr-1 w-25"><b>{{item.total_sold}}</b></div>
                  <div>{{item.item_name}}</div>
                </div>
              </div> <!-- col -->
            </div> <!-- row -->
            
            <div v-if="code==2" class="fixed-height40 text-center justify-content-center d-flex align-items-center">
                <div class="flex-col">
                 <img v-if="data_failed.details" class="img-300" :src="data_failed.details.image_url" />
                 <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
                </div>     
             </div> 
      
        </div> <!--card-body-->
        </div> <!--card--> 
        
        `
    };
    
    /*
      VUE TABLES
    */
    const app_report_sales_summary = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,	   	   
           'components-report-sales-summary-chart' : ComponentsReportSalesChart
        },		
        data(){
           return {		
                 is_loading : false,		   	  
           };
        },
        methods:{		
        },
    });
    const vm_report_sales_summary = app_report_sales_summary.mount('#vue-report-sales-summary');
    
    
    /*
       COMPONENTS CUSTOMER ENTRY
    */
    const ComponentsCustomerEntry = {
        props: ['ajax_url' , 'label' ],
        data(){
           return {						
             data : [],		 
             is_loading : false,
             first_name : '',
             last_name : '',
             email_address : '',
             contact_phone : ''
           };
        },   
        mounted() {	   	     	     	     	  
               
        },
         computed: {
            hasData(){		
                if(empty(this.first_name) || empty(this.last_name) ){
                    return false;
                }
                return true;
            },			
        },
        methods: {
             show(){    	  	 	  		   
                 $( this.$refs.customer_entry_modal ).modal('show');
              },
              close(){  	  	 
                   $( this.$refs.customer_entry_modal ).modal('hide');
              },
              submit(){
                  this.is_loading = true; 
                  axios({
                   method: 'put',
                   url: this.ajax_url+"/createCustomer" ,
                    data : {	       	 	 
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                     'first_name' : this.first_name,
                     'last_name' : this.last_name,
                     'email_address' : this.email_address,
                     'contact_phone' : this.contact_phone,
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){			
                          notify(response.data.msg); 	
                          this.$emit('afterSavecustomer', response.data.details ); 	
                      } else {			 	 
                          notify(response.data.msg,'error'); 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
              },
        },
        template: `		    
        <div ref="customer_entry_modal" class="modal" tabindex="-1" role="dialog" data-backdrop="static"  >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
              
              <div class="modal-body">
              
              <a @click="close" href="javascript:;" class="btn btn-black btn-circle rounded-pill"><i class="zmdi zmdi-close font20"></i></a>
              
              <h4 class="m-0 mb-3 mt-3">{{label.customer}}</h4>
              
              <form @submit.prevent="submit"  class="forms mt-2 mb-2">
              
              <div class="row">
                <div class="col">
                   <div class="form-label-group">
                    <input v-model="first_name" class="form-control form-control-text" placeholder="" id="first_name" type="text" maxlength="255">
                    <label for="first_name" class="required">{{label.first_name}}</label>
                  </div>
                </div> <!-- col -->
                
                <div class="col">
                   <div class="form-label-group">
                    <input v-model="last_name" class="form-control form-control-text" placeholder="" id="last_name" type="text" maxlength="255">
                    <label for="last_name" class="required">{{label.last_name}}</label>
                  </div>
                </div> <!-- col -->
                
              </div><!-- row -->
              
              </form>
              
              </div>      
              <div class="modal-footer border-0">            
                <button type="button" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"         
                 :disabled="!hasData"
                 >
                  <span>{{label.submit}}</span>
                  <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                </button>
              </div>
              
            </div>
          </div>
        </div>  
        `
    };
    
    /*
       COMPONENTS POS ORDER DETAILS
    */
    const ComponentsOrderPOS = {
        components: {
           'components-customer-entry': ComponentsCustomerEntry, 	
           'money-format': ComponentsMoney,   
        },		
        props: ['ajax_url' , 'label' ,'limit' , 'order_uuid'],
        data(){
           return {								 
             is_loading : false,
             items : [],
             order_summary : [],
             summary_total : 0,		 
             items_row : [],
             item_qty : 0,
             promo_code : '',
             promo_loading : false,		 
             pay_left : 0,
             change : 0,
             receive_amount : 0,
             payment_code : '',
             payment_list : [],		 
             create_payment_loading : false,
             client_id : 0
           };
        },   
        computed: {
            hasData(){			
                if(this.items.length>0){
                   return true;
                } 
                return false;
            },	
            hasCoopon(){
                if(!empty(this.promo_code)){
                    return true;
                }
                return false;
            },
            hasValidPayment(){
                if( this.receive_amount<=0){
                    return false;
                }
                if(empty(this.payment_code)){
                    return false;
                }			
                if(this.pay_left.toFixed(2)>0){
                    return false;
                }
                return true;
            },
        },
        mounted() {	   	     	     	     	  
               this.initeSelect2();
               this.paymentList();
        },
        watch: {   	  
               item_qty(newqty, oldqty){   	    	
                   axios({
                    method: 'POST',
                    url: this.ajax_url+"/updatePosQty" ,			    
                    data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&qty=" + newqty + "&item_row=" + this.items_row.item_row ,
                    timeout: $timeout,
                    }).then( response => {	 		 	 
                      if(response.data.code==1){			 	 	
                           this.$emit('refreshOrder');
                      } else {			 	
                          notify(response.data.msg,'error'); 	
                      }
                    }).catch(error => {	
                    //
                    }).then(data => {			     				 
                });		
               },
               receive_amount(newamount, oldamount){   	    	
                   this.pay_left = parseFloat(this.summary_total)-parseFloat(newamount);
                   if(this.pay_left<=0){
                       this.pay_left = 0;
                   }
                   this.change = parseFloat(newamount)-parseFloat(this.summary_total);
                   if(this.change<=0){
                       this.change = 0;
                   }
               }
        },
        methods: {
             POSdetails(){     		
                 var $payload = [];   
                 axios({
                   method: 'put',
                   url: this.ajax_url+"/orderDetails" ,
                    data : {
                         'order_uuid' : this.order_uuid,	       	 	 
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                     'payload' : $payload,			     
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      dump(response.data.code);
                      if(response.data.code==1){
                          this.items = response.data.details.data.items;
                        this.order_summary = response.data.details.data.summary;
                        this.summary_total = response.data.details.data.summary_total;                
                      } else {
                          this.items = [];
                        this.order_summary = [];
                        this.summary_total = 0;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
             },     	
             resetPos(){
                 
                  bootbox.confirm({ 
                    size: "small",
                    title : "" ,
                    message: '<h5>'+this.label.clear_items+'</h5>' + '<p>'+this.label.are_you_sure+'</p>',
                    centerVertical: true,
                    animate: false,
                    buttons: {
                        cancel: {
                           label: this.label.cancel,
                           className: 'btn btn-black small pl-4 pr-4'
                        },
                        confirm: {
                            label: this.label.confirm,
                            className: 'btn btn-green small pl-4 pr-4'
                        },
                    },
                    callback: result => {				    	
                        if(result){
                            this.clearItemPos();
                        }
                    }
                });				
                 
             },
             clearItemPos(){
                 
                 axios({
                   method: 'POST',
                   url: this.ajax_url+"/resetPos" ,			    
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&order_uuid=" + this.order_uuid ,
                   timeout: $timeout,
                 }).then( response => {	 
                      dump(response.data.code);
                      if(response.data.code==1){			 	 				 	 	 
                           this.$emit('afterReset');
                      } else {			 	
                          notify(response.data.msg,'error'); 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });			
                 
             },
             removeItem(items){     		
                 axios({
                   method: 'POST',
                   url: this.ajax_url+"/removeItem" ,			    
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&item_row=" + items.item_row ,
                   timeout: $timeout,
                 }).then( response => {	 
                      dump(response.data.code);
                      if(response.data.code==1){			 	 	
                           this.$emit('refreshOrder');
                      } else {			 	
                          notify(response.data.msg,'error'); 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });
                 
             },
             changeQty(item,less_or_add){
                 this.items_row = item;  
                 this.item_qty = this.items_row.qty;
                 if(less_or_add=="add"){
                     this.items_row.qty++
                     this.item_qty++;
                 } else {
                     this.items_row.qty>1?this.items_row.qty--:1
                     this.item_qty>1?this.item_qty--:1
                 }
             },
             initeSelect2(){		
                $(this.$refs.customer).select2({
                     placeholder: this.label.walkin_customer,
                     width : 'resolve',	
                     language: {
                       searching: ()=> {
                         return this.label.searching;
                       },
                       noResults: ()=> {
                         return this.label.no_results;
                       }
                    },
                     ajax: {
                       delay: 250,		  	 
                       url: this.ajax_url +"/searchCustomer",
                       type: 'PUT',
                       contentType : 'application/json',
                       data: function (params) {
                         var query = {
                           search: params.term,		        
                           'YII_CSRF_TOKEN': $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                           'POS':true
                         }			     
                         return JSON.stringify(query);
                       }
                     }	 	
                });	
            },
            showPromo(){    	  	 	  
               this.$refs.promo_code.focus(); 	
                 $( this.$refs.promo_modal ).modal('show');
              },
              closePromo(){  	  	 
                   $( this.$refs.promo_modal ).modal('hide');
              },
              applyPromoCode(){  	    	
                  this.promo_loading = true;
                  axios({
                   method: 'PUT',
                   url: this.ajax_url+"/applyPromoCode",			   
                   data : {
                         'order_uuid' : this.order_uuid,	       	 	 
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                     'promo_code' : this.promo_code,			     
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){		
                           this.promo_code = '';
                           this.closePromo();	 	 	
                           this.$emit('refreshOrder');
                      } else {			 				 	 	
                          notify(response.data.msg,'error'); 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.promo_loading = false;
                 });     		
              },
              removeVoucher(){  	  	    	    	
                  axios({
                   method: 'PUT',
                   url: this.ajax_url+"/removeVoucher",			   
                   data : {
                         'order_uuid' : this.order_uuid,	       	 	 
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			     		    
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){		
                          notify(response.data.msg); 				 	 	 
                          this.$emit('refreshOrder');
                      } else {			 				 	 	
                          notify(response.data.msg,'error'); 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });     		
              },
              showCustomer(){  	    	
                  this.$refs.customer_entry.show();
              },
              closeCustomer(){
                  
              },
              afterSavecustomer(data){
                  dump(data);
                  this.$refs.customer_entry.close();
                  $(this.$refs.customer).select2("trigger", "select", {
                    data: { id: data.client_id , text : data.client_name }
                });
              },
              showPayment(){
                  this.receive_amount = this.summary_total;  	   
                  $( this.$refs.submit_order_modal ).modal('show');
              },
              closePayment(){
                  $( this.$refs.submit_order_modal ).modal('hide');
              },
              paymentList(){
                  
                  axios({
                   method: 'POST',
                   url: this.ajax_url+"/paymentList",			   
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){		
                          this.payment_list = response.data.details.data;
                          this.payment_code = response.data.details.default_payment;			 	 	
                      } else {			 				 	 	
                          this.payment_list = [];
                          this.payment_code  = '';
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });     	
              },
              submitOrder(){
                  this.create_payment_loading = true;
                  this.client_id = $( this.$refs.customer ).find(':selected').val();
                  axios({
                   method: 'PUT',
                   url: this.ajax_url+"/submitPOSOrder",			   
                   data : {	       	 	 
                     'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),			     		    
                     'order_uuid' : this.order_uuid,
                     'receive_amount' : this.receive_amount,
                     'payment_code' : this.payment_code,
                     'order_notes' : this.order_notes,
                     'client_id' : this.client_id,
                     'order_change' : this.change,
                      },
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){		
                          this.closePayment();
                          this.$emit('afterCreateorder' , this.order_uuid );
                      } else {			 				 	 	
                          notify(response.data.msg,'error'); 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.create_payment_loading = false;
                 });     	
                 
              },
        },   
        template: '#xtemplate_order_details_pos',    
    };
    
    /*
     VUE POS
     */
    const app_pos = Vue.createApp({
        components: {
           'components-menu': ComponentsMenu, 
           'components-item-details': ComponentsItem, 
           'components-order-pos': ComponentsOrderPOS, 	 
           'components-order-print': ComponentsOrderPrint, 	   
        },		
        data(){
           return {		
                 is_loading : false,
                 order_uuid : '',
                 order_type : '',
                 order_uuid_print : ''
           };
        },
        mounted() {	   	     	     	     	  
               this.createOrder();
               //this.afterCreateorder('512eec9a-779d-11ec-a877-9c5c8e164c2c');
        },
        methods:{		
           createOrder(){
                  this.is_loading = true;       
                  axios({
                   method: 'POST',
                   url: apibackend +"/createOrder" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      dump(response.data.code);
                      if(response.data.code==1){		
                          this.order_uuid = response.data.details.order_uuid;	 	 	
                          this.order_type = response.data.details.order_type;				 	 	
                          setTimeout(()=>{ 		
                            this.$refs.pos_details.POSdetails();				               
                        },100);           	 	
                      } else {			 	 	
                          this.order_uuid = '';
                          this.order_type = '';
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		
           },
           showItemDetails($data){     	
               this.$refs.item.show($data);
           },
           refreshOrderInformation(){
               this.$refs.pos_details.POSdetails();
           },       
           afterReset(){
                 this.createOrder();
           },
           afterCreateorder(order_uuid){    
                 this.order_uuid_print = order_uuid;        	         	  
                 this.createOrder();
                 setTimeout(()=>{ 	
                    this.$refs.print.show();
                 },100);  
           },
        },
    });
    app_pos.use(Maska);
    app_pos.use(window["v-money3"].default);
    const vm_pos = app_pos.mount('#vue-pos');
    
    
    
    /* MENU STARTS HERE */
    /*
      COMPONENTS MENU PAGES
    */
    const ComponentsMenuAllPages = {
        props: ['ajax_url' , 'label' , 'menu_id'],
        data(){
           return {						
             data : [],		 
             pages : [],
             is_loading : false,
             add_loading : false,
             custom_loading : false,
             custom_link : '',
             custom_link_text : '',
           };
        },   
        mounted() {	   	     	     	     	  
               this.getAllPages();
        },
        computed: {
            hasData(){			
                if(this.pages.length>0 && this.menu_id>0){
                   return true;
                } 
                return false;
            },
            hasLink(){
                if(!empty(this.custom_link) && !empty(this.custom_link_text) ){
                    return true;
                }
                return false;
            },
        },
        methods: {
             getAllPages(){
                  this.is_loading = true;     
                  axios({
                   method: 'POST',
                   url: this.ajax_url+"/AllPages",
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
             addPageToMenu(){
                  this.add_loading = true;
                  axios({
                   method: 'PUT',
                   url: this.ajax_url+"/addPageToMenu",
                   data : {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                         'menu_id' : this.menu_id,
                         'pages' : this.pages,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          notify(response.data.msg);
                          this.pages = [];
                          this.$emit('afterAddpage');
                      } else {			 	 	
                          notify(response.data.msg,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.add_loading = false;
                 });			
                 
             },
             addCustomPageToMenu(){
                 this.custom_loading = true;
                  axios({
                   method: 'PUT',
                   url: this.ajax_url+"/addCustomPageToMenu",
                   data : {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                         'menu_id' : this.menu_id,
                         'custom_link_text' : this.custom_link_text,
                         'custom_link' : this.custom_link,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          notify(response.data.msg);			 	 	
                          this.$emit('afterAddpage');
                          this.custom_link_text = '';
                          this.custom_link = '';
                      } else {			 	 	
                          notify(response.data.msg,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.custom_loading = false;
                 });			
             },
        },
        template: `	
            
          <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
          </div>
        
          <h6 class="mb-2">{{label.title}}</h6>
          
          <div class="accordion mb-3" id="accordionPages">      
           <div class="card">
             <div class="card-header p-0" id="headingOne">
                 <button class="btn w-100 text-left" type="button" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                   {{label.pages}}
                 </button>
             </div>			
             <div id="collapsePages" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionPages">
               <div class="card-body border-left border-right border-bottom">		                           
                 <ul class="list-group list-group-flush">  
                   <li v-for="(item, index) in data" class="list-group-item p-1 pl-0">      
                      <div class="custom-control custom-checkbox">
                        <input v-model="pages" type="checkbox" class="custom-control-input" :value="index" :id="index" >
                        <label class="custom-control-label" :for="index">{{item}}</label>
                      </div>
                    </li>			    
                 </ul>                
               </div> <!-- body -->
               
               <div class="card-footer text-right">		     
                 <button @click="addPageToMenu" :disabled="!hasData" type="button" class="btn btn-green normal rounded-0" :class="{ loading: add_loading }">
                  <span>Add to menu</span>
                  <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                </button> 	            		     
              </div> <!--card-footer-->
               
             </div> <!-- card -->
           </div> <!-- collapse -->
        </div> <!--accordion-->
        
        
         <div class="accordion" id="accordionCustomlink">      
           <div class="card">
             <div class="card-header p-0" id="headingOne">
                 <button class="btn w-100 text-left" type="button" data-toggle="collapse" data-target="#collapseCustomepage" aria-expanded="true" aria-controls="collapseCustomepage">
                   {{label.custom_links}}
                 </button>
             </div>			
             <div id="collapseCustomepage" class="collapse" aria-labelledby="headingOne" data-parent="#accordionCustomlink">
               <div class="card-body border-left border-right border-bottom">		                           
               
                 <div class="form-group">
                    <label for="custom_link">URL</label>
                    <input v-model="custom_link"
                    placeholder="https://"			    
                     type="text" class="form-control" id="custom_link" >  
                  </div>
                  
                  <div class="form-group">
                    <label for="custom_link_text">Link Text</label>
                    <input v-model="custom_link_text"			    
                     type="text" class="form-control" id="custom_link_text" >  
                  </div>
                
               </div> <!-- body -->
               
               <div class="card-footer text-right">		     
                 <button @click="addCustomPageToMenu" :disabled="!hasLink" type="button" class="btn btn-green normal rounded-0" :class="{ loading: custom_loading }">
                  <span>Add to menu</span>
                  <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                </button> 	            		     
              </div> <!--card-footer-->
               
             </div> <!-- card -->
           </div> <!-- collapse -->
        </div> <!--accordion-->
        `
    };
    
    /*
      COMPONENTS MENU LIST
    */
    const ComponentsMenuList = {
        components: {
         'draggable': vuedraggable
        },
        props: ['ajax_url' , 'label'],
        data(){
           return {						
             data : [],		 
             current_menu : 0,
             is_loading : false,
             enabled: true,	      
             dragging: false,
              list: [
                { name: "John", id: 0 },
                { name: "Joao", id: 1 },
                { name: "Jean", id: 2 }
              ],	 
           };
        },   
        mounted() {	   	     	     	     	  
               this.MenuList();
        },
        methods: {
             MenuList(){
                 this.is_loading = true;     
                  axios({
                   method: 'POST',
                   url: this.ajax_url+"/MenuList",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                           this.data = response.data.details.data;
                           this.current_menu = response.data.details.current_menu;
                           this.$emit('setCurrentmenu',this.current_menu);	 
                      } else {			 	 	
                           this.data = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		
             },
             createNewMenu(){     		
                 this.$emit('createNewmenu');
             },
             setMenuID(menu_id){
                 this.current_menu = menu_id;
                 this.$emit('setCurrentmenu',this.current_menu);	 
             },
             checkMove(evt, originalEvent){     	     		
                 this.is_loading = true;   
                  axios({
                   method: 'PUT',
                   url: this.ajax_url+"/sortMenu",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	  			   	 
                        'menu': this.data,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          notify(response.data.msg);			 	 	
                      } else {			 	 	
                          notify(response.data.msg,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });		
             },
        },
        template: `	        
         <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
         </div>
        
        <div class="mb-2">
          <p class="m-0">Select a menu to edit or <a @click="createNewMenu" class="text-green">create new menu</a></p>
        </div>        
                
        <div class="menu-categories medium mb-3 d-flex">
        <draggable
        :list="data"
        :disabled="!enabled"
        item-key="name"
        class="list-group"
        tag="transition-group"
        ghost-class="ghost"
        @update="checkMove"
        @start="dragging = true"
        @end="dragging = false"
        v-bind="dragOptions"
        >
        <template #item="{ element, index }">
        <a
        @click="setMenuID(element.menu_id)"
        :class="{active : current_menu==element.menu_id}"
        class="text-center rounded align-self-center text-center">	
        <p class="m-0 mt-1 text-truncate">{{element.menu_name}}</p>
        </a>
        </template>
        </div>
        `
    };
    
    
    
    /*
      COMPONENTS MENU STRUCTURE
    */
    const ComponentsMenuStructure = {   
       components: {
         'draggable': vuedraggable
       },
        props: ['ajax_url' , 'label' ,'current_menu'],
        data(){
           return {						
             data : [],		 
             is_loading : false,
             menu_name : '',
             child_menu : [],		 
             create_loading : false,
             delete_loading : false,
             remove_loading : false,		 
             enabled: true,	      
             dragging: false		 
           };
        },   
        mounted() {	   	     	     	     	     	    
        },
         computed: {
        dragOptions() {
          return {
            animation: 200,
            group: "description",
            disabled: false,
            ghostClass: "ghost"
          };
        }
      },
        watch: {   	  
            current_menu(newid,oldid){
                if(newid>0){
                   this.getMenuDetails();
                } else {
                    this.menu_name = '';
                    this.child_menu = [];
                }    	
            }
        },
        methods: {    	
             createMenu(){     		
                  this.create_loading = true;   
                  axios({
                   method: 'PUT',
                   url: this.ajax_url+"/createMenu",			   
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),	  
                        'menu_name': this.menu_name,
                        'menu_id': this.current_menu,
                        'child_menu': this.child_menu,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          notify(response.data.msg);
                          this.$emit('afterSavemenu');
                      } else {			 	 	
                          notify(response.data.msg,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.create_loading = false;
                 });			
             },
             deleteMenu(){
                 
                 bootbox.confirm({ 
                    size: "small",
                    title : "" ,
                    message: '<h5>'+ this.label.delete_confirmation+'</h5>' + '<p>'+ this.label.are_you_sure +'</p>',
                    centerVertical: true,
                    animate: false,
                    buttons: {
                        cancel: {
                           label: this.label.cancel ,
                           className: 'btn btn-black small pl-4 pr-4'
                        },
                        confirm: {
                            label: this.label.delete ,
                            className: 'btn btn-green small pl-4 pr-4'
                        },
                    },
                    callback: result => {				    	
                        if(result){
                            
                             this.delete_loading = true;   
                              axios({
                               method: 'POST',
                               url: this.ajax_url+"/deleteMenu",
                               data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') +  "&menu_id=" + this.current_menu,
                               timeout: $timeout,
                             }).then( response => {	 
                                  if(response.data.code==1){		 	 	
                                      notify(response.data.msg);
                                      this.$emit('afterSavemenu');
                                  } else {			 	 	
                                      notify(response.data.msg,'error');
                                  }
                             }).catch(error => {	
                                //
                             }).then(data => {			     
                                 this.delete_loading = false;
                             });			
                            
                        }
                    }
                });			
                 
             },
             getMenuDetails(){     		 
                  this.is_loading = true;
                  axios({
                   method: 'POST',
                   url: this.ajax_url+"/getMenuDetails",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&current_menu=" + this.current_menu,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.menu_name = response.data.details.menu_name;
                          this.child_menu = response.data.details.data;
                      } else {			 	 	
                          this.menu_name = '';
                          this.child_menu = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
             },
             removeChildMenu(index){     			     	
                 //delete this.child_menu[index];
                 this.remove_loading = true;
                  axios({
                   method: 'POST',
                   url: this.ajax_url+"/removeChildMenu",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&menu_id=" + index,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.getMenuDetails();
                      } else {			 	 	
                          notify(response.data.msg,'error');
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.remove_loading = false;
                 });	
             }
        },
        template: `	
        
        
       <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
          <div>
           <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
          </div>
        </div>
          
        <h6 class="mb-2">{{label.title}}</h6>
        
        <div class="card">
            <div class="card-header border-top border-right border-left">
            
              <div class="row align-items-center">
               <div class="col">		        
                <div class="d-flex">
                  <div class="mr-2">Menu name</div>
                  <div><input v-model="menu_name" type="text"></div>		      
                </div>   		       
               </div> <!--col-->
               <div class="col text-right">
                  <template v-if="current_menu>0">	  
                            
                    <button @click="createMenu" type="button" class="btn btn-green normal rounded-0 mr-2" :class="{ loading: create_loading }">
                      <span>Save menu</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                    </button> 	            
                                    
                    <button @click="deleteMenu" type="button" class="btn btn-link normal rounded-0 text-green" :class="{ loading: delete_loading }">
                      <span>Delete</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                    </button> 	            
                    
                  </template>
                  <template v-else>	    	                 
                    <button @click="createMenu" type="button" class="btn btn-green normal rounded-0" :class="{ loading: create_loading }">
                      <span>Create menu</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                    </button> 	            
                    
                    <button @click="$emit('afterCancelmenu')" type="button" class="btn btn-link normal rounded-0 text-green" >
                      <span>Cancel</span>				  
                    </button> 	            
                    
                  </template>
                  
               </div>
              </div> <!-- row-->
            
            </div> <!--card-header-->
            
            <div class="card-body border-left border-bottom border-right">	      
    
               <p class="font11 text-muted">Drag the items into the order you prefer.</p>
               
               <draggable
                :list="child_menu"
                :disabled="!enabled"
                item-key="name"
                class="list-group"
                tag="transition-group"
                ghost-class="ghost"
                :move="checkMove"
                @start="dragging = true"
                @end="dragging = false"
                v-bind="dragOptions"
                >
                <template #item="{ element, index }">
                    <div class="accordion mb-3"  :class="{ 'not-draggable': !enabled }" :id="'child_accordion_'+element.menu_id">		     
                      <div class="card">		      
                        <div class="card-header p-0" id="headingOne">
                            <button class="btn w-100 text-left" type="button" data-toggle="collapse" :data-target="'#page' + element.menu_id" aria-expanded="true" aria-controls="page1">
                              <b>{{element.menu_name}}</b>
                            </button>
                        </div>				
                        <div :id="'page' + element.menu_id" class="collapse" aria-labelledby="headingOne" :data-parent="'#child_accordion_'+element.menu_id">
                          <div class="card-body border-left border-bottomx border-right">
                          
                              <div class="form-group">
                                <label :for="element.menu_id">Navigation Label</label>
                                <input v-model="child_menu[index].menu_name"  type="text" class="form-control" :id="element.menu_id" >  
                              </div>				      
                              
                          </div> <!--body-->
                          
                          <div class="card-footer p-0">
                            <button @click="removeChildMenu(element.menu_id)" 
                            class="btn btn-link normal text-green"
                            :class="{ loading: remove_loading }"
                            >
                               <span>Remove	</span>
                               <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                            </button>				    
                          </div> <!--card-footer-->
                          
                        </div> <!--page1-->
                      </div> <!--card-->			     
                     </div>  
                    <!--accordion-->
                </template>
               </draggable>    
                
            </div> <!--card-body-->
            
        </div> <!--card-->    
        `
    };
    
    /*
       THEME MENU 
    */
    const app_theme_menu = Vue.createApp({	
       components: {
           'components-menu-allpages': ComponentsMenuAllPages,  
           'components-menu-structure': ComponentsMenuStructure,  
           'components-menu-list' : ComponentsMenuList
       },    
       data(){
           return {						
             current_menu : 0
           };
       },   
       methods:{
             setCurrentmenu(data){   	  	
                 this.current_menu = data;
             },
             createNewmenu(){
                 this.current_menu = 0;
             },
             afterCancelmenu(){
                 dump('afterCancelmenu');
                 this.$refs.menu_list.MenuList();
             },
             afterSavemenu(){
                 dump('afterSavemenu');
                 this.$refs.menu_list.MenuList();
             },
             afterAddpage(){
                  this.$refs.menu_structure.getMenuDetails();
             },
       },
    });
    app_theme_menu.use(Maska);
    const vm_theme_menu = app_theme_menu.mount('#vue-theme-menu');
    
    
    if( $('#sort-items').exists() ) {     
        var el = document.getElementById('sort-items');
        var sortable = Sortable.create(el,{
            swapThreshold: 1,
            animation: 150,
            direction : 'horizontal'
        });
    }
    
    })(jQuery); 
    /*end strict*/