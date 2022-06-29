// соц сети
(function (window, undefined) {
    window.SocialShare = {};

    SocialShare.doPopup = function (e) {
        e = (e ? e : window.event);
        var t = (e.target ? e.target : e.srcElement),
            width = t.data - width || 800,
            height = t.data - height || 500;

        // popup position
        var
            px = Math.floor(((screen.availWidth || 1024) - width) / 2),
            py = Math.floor(((screen.availHeight || 700) - height) / 2);

        // open popup
        var popup = window.open(t.href, "social",
            "width=" + width + ",height=" + height +
            ",left=" + px + ",top=" + py +
            ",location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1"
        );

        if (popup) {
            popup.focus();
            if (e.preventDefault) e.preventDefault();
            e.returnValue = false;
        }

        return !!popup;
    };

    SocialShare.doUpdate = function (e) {
        e = (e ? e : window.event);

        var t = (e.target ? e.target : e.srcElement);
        var social = t.id;

        $.ajax({
            url: document.location.href,
            data: {
                ajax: 1,
                action: 'updateSocial',
                social: social,
                request_data_type: 'json'
            },
            dataType: 'json',
            type: 'POST',

            success: function (data) {

                var counter = data.social;
                if (social.length > 0) {
                    if (typeof data.social !== 'undefined') {

                        $("#" + social).find('span').text(counter[social]);
                        $(".med-14").text(counter['count']);
                    } else {

                        $("#" + social).find('span').text(counter[social]);
                        $(".med-14").text(counter['count']);
                    }
                }
            }
        });
    };
})(window);

