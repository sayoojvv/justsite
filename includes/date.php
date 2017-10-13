<?PHP
date_default_timezone_set('Asia/Kolkata');
$cutime=gmdate("Y-m-d h:i:s A", time() + 3600*(5.5+date("I")));
$cudateexp=explode(" ",$cutime);
$schtime=dateAdd($cudateexp[0],1)." 10:00:00 AM";

echo $cutime."<br>";
echo $schtime."<br>";

$schinter=date_diff(date_create($cutime),date_create($schtime));
$schinterval=$schinter->format("%dd%hh%im%ss");

echo $schinterval;

function dateAdd($source_date,$interval_days)
{

	return date("Y-m-d",strtotime("+".$interval_days." day", strtotime($source_date)));
	//return date('d/m/Y', strtotime('+1 day', strtotime($source_date)));


	
}

?>
