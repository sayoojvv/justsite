$(document).ready(function () {
    $(".fa-arrow-up,.fa-arrow-down").click(function () {
        var hospitalid = $('#hospitalid').val();
        var row = $(this).parents("tr:first");
        if ($(this).is(".fa-arrow-up")) {
            var prerow = $(this).parents("tr:first").prev();
            var order = row.attr('data-oid');
            var fid = row.attr('data-fid');
            var preorder = prerow.attr('data-oid');
            var prefid = prerow.attr('data-fid');
            var direction = 'up';
            console.log("order=" + order);
            console.log("featureid=" + fid);
            console.log("preorder=" + preorder);
            console.log("prefeatureid=" + prefid);
            if (prefid) {
                moveRow(order, fid, preorder, prefid, direction, row, hospitalid);
            }
        } else {
            //row.insertAfter(row.next());
            var nextrow = $(this).parents("tr:first").next();
            var order = row.attr('data-oid');
            var fid = row.attr('data-fid');
            var nextorder = nextrow.attr('data-oid');
            var nextfid = nextrow.attr('data-fid');
            var direction = 'down';
            console.log("order=" + order);
            console.log("featureid=" + fid);
            console.log("nextorder=" + nextorder);
            console.log("nextfid=" + nextfid);
            if (nextfid) {
                moveRow(order, fid, nextorder, nextfid, direction, row, hospitalid);
            }
        }
    });
    var hospitalid = $('#hospitalid').val();
    // Move the row up	
    $(document).on("click", ".removehospitalfeature", function () {
        var data = $(this).attr('data');
        var name = $(this).attr('name');
        console.log('data is :' + data);
        var hiddendata = $('#hiddendata').val();
        var eleid = data;
        var elenam = name;
        var eletype = 'hospitalfeature';
        var $modal = $('#delcheckmodal');
        var style = 'modal-header bg-green';
        var stsstyle = "fa fa-check";
        $modal.load(
            'hospitalfeaturemodaldelete.php', {
                'message': 'Confirm to Remove',
                'eleid': eleid,
                'elenam': elenam,
                'eletype': eletype,
                'style': style,
                'stsstyle': stsstyle,
                'sts': 1,
                'hiddendata': hiddendata
            },
            function () {
                $modal.modal('show');
            }
        );
    });
});

function deleteFeatureConfirm(eleid, hiddendata) {
    $('#delcheckmodal').modal('hide');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/hospitalfeature.php?method=deletefeature',
        data: {
            'data': eleid,
            'type': 'hospitalfeature',
            'hiddendata': hiddendata
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

function moveRow(order, fid, order1, fid1, direction, row, hospitalid) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/hospitalfeature.php?method=reorder&hospitalid=' + hospitalid,
        data: {
            'order': order,
            'fid': fid,
            'order1': order1,
            'fid1': fid1
        }
    }).done(function (response) {
        if (response.sts == 1) {
            location.reload(true);
        }
    }).fail(function () {
        eToast('Something went wrong', 'Error');
    });
}
