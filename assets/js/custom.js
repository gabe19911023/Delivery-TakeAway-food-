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
        if(!window.verified){
            window.confirmationResult.confirm(pin) 
                .then(function(result) { 
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
                }, function(error) { 
                    $('.pin-error').show();
            });
        }
    });
	
	
	
    $(document).on('change','.top-merchant-details input[type=radio]', function() {
        updateAddress($(this).val());
    });  
    $(document).on('change','#orderTypeTime input[type=radio]', function() {
		localStorage.setItem('dlvrtim', '');
        updateAddress($(this).val());
        updateAddressMobile($(this).val());
		cws_updateService($(this).val());
    }); 
	
	function cws_updateService(valu){
		var crtuid = localStorage.getItem('cws_cart_uuid');
        console.log('delivery');
		$.ajax({
            url: '/api/updateService',
            type:'POST',
            data:  "transaction_type="+valu+"&cart_uuid="+crtuid+"&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
            success: function(response) {
			}
		});
	}
	
    $(document).on('change','.mobile-service input[type=radio]', function() {
        updateAddressMobile($(this).val());
		updateAddress($(this).val());
    });
    $(document).on('click','.search-geocomplete .xsearch-input',function(e){
        $('#changeCAddressModal').modal('show');
    });
	
	$(document).on('click','.nnewadresform',function(e){
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
					searchElement.find('.search-geocomplete-results').append(html);
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
		
		$('.address-form #street_number').val('');
		$('.address-form #address').val('');
		$('.address-form #postal_code').val('');
		$('.address-form #city').val('');
		
        var locationId = $(this).parent().attr('data-id');
        $.ajax({
            url: '/api/getLocationDetails',
            type: 'POST',
            data:  'id='+ locationId + "&autosaved_addres=true&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
            success: function(response) {
                if(response.code == 1){
                    $('.search-geocomplete-container .search-input').val(response.details.data.address.formatted_address);
					
					if(response.details.data.address.street_number && response.details.data.address.street_number != ''){
						
						var strrt = response.details.data.address.street_number;
						$('.address-form #street_number').val(strrt);
						var chadd = response.details.data.address.address1
						
						var frmadd = chadd.replace(strrt, "");
						frmadd = frmadd.trim();
						
						$('.address-form #address').val(frmadd);
						
					}else{
						var street = response.details.data.address.formatted_address;
						var checkarr = ['0','1','2','3','4','5','6','7','8','9'];
						if(checkarr.includes(street[0])){
							var gtstreet = street.split(' ');
							$('.address-form #street_number').val(gtstreet[0]);
							var add1 = response.details.data.address.address1;
							var gtadd1 = add1.split(' ');
							gtadd1.shift();
							$('.address-form #address').val(gtadd1.join());
						}else{
						}
					}
					if(response.details.data.address.city && response.details.data.address.city != ''){
						$('.address-form #city').val(response.details.data.address.city);
					}else{
						var addres2 = response.details.data.address.address2;
						if (addres2.indexOf(',') > -1){
							var strCopy = addres2.split(',');
							var ccty = strCopy[1].replace('Greater ', "");
							$('.address-form #city').val(ccty);
						}else{
							var ccty = addres2.replace('Greater ', "");
							$('.address-form #city').val(ccty);
						}
					}
					$('.address-form #postal_code').val(response.details.data.address.postal_code);	
					
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
        // console.log('===========>', 'modal-change-delivery');
		updateDeliveryTimes($('#orderTypeTime input[type=radio]:checked').val());
        $('#orderTypeTime').modal('show');
    });
	$(document).on('click','.savtimeonchecout',function(e){
		$('#orderTypeTime').modal('hide');
		location.reload();
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
		var dlvrtim = $(this).val();
		if(dlvrtim != ''){
			localStorage.setItem('dlvrtim', dlvrtim);
		}
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
    var delivery_date = $('.custom_delivery_days').val();
    var delivery_time = $('.custom_delivery_time').val();
	if(delivery_time){
    delivery_time = JSON.stringify(timeRanges[delivery_date][delivery_time]);
    $.ajax({
        url: '/api/saveCTransactionInfo',
        type:'POST',
        data:  "delivery_date="+delivery_date+"&delivery_time="+delivery_time+"&merchant_id="+merchant_id+"&whento_deliver=schedule&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
        success: function(response) {
            if(response.code == 1){
				if(response.details.cart_uuid != ''){
					localStorage.setItem('cws_cart_uuid', response.details.cart_uuid);
				}
            }
        },
        error: function(response) {
        }
    });
	}
}
function updateDeliveryTimes(type){
    $.ajax({
        url: '/api/getCDeliveryTimes',
        type:'POST',
        data:  "merchant_id="+merchant_id+"&type="+type+"&YII_CSRF_TOKEN="+$('meta[name="YII_CSRF_TOKEN"]').attr('content'),
        success: function(response) {
            if(response.code == 1){
                var html = '';
                // console.log('===========>', response.details);
                $.each(response.details.opening_hours.dates, function( index, value ) {
                    html += '<option value="'+value.value+'">'+value.name+'</option>';
                });
                $('#custom_delivery_days').html('');
                $('#custom_delivery_days').append(html);
                var html = '';
                timeRanges = response.details.opening_hours.time_ranges;
				
				if(timeRanges == ''){
					$('.section-cart .btn-green').attr('style', 'display: none!important');
					$('.quantity-add-cart').attr('disabled',true);
					html = '<option value="">No times available today</option>';
				}else{
                    $('.section-cart .btn-green').attr('style', 'display: block');
					$.each(response.details.opening_hours.time_ranges[getFirstKey(response.details.opening_hours.time_ranges)], function( index, value ) {
                        
                        // current_hour = parseInt(response.details.current1.split(':')[0]);
                        // current_min = parseInt(response.details.current1.split(':')[1].split(' ')[0]);
                        
                        // pos_hour = parseInt(value.start_time.split(':')[0]);
                        // pos_min = parseInt(value.start_time.split(':')[1]);
                        // if(current_hour > pos_hour)return;
                        // if(current_hour == pos_hour && current_min > pos_min)continue;
                        // console.log('============>', response.details);
                        if(index == 0){
							html += '<option value="'+index+'">'+value.pretty_time+' (ASAP)</option>';
                        }else{
							html += '<option value="'+index+'">'+value.pretty_time+'</option>';
						}

					});
				}
                if($('#delivery_time').length > 0){
                    $('#delivery_time').html('');
                    $('#delivery_time').append(html);
                }else{
                    $('#custom_delivery_time').html('');
                    $('#custom_delivery_time').append(html); 
                }
                // console.log('=================> delivery_time', $('#delivery_time').length);
				if(timeRanges != ''){
					var dlvrtim = localStorage.getItem('dlvrtim');
					if(typeof(dlvrtim) != 'undefined' && dlvrtim != null){
						if($("#custom_delivery_time option[value='"+dlvrtim+"']").length){
							$('#custom_delivery_time').val(dlvrtim);
						}
					}
				}
				$("#custom_delivery_time").change();
            }
        },
        error: function(response) {
        }
    });
}
function updateAddress(value){
    updateDeliveryTimes(value);
    setTimeout(700);
    saveScheduleOrder();
    
    console.log('========>', value); // delivery, pick up

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





$( window ).on("load", function() {
	setTimeout(
	function() 
	{
		$("#custom_delivery_time").change();
	}, 2500);
	
});

$(document).on('click','#vue-widget-nav-mobile .dropdown button',function(e){
	e.preventDefault();
});

$(document).on('click','.address-form .basic-info_submt',function(){
	$('.custom_delivery_time').change();
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		var adrs = $('.address-form .search-input').val();
		var stret_numbr = $('.address-form #street_number').val();
		var address = $('.address-form #address').val();
		var postl_cod = $('.address-form #postal_code').val();
		var cite = $('.address-form #city').val();
		localStorage.setItem('srchedadress', adrs);
		localStorage.setItem('srchedstret_numbr', stret_numbr);
		localStorage.setItem('srchedaddress', address);
		localStorage.setItem('srchedpostl_cod', postl_cod);
		localStorage.setItem('srchedcite', cite);
		
		location.reload();
	});
});

$(document).on('click','.quantity-add-cart, .add_to_cart',function(){
	setTimeout(
	function() 
	{
		$('.custom_delivery_time').change();
	}, 2000);
});

$(window).on("load", function() {
	var srchedadress = localStorage.getItem('srchedadress');
	var srchedstret_numbr = localStorage.getItem('srchedstret_numbr');
	var srchedaddress = localStorage.getItem('srchedaddress');
	var srchedpostl_cod = localStorage.getItem('srchedpostl_cod');
	var srchedcite = localStorage.getItem('srchedcite');
	//console.log('srchedadress: '+srchedadress);
	if(typeof(srchedadress) != 'undefined' && srchedadress != null){
		setTimeout(function(){
			$('.search-geocomplete-content .form-control-text').val(srchedadress);
			$('.address-form #street_number').val(srchedstret_numbr);
			$('.address-form #address').val(srchedaddress);
			$('.address-form #postal_code').val(srchedpostl_cod);
			$('.address-form #city').val(srchedcite);
			
			if($('#vue-change-address .container-fluid').length){
				var msgg = "<span class='outofrang'>Sorry, we don't deliver in your area: please change your address or come to pick up your food!</span>";
				$('#changeCAddressModal').modal('show');
				$('.address-form .modal-body').append(msgg);
			}
			if($('#vue-transaction .alert-warning').length){
				var msgg = "<span class='outofrang'>Sorry, we don't deliver in your area: please change your address or come to pick up your food!</span>";
				$('#changeCAddressModal').modal('show');
				$('.address-form .modal-body').append(msgg);
			}
		}, 1000);
	}
});

$(document).on('click', '.adrs_form_close', function(){
	$('#changeCAddressModal').modal('hide');
});

$(document).on('click', '#changephoneModal .close_btn', function(){
	$('#changephoneModal').modal('hide');
});

$(window).on("load", function() {
	$(".merchant-details input[type=radio]").change();
	change_time_dilevy();
});

function change_time_dilevy(){
	var urll = window.location.href;
	if(urll.includes("checkout")){
		if($('.dilevytim_checkout').length){
			var datetext = $('.dilevytim_checkout').text();
			datetext = datetext.replace("-", "");
			const myArray = datetext.split(",");
			var dayy = myArray[0];
			var dtmt = myArray[1].split(" ");
			var mnth = dtmt[1];
			var datt = dtmt[2];
			var otim = myArray[2].split(" ");
			otim.shift();
			otim.shift();
			var optm = otim.join(' ');
			if(datt == '1'){
				var prfx = 'st';
			}else if(datt == '2'){
				var prfx = 'nd';
			}else if(datt == '3'){
				var prfx = 'rd';
			}else{
				var prfx = 'th';
			}
			var dlvrtim = localStorage.getItem('dlvrtim');
			
			if(dlvrtim == 0){
				var nwlin = datt+prfx+' '+mnth+' ('+dayy+') - '+optm+' (ASAP)';
			}else{
				var nwlin = datt+prfx+' '+mnth+' ('+dayy+') - '+optm;
			}
			
			
			$('.dilevytim_checkout').text(nwlin);
			
		}else{
			setTimeout(function() 
			{
				change_time_dilevy();
			}, 500);
			
		}
	}
}

$(document).ready(function(e){
	var urll = window.location.href;
	if(urll.indexOf('account') != -1 && urll.indexOf('checkout') != -1){
		var chkoutpg = localStorage.getItem('chkoutpg');
		if(typeof(chkoutpg) != 'undefined' && chkoutpg != ''){
			
		}else{
			localStorage.setItem('chkoutpg', 'yes');
			location.reload();
		}
	}
});
