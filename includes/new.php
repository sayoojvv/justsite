<?PHP
date_default_timezone_set('Asia/Kolkata');
error_reporting(E_ALL);
ini_set('display_errors', 1); 
$mnumbers="919495793895,919847680023,919747152137,918891407123,918157974785,919495793895,919847680023,918891407123";
$mnumbers="919847680023";

$offset=array("5.5");
$message="KAP-789089";
echo sendMassMobileOTP($mnumbers,$message,true);



function sendMassMobileOTP($mobilenumbers,$message,$delay=false,$offsetarray=null)
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

				
				for($j=0;$j<sizeof($mobilearray);$j++)
				{
					
					
					
						if($currentoffset==$offsetarray[$j])
						{

							$loopmobile.=",".$mobilearray[$j];
							$curlexec=false;
							

						}
						else
						{
							
							
							




							

							if(($endam=="PM" and ($hours==10 or $hours==11 or $hours==9)) or ($endam=="AM" and $hours<10))
							{
								$schname=$mobilearray[$j].'-'.time();
								if($endam=="AM")
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=$cudateexp[0]." 10:00:00 AM";
								}
								else
								{
									$cudateexp=explode(" ",$cutime);
									$schtime=dateAdd($cudateexp[0],1)." 10:00:00 AM";
									

								}

								$schinter=date_diff(date_create($cutime),date_create($schtime));
								$schinterval=$schinter->format("%dd%hh%im%ss");


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

						if(($endam=="PM" and ($hours==10 or $hours==11 or $hours==9)) or ($endam=="AM" and $hours<10))
						{

							
							$schinter=date_diff(date_create($cutime),date_create($schtime));
							$schinterval=$schinter->format("%dd%hh%im%ss");

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
						


						if(($endam=="PM" and ($hours==10 or $hours==11 or $hours==9)) or ($endam=="AM" and $hours<9))
						{

							$schinter=date_diff(date_create($cutime),date_create($schtime));
							$schinterval=$schinter->format("%dd%hh%im%ss");
	
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
		}



	}

	
}
function everything_in_tags($string, $tagname)
{
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    preg_match($pattern, $string, $matches);
    return $matches[1];
}
function dateAdd($source_date,$interval_days)
{

	
	return date("Y-m-d",strtotime("+".$interval_days." day", strtotime($source_date)));
}
?>
