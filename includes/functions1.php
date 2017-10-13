<?php
function connectToRest($url,$POST,$FILES = null){
	
	$post = array();
	$headers = array(
		'AUTH_USER: Rajeev',
		'Accept:application/json'
    );
    
    if(isset($FILES) && count($FILES)>0){		
		$eol = "\r\n"; 
		$BOUNDARY = md5(time()); 
		$BODY=""; 
		
		foreach($POST as $postkey=>$postdata){
			$BODY.= '--'.$BOUNDARY. $eol; 
			$BODY .= 'Content-Disposition: form-data; name='.$postkey . $eol . $eol; 
			$BODY .= "$postdata" . $eol;
		}
		foreach($FILES as $fileskey=>$filesdata){
			$BODY.= '--'.$BOUNDARY. $eol; 
			$BODY.= 'Content-Disposition: form-data; name="'.$fileskey.'"; filename="'.$filesdata['name'].'"'. $eol ; 
			$BODY.= 'Content-Type: '.$filesdata['type'] . $eol; 
			$BODY.= 'Content-Transfer-Encoding: base64' . $eol . $eol; 
			$BODY.= chunk_split(base64_encode(file_get_contents($filesdata['tmp_name']))) . $eol; 
			$BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; 
		}
		$headers[] = "Content-Type: multipart/form-data; boundary=".$BOUNDARY ;	
		
	}
	else {
		$headers[] = "Content-Type:".$_SERVER["CONTENT_TYPE"];
		$BODY=http_build_query($POST);
	}

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
	curl_setopt($ch, CURLOPT_URL, $url); 	
	curl_setopt($ch, CURLOPT_COOKIEJAR, $BOUNDARY.'.txt'); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY); 
	$response = curl_exec($ch);	
	curl_close($curl_handle);
	return $response;

}

function decodeJsontoArray($jsondata){
	return json_decode($jsondata , TRUE);
}


function send_mail($senderName, $senderEmail,$receiverName,$to,$ctype,$subject,$msg,$smtphost,$smtpport,$smtuname,$smtppwd ,$mail,$attachments=null){
	
	$mail->IsSMTP();
	$mail->Host = $smtphost;
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = "ssl";
	$mail->Port       = $smtpport;
	$mail->Username = $smtuname;  // SMTP username
	$mail->Password = $smtppwd; // SMTP password
	
	$mail->From = $senderEmail;
	$mail->AddAddress($to, $receiverName);

	if(strcasecmp($ctype, 'Plain')==0) $isHtml = 'false';
	elseif(strcasecmp($ctype, 'HTML')==0) $isHtml = 'true';
	else $isHtml = 'true';
	$mail->IsHTML($isHtml);

	$mail->Subject = $subject;

	$mail->Body    = $msg;
	$mail->AltBody = $msg;
	if(isset($attachments)){
		foreach($attachments as $attachment){
			$attachmentname=basename($attachment);
			$mail->AddAttachment($attachment,$attachmentname);
		}
	}
	
	$mail->AddReplyTo($senderEmail,$senderName);
	
	$mail->SetFrom($senderEmail,$senderName);
	if(!$mail->Send())
	{
		return 'Message could not be sent. <p> Mailer Error: '.$mail->ErrorInfo;							  
	}
	else{							
		return 1;
	}

}


function send_massmail($senderName, $senderEmail,$receiverName,$to,$bccname,$bccemail,$ctype,$subject,$msg,$smtphost,$smtpport,$smtuname,$smtppwd ,$mail,$attachments=null){
	
	$mail->IsSMTP();
	$mail->Host = $smtphost;
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = "ssl";
	$mail->Port       = $smtpport;
	$mail->Username = $smtuname;  // SMTP username
	$mail->Password = $smtppwd; // SMTP password
	
	$mail->From = $senderEmail;
	$mail->AddAddress($to, $receiverName);

	if(strcasecmp($ctype, 'Plain')==0) $isHtml = 'false';
	elseif(strcasecmp($ctype, 'HTML')==0) $isHtml = 'true';
	else $isHtml = 'true';
	$mail->IsHTML($isHtml);

	$mail->Subject = $subject;

	$mail->Body    = $msg;
	$mail->AltBody = $msg;
	if(isset($attachments)){
		foreach($attachments as $attachment){
			$attachmentname=basename($attachment);
			$mail->AddAttachment($attachment,$attachmentname);
		}
	}
	
	$mail->AddReplyTo($senderEmail,$senderName);
	for($j=0;$j<sizeOf($bccemail);$j++)
	{
	  $mail->AddBCC($bccemail[$j], $bccname[$j]);
	}

	$mail->SetFrom($senderEmail,$senderName);
	if(!$mail->Send())
	{
		return 'Message could not be sent. <p> Mailer Error: '.$mail->ErrorInfo;							  
	}
	else{							
		return 1;
	}

}

