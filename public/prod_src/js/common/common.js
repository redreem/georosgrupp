var common = {

    scrolltop_for_mobile_menu: 200,

    ///////////////SEARCH///////////////////////////
    search: function() {

        $('.searchImg').click(function() {
            $('#top-search-form').submit();
        });

    },

    /////////////////////MOBILE////////////////////////
    scroll_event: function(e) {
        var _t = this;
        var el = $("#breadcrumbsFloat");
        var scroll_top = $(window).scrollTop();

        if (scroll_top > _t.scrolltop_for_mobile_menu) {
            $("#breadcrumbsFloat").removeClass("fadeOut");
            $("#breadcrumbsFloat").addClass("fadeIn");
        } else {
            $("#breadcrumbsFloat").removeClass("fadeIn");
            $("#breadcrumbsFloat").addClass("fadeOut");

            $(".boxSoc").slideUp(150);
            $(".col-12 .showMenuSoc #cansel").fadeOut().removeClass("dsp-block");
            $("#btn_soc").fadeIn().removeClass("dsp-none");

            $(".showMenuBg").removeClass("on");
            $(el).find("#breadcrumbsStat").slideUp(150);

            $(".boxMore").slideUp(150);
            $(".col-12 .showMenuMore #cansel").fadeOut().removeClass("dsp-block");
            $("#btn_more").fadeIn().removeClass("dsp-none");
        }
    },

    mobile: function() {
        var _this = this;
        var el = $("#breadcrumbsFloat");

        $(window).on('scroll', function() {
            _this.scroll_event();
        });

        $('.burgerBtn').click(function() {
            $(this).toggleClass('on');
            $('#breadcrumbsStat').slideToggle(150);
            $('#breadcrumbsStat').css('display', 'block');
        });

        $('body').click(function() {
            $('#breadcrumbsStat').hide();
            $('.burgerBtn').removeClass('on');
        });

        $('#breadcrumbsStat, .burgerBtn, .burgerBtn').click(function(e) {
            e.stopPropagation();
        });

        $('.showMenuMore').click(function() {
            $('.showMenuSoc').find("#btn_soc").removeClass('dsp-none');
            $('.showMenuSoc').parents(el).find(".col-12 .showMenuSoc #cansel").removeClass('dsp-block');
            $('.showMenuSoc').parents(el).find(".boxSoc").hide();

            $('.showMenuBg').removeClass('on');
            $('.showMenuBg').parents(el).find("#breadcrumbsStat").hide();

            $(this).find("#btn_more").toggleClass('dsp-none');
            $(this).parents(el).find(".col-12 .showMenuMore #cansel").toggleClass('dsp-block');
            $(this).parents(el).find(".boxMore").slideToggle(300);

        });

        $('.showMenuSoc').click(function() {
            var show_menu_more = $('.showMenuMore');
            show_menu_more.find("#btn_more").removeClass('dsp-none');
            show_menu_more.parents(el).find(".col-12 .showMenuMore #cansel").removeClass('dsp-block');
            show_menu_more.parents(el).find(".boxMore").hide();

            $('.showMenuBg').removeClass('on');
            $('.showMenuBg').parents(el).find("#breadcrumbsStat").hide();

            $(this).find("#btn_soc").toggleClass('dsp-none');
            $(this).parents(el).find(".col-12 .showMenuSoc #cansel").toggleClass('dsp-block');
            $(this).parents(el).find(".boxSoc").slideToggle(300);

        });

        $('.showMenuBg').click(function() {
            var show_menu_more = $('.showMenuMore');
            show_menu_more.find("#btn_more").removeClass('dsp-none');
            show_menu_more.parents(el).find(".col-12 .showMenuMore #cansel").removeClass('dsp-block');
            show_menu_more.parents(el).find(".boxMore").hide();

            show_menu_more.find("#btn_soc").removeClass('dsp-none');
            show_menu_more.parents(el).find(".col-12 .showMenuSoc #cansel").removeClass('dsp-block');
            show_menu_more.parents(el).find(".boxSoc").hide();

            $(this).toggleClass('on');
            $(this).parents(el).find("#breadcrumbsStat").slideToggle(300);

        });
    },

    resizeWindow: function() {

        $(window).on('resize', function() {
            swiperInit();
            if (window.matchMedia('(max-width: 768px)').matches) {
                if (!$('.searchInp').hasClass('smallInp')) {
                    //$('.logoMobCont').hide();
                    $('.burgerBtn').hide();
                    //$('nav').css({'padding': '0px 0px'});

                } else {
                    $('nav').css({'padding': '0px 15px'});
                }

            } else {
                $('.logoMobCont').show();
                $('.burgerBtn').css({'display': 'flex'});
                //$('nav').css({'padding': '0px 30px'});
            }
        });

    },

    //Returns true if it is a DOM element
    isDOMElement: function(o) {
        return (
            typeof HTMLElement === "object" ? o instanceof HTMLElement : //DOM2
                o && typeof o === "object" && o !== null && o.nodeType === 1 && typeof o.nodeName === "string"
        );
    },

    getUrlParameter: function(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    },

    isVisible: function(elem) {
        if (this.isDOMElement(elem)) {
            var coords = elem.getBoundingClientRect();
            var windowHeight = document.documentElement.clientHeight;

            // верхняя граница elem в пределах видимости < нижняя граница видима
            var topVisible = coords.top > 0 && coords.top < windowHeight;
            var bottomVisible = coords.bottom < windowHeight && coords.bottom > 0;
            return topVisible || bottomVisible;
        }
    },
};

//var mySwiper;

$(document).ready(function() {

    common.search();
    common.mobile();
    common.resizeWindow();

    $('.btnUp').click(function() {
        $('html, body').animate({scrollTop: 0}, 'slow');
    });


    $('#sidebar_nav_buttons a.button').bind('click', function() {

        $('#sidebar_nav_buttons a.button').each(function() {

            $(this).removeClass('active');
        });

        $(this).addClass('active');
    });

    $('.navContainer #menu a.button').bind('click', function() {

        $('.navContainer #menu a.button').each(function() {

            $(this).removeClass('active');
        });

        $(this).addClass('active');
    })
});

window.openLink = function(url) {
    window.location.href = url;
};

DOMTokenList.prototype.containsAny = function(classes) {
    for (var i = 0; i < classes.length; i++) {
        var item = classes[i];

        if (this.contains(item)) {
            return true;
        }
    }
    return false;
};