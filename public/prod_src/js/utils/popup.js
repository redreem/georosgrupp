//Попап
var mobile_detect = require('../utils/mobile_detect');
var popup_opinions = require('../utils/popup/opinions');

module.exports = {

    opinions: popup_opinions,

    rcrsvElmntSrch: function (element, compare) {
        var _this = this;
        if (element == null) {
            return false;
        }

        if (compare(element)) {
            return element;
        }

        return _this.rcrsvElmntSrch(element.parentElement, compare);

    },

    openPopup: function (options) {
        var is_mobile = !!mobile_detect.any();

        if (is_mobile) {
            $('body, html').addClass('freezePage');
        }

        $('.btnUp').css({'display': 'none'});
        $('body').css({'overflow-y': 'hidden'});
        if ($('.firmMenu').hasClass('firmMenuSticky')) {
            $('.firmMenuSticky').css({'display': 'none'});
        }
        $('.contWrap, nav').css({'filter': 'blur(3px)'});

        $('#popup').attr('style', 'position: fixed !important;');
        if (!$('#popup').hasClass('discountPopup')) {
            $('#popup').css({'background-color': 'rgba(0,0,0,.7)'});
        }

        if (typeof options != 'undefined' && typeof options.onOpen === 'function') {
            console.log('onOpen event');
            options.onOpen();
        }
    },

    arrowClicks: function (name, action) {
        var arrow_selectors = '.popup-arrow, .discountPopup .right, .discountPopup .left, .discountPage .right, .discountPage .left, .popupPrices .right, .popupPrices .left';
        var arrow_click_loaded = function () {
            var customscrollElements = $('.popupContentMainText_scroledPart, .popupContentTextMainText');
            customscrollElements.customScroll('destroy');
            customscrollElements.customScroll();
        };

        // arrow clicks
        $(document).on('click', arrow_selectors, function (e) {
            var modalWin = $('#modal_win');

            if (modalWin.length) {
                e.stopPropagation();
                e.preventDefault();

                if (name === 'discount_show' || name === 'price_show') {
                    var attr_href = $(this).attr('href') + '';
                    var module = modalWin.attr('data-module');
                    module = module ? '&module=' + module : '';
                    var url = attr_href + '&ajax=1&request_data_type=json&action=' + action + module;

                    modalWin.load(url, arrow_click_loaded);
                    window.history.pushState({
                        html: $('#modal_win').html(),
                        name: window.history.state.name,
                        href: window.history.state.href,
                        arrows_clicked: !0
                    }, '', attr_href);
                }

                if (name === 'opinion_answers') {
                    var attr_href = $(this).attr('href');
                    var url = attr_href + '&ajax=1&';

                    modalWin.load(url, function () {
                        var customscrollElements = $('.popupContentTextTimerContent');
                        customscrollElements.customScroll('destroy');
                        customscrollElements.customScroll();
                    });
                }
                return !1;
            }
        });
    },

    modulePopup: function (btn, name, action, url, type, popup_options) {
        var _this = this;
        var _location = location.href;

        if (typeof type === 'undefined' || type.length === 0) {
            type = 'get';
        }

        _this.arrowClicks(name, action);

        $(document).on('click', btn, function (event) {
            var t = $(this);
            console.log('popup: click');
            event.preventDefault();
            var target = $(event.target);
            var current_target = $(event.currentTarget);

            console.log(target);
            console.log(current_target);

            if (target.hasClass('disable_click')) {
                return false;
            }

            var attr = '';
            var module = '';
            var params = '';

            if ($(this).attr('data-url')) {
                attr = $(this).attr('data-url');
            }

            event.stopPropagation();

            if ($(this).attr('data-module')) {
                module = '&module=' + $(this).attr('data-module');
            }

            var dataParams = t.data('params');
            if (dataParams) {
                params = '&' + decodeURIComponent($.param(dataParams));
            }

            $.ajax({
                url: url + attr + module + params,
                data: {
                    ajax: 1,
                    request_data_type: 'json',
                    action: action
                },
                type: type,
                dataType: 'json',

                complete: function (data) {

                    var popup;

                    if (!$('#modal_win').length) {
                        popup = $('<div/>').attr('id', 'modal_win');
                        $('body').append(popup);
                    }

                    $('#modal_win').html(data.responseText);

                    window.history.pushState({
                        html: data.responseText,
                        name: name,
                        href: _location,
                        arrows_clicked: !1
                    }, '', url + attr);

                    _this.openPopup(popup_options);

                    console.log(name);

                    switch (name) {
                        case 'firm_add_photo':
                            _this.opinions.createSteps(name);
                            break;
                        case 'discount_show':
                            $('.popupContentTextMainText').customScroll();
                            if (module) {
                                $('#modal_win').attr('data-module', module);
                            }
                            break;
                        case 'opinion_create':
                            _this.opinions.createSteps();
                            break;
                        case 'opinion_add_comment':
                            var id_opinion = window.location.href.split('id_opinion=')[1].split('&')[0].split('#')[0];
                            _this.opinions.createSteps(name);
                            $('#popupContent').customScroll();
                            break;
                        case 'opinion_answers':
                            $('.popupContentTextTimerContent').customScroll();
                            break;
                        case 'price_show':
                            $('.popupContentMainText_scroledPart').customScroll();
                            break;
                        case 'story_show':
                            $('.popupContentMainTextText p').customScroll();
                            break;
                        case 'map_show':
                            $('.popupContentTextMainText').customScroll();
                            break;
                        default:
                            break;
                    }
                }
            });
            return false;
        });

        _this.bindEvents();
    },

    closePopup: function (e) {
        var _this = this;
        console.log('close');

        $('body, html').removeClass('freezePage');

        var clickedElem = _this.rcrsvElmntSrch(e.target, function (el) {
            return el.classList.containsAny(
                [
                    'popupContent',
                    'popupWrapp',
                    'pricePage',
                    'popupPage',
                    'popupContainer'
                ]
            );
        });

        if (!clickedElem) {
            $('#popup').css({'display': 'none'});
            $('.contWrap, nav').css({'filter': ''});
            $('body').css({'overflow-y': 'auto'});
            if ($('.firmMenu').hasClass('firmMenuSticky')) {
                $('.firmMenuSticky').css({'display': 'block'});
                $('.btnUp').css({'display': 'block'});
            }
            $('#modal_win').remove();

            localStorage.removeItem('id_discount');
            localStorage.removeItem('create_opinion');
            localStorage.removeItem('add_comment');
            localStorage.removeItem('add_photo');
            localStorage.removeItem('id_opinion');
            localStorage.removeItem('id_price');
            localStorage.removeItem('id_story');
            localStorage.removeItem('id_map');

            if (window.history.state.href) {
                window.history.pushState({
                    html: '',
                    name: window.history.state.name,
                    href: window.history.state.href,
                    arrows_clicked: !1
                }, '', window.history.state.href);
            }
        }

    },

    bindEvents: function () {
        var _this = this;
        $(window).bind('popstate', function (e) {
            console.log('popstate event');
            if (!e.state) {
                $('#popup').trigger('click');
            }
        });

        $(document).on('click', '#popup', function (e) {
            _this.closePopup(e);
        });
    }
};