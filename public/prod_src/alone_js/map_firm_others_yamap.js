(function () {
    $(document).ready(function () {

        var firma = JSON.parse(FE.getData('placesLocation'));

        var balloon_template = "<div><a class='attachbaloonLink attachbaloonFirmName' href='#FIRMA_URL#'>#FIRMNAME_ONLY#</a><br><a class='attachbaloonLink' href='#FIRMA_MAP_URL#'>#ADRES_FIRM#</a><br><a class='attachbaloonLink' href='#FIRMA_URL#'>#OTZYVY#</a><br><span class='attachbaloonDistance'>#metrov#</span><span class='attachbaloonIcon'></span></div>";

        if (window.matchMedia('(max-width: 640px)').matches) {
            balloon_template = "<a class='attachbaloonMobLink' href='#FIRMA_URL#'><span class='attachbaloonFirmName'>#FIRMNAME_ONLY#</span><br><span>#ADRES_FIRM#</span><br><span>#OTZYVY#</span><br><span class='attachbaloonDistance'>#metrov#</span><span class='attachbaloonIcon'></span></a>";
        }

        var YaMap = new initYandexMap({
            map_id: 'gmaps-map-firm-others',
            use_cluster: false,
            auto_center: false,
            data: JSON.parse(FE.getData('placesOrganization')),
            templates:{
                marker:{
                    title: "#FIRMNAME_ONLY#",
                    id: "ID_FIRM",
                    lat: "GEO_Y_Y",
                    lng: "GEO_Y_X",
                    baloon: balloon_template
                }
            },
            map_config: {
                controls: ['fullscreenControl','zoomControl'],
                zoom: 13,
                type:'yandex#map',
                center: [firma['GEO_Y_Y'],firma['GEO_Y_X']]
            }
        },function(){

            // ѕоказываем балун дл€ выбранной фирмы после загрузки
            YaMap.showMarkerBalloon($('.organizationsItem.organizationsItemActive').data("idfirm"));
        });

        // ѕоказ балуна дл€ конкретной организации
        $('.organizationsItem').click(function(){
            var obj = $(this);
            $('.organizationsItem').removeClass('organizationsItemActive');
            obj.addClass('organizationsItemActive');
            YaMap.ready(function(){
                YaMap.showMarkerBalloon(obj.data("idfirm"));
            });

        });

    });
}());