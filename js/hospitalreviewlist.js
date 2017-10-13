$(document).ready(function () {
    $(document).on("change", "select#department", function () {
        var department = $('#department').val();
        var hospitalid = $('#hospitalid').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php?method=gethospitalreviewwithdept',
            data: {
                'department': department,
                'hospitalid': hospitalid
            }
        }).done(function (response) {
            $('#reviewlist').html(response).trigger('change');
        }).fail(function () {
            eToast('Something went wrong', 'Error');
        });
    });
    $(document).on("change", "#reviewlist", function () {
        compresstext();
    });
});
