//<script type="text/javascript">
//-----------------------client side javascript validation function--------------------------//
// ------------------  check if input value is empty or not  -----------------------//
function isEmpty(input) {
	return !input.replace(/^\s+/g, '').length;
}
//-----------------------------------------validate full name minimum 5 chracters and maximum 250 only alphabets and space allowed ///
function validate_name(input_name) {
	var re = /(?=.*[a-zA-Z ])^[a-zA-Z ]{5,100}$/;
	return re.test(input_name);
}
//--------------------------validate email-----------------------------------------------//
function validate_email(input_email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(input_email);
}
//------------ validate short code only letters and digits- minimum 1 character and maximum 4 ------------------//
function validate_shortcode(input_shortcode) {
	var re = /(?=.*[a-zA-Z0-9])^[a-zA-Z0-9]{1,4}$/;
	return re.test(input_shortcode);
}
//-----------validate username only letters and digits- minimum 6 character and maximum 12 ---------------//
function validate_username(input_username) {
	var re = /(?=.*[a-zA-Z0-9])^[a-zA-Z0-9]{6,12}$/;
	return re.test(input_username);
}
//--------------validate password ---------------------atleast 1 upper case, 1 lower case, 1 number or special character, atleast 8 charactrers in length --//
function validate_adminpassword(input_password) {
	var re = /^(?=.*[a-z])(?=.*[A-Z])((?=.*\d)|(?=.*[$@$!%*?&]))[a-zA-Z\d$@$!%*?&]{8,}$/;
	return re.test(input_password);
}

function validate_userpassword(input_password) {
	var re = /^(?=.*\d)(?=.*[a-zA-Z])[a-zA-Z\d$@$!%*?&]{4,}$/;
	return re.test(input_password);
}
//-------------- validate mobile-------------------- only 10 digits allowed-------------------------//
function validate_mobile(input_mobile) {
	var re = /^[2-9]{1}[0-9]{3,12}$/;
	return re.test(input_mobile);
}
//-------------validate date in dd/mm/yyyy format----------------------------------//
function validate_date(input_date) {
	var dateFormat = /^\d{1,4}[\.|\/|-]\d{1,2}[\.|\/|-]\d{1,4}$/;
	if (dateFormat.test(input_date)) {
		s = input_date.replace(/0*(\d*)/gi, "$1");
		var dateArray = input_date.split(/[\.|\/|-]/);
		dateArray[1] = dateArray[1] - 1;
		if (dateArray[2].length < 4) {
			dateArray[2] = (parseInt(dateArray[2]) < 50) ? 2000 + parseInt(dateArray[2]) : 1900 + parseInt(dateArray[2]);
		}
		var testDate = new Date(dateArray[2], dateArray[1], dateArray[0]);
		if (testDate.getDate() != dateArray[0] || testDate.getMonth() != dateArray[1] || testDate.getFullYear() != dateArray[2]) {
			return false;
		}
		else {
			return true;
		}
	}
	else {
		return false;
	}
}
//--------------------validate currency entry-------------------------------
function validate_currency(input_currency) {
	var re = /^[0-9]+(?:\.[0-9]+)?$/;
	return re.test(input_currency);
}
//-------------------------validate numeric entry----------------------------------//
function validate_number(input_number) {
	var re = /^[0-9]+$/;
	return re.test(input_number);
}
//----------------------- validate website url format-----------------------------------------//
function validate_websiteUrl(input_url) {
	var res = input_url.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
	if (res == null) return false;
	else return true;
}

function validate_Url(input_url) {
	//^(http(s?):\/\/)+(www\.)?[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,3})+(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/
	var res = input_url.match(/^(http(s?):\/\/)+(www\.)?[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,3})+(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/g);
	if (res == null) return false;
	else return true;
}

function validatelongtext(longtext) {
	var res = longtext.match(/^[^<>]+$/);
	if (res == null) return false;
	else return true;
}
//</script>
function placeholderIsSupported() {
	var test = document.createElement('input');
	return ('placeholder' in test);
}

function format_textarea(inputtxt) {
	var status=true;
	var phoneno = /\d{10}/mg;  
	if(inputtxt.match(phoneno))  {
		status=false;
	}

	var emailpart = /(\".*\"|[A-Za-z]\w*)@(\[\d{1,3}(\.\d{1,3}){3}]|[A-Za-z]\w*(\.[A-Za-z]\w*)+)/mg;
	if(inputtxt.match(emailpart))  {
		status=false;
	}

	var urlpart = /(\[url=)?(https?:\/\/)?(www\.|\S+?\.)(\S+?\.)?\S+\s*/mg;
	if(inputtxt.match(urlpart))  {
		status=false;
	}
	return status;
 }

Parsley.addValidator('inputname', {
  validateString: function(input_name , msg) {  
	var re = /(?=.*[a-zA-Z ])^[a-zA-Z ]{5,100}$/;
	if(!re.test(input_name)) return $.Deferred().reject(msg);
  }
});
Parsley.addValidator('nickname', {
  validateString: function(input_name , msg) {  
	var re = /(?=.*[a-zA-Z0-9])^[a-zA-Z0-9]{5,20}$/;
	if(!re.test(input_name)) return $.Deferred().reject(msg);
  }
});
Parsley.addValidator('mobilenumber', {
  validateString: function(input_name , msg) {  
	var re = /^[2-9]{1}[0-9]{9}$/;
	if(!re.test(input_name)) return $.Deferred().reject(msg);
  }
});
Parsley.addValidator('emailaddress', {
  validateString: function(input_name , msg) {  
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if(!re.test(input_name)) return $.Deferred().reject(msg);
  }
});
Parsley.addValidator('adminpassword', {
  validateString: function(input_name , msg) {  
	var re = /^(?=.*[a-z])(?=.*[A-Z])((?=.*\d)|(?=.*[$@$!%*?&]))[a-zA-Z\d$@$!%*?&]{8,20}$/;
	if(!re.test(input_name)) return $.Deferred().reject(msg);
  }
});
Parsley.addValidator('userpassword', {
  validateString: function(input_name , msg) {  
	var re = /^(?=.*\d)(?=.*[a-zA-Z])[a-zA-Z\d$@$!%*?&]{4,20}$/;
	if(!re.test(input_name)) return $.Deferred().reject(msg);
  }
});

Parsley.addValidator('confirmequal', {
  validateString: function(input_name , cfield) {  
	  console.log('field = '+cfield);
	  var fields = cfield.split('||');
	  
	var orgvalue = $("#"+fields[0]).val();
	if(input_name != orgvalue ) return $.Deferred().reject(fields[1]);
  }
});