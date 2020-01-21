module.exports = {

    geo_y_y: 0,
    geo_y_x: 0,
    placesData: [],
    pl: {},
    server_data: {},
    newgmap: null,
    newgmap2: null,
    markersArray: [], // массив маркеров для организаций
    markersArray2: [], // массив маркеров для выбранной организации
    markersArray3: [], // массив маркеров для локации
    config: {},
    map: null,
    map_bounds: null,
    dirDispl: null,
    dirSrvc: null,
    marker: null,
    infoWindow: null,
    subwayMarkers: {},
    orgMarkers: {},
    currWindow: false,
    currMarker: false,
    css_active_class: 'active',
    map_key: '',

    getPoints: function () {

        var $this = this;
        var temp_array = [];
        if ($this.placesData.length > 0) {

            var getOther = JSON.parse($this.placesData);

            for (var x in getOther) {
                temp_array.push([
                    getOther[x].ID_FIRM,
                    getOther[x].GEO_Y_Y,
                    getOther[x].GEO_Y_X,
                    getOther[x].URL_OTZYVY_FIRM,
                    getOther[x].FIRMNAME_ONLY,
                    getOther[x].ADRES_FIRM,
                    getOther[x].OTZYVY,
                    getOther[x].metrov,
                    getOther[x].FIRMA_URL,
                    getOther[x].FIRMA_MAP_URL
                ]);
            }
        }
        return temp_array;
    },

    setServerData: function () {

        var $this = this;

        $this.server_data.placesOrganization = $this.getPoints();
        $this.server_data.placesLocation = [$this.pl];
        // Координаты первой карты
        $this.server_data.coordsMap = {
            lat: $this.geo_y_y,
            lng: $this.geo_y_x
        };
        // Координаты второй карты
        $this.server_data.coordsMap2 = {
            lat: ($this.placesData.length > 0) ? $this.getPoints()[0].GEO_Y_Y : 0,
            lng: ($this.placesData.length > 0) ? $this.getPoints()[0].GEO_Y_X : 0
        };
        $this.server_data.coordsDestination = {
            lat: 55.759205,
            lng: 37.637164
        };

        return $this.server_data;

    },

    setConfig: function () {

        var $this = this;

        // DOM-объект, который отвечает за первую карту на странице
        $this.config.map = document.getElementById('gmaps-map');
        // DOM-объект, который отвечает за вторую карту на странице
        $this.config.map2 = document.getElementById('gmaps-map-2');
        // объект в виде {lat: Number, lng: Number} для задания "финальной точки" для посетителя (то есть адрес месторасположения заказчика)
        $this.config.destination = {
            lat: $this.server_data.coordsDestination.lat,
            lng: $this.server_data.coordsDestination.lng
        };
        // путь к файлу-иконке для использования в маршрутах (точка назначения и отправки)
        $this.config.routeMarker = '/prod_src/img/firms/map/location.svg';

        // текст для destination
        // $this.config.destContent = '<h2>Денталис, стоматология</h2><p>Москва, Троиц, Центральная улица, 15б эт. 3</p>' +
        //     '<p>64 отзыва(25:39)</p><p>240 метров до метро</p>';

        // DOM-объект, который отвечает за input-поле, для ввода адреса посетителем
        $this.config.addr = document.getElementById('startPoint');
        // DOM-объект, который отвечает за кнопку для нажатия при поиске маршрута между финальной точкой и местом, указанным посетителем
        $this.config.dir = document.querySelector('.routeBtn');
        // функция для задания текста в инфоокне, принимает на вход объект ответа DirectionService возвращает текст для инфоокна точки отправки посетителя
        $this.config.originInfoText = function (dirSrvcRspns) {
            return "<h3>" + dirSrvcRspns.start_address +
                "</h3><br><span class='distanceTimeText'>" + dirSrvcRspns.distance.text +
                " => " + dirSrvcRspns.duration.text + "</span>";
        };
        // DOM-объект, который отвечает за кнопку очистки маршрута
        $this.config.clearRoute = undefined;
        // DOM-объект, который отвечает за кнопку очистки маркеров метро
        $this.config.clearSubwayMarkers = undefined;
        // путь к файлу для иконки станций метро
        $this.config.subwayMarkersIcon = '/prod_src/img/firms/map/circleBig.svg';
        // объекты для удобного задания названий в списке, и координат списка станций метро
        // getInfoText - функция, принимает на вход DOM объект элемента списка и ответ от DirectionService, на выходе html-размеченный текст для инфоокна станции метро
        $this.config.subwayMarkersObjs = [
            {
                // name: server_data.subway[0].name, coords: {"lat": server_data.subway[0].coords.lat, "lng": server_data.subway[0].coords.lng},
                getInfoText: function (item, dirSrvcRspns) {
                    return "<h3>" + item.innerText + "</h3><br><span>" + dirSrvcRspns.routes[0].legs[0].duration.text + "</span>";
                }
            },
            {
                // name: server_data.subway[1].name, coords: {"lat": server_data.subway[1].coords.lat, "lng": server_data.subway[1].coords.lng},
                getInfoText: function (item, dirSrvcRspns) {
                    return "<h3>" + item.innerText + "</h3><br><span>" + dirSrvcRspns.routes[0].legs[0].duration.text + "</span>";
                }
            }
        ];
        // [DRIVING, WALKING, BICYCLING, TRANSIT]
        $this.config.travelMode = 'WALKING';
        // CSS селектор элементов списка с названиями станций метро при клике на которые появляется синие маркеры
        $this.config.listItemsSelector = '#tab-2 p .subwayTitle';
        $this.config.orgItemsSelector = '.organizationsItem';

        return $this.config;
    },

    loadScript: function (url) {

        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        script.setAttribute('src', url);
        document.body.appendChild(script);

    },

    decodeHtml: function (decoded) {

        var ta = document.createElement('textarea');
        ta.innerHTML = decoded;
        return ta.value;

    },

    attachbaloon: function (marker, metry, link, firmname, address, opinions, distance, firmlink, maplink) {

        var $this = this;

        var infoContent = "<div><a class='attachbaloonLink attachbaloonFirmName' href='" + firmlink + "'>" + $this.decodeHtml(firmname) + "</a><br><a class='attachbaloonLink' href='" + maplink + "'>" + $this.decodeHtml(address) + "</a><br><a class='attachbaloonLink' href='" + link + "'>" + $this.decodeHtml(opinions) + "</a><br><span class='attachbaloonDistance'>" + $this.decodeHtml(distance) + "</span><span class='attachbaloonIcon'></span></div>";

        if (window.matchMedia('(max-width: 640px)').matches) {
            infoContent = "<a class='attachbaloonMobLink' href='" + firmlink + "'><span class='attachbaloonFirmName'>" + $this.decodeHtml(firmname) + "</span><br><span>" + $this.decodeHtml(address) + "</span><br><span>" + $this.decodeHtml(opinions) + "</span><br><span class='attachbaloonDistance'>" + $this.decodeHtml(distance) + "</span><span class='attachbaloonIcon'></span></a>";
        }

        // (допинформация или id, широта, долгота, ссылка, название, адрес, отзывы, расстояние)
        var infowindow = new google.maps.InfoWindow({
            content: infoContent
        });

        // if (window.innerWidth <= 640) {

        google.maps.event.addListener(marker, 'click', function () {

            $this.clear();

            if ($this.currWindow) {
                $this.currWindow.close();
            }

            $this.currWindow = infowindow;

            if ($this.currMarker) {
                $this.currMarker.setVisible(true);
            }

            $this.currMarker = marker;

            infowindow.open(marker.get('map'), marker);
            document.querySelector('.organizationsItemActive').classList.remove('organizationsItemActive');
            for (var i = 0; i < $this.markersArray.length; i++) {
                var imgUrlBig = '';
                var imgSize = 23;

                if ($this.markersArray[i] === $this.currMarker) {
                    imgUrlBig = 'Big';
                    imgSize = 35;
                    document.querySelectorAll($this.config.orgItemsSelector)[i].classList.add('organizationsItemActive');
                    // $this.markersArray[i].setIcon('/prod_src/img/firms/map/circleBig.svg');
                }
                var image = {
                    url: '/prod_src/img/firms/map/circle' + imgUrlBig + '.svg',
                    size: new google.maps.Size(imgSize, imgSize),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(10, 10)
                };
                $this.markersArray[i].setIcon(image);
            }
            marker.setVisible(false);

        });
    },

    attachlocation: function (marker, firmname, address) {

        var $this = this;

        //(допинформация или id, широта, долгота, название, адрес)
        var infowindow = new google.maps.InfoWindow({
            content: "<h3>" + $this.decodeHtml(firmname) + "</h3>" + "<br>" + "<span class='attachlocationAddress'>" + $this.decodeHtml(address) + "</span>"
        });

        marker.addListener('click', function () {
            $this.clearSMarkers();
            $this.refreshOrgMarkers();
            infowindow.open(marker.get('map'), marker);
            for (var i in $this.markersArray3) {
                $this.markersArray3[i].infoWindow = infowindow;
            }
        });
    },

    setMarkers: function (map, locations, active, start) {

        var $this = this;

        if ($this.server_data.placesOrganization.length > 0) {

            // Удаляем предыдущие маркеры
            if ($this.markersArray) {
                for (i in $this.markersArray) {
                    $this.markersArray[i].setMap(null);
                }
            }

            $this.markersArray = [];

            //Определяем область показа маркеров
            var latlngbounds = new google.maps.LatLngBounds();

            for (var i = 0; i < locations.length; i++) {
                var myLatLng = new google.maps.LatLng(locations[i][1], locations[i][2]);

                //Добавляем координаты маркера в область для дальнейшего масштабирования
                latlngbounds.extend(myLatLng);

                var imgUrlBig = '';
                var imgSize = 23;

                // для активной организации
                if (i === active) {
                    imgUrlBig = 'Big';
                    imgSize = 35;
                }

                var image = {
                    url: '/prod_src/img/firms/map/circle' + imgUrlBig + '.svg',
                    size: new google.maps.Size(imgSize, imgSize),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(10, 10)
                };

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    icon: image,
                    title: $this.decodeHtml(locations[i][4])
                });

                //Задаём функцию на клик (допинформация или id, широта, долгота, ссылка, название, адрес, отзывы, расстояние, ссылка на фирму)
                $this.attachbaloon(marker, locations[i][0], locations[i][3], locations[i][4], locations[i][5], locations[i][6], locations[i][7], locations[i][8], locations[i][9]);

                if (i === active) {
                    $this.currMarker = marker;
                }

                //Закидываем в массив маркеров, чтобы можно было удалить потом
                $this.markersArray.push(marker);
            }

            if (!start) {
                new google.maps.event.trigger($this.currMarker, 'click');
            }

            //Центрируем и масштабируем карту
            map.setCenter(latlngbounds.getCenter(), map.fitBounds(latlngbounds));

            //Меняем масштаб, если уж слишком близко, и только когда фирм мало

            // if ($this.server_data.placesOrganization.length < 3) {
            //     var listener = google.maps.event.addListener(map, "idle", function() {
            //         if (map.getZoom() > 12) map.setZoom(12);
            //         google.maps.event.removeListener(listener);
            //     });
            // }
        }

    },

    setLocation: function (map, locations) {

        var $this = this;
        var map_id = map.getDiv().id;

        $this.map_bounds = new google.maps.LatLngBounds();

        if (locations.length > 0) {

            //Определяем область показа маркеров
            var latlngbounds = new google.maps.LatLngBounds();

            for (var i = 0; i < locations.length; i++) {
                var myLatLng = new google.maps.LatLng(locations[i][0], locations[i][1]);

                //Добавляем координаты маркера в область для дальнейшего масштабирования
                latlngbounds.extend(myLatLng);
                if (map_id === 'gmaps-map') {
                    $this.map_bounds.extend(myLatLng);
                }

                var image = {
                    url: '/prod_src/img/firms/map/location.svg',
                    size: new google.maps.Size(33, 45),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(10, 10)
                };

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    icon: image,
                    title: $this.decodeHtml(locations[i][2])
                });

                //Задаём функцию на клик
                //(допинформация или id, широта, долгота, урл, название, описание, адрес, телефон)
                $this.attachlocation(marker, locations[i][2], locations[i][3]);

                //Закидываем в массив маркеров, чтобы можно было бы удалить потом
                $this.markersArray3.push(marker);
            }

            //Центрируем и масштабируем карту
            map.setCenter(latlngbounds.getCenter(), map.fitBounds(latlngbounds));

            //Меняем масштаб, если уж слишком близко, и только когда фирм мало
            if (locations.length < 3) {
                var listener = google.maps.event.addListener(map, "idle", function () {
                    if (map.getZoom() > 12) map.setZoom(15);
                    google.maps.event.removeListener(listener);
                });
            }
        }
    },

    clickedItems: function (selector, map) {

        var $this = this;

        var listItems = document.querySelectorAll(selector),
            markers = $this.subwayMarkers,
            dirSrvc;

        var currentPosition = {
            lat: $this.geo_y_y,
            lng: $this.geo_y_x
        };

        for (var i = 0; i < listItems.length; i++) {
            !function (i) {

                listItems[i].addEventListener("click", function () {
                    if (selector === $this.config.orgItemsSelector) {
                        $this.clear(true);
                        for (var j in $this.markersArray) {
                            $this.markersArray[j].setMap(null);
                        }
                        $this.setMarkers(map, $this.server_data.placesOrganization, i, false);
                    } else {
                        // click on subway title
                        $this.clear();

                        var clicked_items = $(listItems);
                        var clicked_item = $(listItems[i]);
                        clicked_items.removeClass($this.css_active_class);
                        clicked_item.addClass($this.css_active_class);


                        for (var key in markers) {
                            markers[key].infoWindow.close();
                            markers[key].marker.setVisible(false);
                        }

                        var clicked_position = new google.maps.LatLng(JSON.parse(listItems[i].dataset.coords));

                        $this.fitBoundsToVisibleMarkers(clicked_position);
                        if (markers[listItems[i].dataset.coords]) {
                            map.setCenter(JSON.parse(listItems[i].dataset.coords));
                            new google.maps.event.trigger(markers[listItems[i].dataset.coords].marker, 'click');
                            $this.fitBoundsToVisibleMarkers(clicked_position);
                            return;
                        }

                        dirSrvc = new google.maps.DirectionsService();

                        dirSrvc.route({
                            origin: JSON.parse(listItems[i].dataset.coords),
                            destination: currentPosition,
                            travelMode: $this.config.travelMode
                        }, function (response, status) {
                            if (status !== google.maps.GeocoderStatus.OK) {
                                console.log('Google maps routing failed');
                                return;
                            }

                            markers[listItems[i].dataset.coords] = {
                                marker:
                                    new google.maps.Marker({
                                        position: JSON.parse(listItems[i].dataset.coords),
                                        map: map,
                                        animation: google.maps.Animation.DROP,
                                        icon: $this.config.subwayMarkersIcon
                                    }),
                                infoWindow: new google.maps.InfoWindow({
                                    content: $this.config.subwayMarkersObjs[0].getInfoText(listItems[i], response)
                                    // content: $this.config.subwayMarkersObjs[i].getInfoText(listItems[i], response)
                                })
                            };

                            markers[listItems[i].dataset.coords].marker.addListener('click', function () {
                                markers[listItems[i].dataset.coords].marker.setVisible(true);
                                markers[listItems[i].dataset.coords].infoWindow.open(map, markers[listItems[i].dataset.coords].marker);
                            });
                            new google.maps.event.trigger(markers[listItems[i].dataset.coords].marker, 'click');

                            //map.setCenter(JSON.parse(listItems[i].dataset.coords));
                        });
                    }
                });
            }(i);
        }
    },

    // center and bound markers
    fitBoundsToVisibleMarkers: function (position) {
        var $this = this;
        var bounds = new google.maps.LatLngBounds();

        for (var i in $this.markersArray3) {
            if ($this.markersArray3[i].getVisible()) {
                bounds.extend($this.markersArray3[i].getPosition());
            }
        }
        bounds.extend(position);
        $this.map.setCenter(bounds.getCenter());
        $this.map.fitBounds(bounds);

    },

    direct: function (origin) {

        var $this = this;

        $this.clearSMarkers();

        if ($this.dirDispl)
            $this.dirDispl.setMap(null);
        $this.dirDispl = new google.maps.DirectionsRenderer({suppressMarkers: true});
        $this.dirSrvc = new google.maps.DirectionsService();

        $this.dirDispl.setMap($this.map);

        var request = {
            origin: origin,
            destination: $this.config.destination,
            travelMode: $this.config.travelMode
        };

        $this.dirSrvc.route(request, function (response, status) {

            if (status === google.maps.DirectionsStatus.OK) {
                var d = response.routes[0].legs[0];
                $this.clear(true);

                marker = new google.maps.Marker({
                    position: origin,
                    map: $this.map,
                    animation: google.maps.Animation.DROP,
                    icon: $this.config.routeMarker
                });

                $this.infoWindow = new google.maps.InfoWindow({
                    content: $this.config.originInfoText(d)
                });

                marker.addListener('click', function () {
                    $this.infoWindow.open($this.map, marker);
                });
                new google.maps.event.trigger(marker, 'click');

                $this.dirDispl.setDirections(response);
            }
        });

    },

    clear: function (notAll) {

        var $this = this;

        if ($this.marker) $this.marker.setMap(null);
        if ($this.infoWindow) {
            $this.infoWindow.setMap(null);
            $this.infoWindow = null;
        }
        for (var i in $this.markersArray3) {
            if ($this.markersArray3[i].infoWindow) {
                $this.markersArray3[i].infoWindow.close();
            }
        }

        if (!notAll) {
            if ($this.dirDispl) $this.dirDispl.setMap(null);
        }
    },

    clearSMarkers: function () {

        var $this = this;

        for (var i in $this.subwayMarkers) {
            $this.subwayMarkers[i].marker.setMap(null);
            $this.subwayMarkers[i].infoWindow.setMap(null);
            $this.subwayMarkers[i].infoWindow = null;
            delete $this.subwayMarkers[i];
        }

    },

    refreshOrgMarkers: function () {

        var $this = this;

        for (var i in $this.markersArray) {
            $this.markersArray[i].setMap(null);
        }
        var orgs = document.querySelectorAll('.organizationsItem');
        var activeItem = 0;
        for (var i = 0; i < orgs.length; i++) {
            if (orgs[i].classList.contains('organizationsItemActive')) {
                activeItem = i;
            }
        }
        $this.setMarkers($this.newgmap2, $this.server_data.placesOrganization, activeItem, true);

    },

    initMap: function (data) {

        var $this = data;

        $this.map = new google.maps.Map($this.config.map, {
            center: new google.maps.LatLng($this.server_data.coordsMap.lat, $this.server_data.coordsMap.lng),
            // center: $this.config.destination,
            fullscreenControl: false,
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: false,
            clickableIcons: false,
            zoom: 18
        });

        $this.map_bounds = new google.maps.LatLngBounds();

        if ($this.config.map2) {
            $this.newgmap2 = new google.maps.Map($this.config.map2, {
                center: new google.maps.LatLng($this.server_data.coordsMap2.lat, $this.server_data.coordsMap2.lng),
                fullscreenControl: false,
                mapTypeControl: false,
                streetViewControl: false,
                zoomControl: false,
                clickableIcons: false,
                zoom: 18
            });

            // Запускаем функцию задания локации
            $this.setLocation($this.newgmap2, $this.server_data.placesLocation);
            // Запускаем функцию расстановки массива меток
            $this.setMarkers($this.newgmap2, $this.server_data.placesOrganization, 0, true);
        }
        $this.setLocation($this.map, $this.server_data.placesLocation);

        new google.maps.event.trigger($this.marker, 'click');
        if ($this.config.dir) {
            $this.config.dir.addEventListener("click", function () {
                new google.maps.Geocoder().geocode({'address': $this.config.addr.value}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        $this.direct(results[0].geometry.location);
                    }
                    else {
                        console.log('Geocode was not successful for the following reason: ' + status);
                    }
                });
            });
        }
        if ($this.config.clearRoute)
            $this.config.clearRoute.addEventListener("click", function () {
                $this.clear();
            });
        if ($this.config.clearSubwayMarkers)
            $this.config.clearSubwayMarkers.addEventListener("click", function () {
                $this.clearSMarkers();
            });

        $this.clickedItems($this.config.listItemsSelector, $this.map);
        $this.clickedItems($this.config.orgItemsSelector, $this.newgmap2);
    }

};