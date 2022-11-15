var timeRanges = null;
$(document).ready(function(e){
    var isMobile = false; //initiate as false
    // device detection
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
        isMobile = true;
    }
    // setTimeout(function() { 
        // if($('#section-address').length > 0){
        //     $(document).find('.el-drawer__body').html('');
        //     $(document).find('.el-drawer__body').append($('#section-address').html());
        // }
    // }, 2500);
    setTimeout(function() { 
        if($('#footer_image').length > 0 && $('#footer_image').val() != ''){
            // $('.footer-logo img').attr('src',$('#footer_image').val()); 
            var img = $('#footer_image').val();
            // $('.section-join-us').css('background','url("'+img+'") no-repeat #f6f7f8');
            if($('#footer_facebook').val() == '' || $('#footer_facebook').val() == '#')
                $('.social-list .facebook').hide(); 
            else
                $('.social-list .facebook').attr('href',$('#footer_facebook').val()); 

            if($('#footer_instagram').val() == '' || $('#footer_instagram').val() == '#')
                $('.social-list .instagram').hide(); 
            else
                $('.social-list .instagram').attr('href',$('#footer_instagram').val()); 
            if($('#footer_ticktok').val() == '' || $('#footer_ticktok').val() == '#')
                $('.social-list .ticktok').hide(); 
            else
                $('.social-list .ticktok').attr('href',$('#footer_ticktok').val()); 

            // $('.section-join-us .text-center').css('visibility','hidden');
        }
        $('.top-logo img').attr('src',$('#url_logo').val());
        $('.footer-logo img').attr('src',$('#url_logo').val());
        updateAddress($('.top-merchant-details input[type=radio]:checked').val());
        updateAddressMobile($('.mobile-service input[type=radio]:checked').val());
        $('#vue-widget-nav').detach().appendTo($('.widget-search-bar'));
        if(isMobile){
            $('#vue-widget-nav-mobile').detach().appendTo($('.widget-search-bar-mobile'));
            $('#vue-widget-nav-mobile .widget-search').detach().appendTo($('#vue-cart-preview'));
        }            
        $('.suggestion-tab li:nth-child(2)').trigger('click');
    }, 500);
    $(document).on('click','.btn-group .tip-group-btn',function(e){
        // $(this).addClass('active');
        console.log($(this));
    });  
    $(document).on('click','.phone-selection',function(e){
        $('#changephoneModal').modal('show');
    }); 
    $(document).on('click','.country-selection',function(e){
        $('.phone-code').text($(this).attr('data-code'));
        $('#selected-country-img').attr('src',$(this).attr('data-image'));
    }); 
    $(document).on('submit','.phone-form',function(e){
        e.preventDefault(e);
        var phone = $('.phone-code').text()+$('#phone_number').val();
        $('#phone-code-input').val(phone);
        $('#mobile_prefix-input').val($('.phone-code').text());
        $('.basic-info_submt').attr('disabled','disabled');
        firebase.auth().signInWithPhoneNumber(phone, window.recaptchaVerifier) 
          .then(function(confirmationResult) {
                window.confirmationResult = confirmationResult; 
            $('#changephoneModal').modal('hide');
            $('#verifyCodeModal').modal('show');
            $('.basic-info_submt').removeAttr('disabled');
            $('.phone-error-container').hide();
        }).catch(function (error) {
            $('.phone-error').text(error);
            $('.phone-error-container').show();
            $('.basic-info_submt').removeAttr('disabled');            
        });
    });
    $(document).on('click','#verify-btn',function(e){
        // e.preventDefault(e);
        var pin = $('#pin-code').val();
        // alert(pin);

        // -------------- for dev purpose -------------

        // if(!window.verified){
        //     window.confirmationResult.confirm(pin) 
        //         .then(function(result) { 
                    $('.pin-success').show();
                    $('.pin-error').hide();
                    var form = $('.phone-form');
                    $.ajax({
                        url: form.attr('action'),
                        type:'POST',
                        data: form.serialize() + "&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
                        success: function(data) {                            
                            location.reload();
                        },
                        error: function(response) {
                        }
                    });
            //     }, function(error) { 
            //         $('.pin-error').show();
            // });
        // }

        //  ----------- end ---------------
        
    });    
    $(document).on('change','.top-merchant-details input[type=radio]', function() {
        updateAddress($(this).val());
    });  
    $(document).on('change','#orderTypeTime input[type=radio]', function() {
        updateAddress($(this).val());
        updateAddressMobile($(this).val());
    }); 
    $(document).on('change','.mobile-service input[type=radio]', function() {
        updateAddressMobile($(this).val());
    });
    $(document).on('click','.search-geocomplete-container .xsearch-input',function(e){
        $('#changeCAddressModal').modal('show');
    });
    $(document).on('keyup','.search-geocomplete-container .search-input',function(e){
        var elementInput = $(this);
        var searchElement = $('.modal-body .search-geocomplete-container');
        searchElement.find('.pin_placeholder').hide();
        searchElement.find('.loading-icon').show();
        searchElement.find('.search-geocomplete-results').html('');
        $.ajax({
            url: '/api/getlocation_autocomplete',
            type:'POST',
            data:  'q='+ elementInput.val() + "&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
            success: function(response) {
                if(response.code == 1){
                    var html = '<ul class="list-unstyled m-0 border custom-search-result-list">';
                    $.each(response.details.data, function( index, value ) {
                        html += '<li data-id="'+value.id+'"><a href="javascript:;"><h6 class="m-0">'+value.addressLine1+'</h6><p class="m-0 text-grey">'+value.addressLine2+'</p></a></li>';
                    });
                    html += '</ul>';
                    searchElement.find('.search-geocomplete-results').append(html);
                }
                searchElement.find('.search-geocomplete-results').show();
                searchElement.find('.pin_placeholder').show();
                searchElement.find('.loading-icon').hide();
            },
            error: function(response) {
                searchElement.find('.pin_placeholder').show();
                searchElement.find('.loading-icon').hide();
            }
        });
    });
    $(document).on('click','.search-geocomplete-container .custom-search-result-list a',function(e){
        var searchElement = $('.search-geocomplete-container');
        searchElement.find('.pin_placeholder').hide();
        searchElement.find('.loading-icon').show();
        var locationId = $(this).parent().attr('data-id');
        $.ajax({
            url: '/api/getLocationDetails',
            type: 'POST',
            data:  'id='+ locationId + "&autosaved_addres=true&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
            success: function(response) {
                if(response.code == 1){
                    $('.search-geocomplete-container .search-input').val(response.details.data.address.formatted_address);
                    $('.address-form #street_number').val(response.details.data.address.street_number);
                    $('.address-form #city').val(response.details.data.address.city);
                    $('.address-form #postal_code').val(response.details.data.address.postal_code);
                    $('.address-form #address').val(response.details.data.address.formatted_address);
                }
                searchElement.find('.search-geocomplete-results').hide();
                searchElement.find('.pin_placeholder').show();
                searchElement.find('.loading-icon').hide();
            },
            error: function(response) {
                searchElement.find('.pin_placeholder').show();
                searchElement.find('.loading-icon').hide();
            }
        });
    });
    $(document).on('submit','.address-form',function(e){
        e.preventDefault(e);
        $.ajax({
            url: $(this).attr('action'),
            type:'POST',
            data: $(this).serialize(),
            success: function(response) {     
                if(response.code == 1){
                    $('.xsearch-input').val(response.details.data.address.parse_address);
                    $('#changeCAddressModal').modal('hide');
                }
            },
            error: function(response) {               
            }
        });
    });
    $(document).on('click','.modal-change-delivery',function(e){
        updateDeliveryTimes($('#orderTypeTime input[type=radio]:checked').val());
        $('#orderTypeTime').modal('show');
    });
    $(document).on('change','.custom_delivery_days',function(e){
        var html = '';
        $.each(timeRanges[$(this).val()], function( index, value ) {
            html += '<option value="'+index+'">'+value.pretty_time+'</option>';
        });
        $('#custom_delivery_time').html('');
        $('#custom_delivery_time').append(html);
        saveScheduleOrder();
    });
    $(document).on('change','.custom_delivery_time',function(e){
        saveScheduleOrder();
    });
    $(document).on('click','.add-address',function(e){
        $('#changeCAddressModal').modal('show');
    });
    if($('#mobile_prefix-input').length > 0 && $('#mobile_prefix-input').val() != ''){
        var temp = $('#mobile_prefix-input').val().replace("+", "");
        $('.'+temp+'-country').trigger('click');
    }
    if(window.order_ajax){
        setInterval(function () {
            $.ajax({
                url: '/api/getOrder',
                type:'POST',
                data:  'order_uuid='+ window.order_uuid + "&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
                success: function(data) {
                    if(data.code == 1){
                        var text = data.details.progress.order_status_details;
                        if(data.details.progress.order_progress != 4 && data.details.order_info.delayed_order_mins != ''){
                            text += ' '+data.details.order_info.delayed_order;
                        }
                        $('.estimate_address').text(text);
                        $('.top_order_status').text(data.details.progress.order_status);
                        $('.top_order_status2').text(data.details.order_info.status);
                    }
                    console.log(data);
                },
                error: function(response) {
                }
            });
        }, 5000);
    }
});
function getFirstKey(array) {
  for (var prop in array)
    if (array.propertyIsEnumerable(prop))
      return prop;
}
function saveScheduleOrder(){
    var delivery_date = $('.search-geocomplete-container .custom_delivery_days').val();
    var delivery_time = $('.search-geocomplete-container .custom_delivery_time').val();
    delivery_time = JSON.stringify(timeRanges[delivery_date][delivery_time]);
    $.ajax({
        url: '/api/saveCTransactionInfo',
        type:'POST',
        data:  "delivery_date="+delivery_date+"&delivery_time="+delivery_time+"&merchant_id="+merchant_id+"&whento_deliver=schedule&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
        success: function(response) {
            if(response.code == 1){
            }
        },
        error: function(response) {
        }
    });
}
function updateDeliveryTimes(type){
    $.ajax({
        url: '/api/getCDeliveryTimes',
        type:'POST',
        data:  "merchant_id="+merchant_id+"&type="+type+"&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
        success: function(response) {
            if(response.code == 1){
                var html = '';
                $.each(response.details.opening_hours.dates, function( index, value ) {
                    html += '<option value="'+value.value+'">'+value.name+'</option>';
                });
                $('#custom_delivery_days').html('');
                $('#custom_delivery_days').append(html);
                var html = '';
                timeRanges = response.details.opening_hours.time_ranges;
                $.each(response.details.opening_hours.time_ranges[getFirstKey(response.details.opening_hours.time_ranges)], function( index, value ) {
                    // console.log(value);
                    html += '<option value="'+index+'">'+value.pretty_time+'</option>';
                });
                if($('#delivery_time').length > 0){
                    $('#delivery_time').html('');
                    $('#delivery_time').append(html);
                }else{
                    $('#custom_delivery_time').html('');
                    $('#custom_delivery_time').append(html);
                }
            }
        },
        error: function(response) {
        }
    });
}
function updateAddress(value){
    updateDeliveryTimes(value);
    switch (value) {
    case 'pickup':
        $('.search-geocomplete-content').hide();
        $('#vue-address-needed').hide();
        $('#vue-widget-nav').css('visibility','hidden');
      break;
    case 'delivery':
        $('#vue-widget-nav').css('visibility','visible');
        $('#vue-address-needed').show();
        $('.search-geocomplete-content').show();
      break;
  }
}
function updateAddressMobile(value){
    switch (value) {
    case 'pickup':
        $('#vue-address-needed').hide();
        $('#vue-widget-nav-mobile').css('visibility','hidden');
      break;
    case 'delivery':
        $('#vue-widget-nav-mobile').css('visibility','visible');
        $('#vue-address-needed').show();
      break;
  }
}