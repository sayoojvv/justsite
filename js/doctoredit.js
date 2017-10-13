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
        var eleid = $("#doctorid").val();
        var $modal = $('#delcheckmodal');
        var style = 'modal-header bg-danger';
        var stsstyle = 'fa fa-exclamation-circle';
        var usertype = 'doctor';
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
    //-----------------------------QUALIFICATION ADD--------------------------///
    $(".addquali").on("click", function () {
        var eleid = $("#doctorid").val();
        var $modal = $('#addqualificationmodal');
        var usertype = 'doctor';
        if ($("#qualificationlist").length > 0) $("#qualificationlist").remove();
        var qualificationarray = [];
        $("table.qual > tbody >  tr").each(function (index) {
            $(this).find("input.quali").each(function (index) {
                var r1 = $(this).val();
                qualificationarray.push(r1);
            });
        });
        $modal.load(
            'addqualificationmodal.php', {
                'eleid': eleid,
                'usertype': usertype,
                'prequal': qualificationarray
            },
            function () {
                $modal.modal('show');
            }
        );
    });
    //$("#qualimodaladdbtn").on("click", function() {
    $(document).on("click", "#qualimodaladdbtn", function () {
        var ql = $("#qualificationlist option:selected").text();
        var qlv = $("#qualificationlist option:selected").val();
        console.log(ql);
        var row = $("table.qual > tbody>  tr:last ");
        if ($("table.qual > tbody>  tr:last ").length == 0) {
            var row = $("table.qual > tbody ");
            row.append('<tr class="participantRow"><td>' + ql + '</td><td><input type="hidden" class="quali" value="' + qlv + '" /><a class="button pointer removerow" style="margin:3px;padding:5px; font-size:12px;">Remove</a></td></tr>');
        } else {
            row.after('<tr class="participantRow"><td>' + ql + '</td><td><input type="hidden" class="quali" value="' + qlv + '" /><a class="button pointer removerow" style="margin:3px;padding:5px; font-size:12px;">Remove</a></td></tr>');
        }
        $('#addqualificationmodal').modal('toggle');
    });
    //-------------------------------------------------------------------------///
    //-----------------------------SPECIALIZATION ADD--------------------------///
    $(".addspec").on("click", function () {
        var eleid = $("#doctorid").val();
        var $modal = $('#addspecializationmodal');
        var usertype = 'doctor';
        if ($("#specializationlist").length > 0) $("#specializationlist").remove();
        var specializationarray = [];
        $("table.spec > tbody >  tr").each(function (index) {
            $(this).find("input.specl").each(function (index) {
                var r1 = $(this).val();
                specializationarray.push(r1);
            });
        });
        $modal.load(
            'addspecializationmodal.php', {
                'eleid': eleid,
                'usertype': usertype,
                'prespecl': specializationarray
            },
            function () {
                $modal.modal('show');
            }
        );
    });
    $(document).on("click", "#speclmodaladdbtn", function () {
        var ql = $("#specializationlist option:selected").text();
        var qlv = $("#specializationlist option:selected").val();
        console.log(ql);
        var row = $("table.spec > tbody>  tr:last ");
        if ($("table.spec > tbody>  tr:last ").length == 0) {
            var row = $("table.spec > tbody ");
            console.log(row);
            row.append('<tr class="participantRow"><td>' + ql + '</td><td><input type="hidden" class="specl" value="' + qlv + '" /><a class="button pointer removerow" style="margin:3px;padding:5px; font-size:12px;">Remove</a></td></tr>');
        } else {
            row.after('<tr class="participantRow"><td>' + ql + '</td><td><input type="hidden" class="specl" value="' + qlv + '" /><a class="button pointer removerow" style="margin:3px;padding:5px; font-size:12px;">Remove</a></td></tr>');
        }
        $('#addspecializationmodal').modal('toggle');
    });
    //-------------------------------------------------------------------------///
    $(".removerow").on("click", function () {
        $(this).closest('tr').remove();
    });
    $('#btnsubmit').on('click', function () {
        $('form#doctoredit').parsley().validate();
        validateFront();
    });
});
var validateFront = function () {
    console.log('parseley valid = ' + $('form#doctoredit').parsley().isValid());
    if (false === $('form#doctoredit').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        var preemail=$("#preemail").val();
        var emailaddress=$("#emailaddress").val();
        if(preemail!=emailaddress){
            //------------------------------
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'scripts/updateemail.php?method=sendotp',
                    data: {
                        'newemail': emailaddress
                    }
                }).done(function (response) {
                    if (response.sts == 1) {
                        $('#emailotp').load(
                            'resetemailotp.php', {
                                'newemail': response.newemail
                            },
                            function () {
                                $('#emailotp').modal('show');
                            }
                        );
                    } else {
                        eToast(response.msg, 'Error');
                    }
                }).fail(function () {
                    eToast('Something went wrong', 'Error');
                });
            //-------------------------------
        }else{
            doctoredit();
        }
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

function doctoredit() {
    var qualificationarray = [];
    $("table.qual > tbody >  tr").each(function (index) {
        $(this).find("input.quali").each(function (index) {
            var r1 = $(this).val();
            qualificationarray.push(r1);
        });
    });
    console.log(qualificationarray);
    var specializationarray = [];
    $("table.spec > tbody >  tr").each(function (index) {
        $(this).find("input.specl").each(function (index) {
            var r1 = $(this).val();
            specializationarray.push(r1);
        });
    });
    console.log(specializationarray);
    var formData = new FormData($("form#doctoredit")[0]);
    formData.append('qualification', qualificationarray);
    formData.append('specialization', specializationarray);
    //formData.append('image', $('input[type=file]')[0].files[0]);
    console.log(formData);
    $.ajax({
        url: 'scripts/doctoredit.php',
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

function CheckOtp(newemail){
//--------------------------------------
        var otp=$('#otp').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'scripts/updateemail.php?method=checkotp',
            data: {
                'newemail': newemail,
                'otp' :otp
            }
        }).done(function (response) {
            if(response.sts==1){
                $("#preemail").val(newemail);
                $('#emailotp').modal('toggle');
            }else{
                eToast('Enter valid otp','Error');
            }
        }).fail(function () {
            bootbox.alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
//--------------------------------------
}


Parsley.addValidator('emailchangeexist', {
    validateString: function (value) {
        var id = $('#doctorid').val();
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
        var id = $('#doctorid').val();
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
        var id = $('#doctorid').val();
        var xhr = $.ajax({
            url: 'commonprocess.php?method=mobilechangeexist&value=' + value + '&usertype=member&id=' + id,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This mobile number is already in use.");
        });
    }
});
