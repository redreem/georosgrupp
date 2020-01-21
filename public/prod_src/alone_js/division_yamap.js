(function () {
    $(document).ready(function () {


        var YandexMap = new initYandexMap(
            {   map_id: 'gmaps-map',
                use_cluster: true,
                limit_for_small_markers:7,
                disableScrollZoom:true,
                data: JSON.parse(FE.getData('placesOrganization')),
                templates:{
                    marker:{
                        title: "#FIRMNAME_ONLY#",
                        lat: "GEO_Y_Y",
                        lng: "GEO_Y_X",
                        url: "FIRMA_URL",
                        baloon:'<a href="#FIRMA_URL#" class="testClass"><span class="attachbaloonFirmName">#FIRMNAME_ONLY#</span><br><span>#ADRES_FIRM#</span><br><span></a>'
                    }
                },
                map_config: {
                    controls: ['fullscreenControl','zoomControl'],
                    zoom: 9,
                    //autoFitToViewport:'always',
                    type:'yandex#map',
                    center: [FE.getData('GEO_Y_Y'),FE.getData('GEO_Y_X')]
                }
            });



    });
}());

