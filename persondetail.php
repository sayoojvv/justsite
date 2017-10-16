<?php
require_once("./includes/constants.php");
error_reporting(E_ALL); ini_set('display_errors', 1);
$pagedef =array(
      'pagename' =>'index',
      'filename'=>'index.php',
      'title'=> 'test site',
      'description'=>'test site',
      'keywords'=>'test site',
      'noindex'=>'false',
      'noscript'=>'true',
      'header'=>'true',
      'footer'=>'true',    
      'additionaljs'=>array('persondetail.js')
      );
require_once("./".HOS_CLIENT_DBCONNECT."/topinc.php");
require_once("./includes/functions.php");

$personsql="select * from person";
$persondata=$db->query($personsql,array());
?>


<!DOCTYPE html>
<html lang="en">
   <head>
      <base href="<?php echo HOS_WEBROOT_URL; ?>" target="_self">
      <title>Sayooj Site</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="assets/css/owl.carousel.css">
      <link rel="stylesheet" href="assets/css/owl.theme.css">
      <link href="assets/css/font-awesome.min.css" rel="stylesheet">
      <!-- bootstrap-daterangepicker -->
      <link href="assets/js/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
      <!-- bootstrap-datetimepicker -->
      <link href="assets/js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

      <link href='assets/css/css.css' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="assets/css/dataTables.bootstrap.min.css">
       <link rel="stylesheet" href="assets/css/responsive.bootstrap.min.css">
      <!-- toaster -->
      <link href="<?php echo HOS_WEBROOT_URL; ?>assets/js/toaster/jquery.toast.css" rel="stylesheet">
      <link rel="stylesheet" href="css/body.css">



      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/jquery.min.js"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/bootstrap.min.js"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/owl.carousel.js"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>js/jquery.bootstrap.newsbox.min.js" type="text/javascript"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>js/news-ticker.js" type="text/javascript"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>js/tab-slider.js" type="text/javascript"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>js/my-owl-script.js" type="text/javascript"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>js/pagination.js" type="text/javascript"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/parsleyjs/dist/parsley.js"></script>
      <!-- bootstrap-daterangepicker -->
       <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/moment/min/moment.min.js"></script>
       <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/bootstrap-daterangepicker/daterangepicker.js"></script>
       <!-- bootstrap-datetimepicker -->
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/jquery.form.js"></script>
      <!-- Toaster -->
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/toaster/jquery.toast.js"></script>
      <script src="js/initcomponents.js"></script>
      <script src="js/html5shiv.js"></script>
      <?php
         if(isset($pagedef['additionaljs'])){
            foreach($pagedef['additionaljs'] as $js){
             echo "<script type='text/javascript' src='js/".$js."'></script>";
            }
         }
      ?>
      <script type="text/javascript" src="includes/validation.js"></script>

   </head>

   <body>
      <div class="top-gradient"></div>
      <section style="border-bottom:1px solid #000;">
         <div class="container">
            
         </div>
      </section>
     <section style="margin-bottom:30px; color:#FFF; background-color:#666;">
         <div class="container-fluid">
         <div style="font-size:30px; padding:20px; text-align:center; font-weight:bold;">Member Details</div>
      </div></section>

<section style="margin-top:30px; margin-bottom:30px; text-align:justify;">
  <form class="form-horizontal" role="form" id="personadd" data-parsley-validate class="form-horizontal form-label-left" action="scripts/personadd.php?method=add" method="post" enctype='multipart/form-data' style="width:100%;">
      <div class="container">
      <div class="row">
      <div class="col-md-6">

         <div class="form-group">
            <label for="firstName" class="col-sm-4 control-label">Full Name</label>
            <div class="col-sm-8">
               <input type="text"  name="fullname" id="fullname" placeholder="Full Name" class="form-control" required="required" data-parsley-trigger="change" data-parsley-inputname="Minimum 5 and maximum 100 characters, only alphabets and space allowed" data-parsley-required-message="Please enter full name  (length 5 to 100 characters)">
            </div>
         </div>
         <div class="form-group">
            <label for="email" class="col-sm-4 control-label">Mobile</label>
            <div class="col-sm-8">
               <input type="text"  placeholder="Mobile" class="form-control" title="Enter Mobile number"  name="mobile" id="mobile" data-parsley-trigger="change" required=""  data-parsley-mobilenumber="Please enter a valid mobile number. Only digits allowed." data-parsley-required-message="Please enter a valid mobile number (length 10 digits)">
            </div>
         </div>
         <div class="form-group">
            <label for="email" class="col-sm-4 control-label">Email</label>
            <div class="col-sm-8">
               <input type="email" name="emailaddress" id="emailaddress" placeholder="Email" class="form-control"  data-parsley-trigger="change" required="" data-parsley-emailexist="" data-parsley-emailaddress="Please enter a valid email address" data-parsley-required-message="Please enter a valid email address (length 5 to 100 characters)" data-parsley-length="[5, 100]">
            </div>
         </div>
         <div class="form-group">
            <label for="email" class="col-sm-4 control-label">Photo</label>
            <div class="col-sm-8">
               <div class="input-group">
                  <label class="input-group-addon">
                     <span class="pointer">
                        Browse&hellip; <input type="file" style="display: none;" name="personphoto" id="personphoto" >
                     </span>
                  </label>
                  <input type="text" class="form-control" readonly title="BROWSE IMAGE" placeholder="BROWSE IMAGE">
               </div>
            </div>
         </div>
        </div>
      <div class="col-md-6" style="width:100px; height:100px;">
         <img id="imgthumb" class="img-responsive" src=""></img>
      </div>
      </div>
      </div>
  </form>
  <div class="form-group">
       
           <div align="center">
                <button id="btnsubmit" class="button" style="margin-top:8px; margin-left:0px;">
              Register</button> 
           </div>
     
     </div>
  </div>
</section>


<section>
<div class="col-md-12">
<table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>email</th>
                <th>Phone</th>
                <th>Image</th>
                <th>Actions</th>
                
            </tr>
        </thead>
 
        <tbody>

          <?php  $i = 1;
                  foreach($persondata as $key=>$person){
                  $personid = $person['id'];
                  $url="id=".$personid;
                  $encurl= encrypt_decrypt('encrypt', $url, HOS_URL_ENCWORD);
                  $personpath = HOS_WEBROOT_URL.HOS_UPLOAD_FOLDER.'/person/'.$person['fpath'].'?time='.time() ;
                  $i++;   
          ?>
            <tr>
                <td><?php echo $key+1; ?></td>
                <td><?php echo $person['name']; ?></td>
                <td><?php echo $person['email']; ?></td>
                <td><?php echo $person['phone']; ?></td>

                <td><img width="120" height="60" src="<?php echo $personpath; ?>"></td>
                <td>
                    <div>
                    <button data-id="<?php echo $person['id']; ?>" class="button editperson" style="margin:3px; padding:5px; font-size:13px; width:90px;">Edit</button>
                    <a><button  data="<?php echo $encurl; ?>" name='this person' class="button removediagnosticperson" style="margin:3px; padding:5px; font-size:13px; width:90px;">Remove</button></a>

                   </div>
                </td>
              
            </tr>
            <?php } ?>
  <tr>   
        </tbody>
    </table>
  </div>
</section>
      <script src="js/datatable.js"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/jquery.dataTables.min.js"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/dataTables.bootstrap.min.js"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/dataTables.responsive.min.js"></script>
      <script src="<?php echo HOS_WEBROOT_URL; ?>assets/js/responsive.bootstrap.min.js"></script>
      <script src="js/datatable_search_results.js"></script>
