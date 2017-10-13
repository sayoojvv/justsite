$(document).ready(function () {
    $(document).on("click", ".removealert", function () {
        var data = $(this).attr('data');
        var name = $(this).attr('name');
        console.log('data is :' + data);
        var eleid = data;
        var elenam = name;
        var eletype = 'alert';
        var $modal = $('#delcheckmodal');
        var style = 'modal-header bg-green';
        var stsstyle = "fa fa-check";
        $modal.load(
            'alertmodaldelete.php', {
                'message': 'Confirm to Remove',
                'eleid': eleid,
                'elenam': elenam,
                'eletype': eletype,
                'style': style,
                'stsstyle': stsstyle,
                'sts': 1
            },
            function () {
                $modal.modal('show');
            }
        );
    });
});

function deleteAlertConfirm(eleid) {
    $('#delcheckmodal').modal('hide');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/alert.php?method=deletealert',
        data: {
            'data': eleid,
            'type': 'alert'
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
