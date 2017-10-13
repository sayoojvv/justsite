$(document).ready(function () {
    $(".writereview").on("click", function () {
        var eleid = $("#docid").val();
        console.log(eleid);
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
    $('#btnsubmit').on('click', function () {
        $('form#doctoredit').parsley().validate();
        validateFront();
    });
});
var validateFront = function () {
    console.log('parseley valid = ' + $('form#doctoredit').parsley().isValid());
    if (false === $('form#doctoredit').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        doctoredit();
    }
};
window.Parsley.on('field:error', function () {
    // This global callback will be called for any field that fails validation.
    console.log('Validation failed for: ', this.$element);
});

function doctoredit() {
    var formData = new FormData($("form#doctoredit")[0]);
    //formData.append('image', $('input[type=file]')[0].files[0]);
    console.log(formData);
    $.ajax({
        url: 'scripts/doctoredit.php',
        type: 'POST',
        data: formData,
        async: false,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            if (data.sts == 1) {
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
Parsley.addValidator('emailchangeexist', {
    validateString: function (value) {
        var id = $('#doctorid').val();
        var xhr = $.ajax({
            url: 'commonprocess.php?method=emailchangeexist&value=' + value + '&usertype=member&id=' + id,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This email address already exists");
        });
    }
});
Parsley.addValidator('nicknamechangeexist', {
    validateString: function (value) {
        var id = $('#doctorid').val();
        var xhr = $.ajax({
            url: 'commonprocess.php?method=nicknamechangeexist&value=' + value + '&usertype=member&id=' + id,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This nickname is already in use.");
        });
    }
});
Parsley.addValidator('mobilechangeexist', {
    validateString: function (value) {
        var id = $('#doctorid').val();
        var xhr = $.ajax({
            url: 'commonprocess.php?method=mobilechangeexist&value=' + value + '&usertype=member&id=' + id,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This mobile number is already in use.");
        });
    }
});
