<?PHP
ob_start();
session_start();
date_default_timezone_set('Asia/Kolkata');
define("HOS_WEBROOT_URL","http://localhost/hospitalsurvey/");
define("HOS_WEBADMINROOT_URL",HOS_WEBROOT_URL."webadmin/");
define("HOS_WEBSERVICE_URL",HOS_WEBROOT_URL."webservice/");
define("HOS_WEBSITE_NAME","HOSPITAL SURVEY");
define("HOS_DATE_INPUT_FORMAT","d/m/Y");
define("HOS_DATEPICKER_FORMAT","Y-m-d");
define("HOS_DATE_DISPLAY_FORMAT","jS M Y");
define("HOS_TIME_DISPLAY_FORMAT","h:i:s a");
define("HOS_DATETIME_DISPLAY_FORMAT","jS M Y h:i:s a");
define("HOS_CURRENCY_SYMBOL","&#8377;");
define("HOS_CURRENCY_WORD","INR");
define("HOS_LOGINOTP_SECURITY","false");
define("HOS_EMAIL_VALIDATE_ONLINE","true");
define("HOS_EMAIL_VALIDATE_ONLINE_TIMEOUT","15");
define("HOS_EMAIL_WEB_URL","http://www.digdevinfra.com/webmail");


define("HOS_ADMIN_DBCONNECT","dbconnecta");
define("HOS_CLIENT_DBCONNECT","dbconnectc");

define("HOS_UPLOAD_FOLDER","files");
define("HOS_PASSWORD_ENCWORD","513ipy07");
define("HOS_URL_ENCWORD","zahip78");
define("HOS_ADMIN_SESSION_TIMEOUT","1000");// seconds
define("HOS_PROFILEID_RESERVED","50000");

define("HOS_GLOBAL_SHOP","Representative");
define("HOS_SHOPNAME_PARLIAMENT","MP");
define("HOS_SHOPNAME_ASSEMBLY","MLA");
define("HOS_SHOPNAME_WARD","Councilor");
define("HOS_ADDITIONAL_ROLES",serialize(array('Prime Minister','Central Minister','Chief Minister','State Minister','Mayor','Chairman','Chair-person','President')));
define("HOS_TITLES",serialize(array('Mr.','Miss.','Mrs.','Ms.','Dr.','Prof.','Adv.')));

define("HOS_RESET_PASS_TIMEOUT","15"); // minute
define("HOS_LOGIN_OTP_TIMEOUT","15"); // minute
define("HOS_FORGOTPASS_OTP_TIMEOUT","15");//minutes
define("HOS_REGISTER_AGELIMIT","18"); // years
define("HOS_REG_NOVERIFY_REMOVE","12"); // hours
define("HOS_MAX_FILE_SIZE_LIMIT","3145728"); // in bytes

define("HOS_DISPLAY_IP_INTERVAL","60"); // seconds

/*
define("HOS_SMS_USER","internationalsms");
define("HOS_SMS_PASSWORD","HZlGhtj");
define("HOS_SMS_SENDER","KAPNFO");
define("HOS_SMS_URL","http://193.105.74.159/api/v3/sendsms/plain");

*/
define("HOS_SMS_USER","indiapollu");
define("HOS_SMS_PASSWORD","59853014");
define("HOS_SMS_SENDER","SMSCountry");
define("HOS_SMS_URL","http://www.smscountry.com/SMSCwebservice_Bulk.aspx");

/*
define("HOS_SMS_USER","demotr");
define("HOS_SMS_PASSWORD","tr123");
define("HOS_SMS_SENDER","INDSMS");
define("HOS_SMS_URL","https://app.indiasms.com/sendsms/bulksms");

*/

//--------------------chat configuration-------------------//
define("HOS_CHAT_INIT_COUNT","20");
define("HOS_CHAT_REFRESH_RATE","500");
define("HOS_CHAT_ADMIN_STYLE","#f2dede");
define("HOS_CHAT_LOGGED_USER_STYLE","#e0ffc6");
define("HOS_CHAT_RECEPIENT_STYLE","#d9edf7");
define("HOS_CHAT_MAX_FILE_LIMIT","2");
define("HOS_CHAT_MAX_FILE_ALERT_TEXT","2 MB");
?>


