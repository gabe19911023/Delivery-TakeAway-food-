(function($) {
    "use strict";
    
    /* ===========================  UTILITY STARTS HERE  ===========================  */
    var data_tables;
    var $is_mobile;
    
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
        
        if( $('.new-passowrd').exists() ) {
            $(".new-passowrd").val('');
        }
        
        /*MENU*/
        if( $('.sidebar-nav').exists() ) {     
           $("ul.sidebar-nav ul > li.active").parent().addClass("open");	 
           
           $('ul li a').click(function(){	             
              $(this).parent().find(".sidebar-nav-sub-menu").slideToggle("fast");          
              
              if( $('.nice-scroll').exists() ) {		
                  setTimeout(function(){ 		
                    $(".nice-scroll").getNiceScroll().resize();   
                  }, 100);           
              }
            
           });
               
        }
        
        /*$( document ).ready( function() {
          $( '.dropdown' ).on( 'show.bs.dropdown', function() {
            $( this ).find( '.dropdown-menu' ).first().stop( true, true ).slideDown( 150 );
          } );
          $('.dropdown').on( 'hide.bs.dropdown', function(){
            $( this ).find( '.dropdown-menu' ).first().stop( true, true ).slideUp( 150 );
          } );
        });*/
        
        /*if( $('.top-container').exists() ) {
            var topnav = document.querySelector('.top-container');
            if(window.pageYOffset>0){
                topnav.classList.add('scrolled')
            }
            window.onscroll = function() {		  
              if (window.pageYOffset > 0) {
                topnav.classList.add('scrolled')
              } else {
                topnav.classList.remove('scrolled')
              }
            }
        }*/
        
        
        if( $('.select_two').exists() ) {
            $('.select_two').select2({
                allowClear: false,
                templateResult: hideSelected,
                theme: "classic",		
                language: "en_us"	
            });
        }
        
        if( $('.select_two_ajax').exists() ) {
            var select2_action  = $(".select_two_ajax").attr("action");
            $('.select_two_ajax').select2({
              theme: "classic",
                 language: "en_us",
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
        
        /*if( $('.datepick').exists() ) {
            $('.datepick').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true
            });
        }*/
        
        if( $('.datepick').exists() ) {
            var $this = $('.datepick');	
            $('.datepick').daterangepicker({
                "singleDatePicker": true,	
                "autoUpdateInput": false,		
                "locale" : {
                  format: 'YYYY-MM-DD'
                },
                "autoApply": true,
            }, function(start, end, label) {			
                $this.val( start.format('YYYY-MM-DD') );		
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
        
        /*if( $('.datepick').exists() ) {
            $('.datepick').datetimepicker({
                 format: 'YYYY-MM-DD',			 
            });	
        }*/
        
        if( $('.timepick').exists() ) {
            $('.timepick').datetimepicker({
                  format: 'hh:mm A'			 
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
        
        if( $('.autosize').exists() ) {
            autosize($('.autosize'));
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
            
        if( $('.mask_phone').exists() ) {
             $('.mask_phone').mask('(00) 0000-0000');
        }
        
        if( $('.mask_mobile').exists() ) {
             $('.mask_mobile').mask('+000000000000');
        }
        
        if( $('.mask_date').exists() ) {
            $('.mask_date').mask('0000/00/00');
       }
            
        if( $('.summernote').exists() ) {
            $('.summernote').summernote({
                 height: 200,
                 codeviewFilter : false,
                 toolbar: [
                      ["font", ["bold", "underline", "italic", "clear"]],
                      ["para", ["ul", "ol", "paragraph"]],
                      ["style", ["style"]],
                      ["color", ["color"]],
                      ["table", ["table"]],
                      ["insert", ["link", "picture", "video"]],
                      ["view", ["fullscreen", "undo", "redo","codeview"]],
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
                    autohidemode:true
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
         
    });
    /*end doc ready*/
    
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
    
    jQuery(document).ready(function() {
        
        if ( $(".table_datatables").exists()){
            initTable(  $(".table_datatables"), $(".frm_datatables")  );
        }
            
        $( document ).on( "click", ".checkbox_select_all", function() {
             $(this).toggleClass("checked");
             if( !$(this).hasClass("checked") ){		 
                 $('.checkbox_child').prop('checked',false);	
             } else {
                 $('.checkbox_child').prop('checked',true);	
             }		 	 
        });
        
        $( document ).on( "click", ".datatables_delete", function() {
            $item_id = $(this).data("id");		
            $(".delete_confirm_modal").modal('show');
        });	
        
        $('.delete_confirm_modal').on('shown.bs.modal', function () {
            $(".item_delete").attr('href',delete_link+"?id="+$item_id);
        });
        
        $( document ).on( "click", ".delete_image", function() {		
            $item_id = $(this).data("id");	
            $(".delete_image_confirm_modal").modal('show');
        });	
        
        $('.delete_image_confirm_modal').on('shown.bs.modal', function () {
            $(".item_delete").attr('href',$item_id);
        });
        
        if( $('.merchant_type_selection').exists() ) {
            var $selection = parseInt($(".merchant_type_selection").val());				
            Membership($selection);
        }
        
        $( ".merchant_type_selection" ).change(function() {
            Membership( parseInt($(this).val()) );
        });
        
        $( document ).on( "click", ".process_csv", function() {
            
            if(!$(this).hasClass( "disabled" )){
                $(this).addClass("disabled");
                $(this).html( '<span class="spinner-border spinner-border-sm"></span>' );			
                processCSV( $(this).data("id") );
            }
        });
        
        $( document ).on( "click", ".order_history", function() {
            $item_id = $(this).data("id");				
            $(".order_history_modal").modal('show');
        });	
        $('.order_history_modal').on('show.bs.modal', function () {
            processAjax("order_history","id="+$item_id);
        });
        
        $( document ).on( "change", ".set_default_currency", function() {
            $("input:checkbox").prop('checked', false );
            $(this).prop('checked', true );	
            setTimeout(function(){			
                processAjax("update_currency_default","id="+ $(".set_default_currency:checked").val() )
            }, 100);
        });
        
        $( document ).on( "change", ".set_email_default_provider", function() {
            $("input:checkbox").prop('checked', false );
            $(this).prop('checked', true );	
            setTimeout(function(){			
                processAjax("update_email_provider_default","id="+ $(".set_email_default_provider:checked").val() )
            }, 100);
        });
        
        $( document ).on( "change", ".set_default_smsprovider", function() {
            $("input:checkbox").prop('checked', false );
            $(this).prop('checked', true );	
            setTimeout(function(){			
                processAjax("set_default_smsprovider","id="+ $(".set_default_smsprovider:checked").val() )
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
        
        $( document ).on( "change", ".set_template_email,.set_template_sms,.set_template_push", function(event) {		
            var $method='';
            if( $( this ).hasClass( "set_template_email" ) ){
                $method='email';
            }	
            if( $( this ).hasClass( "set_template_sms" ) ){
                $method='sms';
            }	
            if( $( this ).hasClass( "set_template_push" ) ){
                $method='push';
            }	
            
            var $id = $(event.target).val();
            var $checked = $(this).is(':checked'); 
            $checked = $checked==true?1:0;
            setTimeout(function(){			
                processAjax('set_template',"id="+  $id + "&checked="+ $checked + "&method="+$method );
            }, 100);
        });
            
        $( ".coupon_options" ).change(function() {
            CouponToggle( $(this).val() );
        });
        
        if( $('.coupon_options').exists() ) {
            CouponToggle( $(".coupon_options").val() );
        };
            
        $( document ).on( "click", ".template_restore", function() {
            $(".modal_restore").modal('show');
        });
        
        $(".locale").on("change", function() {	   
           $(".locale_title").val( $( ".locale option:selected" ).text() );
        });
        
        if($is_mobile){		
           if( $("#lazy-start").exists() ) {		
              initLazyLoad();
           }
        }
        
    });
    /*end doc ready*/
    
    
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
    
    var Membership = function($selection){	
        switch($selection){
            case 1:
              $(".membership_type_1").show();
              //$(".membership_type_2").hide();
              $(".membership_type_3").hide();
            break;
            
            case 2:
              $(".membership_type_1").hide();
              $(".membership_type_2").show();
              $(".membership_type_3").hide();
            break;
            
            case 3:
              $(".membership_type_1").hide();
              $(".membership_type_2").show();
              $(".membership_type_3").show();
            break;
        }
    };
    
    var processCSV = function(id){
        processAjax("process_csv","id="+id);
    };
    
    
    var getTimeNow = function(){
        var d = new Date();
        var n = d.getTime(); 
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
               setLazyData( body.details.data );
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
               /*if(body.details.is_search!=true){   	       	  
                  $infinite_scroll.infiniteScroll( 'option', {
                      loadOnScroll: false,
                  });		
                  if($page<=0){
                       $(".page-no-results").show(); 
                  }	       
               } else {	    	 	       	  	       	  
                     if($page<=0){
                         $infinite_scroll.html('');
                      $(".page-no-results").show();
                     } else {
                          $infinite_scroll.infiniteScroll( 'option', {
                      loadOnScroll: false,
                     });	
                     }	       
               }*/
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
    
    
    
    /*
       VUE STARTS HERE
    */
    
    /*
       ==================================================================================================
       START OF ALL COMPONENTS HERE
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
    
    
    
    /*
      COMPONENTS ORDER INFORMATION
    */
    const ComponentsOrderInfo = {
        props: ['ajax_url','group_name','refund_label','remove_item','out_stock_label','manual_status',
        'modify_order','update_order_label','filter_buttons','enabled_delay_order'],
        /*components: {
           'components-rejection-forms': ComponentsRejectionForms, 	
           'components-refund-forms': ComponentsRefund, 
        },	 */
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
             payment_history : []
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
            }
        },
        mounted() {
           this.getOrderStatusList();	   
        },
        methods: {
            orderDetails(order_uuid , $update_summary ){
                this.order_uuid = order_uuid;
                this.is_loading = true;
                this.loading = true;
                
                //var $payload = ['payment_history','print_settings','buttons'];
                var $payload = ['payment_history','print_settings'];
                            
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
                if($do_actions=="reject_form"){     			    		
                    this.$refs.rejection.confirm().then((result) => {		  
                         if(result){			   		   			   		  
                            dump("rejection reason =>" + result);
                            this.updateOrderStatus($uuid,$order_uuid,result);
                         }
                       });    			
                } else {    			    	    			    			    			
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
       CUSTOMER COMPONENTS
    */
    const ComponentsCustomer = {
        props: ['label','ajax_url','client_id','image_placeholder' ,'page_limit' , 'merchant_id'],
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
                        'merchant_id' : this.merchant_id
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
                        { data: 'restaurant_name' },
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
       COMPONENTS MONEY   
    */
    const ComponentsMoney = {
        props: ['ajax_url', 'amount'],
        data(){
           return {						
             data : 0,
             config : {
                prefix : '$',
                suffix: '',
                thousands : ',',
                decimal : '.',
                precision : 2,
            },		
           };
        },   
        mounted() {	   	     	     	     	     	    
              this.data = window["v-money3"].format(this.amount, this.config);
        },    
        updated(){   	
              this.data = window["v-money3"].format(this.amount, this.config);
        },    
        template:`	
        {{data}}
        `
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
       ==================================================================================================
       END OF ALL COMPONENTS
       ==================================================================================================
    */
    
    
    
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
                         //id : data.id,
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
       
       <!--
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
            <input :name="field_path" type="hidden" :value="item.path" />
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
              
        <div ref="modal_uplader" :class="{'modal fade':this.inline=='false'}" id="modalUploader" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalUploader" aria-hidden="true">
        
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
                     <h5>{{label.drop_files}}<br/>{{label.or}}</h5>
                     <a ref="fileinput" class="btn btn-green fileinput-button" href="javascript:;">{{label.select_files}}</a>
                   </div>
                 </div> 	         
                 
                 <!-- file_preview_container -->
                 <div :class="{ 'd-block': preview }" class="file_preview_container">	 
                      <nav class="navbar bg-light d-flex justify-content-end">
                         <button @click="addMore" type="button" class="btn">+ 
                         
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
    
    
    /*
      SELECT 2
      https://github.com/godbasin/vue-select2#readme
    */
    const Select2 =  {
      name: 'Select2',
      data() {
        return {
          select2: null
        };
      },
      emits: ['update:modelValue'],
      props: {
        modelValue: [String, Array], // previously was `value: String`
        id: {
          type: String,
          default: ''
        },
        name: {
          type: String,
          default: ''
        },
        placeholder: {
          type: String,
          default: ''
        },
        options: {
          type: Array,
          default: () => []
        },
        disabled: {
          type: Boolean,
          default: false
        },
        required: {
          type: Boolean,
          default: false
        },
        settings: {
          type: Object,
          default: () => {}
        },
      },
      watch: {
        options: {
          handler(val) {
            this.setOption(val);
          },
          deep: true
        },
        modelValue: {
          handler(val) {
            this.setValue(val);
          },
          deep: true
        },
      },
      methods: {
        setOption(val = []) {
          this.select2.empty();
          this.select2.select2({
            placeholder: this.placeholder,
            ...this.settings,
            data: val
          });
          this.setValue(this.modelValue);
        },
        setValue(val) {
          if (val instanceof Array) {
            this.select2.val([...val]);
          } else {
            this.select2.val([val]);
          }
          this.select2.trigger('change');
        }
      },
      mounted() {
        this.select2 = $(this.$el)
          .find('select')
          .select2({
              theme: "classic",      
            placeholder: this.placeholder,
            ...this.settings,
            data: this.options
          })
          .on('select2:select select2:unselect', ev => {
            this.$emit('update:modelValue', this.select2.val());
            this.$emit('select', ev['params']['data']);
          });
        this.setValue(this.modelValue);
      },
      beforeUnmount() {
        this.select2.select2('destroy');
      },
      template: ` 
      <div>
        <select class="form-control" multiple="multiple" :id="id" :name="name" :disabled="disabled" :required="required"></select>
      </div>
      `
    };
    /*SELECT 2*/
    
    /*
      ComponentsOrderTabList
    */
    const ComponentsOrderTab = {
        props: ['label','ajax_url' ,'group_name' ,'status_list' ],
        components: {    
         'Select2': Select2, 
        },  
        data(){
            return {												
                error : [],			
                status : [],
                is_loading : ''
            };
        },	
        computed: {
            DataValid(){
                if(this.is_loading){
                    return false;
                }
                return true;
            }
        },
        mounted() {
           this.getSettings();
        },  
        methods: {
             getSettings(){
                  
                  axios({
               method: 'put',
               url: this.ajax_url+"/getOrderTab",
               data : {
                     'group_name' : this.group_name,
                 'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  },
               timeout: $timeout,
             }).then( response => {	 
                  this.response_code = response.data.code;
                 if(response.data.code==1){
                    this.status = response.data.details;
                 } else {		   		     	
                     this.data = [];
                 }
             }).catch(error => {	
                
             }).then(data => {			     
                 
             });		    
                 
             },
             show(){  	   	 
                $( this.$refs.modal ).modal('show');
             },
             close(){
                   $( this.$refs.modal ).modal('hide');
             },
             submit(){
                   this.is_loading =  true;  
                   this.success = '';	
                   this.error = [];
                   axios({
               method: 'put',
               url: this.ajax_url+"/saveOrderTab",
               data : {
                    'group_name' : this.group_name,
                     'status' : this.status,
                 'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  },
               timeout: $timeout,
             }).then( response => {	 
                  this.response_code = response.data.code;
                 if(response.data.code==1){		        
                    vm_bootbox.alert( response.data.msg , {size:'small'} );
                    this.close();
                 } else {		   
                     this.error = response.data.msg;		
                     this.this.success = '';     	
                 }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_loading =  false;
             });		 
             },
        },  
        template: `  
         <h5 class="card-title">{{label.title}}</h5>     
         <div class="d-flex justify-content-between mb-2">
          <div><p class="card-text">{{label.text}}</p></div>
         </div>  
             
         <form @submit.prevent="submit" >
          
          <div class="form-label-group">    
          <Select2 v-model="status" :options="status_list" :settings="{ settingOption: value, settingOption: value }" >
          </Select2>
         </div>
         
           <div  v-cloak v-if="error.length>0" class="alert alert-warning" role="alert">
            <p v-cloak v-for="err in error">{{err}}</p>	    
           </div>
              
          <button type="button" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }" 
        :disabled="!DataValid" 
         >
          <span>{{label.save}}</span>
          <div class="m-auto circle-loader" data-loader="circle-side"></div> 
        </button> 
          
         </form> 
        `
    };
    
    
    /* 
      DATATABLES FILTER
    */
    
    const ComponentsDataTablesFilter = {
        props: ['ajax_url','settings'],
        template: '#xtemplate_payout_filter',
        mounted() {
           this.initeSelect2();	  
           if(this.settings.load_filter){
                 this.getFilterData();	   	  
           }
        },
        data(){
           return {				
                 status_list : [],				  
                 order_type_list : [],
              merchant_id : '',		
              client_id : '',
              order_status : '',
              order_type : '',
           };
        },
        methods: {
            initeSelect2(){
                 $('.select2-single').select2({
                     width : 'resolve',		 	
                 });	
                 
                 $('.select2-merchant').select2({
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
                        url: this.ajax_url +"/searchMerchant",
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
                 
                 if(!empty(this.$refs.client_id)){
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
                 }			 
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
                $( this.$refs.merchant_id ).val(null).trigger('change');
                $( this.$refs.client_id ).val(null).trigger('change');			
                $( this.$refs.order_status ).val(null).trigger('change');			
                $( this.$refs.order_type ).val(null).trigger('change');			
            },
            submitFilter(){
                this.merchant_id = $( this.$refs.merchant_id ).find(':selected').val();			
                this.client_id = $( this.$refs.client_id ).find(':selected').val();			
                this.order_status = $( this.$refs.order_status ).find(':selected').val();			
                this.order_type = $( this.$refs.order_type ).find(':selected').val();
                
                this.$emit('afterFilter', {
                    merchant_id : this.merchant_id,	
                    client_id : this.client_id,
                    order_status : this.order_status,
                    order_type : this.order_type,
                }); 			
            },
            closePanel(){
                this.$emit('closePanel');
            },
        },
    };
    
    
    /*
      ORDERS BUTTON SETTINGS
    */
    const ComponentsOrderButton = {
        props: ['label','ajax_url' ,'group_name' ,'status_list' ,'do_action_list' ,'order_type_list' ],
        data(){
            return {			
                is_loading : '',
                error : [],
                status : '',
                button_name : '',	
                data : [],
                uuid : '',
                do_actions : '',	
                class_name : '',
                order_type : ''
            };
        },	
        mounted() {
           this.getOrderButtonList();
        },  
        computed: {
            DataValid(){
                if(this.is_loading){
                    return false;
                }
                return true;
            }
        },
        methods: {
           show(){  	
                 this.button_name = '';   	
                 this.uuid = ''; 
                 this.status = '';
                 this.do_actions = '';
                 this.class_name = '';
                $( this.$refs.modal ).modal('show');
             },
             close(){
                   $( this.$refs.modal ).modal('hide');
             },
             getOrderButtonList(){
                   this.is_loading =  true;  	
                   this.error = [];
                   axios({
               method: 'put',
               url: this.ajax_url+"/getOrderButtonList",
               data : {
                    'group_name' : this.group_name,		   	 
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
                
             }).then(data => {			     
                 this.is_loading =  false;
             });		 
             },
             submit(){
                   this.is_loading =  true;  	
                   this.error = [];
                   axios({
               method: 'put',
               url: this.ajax_url+"/saveOrderButtons",
               data : {
                    'group_name' : this.group_name,
                    'button_name' : this.button_name,
                     'status' : this.status,
                     'order_type' : this.order_type,
                     'uuid' : this.uuid,
                     'class_name' : this.class_name,
                     'do_actions' : this.do_actions,
                 'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  },
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){
                      this.close();
                      this.getOrderButtonList();
                  } else {
                      this.error = response.data.msg;
                  }
             }).catch(error => {	
                
             }).then(data => {			     
                 this.is_loading =  false;
             });		 
             },
             deleteButtons($uuid){
                  vm_bootbox.confirm().then((result) => {		  
                   if(result){
                      
                        axios({
                       method: 'put',
                       url: this.ajax_url+"/deleteButtons",
                       data : {
                            'uuid' : $uuid,				   	 
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                          },
                       timeout: $timeout,
                     }).then( response => {	 
                          if(response.data.code==1){
                              this.getOrderButtonList();				 	 	
                          } else {
                              vm_bootbox.alert( response.data.msg , {size:'small'} );
                          }
                     }).catch(error => {	
                        //
                     }).then(data => {			     
                         //
                     });		 
                       
                   }
               });			  	   	  	
             },
             getButtons($uuid){
                  this.show();
                  this.is_loading = true;
                  this.uuid = '';
                  axios({
               method: 'put',
               url: this.ajax_url+"/getButtons",
               data : {
                    'uuid' : $uuid,				   	 
                 'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                  },
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){		 	 	
                      this.status = response.data.details.stats_id;
                      this.order_type = response.data.details.order_type;
                      this.button_name = response.data.details.button_name;
                      this.uuid = response.data.details.uuid;
                      this.do_actions = response.data.details.do_actions;
                      this.class_name = response.data.details.class_name;
                  } else {
                      vm_bootbox.alert( response.data.msg , {size:'small'} );
                      this.status = '';
                      this.button_name = '';
                      this.uuid = '';
                      this.do_actions = '';
                      this.class_name ='';
                  }
             }).catch(error => {	
                //
             }).then(data => {			     
                 this.is_loading = false;
             });		
             },
        },
        template: `
        <h5 class="card-title">{{label.title}}</h5>
        
        <div class="d-flex justify-content-between mb-2">
          <div><p class="card-text">{{label.text}}</p></div>
          <div><button class="btn btn-green" @click="show"><i class="zmdi zmdi-plus mr-1"></i>{{label.add}}</button></div>
        </div>         
        
         <table class="table table-bordered">
           <thead>
           <tr>
            <td width="33%"><b>{{label.button_name}}</b></td>
            <td><b>{{label.button_status}}</b></td>
            <td  class="text-center"><b>{{label.actions}}</b></td>
           </tr>
           </thead>
           <tr v-for="item in data">
            <td>{{item.button_name}}</td>
            <td>
              <p class="m-0">{{item.status}}</p>
              <p v-if="item.order_type" class="m-0">(<b>{{item.order_type}})</b></p>
            </td>
            <td class="text-center">
            <div class="btn-group btn-group-actions" role="group">
              <a class="btn btn-light" @click="getButtons(item.uuid)">
               <i class="zmdi zmdi-border-color"/>
              </a>
              <a class="btn btn-light" @click="deleteButtons(item.uuid)">
               <i class="zmdi zmdi-delete"/>
              </a>
            </div>
            </td>
           </tr>
        </table>
        
        <div ref="modal" class="modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          
          <form @submit.prevent="submit" >
           
            <div class="form-group">  
              <label for="button_name">{{label.button_name}}</label> 
              <input v-model="button_name" type="text" id="button_name" class="form-control form-control-text">
            </div>
            
            <div class="form-group">  
              <label for="class_name">{{label.class_name}}</label> 
              <input v-model="class_name" type="text" id="class_name" class="form-control form-control-text">
            </div>
            
            <div class="form-group">  
             <label for="status">{{label.button_status}}</label> 
             <select id="status" v-model="status" class="form-control custom-select form-control-select">
               <option v-for="(item, key) in status_list" :value="item.id">{{item.text}}</option>
             </select>
            </div>
            
            <div v-if="order_type_list" class="form-group">  
             <label>{{label.order_type}}</label> 
             <select id="order_type" v-model="order_type" class="form-control custom-select form-control-select">
               <option v-for="(item, key) in order_type_list" :value="key">{{item}}</option>
             </select>
            </div>
            
            <div class="form-group">  
             <label for="status">{{label.actions}}</label> 
             <select id="status" v-model="do_actions" class="form-control custom-select form-control-select">
               <option v-for="(item, key) in do_action_list" :value="key">{{item}}</option>
             </select>
            </div>
            
             <div  v-cloak v-if="error.length>0" class="alert alert-warning mb-2" role="alert">
                <p v-cloak v-for="err in error" class="m-0">{{err}}</p>	    
             </div>   
            
          </form>       
           
          
          </div>      
          <div class="modal-footer">            
            <button type="button" @click="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }" 
            :disabled="!DataValid" 
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
    
    /*
      ORDER TAB SETTINGS
    */
    const vm_order_management = Vue.createApp({  
      components: {    
        'components-order-tabs': ComponentsOrderTab, 
        'components-order-buttons': ComponentsOrderButton, 
      },  
      mounted() {	 
      },  
      methods: {     
      },
    }).mount('#vue-order-settings-tabs');
    
    
    /*
       COMPONENTS DATA TABLES
    */
    const ComponentsDataTables = {
        components: {
           'components-order-filter': ComponentsDataTablesFilter,
        },
        props: ['settings','actions','ajax_url','table_col','columns', 'page_limit', 
        'transaction_type_list','filter','date_filter','merchant_uuid' , 'ref_id'],
        mounted() {
            if(this.settings.auto_load){
               this.getTableData();
            }
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
                
                var translation_vendors = JSON.parse(translation_vendor);
                dump(translation_vendors.today);
                var ranges_object = {};
                ranges_object[translation_vendors.today] = [moment(), moment()];
                ranges_object[translation_vendors.Yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
                ranges_object[translation_vendors.last_7_days] = [moment().subtract(6, 'days'), moment()];
                ranges_object[translation_vendors.last_30_days] = [moment().subtract(29, 'days'), moment()];
                ranges_object[translation_vendors.this_month] = [moment().startOf('month'), moment().endOf('month')];
                ranges_object[translation_vendors.last_month] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];     		
                
                $( this.$refs.date_range ).daterangepicker({	
                    "autoUpdateInput": false,				
                    "showWeekNumbers": true,
                    "alwaysShowCalendars": true,
                    "autoApply": true,
                    "locale" : {
                       format: 'YYYY-MM-DD',
                       daysOfWeek: [ translation_vendors.su , translation_vendors.mo , translation_vendors.tu , 
                       translation_vendors.we, translation_vendors.th, translation_vendors.fr ,translation_vendors.sa],
                       monthNames: [ translation_vendors.january,  translation_vendors.february,  translation_vendors.march, 
                        translation_vendors.may,  translation_vendors.may ,  translation_vendors.july ,  translation_vendors.july ,
                        translation_vendors.august,  translation_vendors.september ,  translation_vendors.october,  translation_vendors.november, 
                        translation_vendors.december],
                       customRangeLabel : translation_vendors.custom_range , 
                    },
                    ranges : ranges_object
                   /*  ranges: {
                       'Today' : [moment(), moment()],
                       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                       'This Month': [moment().startOf('month'), moment().endOf('month')],
                       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }		*/	
                }, (start,end,label) => {				
                    this.date_range = start.format('YYYY-MM-DD') +" "+ this.settings.separator +" "+   end.format('YYYY-MM-DD') ;
                    this.date_start = start.format('YYYY-MM-DD');
                    this.date_end = end.format('YYYY-MM-DD');		
                    this.$emit('afterSelectdate', this.date_start,this.date_end ); 						
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
                             d.merchant_uuid = this.merchant_uuid;
                             d.ref_id = this.ref_id;
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
                    bFilter : this.settings.filter,
                    ordering : this.settings.ordering, 
                    order : [[ this.settings.order_col , this.settings.sortby ]],	
                    //order : [[ 0, "desc" ]],	
                    columns : this.columns	    	
                 });		
                 
                 $datatables = this.datatables;
                 
                 $('.vue_table tbody').on( 'click', '.ref_view_transaction', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 
                      if(!empty(data)){		     	 	
                         $vue.viewTransaction( data.merchant_uuid );
                      }
                 });
                 
                 $('.vue_table tbody').on( 'click', '.ref_payout', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 		     	 		     	 
                      if(!empty(data)){
                         $vue.viewTransaction( data.transaction_uuid );
                      }
                 });
                 
                 $('.vue_table tbody').on( 'click', '.ref_view_order', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 		     	 		     	 
                      if(!empty(data)){		     	    
                         //window.open(data.view_order, "_blank");
                         window.location.href = data.view_order;
                      }
                 });
                              
                 $('.vue_table tbody').on( 'click', '.ref_pdf_order', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 		     	 		     	 
                      if(!empty(data)){		     	    
                         window.open(data.view_pdf, "_blank");
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
                 
                 $('.vue_table tbody').on( 'click', '.ref_view', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 
                      if(!empty(data)){
                          $vue.$emit('view', data ); 
                      }
                 });
    
                 $('.vue_table tbody').on( 'click', '.set_status', function () {			     	 
                    var data = $datatables.row( $(this).parents('tr') ).data();		     	 
                    if(!empty(data)){
                        var $active = $(this).prop('checked');
                        $active = $active==true?"active":"inactive";
                        axios({
                            method: 'post',
                            url: $vue.ajax_url +"/"+data.actions,
                            data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&id=" + data.id  + "&status=" + $active ,
                            timeout: $timeout,
                          }).then( response => {	 						   
                               if(response.data.code==1){				 	 								
                               } else {							  
                               }	 				 	
                          }).catch(error => {	
                             //
                          }).then(data => {			     
                              //
                          });			
                    }
               });
                 
                 $('.vue_table tbody').on( 'click', '.ref_delete', function () {			     	 
                      var data = $datatables.row( $(this).parents('tr') ).data();		     	 
                      if(!empty(data)){
                          
                          bootbox.confirm({ 
                            size: "small",
                            title : "" ,
                            message: '<h5>'+ $vue.settings.delete_confirmation  +'</h5>' + '<p>'+ $vue.settings.delete_warning +'</p>',
                            centerVertical: true,
                            animate: false,
                            buttons: {
                                cancel: {
                                   label: $vue.settings.cancel,
                                   className: 'btn'
                                },
                                confirm: {
                                    label: $vue.settings.delete,
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
            viewTransaction(merchant_uuid){     		
                this.$emit('viewTransaction', merchant_uuid ); 
            }
        },
        template: `			
        
         <div class="row mb-3">
          <div class="col">
              
              <div class="d-flex">
              
              <div v-if="date_filter" class="input-group fixed-width-field mr-2">
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
              
              <button v-if="filter==1" class="ml-2 btn btn-yellow normal" @click="openFilter" >		   		   
               <div class="d-flex">
                 <div class="mr-2"><i class="zmdi zmdi-filter-list"></i></div>
                 <div>{{settings.filters}}</div>
               </div>
              </button>
              
              </div> <!-- flex -->
              
          </div>	  
          <div class="col"></div>
        </div> <!--row-->
                
        <div class="table-responsive-md">
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
    
    
    /*
       COMMISSION BALANCE
    */
    const componentsCommissionBalance = {
        props: ['ajax_url'],
        data(){
           return {			
                 is_loading : false,				
           };
        },
        mounted() {
            this.getBalance();
        },		
        methods: {
            getBalance(){		
                this.is_loading = true;	
                axios({
                   method: 'post',
                   url: this.ajax_url +"/commissionBalance",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance;
                      if(response.data.code==1){				 	 	
                          
                          var $parameters = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                        };
                        
                        if(response.data.details.price_format.position=="right"){
                            $parameters.suffix = response.data.details.price_format.symbol;
                        } else $parameters.prefix = response.data.details.price_format.symbol;
                          
                        $balance = new countUp.CountUp(this.$refs.balance, response.data.details.balance , $parameters );	
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
    
    
    /*
       MERCHANT TOTAL BALANCE
    */
    const componentsMerchantTotalBalance = {
        props: ['ajax_url'],
        data(){
           return {			
                 is_loading : false,				
           };
        },
        mounted() {
            this.getBalance();
        },		
        methods: {
            getBalance(){		
                this.is_loading = true;	
                axios({
                   method: 'post',
                   url: this.ajax_url +"/merchantTotalBalance",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 
                      var $balance;
                      if(response.data.code==1){				 	 	
                          
                          var $parameters = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                        };
                        
                        if(response.data.details.price_format.position=="right"){
                            $parameters.suffix = response.data.details.price_format.symbol;
                        } else $parameters.prefix = response.data.details.price_format.symbol;
                          
                        $balance = new countUp.CountUp(this.$refs.balance, response.data.details.balance , $parameters);	
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
    
    
    /*
       CREATE ADJUSTMENT
    */
    
    const componentsCreateAdjustment = {
        props: ['ajax_url','label','transaction_type_list'],
        data(){
           return {			
                 is_loading : false,
                 transaction_description : '',
                 transaction_type : 'credit',
                 transaction_amount : 0,
           };
        },
        computed: {
            hasData(){					
                if(!empty(this.transaction_description)  && !empty(this.transaction_type) && !empty(this.transaction_amount)  ){
                    return true;
                }		
                return false;
            },
        },
        mounted() {
            
        },		
        methods: {
            show(){
                $( this.$refs.modal_adjustment ).modal('show');
            },
            close(){
                $( this.$refs.modal_adjustment ).modal('hide');
            },
            clear(){
                this.transaction_description = '';
                this.transaction_type = 'credit';
                this.transaction_amount = 0;
            },
            submit(){
                
                var $params = {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                         payment_provider : this.payment_provider,
                         transaction_description : this.transaction_description,
                         transaction_type : this.transaction_type,
                         transaction_amount : this.transaction_amount,
                    };  	 
                 
                this.error = [];
                    this.is_loading = true;	
                axios({
                   method: 'put',
                   url: this.ajax_url +"/commissionadjustment",
                   data : $params,
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){		
                         notify(response.data.msg);
                         this.clear();
                         this.close();
                         this.$emit('afterSave');	    		    			 	    
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
        <div ref="modal_adjustment" class="modal"
        id="modal_adjustment" data-backdrop="static" 
        tabindex="-1" role="dialog" aria-labelledby="modal_adjustment" aria-hidden="true">
        
           <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
             <div class="modal-content"> 
             
             <form @submit.prevent="submit" >
             
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              
              <div class="modal-body">
              
               <div class="form-label-group">    
                   <input type="text" v-model="transaction_description" id="transaction_description"
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="transaction_description">{{label.transaction_description}}</labe>
               </div>
    
               <select ref="transaction_type" v-model="transaction_type"
                 class="form-control custom-select form-control-select mb-3"  >		    
                 <option v-for="(name, key) in transaction_type_list" :value="key">{{name}}</option>		    
               </select>	   
               
               <div class="form-label-group">    
                   <input type="text" v-model="transaction_amount" id="transaction_amount"
                   v-maska="'#################'" 
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="transaction_amount">{{label.transaction_amount}}</labe>
                 </div>
              
               </div> <!-- body -->
              
               <div class="modal-footer">     
    
               <button type="button" class="btn btn-black" data-dismiss="modal">
                <span class="pl-3 pr-3">{{label.close}}</span>
               </button>
                   
                <button type="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"
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
    
    
    const componentsMerchantTransaction = {
        props: ['ajax_url','image_placeholder','label'],
        components: {
           'components-datatable': ComponentsDataTables,	   
        },		
        data(){
           return {			
                 is_loading : false,	 
                 merchant_uuid : '',
                 merchant : [],
                 observer : undefined,
                 plan_history : [],
                 merchant_active : false,
           };
        },    
        mounted(){
            
            this.observer = lozad('.lozad',{
                 loaded: function(el) {
                     el.classList.add('loaded');
                 }
            });    	
        },
        updated () {		
            this.observer.observe();
        },
        methods: {
            show(){
                $( this.$refs.modal_merchant_transaction ).modal('show');
                this.$refs.datatable.getTableData();
                this.getOrderSummary();
            },
            close(){
                $( this.$refs.modal_merchant_transaction ).modal('hide');
            },
            getOrderSummary(){
                this.is_loading = true;		
                axios({
                   method: 'post',
                   url: this.ajax_url +"/getordersummary",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&merchant_uuid=" + this.merchant_uuid ,
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                       if(response.data.code==1){	
    
                           this.merchant = response.data.details.merchant;
                           this.merchant_active = response.data.details.merchant.merchant_active;
                           
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
                        };		  
                        
                        if(response.data.details.price_format.position=="right"){
                            $options.suffix = response.data.details.price_format.symbol;
                        } else $options.prefix = response.data.details.price_format.symbol;
                         
                        var $summary_total =  new countUp.CountUp(this.$refs.summary_total,  response.data.details.total , $options);	
                        $summary_total.start();
                        
                        var $total_refund =  new countUp.CountUp(this.$refs.total_refund,  response.data.details.total_refund , $options);	
                        $total_refund.start();
                
                      } else {						 	 	
                          this.merchant = [];
                          notify(response.data.msg,'error');
                          this.$refs.summary_orders = 0;
                          this.$refs.summary_cancel = 0;
                          this.$refs.summary_total = 0;
                          this.$refs.total_refund = 0;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            },
            merchantActiveConfirmation(){				
                if(this.merchant_active){
                    bootbox.confirm({ 
                        size: "small",
                        title : "" ,
                        message: '<h5>'+this.label.block+'</h5>' + '<p>'+ this.label.block_content +'</p>',
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
                                this.changeMerchantStatus(0);
                            }
                        }
                    });				
                } else {
                    this.changeMerchantStatus(1);
                }
            },
            changeMerchantStatus($status){
                this.is_loading = true;			    	
                axios({
                   method: 'put',
                   url: this.ajax_url+"/changeMerchantStatus",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'merchant_uuid' : this.merchant_uuid,	 
                        'status' : $status,
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.merchant_active = response.data.details.merchant_active;
                      } else {						 	 	
                          this.merchant_active = false;
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            },
        },
        template: '#xtemplate_merchant_transaction',    
    };
    
    /*
      MERCHANT EARNINGS ADJUSTMENT
    */
    const componentsMerchantAdjustment = {
        props: ['ajax_url','label','transaction_type_list'],
        data(){
           return {			
                 is_loading : false,
                 transaction_description : '',
                 transaction_type : 'credit',
                 transaction_amount : 0,
                 merchant_id : ''
           };
        },
        computed: {
            hasData(){					
                if(!empty(this.transaction_description)  && !empty(this.transaction_type) && !empty(this.transaction_amount) ){
                    return true;
                }		
                return false;
            },
        },
        mounted() {
            
        },		
        methods: {
            show(){
                $( this.$refs.modal_merchant_adjustment ).modal('show');
            },
            close(){
                $( this.$refs.modal_merchant_adjustment ).modal('hide');
            },
            clear(){
                this.transaction_description = '';
                this.transaction_type = 'credit';
                this.transaction_amount = 0;
            },
            submit(){
                
                this.merchant_id = $( this.$refs.adjustment_merchant_id ).find(':selected').val();			
                
                var $params = {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                         payment_provider : this.payment_provider,
                         transaction_description : this.transaction_description,
                         transaction_type : this.transaction_type,
                         transaction_amount : this.transaction_amount,
                         merchant_id : this.merchant_id
                    };  	 
                 
                this.error = [];
                    this.is_loading = true;	
                axios({
                   method: 'put',
                   url: this.ajax_url +"/merchantEarningAdjustment",
                   data : $params,
                   timeout: $timeout,
                 }).then( response => {	 			 	 
                      if(response.data.code==1){		
                         notify(response.data.msg);
                         this.clear();
                         this.close();
                         this.$emit('afterSave');	    		    			 	    
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
        <div ref="modal_merchant_adjustment" class="modal"
        id="modal_merchant_adjustment" data-backdrop="static" 
       role="dialog" aria-labelledby="modal_merchant_adjustment" aria-hidden="true">
        
           <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
             <div class="modal-content"> 
             
             <form @submit.prevent="submit" >
             
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              
              <div class="modal-body">
              
               <p class="mb-2"><b>{{label.merchant}}</b></p>
                 <div class="form-label-group mb-3">  
                 <select ref="adjustment_merchant_id" class="form-control select2-merchant"  style="width:100%">	  	  
                 </select>
               </div>
         
               <div class="form-label-group">    
                   <input type="text" v-model="transaction_description" id="transaction_description"
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="transaction_description">{{label.transaction_description}}</labe>
               </div>
    
               <select ref="transaction_type" v-model="transaction_type"
                 class="form-control custom-select form-control-select mb-3"  >		    
                 <option v-for="(name, key) in transaction_type_list" :value="key">{{name}}</option>		    
               </select>	   
               
               <div class="form-label-group">    
                   <input type="text" v-model="transaction_amount" id="transaction_amount"
                   v-maska="'#################'" 
                   placeholder=""
                   class="form-control form-control-text" >
                   <label for="transaction_amount">{{label.transaction_amount}}</labe>
                 </div>
              
               </div> <!-- body -->
              
               <div class="modal-footer">     
    
               <button type="button" class="btn btn-black" data-dismiss="modal">
                <span class="pl-3 pr-3">{{label.close}}</span>
               </button>
                   
                <button type="submit" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"
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
      ACCOUNT TRANSACTIONS
    */
    const app_commission = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,	   
           'components-commission-balance': componentsCommissionBalance,
           'components-total-balance': componentsMerchantTotalBalance,
           'components-create-adjustment' : componentsCreateAdjustment,
           'components-merchant-transaction' : componentsMerchantTransaction,
           'components-merchant-earning-adjustment' : componentsMerchantAdjustment,
        },		
        data(){
           return {		
                 is_loading : false,	   	  
                 balance : 0
           };
        },
        methods:{	
            afterBalance($balance){			
                this.balance = $balance;
            },
            createTransaction(){			
                this.$refs.create_adjustment.show();
            },
            afterSave(){
                this.$refs.datatable.getTableData();
                this.$refs.balance.getBalance();
            },
            viewMerchantTransaction(merchant_uuid){
                this.$refs.merchant_transaction.merchant_uuid = merchant_uuid;
                this.$refs.merchant_transaction.show();
            },		
            createMerchantAdjustment(){
                this.$refs.merchant_adjustment.show();
            },
        },
    });
    app_commission.use(Maska);
    const vm_commission = app_commission.mount('#vue-commission-statement');
    
    
    /*
       COMPONENTS PAYOUT DETAILS
    */
    const ComponentsPayoutDetails = {
        props: ['ajax_url','label'],
        data(){
           return {			
                 is_loading : false,
                 transaction_uuid : '',
                 data : [],
                 merchant :[],
                 provider : []
           };
        },
        computed: {
            isUnpaid(){			
               if( this.data.status=='unpaid' ){
                     return false;
               }
               return true;
            },
        },
        methods:{
            show(){
                $( this.$refs.modal_payout ).modal('show');
                this.getPayoutDetails();
            },
            close(){
                $( this.$refs.modal_payout ).modal('hide');
            },
            getPayoutDetails(){			
                this.is_loading = true;			    	
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/getPayoutDetails",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content')  + "&transaction_uuid=" + this.transaction_uuid ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details.data;
                          this.merchant = response.data.details.merchant;
                          this.provider = response.data.details.provider;
                      } else {				
                          notify(response.data.msg,'error');		 	 	
                          this.data = [];
                          this.merchant = [];
                          this.provider = [];			 	 
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });						 
            },
            changePayoutStatus($actions){
                this.is_loading = true;			    	
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/" + $actions,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content')  + "&transaction_uuid=" + this.transaction_uuid ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          notify(response.data.msg,'success');
                          this.close();
                          this.$emit('afterSave');
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
        <div ref="modal_payout" class="modal"
        id="modal_payout" data-backdrop="static" 
        tabindex="-1" role="dialog" aria-labelledby="modal_payout" aria-hidden="true">
        
           <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
             <div class="modal-content"> 
             
             <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
                <div>
                  <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
                </div>
             </div>
             
             
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                        
              <div class="modal-body grey-bg">
              
              <div class="card p-3">
              
              <div class="row">
                <div class="col">		    
                 <div class="form-group">
                     <label class="m-0 mb-1">{{label.amount}}</label>
                     <h6 class="m-0">{{data.transaction_amount}}</h6>
                  </div> 		    
                  <div class="form-group">
                     <label class="m-0 mb-1">{{label.payment_method}}</label>
                     <h6 class="m-0">{{provider.payment_name}}</h6>
                     <p v-if="provider.is_online==='1'" class="m-0 text-muted font11">({{label.online_payment}})</p>
                     <p v-else class="m-0 text-muted font11">({{label.offline_payment}})</p>
                  </div> 		    
                  
                  <div class="form-group">
                     <label class="m-0 mb-1">{{label.status}}</label>
                     <h6 class="m-0">{{data.status}}</h6>
                  </div> 		    
                  
                </div>
                <!-- col -->
                
                <div class="col">		    
                 <div class="form-group">
                     <label class="m-0 mb-1">{{label.merchant}}</label>
                     <h6 class="m-0">{{merchant.restaurant_name}}</h6>
                  </div> 		    
                  <div class="form-group">
                     <label class="m-0 mb-1">{{label.date_requested}}</label>
                     <h6 class="m-0">{{data.transaction_date}}</h6>
                  </div> 		    
                </div>
                <!-- col -->
                
              </div> 
              <!-- row -->
              
              <h5>{{payment_to_account}}</h5>	
              
              
              <template v-if="data.provider==='paypal'">	  
              <p>{{data.email_address}}</p>
              </template>
              
              <template v-else-if="data.provider==='stripe'">	  		  
              <table class="table table-bordered">
               <tr>
                <td width="40%">{{label.account_number}}</td>
                <td>{{data.account_number}}</td>
               </tr>
               <tr>
                <td >{{label.account_name}}</td>
                <td>{{data.account_holder_name}}</td>
               </tr>
               <tr>
                <td >{{label.account_type}}</td>
                <td>{{data.account_holder_type}}</td>
               </tr>
               <tr>
                <td >{{label.account_currency}}</td>
                <td>{{data.currency}}</td>
               </tr>
               <tr>
                <td>{{label.routing_number}}</td>
                <td>{{data.routing_number}}</td>
               </tr>
               <tr>
                <td>{{label.country}}</td>
                <td>{{data.country}}</td>
               </tr>
              </table>		  
              </template>
              
              <template v-else-if="data.provider==='bank'">	  		  
               <table class="table table-bordered">
               <tr>
                <td width="40%">{{label.account_holders_name}}</td>
                <td>{{data.account_name}}</td>
               </tr>
               <tr>
                <td width="40%">{{label.iban}}</td>
                <td>{{data.account_number_iban}}</td>
               </tr>
               <tr>
                <td width="40%">{{label.switf_code}}</td>
                <td>{{data.swift_code}}</td>
               </tr>
               <tr>
                <td width="40%">{{label.bank_name}}</td>
                <td>{{data.bank_name}}</td>
               </tr>
               <tr>
                <td width="40%">{{label.bank_branch}}</td>
                <td>{{data.bank_branch}}</td>
               </tr>
              </table>
              </template>
                       
              </div>
              </div> <!-- body -->
              
               <div class="modal-footer">     
              
               <button type="button" class="btn btn-black" data-dismiss="modal">
                <span class="pl-3 pr-3">{{label.close}}</span>
               </button>
                               
               <button type="button" class="btn btn-yellow" :class="{ loading: is_loading }" 
               :disabled="isUnpaid"       
               @click="changePayoutStatus('cancelPayout')"  
               >
                <span class="pl-3 pr-3">{{label.cancel_payout}}</span>
               </button>
               
               <template v-if="provider.is_online==='1'">
                   <button type="button" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"	
                   :disabled="isUnpaid"        	           
                   @click="changePayoutStatus('approvedPayout')"  
                    >
                      <span>{{label.approved}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                    </button>
               </template>
               
               <template v-else>
               
                 <button type="button" class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }"	
                   :disabled="isUnpaid"      	           
                   @click="changePayoutStatus('payoutPaid')"  
                    >
                      <span>{{label.set_paid}}</span>
                      <div class="m-auto circle-loader" data-loader="circle-side"></div> 
                  </button>  
               
               </template>
                
                
              </div>
             
              
              </div> <!--content-->
          </div> <!--dialog-->
        </div> <!--modal-->   
        `  	   
    };
    
    /*
       PAYOUT
    */
    const app_payout = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables, 	  
           'components-payout-details': ComponentsPayoutDetails, 	  
        },		
        data(){
           return {		
                 is_loading : false,
                 summary : [],
                 count_up : undefined  	  	   	  
           };
        },
        mounted() {
            this.payoutSummary();
        },
        methods:{	
            payoutSummary(){
                
                this.is_loading = true;			    	
                axios({
                   method: 'POST',
                   url: api_url + "/payoutSummary",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){	
    
                          this.summary = response.data.details.summary;
                          
                          var $options = {							
                            decimalPlaces : 0,
                            separator : ",",
                            decimal : "."
                        };		
                                            
                          var $parameters = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                        };
                        
                        if(response.data.details.price_format.position=="right"){
                            $parameters.suffix = response.data.details.price_format.symbol;
                        } else $parameters.prefix = response.data.details.price_format.symbol;
                                              
                        this.count_up = new countUp.CountUp(this.$refs.ref_unpaid, this.summary.unpaid , $options);	
                        this.count_up.start();
                        
                        this.count_up = new countUp.CountUp(this.$refs.ref_paid, this.summary.paid , $options);	
                        this.count_up.start();
                                            
                        this.count_up = new countUp.CountUp(this.$refs.total_unpaid, this.summary.total_unpaid , $parameters);	
                        this.count_up.start();
                        
                        this.count_up = new countUp.CountUp(this.$refs.ref_total_paid, this.summary.total_paid , $parameters);	
                        this.count_up.start();
                        
                      } 
                      
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });					
                 
            },
            viewPayoutDetails(transaction_uuid){
                this.$refs.payout.transaction_uuid = transaction_uuid;
                this.$refs.payout.show();			
            },
            afterSave(){
                dump('afterSave');
                this.$refs.datatable.getTableData();
                this.payoutSummary();
            },
        },
    });
    const vm_payout = app_payout.mount('#vue-payout');
    
    
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
                dump(this.settings);			
                if ( this.settings.provider=="pusher" && this.settings.user_settings.enabled==1 ){
                    
                    this.beams = new PusherPushNotifications.Client({
                       instanceId: this.settings.pusher_instance_id ,
                    });
                    
                    /*this.beams.clearDeviceInterests()
                    .then(() => console.log('Device interests have been cleared'))
                    .catch(e => console.error('Could not clear device interests', e));
                    return false;*/
                    
                    /*this.beams.start()
                   .then(() => this.beams.removeDeviceInterest('order'))
                   .catch(e => console.error('Could not remove device interest', e));*/
                    
                    /*this.beams.getDeviceInterests()
                      .then(interests => {
                        console.log(interests) // Will log something like ["a", "b", "c"]
                      })
                      .catch(e => console.error('Could not get device interests', e));*/
                    
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
                       if(data.notification_type=="silent"){
                        //
                       } else {
                          this.playAlert();
                          this.addData(data);			   
                       }				   
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
                this.data.unshift($data);
                this.count++;     
                this.new_message = true;
                
                setTimeout(()=>{ 		
                    this.new_message = false;
                    //this.player.stop();
                }, 1000);        
                
                /*REFRESH SIDEBAR*/		    		    
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
       NOTIFICATIONS
    */
    const app_notifications = Vue.createApp({
        components: {
           'components-notification': ComponentsNotification, 	  	   
        },		
    });
    const vm_notifications = app_notifications.mount('#vue-notifications');	
    
    
    /*
      VIEW ORDER
    */
    const app_order_view = Vue.createApp({	
        components: {
           'components-orderinfo': ComponentsOrderInfo, 
           //'components-delay-order': ComponentsDelayForm, 
           //'components-rejection-forms': ComponentsRejectionForms, 
           'components-order-history': ComponentsOrderHistory, 
           'components-order-print': ComponentsOrderPrint, 
           //'components-menu': ComponentsMenu, 
           //'components-item-details': ComponentsItem, 
           'components-customer-details': ComponentsCustomer, 
           'components-merchant-transaction' : componentsMerchantTransaction,
       },
       data(){
        return {						
            order_uuid : '',	
            client_id : '',	
            merchant_id : '',	
            merchant_uuid : '',
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
                   /*data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                        'order_uuid' : this.order_uuid,	 			   	 
                      },*/
                      data : 'YII_CSRF_TOKEN='+$('meta[name=YII_CSRF_TOKEN]').attr('content') + "&order_uuid=" + this.order_uuid,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.group_name = response.data.details.group_name;
                          this.manual_status = response.data.details.manual_status;
                          this.modify_order = response.data.details.modify_order;		
                          this.merchant_id = response.data.details.merchant_id;
                          this.merchant_uuid = response.data.details.merchant_uuid;
                          this.client_id = response.data.details.client_id;
                          this.filter_buttons = response.data.details.filter_buttons;
                      } else {				
                          this.merchant_id = '';	
                          this.merchant_uuid = '';	
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
         viewMerchantTransaction(){     	
             this.$refs.merchant_transaction.merchant_uuid = this.merchant_uuid;
            this.$refs.merchant_transaction.show();
         },
       },
    });
    app_order_view.use(Maska);
    const vm_order_view = app_order_view.mount('#vue-order-view');
    
    
    /*
      ORDER HISTORY
    */
    const app_order_list = Vue.createApp({
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
                   url: api_url +"/getAllOrderSummary",
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
    const vm_order_list = app_order_list.mount('#vue-order-list');	
    
    
    /*
       COMPONENTS REPORTS MERCHANT SUMMARY
    */
    const ComponentsReportsMerchant = {
        props: ['ajax_url' , 'label' ],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        mounted() {	   	     	     	     	  
               this.ReportsMerchantSummary();
        },
        methods: {
             ReportsMerchantSummary(){
                 this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/ReportsMerchantSummary",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;					 	 	
                          
                          var $options = {							
                            decimalPlaces : 0,
                            separator : ",",
                            decimal : "."
                        };					
                        
                        var $count_up = new countUp.CountUp(this.$refs.total_registered,  response.data.details.total_registered , $options);	
                        $count_up.start();
                        
                        $count_up = new countUp.CountUp(this.$refs.commission_total,  response.data.details.commission_total , $options);	
                        $count_up.start();
                        
                        $count_up = new countUp.CountUp(this.$refs.membership_total,  response.data.details.membership_total , $options);	
                        $count_up.start();
                        
                        $count_up = new countUp.CountUp(this.$refs.total_active,  response.data.details.total_active , $options);	
                        $count_up.start();
                        
                        $count_up = new countUp.CountUp(this.$refs.total_inactive,  response.data.details.total_inactive , $options);	
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
        <div class="row">
           <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.total_registered}}</p><h5 ref="total_registered" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
            <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.commission_total}}</p><h5 ref="commission_total" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
            <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.membership_total}}</p><h5 ref="membership_total" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
            <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.total_active}}</p><h5 ref="total_active" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
            <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.total_inactive}}</p><h5 ref="total_inactive" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
          </div> <!--row-->
        `
    };
    
    
    
    /*
       COMPONENTS REPORTS ORDER EARNINGS
    */
    const ComponentsReportsEarnings = {
        props: ['ajax_url' , 'label' ],
        data(){
           return {						
              data : [],		 
              is_loading : false,
              date_start : '',
              date_end : '',
           };
        },   
        mounted() {	   	     	     	     	  
               this.ReportsOrderEarningSummary();
        },
        methods: {
            setDate(date_start, date_end){
                this.date_start = date_start;
                this.date_end = date_end;
                this.ReportsOrderEarningSummary();
            },
             ReportsOrderEarningSummary(){
                 this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/ReportsOrderEarningSummary",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&date_start=" + this.date_start + "&date_end=" + this.date_end ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;					 	 	
                          
                          var $options = {							
                            decimalPlaces : 0,
                            separator : ",",
                            decimal : "."
                        };					
                        
                        var $count_up = new countUp.CountUp(this.$refs.total_count,  response.data.details.total_count , $options);	
                        $count_up.start();
                        
                        var $options = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        };	
                        
                        $count_up = new countUp.CountUp(this.$refs.admin_earning,  response.data.details.admin_earning , $options);	
                        $count_up.start();
                        
                        $count_up = new countUp.CountUp(this.$refs.merchant_earning,  response.data.details.merchant_earning , $options);	
                        $count_up.start();
                        
                        $count_up = new countUp.CountUp(this.$refs.total_sell,  response.data.details.total_sell , $options);	
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
        <div class="row">
           <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.total_count}}</p><h5 ref="total_count" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
            <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.admin_earning}}</p><h5 ref="admin_earning" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
            <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.merchant_earning}}</p><h5 ref="merchant_earning" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
            
            <div class="col">
              <div class="bg-light p-3 mb-3 rounded">   
               <div class="d-flex">
                <p class="m-0 mr-2 text-muted text-truncate">{{label.total_sell}}</p><h5 ref="total_sell" class="m-0">0</h5>
               </div>  	  
              </div><!-- bg-light-->
            </div> <!--col-->
                    
          </div> <!--row-->
        `
    };
    
    
    /*
       POPUP VIEW DATA
    */
    const ComponentsViewData = {
        props: ['ajax_url' , 'label' ,'limit' , 'method'],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        mounted() {	   	     	     	     	  
               
        },
        methods: {
             show(view_id){												
                 this.viewData(view_id);
                $( this.$refs.view_data ).modal('show');					    
            },
            close(){							
                $( this.$refs.view_data ).modal('hide');			    			   
            },
            viewData(view_id){
                this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url+"/" + this.method ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&view_id=" + view_id,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){					 	 	
                          this.data = response.data.details;
                      } else {						 	 	
                          notify( response.data.msg ,'error')
                      }			 	 			 	
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			
            },
        },
        template: `	
        <div ref="view_data" class="modal" tabindex="-1" role="dialog" >
        <div :class="{ 'modal-lg': data.type=='email' }" class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          
          <div class="modal-body position-relative">
          
          <div v-if="is_loading" class="loading cover-loader d-flex align-items-center justify-content-center">
            <div>
              <div class="m-auto circle-loader medium" data-loader="circle-side"></div> 
            </div>
          </div>
          
          <h5 class="mb-4">{{label.title}}</h5>	  
          
          <div class="view-content p-4 rounded"  :class="data.type" >	  
             <span v-html="data.content"></span>
          </div>
          
         
          </div> <!-- body -->
          
          <div class="modal-footer border-0">            
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
      VUE TABLES
    */
    const app_tables = Vue.createApp({
        components: {
           'components-datatable': ComponentsDataTables,	   	   
           'components-reports-merchant': ComponentsReportsMerchant,
           'components-reports-earnings': ComponentsReportsEarnings,
           'components-view-data': ComponentsViewData,
        },		
        data(){
           return {		
                 is_loading : false,		   	  
           };
        },
        methods:{		
            afterSelectdate(date_start, date_end){    		
                this.$refs.summary_earnings.setDate(date_start, date_end);
            },
            view(data){    		
                this.$refs.view_data.show(data.view_id);
            }
        },
    });
    app_tables.use(Maska);
    const vm_tables = app_tables.mount('#vue-tables');
    
    
    
    /* 
      COMPONETS SALES OVERVIEW
    */
    const ComponentsSalesSummary = {
        props: ['ajax_url' , 'label' ,'merchant_type', 'domain' ],
        data(){
           return {						
             data : [],		 
             is_loading : false,
           };
        },   
        mounted() {	   	     	    
            this.salesSummary();
            this.validateIdentity();
        },
        methods: {
             salesSummary(){
                 
                 axios({
                   method: 'POST',
                   url: this.ajax_url+"/commissionSummary" ,
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') ,
                   timeout: $timeout,
                 }).then( response => {	 
                      var $count_up;
                      if(response.data.code==1){						 	 	
                          
                        $count_up = this.count_up = new countUp.CountUp(this.$refs.commission_week, response.data.details.commission_week ,  {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        });	
                        $count_up.start();
                        
                        $count_up = this.count_up = new countUp.CountUp(this.$refs.commission_month, response.data.details.commission_month ,  {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        });	
                        $count_up.start();
                                                                
                        $count_up = this.count_up = new countUp.CountUp(this.$refs.subscription_month, response.data.details.subscription_month ,  {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        });	
                        $count_up.start();					
                        
                      } else {				 	 	
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     
                 });		
             },		
            validateIdentity(){
                //var url = window.location.href;
                var domain =  window.location.hostname; //  window.location.hostname; //url.replace('http://','').replace('https://','').split(/[/?#]/)[0];						
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
        template: `	    
        <div class="row">
            <div class="col mb-3 mb-xl-0">
              <div class="card">
                <div class="card-body" style="padding:10px;">
                
                  <div id="boxes" class="d-flex align-items-center">
                    <div class="mr-2"><div class="rounded box box-1 d-flex align-items-center  justify-content-center"><i class="zmdi zmdi-money-box"></i></div></div>
                    <div>
                       <h6 class="m-0 text-muted font-weight-normal">{{label.commission_week}}</h6>
                       <h6 class="m-0 position-relative" ref="commission_week">0
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
                       <h6 class="m-0 text-muted font-weight-normal">{{label.commission_month}}</h6>
                       <h6 class="m-0 position-relative" ref="commission_month">0
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
                       <h6 class="m-0 text-muted font-weight-normal">{{label.subscription_month}}</h6>
                       <h6 class="m-0 position-relative" ref="subscription_month">0
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
         
          <div class="row">
            <div class="col  mb-3 mb-xl-0">        
              <h5 class="m-0">{{label.title}}</h5>   
              <p class="m-0 text-muted">{{label.sub_title}}</p>        
            </div>
            <div class="col ">
    
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
                </td>
               <td width="15%" class="text-left align-middle">
                  {{item.restaurant_name}}
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
        updated () {
            
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
            <div class="col">        
              <h5 v-if="item_tab[currentTab]" class="m-0">{{item_tab[currentTab].title}}</h5>   
              <p  v-if="item_tab[currentTab]" class="m-0 text-muted">{{item_tab[currentTab].sub_title}}</p>        
            </div>
            <div class="col">
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
          <div class="mt-3 table-item">
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
                        <a ><img :src="item.image_url" class="img-60 rounded-circle"></a>
                     </div>
                     <div class="flex-col">
                        <a class="font-weight-bold hover-text-primary mb-1 text-green">{{item.item_name}}</a>
                        <p class="m-0 text-muted">{{item.category_name}}</p>                    
                        <p class="m-0 text-muted"><b>{{item.restaurant_name}}</b></p>
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
           </template>
           
           <template v-else>
           <components-item-sales
             :ajax_url="ajax_url"
             period="30"
             :label="label"
           >
           </components-item-sales>
           </template>            
            
            <div v-if="!hasData" class="fixed-height40 text-center justify-content-center d-flex align-items-center">
                <div class="flex-col">
                 <img v-if="data_failed.details" class="img-300" :src="data_failed.details.image_url" />
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
      COMPONENTS POPULAR BY REVIEW
    */
    const ComponentsPopularMerchantReview = {
        props: ['ajax_url' , 'label' ,'limit'],
        data(){
           return {						
             data : [],		 
             is_loading : false,
             cuisine_list : [],
             data_failed : [],
           };
        },   
        mounted() {	   	     	     	     	  
               this.PopularMerchantByReview();
        },
         computed: {
           hasData(){			
                  if ((typeof  this.data.data !== "undefined") && ( this.data.data !== null)) { 	   
               if(this.data.data.length>0){
                  return true;
               } 
                  }
               return false;
           },		
        },
        methods: {
             PopularMerchantByReview(){
                 
                 this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/PopularMerchantByReview",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&limit=" + this.limit  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;		
                          this.cuisine_list = response.data.details.cuisine_list;		
                      } else {		 	 	
                          this.data = [];
                          this.data_failed = response.data;
                          this.cuisine_list = [];
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
           
           <div class="table-responsive-md">
           <table class="table mt-3">
              <thead>
               <tr>
                 <th class="p-0"></th>
                 <th class="p-0" width="20%"></th>             
               </tr>
              </thead>
              <tbody>
                <tr v-for="item in data.data" >
                 <td class="text-left align-middle">
                   <div class="d-flex  align-items-center"> 
                     <div class="mr-3">
                        <a ><img :src="item.image_url" class="img-60 rounded"></a>
                     </div>
                     <div class="flex-col w-70">
                        <a :href="item.view_merchant" class="font-weight-bold hover-text-primary mb-1 text-green">{{item.restaurant_name}}</a>                              
                        <p v-if="cuisine_list[item.merchant_id]" class="text-truncate m-0">
                          <span v-for="cuisine in cuisine_list[item.merchant_id]" class="a-12 mr-1">{{cuisine}},</span>                      
                        </p>         
                        <p  class="m-0"><b class="mr-1">{{item.review_count}}</b><i class="zmdi zmdi-star mr-1 text-grey"></i><u>{{item.ratings}}+ ratings</u></p>                                 
                     </div>
                   </div> <!--flex-->
                 </td>
                 <td class="text-right align-middle">
                 {{item.ratings}}
                 </td>
                 </tr>            
              </tbody>
            </table>
            </div>
        
            <template v-if="!is_loading">
             <div v-if="!hasData" class="fixed-height35 text-center justify-content-center d-flex align-items-center">
                <div class="flex-col">
                 <img v-if="data_failed.details" class="img-300" :src="data_failed.details.image_url" />
                 <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
                </div>     
             </div>   
            </template>
             
        `
    };
    
    
    /*
      COMPONENTS POPULAR MERCHANT
    */
    const ComponentsPopularMerchant = {
        components: {
          'components-merchant-popular-review' : ComponentsPopularMerchantReview
        },
        props: ['ajax_url' , 'label' ,'limit' ,'item_tab'],
        data(){
           return {						
             data : [],		 
             data_failed : [],
             is_loading : false,
             currentTab : 'popular',
             cuisine_list : []
           };
        },   
        mounted() {	   	     	     	     	  
               this.popularMerchant();
        },
        computed: {
           hasData(){				  
                  if ((typeof  this.data.data !== "undefined") && ( this.data.data !== null)) { 	   
               if(this.data.data.length>0){
                  return true;
               } 
                  }
               return false;
           },		
        },
        methods: {
             
            popularMerchant(){    	
                this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/popularMerchant",
                   data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content') + "&limit=" + this.limit  ,
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		
                          this.data = response.data.details;		
                          this.cuisine_list = response.data.details.cuisine_list;		
                      } else {		 	 	
                          this.data = [];
                          this.data_failed = response.data;
                          this.cuisine_list = [];
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
            <div class="col">        
              <h5 v-if="item_tab[currentTab]" class="m-0">{{item_tab[currentTab].title}}</h5>   
              <p  v-if="item_tab[currentTab]" class="m-0 text-muted">{{item_tab[currentTab].sub_title}}</p>        
            </div>
            <div class="col">
              
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
                 <a class="dropdown-item" @click="currentTab=key"  :class="{active : currentTab==key}"   >{{item.title}}</a>			
                 </template>
                </div>
              </div>
    
              </div>
              <!-- dropdown -->
    
    
            </div> <!-- col -->
          </div>  
          <!--row-->	
          
          
        <div v-if="currentTab=='popular'" class="mt-3 table-item table-responsive">
            <table class="table" >
              <thead>
               <tr>
                 <th class="p-0" ></th>
                 <th class="p-0" width="20%" ></th>             
               </tr>
              </thead>
              <tbody>
                <tr v-for="item in data.data" >
                 <td class="text-left align-middle">
                   <div class="d-flex align-items-center"> 
                     <div class="mr-3">
                        <a ><img :src="item.image_url" class="img-60 rounded"></a>
                     </div>
                     <div class="flex-col w-70 text-truncate cuisine-truncate">
                        <a :href="item.view_merchant" class="font-weight-bold hover-text-primary mb-1 text-green">{{item.restaurant_name}}</a>                              
                        <p v-if="cuisine_list[item.merchant_id]" class="text-truncate m-0">
                        <span v-for="cuisine in cuisine_list[item.merchant_id]" class="a-12 mr-1">{{cuisine}},</span>                      
                        </p>         
                        <p v-if="item.ratings" class="m-0"><b class="mr-1">{{item.ratings.review_count}}</b><i class="zmdi zmdi-star mr-1 text-grey"></i><u>{{item.ratings.rating}}+ {{label.ratings}}</u></p>                                 
                     </div>
                   </div> <!--flex-->
                 </td>
                 <td class="text-right align-middle">
                   <p class="m-0 text-muted">{{item.total_sold_pretty}}</p>
                 </td>
                 </tr>            
              </tbody>
            </table>
            
             <div v-if="!hasData" class="fixed-height35 text-center justify-content-center d-flex align-items-center">
                <div class="flex-col">
                 <img v-if="data_failed.details" class="img-300" :src="data_failed.details.image_url" />
                 <h6 class="mt-3 text-muted font-weight-normal">{{data_failed.msg}}</h6>
                </div>     
             </div>   
            
          </div>   
          
          <template v-else>
           <components-merchant-popular-review
             :ajax_url="ajax_url"         
             :label="label"
           >
           </components-merchant-popular-review>
           </template>     
    
           
                 
          
        
        </div> <!--card body-->
        </div> <!--card--> 
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
                        
                        $count_up = this.count_up = new countUp.CountUp(this.$refs.stats_new_customer,  response.data.details.new_customer , $options);	
                        $count_up.start();		
                                                        
                        var $options = {							
                            decimalPlaces : response.data.details.price_format.decimals,
                            separator : response.data.details.price_format.thousand_separator,
                            decimal :  response.data.details.price_format.decimal_separator,
                            prefix : response.data.details.price_format.symbol,
                        };		   
                        
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
        
        <div class="row mb-3 ">
              <div class="col  mb-2 mb-xl-0">
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
    
               <div class="col  mb-2 mb-xl-0">
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
          
           <div class="dashboard-statistic position-relative mb-3 ">
            
            <div class="row">
              <div class="col mb-2 mb-xl-0">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center">
                         <div class="flex-col mr-3"><h1 class="m-0"><i class="zmdi zmdi-face"></i></h1></div>
                         <div class="flex-col">
                           <h3 class="mb-1 text-violet" ref="stats_new_customer">0</h3>
                           <h5 class="m-0 text-secondary">{{label.new_customer}}</h5>
                         </div>
                      </div>
                    </div>
                  </div>
              </div> <!--col-->
    
               <div class="col mb-2 mb-xl-0">
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
       COMPONENTS RECENT PAYOUT   
    */
    const ComponentsRecentPayout = {
        props: ['ajax_url' , 'label' ,'limit'],
        data(){
           return {						
             data : [],		 
             data_failed : [],
             is_loading : false,
           };
        },   
        mounted() {	   	     	     	     	  
               this.RecentPayout();
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
             RecentPayout(){
                 this.is_loading = true;	
                axios({
                   method: 'POST',
                   url: this.ajax_url +"/RecentPayout",
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
           <h5 class="m-0 mb-3">{{label.recent_payout}}</h5>    
           
            <table class="table">
              <thead>
               <tr>
                 <th class="p-0 mw-200"></th>
                 <th class="p-0 mw-200"></th>             
               </tr>
              </thead>
              <tbody>
                <tr v-for="item in data" >
                 <td class="text-left align-middle">	               
                   <div class="d-flex  align-items-center"> 
                     <div class="mr-3">
                       <a @click="$emit('viewPayout',item.transaction_uuid)">
                       <img :src="item.image_url" class="img-60 rounded-circle">
                       </a>
                     </div>
                     <div class="flex-col">
                        <a @click="$emit('viewPayout',item.transaction_uuid)" href="javascript:;" class="font-weight-bold hover-text-primary mb-1">
                         {{item.restaurant_name}}
                        </a>	            
                        <div><small class="text-muted">{{item.transaction_date}}</small></div>
                        <div class="badge payment " :class="item.status_class" >{{item.status}}</div>
                     </div>
                   </div> <!--flex-->
                 </td>
                 <td class="text-right align-middle">
                   <p class="m-0 text-muted">{{item.transaction_amount_pretty}}</p>
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
      VUE DASHBOARD
    */
    const app_dashboard = Vue.createApp({	   
       components: {
           'components-sales-summary': ComponentsSalesSummary,  
           'components-last-orders': ComponentsLastOrders,
           'components-popular-items': ComponentsPopularItems, 
           'components-chart-sales': ComponentsChartSalesOverview, 
           'components-popular-customer': ComponentsPopularCustomer, 	   
           'components-latest-review': ComponentsLatestReview, 	   
           'components-popular-merchant': ComponentsPopularMerchant, 	
           'components-daily-statistic': ComponentsDailyStatistic, 	
           'components-recent-payout': ComponentsRecentPayout,
           'components-customer-details': ComponentsCustomer, 	
           'components-payout-details': ComponentsPayoutDetails, 	  
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
               url: api_url +"/dashboardSummary",
               data : 'YII_CSRF_TOKEN=' + $('meta[name=YII_CSRF_TOKEN]').attr('content'),
               timeout: $timeout,
             }).then( response => {	 
                  if(response.data.code==1){					 	 	
                      var $options = {							
                        decimalPlaces : 0,
                        separator : ",",
                        decimal : "."
                    };					
                    
                    var $total_merchant = new countUp.CountUp(this.$refs.summary_merchant,  response.data.details.total_merchant , $options);	
                    $total_merchant.start();
                                                    
                    var $options = {							
                        decimalPlaces : response.data.details.price_format.decimals,
                        separator : response.data.details.price_format.thousand_separator,
                        decimal :  response.data.details.price_format.decimal_separator,
                        prefix : response.data.details.price_format.symbol,
                    };		   
                    
                    var $count_up;
                    $count_up = this.count_up = new countUp.CountUp(this.$refs.summary_commission,  response.data.details.total_commission , $options);	
                    $count_up.start();		       		       
                    
                    $count_up = new countUp.CountUp(this.$refs.summary_sales, response.data.details.total_sales , $options);	
                    $count_up.start();		 	 	
                                    
                    $count_up = new countUp.CountUp(this.$refs.summary_subscriptions, response.data.details.total_subscriptions , $options);	
                    $count_up.start();		 	 	
            
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
             viewPayout(transaction_uuid){   	  	 
                  this.$refs.payout.transaction_uuid = transaction_uuid;
             this.$refs.payout.show();	
             },
             afterSave(){						
              this.$refs.recent_payout.RecentPayout();
          },
       },
    });
    const vm_dashboard = app_dashboard.mount('#vue-dashboard');
    
    
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
    
    /*
      ADDON MANAGER
    */
    const app_addon_manager = Vue.createApp({
        data(){
            return {						
                active_tab : 'addontab1',			
                is_loading : false,
                loading_addons : false,
                loading_activated : false,
                data : [],
                data_addons : []
            };
        },   
        mounted() {
            this.getAddons();		
            this.getAvailableAddons();	
        },	 
        computed: {
            hasData(){						
                if(this.data.length>0){
                   return true;
                } 
                return false;
            },
            hasAddons(){						
                if(this.data_addons.length>0){
                   return true;
                } 
                return false;
            },
        },
        methods :{
            getAddons(){
                this.is_loading = true;   	    		
                axios({
                   method: 'POST',
                   url: api_url+"/getAddons",
                   data : "YII_CSRF_TOKEN=" + $('meta[name=YII_CSRF_TOKEN]').attr('content'),			    
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
            enabledDisabledAddon(data){			  
                this.loading_activated = true;
                axios({
                    method: 'put',
                    url: api_url+"/enableddisabledaddon",
                    data : {
                         'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),
                         'uuid' : data.uuid,
                         'activated' : data.activated
                       },
                    timeout: $timeout,
                  }).then( response => {	 
                       if(response.data.code==1){		 	 	
                          ElementPlus.ElNotification({			
                            title: response.data.details.title,			
                            message: response.data.msg,
                            position: 'bottom-right',
                            type: 'success',
                          });
                       } else {			 	 				 	 	
                          ElementPlus.ElNotification({						
                            title: response.data.details.title,			
                            message: response.data.msg,
                            type: 'warning',
                          });
                       }
                  }).catch(error => {	
                     //
                  }).then(data => {			     
                    this.loading_activated = false;
                  });			 	 
            },
            getAvailableAddons(){			
                this.loading_addons = true;   	    		
                axios({
                   method: 'POST',
                   url: "https://bastisapp.com/activation/addons",
                   data : "",
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){		 	 	
                          this.data_addons = response.data.details.data;					
                      } else {			 	 				 	 	
                        this.data_addons = [];
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.loading_addons = false;
                 });			 	
            },
        },
    });
    app_addon_manager.use(ElementPlus);
    const vm_addon_manager = app_addon_manager.mount('#vue-addons');
    
    
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
                $(this.$refs.modal).modal('show');
            },
            close(){
                $(this.$refs.modal).modal('hide');
            },		
            setMessage(new_message){
                this.new_message = new_message;
            },
        },
        template:`
        <div class="modal" ref="modal"  tabindex="-1" role="dialog"  aria-hidden="true"
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
      BANK PAYMENT REGISTRATION
    */
    const app_bankreg = Vue.createApp({		
        data(){
            return {						
              data : []
            };
        },   
        mounted() {
            this.initProvider();
        },
        methods:{		
            initProvider(){		
                try {
                    this.$refs[provider_selected].PaymentRender();
                 } catch(err) {  	 	  	       
                   console.debug(err);
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
        },
    });
    
    if ((typeof  components_bundle !== "undefined") && ( components_bundle !== null)) {	
        $.each(components_bundle,function(components_name, components_value) {			
            app_bankreg.component(components_name, components_value );
        });
    }
    app_bankreg.component('components-loading-box',componentLoadingBox);
    app_bankreg.use(ElementPlus);
    const vm_bankreg = app_bankreg.mount('#vue-bankregistration');
    
    
    
    /*
      COMPONENTS PHONE NUMBER
    */
    const ComponentsPhoneNumber = {
        props: ['label','default_country','only_countries','phone','field_phone','field_phone_prefix'],		
        data(){
           return {
              data : [],		  
              country_flag : '',
              mobile_prefix : '',
              mobile_number : ''
           };	
        },	
        updated () {				
            
        },
        mounted () {
          this.mobile_number = this.phone;
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
                         //this.$emit('update:mobile_prefix', data.details.default_data.phonecode );
                         this.mobile_prefix = data.details.default_data.phonecode;
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
              this.mobile_prefix = data.phonecode;
                 //this.$emit('update:mobile_prefix', data.phonecode );       	
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
            <input type="hidden" :name="field_phone_prefix" :value="mobile_prefix" />
            <input :name="field_phone" type="text"  v-maska="'###################'"  ref="ref_mobile_number"
            :value="mobile_number"  >
            
        </div> <!--inputs-->
        `
    };
    
    
    /*
      PHONE
    */
    const app_phone = Vue.createApp({		
        components: {    	    	 
            'component-phone' : ComponentsPhoneNumber,  		
         },
        data(){
            return {						
                data :[]
            };
        }
    });
    app_phone.use(Maska);
    const vm_phone = app_phone.mount('#app-phone');
    
    
    const ComponentsCalendar = {	
        props: ['label','size','ajax_url'],
        data(){
            return {
                loading : true,
                is_loading : false,			
                sched_calendar : null,
                schedule_uuid : '',
                driver_id : '',
                car_id : '',
                date_start : '',
                time_start : '',
                time_end : '',
                instructions :''
            };
        },
        created () {
            this.initCalendar();		
        },
        computed: {
            hasData(){			
                let length = this.schedule_uuid.length;			
                if(length>0){
                    return true;
                }
                return false;
            },
        },
        mounted () {
            this.select2Driver();
            this.select2Car();
        },
        methods :{ 
            newSchedule(){
                this.clearForm();
                $( this.$refs.modal_sched ).modal('show');
            },
            initCalendar(){			
                document.addEventListener('DOMContentLoaded', ()=> {				
                    this.sched_calendar = new FullCalendar.Calendar(this.$refs.calendar, {
                      //initialView: 'dayGridMonth',	  
                      initialView: 'dayGridMonth',
                        headerToolbar: {
                            right: 'today prev,next',
                            center: 'title',
                            left: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                        },
                      selectable: true,
                      unselectAuto : true,	
                      loading: (isLoading )=>{					
                        this.loading = isLoading;
                      },			  
                      eventSources: [		
                        {
                          url: api_url+"/getDriverSched",
                          method: 'POST',		  					  
                        //   color: '#3ecf8e',   	
                        //   textColor : "red",					  
                          success: ()=>{
                            //					
                          },
                          failure: ()=> {						
                            ElementPlus.ElNotification({						
                                title: "Error",			
                                message: "there was an error while fetching data",
                                type: 'warning',
                                position: 'bottom-right',
                              });						
                          },				  
                        }
                      ],
                      dateClick: (info)=> {
                        this.clearForm();					
                        this.date_start = info.dateStr;
                        $( this.$refs.modal_sched ).modal('show');
                      },	 	
                      eventClick: (info)=> {					
                        this.getSchedule(info.event.id);
                      },		
                      eventContent: (info , createElement)=> {
                        let data = info.event.extendedProps;
                        console.debug(data);
                        let html = '';					
                        html +='<div class="fc-content"><span class="fc-time"></span><span class="fc-title">';
                            html +='<div>'+data.time+'</div>';
                            html +='<div>'+data.plate_number+'</div>';
                            html +='<div class="mytable">';						  
                               html +='<div class="mycol">';	
                               html +='<img class="circle_image" src="'+data.avatar+'">';
                               html +='</div>';	
                               html +='<div class="mycol"><span class="d-inline-block text-truncate" style="max-width: 90px;">'+ data.name+'</span></div>';
                            html +='</div>';
                        html +='</span></div>';
                        return {html : html};
                        //return createElement('i', {}, html)
                      }		
                    });
                    this.sched_calendar.render();
                });
            },
            select2Driver(){			
                $(this.$refs.driver_list).select2({
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
                      url: this.ajax_url +"/searchDriver",
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
            select2Car(){			
                $(this.$refs.car_list).select2({
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
                      url: this.ajax_url +"/searchCar",
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
            submit(){
                this.is_loading = true;   
                this.driver_id = $( this.$refs.driver_list ).find(':selected').val();
                this.car_id = $( this.$refs.car_list ).find(':selected').val();
    
                axios({
                   method: 'put',
                   url: this.ajax_url+"/addSchedule",
                   data : {
                        'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		
                      schedule_uuid : this.schedule_uuid,
                      driver_id : this.driver_id,
                      vehicle_id : this.car_id,
                      date_start : this.date_start,
                      time_start : this.time_start,
                      time_end : this.time_end,
                      instructions : this.instructions
                      },
                   timeout: $timeout,
                 }).then( response => {	 
                      if(response.data.code==1){
                        ElementPlus.ElNotification({			
                            title: this.label.success,
                            message: response.data.msg,
                            position: 'bottom-right',
                            type: 'success',
                          });
                          this.afterInsert();
                      } else {			 	 	
                        ElementPlus.ElNotification({						
                            title: this.label.error,
                            message: response.data.msg,
                            position: 'bottom-right',
                            type: 'warning',
                          });
                      }
                 }).catch(error => {	
                    //
                 }).then(data => {			     
                     this.is_loading = false;
                 });			 	    			
            },
            getSchedule(scheduleId){
                this.loading = true;
                axios({
                    method: 'put',
                    url: this.ajax_url+"/getSchedule",
                    data : {
                      'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		
                      schedule_uuid : scheduleId,				
                    },
                    timeout: $timeout,
                  }).then( response => {	 
                       if(response.data.code==1){					  
                          const $data = response.data.details;
                          this.clearForm();
                          $( this.$refs.modal_sched ).modal('show');
    
                          this.schedule_uuid = $data.sched.schedule_uuid;
                          this.driver_id = $data.sched.driver_id;
                          this.car_id = $data.sched.car_id;
                          this.date_start = $data.sched.date_start;
                          this.time_start = $data.sched.time_start;
                          this.time_end = $data.sched.time_end;
                          this.instructions = $data.sched.instructions;
    
                          setTimeout(() => {
                            var newOption = new Option($data.driver.text, $data.driver.id, false, false);					  
                            $( this.$refs.driver_list ).append(newOption).trigger('change');					 					  
    
                            var newOption = new Option($data.car.text, $data.car.id, false, false);
                            $( this.$refs.car_list ).append(newOption).trigger('change');			
                            
                            $( this.$refs.driver_list ).val($data.driver.id).trigger('change');
                            $( this.$refs.car_list ).val($data.car.id).trigger('change');
    
                          }, 1); 
                       } else {			 	 	
                         ElementPlus.ElNotification({						
                             title: this.label.error,
                             message: response.data.msg,
                             position: 'bottom-right',
                             type: 'warning',
                           });
                       }
                  }).catch(error => {	
                     //
                  }).then(data => {			     
                      this.loading = false;
                  });			 	    	
            },
            ConfirmDeleteSchedule(){
                ElementPlus.ElMessageBox.confirm(
                this.label.delete_message,
                this.label.delete_confirm,
                {
                    confirmButtonText: this.label.ok,
                    cancelButtonText: this.label.cancel,
                    type: 'warning',
                }
                )
                .then(() => {				
                    this.deleteSchedule();
                }).catch(() => {
                    //
                });
            },
            deleteSchedule(){			
                this.delete_loading = false;
                axios({
                    method: 'put',
                    url: this.ajax_url+"/deleteSchedule",
                    data : {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		
                       schedule_uuid : this.schedule_uuid,				   
                    },
                    timeout: $timeout,
                  }).then( response => {	 
                       if(response.data.code==1){
                         ElementPlus.ElNotification({			
                             title: this.label.success,
                             message: response.data.msg,
                             position: 'bottom-right',
                             type: 'success',
                           });
                           this.afterDelete();
                       } else {			 	 	
                         ElementPlus.ElNotification({						
                             title: this.label.error,
                             message: response.data.msg,
                             position: 'bottom-right',
                             type: 'warning',
                           });
                       }
                  }).catch(error => {	
                     //
                  }).then(data => {			     
                      this.delete_loading = false;
                  });			 	    			
    
            },
            afterInsert(){
                $( this.$refs.modal_sched ).modal('hide');
                this.clearForm();			
                this.sched_calendar.refetchEvents();		  
            },
            afterDelete(){
                $( this.$refs.modal_sched ).modal('hide');
                this.clearForm();			
                this.sched_calendar.refetchEvents();		  
            },
            clearForm(){
                this.schedule_uuid = '';
                this.driver_id = '';
                this.car_id = '';
                this.date_start ='';
                this.time_start ='';
                this.time_end = '';
                this.instructions = '';
                $( this.$refs.driver_list ).val(null).trigger('change');
                $( this.$refs.car_list ).val(null).trigger('change');
            }
            //
        },
        template: `	
        <div v-loading="loading" >
           <div id="fs-calendar" ref="calendar"></div>
        </div>
    
        <div ref="modal_sched" class="modal" role="dialog" data-backdrop="static" >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            
        <form @submit.prevent="submit" >	  
    
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{label.title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body position-relative">
                      
          <div class="form-group">  
              <label class="mr-4" >Date</label>
              <div> 
                <el-date-picker
                    v-model="date_start"
                    type="date"
                    placeholder="Pick a date"	   	   
                    size="default"				
                    />          
                </div>	
         </div>
    
         <div class="row">
           <div class="col"> 
                <div class="form-group">  
                    <label class="mr-4" >Time start</label>	
                    <div>
                    <el-time-select
                    v-model="time_start"
                    start="08:00"
                    step="00:10"
                    end="24:00"			
                    placeholder="Select time"
                    />	 
                    </div>
                </div>
           </div>
           <div class="col"> 
                <div class="form-group">  
                    <label class="mr-4" >Time ends</label>	
                    <div>
                    <el-time-select
                    v-model="time_end"
                    start="08:00"
                    step="00:10"
                    end="24:00"
                    placeholder="Select time"
                    />	 
                    </div>
                </div>
           </div>
         </div>
         <!-- row -->
              
    
         <div class="form-group">  
            <label class="mr-4" >Select Driver</label> 
            <select ref="driver_list" class="form-control select2-driver"  style="width:100%">	  	  
            </select>
        </div>	
    
        <div class="form-group">  
        <label class="mr-4" >Select Car</label> 
        <select ref="car_list" class="form-control select2-car"  style="width:100%">	  	  
        </select>
        </div>	
    
        <div class="form-group">  
        <label class="mr-4" >Instructions</label> 
        <el-input
            v-model="instructions"
            :rows="2"
            type="textarea"
            placeholder="Add instructions to driver"
        />
        </div>	
      
          </div>      
          <div class="modal-footer">     	
    
            <button type="button" class="btn btn-black pl-4 pr-4"  data-dismiss="modal">
              <span>{{label.close}}</span>          
            </button>
    
            <button v-if="hasData" type="button" class="btn btn-danger pl-4 pr-4" @click="ConfirmDeleteSchedule">
              <span>{{label.delete}}</span>          
            </button>
    
            <button type="submit"  class="btn btn-green pl-4 pr-4" :class="{ loading: is_loading }" >
            <span v-if="!hasData">{{label.save}}</span>
            <span v-else>{{label.update}}</span>
            <div class="m-auto circle-loader" data-loader="circle-side"></div> 
          </button>
          </div>
                
    
        </div>
      </div>
      </form>
    </div>        
        `
    };
    
    /*
    DIVER CALENDAR  
    */
    const app_sched = Vue.createApp({		
        components: {    	    	 
            'component-calendar' : ComponentsCalendar,  		
         },
        data(){
            return {						
                data :[]
            };
        },	
        methods: {
            newSchedule(){
                console.debug('newSchedule');
                this.$refs.appcalendar.newSchedule();
            }
        },
    });
    app_sched.use(ElementPlus);
    const vm_sched = app_sched.mount('#app-schedule');
    
    
    /*
     DRIVER OVER VIEW 
    */
    const ComponentsDriverOverview = {
        props: ['label','ajax_url','driver_uuid'],
        data() {
            return {
                loading : false,
                loading_activity : false,
                overview_data : [],
                tableData : []			
            };
        },	
        mounted() {
            this.getDriverOverview();
            this.getDriverActivity();
        },
        methods: {
            getDriverOverview(){
                this.loading = true;
                axios({
                    method: 'put',
                    url: this.ajax_url+"/getDriverOverview",
                    data : {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		
                       driver_uuid : this.driver_uuid,				   
                    },
                    timeout: $timeout,
                  }).then( response => {	 
                       if(response.data.code==1){		
                          this.overview_data = response.data.details;
                       } else {
                          this.overview_data = [];
                       }
                  }).catch(error => {	
                     //
                  }).then(data => {			     
                    this.loading = false;
                  });			 	    	
            },
            getDriverActivity(){
                this.loading_activity = true;
                axios({
                    method: 'put',
                    url: this.ajax_url+"/getDriverActivity",
                    data : {
                       'YII_CSRF_TOKEN':$('meta[name=YII_CSRF_TOKEN]').attr('content'),		
                       driver_uuid : this.driver_uuid,				   
                    },
                    timeout: $timeout,
                  }).then( response => {	 
                       if(response.data.code==1){		
                          this.tableData = response.data.details.data;
                       } else {
                          this.tableData = [];
                       }
                  }).catch(error => {	
                     //
                  }).then(data => {			     
                    this.loading_activity = false;
                  });			 	    				
            },
        },
        template: '#xtemplate_overview',	
    };
    
    
    const app_driveroverview = Vue.createApp({			
        components: {    	    	 
            'component-driveroverview' : ComponentsDriverOverview,  		
         },
        data(){
            return {						
                data :[]
            };
        },		
    });
    app_driveroverview.use(ElementPlus);
    const vm_driveroverview = app_driveroverview.mount('#app-driveroverview');
    
    
    })(jQuery); 
    /*end strict*/