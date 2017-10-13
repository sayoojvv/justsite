$(document).ready(function () {
   $('#btnsubmit').on('click', function () {
    $('form#personadd').parsley().validate();
        validateFront();
    }); 


   $("#personphoto").change(function(){
        readURL(this);
    });
});

var validateFront = function () {
    if (false === $('form#personadd').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        personadd();
    }
};
function personadd() {
    $("#personadd").ajaxForm({
        dataType: 'json',
        beforeSubmit: function (data) {},
        success: function (data) {
            if (data.sts > 0) {
                /*setTimeout(function () {
                	window.location = data.url;
                }, 1000);*/
            } else {
                eToast(data.msg, 'Error');
            }
        }
    }).submit();
}
Parsley.addValidator('emailexist', {
    validateString: function (value) {
        var xhr = $.ajax({
            url: 'commonprocess.php?method=emailexist&value=' + value + '&usertype=doctor',
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This email address already exists");
        });
    }
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function (e) {
            $('#imgthumb').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

