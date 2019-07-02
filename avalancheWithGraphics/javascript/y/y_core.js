// The remaining functions are utility functions added by Adam Wulf
// on June of 2004
function substr_replace(s, t, u) {
  /*
  **  Replace a token in a string
  **    s  string to be processed
  **    t  token to be found and removed
  **    u  token to be inserted
  **  returns new String
  */
  i = s.indexOf(t);
  r = "";
  if (i == -1) return s;
  r += s.substring(0,i) + u;
  if ( i + t.length < s.length)
    r += substr_replace(s.substring(i + t.length, s.length), t, u);
  return r;
}

// returns true if the keycode is nonVisible
function xNonVisibleChar(key){
	// if the key equals backspace, delete, tab, or enter, or capslock respectively
	if(xNum(key) && (key == 8 || key == 46 || key == 9 || key == 13 || key == 20
	 || key == 37 || key == 38 || key == 39 || key == 40)){
		return true;
	}else{
		return false;
	}
}
// returns a two character representation of a weekday, given its index
function xGetDOW(d){
	var day_array = new Array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
	if(xNum(d)){
		return day_array[d];
	}
}

// checks to validate an email address.
function xEmailCheck(emailStr) {
	var emailPat=/^(.+)@(.+)$/;
	var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]";
	var validChars="\[^\\s" + specialChars + "\]";
	var quotedUser="(\"[^\"]*\")";
	var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	var atom=validChars + '+';
	var word="(" + atom + "|" + quotedUser + ")";
	var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
	var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
	
	var matchArray=emailStr.match(emailPat);
	if (matchArray==null) {
		return false;
	}
	var user=matchArray[1];
	var domain=matchArray[2];
	
	if (user.match(userPat)==null) {
	    return false;
	}
	
	var IPArray=domain.match(ipDomainPat);
	if (IPArray!=null) {
		  for (var i=1;i<=4;i++) {
		    if (IPArray[i]>255) {
			return false;
		    }
	    }
	    return true;
	}
	
	var domainArray=domain.match(domainPat);
	if (domainArray==null) {
	    return false;
	}
	
	var atomPat=new RegExp(atom,"g");
	var domArr=domain.match(atomPat);
	var len=domArr.length;
	if (domArr[domArr.length-1].length<2 || 
	    domArr[domArr.length-1].length>3) {
	   return false;
	}
	if (len<2) {
	   return false;
	}
	return true;
}

// end x_core.js
