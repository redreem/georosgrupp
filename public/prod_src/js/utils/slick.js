//Слик
require('slick-carousel');
require('slick-carousel/slick/slick.css');
require('slick-carousel/slick/slick-theme.css');

var slickAdded = false;
var elem;

module.exports = {

    addSliders: function(breakpoint, arrow) {

        $(elem).slick({
            responsive: [
                {
                    breakpoint: 1000,
                    settings: 'unslick'
                },
                {
                    breakpoint: breakpoint,
                    settings: {
                        mobileFirst: true,
                        infinite: false,
                        speed: 100,
                        slidesToShow: 1,
                        variableWidth: true,
                        arrows: arrow,
                        centerPadding: '60px',
                        focusOnSelect: true
                    }
                }
            ]
        });

        slickAdded = true;

    },

    moduleSlick: function(block, min, breakpoint, arrow) {

        var _this = this;
        elem = block;

        _this.addSliders(breakpoint, arrow);

        $(window).on('resize orientationchange', function() {
            if (window.matchMedia('(min-width: ' + min + 'px)').matches) {
                console.log('hide');
                $(elem).slick('unslick');
                slickAdded = false;
            } else {
                if (!slickAdded) {
                    _this.addSliders(breakpoint, arrow);
                    console.log('added sliders')
                }
                console.log('sliders already added');
            }
        });
    }
};
