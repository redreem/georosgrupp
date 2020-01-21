module.exports = {

    fbshare: function () {
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(location.href), 'sharer', 'toolbar=0,status=0,width=626,height=436');
        return !1
    },

};