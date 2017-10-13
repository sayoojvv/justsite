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
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
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
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
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
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
});
$('#btnsubmit').on('click', function () {
    $('form#hospitaladd').parsley().validate();
    validateFront();
});
var validateFront = function () {
    if (false === $('form#hospitaladd').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        hospitaladd();
    }
};

function hospitaladd() {
    $("#hospitaladd").ajaxForm({
        dataType: 'json',
        beforeSubmit: function (data) {},
        success: function (data) {
            if (data.sts > 0) {
                /*setTimeout(function () {
                	window.location = data.url;
                }, 1000);*/
            } else {
                eToast(data.msg, 'Error');
            }
        }
    }).submit();
}
Parsley.addValidator('emailexist', {
    validateString: function (value) {
        var xhr = $.ajax({
            url: 'commonprocess.php?method=emailexist&value=' + value + '&usertype=doctor',
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This email address already exists");
        });
    }
});
Parsley.addValidator('nicknameexist', {
    validateString: function (value) {
        var xhr = $.ajax({
            url: 'commonprocess.php?method=nicknameexist&value=' + value + '&usertype=doctor',
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This nickname is already in use.");
        });
    }
});
Parsley.addValidator('mobileexist', {
    validateString: function (value) {
        var xhr = $.ajax({
            url: 'commonprocess.php?method=mobileexist&value=' + value + '&usertype=doctor',
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This mobile number is already in use.");
        });
    }
});
