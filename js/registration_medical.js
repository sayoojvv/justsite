$(document).ready(function () {
    $("input[name=firmtype]").on("change", function () {
        var type = $('input[name=firmtype]:checked').attr("value");
        $('input').each(function (index, value) {
            $(this).removeAttr('readonly');
        });
        $('textarea').each(function (index, value) {
            $(this).removeAttr('readonly');
        });
        $('select').each(function (index, value) {
            $(this).removeAttr('readonly');
        });
        if (type == 'diagnostic') {
            $('.hospital').hide();
            $('input[type=text]').each(function (index, value) {
                $(this).val('');
            });
            $('textarea').each(function (index, value) {
                $(this).text('');
            });
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'commonprocess.php',
                data: {
                    'method': 'getdiagnosticunlisted',
                    'type': 'diagnostic'
                }
            }).done(function (response) {
                $("#firmid").html(response).trigger('change');
            }).fail(function () {
                alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
            });
        } else {
            $('.hospital').show();
            $('input[type=text]').each(function (index, value) {
                $(this).val('');
            });
            $('textarea').each(function (index, value) {
                $(this).text('');
            });
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'commonprocess.php',
                data: {
                    'method': 'getdiagnosticunlisted',
                    'type': 'hospital'
                }
            }).done(function (response) {
                $("#firmid").html(response).trigger('change');
            }).fail(function () {
                alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
            });
        }
    });
    $("#firmid").on("change", function () {
        var type = $('input[name=firmtype]:checked').attr("value");
        var firmid = $("#firmid").val();
        if (firmid != '') {
            console.log(type);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'commonprocess.php',
                data: {
                    'method': 'getfirmdetails',
                    'type': type,
                    'firmid': firmid
                }
            }).done(function (response) {
                console.log(response.fullname);
                if (response.fullname) {
                    $('#fullname').val(response.fullname);
                    $('#fullname').attr('readonly', true)
                } else {
                    $('#fullname').val('');
                    $('#fullname').removeAttr('readonly');
                }
                if (response.shortname) {
                    $('#shortname').val(response.shortname);
                    $('#shortname').attr('readonly', true)
                } else {
                    $('#shortname').val('');
                    $('#shortname').removeAttr('readonly');
                }
                if (response.email) {
                    $('#emailaddress').val(response.email);
                    $('#emailaddress').attr('readonly', true)
                } else {
                    $('#emailaddress').val('');
                    $('#emailaddress').removeAttr('readonly');
                }
                if (response.phone) {
                    $('#phonenumber').val(response.phone);
                    $('#phonenumber').attr('readonly', true)
                } else {
                    $('#phonenumber').val('');
                    $('#phonenumber').removeAttr('readonly');
                }
                if (response.mobile) {
                    $('#mobile').val(response.mobile);
                    $('#mobile').attr('readonly', true)
                } else {
                    $('#mobile').val('');
                    $('#mobile').removeAttr('readonly');
                }
                if (response.website) {
                    $('#website').val(response.website);
                    $('#website').attr('readonly', true)
                } else {
                    $('#website').val('');
                    $('#website').removeAttr('readonly');
                }
                if (response.estyear) {
                    $('#establishedyear').val(response.estyear);
                    $('#establishedyear').attr('readonly', true)
                } else {
                    $('#establishedyear').val('');
                    $('#establishedyear').removeAttr('readonly');
                }
                if (response.descr) {
                    $('#description').text(response.descr);
                    $('#description').attr('readonly', true)
                } else {
                    $('#description').text('');
                    $('#description').removeAttr('readonly');
                }
                if (response.hospital_nophysicians) {
                    $('#nophysicians').val(response.hospital_nophysicians);
                    $('#nophysicians').attr('readonly', true)
                } else {
                    $('#nophysicians').text('');
                    $('#nophysicians').removeAttr('readonly');
                }
                if (response.hospital_nobed) {
                    $('#nobed').val(response.hospital_nobed);
                    $('#nobed').attr('readonly', true)
                } else {
                    $('#nobed').text('');
                    $('#nobed').removeAttr('readonly');
                }
                if (response.hospital_noemployee) {
                    $('#noemployees').val(response.hospital_noemployee);
                    $('#noemployees').attr('readonly', true)
                } else {
                    $('#noemployees').text('');
                    $('#noemployees').removeAttr('readonly');
                }
                if (response.streetaddr) {
                    $('#address').val(response.streetaddr);
                    $('#address').attr('readonly', true)
                } else {
                    $('#address').text('');
                    $('#address').removeAttr('readonly');
                }
                $('#state').val(response.state_id);
                //---------------------------------------
                var stateid = response.state_id;
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'commonprocess.php',
                    data: {
                        'method': 'getdistrict',
                        'value': stateid
                    }
                }).done(function (data) {
                    $("#district").html(data);
                    $('#district').val(response.district_id);
                }).fail(function () {
                    alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
                });
                //---------------------------------------
                var districtid = response.district_id;
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'commonprocess.php',
                    data: {
                        'method': 'getpincode',
                        'value': districtid
                    }
                }).done(function (data) {
                    $("#pincode").html(data);
                    $('#pincode').val(response.pincode_id);
                }).fail(function () {
                    alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
                });
                //-----------------------------------------
                var pincodeid = response.pincode_id;
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'commonprocess.php',
                    data: {
                        'method': 'getarea',
                        'value': pincodeid
                    }
                }).done(function (data) {
                    $("#area").html(data);
                    $('#area').val(response.area_id);
                }).fail(function () {
                    alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
                });
                //-----------------------------------------
            }).fail(function () {
                alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
            });
            $('#state').attr('readonly', true)
            $('#district').attr('readonly', true)
            $('#pincode').attr('readonly', true)
            $('#area').attr('readonly', true)
        }
    });
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
    $('#btnsubmit').on('click', function () {
        $('form#register_medical').parsley().validate();
        validateFront();
    });
});
var validateFront = function () {
    if (false === $('form#register_medical').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        registration_medical();
    }
};

function registration_medical() {
    $("#register_medical").ajaxForm({
        dataType: 'json',
        beforeSubmit: function (data) {},
        success: function (data) {
            if (data.sts > 0) {
                setTimeout(function () {
                    window.location = data.url;
                }, 1000);
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
