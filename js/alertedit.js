$(document).ready(function () {
    $(document).on("change", "#hospital-ac", function () {
        $("#department-ac").val('');
        $("#department").val('');
        getdepartments();
    });
    $('#btnsubmit').on('click', function () {
        $('form#alertedit').parsley().validate();
        validateFront();
    });
});

function getdepartments() {
    var hospital = $("#hospital-ac").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'commonprocess.php?method=gethospdepartments',
        data: {
            'hospital': hospital
        }
    }).done(function (response) {
        $("#department").html(response);
    }).fail(function () {
        eToast('Something went wrong', 'Error');
    });
}
var validateFront = function () {
    if (false === $('form#alertedit').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        alertedit();
    }
};
window.Parsley.on('field:error', function () {
    // This global callback will be called for any field that fails validation.
    console.log('Validation failed for: ', this.$element);
});

function alertedit() {
    $("#alertedit").ajaxForm({
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
