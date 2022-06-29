//Параллакс
var number;

module.exports = {

    parallax: function() {

        var yPos = -($(window).scrollTop() / 3);
        var coords = 'center '+ yPos + 'px';
        var st = $(this).scrollTop();
        
        $('.victoryHeader').css({"backgroundPosition": coords});
        $('.victoryHeaderText').css({ 
            "-webkit-transform": "translate(0%," + st/number +"%)"
        });

    },

    bindScroll: function() {

        var _this = this;

        if (window.matchMedia('(max-width: 1230px)').matches) {
            $('.victoryHeader').css({"backgroundPosition": "center bottom"});
            $('.victoryHeaderText').css({ 
                "-webkit-transform": "translate(0%, 0%)"
            });
            $(window).unbind('scroll');
        } else {
            $(window).bind('scroll', _this.parallax);
        }

    },

    moduleParallax: function(num) {

        var _this = this;
        number = num;

        _this.bindScroll();
        
        $(window).on('resize orientationchange', function() {
            _this.bindScroll();
        });

    }

}