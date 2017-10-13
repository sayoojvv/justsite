$(document).ready(function () {
    $('#btnsubmit').on('click', function () {
        $('form#hospitaldepartmentadd').parsley().validate();
        validateDepartmentAdd();
    });
    $('.deactivate').on('click', function () {
        var data = $(this).attr('data');
        var name = $(this).attr('name');
        var hiddendata = $('#hiddendata').val();
        console.log('data is :' + data);
        var eleid = data;
        var elenam = name;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'scripts/hospitaldepartment.php?method=deactivate',
            data: {
                'data': eleid,
                'type': 'hospitaldepartment',
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
    });
    $('.activate').on('click', function () {
        var data = $(this).attr('data');
        var name = $(this).attr('name');
        var hiddendata = $('#hiddendata').val();
        console.log('data is :' + data);
        var eleid = data;
        var elenam = name;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'scripts/hospitaldepartment.php?method=activate',
            data: {
                'data': eleid,
                'type': 'hospitaldepartment',
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
    });
    $(document).on("click", ".suggestdeptmodal", function () {
        var hospitalid = $('#hospitalid').val();
        var hiddendata = $('#hiddendata').val();
        var $modal = $('#suggestdepartment');
        $modal.load(
            'suggestdepartmentmodal.php', {
                'hospitalid': hospitalid,
                'hiddendata': hiddendata
            },
            function () {
                $modal.modal('show');
            }
        );
    });
    $(document).on("click", "#suggestdept", function () {
        var department = $('input#department').val();
        var hiddendata = $('#hiddendata').val();
        if (!department || department == '') {
            wToast('Enter a Department Name', 'Warning');
        } else {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: 'scripts/hospitaldepartment.php?method=suggestdepartment',
                data: {
                    'department': department,
                    'hiddendata': hiddendata
                }
            }).done(function (response) {
                if (response.sts == 1) {
                    $('#suggestdepartment').modal('toggle');
                    sToast(response.msg, 'Success');
                } else {
                    eToast(response.msg, 'Error');
                }
            }).fail(function () {
                eToast('Something went wrong', 'Error');
            });
        }
    });
    $(document).on("click", ".removehospdepartment", function () {
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
                'type': 'hospitaldepartment'
            }
        }).done(function (response) {
            var eleid = data;
            var elenam = name;
            var eletype = 'hospitaldepartment';
            var $modal = $('#delcheckmodal');
            if (response.sts == 1) {
                var style = 'modal-header bg-green';
                var stsstyle = "fa fa-check";
                $modal.load(
                    'hospitaldepartmentmodaldelete.php', {
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
                    'hospitaldepartmentmodaldelete.php', {
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
});

function deleteHospitalDepartmentConfirm(eleid, hiddendata) {
    $('#delcheckmodal').modal('hide');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/hospitaldepartment.php?method=deletehospitaldepartment',
        data: {
            'data': eleid,
            'type': 'hospitaldepartment',
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
var validateDepartmentAdd = function () {
    console.log('parseley valid = ' + $('form#hospitaldepartmentadd').parsley().isValid());
    if (false === $('form#hospitaldepartmentadd').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        hospitaldepartmentadd();
    }
};
window.Parsley.on('field:error', function () {
    // This global callback will be called for any field that fails validation.
    console.log('Validation failed for: ', this.$element);
});

function hospitaldepartmentadd() {
    $("#hospitaldepartmentadd").ajaxForm({
        dataType: 'json',
        beforeSubmit: function (data) {},
        success: function (data) {
            if (data.sts > 0) {
                sToast(data.msg, 'Success');
                setTimeout(function () {
                    window.location = data.url;
                }, 1000);
            } else {
                eToast(data.msg, 'Error');
            }
        }
    }).submit();
}
