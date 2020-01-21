(function () {
    $(document).ready(function () {

        var firma = JSON.parse(FE.getData('placesLocation'));

        var YaMap = new initYandexMap({
            map_id: 'gmaps-map-firm',
            disableScrollZoom: true,
            auto_center: false,
            data: [firma],
            templates: {
                marker: {
                    title: "#FIRMNAME_ONLY#",
                    lat: "GEO_Y_Y",
                    lng: "GEO_Y_X",
                    baloon: "<h3>#FIRMNAME_ONLY#</h3><br><span class='attachlocationAddress'>#ADRES_FIRM#</span>"
                }
            },
            map_config: {
                controls: ['fullscreenControl', 'zoomControl'],
                zoom: 13,
                type: 'yandex#map',
                center: [firma['GEO_Y_Y'], firma['GEO_Y_X']]
            }
        });


        // Построение маршрута до метро
        $('.subwayTitle').on('click', function () {

            $('.subwayTitle').removeClass('active');
            $(this).addClass('active');

            var metro_coors = $(this).data("coords");
            YaMap.ready(function () {
                YaMap.route({
                    from: [metro_coors.lat, metro_coors.lng],
                    to: [firma['GEO_Y_Y'], firma['GEO_Y_X']],
                    type: "pedestrian"
                });
            });

        });

        // При клике на вкладках ставим фирму в исходное положение
        $('.tab-link').on('click', function () {

            $('.subwayTitle').removeClass('active');
            YaMap.ready(function () {
                YaMap.map.setCenter(YaMap.options.map_config.center, YaMap.options.map_config.zoom);
                YaMap.setMarkers(YaMap.options.data);
            });
        });

        // Маршрут по названию
        $('.routeBtn').click(function () {

            if (!$(this).hasClass('disabled')) {
                YaMap.ready(function () {
                    YaMap.route({
                        from: $('#startPoint').val(),
                        to: [firma['GEO_Y_Y'], firma['GEO_Y_X']]
                    })
                });
            }
        });

    });
}());

