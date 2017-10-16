<?php
   require_once("../includes/constants.php");
   error_reporting(E_ALL); ini_set('display_errors', 1);
   require_once("../".HOS_CLIENT_DBCONNECT."/topinc.php");
   require_once("../includes/functions.php");

   
if(isset($_REQUEST)){
	$id=$_REQUEST['data'];
	$usertype = $_REQUEST['usertype'];
	if($usertype == 'hospital'){
		
		$uploadLocationLogoThumb = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo/thumb' . DIRECTORY_SEPARATOR ;
		$uploadLocationLogoLarge = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo/large' . DIRECTORY_SEPARATOR ;
		$hospitaldatasql = "select hospital_logo from hospital_master where hospital_id = :hospitalid  ";
		$hospitaldata =  $db->row($hospitaldatasql, array('hospitalid'=> $id));
		
		if(empty($hospitaldata)){
			$msg='Hospital records not found';
			$sts= 0;
			$result = array('sts'=>$sts,'msg' =>$msg);
			echo json_encode($result);
			exit;			
		}
		
		$logourlthumb = $uploadLocationLogoThumb.$hospitaldata['hospital_logo'];
		$logourllarge = $uploadLocationLogoLarge.$hospitaldata['hospital_logo'];

		$updatesql = "update hospital_master set hospital_logo = '' where hospital_id = :id";
	}
	elseif($usertype == 'diagnostic'){
		
		$uploadLocationLogoThumb = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/diagnostic-logo/thumb' . DIRECTORY_SEPARATOR ;
		$uploadLocationLogoLarge = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/diagnostic-logo/large' . DIRECTORY_SEPARATOR ;
		$diagnosticdatasql = "select diagnostic_logo from diagnostic_master where diagnostic_id = :diagnosticid ";
		$diagnosticdata =  $db->row($diagnosticdatasql, array('diagnosticid'=> $id));
		
		if(empty($diagnosticdata)){
			$msg='Diagnostic centre records not found';
			$sts= 0;
			$result = array('sts'=>$sts,'msg' =>$msg);
			echo json_encode($result);
			exit;
			
		}
		
		$logourlthumb = $uploadLocationLogoThumb.$diagnosticdata['diagnostic_logo'];
		$logourllarge = $uploadLocationLogoLarge.$diagnosticdata['diagnostic_logo'];
		
		$updatesql = "update diagnostic_master set diagnostic_logo = '' where diagnostic_id = :id";
	}
			
	
	try {
		$result = $db->query($updatesql, array( 'id' => $id ));
		$msg             = "Logo removed";
		$sts             = 1;
		if(isset($logourlthumb) && is_file($logourlthumb) && file_exists($logourlthumb)){
			unlink($logourlthumb);
		}
		if(isset($logourllarge) && is_file($logourllarge) && file_exists($logourllarge)){
			unlink($logourllarge);
		}		
	} 
	catch (Exception $E) {
		$msg = 'Logo removal failed';
		$sts = 0;
		$rtn = array('sts' => $sts,'msg' => $msg);
		echo json_encode($rtn);
		exit;
	}
	$rtn = array(
		'sts' => $sts,
		'msg' => $msg
	);
    echo json_encode($rtn);
	
}

?>
