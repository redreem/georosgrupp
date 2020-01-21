//ׁ‏זועû
if (module.hot) {
    module.hot.accept();
}

var common = require('./common/common.js');

require('../styl/stories.styl');
require('../styl/stories_popup.styl');
require('../styl/utils/jquery.custom-scroll.styl');
require('./utils/jquery.custom-scroll.js');

require('../styl/utils/carousel.styl');

require('../styl/modules/soc-ntw-menu.styl');

require('./utils/owl.carousel.js');
require('./_modules/social.js');

var news_manager = require('./_modules/news.js');
var sticky = require('./utils/sticky.js');
var slick = require('./utils/slick.js');
var popup = require('./utils/popup.js');

var id_firm = FE.getData('id_firm');

var stories = {

    cutText: function() {

        $('.textItem').each(function() {
            if ($(this).hasClass('cutted')) {
                return;
            }

            var content = $(this);
            var size = content.closest('.hoverBlockText').height() / 2;
            var text = content.text();

            if (text.length > size) {
                content.text(text.slice(0, size) + ' \u2026');
            }

            $(this).addClass('cutted');
        });

    },

    backEvent: function() {

        if (window.location.href.indexOf('id_story') !== -1) {
            $('.popupContentMainTextText p').customScroll();
            var id_story = window.location.href.split('id_story=')[1];
            localStorage.removeItem('id_story');
            window.onpopstate = function(event) {
                localStorage.setItem('id_story', id_story);
                window.location.href = window.location.href.split('#')[0];
            };
        }

        if (localStorage.getItem('id_story')) {
            var id = localStorage.getItem('id_story');
            $('[data-url="' + id + '"]').click();
        }

    },

    loader: function() {

        $(document).on('click', '#loader_button', function() {
            $('#loader_button').hide();
            $('#floatingBarsG').show();
            var page = parseInt($(this).attr('data-page')) || 1;
            $.get('/stories?ajax=1&id_firm=' + id_firm + '&action=load&page=' + page, function(r) {
                $('.loader').remove();
                $('.storiesItemsContainer').append(r);
                stories.cutText();
                popup.modulePopup('.storiesItemBlock', 'story_show', 'getStory', '/stories?id_firm=' + id_firm + '&id_story=');
            });
        });

    },

    likes: function() {

        $(document).on('click', '.stories-likes, .stories-dislikes', function() {
            var isLike = $(this).hasClass('stories-likes');
            var row = $(this).closest('.stories-likes-row');
            if (!row.length)
                return;
            var storyId = parseInt(row.attr('data-story-id')) || 0;
            if (!storyId)
                return;
            $.post('/stories', {action: 'like', id_story: storyId, is_like: (isLike ? 1 : 0)}, function(r) {
                r = parseInt(r) || 0;
                if (r > 0) {
                    $('.stories-likes-row[data-story-id=' + storyId + '] ' + (isLike ? 'span.stories-likes' : 'span.stories-dislikes')).html(r);
                }
            });
        });

    }
};

$(document).ready(function() {

    news_manager.img = 1;
    news_manager.limit = '0-5';
    news_manager.site = 7;
    news_manager.show_news('#anons');

    common.initSocials();

    var id_firm = window.location.href.split('id_firm=')[1].split('#')[0];
    sticky.moduleStickyMenu();
    slick.moduleSlick('.firmMenuMobItems ul', 769, 768, true);
    stories.cutText();
    popup.modulePopup('.storiesItemBlock', 'story_show', 'getStory', '/stories?id_firm=' + id_firm + '&id_story=');
    stories.backEvent();
    stories.loader();
    stories.likes();

});
