module.exports = {

    slide: function(first, second) {

        $(first).animate({
            opacity: 0,
        }, 100);
        $(first).hide(400);
        $(second).delay(300).show(300);

        $(second).animate({
            opacity: 1,
        }, 100);

    },

    validateInp: function() {

        var _this = this;

        var email = $('#mailInput').val();
        var name = $('#mailNameInput').val();

        if (email && name && _this.isValidEmailAddress(email)) {
            $('.btnNext').prop('disabled', false);
        } else {
            $('.btnNext').prop('disabled', true);
        }

    },

    isValidEmailAddress: function(emailAddress) {

        var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        return pattern.test(emailAddress);

    },

    readURL: function(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#imagepreview1, #secondImgPrev').prop('src', e.target.result).show();
            };

            reader.readAsDataURL(input.files[0]);
        }

    },

    check2Step: function() {

        if ($('input#check').is(':checked') && $('#opinionInpText').val() != '' && $('#heading').val() != '') {
            $('.seeComment').prop('disabled', false);
        } else {
            $('.seeComment').prop('disabled', true);
        }

        $('.opinionView h3').text($('#heading').val());
        $('.opinionViewText p').text($('#opinionInpText').val());

    },

    check3Step: function() {

        if ($('#secondImgPrev').prop('src') === '') {
            $('.opinionStep3addPhotoButtonsFinal').prop('disabled', true);
        } else {
            $('.opinionStep3addPhotoButtonsFinal').prop('disabled', false);
            console.log('active')
        }

    },

    enterClick: function() {
        $('#anonimName, #organizationName').val('');
        $('.btnNext').prop('disabled', true);

        if ($('input[type=radio][name=enter]:checked').val() === 'tmail') {
            $('.socialsIcons, #anonimName, #organizationName').prop('disabled', true);
            $('#mailInput, #mailNameInput').prop('disabled', false);
            $('#mailInput, #mailNameInput').keyup(function() {
                _this.validateInp();
            });
            $('.mailMarkNote').css({'display': 'block'});
            $('.uLogin').hide();
            $('#uLogin-placeholder').show();
        } else if ($('input[type=radio][name=enter]:checked').val() === 'anom') {
            $('#mailInput, #mailNameInput, #organizationName').val('');
            $('.socialsIcons, #mailInput, #mailNameInput, #organizationName').prop('disabled', true);
            $('#anonimName').prop('disabled', false);
            $('.mailMarkNote').css({'display': 'none'});
            $('#anonimName').keyup(function() {
                if ($('#anonimName').val()) {
                    $('.btnNext').prop('disabled', false);
                } else {
                    $('.btnNext').prop('disabled', true);
                }
            });
            $('.uLogin').hide();
            $('#uLogin-placeholder').show();
        } else if ($('input[type=radio][name=enter]:checked').val() === 'organization') {
            $('#mailInput, #mailNameInput, #anonimName').val('');
            $('#mailInput, #mailNameInput, .socialsIcons').prop('disabled', true);
            $('#organizationName').prop('disabled', false);
            $('#organizationName').keyup(function() {
                if ($('#organizationName').val()) {
                    $('.btnNext').prop('disabled', false);
                } else {
                    $('.btnNext').prop('disabled', true);
                }
            });
            $('.uLogin').hide();
            $('#uLogin-placeholder').show();
        } else {
            $('#mailInput, #mailNameInput, #anonimName, #organizationName').val('');
            $('.socialsIcons').prop('disabled', false);
            if ($('#social_serialized').val()) {
                $('.btnNext').prop('disabled', false);
            } else {
                $('.btnNext').prop('disabled', true);
            }
            $('#mailInput, #mailNameInput, #anonimName, #organizationName').prop('disabled', true);
            $('.mailMarkNote').css({'display': 'none'});
            $('#uLogin-placeholder').hide();
            $('.uLogin').show();
        }
    },

    enterChange: function() {
        $('.personBlockImg .imgMark').html('?');
        $('.personStatus').html('');

        switch ($('[name=enter]:checked').val()) {
            case 'anom':
                $('.personName, .author').html($('#anonimName').val());
                break;
            case 'soc':
                $('.personName, .author').html($('#social_name').val());
                var v = $('#social_img').val();
                if (v) {
                    $('.personBlockImg .imgMark').html('<img alt="" src="' + v + '">');
                }
                $('.personStatus').html($('#social_provider').val());
                break;
            case 'organization':
                $('.personName, .author').html($('#organizationName').val());
                break;
            case 'tmail':
                $('.personName, .author').html($('#mailNameInput').val());
                break;
        }
    },

    createSteps: function(popup) {

        var _this = this;

        var socialJustPosted = $('#social_just_posted');
        if (+socialJustPosted.val() === 1) {
            socialJustPosted.val('0');
            $('input[name=enter]#socials').click();
            _this.enterClick();
            _this.enterChange();
        }

        //////____________________________________step1_____________________________________________________________/////
        $('#mailInput, #mailNameInput').keyup(function() {
            _this.validateInp();
        });

        $('input[name=enter]').click(function(e) {
            _this.enterClick();
        });

        $('.btnNext').click(function() {
            _this.slide('.opinionStep1', '.opinionStep2');
            $('.seeComment').prop('disabled', true);
            _this.check2Step();
        });
        //////__________________________________step1end_____________________________________________________////

        ///////_________________________________step2________________________________________________________////////

        $('input#check').on('click', function() {
            _this.check2Step();
        });
        $('#opinionInpText').keyup(function() {
            _this.check2Step();
        });
        $('#heading').keyup(function() {
            _this.check2Step();
        });
        $('.seeComment').click(function() {
            if (popup == 'firm_add_photo' || popup == 'opinion_add_comment') {
                _this.check3Step();
            } else {
                $('.opinionView h3').text($('#heading').val());
                $('.opinionViewText p').text($('#opinionInpText').val());
            }
            _this.slide('.opinionStep2', '.opinionStep3noPhoto');
        });
        ///////_________________________________step2end______________________________________________________/////

        // region Step3
        //step3//
        $('.opinionStepBackBtn').click(function() {
            _this.slide('.opinionStep2', '.opinionStep1');
        });

        $('.opinionStep3ButtonsChange').click(function() {
            _this.slide('.opinionStep3noPhoto', '.opinionStep2');
            if ($('#imagepreview1').hasClass('visibleImgInp')) {
                $('#cancel').css('opacity', '1');
            }
        });
        // endregion

        ///////_________________________________step4_____________________________________________________///////
        $('.opinionStep3addPhotoButtonsFinal').click(function() {
            _this.slide('.popupWrapp, .popupContainerNav', '.opinionStepThanks, .popupContainerNav');
        });
        $('.opinionStep3ButtonsFinal').click(function() {
            var id_firm = FE.getData('id_firm');
            var id_net = FE.getData('id_net');
            var id_opinion = $('#popup').attr('data-opinion');
            var action = $('#popup').hasClass('popupAddComment') ? 'answer' : 'add';
            var heading = action === 'answer' ? '' : $('#heading').val();
            var text = $('#opinionInpText').val();
            var author = $('.author').text();
            var social_serialized = $('#social_serialized').val();
            var id_top;
            if (action === 'answer') {
                id_top = $('#popup').attr('data-top') === '1' ? '1' : '11';
            } else {
                id_top = $('[name=goodBadInp]:checked').val() === '1' ? '1' : '11';
            }
            var legal = $('[name=check]').is(':checked') ? '1' : '0';
            var contact = $('#mailInput').val();
            var image = $('#secondImgPrev').attr('src');
            if (!image || 'data:image/' !== image.substr(0, 11)) {
                image = '';
            }

            $.post('/opinions', {
                action: action,
                id_firm: id_firm,
                id_net: id_net,
                id_opinion: id_opinion,
                id_top: id_top,
                heading: heading,
                text: text,
                author: author,
                contact: contact,
                legal: legal,
                image: image,
                social_serialized: social_serialized,
                ajax: 1,
                request_data_type: 'json'
            }, function(data) {
                var json = data.content;
                var success = !!json.success;
                if (success) {
                    $('[data-id_text]').html(json.text_id);
                    _this.slide('.popupWrapp, .popupContainerNav', '.opinionStepThanks, .popupContainerNav');
                } else {
                    alert(json.message);
                }
            }, 'json');
        });

        ///////_________________________________step4end__________________________________________________///////

        $('[name=enter], #mailNameInput, #anonimName, #organizationName').change(function() {
            _this.enterChange();
        });

        $('#file').change(function() {
            _this.readURL(this);
            $('#file').val('');
            $('#imagepreview1, #cancel').css({'opacity': '1'});
            $('#imagepreview1').addClass('visibleImgInp');
            $('.uploadText, .findText, .findImgIcon, .filePhotoBtn, .noPhotoWrapp').hide();
            $('#noPhoto').addClass('responsiveImgCont');
        });

        $('.clipI').click(function() {
            $('#file').replaceWith($('#file').clone(true));
        });

        $('#cancel').click(function(e) {
            $('#file, #noPhotoI').val('');
            $('#imagepreview1, #secondImgPrev').attr('src', '');
            $('#imagepreview1, #cancel, #cancel1').css({'opacity': '0'});
            $('#imagepreview1').removeClass('visibleImgInp');
            $('.noPhotoWrapp').show();
            $('#noPhoto').removeClass('responsiveImgCont');
        });

        $('#noPhotoI').change(function() {
            _this.readURL(this);
            $('#noPhoto').addClass('responsiveImgCont');
            $('#secondImgPrev, #imagepreview1').css({'opacity': '1'});
            $('#imagepreview1').addClass('visibleImgInp');
            $('.opinionStep3addPhotoButtonsFinal').prop('disabled', false);
            $('.uploadText, .findText, .findImgIcon, .filePhotoBtn, .noPhotoWrapp').hide();
        });

        $('#noPhoto').click(function() {
            $('#noPhotoI').val('');
            $('#noPhotoI').replaceWith($('#noPhotoI').clone(true));
        });
    }
};