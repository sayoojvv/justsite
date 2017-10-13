<?PHP
///-----------------------server side php validation function--------------------------//

// ------------------  check if input value is empty or not  -----------------------//

function isEmpty($input)
{
	if(empty($input))
	{
		return true;	
	}
	elseif(strlen(trim($input))==0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//-----------------------------------------validate full name minimum 5 chracters and maximum 250 only alphabets and space allowed ///

function validate_name($input_name)
{
	if (!preg_match("/(?=.*[a-zA-Z ])^[a-zA-Z ]{5,100}$/",$input_name))
	{
      	return false;
    }
	else
	{
		return true;
	}
}

///--------------------------validate email-----------------------------------------------//
function validate_email($input_email)
{ 
	if (!filter_var($input_email, FILTER_VALIDATE_EMAIL))
	{
      	return false;
    }
	else
	{
		return true;
	}
}

//------------ validate short code only letters and digits- minimum 1 character and maximum 4 ------------------//

function validate_shortcode($input_shortcode)  
{
	if (!preg_match("/(?=.*[a-zA-Z0-9])^[a-zA-Z0-9]{1,4}$/",$input_shortcode)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}

//-----------validate username only letters and digits- minimum 6 character and maximum 12 ---------------//

function validate_username($input_username) 
{
	if (!preg_match("/(?=.*[a-zA-Z0-9])^[a-zA-Z0-9]{6,12}$/",$input_username)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}

//--------------validate password ---------------------atleast 1 upper case, 1 lower case, 1 number or special character, atleast 8 charactrers in length --//

function validate_password($input_password)
{
	if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])((?=.*\d)|(?=.*[$@$!%*?&]))[a-zA-Z\d$@$!%*?&]{8,}$/",$input_password)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}

function validate_userpassword($input_password)
{	
	if (!preg_match("/^(?=.*\d)(?=.*[a-zA-Z])[a-zA-Z\d$@$!%*?&]{4,}$/",$input_password)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}

//-------------- validate mobile-------------------- only 10 digits allowed-------------------------//

function validate_mobile($input_mobile)
{
	if (!preg_match("/^[2-9]{1}[0-9]{9}$/",$input_mobile)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}

//-------------validate date in dd/mm/yyyy format----------------------------------//

function validate_date($input_date)
{
	
	
	if (preg_match("/^(\d{1,4})[\.|\/|-](\d{1,2})[\.|\/|-](\d{1,4})/", $input_date, $matches)) 
	{
    	if (!checkdate($matches[2], $matches[1], $matches[3])) 
		{	
			return false;
		}
		else
		{
			return true;		
		}
	} 
	else 
	{ 
		return false;
	}

}

//--------------------validate currency entry-------------------------------

function validate_currency($input_currency)
{
	 if (!preg_match("/^[0-9]+(?:\.[0-9]+)?$/",$input_currency)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}

//-------------------------validate numeric entry----------------------------------//

function validate_number($input_number)
{
	 if (!preg_match("/^[0-9]+$/",$input_number)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}

//----------------------- validate website url format-----------------------------------------//

function validate_websiteUrl($input_url)
{
	 if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$input_url)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}


function validatelongtext($longtext)
{
    if (!preg_match("/^[^<>]+$/",$longtext)) 
	{
      	return false;
    }
	else
	{
		return true;
	}
}


//$string ='http://@123google$%';
//echo "String Empty Status = ".validate_websiteUrl($string);
?>
