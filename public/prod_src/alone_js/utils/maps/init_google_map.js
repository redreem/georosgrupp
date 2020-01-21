function initGoogleMap(options) {

    var _this = this;
    _this.options = {
        map_id: false,
        map: false,
        use_cluster: false, // ������������ �������������
        limit_for_small_markers: 7, // ��� ����� ���-�� �������� ����� ������������ �����
        data: [], // ������
        templates: {
            marker: {
                //title: "", // ������ �� ������ #���� ������#
                lat: "GEO_Y_Y", // ���� ������ ���������� �� lat
                lng: "GEO_Y_X", // ���� ������ ���������� �� lng
                //url: "FIRMA_URL"  // ���� ������ ���������� �� url
				//baloon: '' // ������ �� ������ #���� ������#
            },
            
        },
        map_config: {
            fullscreenControl: true,
            mapTypeControl: true,
            streetViewControl: true,
            zoomControl: true,
            clickableIcons: true,
            zoom: 11,
            center: {lat:33,lng:54}
        }
    }

    $.extend(_this.options, options);

    // ���� ���������� ���������� �������� ��� contorl �� �����
    if(typeof is_screen_shoter != 'undefined')
    {
       var options_screen_shoter =
        {
            fullscreenControl: false,
            mapTypeControl: false,
            streetViewControl: false,
            zoomControl: false,
            clickableIcons: false
        }

        $.extend(_this.options.map_config, options_screen_shoter);

    }



    _this.map = new google.maps.Map(document.getElementById(options.map_id), _this.options.map_config);
    _this.infoWindow = new google.maps.InfoWindow();
    _this.markersArray = []; // ������ �������� ��� �������
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
    }

    _this.attachbaloon = function (marker, data_item) {


        var infowindow = new google.maps.InfoWindow({
            content: _this.parseTemplate(data_item, _this.options.templates.marker.baloon)
        });

        if (window.innerWidth <= 640) {

            google.maps.event.addListener(marker, 'click', function () {

                // _this.infowindow.close();
                // _this.infowindow.setContent(_this.parseTemplate(data_item, _this.options.templates.baloon));

                /* if ($this.currWindow) {
                 $this.currWindow.close();
                 }

                 $this.currWindow = infowindow;

                 if ($this.currMarker) {
                 $this.currMarker.setVisible(true);
                 }

                 $this.currMarker = marker;

                 infowindow.open(_this.map, marker);
                 marker.setVisible(false);*/
            });

        }

    };
    _this.setMarkers = function (locations) {

        var data = _this.options.data;

        if (_this.options.data.length > 0) {

            // ������� ���������� �������
            if (_this.markersArray) {
                for (i in _this.markersArray) {
                    _this.markersArray[i].setMap(null);
                }
            }

            // ���������� ������� ������ ��������
            var latlngbounds = new google.maps.LatLngBounds();

            for (var i = 0; i < data.length; i++) {
                var myLatLng = new google.maps.LatLng(data[i][_this.options.templates.marker.lat],data[i][_this.options.templates.marker.lng]);

                // ��������� ���������� ������� � ������� ��� ����������� ���������������
                latlngbounds.extend(myLatLng);

                var image = {
                    url: '/prod_src/img/map-location-point.svg',
                    size: new google.maps.Size(33, 45),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(10, 10)
                };

                if (data.length > _this.options.limit_for_small_markers) {
                    image = {
                        url: '/prod_src/img/map-circle-marker.svg'
                    };
                }
				
				var markerOptions = {
                    position: myLatLng,
                    map: _this.map,
                    icon: image,
                    title: _this.decodeHtml(_this.parseTemplate(data[i], _this.options.templates.marker.title)),
                    url: data[i][_this.options.templates.marker.url],
                }
				
				if("url" in _this.options.templates.marker)
				{
					markerOptions["url"] = data[i][_this.options.templates.marker.url];
				}


                var marker = new google.maps.Marker(markerOptions);

				if("url" in _this.options.templates.marker)
				{
					google.maps.event.addListener(marker, 'click', function () {
						window.location.href = this.url;
					});
				}

                // ����� ����������� ������
				if("baloon" in _this.options.templates.marker)
				{
                 _this.attachbaloon(marker,data[i]);
				}

                // ���������� � ������ ��������, ����� ����� ���� �� ������� �����
                _this.markersArray.push(marker);
            }

            // ���������� � ������������ �����
            _this.map.setCenter(latlngbounds.getCenter(), _this.map.fitBounds(latlngbounds));

            if (_this.options.use_cluster == true) {
                var cluster = new MarkerClusterer(_this.map, _this.markersArray, {
                    maxZoom: 10,
                    zoomOnClick: true,
                    imagePath: '/prod_src/img/marker-cluster/m'
                });
            }

            // ����������� ����� ��� ��������������� ������ ��� �����������
            if(typeof is_screen_shoter != 'undefined') {
                var timout;
                $(window).resize(function () {
                    _this.map.setCenter(latlngbounds.getCenter(), _this.map.fitBounds(latlngbounds));
                    if (_this.options.use_cluster == true) {
                        clearTimeout(timout);
                        timout = setTimeout(function () {
                            cluster.clearMarkers();
                            cluster.addMarkers(_this.markersArray);
                        }, 200);
                    }
                });
            }
        }
  
    };

    // ��������� ������� ����������� ������� �����
    _this.setMarkers(_this.map, _this.data);

    return _this;
}
