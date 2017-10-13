$(document).on('keypress', '#password', function (e) {
    if (e.which === 13) {
        ValidateLogin();
    }
});
$(document).ready(function () {
    $(".writereview").on("click", function () {
        var eleid = $("#hospid").val();
        var $modal = $('#writereview');
        $modal.load(
            'writereview.php', {
                'eleid': eleid
            },
            function () {
                $modal.modal('show');
            }
        );
    });
    $(".writereviewneigh").on("click", function () {
        var eleid = $(this).attr('data-val');
        var $modal = $('#writereview');
        $modal.load(
            'writereview.php', {
                'eleid': eleid
            },
            function () {
                $modal.modal('show');
            }
        );
    });
    $(document).on("change", 'select#departmentlist', function () {
        var hospid = $("#hospid").val();
        var department = $("#departmentlist").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getdoctorphotowithdept',
                'hospitalid': hospid,
                'department': department
            }
        }).done(function (response) {
            if (response) {
                $owl = $('#owl-demo1');
                $owl.data('owlCarousel').destroy();
                $(".doctor").html(response);
                $owl.owlCarousel({
                    autoPlay: 3000, //Set AutoPlay to 3 seconds
                    items: 6,
                    itemsDesktop: [1199, 3],
                    itemsDesktopSmall: [979, 3]
                });
            } else {
                $(".doctor").html(response);
            }
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $(document).on("change", 'select#department', function () {
        var hospid = $("#hospid").val();
        var department = $("#department").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getdocwithdept',
                'hospitalid': hospid,
                'department': department
            }
        }).done(function (response) {
            $("#doctor").html(response);
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $(document).on("click", '#btnsubmithosp', function () {
        $('form#writereviewform').parsley().validate();
        validateFront();
    });
});
var validateFront = function () {
    console.log('parseley valid = ' + $('form#writereviewform').parsley().isValid());
    if (false === $('form#writereviewform').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        writereview();
    }
};
window.Parsley.on('field:error', function () {
    console.log('Validation failed for: ', this.$element);
});

function writereview() {
    var hospid = $("#hospital_id").val();
    var formData = new FormData($("form#writereviewform")[0]);
    $.ajax({
        url: 'scripts/writereviewprocess.php?method=hospital&firmid=' + hospid,
        type: 'POST',
        data: formData,
        async: false,
        dataType: 'json',
        success: function (data) {
            if (data.sts == 1) {
                $('#writereview').modal('toggle');
                sToast(data.msg, 'Success');
                setTimeout(function () {
                    location.reload();
                }, 3000);
            } else {
                eToast(data.msg, 'Error');
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
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
                //--------------------------------
                location.reload();
                //--------------------------------
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
