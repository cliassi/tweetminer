
function cookie(name, value){
	if(typeof(value)=='undefined'){
		return $.cookie(name);
	} else{
		return $.cookie(name,value);
	}
}
function setcookie(name,value){
	return $.cookie(name,value);
}