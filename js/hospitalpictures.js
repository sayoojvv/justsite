$(document).ready(function () {
    $(document).on("click", ".removehospitalpicture", function () {
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
                'type': 'hospitalpicture'
            }
        }).done(function (response) {
            var eleid = data;
            var elenam = name;
            var eletype = 'hospitalpicture';
            var $modal = $('#delcheckmodal');
            if (response.sts == 1) {
                var style = 'modal-header bg-green';
                var stsstyle = "fa fa-check";
                $modal.load(
                    'hospitalpicturemodaldelete.php', {
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
                    'hospitalpicturemodaldelete.php', {
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

function deleteHospitalPictureConfirm(eleid, hiddendata) {
    $('#delcheckmodal').modal('hide');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/hospitalpicture.php?method=deletehospitalpicture',
        data: {
            'data': eleid,
            'type': 'hospitalpicture',
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
