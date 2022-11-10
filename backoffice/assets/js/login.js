var dump = function(data)
{
	console.debug(data);
}

var dump2 = function(data) {
	alert(JSON.stringify(data));	
};


jQuery(document).ready(function() {
	
	$( document ).on( "click", ".change_field_href", function() {		 
		 $(this).find("i").toggleClass( "zmdi zmdi-eye zmdi zmdi-eye-off" );
		 var $fields = $(this).parent().find("input");
		 var $type = $fields.attr("type");		 
		 if($type=="password"){		 	
		 	$fields.attr('type','text');
		 } else {		 	
		 	$fields.attr("type","password");
		 }
	});
	
});
/*end doc*/