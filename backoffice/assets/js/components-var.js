var ajax_request_cmp = {};
var $timeout_cmp = 20000;
var $content_type = {
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

var empty = function(data){	
	if (typeof data === "undefined" || data==null || data=="" || data=="null" || data=="undefined" ) {	
		return true;
	}
	return false;
};