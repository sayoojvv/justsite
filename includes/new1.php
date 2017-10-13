<?PHP
date_default_timezone_set('Asia/Kolkata');
error_reporting(E_ALL);
ini_set('display_errors', 1); 
$mnumbers="919847680023";
//$mnumbers="919847680023";
include_once("functions1.php");
$offset=array("5.5");
//$offset=array("5.5");
$message="KAP-".date('d/m/Y H:i:s');
echo sendMobileOTP($mnumbers,$message,true,$offset);

?>
