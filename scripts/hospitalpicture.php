<?php
   error_reporting(E_ALL); ini_set('display_errors', 1);
   require_once("../includes/constants.php");
   require_once("../".HOS_CLIENT_DBCONNECT."/topinc.php");
   require_once("../includes/functions.php");
   
   
   $uploadRootLocation = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-pictures' . DIRECTORY_SEPARATOR ;
   $uploadThumbLocation = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-pictures' . DIRECTORY_SEPARATOR.'thumb' . DIRECTORY_SEPARATOR ;
   $uploadLargeLocation = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-pictures' . DIRECTORY_SEPARATOR.'large' . DIRECTORY_SEPARATOR ;
   $uploadMediumLocation = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-pictures' . DIRECTORY_SEPARATOR.'medium' . DIRECTORY_SEPARATOR ;
   $uploadSocialLocation = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../'.HOS_UPLOAD_FOLDER.'/hospital-pictures' . DIRECTORY_SEPARATOR.'social' . DIRECTORY_SEPARATOR ;
   
if(isset($_REQUEST)){
	$method=$_GET['method'];
	if($method == 'addhospitalpicture'){
		
		$description = $_REQUEST['description'];
		$hospitalid = $_REQUEST['hospitalid'];
		$hiddendata = $_REQUEST['hiddendata'];
		$main = isset($_REQUEST['main'])? $_REQUEST['main'] : 'no' ;
		
		if(trim($description) == ''){
			$msg = "Please enter description!"; $sts = 0; $rtn = array( 'sts' => $sts, 'msg' => $msg ); echo json_encode($rtn); exit;
		}	
		
		$hospitaldatasql = "SELECT  `hospital_code`, `hospital_fullname`, `hospital_shortname`, `hospital_streetadress`, `area_id`, `hospital_email`, `hospital_phone`, `hospital_mobile`, `hospital_website`, `hospital_longitude`, `hospital_lattitude`, `hospital_desc`, `hospital_estyear`, `hospital_nobed`, `hospital_nophysicians`, `hospital_noemployee`, `hospital_logo` FROM `hospital_master` WHERE  hospital_id = :hospitalid";
		$hospitaldata = $db->row($hospitaldatasql , array('hospitalid'=>$hospitalid));
		
		if($main == 'yes'){
			$updatemainsql = "update hospital_pictures set hospitalp_main ='no' where hospital_id = :hospitalid";
			$updatemaindata = array('hospitalid'=>$hospitalid );
		}
				
		$insertsql = "INSERT INTO `hospital_pictures`( `hospital_id`, `hospitalp_desc`, `hospitalp_main` ,hospitalp_pic) VALUES (:hospitalid , :description, :main , :time)";
		$data = array('hospitalid'=>  $hospitalid , 'description'=> $description , 'main'=> $main , 'time' => time() );
		
		$db->beginTransaction();
		try {
			   if($main == 'yes'){
					$updatemainpicture = $db->query($updatemainsql, $updatemaindata );
			   }
			   
			   $insertpicture = $db->query($insertsql, $data );
			   $insertid        = $db->lastInsertId();
			   $sts  = 1;		   
		}
		catch (Exception $E) {
			$msg = $E->getMessage();
			$sts = 0;
			$result = array('sts'=>$sts,'msg' =>$msg);
			echo json_encode($result);
			exit;
		}
		
		if($sts  == 1){
            
            $picturewidth=980;
			$pictureheight=380;
			$thumbwidth= 200;
			$thumbheight = 100;
			$largewidth= 770;
			$largeheight = 500;
			$mediumwidth= 270;
			$mediumheight = 243;
			$socialwidth= 560;
			$socialheight = 292;
			$pictureuploaded = 0;
			for($i=0;$i<count($_FILES);$i++){
				$fileid= 'hospitalpic';
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
				$filename = $hospitaldata['hospital_code'].'_'.$insertid;
				$ext = end((explode(".", $_FILES[$fileid]["name"])));
				$orgrootfilepath=$uploadRootLocation.$filename.'.'.$ext;
				$thumbfilepath=$uploadThumbLocation.$filename.'.'.$ext;
				$largefilepath=$uploadLargeLocation.$filename.'.'.$ext;
				$mediumfilepath=$uploadMediumLocation.$filename.'.'.$ext;
				$socialfilepath=$uploadSocialLocation.$hospitaldata['hospital_code'].'.'.$ext;


				$image_info = getimagesize($_FILES[$fileid]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
				
				if(move_uploaded_file($_FILES[$fileid]["tmp_name"], $orgrootfilepath)) {
				  $sts = 1;
				  $pictureuploaded = 1;
				  $savedurl = $filename.'.'.$ext;
				  if ($cropped = resizeThumbnailImage( $thumbfilepath,$orgrootfilepath,$thumbwidth,$thumbheight)) {
					resizeThumbnailImage( $largefilepath,$orgrootfilepath,$largewidth,$largeheight);
					resizeThumbnailImage( $mediumfilepath,$orgrootfilepath,$mediumwidth,$mediumheight);

					if($main=='yes'){
						resizeThumbnailImage( $socialfilepath,$orgrootfilepath,$socialwidth,$socialheight);
					}

					$updatepicturesql = "update hospital_pictures set  hospitalp_pic = :savedurl  where hospitalp_id = :hospitalpicid ";
					$updatearr= array('savedurl'=> $savedurl ,'hospitalpicid' => $insertid);

					try
					 {
						$result = $db->query($updatepicturesql, $updatearr);
						$msg="Inserted Successfully";
						$sts= 1;
						$db->commit();

					 }
					 catch (Exception $E)
					 {
						$msg=$E -> getMessage();
						$sts= 0;
						$result = array('sts'=>$sts,'msg' =>$msg);
						echo json_encode($result);
						exit;
					 }    	
		
				  }
				  				  
				} else {
				  $sts = 0 ;
				  $msg = "File upload Failed";
				  $rtn = array('sts' => $sts,'msg'=> $msg );
				  echo json_encode($rtn);     
				  exit;
				}
			}
                    
			
		}
		
		echo json_encode(array('sts'=>$sts,'msg' =>$msg , 'url' =>"./hospital_pictures.php?data=$hiddendata"));
		exit;
	}
	elseif($method == 'edithospitalpicture'){
		
		$description = $_REQUEST['description'];
		$hospitalid = $_REQUEST['hospitalid'];
		$url = "hospital=".$hospitalid;
		$encurl= encrypt_decrypt('encrypt', $url, HOS_URL_ENCWORD);
		$hospitalpicid = $_REQUEST['hospitalpicid'];
		$main = isset($_REQUEST['main'])? $_REQUEST['main'] : 'no' ;
		
		
		$hospitalpicsql = "SELECT hp.`hospitalp_id`, h.`hospital_id`, h.hospital_fullname , h.hospital_code ,  `hospitalp_desc`, `hospitalp_pic`, `hospitalp_main` FROM `hospital_pictures`hp join hospital_master h on hp.hospital_id = h.hospital_id and hp.hospitalp_id = :hospitalpicid";
		$hospitalpicdata = $db->row($hospitalpicsql,array('hospitalpicid'=> $hospitalpicid));
		
		$oldfilename = $hospitalpicdata['hospitalp_pic'];
		$filename = $hospitalpicdata['hospital_code'].'_'.$hospitalpicdata['hospitalp_id'];
		
		if($main == 'yes'){
			$updatemainsql = "update hospital_pictures set hospitalp_main ='no' where hospital_id = :hospitalid";
			$updatemaindata = array('hospitalid'=>$hospitalid );
		}
		
		$updatehospitalpicsql = "UPDATE `hospital_pictures` SET `hospitalp_desc` = :description , `hospitalp_main` = :main ";   
		
		$data = array('description'=>  $description , 'main'=> $main , 'hospitalpicid'=> $hospitalpicid );
		
		$filecount = count($_FILES);
		for($i=0;$i<count($_FILES);$i++){
				$fileid= 'hospitalpic';
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
				$orgrootfilepath=$uploadRootLocation.$filename.'.'.$ext;
				$thumbfilepath=$uploadThumbLocation.$filename.'.'.$ext;
				$largefilepath=$uploadLargeLocation.$filename.'.'.$ext;
				$mediumfilepath=$uploadMediumLocation.$filename.'.'.$ext;
				$socialfilepath=$uploadSocialLocation.$hospitalpicdata['hospital_code'].'.'.$ext;

				$image_info = getimagesize($_FILES[$fileid]["tmp_name"]);
				$image_width = $image_info[0];
				$image_height = $image_info[1];
	            $picturewidth=980;
				$pictureheight=380;
				$thumbwidth= 200;
				$thumbheight = 100;
				$largewidth= 770;
				$largeheight = 500;
				$mediumwidth= 270;
				$mediumheight = 243;
				$socialwidth= 560;
				$socialheight = 292;
				
				$rmfilename = $uploadRootLocation.$oldfilename ;
			    if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
				  unlink($rmfilename);
			    }
			    $rmfilename = $uploadThumbLocation.$oldfilename ;
			    if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
				  unlink($rmfilename);
			    }
			    $rmfilename = $uploadLargeLocation.$oldfilename ;
			    if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
				  unlink($rmfilename);
			    }
				$rmfilename = $uploadMediumLocation.$oldfilename ;
			    if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
				  unlink($rmfilename);
			    }

				
				if(move_uploaded_file($_FILES[$fileid]["tmp_name"], $orgrootfilepath)) {
					$sts = 1;
					resizeThumbnailImage( $orgrootfilepath, $orgrootfilepath,$picturewidth,$pictureheight);
					resizeThumbnailImage( $thumbfilepath,$orgrootfilepath,$thumbwidth,$thumbheight);
					resizeThumbnailImage( $largefilepath,$orgrootfilepath,$largewidth,$largeheight);
					resizeThumbnailImage( $mediumfilepath,$orgrootfilepath,$mediumwidth,$mediumheight);
					if($main=='yes'){
					$rmfilename = $uploadSocialLocation.$hospitalpicdata['hospital_code'];
						if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
						  unlink($rmfilename);
						}
						resizeThumbnailImage( $socialfilepath,$orgrootfilepath,$socialwidth,$socialheight);
					}		  
					$saveurl = $filename.'.'.$ext;
					$updatehospitalpicsql .= ' , hospitalp_pic = :saveurl ';
					$data['saveurl'] = $saveurl ;
				  
				} else {
					$sts = 0 ;
					$msg = "File upload Failed";
					$rtn = array('sts' => $sts,'msg'=> $msg );
					echo json_encode($rtn);     
					exit;
				}
			}
		
			
		
		$updatehospitalpicsql .= " where hospitalp_id = :hospitalpicid ";
		
		
		try {
			  if($main == 'yes'){
			   $updatemainpicture = $db->query($updatemainsql, $updatemaindata );
			  }
			   $updatehospitalpic = $db->query($updatehospitalpicsql, $data );
			   $msg  = "Hospital Picture updated.";
			   $sts  = 1;		   
		}
		catch (Exception $E) {
			$msg = $E->getMessage();
			$sts = 0;
			$result = array('sts'=>$sts,'msg' =>$msg);
			echo json_encode($result);
			exit;
		}
		
		$encurl=encrypt_decrypt('encrypt', 'hospid='.$hospitalid, HOS_URL_ENCWORD);;

		echo json_encode(array('sts'=>$sts,'msg' =>$msg , 'url' =>"./hospital_pictures.php?data=$encurl"));
		exit;
		
	}
	elseif($method == 'deletehospitalpicture'){	
			
		$data = $_REQUEST['data'];
		$post = decodeurl($data);
		$hospitalpicid = $post['id'];
		$hiddendata = $_REQUEST['hiddendata'];
		
		$hospitalpicsql = "SELECT hp.`hospitalp_id`, h.`hospital_id`, h.hospital_fullname , h.hospital_code ,  `hospitalp_desc`, `hospitalp_pic`, `hospitalp_main` FROM `hospital_pictures`hp join hospital_master h on hp.hospital_id = h.hospital_id and hp.hospitalp_id = :hospitalpicid";
		$hospitalpicdata = $db->row($hospitalpicsql,array('hospitalpicid'=> $hospitalpicid));
		
		$delhospitalpicsql = "delete from hospital_pictures where hospitalp_id = :hospitalpicid";
		$data = array( 'hospitalpicid'=> $hospitalpicid);
		try {
			   $delhospitalpic = $db->query($delhospitalpicsql, $data );
				if($delhospitalpic > 0){
					$msg = "Deleted Successfully!";
					$sts =1;
				}
				else{
					$msg = "Nothing to Delete!";
					$sts =1;
				}	   
				
				if(trim($hospitalpicdata['hospitalp_pic'])!= ''){
					$rmfilename = $uploadRootLocation.$hospitalpicdata['hospitalp_pic'] ;
					if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
					  unlink($rmfilename);
					}
					$rmfilename = $uploadThumbLocation.$hospitalpicdata['hospitalp_pic'] ;
					if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
					  unlink($rmfilename);
					}
					$rmfilename = $uploadLargeLocation.$hospitalpicdata['hospitalp_pic'] ;
					if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
					  unlink($rmfilename);
					}
					$rmfilename = $uploadSocialLocation.$hospitalpicdata['hospital_code'];
					if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
					  unlink($rmfilename);
					}
					$rmfilename = $uploadMediumLocation.$hospitalpicdata['hospitalp_pic'] ;
					if(isset($rmfilename) && is_file($rmfilename) && file_exists($rmfilename)){
					  unlink($rmfilename);
					}
				}
				
		}
		catch (Exception $E) {
			$msg = $E->getMessage();
			$sts = 0;
			$result = array('sts'=>$sts,'msg' =>$msg);
			echo json_encode($result);
			exit;
		}
		

		echo json_encode(array('sts'=>$sts,'msg' =>$msg , 'url' =>"./hospital_pictures.php?data=$hiddendata"));
		exit;
	}
	
	
}


?>