function encrypt_decrypt($action, $string,$key) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = $key;
    $secret_iv = $key;

    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function get_client_ip() {
    $ipaddress = '';
/*
    if ($_SERVER['HTTP_CLIENT_IP'] != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_X_FORWARDED'] != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if ($_SERVER['HTTP_FORWARDED_FOR'] != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_FORWARDED'] != '127.0.0.1')
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1')
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
*/
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	return $ipaddress;
	
}

function sendEmailOTP($senderName, $senderEmail,$receiverName,$to,$ctype,$subject,$msg,$smtphost,$smtpport,$smtuname,$smtppwd,$mail )
{
	$mailStatus = send_mail($senderName, $senderEmail,$receiverName,$to,$ctype,$subject,$msg,$smtphost,$smtpport,$smtuname,$smtppwd,$mail );
	
	return $mailStatus;
}


function sendMobileOTP($mobilenumbers,$message,$delay=false,$offsetarray=null)
{


/*

	$user="indiapollu"; //your username
	$password="59853014"; //your password
	$senderid="SMSCountry"; //Your senderid
	$messagetype="N"; //Type Of Your Message
	$DReports="Y"; //Delivery Reports

	*/

	$user="internationalsms"; //your username
	$password="HZlGhtj"; //your password
	$senderid="KAPNFO"; //Your senderid

	$message = urlencode($message);

	if($delay==false)
	{

		//$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
		//$postfields="User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports";
		
		$url="http://193.105.74.159/api/v3/sendsms/plain";
		$postfields="user=$user&password=$password&GSM=$mobilenumbers&SMSText=$message&sender=$senderid&type=longSMS";

		echo $postfields."<br>";

		$ch = curl_init();
		if (!$ch){return 0;}
		$ret = curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt ($ch, CURLOPT_POSTFIELDS,$postfields);
		$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
	
	
		$curlresponse = curl_exec($ch); // execute
		

		if(curl_errno($ch))	

			return 0;
		
		if (empty($ret)) 
		{
		
			die(curl_error($ch));
			curl_close($ch); // close cURL handler
			return 0;
		} 
		else 
		{
					
			curl_close($ch); 
			
			/*
			if(preg_match('/^OK:/m', $curlresponse))
				return 1;
			else
				return 0;

			*/
			
			$resultarray=everything_in_tags($curlresponse,"status");
			if($resultarray==0)
				return 1;
			else

				return 0;

		}

	
	}
	else
	{
		if(sizeof($offsetarray)>0)
		{
			$mobilearray=explode(',',$mobilenumbers);
			if(sizeof($mobilearray)==sizeof($offsetarray))
			{
				$currentoffset=$offsetarray[0];
				$loopmobile="";
				$cutime=gmdate("Y-m-d h:i:s A", time() + 3600*($offsetarray[0]+date("I")));

				$endam=substr($cutime,strlen($cutime)-2);
				$hours=intval(substr($cutime,11,2));

				echo sizeof($mobilearray)."<br>";

				for($j=0;$j<sizeof($mobilearray);$j++)
				{
					
					
					
						if($currentoffset==$offsetarray[$j])
						{

							$loopmobile.=",".$mobilearray[$j];
							$curlexec=false;
							

						}
						else
						{
							
							
							




							

							if(($endam=="PM" and ($hours==10 or $hours==11 or $hours==9)) or ($endam=="AM" and $hours<7))
							{
								$schname=$mobilearray[$j].'-'.time();
								if($endam=="AM")
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=$cudateexp[0]." 07:00:00 AM";
								}
								else
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=dateAdd($cudateexp[0],1)." 07:00:00 AM";
									

								}

								$schinter=date_diff(date_create($cutime),date_create($schtime));
								$schinterval=$schinter->format("%dd%hh%im%ss");
								$schinterval1=($schinter->format("%d")*86400)+($schinter->format("%h")*3600)+($schinter->format("%i")*60)+$schinter->format("%s");

								$times=time()+$schinterval1;
								echo gmdate("Y-m-d h:i:s A",$times).'<br>';
								//$url="http://www.smscountry.com/APISetReminder.asp";
								//$postfields="User=$user&passwd=$password&mobilenumber=".substr($loopmobile,1)."&message=$message&SenderName=$senderid&mtype=$messagetype&DR=$DReports&schedulerName=$schname&ScheduledDateTime=$schtime&systemcurrenttime=$cutime&interval=0";	
								

								$url="http://193.105.74.159/api/v3/sendsms/plain";
								$postfields="user=$user&password=$password&GSM=".substr($loopmobile,1)."&SMSText=$message&sender=$senderid&type=longSMS&SendDateTime=$schinterval";



								$curlexec=true;
								$loopmobile=",".$mobilearray[$j];
								$currentoffset=$offsetarray[$j];

								
								

							}
							else
							{
						
						
								//$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
								//$postfields="User=$user&passwd=$password&mobilenumber=".substr($loopmobile,1)."&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports";

								$url="http://193.105.74.159/api/v3/sendsms/plain";
								$postfields="user=$user&password=$password&GSM=".substr($loopmobile,1)."&SMSText=$message&sender=$senderid&type=longSMS";

								$curlexec=true;
								$loopmobile=",".$mobilearray[$j];
								$currentoffset=$offsetarray[$j];
								
						
						

							}
					


							$cutime=gmdate("Y-m-d h:i:s A", time() + 3600*($offsetarray[$j]+date("I")));

							$endam=substr($cutime,strlen($cutime)-2);
							$hours=intval(substr($cutime,11,2));
						}




					if($j==sizeof($offsetarray)-1 and $curlexec==false)
					{

						if(($endam=="PM" and ($hours==10 or $hours==11 or $hours==9)) or ($endam=="AM" and $hours<7))
						{

							
							
							$schname=$mobilearray[$j].'-'.time();
								if($endam=="AM")
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=$cudateexp[0]." 07:00:00 AM";
								}
								else
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=dateAdd($cudateexp[0],1)." 07:00:00 AM";
									

								}

							$schinter=date_diff(date_create($cutime),date_create($schtime));
							$schinterval=$schinter->format("%dd%hh%im%ss");
							$schinterval1=($schinter->format("%d")*86400)+($schinter->format("%h")*3600)+($schinter->format("%i")*60)+$schinter->format("%s");

								$times=time()+$schinterval1;
								echo gmdate("Y-m-d h:i:s A",$times).'<br>';

							//$url="http://www.smscountry.com/APISetReminder.asp";
							//$postfields="User=$user&passwd=$password&mobilenumber=".substr($loopmobile,1)."&message=$message&SenderName=$senderid&mtype=$messagetype&DR=$DReports&schedulerName=$schname&ScheduledDateTime=$schtime&systemcurrenttime=$cutime&interval=0";	
							

							$url="http://193.105.74.159/api/v3/sendsms/plain";
							$postfields="user=$user&password=$password&GSM=".substr($loopmobile,1)."&SMSText=$message&sender=$senderid&type=longSMS&SendDateTime=$schinterval";


							$curlexec=true;
							$loopmobile="";
							
							
							
						}
						else
						{

							//$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
							//$postfields="User=$user&passwd=$password&mobilenumber=".substr($loopmobile,1)."&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports";
							

							$url="http://193.105.74.159/api/v3/sendsms/plain";
							$postfields="user=$user&password=$password&GSM=".substr($loopmobile,1)."&SMSText=$message&sender=$senderid&type=longSMS";


							$curlexec=true;
							$loopmobile="";
							
							

						}
						
					}
					if($curlexec==true )
					{
		
						echo $postfields."<br>";

						$ch = curl_init();
						if (!$ch){return 0;}
						$ret = curl_setopt($ch, CURLOPT_URL,$url);
						curl_setopt ($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
						curl_setopt($ch, CURLOPT_TIMEOUT, 20);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
						curl_setopt ($ch, CURLOPT_POSTFIELDS,$postfields);
						$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
						$fp=fopen(dirname(__FILE__)."/../sms/log.txt","a");
						fwrite($fp,$url." : ".$postfields." : ".date('d/m/Y H:i:s').PHP_EOL);	
						
						$curlresponse = curl_exec($ch); // execute

							
						if(curl_errno($ch))	

							//return 0;
		
						if (empty($ret)) 
						{
		
							die(curl_error($ch));
							curl_close($ch); // close cURL handler
							//return 0;
						} 
						else 
						{
							
		
							curl_close($ch); 
							//return 1;
		
			
						}
						
						$curlexec=false;
						
					}
					if($j==sizeof($offsetarray)-1 && $loopmobile != "")
					{
						


						if(($endam=="PM" and ($hours==10 or $hours==11 or $hours==9)) or ($endam=="AM" and $hours<7))
						{

							

							$schname=$mobilearray[$j].'-'.time();
								if($endam=="AM")
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=$cudateexp[0]." 07:00:00 AM";
								}
								else
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=dateAdd($cudateexp[0],1)." 07:00:00 AM";
									

								}


							$schinter=date_diff(date_create($cutime),date_create($schtime));
							$schinterval=$schinter->format("%dd%hh%im%ss");
							$schinterval1=($schinter->format("%d")*86400)+($schinter->format("%h")*3600)+($schinter->format("%i")*60)+$schinter->format("%s");

								$times=time()+$schinterval1;
								echo gmdate("Y-m-d h:i:s A",$times).'<br>';
	
							//$url="http://www.smscountry.com/APISetReminder.asp";
							//$postfields="User=$user&passwd=$password&mobilenumber=".substr($loopmobile,1)."&message=$message&SenderName=$senderid&mtype=$messagetype&DR=$DReports&schedulerName=$schname&ScheduledDateTime=$schtime&systemcurrenttime=$cutime&interval=0";	

							
							$url="http://193.105.74.159/api/v3/sendsms/plain";
							$postfields="user=$user&password=$password&GSM=".substr($loopmobile,1)."&SMSText=$message&sender=$senderid&type=longSMS&SendDateTime=$schinterval";

							$loopmobile="";
							
							
						}
						else
						{

							//$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
							//$postfields="User=$user&passwd=$password&mobilenumber=".substr($loopmobile,1)."&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports";
							
							$url="http://193.105.74.159/api/v3/sendsms/plain";
							$postfields="user=$user&password=$password&GSM=".substr($loopmobile,1)."&SMSText=$message&sender=$senderid&type=longSMS";
							$loopmobile="";
							
							

						}

						echo $postfields."<br>";

						$ch = curl_init();
						if (!$ch){return 0;}
						$ret = curl_setopt($ch, CURLOPT_URL,$url);
						curl_setopt ($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
						curl_setopt($ch, CURLOPT_TIMEOUT, 20);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
						curl_setopt ($ch, CURLOPT_POSTFIELDS,$postfields);
						$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
						
						$fp=fopen(dirname(__FILE__)."/../sms/log.txt","a");
						fwrite($fp,$url." : ".$postfields." : ".date('d/m/Y H:i:s').PHP_EOL);						
						
						
						

						
						$curlresponse = curl_exec($ch); // execute
						
	
						if(curl_errno($ch))	

							//return 0;
		
						if (empty($ret)) 
						{
		
							die(curl_error($ch));
							curl_close($ch); // close cURL handler
							//return 0;
						} 
						else 
						{
							
		
							curl_close($ch); 
							//return 1;
		
			
						}
						

						$curlexec=false;

							


					}

				}

				return 1;
			}
			else

			{
				return 0;
			}
		}
		else
		{

			return 0;
		}


	}


	
}


