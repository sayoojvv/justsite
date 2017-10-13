$(document).ready(function () {
    $(".submitheader").on("click", function () {
        var searchfield = $("#searchheaderfield").val();
        if (!searchfield) {
            if (placeholderIsSupported()) {
                $("#searchheaderfield").attr('placeholder', 'Enter a search word');
                $("#searchheaderfield").addClass('invalid');
            } else {
                $("#errmsgheader").text('Enter a search word');
            }
            $("#searchheaderfield").focus();
            return false;
        }
        $("searchheaderfield").attr("placeholder", "Search hospitals using name or place");
        if (placeholderIsSupported()) {
            $("#searchheaderfield").attr('placeholder', 'Search hospitals using name or place');
            $("#searchheaderfield").addClass('valid');
        } else {
            $("#errmsgheader").text('');
        }
        document.getElementById('search_locationheader').submit();
    });
    if ($('#setdistval').length == 0 && $('#setstateval').length == 0) {
        var loc_lng = "";
        var loc_lat = "";
        var watchID, geoLoc;
        loc_lat = geoplugin_latitude();
        loc_lng = geoplugin_longitude();
        if (navigator.geolocation) {
            var options = {
                timeout: 60000
            };
            geoLoc = navigator.geolocation;
            watchID = geoLoc.watchPosition(showPosition, errorHandler, options);
        } else {
            Ajax_detectdist(loc_lng, loc_lat)
        }
    };
    $('.selectdist').on('click', function () {
        var distid = $(this).attr('data-district-id');
        console.log('selected districtid=' + distid);
        setDistrict(distid);
    });
    $('.selectstate').on('click', function () {
        var stateid = $(this).attr('data-state-id');
        console.log('selected stateid=' + stateid);
        setState(stateid);
    });

    function showPosition(position) {
        loc_lat = position.coords.latitude;
        loc_lng = position.coords.longitude;
        stopWatch();
        Ajax_detectdist(loc_lng, loc_lat);
    }

    function errorHandler(err) {
        if (err.code == 1) {
            stopWatch();
            //alert('longitude11111 : ' + loc_lng + ' , latitude : ' + loc_lat);
            Ajax_detectdist(loc_lng, loc_lat);
        } else if (err.code == 2) {
            stopWatch();
            //alert('longitude111111 : ' + loc_lng + ' , latitude : ' + loc_lat);
            //Ajax_detectdist(loc_lng, loc_lat);
        }
    }

    function stopWatch() {
        geoLoc.clearWatch(watchID);
    }
});

function Ajax_detectdist(loc_lng, loc_lat) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/detectdistrict.php',
        data: {
            'latitude': loc_lat,
            'longitude': loc_lng
        }
    }).done(function (response) {
        //$("#setloc").text(response.district);
        //$("#setloc").attr('data-set-dist', response.districtid);
        //if(response.url!='none')  window.location = response.url;
        location.reload();
    }).fail(function () {
        alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
    });
}

function setDistrict(distid) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/detectdistrict.php?method=set',
        data: {
            'district': distid,
            'type': 'district'
        }
    }).done(function (response) {
        location.reload();
    }).fail(function () {
        alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
    });
}

function setState(stateid) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'scripts/detectdistrict.php?method=set',
        data: {
            'state': stateid,
            'type': 'state'
        }
    }).done(function (response) {
        location.reload();
    }).fail(function () {
        alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
    });
}
