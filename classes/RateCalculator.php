<?php
class RateCalculator
{
    function __construct()
    {
    }
    //-------------------------
    //---------- To plot line chart of a shop
    public function getRateChartDataLine($fdt, $cid, $typ, $db, $rmviss, $displayhide = 'yes')
    {
        $output_format = 'Y-m-d';
        if ($typ == '15d') {
            $step  = '-5 day';
            $count = 3;
        } //$typ == '15d'
        if ($typ == '1m') {
            $step  = '-10 day';
            $count = 3;
        } //$typ == '1m'
        if ($typ == '2m') {
            $step  = '-15 day';
            $count = 4;
        } //$typ == '1m'
        if ($typ == '3m') {
            $step  = '-1 month';
            $count = 3;
        } //$typ == '3m'
        if ($typ == '6m') {
            $step  = '-2 month';
            $count = 3;
        } //$typ == '6m'
        if ($typ == '1y') {
            $step  = '-2 month';
            $count = 6;
        } //$typ == '1y'
        $dates     = $this->date_range($fdt, $step, $output_format, $count);
        $rev_dates = array_reverse($dates);
        $i         = 0;
        foreach ($rev_dates as $key => $value) {
            foreach ($cid as $val => $ele) {
                $rate                     = $this->getOverallUserRating($value['interval_end'], $ele, $db, $rmviss, $displayhide);
                //print_r($rate);
                $cname                    = $rate['cname'];
                $data[$cname][$i]['rate'] = $rate['rate'];
                $data[$cname][$i]['date'] = $value['interval_end'];
                if ($typ == '6m' || $typ == '1y') {
                    $data[$cname][$i]['dispdate'] = date("M Y", strtotime($value['interval_end']));
                } else {
                    $data[$cname][$i]['dispdate'] = date(IPY_DATE_DISPLAY_FORMAT, strtotime($value['interval_end']));
                }
                $data[$cname][$i]['peoplerate'] = $rate['peoplerating'];
                $data[$cname][$i]['cid']        = $cid;
                $data[$cname][$i]['cname']      = $rate['cname'];
                $data[$cname][$i]['xaxis']      = "RATING";
                $i += 1;
            }
        }
        return $data;
    }
    //----------to calculate the rate of individual issues
    public function getComplaintRating($idt, $sdt, $fdt, $dln, $index)
    {
        if ($sdt == 'NULL' || $sdt == NULL || ($sdt > $fdt)) {
            $sdt = $fdt;
        } //$sdt == 'NULL' || $sdt == NULL || ( $sdt > $fdt )
        $d1     = $this->dateDiff($sdt, $idt);
        $d2     = $this->dateDiff($dln, $idt);
        $rating = $index - (($index) * ($d1 / ($d1 + $d2)));
        return $rating;
    }
    //---------- To find the overall rating of all complaints related to that shop within that particular date
    public function getOverallUserRating($fdt, $cid, $db, $rmviss, $displayhide)
    {
        //------------declaration part
        $weighted_solved   = 1;
        $weighted_unsolved = 1;
        //------------
        $totrate           = 0;
        $totpeoplerate     = 0;
        $cnt               = 0;
        $cntpeople         = 0;
        $ratearray         = $this->getAllComplaints($fdt, $cid, $db, $rmviss);
        //return $ratearray;
        foreach ($ratearray as $key => $value) {
            if ($value['na'] == 1 || ($value['hide'] == 'yes' && $displayhide == 'yes') || (isset($value['notexceeded']) && $value['notexceeded'])) {
                $finrate['cname']        = $value['name'];
                $finrate['constituency'] = $value['constituency'];
                $finrate['peoplerating'] = 0.1;
                $finrate['rate']         = 0.1;
                continue;
            } else {
                $compexist               = 1;
                $finrate['cname']        = $value['name'];
                $finrate['constituency'] = $value['constituency'];
                $peoplerating            = $value['peoplerating'];
                if ($value['sts'] == "Open") {
                    $rating = $value['rating'] * $weighted_unsolved;
                } else {
                    $rating = $value['rating'] * $weighted_solved;
                }
                $totrate = $totrate + $rating;
                if ($peoplerating > 0) {
                    $totpeoplerate = $totpeoplerate + $peoplerating;
                    $cntpeople     = $cntpeople + 1;
                }
                $cnt = $cnt + 1;
            }
        }
        if ($compexist == 1) {
            $finrate['rate'] = $totrate / $cnt;
            if ($cntpeople == 0) {
                $finrate['peoplerating'] = 0.1;
            } else {
                $finrate['peoplerating'] = $totpeoplerate / $cntpeople;
            }
        }
        return $finrate;
    }
    //-------------to get an array with rating and status of all complaints related to that user within that particular date
    public function getAllComplaints($fdt, $cid, $db, $rmviss=null)
    {
        //------------declaration part
        $points   = 10;
        $defpoint = 0.1;
        //-----------------------------
        $qrydl    = "select CONCAT(settings_complaintResponseDeadline,' DAY') as due from settings_master limit 1";
        $datadl   = array();
        $querydl  = $db->row($qrydl, $datadl);
        $due      = $querydl['due'];
        $qry      = "select register_id as id,register_name as nam,(case when register_shopAS='Parliament' then (select parliament_name from parliament_master where parliament_id=register_shop_parliamentId) when register_shopAS='Assembly' then (select assembly_name from assembly_master where assembly_id=register_shop_assemblyId) else (select ward_name from ward_master where ward_id=register_shop_wardId) END) as constituency,(case when register_shopAS='Parliament' then register_shop_parliamentId when register_shopAS='Assembly' then register_shop_assemblyId else register_shop_wardId END) as loc,register_shopAS from register_master where register_id='" . $cid . "'";
        $data     = array();
        try {
            $query = $db->query($qry, $data);
            $msg   = array(
                "WORKING FINE"
            );
        }
        catch (Exception $E) {
            $msg = $E->getMessage();
            $sts = 0;
        }
        //return $query;
        $rmtext = '';
        $count  = 0;
        foreach ($rmviss as $key => $rmv) {
            if ($count == 0) {
                $rmtext .= " complaints_id not in ( " . $rmv . " ";
            } else {
                $rmtext .= " , " . $rmv . "";
            }
            $count = $count + 1;
        }
        if (!empty($rmviss)) {
            $rmtext .= ") and ";
        }
        foreach ($query as $key => $aquery) {
            $cid          = $aquery['id'];
            $cname        = $aquery['nam'];
            $locid        = $aquery['loc'];
            $loctyp       = $aquery['register_shopAS'];
            $constituency = $aquery['constituency'];
            if ($loctyp == 'Parliament') {
                $issuesqry = "select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master c,register_master r  where " . $rmtext . " r.register_id='" . $cid . "' and c.complaints_responsibleMember=r.register_id  and r.register_type='shop'  and r.register_shopApproved='Yes' and r.register_shopAS='Parliament' and c.parliament_id=:locid and c.assembly_id is null and c.parliament_id=r.register_shop_parliamentId and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'";
            } else if ($loctyp == 'Assembly') {
                $issuesqry = "select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master c,register_master r  where " . $rmtext . " r.register_id='" . $cid . "' and c.complaints_responsibleMember=r.register_id  and r.register_type='shop'  and r.register_shopApproved='Yes' and r.register_shopAS='Assembly' and c.assembly_id=:locid and c.localbody_id is null and c.assembly_id=r.register_shop_assemblyId and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'";
            } else if ($loctyp == 'Ward') {
                $issuesqry = "select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master c,register_master r  where " . $rmtext . " r.register_id='" . $cid . "' and c.complaints_responsibleMember=r.register_id  and r.register_type='shop'  and r.register_shopApproved='Yes' and r.register_shopAS='Ward' and c.ward_id=:locid and c.ward_id=r.register_shop_wardId and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'";
            }
            $dataset = array(
                'locid' => $locid
            );
            //echo $issuesqry;
            //return $issuesqry;
            try {
                $issues = $db->query($issuesqry, $dataset);
            }
            catch (Exception $E) {
                $msg = $E->getMessage();
                $sts = 0;
            }
            //return $issues;
            if (count($issues) == 0) {
                $rate[0]['name']         = $cname;
                $rate[0]['constituency'] = $constituency;
                $rate[0]['rating']       = $defpoint;
                $rate[0]['peoplerating'] = $defpoint;
                $rate[0]['sts']          = 'UNDEFINED';
            }
            $i   = 0;
            $iss = array();
            foreach ($issues as $key => $issue) {
                $iss[$i]['id']           = $issue['id'];
                $iss[$i]['code']         = encrypt_decrypt('decrypt', $issue['code'], IPY_PASSWORD_ENCWORD);
                $iss[$i]['dte']          = $issue['dte'];
                $iss[$i]['fdt']          = $fdt;
                $iss[$i]['solveddte']    = $issue['sdte'];
                $iss[$i]['dln']          = $issue['dln'];
                $iss[$i]['sts']          = $issue['sts'];
                $iss[$i]['hide']         = $issue['hide'];
                $iss[$i]['na']           = $issue['na'];
                $iss[$i]['peoplerating'] = $issue['peoplerating'];
                //echo  "CODE=>(".$iss[$i]['code'].") dln=".$issue['dln']."and fdt=".$fdt." and solved date=".$issue['sdte'];
                if ($issue['na'] == '1') {
                    $iss[$i]['sdte']  = '';
                    $iss[$i]['sts']   = 'NA';
                    $iss[$i]['class'] = 'glyphicon glyphicon-asterisk yellow';
                    $iss[$i]['order'] = 3;
                } else if (!empty($issue['sdte']) && ($issue['sdte'] <= $fdt)) {
                    $iss[$i]['name']  = $cname;
                    $iss[$i]['sdte']  = $issue['sdte'];
                    $iss[$i]['class'] = 'glyphicon glyphicon-asterisk green';
                    $iss[$i]['order'] = 1;
                } else {
                        if($issue['dln']>$fdt){
                            $iss[$i]['sdte']  = '';
                            $iss[$i]['sts']   = 'Open';
                            $iss[$i]['class'] = 'glyphicon glyphicon-asterisk red';
                            $iss[$i]['order'] = 2;
                            $iss[$i]['notexceeded'] = 1;
                        }
                        else{
                            $iss[$i]['sdte']  = '';
                            $iss[$i]['sts']   = 'Open';
                            $iss[$i]['class'] = 'glyphicon glyphicon-asterisk red';
                            $iss[$i]['order'] = 2;
                        }
                }
                $i = $i + 1;
            }
        }
        $i = 0; //$rate=array();
        foreach ($iss as $key => $value) {
            $rating                   = $this->getComplaintRating($value['dte'], $value['sdte'], $fdt, $value['dln'], $points);
            $rate[$i]['id']           = $value['id'];
            $rate[$i]['code']         = $value['code'];
            $rate[$i]['date']         = date(IPY_DATE_DISPLAY_FORMAT, strtotime($value['dte']));
            $rate[$i]['dte']          = $value['dte'];
            $rate[$i]['hide']         = $value['hide'];
            $rate[$i]['na']           = $value['na'];
            $rate[$i]['class']        = $value['class'];
            $rate[$i]['peoplerating'] = $value['peoplerating'];
            $rate[$i]['name']         = $cname;
            $rate[$i]['constituency'] = $constituency;
            if((isset($value['notexceeded']) && $value['notexceeded']==1) || ( isset($value['na']) && $value['na']==1)){
                $rate[$i]['notexceeded']          =1;
            }else{
                $rate[$i]['rating']       = round($rating);//new change
            }
            $rate[$i]['sts']          = $value['sts'];
            $i                        = $i + 1;
        }
        //print_r($rate);
        return $rate;
    }
    //----to get an array with rating and status of all complaints related to that Location within that particular date-------//
    //----#############################################################################################################-------//
    public function getAllComplaintsLocation($fdt, $locdata, $db, $rmviss=null)
    {
        //------------declaration part
        $points   = 10;
        $due      = '15 DAY';
        $defpoint = 0.1;
        //-----------------------------
        $rmtext   = '';
        $count    = 0;
        foreach ($rmviss as $key => $rmv) {
            if ($count == 0) {
                $rmtext .= " complaints_id not in ( " . $rmv . " ";
            } else {
                $rmtext .= " , " . $rmv . "";
            }
            $count = $count + 1;
        }
        if (!empty($rmviss)) {
            $rmtext .= ") and";
        }
        $locid  = $locdata['locid'];
        $locnam = $locdata['locname'];
        $loctyp = $locdata['loctyp'];
        if ($loctyp == 'Parliament') {
            $issuesqry = "select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master c,register_master r  where " . $rmtext . "  c.complaints_responsibleMember=r.register_id  and r.register_type='shop'  and r.register_shopApproved='Yes' and r.register_shopAS='Parliament' and c.parliament_id=:locid and c.assembly_id is null and c.parliament_id=r.register_shop_parliamentId and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'     union      select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master  where " . $rmtext . "  complaints_responsibleMember is null and parliament_id=:locid1 and assembly_id is null and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'";
        } else if ($loctyp == 'Assembly') {
            $issuesqry = "select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master c,register_master r  where " . $rmtext . "  c.complaints_responsibleMember=r.register_id  and r.register_type='shop'  and r.register_shopApproved='Yes' and r.register_shopAS='Assembly' and c.assembly_id=:locid and c.localbody_id is null and c.assembly_id=r.register_shop_assemblyId and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'     union      select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master  where " . $rmtext . "  complaints_responsibleMember is null and assembly_id=:locid1 and ward_id is null  and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'";
        } else if ($loctyp == 'Ward') {
            $issuesqry = "select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master c,register_master r  where " . $rmtext . "  c.complaints_responsibleMember=r.register_id  and r.register_type='shop'  and r.register_shopApproved='Yes' and r.register_shopAS='Ward' and c.ward_id=:locid and c.ward_id=r.register_shop_wardId and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'     union      select complaints_id as id,complaints_code as code,DATE( FROM_UNIXTIME( complaints_postedon ) ) as dte , complaints_status as sts,(select rating_score from rating_master where complaint_id= complaints_id and rating_active='yes') as peoplerating,(case when complaints_hide='no' then 'no' else 'yes' end) as hide, DATE_ADD(DATE( FROM_UNIXTIME( complaints_postedon ) ),INTERVAL " . $due . ") as dln,complaints_settleDate as sdte, complaints_status as sts,(case when complaints_status='NA' then 1 else 0 end ) as na from complaints_master  where " . $rmtext . "  complaints_responsibleMember is null and ward_id=:locid1 and DATE( FROM_UNIXTIME( complaints_postedon ) )<='" . $fdt . "'";
        }
        //return $issuesqry;
        $dataset = array(
            'locid' => $locid,
            'locid1' => $locid
        );
        try {
            $issues = $db->query($issuesqry, $dataset);
        }
        catch (Exception $E) {
            $msg = $E->getMessage();
            $sts = 0;
        }
        if (count($issues) == 0) {
            $rate[0]['constituency'] = $locnam;
            $rate[0]['rating']       = $defpoint;
            $rate[0]['peoplerating'] = $defpoint;
            $rate[0]['sts']          = 'UNDEFINED';
        }
        $i   = 0;
        $iss = array();
        foreach ($issues as $key => $issue) {
            $iss[$i]['id']           = $issue['id'];
            $iss[$i]['code']         = encrypt_decrypt('decrypt', $issue['code'], IPY_PASSWORD_ENCWORD);
            $iss[$i]['dte']          = $issue['dte'];
            $iss[$i]['fdt']          = $fdt;
            $iss[$i]['solveddte']    = $issue['sdte'];
            $iss[$i]['dln']          = $issue['dln'];
            $iss[$i]['sts']          = $issue['sts'];
            $iss[$i]['hide']         = $issue['hide'];
            $iss[$i]['na']           = $issue['na'];
            $iss[$i]['peoplerating'] = $issue['peoplerating'];
            if ($issue['na'] == '1') {
                $iss[$i]['sdte']  = '';
                $iss[$i]['sts']   = 'NA';
                $iss[$i]['class'] = 'glyphicon glyphicon-asterisk yellow';
                $iss[$i]['order'] = 3;
            } else if (!empty($issue['sdte']) && ($issue['sdte'] <= $fdt)) {
                $iss[$i]['name']  = $locnam;
                $iss[$i]['sdte']  = $issue['sdte'];
                $iss[$i]['class'] = 'glyphicon glyphicon-asterisk green';
                $iss[$i]['order'] = 1;
            } else {
                        if($issue['dln']>$fdt){
                            $iss[$i]['sdte']  = '';
                            $iss[$i]['sts']   = 'Open';
                            $iss[$i]['class'] = 'glyphicon glyphicon-asterisk red';
                            $iss[$i]['order'] = 2;
                            $iss[$i]['notexceeded'] = 1;
                        }
                        else{
                            $iss[$i]['sdte']  = '';
                            $iss[$i]['sts']   = 'Open';
                            $iss[$i]['class'] = 'glyphicon glyphicon-asterisk red';
                            $iss[$i]['order'] = 2;
                        }
            }
            $i = $i + 1;
        }
        //return $iss;//sayyy
        $i = 0;
        foreach ($iss as $key => $value) {
            $rating                   = $this->getComplaintRating($value['dte'], $value['sdte'], $fdt, $value['dln'], $points);
            $rate[$i]['id']           = $value['id'];
            $rate[$i]['code']         = $value['code'];
            $rate[$i]['date']         = date(IPY_DATE_DISPLAY_FORMAT, strtotime($value['dte']));
            $rate[$i]['dte']          = $value['dte'];
            $rate[$i]['hide']         = $value['hide'];
            $rate[$i]['na']           = $value['na'];
            $rate[$i]['class']        = $value['class'];
            $rate[$i]['peoplerating'] = $value['peoplerating'];
            $rate[$i]['name']         = $locnam;
            $rate[$i]['constituency'] = $locnam;
            if((isset($value['notexceeded']) && $value['notexceeded']==1) || ( isset($value['na']) && $value['na']==1)){
                $rate[$i]['notexceeded']          =1;
            }else{
                $rate[$i]['rating']       = round($rating);//new change
            }
            $rate[$i]['sts']          = $value['sts'];
            $i                        = $i + 1;
        }
        return $rate;
    }
    //---------- To find the overall rating of all complaints related to that location within that particular date------------//
    //----------##################################################################################################------------//
    public function getOverallUserRatingLocation($fdt, $locdata, $db, $rmviss, $displayhide)
    {
        //------------declaration part
        $weighted_solved   = 1;
        $weighted_unsolved = 1;
        //------------
        $totrate           = 0;
        $totpeoplerate     = 0;
        $cnt               = 0;
        $cntpeople         = 0;
        //return $locdata;exit;
        $ratearray         = $this->getAllComplaintsLocation($fdt, $locdata, $db, $rmviss);
        //return $ratearray;exit;
        foreach ($ratearray as $key => $value) {
            if ($value['na'] == 1 || $value['hide'] == 'yes'  || (isset($value['notexceeded']) && $value['notexceeded'])) {
                $finrate['cname']        = $value['name'];
                $finrate['constituency'] = $value['constituency'];
                $finrate['peoplerating'] = 0.1;
                $finrate['rate']         = 0.1;
                continue;
            } else {
                $compexist               = 1;
                $finrate['locname']      = $value['name'];
                $finrate['constituency'] = $value['constituency'];
                $peoplerating            = $value['peoplerating'];
                if ($value['sts'] == "Open") {
                    $rating = $value['rating'] * $weighted_unsolved;
                } else {
                    $rating = $value['rating'] * $weighted_solved;
                }
                $totrate = $totrate + $rating;
                if ($peoplerating > 0) {
                    $totpeoplerate = $totpeoplerate + $peoplerating;
                    $cntpeople     = $cntpeople + 1;
                }
                $cnt = $cnt + 1;
            }
        }
        if ($compexist == 1) {
            $finrate['rate'] = $totrate / $cnt;
            if ($cntpeople == 0) {
                $finrate['peoplerating'] = 0.1;
            } else {
                $finrate['peoplerating'] = $totpeoplerate / $cntpeople;
            }
        }
        return $finrate;
    }
    //---------- To plot line chart of various levels---------//
    //----------#####################################---------//
    public function getRateChartDataLevelLine($fdt, $locdata, $typ, $db, $rmviss, $displayhide = 'yes')
    {
        $output_format = 'Y-m-d';
        if ($typ == '15d') {
            $step  = '-5 day';
            $count = 3;
        } //$typ == '15d'
        if ($typ == '1m') {
            $step  = '-10 day';
            $count = 3;
        } //$typ == '1m'
        if ($typ == '2m') {
            $step  = '-15 day';
            $count = 4;
        } //$typ == '1m'
        if ($typ == '3m') {
            $step  = '-1 month';
            $count = 3;
        } //$typ == '3m'
        if ($typ == '6m') {
            $step  = '-2 month';
            $count = 3;
        } //$typ == '6m'
        if ($typ == '1y') {
            $step  = '-2 month';
            $count = 6;
        } //$typ == '1y'
        $dates     = $this->date_range($fdt, $step, $output_format, $count);
        $rev_dates = array_reverse($dates);
        $i         = 0;
        foreach ($rev_dates as $key => $value) {
            foreach ($locdata as $val => $ele) {
                $rate                     = $this->getOverallUserRatingLocation($value['interval_end'], $ele, $db, $rmviss, $displayhide);
                //return $ele['locname'];
                //return $rate;//exit;
                $cname                    = trim($ele['locname']);
                //if(! $rate['rate']){$rate['rate']=0.1;}
                $data[$cname][$i]['rate'] = $rate['rate'];
                $data[$cname][$i]['date'] = $value['interval_end'];
                if ($typ == '6m' || $typ == '1y') {
                    $data[$cname][$i]['dispdate'] = date("M Y", strtotime($value['interval_end']));
                } else {
                    $data[$cname][$i]['dispdate'] = date(IPY_DATE_DISPLAY_FORMAT, strtotime($value['interval_end']));
                }
                $data[$cname][$i]['peoplerate'] = $rate['peoplerating'];
                $data[$cname][$i]['cid']        = $ele['locid'];
                $data[$cname][$i]['cname']      = trim($ele['locname']);
                $data[$cname][$i]['xaxis']      = "RATING";
                $i += 1;
            }
        }
        return $data;
    }
    //---------- To get all complaints with in a range with rating
    public function getRateofComplaints($fdt, $cid, $typ, $db)
    {
        $date = $fdt;
        $i    = 0;
        foreach ($cid as $val => $ele) {
            $rate = $this->getAllComplaints($date, $ele, $db);
            return $rate;
        }
    }
    //-------------- To get dates when type is chosen
    public function date_range($first, $step, $output_format, $count)
    {
        $dates                    = array();
        $current                  = strtotime($first);
        $i                        = 1;
        $dates[0]['interval_end'] = date($output_format, $current);
        while ($count > ($i - 1)) {
            $current                   = strtotime($step, $current);
            $dates[$i]['interval_end'] = date($output_format, $current);
            $i                         = $i + 1;
        } //$current <= $last
        return $dates;
    }
    //---------- To plot pie chart
    public function getRateChartDataPie($tdt, $cid, $db, $rmviss, $resulttype, $displayhide = 'yes')
    {
        $countarray = array();
        foreach ($cid as $val => $ele) {
            //$Cnt[$val]=$ele;
            //$ratearray = $this->getAllComplaints($tdt, $ele, $db, $rmviss, $displayhide);
            $ratearray = $this->getAllComplaints($tdt, $ele, $db, $rmviss);
            //return $ratearray;
            foreach ($ratearray as $key => $value) {
                if ($value['hide'] == 'no' || $displayhide == 'no') {
                    (!isset($countarray[$value['sts']])) ? $countarray[$value['sts']] = 1 : $countarray[$value['sts']] += 1;
                }
            }
        }
        $i   = 0;
        $tot = 0;
        $Cnt = array();
        foreach ($countarray as $cnt => $value) {
            $Cnt[$i]['ind'] = $cnt;
            $Cnt[$i]['val'] = $value;
            $tot            = $tot + $value;
            if ($cnt == "Open") {
                $Cnt[$i]['color'] = '#e85656';
            }
            if ($cnt == "Closed") {
                $Cnt[$i]['color'] = '#209e91';
            }
            if ($cnt == "NA") {
                $Cnt[$i]['color'] = '#FFCE56';
            }
            $i += 1;
        }
        if (isset($resulttype) && $resulttype == 'percentage') {
            foreach ($Cnt as $key => $val) {
                $Cnt[$key]['val'] = round(($Cnt[$key]['val'] / $tot) * 100, 2);
            }
        }
        return $Cnt;
    }
    //---------- To plot pie chart of a level
    public function getRateChartDataPieLevel($tdt, $locdata, $db, $rmviss, $resulttype, $displayhide = 'yes')
    {
        $countarray = array();
        foreach ($locdata as $val => $ele) {
            //$Cnt[$val]=$ele;
            //$ratearray = $this->getAllComplaintsLocation($tdt, $ele, $db, $rmviss, $displayhide);
            $ratearray = $this->getAllComplaintsLocation($tdt, $ele, $db, $rmviss);
            //return $ratearray;//sayyy
            foreach ($ratearray as $key => $value) {
                if (isset($value['hide']) && $value['hide'] == 'no') {
                    (!isset($countarray[$value['sts']])) ? $countarray[$value['sts']] = 1 : $countarray[$value['sts']] += 1;
                }
            }
        }
        $i   = 0;
        $tot = 0;
        $Cnt = array();
        foreach ($countarray as $cnt => $value) {
            $Cnt[$i]['ind'] = $cnt;
            $Cnt[$i]['val'] = $value;
            $tot            = $tot + $value;
            if ($cnt == "Open") {
                $Cnt[$i]['color'] = '#e85656';
            }
            if ($cnt == "Closed") {
                $Cnt[$i]['color'] = '#209e91';
            }
            if ($cnt == "NA") {
                $Cnt[$i]['color'] = '#FFCE56';
            }
            $i += 1;
        }
        if (isset($resulttype) && $resulttype == 'percentage') {
            foreach ($Cnt as $key => $val) {
                $Cnt[$key]['val'] = round(($Cnt[$key]['val'] / $tot) * 100, 2);
            }
        }
        return $Cnt;
    }
    //-----------------to find the difference between two dates
    public function dateDiff($d1, $d2)
    {
        return round(abs(strtotime($d1) - strtotime($d2)) / 86400);
    }
}
?>
