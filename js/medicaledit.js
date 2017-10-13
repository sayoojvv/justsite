$(document).ready(function () {
    $("#state").on("change", function () {
        var stateid = $("#state").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getdistrict',
                'value': stateid
            }
        }).done(function (response) {
            $("#district").html(response).trigger('change');
        }).fail(function () {
            bootbox.alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $("#district").on("change", function () {
        var districtid = $("#district").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getpincode',
                'value': districtid
            }
        }).done(function (response) {
            $("#pincode").html(response).trigger('change');
        }).fail(function () {
            bootbox.alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $("#pincode").on("change", function () {
        var pincodeid = $("#pincode").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getarea',
                'value': pincodeid
            }
        }).done(function (response) {
            $("#area").html(response).trigger('change');
        }).fail(function () {
            bootbox.alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $("#deleteprofilepic").on("click", function () {
        var eleid = $("#medicalid").val();
        var $modal = $('#delcheckmodal');
        var style = 'modal-header bg-danger';
        var stsstyle = 'fa fa-exclamation-circle';
        var usertype = 'medical';
        $modal.load(
            'profilepicmodaldelete.php', {
                'eleid': eleid,
                'usertype': usertype,
                'style': style,
                'stsstyle': stsstyle
            },
            function () {
                $modal.modal('show');
            }
        );
    });
    $('#btnsubmit').on('click', function () {
        $('form#medicaledit').parsley().validate();
        validateFront();
    });
});
var validateFront = function () {
    console.log('parseley valid = ' + $('form#medicaledit').parsley().isValid());
    if (false === $('form#medicaledit').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        medicaledit();
    }
};

function deleteProfilepicConfirm(eleid, usertype) {
    $('#delcheckmodal').modal('hide');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/deleteprofilepic.php',
        data: {
            'data': eleid,
            'usertype': usertype
        }
    }).done(function (response) {
        if (response.sts == 1) {
            sToast(response.msg, 'Success');
            $('#displaypicture').hide();
        } else {
            eToast(response.msg, 'Error');
        }
    }).fail(function () {
        eToast('Something went wrong', 'Error');
    });
}
window.Parsley.on('field:error', function () {
    // This global callback will be called for any field that fails validation.
    console.log('Validation failed for: ', this.$element);
});

function medicaledit() {
    var formData = new FormData($("form#medicaledit")[0]);
    $.ajax({
        url: 'scripts/medicaledit.php',
        type: 'POST',
        data: formData,
        async: false,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if (data.sts == 1) {
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
Parsley.addValidator('emailchangeexist', {
    validateString: function (value) {
        var id = $('#medicalid').val();
        var xhr = $.ajax({
            url: 'commonprocess.php?method=emailchangeexist&value=' + value + '&usertype=member&id=' + id,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This email address already exists");
        });
    }
});
Parsley.addValidator('nicknamechangeexist', {
    validateString: function (value) {
        var id = $('#medicalid').val();
        var xhr = $.ajax({
            url: 'commonprocess.php?method=nicknamechangeexist&value=' + value + '&usertype=member&id=' + id,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This nickname is already in use.");
        });
    }
});
Parsley.addValidator('mobilechangeexist', {
    validateString: function (value) {
        var id = $('#medicalid').val();
        var xhr = $.ajax({
            url: 'commonprocess.php?method=mobilechangeexist&value=' + value + '&usertype=member&id=' + id,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This mobile number is already in use.");
        });
    }
});
