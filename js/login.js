 $(document).ready(function () {
     $('#password').on('keypress', function (e) {
         if (e.which === 13) {
             ValidateLogin();
         }
     });
 });

 function ValidateLogin() {
     var validclass = 'login-input valid';
     var invalidclass = 'login-input invalid';
     var email = document.getElementById("email");
     var password = document.getElementById("password");
     console.log(email.value);
     console.log(password.value);
     var emailsts = 0;
     var passwordsts = 0;
     var msg = '';
     if (isEmpty(email.value)) {
         msg = "E-mail is mandatory";
         emailsts = 0;
         email.className = invalidclass;
         if (placeholderIsSupported()) email.setAttribute('placeholder', msg);
         else {
             document.getElementById("logemailerr").innerHTML = msg;
         }
     } else {
         //if (validate_email(email.value.trim())) {
         emailsts = 1;
         email.className = validclass;
         email.setAttribute('placeholder', 'E-mail');
         document.getElementById("logemailerr").innerHTML = '';
         /* }
          else {
         	 msg = "Please enter a valid email address";
         	 emailsts = 0;
         	 email.className = invalidclass;
         	 document.getElementById("logemailerr").innerHTML = msg;
          }*/
     }
     if (isEmpty(password.value)) {
         msg = "Password is mandatory";
         passwordsts = 0;
         password.className = invalidclass;
         if (placeholderIsSupported()) password.setAttribute('placeholder', msg);
         else {
             document.getElementById("logpassworderr").innerHTML = msg;
         }
     } else {
         passwordsts = 1;
         password.className = 'form-control round_edge valid';
         password.setAttribute('placeholder', 'Password');
         document.getElementById("logpassworderr").innerHTML = '';
     }
     console.log("emailsts= " + emailsts);
     console.log("passwordsts= " + passwordsts);
     if (passwordsts == 0 || emailsts == 0) {
         return false;
     } else {
         document.getElementById("commonerr").innerHTML = '';
         $.ajax({
             type: 'POST',
             dataType: 'json',
             url: 'loginprocess.php',
             data: {
                 'email': email.value,
                 'password': password.value
             }
         }).done(function (response) {
             console.log(response);
             if (response.sts == 1) {
                 document.getElementById("commonerr").innerHTML = response.msg;
                 window.location.href = response.url;
                 return;
             } else {
                 document.getElementById("commonerr").className = 'redtext';
                 document.getElementById("commonerr").innerHTML = response.msg;
                 return;
             }
         }).fail(function () {
             document.getElementById("commonerr").className = 'redtext';
             document.getElementById("commonerr").innerHTML = "Something Went Wrong";
         });
     }
 }
