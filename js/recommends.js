$(document).ready(function () {
    $(document).on("change", "select#firmtype", function () {
        var firm = $('#firmtype').val();
        var firmtype = this.options[this.selectedIndex].getAttribute('data-type');
        console.log(firm);
        console.log(firmtype);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php?method=getrecommends',
            data: {
                'firm': firm,
                'firmtype': firmtype
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
