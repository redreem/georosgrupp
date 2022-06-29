//Меню Поделиться
module.exports = {

    asideButtons: function() {

        $('.clickBtn').click(function(e) {

            e.stopPropagation();

            if (!$(this).hasClass('activeClickBtn')) {
                $('.clickBtn').removeClass('activeClickBtn');
                $('#navWrap').removeClass('navWrapActive');
                $('.moreActionsContainer, .shareLinkContainer').css('opacity', '1');
                $(this).parent().css('opacity', '1');

                // if (window.matchMedia('(max-width: 768px)').matches) {
                //     $('.mobDots').css('display', 'block');
                //     $('.openDots').css('display', 'none');
                // } else {
                //     $('.mobDots').css('display', 'none');
                //     $('.openDots').css('display', 'block');
                // }
            
                $('.mobDots').css('display', 'none');
                $('.openDots').css('display', 'block');
                
                $('.openMore').css('display', 'block');
                $('.closeImg').css('display', 'none');
                $('.openMenu').slideUp('fast');

                $(this).addClass('activeClickBtn');
                $(this).find('.openImg').css('display', 'none');
                $(this).find('.closeImg').css('display', 'block');
                $(this).parent().find('.openMenu').slideDown('fast');
                $('#navWrap').addClass('navWrapActive');
                $('.moreActionsContainer, .shareLinkContainer').css('opacity', '0.5');
                $(this).parent().css('opacity', '1');
            } else {
                $('#navWrap').removeClass('navWrapActive');
                $(this).removeClass('activeClickBtn');
                $(this).find('.openImg').css('display', 'block');
                $('.moreActionsContainer, .shareLinkContainer').css('opacity', '1');

                // if (window.matchMedia('(max-width: 768px)').matches) {
                //     $('.openDots').css('display', 'none');
                //     $('.mobDots').css('display', 'block');
                // } else {
                //     $('.openDots').css('display', 'block');
                //     $('.mobDots').css('display', 'none');
                // }
                
                $('.openDots').css('display', 'block');
                $('.mobDots').css('display', 'none');

                $(this).find('.closeImg').css('display', 'none');
                $(this).parent().find('.openMenu').slideUp('fast');
            }

        });

        $(window).on('resize', function() {
            if ($('.openDots').css('display') == 'block') {
                // if(window.matchMedia('(max-width: 768px)').matches) {
                //     $('.mobDots').css('display', 'block');
                //     $('.openDots').css('display', 'none');
                // }

                $('.mobDots').css('display', 'none');
            }

            // if ($('.mobDots').css('display') == 'block') {
            //     if(!window.matchMedia('(max-width: 768px)').matches) {
            //         $('.mobDots').css('display', 'none');
            //         $('.openDots').css('display', 'block');
            //     }
            // }
        });

        $('body').click(function() {
            $('.moreActions, .shareLink').hide();
            $('.dotsBtn').removeClass('activeBtnDot');
            $('.redBtn').removeClass('activeBtnRed');
            $('#navWrap').removeClass('navWrapActive');
            $('.openMore').css('display', 'block');
            $('.clickBtn').removeClass('activeClickBtn');
            $('.closeMore, .closeDots').css('display', 'none');
            $('.moreActionsContainer, .shareLinkContainer').css('opacity', '1');

            // if (window.matchMedia('(max-width: 768px)').matches) {
            //     $('.mobDots').css('display', 'block');
            //     $('.openDots').css('display', 'none');
            // } else {
            //     $('.mobDots ').css('display', 'none');
            //     $('.openDots ').css('display', 'block');
            // }
               
            $('.mobDots ').css('display', 'none');
            $('.openDots ').css('display', 'block');
        });

        $('.moreActions, .shareLink').click(function(e) {
            e.stopPropagation();
        });
    }

}