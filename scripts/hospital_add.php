<?php
   require_once("../includes/constants.php");
   error_reporting(E_ALL); ini_set('display_errors', 1);
   require_once("../".HOS_CLIENT_DBCONNECT."/topinc.php");
   require_once("../includes/functions.php");
   
if(isset($_REQUEST)){
	//print_r($_REQUEST);
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

	$query   = "select count(*) as count from hospital_master where hospital_fullname=:fullname and hospital_shortname=:shortname";
	$dataset = array('fullname' => $fullname , 'shortname'=>$shortname);
	$out     = $db->row($query, $dataset);
	if ($out['count'] > 0) {
		$msg = "This hospital (shortname - fullname combination) already exists!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); 
		exit;			
	}

	$insertsql = "INSERT INTO `hospital_master`( `hospital_fullname`, `hospital_shortname`, `hospital_streetadress`, `area_id`, `hospital_email`, `hospital_phone`, `hospital_mobile`, `hospital_website`, `hospital_longitude`, `hospital_lattitude`, `hospital_desc`, `hospital_estyear`, `hospital_nobed`, `hospital_nophysicians`, `hospital_noemployee`) VALUES ( :fullname , :shortname , :address ,:area, :emailaddress ,:phonenumber, :mobile , :website ,  :longitude , :latitude ,:description , :establishedyear , :nobed , :nophysicians , :noemployees ) ";
	$data = array('fullname'=>  $fullname , 'shortname'=> $shortname , 'address'=> $address , 'area'=>  $area , 'emailaddress'=> $emailaddress , 'phonenumber'=> $phonenumber , 'mobile'=> $mobile , 'website'=>  $website , 'longitude'=> $longitude , 'latitude'=> $latitude , 'description'=>  $description , 'establishedyear'=> $establishedyear , 'nobed'=> $nobed, 'nophysicians'=>  $nophysicians , 'noemployees'=> $noemployees );
	try {
		   $inserthospital = $db->query($insertsql, $data );
		   $insertid        = $db->lastInsertId();
		   $firmid=$insertid;
		   $sts  = 1;		   
	}
	catch (Exception $E) {
		$msg = $E->getMessage();
		$sts = 0;
		$result = array('sts'=>$sts,'msg' =>$msg);
		echo json_encode($result);
		exit;
	}
	//-----------------------------------------------------------logo

	if($sts  == 1){
		$uploadLocationRootLogo = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo' . DIRECTORY_SEPARATOR ;
		$uploadLocationThumbLogo = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo/thumb' . DIRECTORY_SEPARATOR ;
		$uploadLocationSocialLogo = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-logo/social' . DIRECTORY_SEPARATOR ;

		$hospnewid=$insertid+1000;
        $hospcode ="HSH".$hospnewid;
        
        $hospupdatesql = "update hospital_master set hospital_code = :hospcode ";
        $updatearr = array('hospcode' =>$hospcode , 'insertid' => $insertid);
                    
		$pictureuploaded = 0;
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
				$filename = $hospcode;
				$ext = end((explode(".", $_FILES[$fileid]["name"])));
				$orgfilepath=$uploadLocationRootLogo.$filename.'.'.$ext;
				$renamedLocationLogoThumb = $uploadLocationThumbLogo.$filename.".".$ext;
				$renamedLocationLogoSocial = $uploadLocationSocialLogo.$filename.".".$ext;
				$image_info = getimagesize($_FILES[$fileid]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				$picturewidthlogothumb=105;
				$pictureheightlogothumb=55;
				$picturewidthlogosocial=560;
				$pictureheightlogosocial=292;

				if(move_uploaded_file($_FILES[$fileid]["tmp_name"], $orgfilepath)) {
					$sts = 1;
					$pictureuploaded = 1;
					resizeThumbnailImage( $renamedLocationLogoThumb, $orgfilepath,$picturewidthlogothumb,$pictureheightlogothumb);
					resizeThumbnailImage( $renamedLocationLogoSocial, $orgfilepath,$picturewidthlogosocial,$pictureheightlogosocial);
					$savedurl = $hospcode.".".$ext;
					$hospupdatesql .= " , hospital_logo = :savedurl ";
					$updatearr['savedurl'] = $savedurl;
				  
				} else {
				  $sts = 0 ;
				  $msg = "File upload Failed";
				  $rtn = array('sts' => $sts,'msg'=> $msg );
				  echo json_encode($rtn);     
				  exit;
				}
			}
		}


		$hospupdatesql .= " where hospital_id = :insertid ";
		
		try
         {
                $result = $db->query($hospupdatesql, $updatearr);
                $msg="Hospital Added Successfully";
                $sts= 1;
         }
         catch (Exception $E)
         {
              $msg=$E -> getMessage();
              $sts= 0;
              $result = array('sts'=>$sts,'msg' =>$msg);
              echo json_encode($result);
              exit;
         }  
	//-------------------------------------------------------------------------------------------------------------------------------
		$inserthospmedsql="insert into hospital_register (`hospital_id`, `register_id`) values(:hospitalid , :medicalid)";
		$hospmeddata= array('hospitalid' => $insertid ,'medicalid' => $medicalid );
		try {
			   $inserthospmed = $db->query($inserthospmedsql, $hospmeddata );
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
