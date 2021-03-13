admin = $("#admin_checkbox")
seller = $("#seller_checkbox")
admin_div = admin.children('div')
seller_div = seller.children('div')
admin_label = admin_div.children("label")
admin_input = admin_div.children("input[type=checkbox]")
seller_label = seller_div.children("label")
seller_input = seller_div.children("input[type=checkbox]")
admin_div.addClass("custom-control custom-checkbox")
seller_div.addClass("custom-control custom-checkbox")
seller_label.addClass("custom-control-label")
seller_input.addClass("custom-control-input")
seller_label.before(seller_input)
admin_label.addClass("custom-control-label")
admin_input.addClass("custom-control-input")
admin_label.before(admin_input)

let map;
let markers = [];

function initMap() {
    fetchLocationName($("#lat").val(), $("#lng").val());
    const uluru = {lat: 36.69858, lng: 10.14278};
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: uluru,
        mapTypeId: "terrain",
    });
    map.addListener("click", (event) => {
        alert("Latitude: " + event.latLng.lat() + " " + ", longitude: " + event.latLng.lng());
        deleteMarkers();
        addMarker(event.latLng);

        document.getElementById("edit_user_longitude").value = event.latLng.lng().toFixed(7);
        document.getElementById("edit_user_latitude").value = event.latLng.lat().toFixed(8);
        $("#lat").val(event.latLng.lat().toFixed(8));
        $("#lng").val(event.latLng.lng().toFixed(7));
        fetchLocationName(event.latLng.lat(), event.latLng.lng());
    });
}

function setMapOnAll(map) {
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

function clearMarkers() {
    setMapOnAll(null);
}

function showMarkers() {
    setMapOnAll(map);
}

function addMarker(location) {
    const marker = new google.maps.Marker({
        position: location,
        map: map,
    });
    markers.push(marker);
}

function deleteMarkers() {
    clearMarkers();
    markers = [];
}

// we take adminArea5 as neighborhood  and adminArea3 as county
async function fetchLocationName(lat, lng) {
    await fetch(
        'https://www.mapquestapi.com/geocoding/v1/reverse?key=HADD5t74PYRGaP3WeBDm36TWGYlRHTBQ&location=' + lat + '%2C' + lng + '&outFormat=json&thumbMaps=false',
    )
        .then((response) => response.json())
        .then((responseJson) => {
            let x = JSON.stringify(responseJson)
            console.log(
                //'ADDRESS GEOCODE is BACK!! => ' + x,
                'for me => ' + responseJson.results[0].locations[0].adminArea3 + ", " + responseJson.results[0].locations[0].adminArea5,
            );
            $loc = $("#loc");
            $loc.val(responseJson.results[0].locations[0].adminArea3 + ", " + responseJson.results[0].locations[0].adminArea5)
            $loc.html(responseJson.results[0].locations[0].adminArea3 + ", " + responseJson.results[0].locations[0].adminArea5);
        });
}