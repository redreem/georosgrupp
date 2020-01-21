function initYandexMap(options, callback) {

    var _this = this;
    _this.options = {
        map_id: false,
        map: false,
        auto_center: true,
        use_cluster: false, // Использовать кластаризацию
        disableScrollZoom: false, // Отключить масштабирование колесиком
        limit_for_small_markers: 7, // При каком кол-ве маркеров будут показываться точки
        data: [], // Данные
        templates: {
            marker: {
                //title: "", // Шаблон из данных #ключ данных#
                lat: "GEO_Y_Y", // ключ данных отвечающий за lat
                lng: "GEO_Y_X", // ключ данных отвечающий за lng
                //url: "FIRMA_URL"  // ключ данных отвечающий за url
                //baloon: '' // Шаблон из данных #ключ данных#
            }
        },
        map_config: {
            zoom: 11,
            center: [33, 54]
        }
    }

    $.extend(_this.options, options);

    // Если обращается скриншетер отрубаем все contorl на карте
    if (typeof is_screen_shoter != 'undefined') {
        var options_screen_shoter =
        {
            controls: [],
            type: 'yandex#map'
        }

        $.extend(_this.options.map_config, options_screen_shoter);

    }

    _this.markersArray = []; // массив маркеров для адресов
    _this.decodeHtml = function (decoded) {

        var ta = document.createElement('textarea');
        ta.innerHTML = decoded;
        return ta.value;

    };

    _this.parseTemplate = function (item, template) {

        for (key in item) {
            template = template.replace('#' + key + '#', item[key]);
        }
        return _this.decodeHtml(template);
    };

    _this.setMarkers = function (locations) {

        var data = locations;

        // Удаляем все с карты и отчищаем массив маркеров
        _this.map.geoObjects.removeAll();
        _this.markersArray = [];

        for (var i = 0; i < data.length; i++) {

            var myLatLng = [data[i][_this.options.templates.marker.lat], data[i][_this.options.templates.marker.lng]];

            var image = {
                iconLayout: 'default#image',
                iconImageHref: '/prod_src/img/map-location-point.svg',
                iconImageSize: [33, 45],
                iconImageOffset: [-16, -45],
                iconContentOffset: [10, 10],
                hideIconOnBalloonOpen: false
            };

            if (data.length > _this.options.limit_for_small_markers) {
                image = {
                    iconLayout: 'default#image',
                    iconImageHref: '/prod_src/img/map-circle-marker.svg',
                    iconImageSize: [16, 16],
                    iconImageOffset: [-7, 0],
                    hideIconOnBalloonOpen: false
                };
            }

            var marker_options = {};

            // Если нету URL то показываем баллун
            if (!("url" in _this.options.templates.marker)) {
                if ("title" in _this.options.templates.marker) {
                    marker_options["hintContent"] = _this.decodeHtml(_this.parseTemplate(data[i], _this.options.templates.marker.title));
                }

                if ("baloon" in _this.options.templates.marker) {
                    marker_options["balloonContent"] = _this.decodeHtml(_this.parseTemplate(data[i], _this.options.templates.marker.baloon));
                }

            }

            var marker = new ymaps.Placemark(myLatLng, marker_options, image);

            // Если у маркеров нет id, то индифицируем его по счетчику
            if (!("id" in _this.options.templates.marker)) {
                marker.id = i;
            }
            else {
                marker.id = data[i][_this.options.templates.marker.id];
            }

            // Если есть URL то открываем страницу при клике
            if ("url" in _this.options.templates.marker) {
                marker.url = data[i][_this.options.templates.marker.url];
                marker.events.add('click', function (e) {
                    window.location.href = e.get('target').url;
                });
            }

            // Закидываем в массив маркеры
            _this.markersArray.push(marker);
            if (_this.options.use_cluster == false) {
                _this.map.geoObjects.add(marker);
            }

        }

        if (_this.options.use_cluster == true) {
            var myClusterer = new ymaps.Clusterer({minClusterSize: 5, gridSize: 64});
            myClusterer.add(_this.markersArray);
            _this.map.geoObjects.add(myClusterer);
        }

        // Автомасштаб и центрирование
        if (_this.options.auto_center == true) {
            _this.map.setBounds(_this.map.geoObjects.getBounds(), {
                checkZoomRange: true
            });
        }
    };

    // Показ балуна маркеара по id
    _this.showMarkerBalloon = function (marker_id) {

        _this.markersArray.forEach(function (marker) {
            if (marker.id * 1 == marker_id * 1) {
                marker.balloon.open();
            }
        });

    }

    // Постройка маршрута с автомасштабированием
    _this.route = function (opt) {


        if (!opt.from || !opt.to) {
            return false;
        }


        if (!opt.type) {
            opt.type = "auto";
        }

        var rout_option = {
            mapStateAutoApply: true,
            routingMode: opt.type
        }

        var from, to;
       // if (opt.type == "pedestrian") {
            rout_option.multiRoute = true;
            from = opt.from;
            to = opt.to;
       // }
       // else {
       //     from = {type: 'viaPoint', point: opt.from};
       //     to = {type: 'wayPoint', point: opt.to};
        //}

        _this.map.geoObjects.removeAll();
        ymaps.route([from, to], rout_option).done(function (route) {
            _this.map.geoObjects.add(route);
        });

    }


    // После установки маркеров карта готова
    _this.isReady = false;
    _this.ready = function (ready) {
        var timoutId = setInterval(function () {

            if (_this.isReady == true) {
                clearInterval(timoutId);
                if (typeof ready === "function") {
                    ready();
                }
            }
        }, 10);
    };


    // После того как готово API yandex карты, ставим маркеры
    ymaps.ready(function () {

        _this.map = new ymaps.Map(options.map_id, _this.options.map_config);

        if (_this.options.disableScrollZoom == true) {

            _this.map.behaviors.disable('scrollZoom');
        }

        // Запускаем функцию расстановки массива меток
        _this.setMarkers(_this.options.data);

        //_this.isReady = true;

        if (typeof callback === "function") {
            callback();
        }

        _this.isReady = true;

    });


    return _this;
}

