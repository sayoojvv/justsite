<?php
require_once("./includes/constants.php");
//error_reporting(E_ALL); ini_set('display_errors', 1);
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
      'additionaljs'=>array('registration.js')
      );
require_once("./".HOS_CLIENT_DBCONNECT."/topinc.php");
require_once("./includes/functions.php");
include_once("header.php");

$post = decodeurl($_GET['data']);
$type=$post['type'];


$titles=unserialize(HOS_TITLES);

$statesql = "SELECT `state_id`, `state_name` FROM `state_master` where state_active = 'yes' order by state_name ";
$staterow = $db->query($statesql,array());
?>
      <section style="background-color:#f0f0f0">
       <form class="form-horizontal" role="form" id="doctoradd" data-parsley-validate class="form-horizontal form-label-left" action="scripts/register.php?method=<?php echo 'register'.$type;?>" method="post" enctype='multipart/form-data' style="width:100%;">
         <div class="container">
         <div class="row">
    
          <div class="col-md-6">
             <h3 style="text-align:center;">Registration Form ( <?php echo ucfirst($type);?> )</h3>
               <br>
               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">Title</label>
                  <div class="col-sm-8">
                     <select id="title" name='title' class="form-control" required="required" title="Select title" data-parsley-required-message="Please select title">
                        <option value="">-- Choose --</option>
                              <?php
                              foreach ($titles as $key => $title){
                                 echo '<option value="'.$title.'">'.$title.'</option>';
                              }
                              ?>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">Full Name</label>
                  <div class="col-sm-8">
                     <input type="text"  name="fullname" id="fullname" placeholder="Full Name" class="form-control" required="required" data-parsley-trigger="change" data-parsley-inputname="Minimum 5 and maximum 100 characters, only alphabets and space allowed" data-parsley-required-message="Please enter full name  (length 5 to 100 characters)">
                  </div>
               </div>
               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">Nick Name</label>
                  <div class="col-sm-8">
                     <input type="text" id="firstName" placeholder="Nick Name" name="nickname" id="nickname" class="form-control" data-parsley-trigger="change" required="" data-parsley-nicknameexist="" data-parsley-required-message="Please enter nickname (length 5 to 20 characters)" data-parsley-nickname="Minimum 5 and maximum 20 characters , only alphabets and numbers and no space allowed">
                  </div>
               </div>
               <div class="form-group">
                  <label for="email" class="col-sm-4 control-label">Email</label>
                  <div class="col-sm-8">
                     <input type="email" name="emailaddress" id="emailaddress" placeholder="Email" class="form-control"  data-parsley-trigger="change" required="" data-parsley-emailexist="" data-parsley-emailaddress="Please enter a valid email address" data-parsley-required-message="Please enter a valid email address (length 5 to 100 characters)" data-parsley-length="[5, 100]">
                  </div>
               </div>


               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">Password</label>
                  <div class="col-sm-8">
                     <input class="form-control" placeholder="Type password here" type="password" required title="Type your password here"  name="password" id="password" data-parsley-trigger="change"  data-parsley-required-message="Please enter password (length 4 to 20 characters)" data-parsley-userpassword="Password must contain atleast 1 alphabet, 1 number and length between 4 to 20 characters.">
                  </div>
               </div>

                <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">Re-enter Password</label>
                  <div class="col-sm-8">
                     <input class="form-control" placeholder="Confirm password" type="password" required title="Confirm your password"  name="cpassword" id="cpassword" data-parsley-required-message="Please enter confirmation password (length 4 to 20 characters)" data-parsley-confirmequal="password||Password does not match the confirmation password">
                  </div>
               </div>

               <div class="form-group">
                  <label for="mobile" class="col-sm-4 control-label">Mobile</label>
                  <div class="col-sm-8">
                     <input type="text" name="mobile" id="mobile" placeholder="Mobile" class="form-control" data-parsley-trigger="change" required="" data-parsley-mobileexist="" data-parsley-mobilenumber="Please enter a valid mobile number. Only digits allowed." data-parsley-required-message="Please enter a valid mobile number (length 10 digits)">
                  </div>
               </div>
               <div class="form-group">
                  <label for="birthDate" class="col-sm-4 control-label" >Date of Birth</label>
                  <div class="col-sm-8">
                    <div class='input-group date datepickerelement' id="ddt">
                              <input type='text' placeholder="Enter date of birth"  data-parsley-dateformat="dd-mm-yyyy" name="dob" max-date="<?php echo date(HOS_DATEPICKER_FORMAT);?>" id='dob' class="form-control"  required="required" data-parsley-required-message="Please choose date of birth"/>
                              <span class="input-group-addon">
                                 <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                           </div>
                  </div>
               </div>
               <div class="form-group">
                  <label for="country" class="col-sm-4 control-label">Marital Status</label>
                  <div class="col-sm-8">
                     <select  id="maritalstatus" class="form-control" name="maritalstatus" required="required" title="Select maritalstatus" data-parsley-required-message="Please select marital status">
                        <option value="">-- Choose marital status --</option>
                              <option value="Single">Single</option>
                              <option value="Married">Married</option>
                              <option value="Divorced">Divorced</option>
                              <option value="Widowed">Widowed</option>
                     </select>
                  </div>
               </div>
               <!-- /.form-group -->
               <div class="form-group">
                  <label class="control-label col-sm-4">Gender</label>
                  <div class="col-sm-8">
                     <div class="row">
                        <div class="col-sm-12">
                           <p>
                           <input style="display:inline" type="radio" name="gender"  value="Male" required="" data-parsley-required-message="Please select gender"/> Male
                           <input style="display:inline;margin-left:20px;" type="radio" name="gender"  value="Female" required=""/> Female
                           <input  style="display:inline;margin-left:20px;" type="radio" name="gender"  value="Other" required=""/> Other
                          </p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            
            <div class="col-md-6">
              <h3 style="text-align:center;">Your Location</h3>
               <br>
               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label" >Street Address</label>
                  <div class="col-sm-8">
                     <input type="text" id="address" name="address" placeholder="Address" class="form-control" required data-parsley-required-message="Please enter street address">
                  </div>
               </div>
               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">State</label>
                  <div class="col-sm-8">
                    <select id="state" class="form-control" name="state" required="required" title="Select state" data-parsley-required-message="Select state">
                        <option value="">-- Choose State --</option>
                                       <?php
                                       foreach ($staterow as $state){
                                       ?>
                                          <option value="<?php echo $state['state_id']; ?>"> <?php echo $state['state_name']; ?> </option>
                                       <?php
                                       }
                                       ?>
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">District</label>
                  <div class="col-sm-8">
                    <select id="district" class="form-control" name="district" required="required" title="Select district" data-parsley-required-message="Select district">
                        <option value="">-- Choose district --</option>
                        
                     </select>
                  </div>
               </div>

               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">Pin Code</label>
                  <div class="col-sm-8">
                     <select id="pincode" class="form-control" name="pincode" required="required" title="Select pincode" data-parsley-required-message="Select pincode">
                        <option value="">-- Choose pincode --</option>
                        
                     </select>
                  </div>
               </div>
               <div class="form-group">
                  <label for="firstName" class="col-sm-4 control-label">Area</label>
                  <div class="col-sm-8">
                     <select id="area" class="form-control" name="area" required="required" title="Select area" data-parsley-required-message="Select area">
                                       <option value="">-- Choose area --</option>
                                       
                                    </select>
                  </div>
               </div>
               <!-- /.form-group -->
               <!-- /.form-group -->
               <div class="form-group">
                  <div class="col-sm-9 col-sm-offset-3">
                     <div class="checkbox">
                        <label>
                        <input type="checkbox" required="required" name="terms" id="terms" data-parsley-required-message="Accept terms and conditions" value="0">I accept the terms and conditions
                        </label>
                     </div>
                  </div>
               </div>
               <!-- /.form-group -->
               <div class="form-group">
                  <div class="col-sm-9 col-sm-offset-3">
                     <div class="checkbox">
                        <label>
                        <input type="checkbox"  name="subscribe" id="subscribe" value="0">Subscribe to our newsletter
                        </label>
                     </div>
                  </div>
               </div>
               <!-- /.form-group -->
               
            </div>
            
            <div class="col-md-12">
            <hr>
           <div class="form-group">
                 
                     <div align="center">
                          <button id="btnsubmit" class="button" style="margin-top:8px; margin-left:0px;">
                        Register</button> 
                     </div>
               
               </div>
            </div>
             
            
         
           
          </div>
           
            <!-- /form -->
         </div>
          </form>
       
      </section>
      
   <?php
      include_once("footer.php"); 
   ?>