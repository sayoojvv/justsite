$(document).ready(function () {

    var table=$('#results').DataTable({ "order": [[ 0, "desc" ]] });
    table.column( 0 ).visible( false );
    $(".writereviewhosp").on("click", function () {
        var eleid = $(this).attr('data-val');
        var $modal = $('#writereview');
        $modal.load(
            'writereview.php', {
                'eleid': eleid
            },
            function () {
                $modal.modal('show');
            }
        );
    });
    $(".writereviewdiag").on("click", function () {
        var eleid = $(this).attr('data-val');
        var $modal = $('#writereview');
        $modal.load(
            'writereviewdiagnostic.php', {
                'eleid': eleid
            },
            function () {
                $modal.modal('show');
            }
        );
    });


    $(document).on("change", 'select#department', function () {
        var firm = $("#hospital_id").val();
        var department = $("#department").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getdocwithdept',
                'hospitalid': firm,
                'department': department
            }
        }).done(function (response) {
                $("#doctor").html(response);
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $(document).on("click", '#btnsubmithosp', function () {
        $('form#writereviewform').parsley().validate();
        validateFrontHosp();
    });

});



var validateFrontHosp = function () {
    console.log('parseley valid = ' + $('form#writereviewform').parsley().isValid());
    if (false === $('form#writereviewform').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        writereviewHosp();
    }
};
window.Parsley.on('field:error', function () {
    console.log('Validation failed for: ', this.$element);
});
function writereviewHosp() {
    var hospid = $("#hospital_id").val();
    var formData = new FormData($("form#writereviewform")[0]);
    $.ajax({
        url: 'scripts/writereviewprocess.php?method=hospital&firmid=' + hospid,
        type: 'POST',
        data: formData,
        async: false,
        dataType: 'json',
        success: function (data) {
            if (data.sts == 1) {
                $('#writereview').modal('toggle');
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

$(function () {
    $(document).on("click", '#btnsubmit', function () {
        $('form#writereviewform').parsley().validate();
        validateFrontDiag();
    });
});
var validateFrontDiag = function () {
    console.log('parseley valid = ' + $('form#writereviewform').parsley().isValid());
    if (false === $('form#writereviewform').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        writereviewdiag();
    }
};
window.Parsley.on('field:error', function () {
    console.log('Validation failed for: ', this.$element);
});
function writereviewdiag() {
    var diagnosticid = $("#diagnosticid").val();
    var formData = new FormData($("form#writereviewform")[0]);
    $.ajax({
        url: 'scripts/writereviewprocess.php?method=diagnostic&firmid=' + diagnosticid,
        type: 'POST',
        data: formData,
        async: false,
        dataType: 'json',
        success: function (data) {
            if (data.sts == 1) {
                $('#writereview').modal('toggle');
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
