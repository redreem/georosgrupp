module.exports = {
    site: 0,
    limit: '',
    img: 0,
    news_show: 0,

    show_news: function (id_wrap) {
        var _this = this;
        $.ajax({
            url: document.location.href,
            data: {
                ajax: 1,
                action: 'showNews',
                img: this.img,
                limit: this.limit,
                site: this.site,
                request_data_type: 'json'
            },
            dataType: 'json',
            type: 'POST',

            success: function (data) {

                if (typeof data.news != 'undefined') {

                    $(id_wrap).append(data.news);
                } else {

                    $(id_wrap).append(data.responseJSON.news);
                }

                _this.initCarousel();
            }
        });
    },

    initCarousel: function () {
        $('.un_hv').hover(
            function () {
                $(this).prev().addClass("opacity")
            }, function () {
                $(this).prev().removeClass("opacity")
            }
        );

        $("#owl-news").owlCarousel({
            items: 5,
            autoplay: false,
            loop: true,
            margin: 30,
            mouseDrag: false,
            singleItem: true,
            nav: false,
            dots: false,
            navText : ['<div class="btn-prev-n"><img src="/prod_src/img/firms/opinions/arrow-point-to-right.png"></div>',
            '<div class="btn-next-n"><img src="/prod_src/img/firms/opinions/arrow-point-to-right.png"></div>'],
            smartSpeed:1000,
            navSpeed: 1000,
            responsive: {
                0: {
                    items: 1.5,
                    margin: 20,
                    mouseDrag: true,
                    nav: false
                },
                480: {
                    items: 2,
                    mouseDrag: true,
                    nav: true
                },
                560: {
                    items: 3,
                    mouseDrag: true,
                    nav: true
                },
                768: {
                    items: 4,
                    mouseDrag: true,
                    nav: true
                },
                992: {
                    items: 5,
                    mouseDrag: true,
                    nav: true
                },
                1024:{
                    items:5,
                    nav: false
                },
            }
        });
    }
};