function dateAdd($source_date,$interval_days)
{

	return date("Y-m-d",strtotime($interval_days." day", strtotime($source_date)));
}
function magicprofileID($profileID)
{

  if($profileID % 7==0 and $profileID>IPY_PROFILEID_RESERVED)
  {

    return 1;

  }
  else
  {

    return 0;


  }

    
}

function CheckforDelete($ID,$tablearray,$db)
{
	foreach ($tablearray as $key => $value) 
	{
		$table=$value['table'];$field=$value['field'];$msg=$value['msg'];
		$qry= "select 1 from ".$table." where ".$field."=".$ID." limit 1";
		try
		{
		  $data = array();
		  $result =$db->query($qry, $data);
		  if(count($result) > 0)
		  {
                	$message=$msg;
                	$sts=0;
			break;
		  }
		  else
		  {
               		$message="Confirm To Remove";
               		$sts=1;
		  }
	      }
	      catch (Exception $E)
	      {
                $message=$E -> getMessage();
                $sts= 0;
		break;
	      }
      	
      }
      $rtn = array('sts'=>$sts,'msg' =>$message);
      return $rtn;
}

function CheckforRelationalDelete($ID,$tablearray,$db)
{
	foreach ($tablearray as $key => $value) 
	{
		$field='';
		$table=$value['table'];$msg=$value['msg'];
		$i=1;
		foreach ($value['fields'] as $key =>$val ) {
			$id= $value['values'][$key];
			if($i==1){
				$field.= $val ."=" .$id;
			}else{
				$field.= " and ". $val ."=" .$id;
			}
			$i+=1;
		}
		$qry= "select 1 from ".$table." where ".$field." limit 1";
		try
		{
		  $data = array();
		  $result =$db->query($qry, $data);
		  if(count($result) > 0)
		  {
						$message=$msg;
						$sts=0;
						break;
		  }
		  else
		  {
							$message="Confirm To Remove";
							$sts=1;
		  }
		}
		catch (Exception $E)
		{
					 $message=$E -> getMessage();
					 $sts= 0;
					 break;
		}
	}
      $rtn = array('sts'=>$sts,'msg' =>$message);
      return $rtn;
}


