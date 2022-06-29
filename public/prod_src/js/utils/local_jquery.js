var $ = {};
if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
    $ = require('jquery');
} else {
    $ = window.$;
}

module.exports = $;