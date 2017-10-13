$(document).ready(function () {
    $('#btnsubmit').on('click', function () {
        $('form#hospitaldoctoredit').parsley().validate();
        validateFront();
    });
});
var validateFront = function () {
    console.log('parseley valid = ' + $('form#hospitaldoctoredit').parsley().isValid());
    if (false === $('form#hospitaldoctoredit').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        hospitaldoctoredit();
    }
};
window.Parsley.on('field:error', function () {
    // This global callback will be called for any field that fails validation.
    console.log('Validation failed for: ', this.$element);
});

function hospitaldoctoredit() {
    $("#hospitaldoctoredit").ajaxForm({
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
