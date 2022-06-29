//—кидки
if (module.hot) {
    module.hot.accept();
}

var common = require('./common/common.js');

require('../styl/discounts.styl');
require('../styl/discounts_popup.styl');

require('./utils/jquery.custom-scroll.js');

require('../styl/utils/jquery.custom-scroll.styl');
require('../styl/utils/carousel.styl');

require('../styl/modules/soc-ntw-menu.styl');
require('../styl/modules/discount.styl');

require('./utils/owl.carousel.js');
require('./_modules/social.js');

var news_manager = require('./_modules/news.js');
var sticky = require('./utils/sticky.js');
var slick = require('./utils/slick.js');
var popup = require('./utils/popup.js');
var share = require('./library/share.js');

var discounts = {
    id_firm: 0,
    id_net: 0,
    popup_url_template: '',
    today_end: false,
    leave_end: false,
    already_end: false,
    views: false,
    detail: false,
    num: false,
    loaded_by_click: false,
    no_discounts: false,
    lock: false,

    disabled_class: 'disable_click',
    disabled_elements_for_click_selector:
        '.disk-mask, .discountsImage, .discountsBlockItem, .discountsButton, .discountsText, .discountsDescription',

    backEvent: function() {

        if (window.location.href.indexOf('id_discount') !== -1) {

            $('.popupContentTextMainText').customScroll();

            $('html, body').animate({scrollTop: 0}, 'fast');
            localStorage.removeItem('id_discount');
            window.onpopstate = function(event) {
                localStorage.setItem('id_discount', id_discount);
                window.location.href = window.location;
            };
        }

        if (localStorage.getItem('id_discount')) {
            var id = localStorage.getItem('id_discount');
            $('[data-url="' + id + '"]').click();
        }

        this.show_hide_button();
    },

    sendClickLog: function(id_discount) {

        $.ajax({
            url: '/discounts?id_discount=' + id_discount + '&click',
            data: {
                ajax: 1,
                action: 'sendClickLog'
            },
            dataType: 'json',
            type: 'POST',
            success: function(data) {
                console.log(data);
            }
        });

        return true;
    },

    /**
     * ѕровер€ет элемент на попадание в видимую часть экрана.
     * ƒл€ попадани€ достаточно, чтобы верхн€€ или нижн€€ границы элемента были видны.
     */

    isVisible: function(elem) {
        if (common.isDOMElement(elem)) {
            var coords = elem.getBoundingClientRect();
            var windowHeight = document.documentElement.clientHeight;

            // верхн€€ граница elem в пределах видимости < нижн€€ граница видима
            var topVisible = coords.top > 0 && coords.top < windowHeight;
            var bottomVisible = coords.bottom < windowHeight && coords.bottom > 0;
            return topVisible || bottomVisible;
        }
    },

    showVisible: function() {

        var $this = discounts;

        if ($this.isVisible($('#bottomOfDiscountsBlock').get(0)) && $this.loaded_by_click) {

            $this.scroll();
        }

        var imgs = $('.discountsImage img.bgImage');

        for (var i = 0; i < imgs.length; i++) {

            var img = imgs[i];
            var realsrc = img.getAttribute('realsrc');

            if (!realsrc) continue;

            if ($this.isVisible(img)) {
                img.src = realsrc;
                img.setAttribute('realsrc', '');
            }
        }
    },

    show_hide_button: function() {
        var count = $('.discountsBlock .discountsBlockItem').length;
        if (count < FE.getData('limit_count')) {
            $('#load').hide();
        }
    },

    scroll: function(event) {

        if (this.no_discounts || this.lock) {

            return;
        }

        this.lock = true;
        this.loaded_by_click = true;

        $('#load a.actionBtn').hide();

        var $this = this;

        //event.stopPropagation();

        $('#imgLoad').show();

        $.ajax({
            url: '/discounts?id_firm=' + this.id_firm,
            dataType: 'html',
            type: 'POST',
            data: {
                num: this.num,
                id_firm: this.id_firm,
                action: 'scrolling',
                ajax: 1
            },
            cache: false,
            success: function(response) {

                if (!response || response.length === 0) {

                    $this.no_discounts = true;

                    $('#imgLoad').hide();
                    $('#load').hide();

                } else {

                    $this.loaded_by_click = true;

                    $('#load').hide();
                    $('.discountsBlock').append(response);

                    $this.num = $this.num + FE.getData('limit_count');
                }

                //popup on
                popup.modulePopup(
                    '#module-discounts [data-url], .discountsTitle, .discountsButton button', 'discount_show',
                    'getdiscount',
                    '/discounts?id_discount='
                );
                $this.backEvent();

            },
            error: function(response) {
                console.log('error');
                console.log(response);
            },
            complete: function() {

                $this.lock = false;
            }

        });
    },

    initNews: function() {

        news_manager.img = 1;
        news_manager.limit = '0-5';
        news_manager.site = 7;
        news_manager.show_news('#anons');
    },

    initSocials: function() {

        common.initSocials();

        $('a.fbbtn').bind('click', function() {
            return share.fbshare();
        });
    },

    init: function() {

        this.id_firm = FE.getData('id_firm');
        this.id_net = FE.getData('id_net');
        this.today_end = FE.getData('today_end');
        this.leave_end = FE.getData('leave_end');
        this.already_end = FE.getData('already_end');
        this.views = FE.getData('views');
        this.detail = FE.getData('detail');
        this.popup_url_template = FE.getData('popup_url_template');
        this.num = FE.getData('limit_count');
    },

    resizeEvent: function() {
        var _this = this;
        var window_size = $(window).width();

        var disabled_elements_for_click = $(_this.disabled_elements_for_click_selector);
        if (window_size > 1024) {
            disabled_elements_for_click.addClass(_this.disabled_class);
        } else {
            disabled_elements_for_click.removeClass(_this.disabled_class);
        }
    },
    showAllFilials: function(e, cb) {
        var t = $(e.target);
        var filials_data_wrapper = t.closest('.filials_data_wrapper');
        filials_data_wrapper.find('.filials_data .priceSubsidiary').removeClass('hide');
        t.addClass('hide');
        if (typeof cb === 'function') {
            cb();
        }
    }
};

