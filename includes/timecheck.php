<?PHP
//date_default_timezone_set('Asia/Kolkata');
$cutime        = gmdate("Y-m-d h:i:s A", time() + 3600 * (-7 + date("I")));
$endam         = substr($cutime, strlen($cutime) - 2);
$hours         = intval(substr($cutime, 11, 2));

echo $hours." ".$endam;

echo "<br><br>";
echo time();
echo "<br><br>";
echo date("Y-m-d H:i:s",1489759200);
?>
