//Стики

module.exports = {

    sticky: function () {

        var top;

        if ($('.monopolist').is(':visible')) {
            top = 700;
        } else {
            top = 300;
        }

        if ($(window).scrollTop() >= top) {
            $('.firmMenuSticky').addClass('show');
            $('.btnUp').css({'display': 'block'});
            $('.underHeader').css('display', 'none')
        } else {
            $('.firmMenuSticky').removeClass('show');
            $('.btnUp').css({'display': 'none'});
            $('.underHeader').css('display', 'block')
        }

    },

    bindScroll: function () {

        var _this = this;

        if (window.matchMedia('(max-width: 768px)').matches) {
            //$(window).unbind('scroll');
        } else {
            $(window).bind('scroll', _this.sticky);
        }

    },

    moduleStickyMenu: function () {

        var _this = this;

        $(window).on('resize orientationchange', function () {
            if (window.matchMedia('(max-width: 768px)').matches) {
                $('.firmMenu').removeClass('firmMenuSticky');
            }
        });

        _this.bindScroll();

        $(window).on('resize orientationchange', function () {
            _this.bindScroll();
        });

    }

};