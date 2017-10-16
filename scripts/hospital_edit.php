<?php
   require_once("../includes/constants.php");
   error_reporting(E_ALL); ini_set('display_errors', 1);
   require_once("../".HOS_CLIENT_DBCONNECT."/topinc.php");
   require_once("../includes/functions.php");
   
if(isset($_REQUEST)){

	$uploadLocationRootLogo = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo' . DIRECTORY_SEPARATOR ;
	$uploadLocationThumbLogo = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo/thumb' . DIRECTORY_SEPARATOR ;
	$uploadLocationSocialLogo = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo/social' . DIRECTORY_SEPARATOR ;
	//print_r($_REQUEST);
	$hospitalid = $_REQUEST['hospitalid'];
	$medicalid = $_SESSION['HOS_SIGN_CLIENTUSERID'];
	$shortname = $_REQUEST['shortname'];
	$fullname = $_REQUEST['fullname'];
	$description = $_REQUEST['description'];
	$emailaddress = $_REQUEST['emailaddress'];
	$phonenumber = $_REQUEST['phonenumber'];
	$mobile = $_REQUEST['mobile'];
	$website = $_REQUEST['website'];
	$establishedyear = $_REQUEST['establishedyear'];
	$nobed = $_REQUEST['nobed'];
	$nophysicians = $_REQUEST['nophysicians'];		
	
	$address = $_REQUEST['address'];
	$area = $_REQUEST['area'];
	$pincode = $_REQUEST['pincode'];

	$noemployees = $_REQUEST['noemployees'];
	$time = time();

		
	if(trim($shortname) == ''){
		$msg = "Please enter shortname!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($fullname) == ''){
		$msg = "Please enter fullname!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($description) == ''){
		$msg = "Please enter description!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($emailaddress) == ''){
		$msg = "Please enter email address!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($mobile) == ''){
		$msg = "Please enter mobile number!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($phonenumber) == ''){
		$msg = "Please enter phonenumber!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($website) == ''){
		$msg = "Please enter website!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}		
	if(trim($establishedyear) == ''){
		$msg = "Please select establishedyear!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($address) == ''){
		$msg = "Please enter address!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($area) == ''){
		$msg = "Please select area!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($pincode) == ''){
		$msg = "Please select Pincode!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($nobed) == ''){
		$msg = "Please select number of beds!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($nophysicians) == ''){
		$msg = "Please enter number of physicians!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}
	if(trim($noemployees) == ''){
		$msg = "Please select number of employees!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
	}


if(isset($_REQUEST['lat']) && trim($_REQUEST['lat'])!='' && isset($_REQUEST['lng']) && trim($_REQUEST['lng'])!=''){
	$latitude=$_REQUEST['lat'];
	$longitude=$_REQUEST['lng'];
}else{
	$getlatlongsql="select pincode_longitude, `pincode_latitude` FROM `pincode_master` where pincode_id =:pincode";
	$getlatlong=$db->row($getlatlongsql, array('pincode' => $pincode));
	$latitude=$getlatlong['pincode_latitude'];
	$longitude=$getlatlong['pincode_longitude'];
}

	$db->beginTransaction();
	$hospitalsql = "select hospital_shortname ,hospital_logo,hospital_code from hospital_master where hospital_id = :hospitalid";
	$hospitaldata = $db->row($hospitalsql,array('hospitalid'=> $hospitalid));
	
	$oldfilename = $hospitaldata['hospital_logo'];
	$filename = $hospitaldata['hospital_code'];

	$updatehospitalsql = "UPDATE `hospital_master` SET `hospital_fullname` = :fullname , `hospital_shortname` = :shortname, `hospital_streetadress` = :address , `area_id`= :area , `hospital_email`= :emailaddress , `hospital_phone`= :phonenumber , `hospital_mobile`= :mobile, `hospital_website`= :website, `hospital_longitude`= :longitude, `hospital_lattitude`= :latitude, `hospital_desc`= :description ,`hospital_estyear`= :establishedyear, `hospital_nobed`= :nobed, `hospital_nophysicians`= :nophysicians, `hospital_noemployee`= :noemployees ";   
		
	$data = array('fullname'=>  $fullname , 'shortname'=> $shortname , 'address'=> $address , 'area'=>  $area , 'emailaddress'=> $emailaddress , 'phonenumber'=> $phonenumber , 'mobile'=>  $mobile , 'website'=> $website , 'longitude'=> $longitude , 'latitude'=>  $latitude , 'description'=> $description , 'establishedyear'=> $establishedyear, 'nobed'=>  $nobed , 'nophysicians'=> $nophysicians ,'noemployees'=> $noemployees , 'hospitalid'=> $hospitalid );
		$filecount = count($_FILES);
		for($i=0;$i<count($_FILES);$i++){
				$fileid= 'logopicture';
				if(!validatefile($_FILES[$fileid]["tmp_name"],array('image/gif','image/jpeg','image/png'))){
					$sts = 0;
					$msg = "Please upload valid attachment files( JPG/PNG/GIF allowed)";
					$rtn = array(
						'sts' => $sts,
						'msg' => $msg
					);
					echo json_encode($rtn);
					exit;
				}
				$size = $_FILES[$fileid]['size'];
				if($size>(1024*1024)) {
					$msg = "Image size exceeded the maximum size limit (1 mb)";
					$rtn = array(
						'sts' => $sts,
						'msg' => $msg
					);
					echo json_encode($rtn);
					exit;
				}
				$ext = end((explode(".", $_FILES[$fileid]["name"])));
				$orgfilepathLogoRoot=$uploadLocationRootLogo.$filename.'.'.$ext;
				$orgfilepathLogoThumb=$uploadLocationThumbLogo.$filename.'.'.$ext;
				$orgfilepathLogoSocial=$uploadLocationSocialLogo.$filename.'.'.$ext;
				$image_info = getimagesize($_FILES[$fileid]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				$picturewidthlogothumb=105;
				$pictureheightlogothumb=55;
				$picturewidthlogosocial=560;
				$pictureheightlogosocial=292;
				
				$rmfilename = $uploadLocationRootLogo.$oldfilename ;
			    if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
				  unlink($rmfilename);
			    }
				$rmfilename = $uploadLocationThumbLogo.$oldfilename ;
			    if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
				  unlink($rmfilename);
			    }
			    $rmfilename = $uploadLocationSocialLogo.$oldfilename ;
			    if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
				  unlink($rmfilename);
			    }
				
				if(move_uploaded_file($_FILES[$fileid]["tmp_name"], $orgfilepathLogoRoot)) {
				  $sts = 1;
				  resizeThumbnailImage( $orgfilepathLogoThumb, $orgfilepathLogoRoot ,$picturewidthlogothumb,$pictureheightlogothumb);
				  resizeThumbnailImage( $orgfilepathLogoSocial, $orgfilepathLogoRoot ,$picturewidthlogosocial,$pictureheightlogosocial);
				  $saveurl = $filename.'.'.$ext;
				  $updatehospitalsql .= ' , hospital_logo = :saveurl ';
				  $data['saveurl'] = $saveurl ;
				  
				} else {
				  $sts = 0 ;
				  $msg = "File upload Failed";
				  $rtn = array('sts' => $sts,'msg'=> $msg );
				  echo json_encode($rtn);     
				  exit;
				}
			}
		
		
		$updatehospitalsql .= " where hospital_id = :hospitalid ";
		
		
		try {
			   $updatehospital = $db->query($updatehospitalsql, $data );
			   $msg  = "Hospital details updated.";
			   $sts  = 1;		   
		}
		catch (Exception $E) {
			$msg = $E->getMessage();
			$sts = 0;
			$result = array('sts'=>$sts,'msg' =>$msg);
			echo json_encode($result);
			exit;
		}

	$db->commit();
	$url=HOS_WEBROOT_URL.'/medical_hospital.php';
	//header("location:".$url);
	echo json_encode(array('sts'=>$sts,'msg' =>$msg ,'url' => $url));

	exit;
}


?>
