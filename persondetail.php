<?php
require_once("./includes/constants.php");
error_reporting(E_ALL); ini_set('display_errors', 1);
$pagedef =array(
      'pagename' =>'index',
      'filename'=>'index.php',
      'title'=> 'Hospital Survey | Welcome to Hospital Survey',
      'description'=>'Hospital Survey.',
      'keywords'=>'Hospital Survey,Survey,Reviews,Reccomends',
      'noindex'=>'false',
      'noscript'=>'true',
      'header'=>'true',
      'footer'=>'true',    
      'additionaljs'=>array('persondetail.js')
      );
require_once("./".HOS_CLIENT_DBCONNECT."/topinc.php");
require_once("./includes/functions.php");
include_once("header1.php");

$contentsql = "SELECT content_heading, `content_matter` FROM `content_master` WHERE content_ref='page_content_terms_of_use'";
$contentrow = $db->row( $contentsql , array() );
?>
     <section style="margin-bottom:30px; color:#FFF; background-color:#666;">
         <div class="container-fluid">
         <div style="font-size:30px; padding:20px; text-align:center; font-weight:bold;">Member Details</div>
      </div></section>

<section style="margin-top:30px; margin-bottom:30px; text-align:justify;">
  <form class="form-horizontal" role="form" id="personadd" data-parsley-validate class="form-horizontal form-label-left" action="scripts/register.php?method=<?php echo 'register'.$type;?>" method="post" enctype='multipart/form-data' style="width:100%;">
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
                  <input type="text" class="form-control" readonly title="BROWSE LOGO" placeholder="BROWSE LOGO">
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


</section>


<?php
      include_once("footer.php");
      
?>
