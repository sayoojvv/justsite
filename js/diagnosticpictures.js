$(document).ready(function () {
    $(document).on("click", ".removediagnosticpicture", function () {
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
                'type': 'diagnosticpicture'
            }
        }).done(function (response) {
            var eleid = data;
            var elenam = name;
            var eletype = 'diagnosticpicture';
            var $modal = $('#delcheckmodal');
            if (response.sts == 1) {
                var style = 'modal-header bg-green';
                var stsstyle = "fa fa-check";
                $modal.load(
                    'diagnosticpicturemodaldelete.php', {
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
                    'diagnosticpicturemodaldelete.php', {
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

function deleteDiagnosticCentrePictureConfirm(eleid, hiddendata) {
    $('#delcheckmodal').modal('hide');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/diagnosticpicture.php?method=deletediagnosticpicture',
        data: {
            'data': eleid,
            'type': 'diagnosticpicture',
            'hiddendata': hiddendata
        }
    }).done(function (response) {
        if (response.sts == 1) {
            sToast(response.msg, 'Success');
            console.log(response.url);
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
