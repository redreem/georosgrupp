//ulogin

module.exports = {
    timerId: undefined,
    totalRuns: 0,
    initializedSelectors: [],

    init: function (uLoginParams, options) {

        var _this = this;
        _this.timerId = setInterval(function () {
            _this.totalRuns++;
            var uLoginElement = $('#' + options.selector);

            if (typeof window.uLogin !== 'undefined' && uLoginElement.length) {
                clearInterval(_this.timerId);
                setTimeout(function () {
                    window.uLogin.customInit(options.selector, uLoginParams);
                }, 300);
            }
            if (_this.totalRuns > 600) {
                clearInterval(_this.timerId);
            }
        }, 1000);

    }

};