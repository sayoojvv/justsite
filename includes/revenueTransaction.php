<?php

//error_reporting(E_ALL);ini_set('display_errors', 1);

function revenueTransaction($advid,$advactid, $revFrom, $revDate, $revAmt, $revDesc, $payMode, $billno, $createdbyuser, $createdbytime , $db){
	
	$db->beginTransaction();
	
	$billsql = "select count(1) as billcount from advrevenue_master where `advrevenue_bill-recipt_no` = :billno";
	$billarr = array('billno'=> $billno);
	$billrow = $db->row($billsql,$billarr);
	$billcount = $billrow['billcount'];
	
	if($billcount > 0 ){
		$sts = 0;
		$msg = "Duplicate Bill no detected.";
	}
	else {
	
	
		$membercountsql = "select count(1) as membercount from advertisement_master where  advertisement_id = :advid and advertisement_refmemberID is not null ";
		$advarray = array('advid' => $advid);
		$membercountrow = $db->row($membercountsql, $advarray);
		$membercount = $membercountrow['membercount'];
		if($revFrom == 'company') {
			
			$depositrecordsql ="select count(1) as countDeposit  from advrevenue_master where advertisement_id = :advid and advrevenue_from ='company' and advrevenue_paymentStatus = 'Deposit'";
			
			$depositrow = $db->row($depositrecordsql, $advarray);
			$depositrecordcount = $depositrow['countDeposit'];
			
			if($depositrecordcount > 0 && $payMode == 'Deposit'){
				$sts = 0;
				$msg = "Already a Deposit Record found.";
			}
			else {
				
				
				$balanceAmountsql = "select amount as balamount ,advreserve_id  from advreservepayment_master where advreserve_used = 'no' and advertisement_id = :advid";
				
				
				$balanceAmountrow = $db->row($balanceAmountsql, $advarray);
				if(count($balanceAmountrow)>0 && $balanceAmountrow['balamount']>0){
					$balanceAmount = $balanceAmountrow['balamount'];
					$reserveid = $balanceAmountrow['advreserve_id'];
				}
								
			
				$countOwesql = "select count(1) as countOwe from advrevenue_master where advertisement_id = :advid and advrevenue_from ='company' and advrevenue_paymentStatus = 'Owe'";
				$countOwerow = $db->row($countOwesql, $advarray);
				$countOwe = $countOwerow['countOwe'];
				//echo "countowe = ".$countOwe;
				if($countOwe >0) {
					$owerowsql = "select advrevenue_amount,advrevenue_id ,advrevenue_idfrom,advrevenue_idto,advrevenue_idcount from advrevenue_master where advertisement_id = :advid and advrevenue_from ='company' and advrevenue_paymentStatus = 'Owe'";
					$owerow = $db->row($owerowsql, $advarray);
					$oweAmount = $owerow['advrevenue_amount'];
					$advRevenueid = $owerow['advrevenue_id'];
					$advActidfrom = $owerow['advrevenue_idfrom'];
					$advActidto = $owerow['advrevenue_idto'];
					$advActidCount = $owerow['advrevenue_idcount'];
					
									
					$payAmt = $revAmt + $balanceAmount;
					
					
					$oweRefrowsql = "select advrevenue_amount,advrevenue_id ,advrevenue_idfrom,advrevenue_idto,advrevenue_idcount from advrevenue_master where advertisement_id = :advid and advrevenue_from ='ipy' and advrevenue_paymentStatus = 'Owe'  and TRIM(`advrevenue_ref_bill-recipt_no`) = ''";
					$oweRefrow = $db->row($oweRefrowsql, $advarray);
					$RefoweAmount = $oweRefrow['advrevenue_amount'];
					$Refoweid = $oweRefrow['advrevenue_id'];
					
					if($oweAmount == $payAmt) {
												
						try
						{
							
							$clearOwesql = "update advrevenue_master set advrevenue_paymentStatus = :status , advrevenue_date = :revDate, advrevenue_desc = :revDesc, `advrevenue_bill-recipt_no` = :billno , created_by = :createdbyuser , created_on = :createdbytime   where advrevenue_id = :advRevenueid ";
							$clearOwearr = array('status'=>'Credit','revDate' =>$revDate , 'revDesc'=> $revDesc , 'billno' => $billno , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advRevenueid' => $advRevenueid);
							
							$clearOwe = $db->query($clearOwesql, $clearOwearr );
							
							if($membercount>0) {
								$updateRefOwesql = "update advrevenue_master set advrevenue_date = :revDate, `advrevenue_ref_bill-recipt_no` = :billno , created_by = :createdbyuser , created_on = :createdbytime   where advertisement_id = :advid and advrevenue_from ='ipy' and advrevenue_paymentStatus = 'Owe' and TRIM(`advrevenue_ref_bill-recipt_no`) = ''";
								$updateRefOwearr = array('revDate' =>$revDate , 'billno' => $billno , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid);
								$updateRefOwe = $db->query($updateRefOwesql, $updateRefOwearr );
							}
							
							$insertpaymentsql = "insert into `advpayment_master`(`advertisement_id`, `advpayment_date`, `advpayment_from`, `advpayment_amount`, `advpayment_bill-recipt_no`, `advpayment_desc`,`created_by`, `created_on`) values(:advid ,:revDate ,:revFrom ,:revAmt ,:billno , :revDesc, :createdbyuser ,:createdbytime)";
							$insertpaymentarr =array('revDate' =>$revDate , 'revFrom'=> $revFrom , 'revAmt'=> $revAmt , 'billno' => $billno , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid ,'revDesc'=>$revDesc);
							$insertpayment = $db->query($insertpaymentsql, $insertpaymentarr );
							
							
							
							$sts = 1;
							$msg = 'Current Owe from company cleared';
						}
						catch (Exception $E)
						{
							$msg=$E -> getMessage();
							$sts= 0;
						}
						
					}
					elseif ($oweAmount > $payAmt) {
						
						$advactionsql = "SELECT advaction_costamount,advaction_id,advaction_refamount  from advaction_master where advertisement_id = :advid and advaction_id between :advActidfrom and :advActidto order by advaction_id";
						$advactionarr = array('advActidfrom'=> $advActidfrom, 'advActidto' => $advActidto , 'advid' => $advid); 
						$advactionrow = $db->query($advactionsql, $advactionarr );
						
						$partamt = 0;
						$partCount = 0;
						$refamount = 0;
						foreach ($advactionrow as $advaction){
							$partamt = $partamt+ $advaction['advaction_costamount'];
							
							if($partamt > $payAmt) {
								$startrecord = $advaction['advaction_id'];
								
								if($advaction['advaction_costamount'] > $payAmt && $partCount==0){
									$sts = 0;
									$msg = "Your Payment amount + Credit Balance must be greater than or equal to ".$advaction['advaction_costamount']  ;
								}
								break;
							}
							$endrecord = $advaction['advaction_id'];
							$partCount = $partCount +1;	
							$splitAmt = $partamt ;
							$refamount = $refamount + $advaction['advaction_refamount'];
							$sts = 1;
						}
						
						if($sts > 0 ){
							$balamt = $payAmt - $splitAmt ;
							$newcount = $advActidCount - $partCount;
							try
							{
								
								$updateDepsql =" update advrevenue_master set advrevenue_paymentStatus = 'Credit',advrevenue_date = :revDate, advrevenue_desc = :revDesc, `advrevenue_bill-recipt_no` = :billno ,advrevenue_amount = :splitAmt, created_by = :createdbyuser , created_on = :createdbytime, advrevenue_idto = :endrecord ,advrevenue_idcount = :partCount  where advrevenue_id = :advRevenueid";
								$updateDeparr = array('revDate' =>$revDate ,'revDesc'=>$revDesc, 'splitAmt'=> $splitAmt , 'billno' => $billno , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advRevenueid' => $advRevenueid , 'endrecord'=> $endrecord, 'partCount'=>$partCount);
								
								$updateDeprow = $db->query($updateDepsql, $updateDeparr );
								
								
								$description = 'Company owe to ipy';
								
								$restamt = $oweAmount - $splitAmt ;
								$remainingamt = $restamt ;
								$newOwerecordsql = "insert into `advrevenue_master` ( `advertisement_id`, `advrevenue_from`, `advrevenue_date`, `advrevenue_idfrom`, `advrevenue_idto`, `advrevenue_idcount`, `advrevenue_amount`, `advrevenue_desc`, `advrevenue_paymentStatus`, `created_by`, `created_on`) values(:advid, :revFrom, :revDate, :startrecord ,:advActidto ,:newcount ,:restamt ,:description ,'Owe' ,:createdbyuser,:createdbytime)";
								$newOwerecordarr = array('revDate' =>$revDate ,'description'=>$description, 'restamt'=> $restamt , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid , 'startrecord'=> $startrecord, 'newcount'=>$newcount , 'advActidto' => $advActidto ,'revFrom'=>$revFrom);
								
								$newOwerecordrow = $db->query($newOwerecordsql, $newOwerecordarr );
								
								
								if($membercount>0) {
									$updateRefOwesql = "update advrevenue_master set advrevenue_date = :revDate, `advrevenue_ref_bill-recipt_no` = :billno , created_by = :createdbyuser , created_on = :createdbytime ,advrevenue_amount = :refamount   where advertisement_id = :advid and advrevenue_from ='ipy' and advrevenue_paymentStatus = 'Owe' and TRIM(`advrevenue_ref_bill-recipt_no`) = ''";
									$updateRefOwearr = array('revDate' =>$revDate , 'billno' => $billno ,'refamount'=> $refamount ,  'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid);
									$updateRefOwe = $db->query($updateRefOwesql, $updateRefOwearr );
									
									$description = 'ipy owe to Referrer';
									$restamt = $RefoweAmount - $refamount ;
									
									$newRefOwesql = "insert into `advrevenue_master` ( `advertisement_id`, `advrevenue_from`, `advrevenue_date`, `advrevenue_idfrom`, `advrevenue_idto`, `advrevenue_idcount`, `advrevenue_amount`, `advrevenue_desc`, `advrevenue_paymentStatus`, `created_by`, `created_on`) values(:advid, :revFrom, :revDate, :startrecord ,:advActidto ,:newcount ,:restamt ,:description ,'Owe' ,:createdbyuser,:createdbytime)";
									$newRefOwearr = array('revDate' =>$revDate ,'description'=>$description, 'restamt'=> $restamt , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid , 'startrecord'=> $startrecord, 'newcount'=>$newcount , 'advActidto' => $advActidto ,'revFrom'=>'ipy');
									$newRefOwerow = $db->query($newRefOwesql, $newRefOwearr );
									
								}
								
								$insbalancesql = "insert into advreservepayment_master(advertisement_id, amount , `advpayment_bill-recipt_no` ) values(:advid,  :balamt, :billno)";
								$insbalancearr = array('advid' => $advid ,'balamt'=> $balamt , 'billno'=>$billno);
								$insbalancerow = $db->query($insbalancesql, $insbalancearr );					
								
								$insAdvsql = "insert into `advpayment_master`(`advertisement_id`, `advpayment_date`, `advpayment_from`, `advpayment_amount`, `advpayment_bill-recipt_no`,`advpayment_desc`, `created_by`, `created_on`) values( :advid , :revDate , :revFrom , :revAmt , :billno , :revDesc, :createdbyuser , :createdbytime )";
								$insAdvarr = array('revDate' =>$revDate , 'revAmt'=> $revAmt , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid ,'revFrom'=>$revFrom ,'billno'=>$billno,'revDesc'=>$revDesc);
								$insAdvrow = $db->query($insAdvsql, $insAdvarr );
								
								
								 
								$sts = 1;
								$msg = 'Partial Transaction of '.$splitAmt.' only.New Owe Record created with amount '.$remainingamt;
								
							
							}
							catch (Exception $E)
							{
								$msg=$E -> getMessage();
								$sts= 0;
							}
						}
						
						
						
					}
					else {
						
						
						try
						{
						
							
							$updateOwesql = "update advrevenue_master set advrevenue_paymentStatus = 'Deposit',advrevenue_date = :revDate, advrevenue_desc = :revDesc, `advrevenue_bill-recipt_no` = :billno ,advrevenue_amount = :payAmt, created_by = :createdbyuser , created_on = :createdbytime  where advrevenue_id = :advRevenueid";
							
							$updateOwearr = array('revDate' =>$revDate , 'revDesc'=> $revDesc , 'billno' => $billno , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advRevenueid' => $advRevenueid ,'payAmt'=> $payAmt);
							$updateOwerow = $db->query($updateOwesql, $updateOwearr);
							
							
							
							$insAdvsql = "insert into `advpayment_master`(`advertisement_id`, `advpayment_date`, `advpayment_from`, `advpayment_amount`, `advpayment_bill-recipt_no`,`advpayment_desc`, `created_by`, `created_on`) values( :advid , :revDate , :revFrom , :revAmt , :billno , :revDesc, :createdbyuser , :createdbytime)";
							$insAdvarr = array('revDate' =>$revDate , 'revAmt'=> $revAmt , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid ,'revFrom'=>$revFrom ,'billno'=>$billno,'revDesc' =>$revDesc);
							$insAdvrow = $db->query($insAdvsql, $insAdvarr );
							
							
							$sts = 1 ;
							//$msg = 'Transacted only '.$oweAmount.' out of '.$revAmt.' and rest Deposited';
							$msg = 'Deposited';
						
						}
						catch (Exception $E)
						{
							$msg=$E -> getMessage();
							$sts= 0;
							$rtn=array('sts'=>$sts,'msg'=>$msg);
							echo json_encode($rtn);
							exit;
						}
						
						
					}
									
				}
				else {
				
					$insertDepsql = "insert into `advrevenue_master` ( `advertisement_id`, `advrevenue_from`, `advrevenue_date`, `advrevenue_idfrom`, `advrevenue_idto`, `advrevenue_idcount`, `advrevenue_amount`, `advrevenue_desc`, `advrevenue_paymentStatus`,`advrevenue_bill-recipt_no`, `created_by`, `created_on`) values(:advid, :revFrom, :revDate, NULL ,NULL ,0 ,:revAmt , :revDesc ,'Deposit' , :billno, :createdbyuser, :createdbytime)";
					$insertDeparr = array('revDate' =>$revDate , 'revDesc'=> $revDesc , 'billno' => $billno , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid ,'revAmt'=> $revAmt,'revFrom' =>$revFrom);
					$insertDeprow = $db->query($insertDepsql, $insertDeparr);
					
					$insAdvsql = "insert into `advpayment_master`(`advertisement_id`, `advpayment_date`, `advpayment_from`, `advpayment_amount`, `advpayment_bill-recipt_no`, `advpayment_desc`, `created_by`, `created_on`) values( :advid, :revDate, :revFrom, :revAmt, :billno, :revDesc , :createdbyuser, :createdbytime)";
					$insAdvarr = array('revDate' =>$revDate , 'revAmt'=> $revAmt , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid ,'revFrom'=>$revFrom ,'billno'=>$billno, 'revDesc'=> $revDesc);
					//$insAdvrow = $db->query($insAdvsql, $insAdvarr );
					 try
						{
							$insAdvrow = $db->query($insAdvsql, $insAdvarr );       
							if($insAdvrow > 0){
								$msg = "INSERTED SUCCESSFULLY";
								$sts =1;
							}
							else{
								$msg = "Nothing to Insert!";
								$sts =1;
							}
						}
						catch (Exception $E)
						{
							$msg=$E -> getMessage();
							$sts= 0;
							$rtn=array('sts'=>$sts,'msg'=>$msg);
							echo json_encode($rtn);
							exit;
						}
					$sts = 1;
					$msg = 'Deposited ';
				}
				
				if($balanceAmount > 0  && $sts>0 ) {
					$updatebalancesql = "update advreservepayment_master set `advreserve_usedbillno` = :billno , advreserve_used = 'yes'  where advertisement_id = :advid and advreserve_used ='no' and advreserve_id = :reserveid";
					$updatebalancearr = array( 'billno' => $billno , 'advid' => $advid ,'reserveid'=>$reserveid );
					$updatebalance = $db->query($updatebalancesql, $updatebalancearr );
				}
					
				
			}
			
			
		}
		elseif($revFrom == 'ipy') {
			//$sts = 1 ;
			//$msg ='Current Owe cleared';
			
			$countOwesql = "select count(1) as countOwe from advrevenue_master where advertisement_id = :advid and advrevenue_from ='ipy' and advrevenue_paymentStatus = 'Owe' and `advrevenue_ref_bill-recipt_no` != '' and advrevenue_id = :advactid";
			$advarray['advactid']= $advactid;
			$countOwerow = $db->row($countOwesql, $advarray);
			$countOwe = $countOwerow['countOwe'];
			if($countOwe >0) {
				
				$RefoweRecordsql = "select advrevenue_amount,advrevenue_id ,advrevenue_idfrom,advrevenue_idto,advrevenue_idcount from advrevenue_master where advertisement_id = :advid and advrevenue_from ='ipy' and advrevenue_paymentStatus = 'Owe' and `advrevenue_ref_bill-recipt_no` != '' and advrevenue_id = :advactid";
				$RefoweRecordrow = $db->row($RefoweRecordsql, $advarray);
				$oweAmount = $RefoweRecordrow['advrevenue_amount'];
				$advRevenueid = $RefoweRecordrow['advrevenue_id'];
				$advActidfrom = $RefoweRecordrow['advrevenue_idfrom'];
				$advActidto = $RefoweRecordrow['advrevenue_idto'];
				$advActidCount = $RefoweRecordrow['advrevenue_idcount'];
				
				if($oweAmount == $revAmt) {
					
					$updateRefsql = "update advrevenue_master set advrevenue_paymentStatus = 'Credit',advrevenue_date = :revDate, advrevenue_desc = :revDesc, `advrevenue_bill-recipt_no` = :billno, created_by = :createdbyuser , created_on = :createdbytime  where advrevenue_id = :advRevenueid";
					$updateRefarr =array('revDate'=> $revDate , 'revDesc' => $revDesc , 'billno' => $billno , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advRevenueid' => $advRevenueid  );
					$updateRefrow = $db->query($updateRefsql, $updateRefarr);
					
					$insAdvsql = "insert into `advpayment_master`(`advertisement_id`, `advpayment_date`, `advpayment_from`, `advpayment_amount`, `advpayment_bill-recipt_no`, `advpayment_desc`, `created_by`, `created_on`) values(:advid , :revDate , :revFrom , :revAmt , :billno , :revDesc , :createdbyuser , :createdbytime)";
					$insAdvarr = array('revDate' =>$revDate , 'revAmt'=> $revAmt , 'createdbyuser' => $createdbyuser,  'createdbytime' => $createdbytime, 'advid' => $advid ,'revFrom'=>$revFrom ,'billno'=>$billno, 'revDesc' =>$revDesc);
					$insAdvrow = $db->query($insAdvsql, $insAdvarr );
					
					$sts = 1 ;
					$msg ='Current Owe cleared';
					
				}
				
			}
			
			
		}		
	}
	$db->commit();
return array('sts'=>$sts ,'msg' =>$msg );
	
}
