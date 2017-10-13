$(document).ready(function () {
    $(document).on("click", ".removehospdoctor", function () {
        var data = $(this).attr('data');
        var name = $(this).attr('name');
        var hiddendata = $('#hiddendata').val();
        console.log('data is :' + data);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'deleteConfirm.php',
            data: {
                'data': data,
                'type': 'hospitaldoctor'
            }
        }).done(function (response) {
            var eleid = data;
            var elenam = name;
            var eletype = 'hospitaldoctor';
            var $modal = $('#delcheckmodal');
            if (response.sts == 1) {
                var style = 'modal-header bg-green';
                var stsstyle = "fa fa-check";
                $modal.load(
                    'hospitaldoctormodaldelete.php', {
                        'message': response.msg,
                        'eleid': eleid,
                        'elenam': elenam,
                        'eletype': eletype,
                        'style': style,
                        'stsstyle': stsstyle,
                        'sts': response.sts,
                        'hiddendata': hiddendata
                    },
                    function () {
                        $modal.modal('show');
                    }
                );
            } else {
                var style = 'modal-header bg-red';
                var stsstyle = 'fa fa-exclamation-circle';
                $modal.load(
                    'hospitaldoctormodaldelete.php', {
                        'message': response.msg,
                        'eleid': eleid,
                        'elenam': elenam,
                        'eletype': eletype,
                        'style': style,
                        'stsstyle': stsstyle,
                        'sts': response.sts
                    },
                    function () {
                        $modal.modal('show');
                    }
                );
            }
        }).fail(function () {
            eToast('Something went wrong', 'Error');
        });
    });
    $(document).on("change", "select#department", function () {
        var department = $('#department').val();
        var hospitalid = $('#hospitalid').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php?method=getdocwithdepartment',
            data: {
                'department': department,
                'hospitalid': hospitalid
            }
        }).done(function (response) {
            $('#docdetails').html(response);
        }).fail(function () {
            eToast('Something went wrong', 'Error');
        });
    });
});

function deleteHospitalDoctorConfirm(eleid, hiddendata) {
    $('#delcheckmodal').modal('hide');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/hospitaldoctor.php?method=deletehospitaldoctor',
        data: {
            'data': eleid,
            'type': 'hospitaldoctor',
            'hiddendata': hiddendata
        }
    }).done(function (response) {
        if (response.sts == 1) {
            sToast(response.msg, 'Success');
            setTimeout(function () {
                window.location = response.url;
            }, 1000);
        } else {
            eToast(response.msg, 'Error');
        }
    }).fail(function () {
        eToast('Something went wrong', 'Error');
    });
}
