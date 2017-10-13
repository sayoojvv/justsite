$(document).ready(function () {
	
	$(document).on("click", ".removehtip", function () {
		var data = $(this).attr('data');
		var name = $(this).attr('name');
		console.log('data is :'+data);		          
		var eleid = data;
		var elenam = name;
		var eletype = 'faq';
		var $modal = $('#delcheckmodal');
		var style = 'modal-header bg-success';
		var stsstyle = "fa fa-check";                
		$modal.load(
			'htipmodaldelete.php',{'message': 'Confirm to Remove', 'eleid': eleid , 'elenam': elenam ,'eletype' : eletype, 'style' :style ,'stsstyle': stsstyle , 'sts' : 1},
			function(){
				$modal.modal('show');
			}
		);
	});
	
});

function deleteHtipConfirm(eleid){	
	$('#delcheckmodal').modal('hide');
	$.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'scripts/htip.php?method=deletehtip',
            data: {
                'data': eleid,
                'type': 'htip'
            }
        }).done(function (response) {            
			
            if (response.sts == 1) {
                sToast(response.msg , 'Success');
				setTimeout(function () {
					window.location = response.url;
				}, 1000);
            } else {
                eToast(response.msg , 'Error');
            }
        }).fail(function () {
            eToast('Something went wrong' , 'Error');
        });
	
	
}