$(document).ready(function() {

    discounts.initNews();
    discounts.initSocials();
    discounts.init();

    var popupContentTextMainText = $('.popupContentTextMainText');

    $(document).on('click', '.' + discounts.disabled_class, function() {
        return false;
    });

    $('#load a.actionBtn').bind('click', function() {

        discounts.scroll(this.event);
        return false;
    });

    sticky.moduleStickyMenu();
    slick.moduleSlick('.firmMenuMobItems ul', 769, 768, true);

    var id_org_name = 'id_firm';
    var id_org = discounts.id_firm;
    if (discounts.id_net > 0) {
        id_org_name = 'id_net';
        id_org = discounts.id_net;
    }

    var popup_url = common.getTemplateString(discounts.popup_url_template, {
        id_org_name: id_org_name, id_org: id_org, id_discount: ''
    });

    if ($('#popupContainerWithArrows').hasClass('popupPage')) {
        console.log('no connect popup module');
    } else {
        popup.modulePopup(
            '#module-discounts [data-url], .module_discount,  .discountsTitle, .discountsButton, .discountsBlockItem',
            'discount_show',
            'getdiscount',
            popup_url
        );
    }

    $('.popupContentMainText_scroledPart').customScroll();

    if ($('#popup').hasClass('discountPage')) {

        $('.sectionBreadcrumbs').addClass('hiddenCrumbs');
    } else {

        $('.sectionBreadcrumbs').removeClass('hiddenCrumbs');
    }

    $('#imgLoad').hide();

    window.onscroll = discounts.showVisible;
    discounts.showVisible();
    discounts.backEvent();

    discounts.resizeEvent();

    $('div#owl-discounts').owlCarousel({
        items: 3,
        autoplay: false,
        loop: true,
        margin: 0,
        mouseDrag: true,
        singleItem: true,
        nav: true,
        dots: false,
        navText: ['<div class="btn-prev"><img src="/prod_src/img/firms/opinions/arrow-point-to-right.png"></div>',
            '<div class="btn-next"><img src="/prod_src/img/firms/opinions/arrow-point-to-right.png"></div>'],
        smartSpeed: 1000,
        navSpeed: 1000,
        responsive: {
            0: {
                items: 1.3,
                nav: false,
            },
            480: {
                items: 2,
                nav: false,
            },
            640: {
                items: 2,
                nav: true,
            },
            768: {
                items: 2,
                nav: true,
            },
            1024: {
                items: 3,
                nav: true,
            },
        }
    });

    $(document).on('click', '.show_all_filials', function(e) {
        popupContentTextMainText = $('.popupContentTextMainText');
        discounts.showAllFilials(e, function() {
            popupContentTextMainText.customScroll('destroy');
            popupContentTextMainText.customScroll();
        });
    });
});

$(window).resize(function() {
    discounts.resizeEvent();
});

module.exports = discounts;