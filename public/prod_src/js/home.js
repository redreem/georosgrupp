var home = {

}

var modal_win = {
    open: function(w, h, t, l) {

        if (typeof w !== 'undefined') {
            $('#modal_win').css({width: w, left: ($('body').width() - w) / 2});
        }

        if (typeof h !== 'undefined') {
            $('#modal_win').css({height: h, top: '20px'});
        }

        if (typeof t !== 'undefined') {
            $('#modal_win').css({top: t});
        }

        if (typeof l !== 'undefined') {
            $('#modal_win').css({left: l});
        }

        $('#modal_win').show();
    },
    close: function() {
        $('#modal_win').hide();
    },
    content: function(content) {
        $('#modal_win_content').html(content);
    }
};

function swiperInit() {

    var sweeperWidth;
    var sweeperHeight;

    if (window.matchMedia('(max-width: 768px)').matches) {


    }

    mySwiper = new Swiper ('.swiper-container', {

        direction: 'horizontal',
        loop: true,
        pagination: {
            el: '.swiper-pagination',
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        scrollbar: {
            el: '.swiper-scrollbar',
        },

        setWrapperSize: true,

        parallax: true,
        speed: 1500,
        delay: 5000,

        autoplay:true,

        on: {
            init: function () {

                swiperText.init();
            },

            slideChangeTransitionStart: function () {

                if (typeof swiperText != 'undefined') {
                    //swiperText.slide();
                }
            },
        },
    });
}
$(document).ready(function() {

    swiperInit();
});


