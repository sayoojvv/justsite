<?php
require_once("Db.class.php");
try{
	$db      = new Db();
}
catch(Exception $e){
	$response_array = array('response' => 'Error',"ErrorDesc"=>$e->getMessage());
	$response = json_encode($response_array,JSON_UNESCAPED_SLASHES);			
	echo $response;die();	
}

?>
