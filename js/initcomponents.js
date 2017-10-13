$(document).ready(function () {
    //--------------for selecting the default selection in case of combo box--------------//
    $('select').each(function () {
        var dfl = $(this).attr('dfl');
        var eleId = $(this).attr('id');
        if (dfl) {
            $('#' + eleId + " option[value='" + dfl + "']").prop('selected', true).trigger('change');
        }
    });
    $(document).on('change', ':file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $(document).ready(function () {
        $(':file').on('fileselect', function (event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;
            if (input.length) {
                input.val(log);
            } else {
                if (log) alert(log);
            }
        });
    });
    //--------------------------------------------------------------------------------------//
    //-------------------Datepicker element with maxdate -mindate-defaultdate implementation------------------------//
    $('div .datepickerelement').each(function () {
        var ParEleId = $(this).attr('id');
        var inp = $(this).find("input");
        var eleId = $(inp).attr('id');
        if ($(inp).attr('def-date')) {
            var defDate = moment.utc($(inp).attr('def-date'));
        } else {
            var defDate = new Date();
        }
        if ($(inp).attr('format')) {
            var Format = $(inp).attr('format');
        } else {
            var Format = 'DD-MM-YYYY';
        }
        $('#' + ParEleId + '.datepickerelement').datetimepicker({
            format: Format,
            defaultDate: defDate
        });
        if ($(inp).attr('max-date')) {
            var MaxDate = moment.utc($(inp).attr('max-date'));
            $('#' + ParEleId + '.datepickerelement').data("DateTimePicker").maxDate(MaxDate)
        }
        if ($(inp).attr('min-date')) {
            var MinDate = moment.utc($(inp).attr('min-date'));
            $('#' + ParEleId + '.datepickerelement').data("DateTimePicker").minDate(MinDate)
        }
        if ($(inp).attr('view-mode')) {
            var Viewmode = $(inp).attr('view-mode')
            $('#' + ParEleId + '.datepickerelement').data("DateTimePicker").viewMode(Viewmode)
        }
    });
    //---------------------------
});
//------------------------------toaster initialization eToast (error toaster) sToast(success Toast) wToast(warning Toast) -------//
function eToast(msg, heading) {
    $.toast({
        heading: heading,
        showHideTransition: 'plain', //slide , fade option can also be used
        icon: 'error',
        text: msg,
        stack: false
    });
}

function sToast(msg, heading, url) {
    $.toast({
        heading: heading,
        showHideTransition: 'plain', //slide , fade option can also be used
        icon: 'success',
        text: msg,
        stack: false
    });
}

function wToast(msg, heading) {
    $.toast({
        heading: heading,
        showHideTransition: 'plain', //slide , fade option can also be used
        icon: 'warning',
        text: msg,
        stack: false
    });
}
//----------------------------------------------------------------------------------------------------------------------------//
$(document).ready(function () {
    // Configure/customize these variables.
    compresstext();
});

function compresstext() {
    var showChar = 100; // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = "Show more";
    var lesstext = "Show less";
    $('.more').each(function () {
        var content = $(this).text();
        var attr = $(this).attr('data-length');
        if (typeof attr !== typeof undefined && attr !== false) {
            showChar = $(this).attr('data-length');
        }
        if (content.length > showChar) {
            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);
            var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
            $(this).html(html);
        }
    });
    $(".morelink").click(function () {
        if ($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(moretext);
        } else {
            $(this).addClass("less");
            $(this).html(lesstext);
        }
        $(this).parent().prev().toggle();
        $(this).prev().toggle();
        return false;
    });
};
$(document).ready(function () {
    $("#statefooter").on("change", function () {
        var stateid = $("#statefooter").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getdistrict',
                'value': stateid
            }
        }).done(function (response) {
            $("#districtfooter").html(response).trigger('change');
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $("#districtfooter").on("change", function () {
        var districtid = $("#districtfooter").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getareaofdistrict',
                'value': districtid
            }
        }).done(function (response) {
            $("#areafooter").html(response).trigger('change');
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $('#btnsubmitcontactusfooter').on('click', function () {
        $('form#contact_us').parsley().validate();
        validateContactUs();
    });
});
var validateContactUs = function () {
    if (false === $('form#contact_us').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        contact_us();
    }
};

function contact_us() {
    $("#contact_us").ajaxForm({
        dataType: 'json',
        beforeSubmit: function (data) {},
        success: function (data) {
            if (data.sts > 0) {
                $('sendername').val('');
                $('senderemail').val('');
                $('usercomment').text('');
                sToast(data.msg, 'Success');
            } else {
                eToast(data.msg, 'Error');
            }
        }
    }).submit();
}

function validateLocationFooter() {
    var state = $("#statefooter").val();
    if (!state) {
        $("#errmsgfooter").text('Choose a State');
        $("#statefooter").focus();
        return false;
    }
    $("#errmsgfooter").text('');
    return true;
}


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