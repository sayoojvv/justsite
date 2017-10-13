$(document).ready(function () {
    $("input:radio[name=firmtype]").on("change", function () {
        var firmtype = $("input:radio[name=firmtype]:checked").val();
        if (firmtype == 'hospital') {
            $("#department").show();
            $("#departmentdist").show();
            $("#departmentdistlabel").show();
            $('#firmtypedistance').val('hospital');
            $('#firmtypename').val('hospital');
        } else {
            $("#department").val('');
            $("#department").hide();
            $("#departmentdist").val('');
            $("#departmentdist").hide();
            $("#departmentdistlabel").hide();
            $('#firmtypedistance').val('diagnostic');
            $('#firmtypename').val('diagnostic');
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
});

function validateLocation() {
    var firmtype = $("input:radio[name=firmtype]:checked").val();
    if (!firmtype || firmtype == '') {
        wToast('Choose Either Hospital or Diagnostic Centre');
        document.getElementsByName('firmtype')[0].focus();
        return false;
    }
    var state = $("#state").val();
    if (!state) {
        wToast('Choose a state');
        $("#state").focus();
        return false;
    }
    return true;
}

function validateDistance() {
    var firmtype = $("input:radio[name=firmtype]:checked").val();
    if (!firmtype || firmtype == '') {
        wToast('Choose Either Hospital or Diagnostic Centre');
        document.getElementsByName('firmtype')[0].focus();
        return false;
    }
    var pincodedist = $("#pincodedist").val();
    if (!pincodedist) {
        wToast('Choose a Pin Code');
        $("#pincodedist").focus();
        return false;
    }
    var distancelim = $("#distancelim").val();
    if (!distancelim) {
        wToast('Choose Within');
        $("#distancelim").focus();
        return false;
    }
    return true;
}

function validateName() {
    var firmtype = $("input:radio[name=firmtype]:checked").val();
    if (!firmtype || firmtype == '') {
        wToast('Choose Either Hospital or Diagnostic Centre');
        document.getElementsByName('firmtype')[0].focus();
        return false;
    }
    var firmname = $("#firmname").val();
    if (!firmname || firmname == '') {
        wToast('Enter a name to search');
        $("#firmname").focus();
        return false;
    }
    return true;
}
