$(document).ready(function () {
    $("#state").on("change", function () {
        var stateid = $("#state").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getdistrict',
                'value': stateid
            }
        }).done(function (response) {
            $("#district").html(response).trigger('change');
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $("#district").on("change", function () {
        var districtid = $("#district").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getpincode',
                'value': districtid
            }
        }).done(function (response) {
            $("#pincode").html(response).trigger('change');
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $("#pincode").on("change", function () {
        var pincodeid = $("#pincode").val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'commonprocess.php',
            data: {
                'method': 'getarea',
                'value': pincodeid
            }
        }).done(function (response) {
            $("#area").html(response).trigger('change');
        }).fail(function () {
            alert("<span style='color:red' >" + 'Something Went Wrong ....' + "</span>");
        });
    });
    $('#btnsubmit').on('click', function () {
        $('form#hospital_add').parsley().validate();
        validateFront();
    });
});
var validateFront = function () {
    if (false === $('form#hospital_add').parsley().isValid()) {
        console.log('parsley invalid');
        $('.bs-callout-info').addClass('hidden');
        $('.bs-callout-warning').removeClass('hidden');
    } else {
        console.log('parsley valid');
        $('.bs-callout-info').removeClass('hidden');
        $('.bs-callout-warning').addClass('hidden');
        hospital_add();
    }
};

function hospital_add() {
    $("#hospital_add").ajaxForm({
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
Parsley.addValidator('nicknameexist', {
    validateString: function (value) {
        var xhr = $.ajax({
            url: 'commonprocess.php?method=nicknameexisthospital&value=' + value,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This Shortname is already in use.");
        });
    }
});
Parsley.addValidator('fullnameexist', {
    validateString: function (value) {
        var xhr = $.ajax({
            url: 'commonprocess.php?method=fullnameexisthospital&value=' + value,
            dataType: 'json'
        });
        return xhr.then(function (json) {
            if (json == 1) return $.Deferred().reject("This Full name is already in use.");
        });
    }
});
//--------------------------------------maplocator----------------------------//
var map; //Will contain map object.
var marker = false; ////Has the user plotted their location marker? 
//Function called to initialize / create the map.
//This is called when the page has loaded.
function initMap() {
    //The center location of our map.
    var centerOfMap = new google.maps.LatLng(10.0037578, 76.3579401);
    //Map options.
    var options = {
        center: centerOfMap, //Set center.
        zoom: 10 //The zoom value.
    };
    //Create the map object.
    map = new google.maps.Map(document.getElementById('map'), options);
    //Listen for any clicks on the map.
    google.maps.event.addListener(map, 'click', function (event) {
        //Get the location that the user clicked.
        var clickedLocation = event.latLng;
        //If the marker hasn't been added.
        if (marker === false) {
            //Create the marker.
            marker = new google.maps.Marker({
                position: clickedLocation,
                map: map,
                draggable: true //make it draggable
            });
            //Listen for drag events!
            google.maps.event.addListener(marker, 'dragend', function (event) {
                markerLocation();
            });
        } else {
            //Marker has already been added, so just change its location.
            marker.setPosition(clickedLocation);
        }
        //Get the marker's location.
        markerLocation();
    });
}
//This function will get the marker's current location and then add the lat/long
//values to our textfields so that we can save the location.
function markerLocation() {
    //Get location.
    var currentLocation = marker.getPosition();
    //Add lat and lng values to a field that we can save.
    document.getElementById('lat').value = currentLocation.lat(); //latitude
    document.getElementById('lng').value = currentLocation.lng(); //longitude
}
//Load the map when the page has finished loading.
google.maps.event.addDomListener(window, 'load', initMap);
//--------------------------------------maplocator----------------------------//
