<?PHP
if(isset($_SESSION['HOS_SIGN_CLIENTUSERID']) && is_numeric($_SESSION['HOS_SIGN_CLIENTUSERID']) && isset($_SESSION['HOS_SIGN_CLIENTUSERNAME']) && trim($_SESSION['HOS_SIGN_CLIENTUSERNAME']) != "" && isset($_SESSION['HOS_SIGN_CLIENTUSERTYPE']) && in_array($_SESSION['HOS_SIGN_CLIENTUSERTYPE'],array('member','medical','doctor')) ) 
{
	$usersql = "select count(1) as usercount from register_master where register_id = :regid and register_active= 'yes' and register_emailverified ='yes'";
	$uarr = array('regid'=>$_SESSION['HOS_SIGN_CLIENTUSERID']);
	$userrow = $db->row($usersql,$uarr);
	//print_r($userrow);die();
	if($userrow['usercount'] == 1){
	}
	else {
		$passdata = "sts=0&type=SessionTimeout";
		$url = encrypt_decrypt('encrypt',$passdata ,HOS_URL_ENCWORD);
		header("location:logout.php");
		exit();
	}
}
else
{
	
	$passdata = "sts=0&type=SessionTimeout";
	$url = encrypt_decrypt('encrypt',$passdata ,HOS_URL_ENCWORD);
	header("location:login?data=".$url);
	exit();

}
?>



