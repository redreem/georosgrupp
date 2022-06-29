//Страница 404
if (module.hot) {
    module.hot.accept();
}

require('./common/common.js');

require('../styl/page404.styl');
require('../styl/utils/carousel.styl');

require('./utils/owl.carousel.js');

var news_manager = require('./_modules/news.js');

$(document).ready(function() {
    news_manager.show_news('#anons');
    news_manager.initCarousel();
});

