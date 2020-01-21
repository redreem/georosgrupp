(function () {
    $(document).ready(function () {

        
        var GoogleMap = new initGoogleMap(
            {   map_id: 'gmaps-map',
                use_cluster: true,
                limit_for_small_markers:7,
                data: JSON.parse(FE.getData('placesOrganization')),
                templates:{
                    marker:{
                        title: "#FIRMNAME_ONLY#",
                        lat: "GEO_Y_Y",
                        lng: "GEO_Y_X",
                        url: "FIRMA_URL",
	                    baloon:'<a href="#FIRMA_URL#" class="testClass"><span class="attachbaloonFirmName">#FIRMNAME_ONLY#</span><br><span>#ADRES_FIRM#</span><br><span></a>'
                    },
                },
                map_config: {
                    fullscreenControl: true,
                    mapTypeControl: false,
                    streetViewControl: false,
                    zoomControl: false,
                    clickableIcons: false,
                    zoom: 11,
                    center: {lat:FE.getData('GEO_Y_Y'),lng:FE.getData('GEO_Y_X')}
                }
            });


    });
}());

