<?php
function connectToRest($url, $POST, $FILES = null)
{
    $post    = array();
    $headers = array(
        'AUTH_USER: Rajeev',
        'Accept:application/json'
    );
    if (isset($FILES) && count($FILES) > 0) {
        $eol      = "\r\n";
        $BOUNDARY = md5(time());
        $BODY     = "";
        foreach ($POST as $postkey => $postdata) {
            $BODY .= '--' . $BOUNDARY . $eol;
            $BODY .= 'Content-Disposition: form-data; name=' . $postkey . $eol . $eol;
            $BODY .= "$postdata" . $eol;
        }
        foreach ($FILES as $fileskey => $filesdata) {
            $BODY .= '--' . $BOUNDARY . $eol;
            $BODY .= 'Content-Disposition: form-data; name="' . $fileskey . '"; filename="' . $filesdata['name'] . '"' . $eol;
            $BODY .= 'Content-Type: ' . $filesdata['type'] . $eol;
            $BODY .= 'Content-Transfer-Encoding: base64' . $eol . $eol;
            $BODY .= chunk_split(base64_encode(file_get_contents($filesdata['tmp_name']))) . $eol;
            $BODY .= '--' . $BOUNDARY . '--' . $eol . $eol;
        }
        $headers[] = "Content-Type: multipart/form-data; boundary=" . $BOUNDARY;
    } else {
        $headers[] = "Content-Type:" . $_SERVER["CONTENT_TYPE"];
        $BODY      = http_build_query($POST);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $BOUNDARY . '.txt');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY);
    $response = curl_exec($ch);
    curl_close($curl_handle);
    return $response;
}
function decodeJsontoArray($jsondata)
{
    return json_decode($jsondata, TRUE);
}
function send_mail($senderName, $senderEmail, $receiverName, $to, $ctype, $subject, $msg, $smtphost, $smtpport, $smtuname, $smtppwd, $mail, $attachments = null)
{
    $mail->IsSMTP();
    $mail->Host       = $smtphost;
    $mail->SMTPAuth   = true;
	
	if(strpos($smtuname, 'gmail.com')===false)

    $mail->SMTPSecure = "";
else
 $mail->SMTPSecure = "ssl";
    $mail->Port       = $smtpport;
    $mail->Username   = $smtuname; // SMTP username
    $mail->Password   = $smtppwd; // SMTP password
    $mail->From       = $senderEmail;
    $mail->AddAddress($to, $receiverName);
    if (strcasecmp($ctype, 'Plain') == 0)
        $isHtml = 'false';
    elseif (strcasecmp($ctype, 'HTML') == 0)
        $isHtml = 'true';
    else
        $isHtml = 'true';
    $mail->IsHTML($isHtml);
    $mail->Subject = $subject;
    $mail->Body    = $msg;
    $mail->AltBody = $msg;
    if (isset($attachments)) {
        foreach ($attachments as $attachment) {
            $attachmentname = basename($attachment);
            $mail->AddAttachment($attachment, $attachmentname);
        }
    }
    $mail->AddReplyTo($senderEmail, $senderName);
    $mail->SetFrom($senderEmail, $senderName);
    if (!$mail->Send()) {
        return 'Message could not be sent-'.$mail->SMTPSecure.' <p> Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return 1;
    }
}
function send_massmail($senderName, $senderEmail, $receiverName, $to, $bccname, $bccemail, $ctype, $subject, $msg, $smtphost, $smtpport, $smtuname, $smtppwd, $mail, $attachments = null)
{
    $mail->IsSMTP();
    $mail->Host       = $smtphost;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "";
    $mail->Port       = $smtpport;
    $mail->Username   = $smtuname; // SMTP username
    $mail->Password   = $smtppwd; // SMTP password
    $mail->From       = $senderEmail;
    $mail->AddAddress($to, $receiverName);
    if (strcasecmp($ctype, 'Plain') == 0)
        $isHtml = 'false';
    elseif (strcasecmp($ctype, 'HTML') == 0)
        $isHtml = 'true';
    else
        $isHtml = 'true';
    $mail->IsHTML($isHtml);
    $mail->Subject = $subject;
    $mail->Body    = $msg;
    $mail->AltBody = $msg;
    if (isset($attachments)) {
        foreach ($attachments as $attachment) {
            $attachmentname = basename($attachment);
            $mail->AddAttachment($attachment, $attachmentname);
        }
    }
    $mail->AddReplyTo($senderEmail, $senderName);
    for ($j = 0; $j < sizeOf($bccemail); $j++) {
        $mail->AddBCC($bccemail[$j], $bccname[$j]);
    }
    $mail->SetFrom($senderEmail, $senderName);
    if (!$mail->Send()) {
        return 'Message could not be sent. <p> Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return 1;
    }
}
function encrypt_decrypt($action, $string, $key)
{
    $output         = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key     = $key;
    $secret_iv      = $key;
    // hash
    $key            = hash('sha256', $secret_key);
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv             = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && trim($_SERVER['HTTP_CLIENT_IP'])!='')
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])&& !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && trim($_SERVER['HTTP_X_FORWARDED_FOR'])!='')
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED'])&& !empty($_SERVER['HTTP_X_FORWARDED']) && trim($_SERVER['HTTP_X_FORWARDED'])!='')
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR'])&& !empty($_SERVER['HTTP_FORWARDED_FOR']) && trim($_SERVER['HTTP_FORWARDED_FOR'])!='')
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED'])&& !empty($_SERVER['HTTP_FORWARDED']) && trim($_SERVER['HTTP_FORWARDED'])!='')
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR'])&& !empty($_SERVER['REMOTE_ADDR']) && trim($_SERVER['REMOTE_ADDR'])!='')
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function sendEmailOTP($senderName, $senderEmail, $receiverName, $to, $ctype, $subject, $msg, $smtphost, $smtpport, $smtuname, $smtppwd, $mail)
{
    $mailStatus = send_mail($senderName, $senderEmail, $receiverName, $to, $ctype, $subject, $msg, $smtphost, $smtpport, $smtuname, $smtppwd, $mail);
    return $mailStatus;
}
function sendMobileOTP($mobilenumbers, $message, $delay = false, $offsetarray = null, $dbcon = null)
{
   
    $message = urlencode($message);
    if ($delay == false) {
        
	

	//smcountry
        $postfields="User=".IPY_SMS_USER."&passwd=".IPY_SMS_PASSWORD."&mobilenumber=$mobilenumbers&message=$message&sid=".IPY_SMS_SENDER."&mtype=N&DR=Y";

	//kapsystem
        //$postfields = "user=" . IPY_SMS_USER . "&password=" . IPY_SMS_PASSWORD . "&GSM=$mobilenumbers&SMSText=$message&sender=" . IPY_SMS_SENDER . "&type=longSMS";

	//indiasms
	//$postfields="username=".IPY_SMS_USER."&password=".IPY_SMS_PASSWORD."&type=TEXT&sender=".IPY_SMS_SENDER."&mobile=$mobilenumbers&message=".$message; 


        //echo $postfields."<br>";
        $ch         = curl_init();
        if (!$ch) {
            return 0;
        }
        $ret = curl_setopt($ch, CURLOPT_URL, IPY_SMS_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


	$nth = nth_strpos($postfields, '&', 2, true);
	$postfields1=substr($postfields, $nth);

        $fp  = fopen(dirname(__FILE__) . "/../ipy-sms/log.txt", "a");
        fwrite($fp, IPY_SMS_URL . " : " . $postfields1 . " : " . date('d/m/Y H:i:s') . PHP_EOL);

        $curlresponse = curl_exec($ch); // execute
        if (curl_errno($ch))
            return 0;
        if (empty($ret)) {
            die(curl_error($ch));
            curl_close($ch); // close cURL handler
            return 0;
        } else {
            curl_close($ch);
            
		//smscountry

            if(preg_match('/^OK:/m', $curlresponse))
            return 1;
            else
            return 0;


	
		//indiasms
/*
            if(preg_match('/^SUBMIT_SUCCESS:/m', $curlresponse))
            return 1;
            else
            return 0;
            
            */
            
	//kapsystem
/*
            $resultarray = everything_in_tags($curlresponse, "status");
            if ($resultarray == 0)
                return 1;
            else
                return 0;
*/


        }
    } else {
        $dest_numbers = "";
        $dest_msg     = "";
        $dest_time    = 0;
        if (sizeof($offsetarray) > 0) {
            $mobilearray = explode(',', $mobilenumbers);
            if (sizeof($mobilearray) == sizeof($offsetarray)) {
                $currentoffset = $offsetarray[0];
                $loopmobile    = "";
                $cutime        = gmdate("Y-m-d h:i:s A", time() + 3600 * ($offsetarray[0] + date("I")));
                $endam         = substr($cutime, strlen($cutime) - 2);
                $hours         = intval(substr($cutime, 11, 2));
                for ($j = 0; $j < sizeof($mobilearray); $j++) {
                    if ($currentoffset == $offsetarray[$j]) {
                        $loopmobile .= "," . $mobilearray[$j];
                        $curlexec = false;
                    } else {
                        if (($endam == "PM" and ($hours == 10 or $hours == 11 or $hours == 9)) or ($endam == "AM" and ($hours < 7 or $hours==12))) {
                            $schname = $mobilearray[$j] . '-' . time();
                            if ($endam == "AM") {
                                $cudateexp = explode(" ", $cutime);
                                $schtime   = $cudateexp[0] . " 07:00:00 AM";
                            } else {
                                $cudateexp = explode(" ", $cutime);
                                $schtime   = dateAdd($cudateexp[0], 1) . " 07:00:00 AM";
                            }
                            $schinter      = date_diff(date_create($cutime), date_create($schtime));
                            $schinterval   = $schinter->format("%dd%hh%im%ss");
                            $schinterval1  = ($schinter->format("%d") * 86400) + ($schinter->format("%h") * 3600) + ($schinter->format("%i") * 60) + $schinter->format("%s");
                           
                            
//smscountry
				$postfields="User=".IPY_SMS_USER."&passwd=".IPY_SMS_PASSWORD."&mobilenumber=".substr($loopmobile,1)."&message=$message&SenderName=".IPY_SMS_SENDER."&mtype=N&DR=Y&schedulerName=$schname&ScheduledDateTime=$schtime&systemcurrenttime=$cutime&interval=0";
	

//kapsystem
                           // $postfields    = "user=" . IPY_SMS_USER . "&password=" . IPY_SMS_PASSWORD . "&GSM=" . substr($loopmobile, 1) . "&SMSText=$message&sender=" . IPY_SMS_SENDER . "&type=longSMS&SendDateTime=$schinterval";


//indiasms
	//$postfields="username=".IPY_SMS_USER."&password=".IPY_SMS_PASSWORD."&type=TEXT&sender=".IPY_SMS_SENDER."&mobile=" . substr($loopmobile, 1) . "&message=".$message; 


                            $dest_numbers  = substr($loopmobile, 1);
                            $dest_msg      = $message;
                            $dest_time     = time() + $schinterval1;
                            $curlexec      = true;
                            $loopmobile    = "," . $mobilearray[$j];
                            $currentoffset = $offsetarray[$j];
                        } else {
                           
			//smcountry
                            $postfields="User=".IPY_SMS_USER."&passwd=".IPY_SMS_PASSWORD."&mobilenumber=".substr($loopmobile,1)."&message=$message&sid=".IPY_SMS_SENDER."&mtype=N&DR=Y";
                            
	//kapsystem
	//$postfields    = "user=" . IPY_SMS_USER . "&password=" . IPY_SMS_PASSWORD . "&GSM=" . substr($loopmobile, 1) . "&SMSText=$message&sender=" . IPY_SMS_SENDER . "&type=longSMS";


//indiasms
	//$postfields="username=".IPY_SMS_USER."&password=".IPY_SMS_PASSWORD."&type=TEXT&sender=".IPY_SMS_SENDER."&mobile=" . substr($loopmobile, 1) . "&message=".$message; 


                            $dest_numbers  = substr($loopmobile, 1);
                            $dest_msg      = $message;
                            $dest_time     = 0;
                            $curlexec      = true;
                            $loopmobile    = "," . $mobilearray[$j];
                            $currentoffset = $offsetarray[$j];
                        }
                        $cutime = gmdate("Y-m-d h:i:s A", time() + 3600 * ($offsetarray[$j] + date("I")));
                        $endam  = substr($cutime, strlen($cutime) - 2);
                        $hours  = intval(substr($cutime, 11, 2));
                    }
                    if ($j == sizeof($offsetarray) - 1 and $curlexec == false) {
                        if (($endam == "PM" and ($hours == 10 or $hours == 11 or $hours == 9)) or ($endam == "AM" and ($hours < 7 or $hours==12))) {
                            $schname = $mobilearray[$j] . '-' . time();
                            if ($endam == "AM") {
                                $cudateexp = explode(" ", $cutime);
                                $schtime   = $cudateexp[0] . " 07:00:00 AM";
                            } else {
                                $cudateexp = explode(" ", $cutime);
                                $schtime   = dateAdd($cudateexp[0], 1) . " 07:00:00 AM";
                            }
                            $schinter     = date_diff(date_create($cutime), date_create($schtime));
                            $schinterval  = $schinter->format("%dd%hh%im%ss");
                            $schinterval1 = ($schinter->format("%d") * 86400) + ($schinter->format("%h") * 3600) + ($schinter->format("%i") * 60) + $schinter->format("%s");
                            

			//smcountry
                            $postfields="User=".IPY_SMS_USER."&passwd=".IPY_SMS_PASSWORD."&mobilenumber=".substr($loopmobile,1)."&message=$message&SenderName=".IPY_SMS_SENDER."&mtype=N&DR=Y&schedulerName=$schname&ScheduledDateTime=$schtime&systemcurrenttime=$cutime&interval=0";
	
			//kapsystem
                           // $postfields   = "user=" . IPY_SMS_USER . "&password=" . IPY_SMS_PASSWORD . "&GSM=" . substr($loopmobile, 1) . "&SMSText=$message&sender=" . IPY_SMS_SENDER . "&type=longSMS&SendDateTime=$schinterval";


//indiasms
	//$postfields="username=".IPY_SMS_USER."&password=".IPY_SMS_PASSWORD."&type=TEXT&sender=".IPY_SMS_SENDER."&mobile=" . substr($loopmobile, 1) . "&message=".$message; 



                            $dest_numbers = substr($loopmobile, 1);
                            $dest_msg     = $message;
                            $dest_time    = time() + $schinterval1;
                            $curlexec     = true;
                            $loopmobile   = "";
                        } else {
                           

			//smscountry
                            $postfields="User=".IPY_SMS_USER."&passwd=".IPY_SMS_PASSWORD."&mobilenumber=".substr($loopmobile,1)."&message=$message&sid=".IPY_SMS_SENDER."&mtype=N&DR=Y";
                            

		//kapsystem
	//$postfields   = "user=" . IPY_SMS_USER . "&password=" . IPY_SMS_PASSWORD . "&GSM=" . substr($loopmobile, 1) . "&SMSText=$message&sender=" . IPY_SMS_SENDER . "&type=longSMS";


//indiasms
	//$postfields="username=".IPY_SMS_USER."&password=".IPY_SMS_PASSWORD."&type=TEXT&sender=".IPY_SMS_SENDER."&mobile=" . substr($loopmobile, 1) . "&message=".$message; 




                            $dest_numbers = substr($loopmobile, 1);
                            $dest_msg     = $message;
                            $dest_time    = 0;
                            $curlexec     = true;
                            $loopmobile   = "";
                        }
                    }
                    if ($curlexec == true) {
                        //echo $postfields."<br>";
                        if ($dest_time == 0) {
                            $ch = curl_init();
                            if (!$ch) {
                                return 0;
                            }
                            $ret = curl_setopt($ch, CURLOPT_URL, IPY_SMS_URL);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                            $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$nth = nth_strpos($postfields, '&', 2, true);
				$postfields1=substr($postfields, $nth);

                            $fp  = fopen(dirname(__FILE__) . "/../ipy-sms/log.txt", "a");
                            fwrite($fp, IPY_SMS_URL . " : " . $postfields1 . " : " . date('d/m/Y H:i:s') . PHP_EOL);


                            $curlresponse = curl_exec($ch); // execute
                            if (curl_errno($ch))
                            //return 0;
                                if (empty($ret)) {
                                    die(curl_error($ch));
                                    curl_close($ch); // close cURL handler
                                    //return 0;
                                } else {
                                    curl_close($ch);
                                    //return 1;
                                }
                        } else {
                            $insertcron = "insert into smscron_master (smscron_gsm, smscron_msg , smscron_time) values(:gsm , :msg , :ttime)";
                            $datacron   = array(
                                'gsm' => $dest_numbers,
                                'msg' => $dest_msg,
                                'ttime' => $dest_time
                            );
                            $out        = $dbcon->query($insertcron, $datacron);
                        }
                        $curlexec = false;
                    }
                    if ($j == sizeof($offsetarray) - 1 && $loopmobile != "") {
                        if (($endam == "PM" and ($hours == 10 or $hours == 11 or $hours == 9)) or ($endam == "AM" and ($hours < 7 or $hours==12))) {
                            $schname = $mobilearray[$j] . '-' . time();
                            if ($endam == "AM") {
                                $cudateexp = explode(" ", $cutime);
                                $schtime   = $cudateexp[0] . " 07:00:00 AM";
                            } else {
                                $cudateexp = explode(" ", $cutime);
                                $schtime   = dateAdd($cudateexp[0], 1) . " 07:00:00 AM";
                            }
                            $schinter     = date_diff(date_create($cutime), date_create($schtime));
                            $schinterval  = $schinter->format("%dd%hh%im%ss");
                            $schinterval1 = ($schinter->format("%d") * 86400) + ($schinter->format("%h") * 3600) + ($schinter->format("%i") * 60) + $schinter->format("%s");
                           
//smscountry
                           $postfields="User=".IPY_SMS_USER."&passwd=".IPY_SMS_PASSWORD."&mobilenumber=".substr($loopmobile,1)."&message=$message&SenderName=".IPY_SMS_SENDER."&mtype=N&DR=Y&schedulerName=$schname&ScheduledDateTime=$schtime&systemcurrenttime=$cutime&interval=0";
	
//kapsystem
                           // $postfields   = "user=" . IPY_SMS_USER . "&password=" . IPY_SMS_PASSWORD . "&GSM=" . substr($loopmobile, 1) . "&SMSText=$message&sender=" . IPY_SMS_SENDER . "&type=longSMS&SendDateTime=$schinterval";

//indiasms
	//$postfields="username=".IPY_SMS_USER."&password=".IPY_SMS_PASSWORD."&type=TEXT&sender=".IPY_SMS_SENDER."&mobile=" . substr($loopmobile, 1) . "&message=".$message; 




                            $dest_numbers = substr($loopmobile, 1);
                            $dest_msg     = $message;
                            $dest_time    = time() + $schinterval1;
                            $loopmobile   = "";
                        } else {
                            


//smscountry
                         $postfields="User=".IPY_SMS_USER."&passwd=".IPY_SMS_PASSWORD."&mobilenumber=".substr($loopmobile,1)."&message=$message&sid=".IPY_SMS_SENDER."&mtype=N&DR=Y";


//kapsystem
                            //$postfields   = "user=" . IPY_SMS_USER . "&password=" . IPY_SMS_PASSWORD . "&GSM=" . substr($loopmobile, 1) . "&SMSText=$message&sender=" . IPY_SMS_SENDER . "&type=longSMS";


//indiasms
	//$postfields="username=".IPY_SMS_USER."&password=".IPY_SMS_PASSWORD."&type=TEXT&sender=".IPY_SMS_SENDER."&mobile=" . substr($loopmobile, 1) . "&message=".$message; 



                            $dest_numbers = substr($loopmobile, 1);
                            $dest_msg     = $message;
                            $dest_time    = 0;
                            $loopmobile   = "";
                        }
                        //echo $postfields."<br>";
                        if ($dest_time == 0) {
                            $ch = curl_init();
                            if (!$ch) {
                                return 0;
                            }
                            $ret = curl_setopt($ch, CURLOPT_URL, IPY_SMS_URL);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                            $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


				$nth = nth_strpos($postfields, '&', 2, true);
				$postfields1=substr($postfields, $nth);


                            $fp  = fopen(dirname(__FILE__) . "/../ipy-sms/log.txt", "a");
                            fwrite($fp, IPY_SMS_URL . " : " . $postfields1 . " : " . date('d/m/Y H:i:s') . PHP_EOL);
                            $curlresponse = curl_exec($ch); // execute
                            if (curl_errno($ch))
                            //return 0;
                                if (empty($ret)) {
                                    die(curl_error($ch));
                                    curl_close($ch); // close cURL handler
                                    //return 0;
                                } else {
                                    curl_close($ch);
                                    //return 1;
                                }
                        } else {
                            $insertcron = "insert into smscron_master (smscron_gsm, smscron_msg , smscron_time) values(:gsm , :msg , :ttime)";
                            $datacron   = array(
                                'gsm' => $dest_numbers,
                                'msg' => $dest_msg,
                                'ttime' => $dest_time
                            );
                            $out        = $dbcon->query($insertcron, $datacron);
                        }
                        $curlexec = false;
                    }
                }
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
}
function dateAdd($source_date, $interval_days)
{
    return date("Y-m-d", strtotime($interval_days . " day", strtotime($source_date)));
}
function magicprofileID($profileID,$regDate='')
{
   /*
    if ($profileID % 7 == 0 and $profileID > IPY_PROFILEID_RESERVED and $regDate != "" and $regDate >= date("Y-m-d",strtotime('2017-03-25')) and $regDate <= date("Y-m-d",strtotime('2017-03-31')))

    {
        return 1;

    }
    else 
    {
        return 0;
    }
    */
    return 0;

}
function CheckforDelete($ID, $tablearray, $db)
{
    foreach ($tablearray as $key => $value) {
        $table = $value['table'];
        $field = $value['field'];
        $msg   = $value['msg'];
        $qry   = "select 1 from " . $table . " where " . $field . "=" . $ID . " limit 1";
        try {
            $data   = array();
            $result = $db->query($qry, $data);
            if (count($result) > 0) {
                $message = $msg;
                $sts     = 0;
                break;
            } else {
                $message = "Confirm To Remove";
                $sts     = 1;
            }
        }
        catch (Exception $E) {
            $message = $E->getMessage();
            $sts     = 0;
            break;
        }
    }
    $rtn = array(
        'sts' => $sts,
        'msg' => $message
    );
    return $rtn;
}
function CheckforRelationalDelete($ID, $tablearray, $db)
{
    foreach ($tablearray as $key => $value) {
        $field = '';
        $table = $value['table'];
        $msg   = $value['msg'];
        $i     = 1;
        foreach ($value['fields'] as $key => $val) {
            $id = $value['values'][$key];
            if ($i == 1) {
                $field .= $val . "=" . $id;
            } else {
                $field .= " and " . $val . "=" . $id;
            }
            $i += 1;
        }
        $qry = "select 1 from " . $table . " where " . $field . " limit 1";
        try {
            $data   = array();
            $result = $db->query($qry, $data);
            if (count($result) > 0) {
                $message = $msg;
                $sts     = 0;
                break;
            } else {
                $message = "Confirm To Remove";
                $sts     = 1;
            }
        }
        catch (Exception $E) {
            $message = $E->getMessage();
            $sts     = 0;
            break;
        }
    }
    $rtn = array(
        'sts' => $sts,
        'msg' => $message
    );
    return $rtn;
}
function getCountryCodefromIp()
{
    $ip      = get_client_ip();
    $url     = "http://api.ipinfodb.com/v3/ip-city/?key=ee596eb11609594154b1e824e145c813a85f8b5a2fa462fcc776a23ba4ac236f&ip=" . $ip . "&format=json";
    $ch      = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, TRUE);
}
function chooseAdvertisements($bannertype, $location, $locationlevel, $limitcount, $ip, $ctime, $db, $memid = 0)
{
    $cdate         = date("Y-m-d");
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
    $arraydata         = array(
        'cdate' => $cdate,
        'bannertype' => $bannertype,
        'location' => $location,
        'limitcount' => $limitcount,
        'ip' => $ip,
        'dispfraudtime' => $dispfraudtime,
        'memid' => $memid
    );
    //echo $sql;
    //print_r($arraydata);
    $advertisementsrow = $db->query($sql, $arraydata);
    return $advertisementsrow;
}


function getRenderAdvertisements($bannerstring, $location, $locationlevel, $ip, $ctime, $db, $memid = 0)
{
    $cdate         = date("Y-m-d");
    $dispfraudtime = $ctime - IPY_DISPLAY_IP_INTERVAL;
    switch ($locationlevel) {
        case "country":
            $advsql = "call getRenderAdvCountry(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        case "state":
            $advsql = "call getRenderAdvState(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        case "district":
            $advsql = "call getRenderAdvDistrict(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        case "parliament":
            $advsql = "call getRenderAdvParliament(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        case "assembly":
            $advsql = "call getRenderAdvAssembly(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        case "localbody":
            $advsql = "call getRenderAdvLocalbody(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        case "ward":
            $advsql = "call getRenderAdvWard(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        case "none":
            $advsql = "call getRenderAdvCountry(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
            break;
        default:
            $advsql = "call getRenderAdvCountry(:cdate, :ctime, :bannerstring, :location, :ip, :dispfraudtime,:memid)";
    }
    $arraydata = array(
        'cdate' => $cdate,
        'bannerstring' => $bannerstring,
        'location' => $location,
        'ip' => $ip,
        'dispfraudtime' => $dispfraudtime,
        'memid' => $memid,
        'ctime' => $ctime
    );
    
    try
    {
    $advertisementsrow = $db->query($advsql, $arraydata);
    }
    catch (Exception $E)
    {
        $advertisementsrow = array();
        echo $E -> getMessage();
    }
    return $advertisementsrow;
}




function renderAdvertisements($advid, $memid, $ip, $advtime, $advdate, $db)
{
    if (empty($memid) or ($memid == '') or ($memid == 'NULL') or ($memid == NULL)) {
        $sql       = "call renderAdvertisement(:advid, null, :ip, :advtime, :advdate)";
        $arraydata = array(
            'advid' => $advid,
            'ip' => $ip,
            'advtime' => $advtime,
            'advdate' => $advdate
        );
    } else {
        $sql       = "call renderAdvertisement(:advid, :memid, :ip, :advtime, :advdate)";
        $arraydata = array(
            'advid' => $advid,
            'memid' => $memid,
            'ip' => $ip,
            'advtime' => $advtime,
            'advdate' => $advdate
        );
    }
    //print_r($arraydata);
    $renderadvrow = $db->row($sql, $arraydata);
    return $renderadvrow;
}
function clickAdvertisement($actid, $memid, $ip, $db)
{
    $time     = time();
    $date     = date("Y-m-d");
    //$clickfraudtime = $time - IPY_CLICK_FRAUD_INTERVAL;
    //$clicksql = "call clickAdvertisement(:actid,:memid,:ip,:time, :date, :clickfraudtime)";
    //$arr = array('actid' =>$actid , 'memid' => $memid , 'ip' => $ip ,'time' =>$time, 'date'=>$date,'clickfraudtime' =>$clickfraudtime);
    $clicksql = "call clickAdvertisement(:actid,:memid,:ip,:time, :date)";
    $arr      = array(
        'actid' => $actid,
        'memid' => $memid,
        'ip' => $ip,
        'time' => $time,
        'date' => $date
    );
    $clickrow = $db->row($clicksql, $arr);
    return $clickrow;
}
function everything_in_tags($string, $tagname)
{
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    preg_match($pattern, $string, $matches);
    return $matches[1];
}
function getextension($url)
{
    $extension = substr(strrchr($url, "."), 1);
    return $extension;
}
function nth_strpos($str, $substr, $n, $stri = false)
{
    if ($stri) {
        $str = strtolower($str);
        $substr = strtolower($substr);
    }
    $ct = 0;
    $pos = 0;
    while (($pos = strpos($str, $substr, $pos)) !== false) {
        if (++$ct == $n) {
            return $pos;
        }
        $pos++;
    }
    return false;
}  

function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(curl_exec($ch)!==FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function is_image($path)
{
    $a = getimagesize($path);
    $image_type = $a[2];
     
    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG )))
    {
        return true;
    }
    return false;
}




function getAdditionalRoles($type)
{
	 
	$addiroles=unserialize(IPY_ADDITIONAL_ROLES);
	//print_r($addiroles);
	$rtn =array();
	//echo "type=".$type;
    if($type == 'Parliament'){
		if($addiroles[0] != '') $rtn[] =  $addiroles[0];
		if($addiroles[1] != '') $rtn[] =  $addiroles[1];
	}
	elseif($type == 'Assembly'){
		if($addiroles[2] != '')$rtn[] =  $addiroles[2];
		if($addiroles[3] != '')$rtn[] =  $addiroles[3];
	}
	elseif($type == 'Ward'){
		if($addiroles[4] != '')$rtn[] =  $addiroles[4];
		if($addiroles[5] != '')$rtn[] =  $addiroles[5];
		if($addiroles[6] != '')$rtn[] =  $addiroles[6];
		if($addiroles[7] != '')$rtn[] =  $addiroles[7];
	}
	return $rtn;
}

function validatefile($path,$allowed_mimetype){
    $mime=mime_content_type($path);
    //array('image/gif','image/jpeg','image/png','text/plain','text/vnd.ms-word','application/pdf','application/msword',' application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    if(in_array($mime , $allowed_mimetype)){
         return true;
    }
    return false;
}
function decodeurl($url){
	$datadecoded=encrypt_decrypt('decrypt',$url,HOS_URL_ENCWORD);
	$datas=explode("&", $datadecoded);
	$post=array();
	foreach($datas as $data){
		$strpos = strpos($data, '=');
		$key = substr($data, 0, strpos($data, '='));
		$str = substr($data, $strpos+strlen('='));
		$arr= array($key => $str);
		$post=$post+$arr;
	}
	return $post;
}

?>
