$(document).ready(function () {
	tinymce.init({
	  selector: 'textarea#longdesc',
	  height: 300,
	  menubar: false,
	  plugins: [
		'advlist autolink lists link image charmap print preview anchor',
		'searchreplace visualblocks code fullscreen',
		'insertdatetime media table contextmenu paste code'
	  ],
	  toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
	  
	});
$('#btnsubmit').on('click', function () {
	console.log('clicked');
	$('form#htipedit').parsley().validate();
	validateHealthTip();
});
	
});


var validateHealthTip = function () {
    if (true === $('form#htipedit').parsley().isValid()) {
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        htipedit();
    } else {
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    }
};

function htipedit(){
	
	var longdesc = tinyMCE.get('longdesc').getContent();
	
	var formData = new FormData($("form#htipedit")[0]);
	formData.append('longdesc', longdesc);	
	
	$.ajax({
		url: 'scripts/htip.php?method=edithtip'
		, type: 'POST'
		, data: formData
		, async: false
		, dataType: 'json'
		, success: function (data) {
			console.log(data);
			if(data.sts>0){	
				sToast(data.msg , 'Success');
				setTimeout(function () {
					window.location = data.url;
				}, 1000);
			}
			else {
				eToast(data.msg , 'Error');
			}
		}
		, cache: false
		, contentType: false
		, processData: false
	});
	
	
}
