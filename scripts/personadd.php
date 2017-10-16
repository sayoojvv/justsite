<?php
   require_once("../includes/constants.php");
   //error_reporting(E_ALL); ini_set('display_errors', 1);
   require_once("../".HOS_CLIENT_DBCONNECT."/topinc.php");
   require_once("../includes/functions.php");
   
if(isset($_REQUEST)){
	if($_GET['method']=='add'){
		//print_r($_REQUEST); print_r($_FILES); exit;
		$fullname = $_REQUEST['fullname'];
		$mobile = $_REQUEST['mobile'];
		$emailaddress = $_REQUEST['emailaddress'];
		$time = time();

			
		if(trim($mobile) == ''){
			$msg = "Please enter mobile!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
		}
		if(trim($fullname) == ''){
			$msg = "Please enter fullname!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
		}
		if(trim($emailaddress) == ''){
			$msg = "Please enter email address!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
		}


		$db->beginTransaction();

		$query   = "select count(*) as count from person where email=:emailaddress ";
		$dataset = array('emailaddress'=>$emailaddress);
		$out     = $db->row($query, $dataset);
		if ($out['count'] > 0) {
			$msg = "This email already exists!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); 
			exit;			
		}

		$insertsql = "INSERT INTO `person`( `name`, email,phone) VALUES ( :fullname , :emailaddress ,:phone ) ";
		$data = array('fullname'=>  $fullname ,  'emailaddress'=> $emailaddress , 'phone'=> $mobile  );
		try {
			   $insertpersonital = $db->query($insertsql, $data );
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
			$uploadLocationRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/person' . DIRECTORY_SEPARATOR ;

			$personnewid=$insertid+1000;
	        $personcode ="PER".$personnewid;
	        
	        $personupdatesql = "update person set  ";
	        $updatearr = array( 'insertid' => $insertid);
	                    
			$pictureuploaded = 0;

				for($i=0;$i<count($_FILES);$i++){
					$fileid='personphoto';
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
					$filename = $personcode;
					$ext = end((explode(".", $_FILES[$fileid]["name"])));
					$orgfilepath=$uploadLocationRoot.$filename.'.'.$ext;
					$image_info = getimagesize($_FILES[$fileid]["tmp_name"]);
					$image_width = $image_info[0];
					$image_height = $image_info[1];
					$picturewidthlogothumb=105;
					$pictureheightlogothumb=55;


					if(move_uploaded_file($_FILES[$fileid]["tmp_name"], $orgfilepath)) {
						$sts = 1;
						$pictureuploaded = 1;
						$savedurl = $personcode.".".$ext;
						$personupdatesql .= " fpath = :savedurl ";
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
			$personupdatesql .= " where id = :insertid ";
			
			try
	         {
	                $result = $db->query($personupdatesql, $updatearr);
	                $msg="Person Added Successfully";
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
		$db->commit();
		echo json_encode(array('sts'=>$sts,'msg' =>$msg));

		exit;
	}elseif($_GET['method']=='getdata'){
	 	$id=$_REQUEST['id'];
	 	$personsql="select * from person where id=:id";
		$persondata=$db->row($personsql,array('id' => $id));
		$persondata['fpath']=HOS_WEBROOT_URL.HOS_UPLOAD_FOLDER.'/person/'.$persondata['fpath'];
		$result = array('data'=>$persondata);
		echo json_encode($result);
		exit;
 	}elseif($_GET['method']=='emailexist'){
	 	$email=$_REQUEST['value'];
	 	$personsql="select count(1) as cnt from person where email=:email";
		$result=$db->row($personsql,array('email' => $email));
		$result=$result['cnt'];
		echo json_encode($result);
		exit;
 	}
 }



?>
