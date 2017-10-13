<?PHP
$actual_link = IPY_WEBADMINROOT_URL;
$timeout     = IPY_ADMIN_SESSION_TIMEOUT; // Number of seconds until it times out.
if (!empty($_GET['privilegeid'])) {
    $previlage_id = $_GET['privilegeid'];
}
if(isset($_GET['type'])) { $type = $_GET['type'];}
if (!empty($type)) {
    exit;
} else {
    if (isset($_SESSION['timeout']) && !empty($_SESSION['timeout']) && $_SESSION['timeout'] != '') {
        $duration = time() - (int) $_SESSION['timeout'];
        if ($duration > $timeout) {
            $_SESSION['IPY_SIGN_USERID']       = '';
            $_SESSION['IPY_SIGN_USERNAME']     = '';
            $_SESSION['IPY_SIGN_USERTYPE']     = '';
            $_SESSION['IPY_SIGN_USERLEVEL']    = '';
            $_SESSION['IPY_SIGN_USERLEVEL_ID'] = '';
            $_SESSION['IPY_PREVILAGES']        = '';
            $_SESSION['timeout']               = '';
            $Errormessage                      = urlencode("Session has been expired. Please re-login to continue.");
            echo ("<script language='javascript'>
				//alert('Unauthorised access detected. Please login again.');
				window.location.href='logout.php?sts=1&msg=" . $Errormessage . "';
			   </script>");
            exit();
        }
    }
    $_SESSION['timeout'] = time();
    if (isset($_SESSION['IPY_SIGN_USERID']) && is_numeric($_SESSION['IPY_SIGN_USERID']) && isset($_SESSION['IPY_SIGN_USERNAME']) && trim($_SESSION['IPY_SIGN_USERNAME']) != "" && isset($_SESSION['IPY_SIGN_USERTYPE']) && in_array($_SESSION['IPY_SIGN_USERTYPE'], array(
        'super',
        'sub'
    ))) {
        if ($_SESSION['IPY_SIGN_USERTYPE'] == 'super') {
        } else {
            if (isset($_SESSION['IPY_SIGN_USERLEVEL']) && trim($_SESSION['IPY_SIGN_USERLEVEL']) != "") {
                if (isset($_SESSION['IPY_SIGN_USERLEVEL_ID']) && is_numeric($_SESSION['IPY_SIGN_USERLEVEL_ID'])) {
                    if (isset($_SESSION['IPY_PREVILAGES']) && trim($_SESSION['IPY_PREVILAGES']) != "") {
                        if (!in_array($previlage_id, explode("@", $_SESSION['IPY_PREVILAGES']))) {
                            $Errormessage = urlencode("You don't have permission to operate in this section.");
							if(isset($modelscreen) && $modelscreen==1){
								header("Location:" . $actual_link . "no-previlage.php?sts=1&modal=1&msg=" . $Errormessage);
								exit();
							}
							else{
								header("Location:" . $actual_link . "no-previlage.php?sts=1&msg=" . $Errormessage);
								exit();
							}
                        }
                    } else {
                        $Errormessage = urlencode("Unauthorised access detected. Please login to access.");
                        echo ("<script language='javascript'>
								//alert('Unauthorised access detected. Please login again.');
								window.location.href='logout.php?sts=1&msg=" . $Errormessage . "';
							   </script>");
                        exit();
                    }
                } else {
                    $Errormessage = urlencode("Your Level of operation is not assigned by the administrator.");
                    header("Location:" . $actual_link . "no-previlage.php?sts=1&msg=" . $Errormessage);
                    exit();
                }
            }
        }
    } else {
        $Errormessage = urlencode("Session has been expired. Please re-login to continue.");
        echo ("<script language='javascript'>
				//alert('Unauthorised access detected. Please login again.');
				window.location.href='logout.php?sts=1&msg=" . $Errormessage . "';
			   </script>");
        exit();
    }
}
?>