function getCountryCodefromIp(){
	$ip = get_client_ip();
	$url="http://api.ipinfodb.com/v3/ip-city/?key=ee596eb11609594154b1e824e145c813a85f8b5a2fa462fcc776a23ba4ac236f&ip=".$ip."&format=json";
$ch=curl_init();
$timeout=5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Accept: application/json'
	));
	$result=curl_exec($ch);
	curl_close($ch);
	return json_decode($result, TRUE);
}


function chooseAdvertisements($bannertype, $location ,$locationlevel ,$limitcount ,$ip ,$ctime ,$db,$memid=0) {
	$cdate = date("Y-m-d") ;
	$dispfraudtime = $ctime - IPY_DISPLAY_IP_INTERVAL;
	
	switch ($locationlevel) {
		case "country":
			$sql = "call getAdvertisementCountry(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime, :memid)";
			break;
		case "state":
			$sql = "call getAdvertisementState(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
			break;
		case "district":
			$sql = "call getAdvertisementDistrict(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
			break;
		case "parliament":
			$sql = "call getAdvertisementParliament(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
			break;
		case "assembly":
			$sql = "call getAdvertisementAssembly(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
			break;
		case "localbody":
			$sql = "call getAdvertisementLocalbody(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
			break;
		case "ward":
			$sql = "call getAdvertisementWard(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
			break;
		case "none":
			$sql = "call getAdvertisementCountry(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
			break;						
		default:
			$sql = "call getAdvertisementCountry(:cdate, :bannertype, :location, :limitcount, :ip, :dispfraudtime,:memid)";
	}
	$arraydata = array('cdate'=>$cdate,'bannertype'=>$bannertype,'location'=>$location,'limitcount'=>$limitcount ,'ip' =>$ip ,'dispfraudtime'=> $dispfraudtime,'memid'=>$memid);
	$advertisementsrow = $db->query($sql,$arraydata);
	return $advertisementsrow;
}
function renderAdvertisements($advid,$memid,$ip,$advtime,$advdate,$db){
	
	
	if(empty($memid) or ($memid=='') or ($memid=='NULL') or ($memid==NULL)){
		$sql = "call renderAdvertisement(:advid, null, :ip, :advtime, :advdate)";
		$arraydata = array('advid'=>$advid,'ip'=>$ip,'advtime'=>$advtime,'advdate'=>$advdate);
	}else{
		$sql = "call renderAdvertisement(:advid, :memid, :ip, :advtime, :advdate)";
		$arraydata = array('advid'=>$advid,'memid' =>$memid ,'ip'=>$ip,'advtime'=>$advtime,'advdate'=>$advdate);
	}
	
	//print_r($arraydata);
	$renderadvrow  = $db->row($sql,$arraydata);
	return $renderadvrow;
}
function clickAdvertisement($actid,$memid,$ip,$db){
	$time = time();
	$date = date("Y-m-d") ;
	//$clickfraudtime = $time - IPY_CLICK_FRAUD_INTERVAL;
	
	//$clicksql = "call clickAdvertisement(:actid,:memid,:ip,:time, :date, :clickfraudtime)";
	//$arr = array('actid' =>$actid , 'memid' => $memid , 'ip' => $ip ,'time' =>$time, 'date'=>$date,'clickfraudtime' =>$clickfraudtime);
	$clicksql = "call clickAdvertisement(:actid,:memid,:ip,:time, :date)";
	$arr = array('actid' =>$actid , 'memid' => $memid , 'ip' => $ip ,'time' =>$time, 'date'=>$date);
	$clickrow = $db->row($clicksql,$arr);
	return $clickrow;
}

function everything_in_tags($string, $tagname)
{
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    preg_match($pattern, $string, $matches);
    return $matches[1];
}

?>
