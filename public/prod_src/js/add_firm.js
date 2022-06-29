//Добавление фирмы
if (module.hot) {
    module.hot.accept();
}

require('./common/common.js');
require('../styl/add_firm.styl');

var add_firm = {

    formActions: function () {

        // Активации кнопки в зависисмости от заполненности всех полей
        function check_all_required() {
            // Если краткая форма
            var required_num = 1;

            // Если полная форма
            if ($('#show_full_form').is(':checked')) {
                required_num = $('#addFirm .required').length;
            }

            // Сравниваем кол-во заполенныйх обязательных полей с общим кол-вом обязательных
            $('#addFirm .required').each(function () {
                if ($(this).val().replace(/[ ]/g, '').length != 0) {
                    required_num--;
                }
                else {
                    //Если мултиполе, проверяем есть ли уже подобное заполненное поле
                    var has_value = false;

                    if ($(this).closest('.formMultiInput').length != 0) {
                        var name = $(this).attr('name');
                        $('#addFirm input[name="' + name + '"]').each(function () {
                            if ($(this).val().replace(/[ ]/g, '').length != 0) {
                                has_value = true;
                                return false;
                            }
                        });
                    }

                    // Если есть, то считаем, что оно заполнено
                    if (has_value == true) required_num--;

                }
            });

            if (required_num == 0) {
                $('.formBtn').prop('disabled', false);
            } else {
                $('.formBtn').prop('disabled', true);
            }
        }

        function isUrl(url) {
            if (url.indexOf('http://') == -1 && url.indexOf('https://') == -1) {
                url = 'http://' + url;
            }

            var re_weburl = new RegExp(
                "^" +
                "(?:(?:(?:https?|ftp):)?\\/\\/)" +
                "(?:\\S+(?::\\S*)?@)?" +
                "(?:" +
                "(?!(?:10|127)(?:\\.\\d{1,3}){3})" +
                "(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" +
                "(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})" +
                "(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" +
                "(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" +
                "(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" +
                "|" +
                "(?:" +
                "(?:" +
                "[a-z0-9\\u00a1-\\uffff]" +
                "[a-z0-9\\u00a1-\\uffff_-]{0,62}" +
                ")?" +
                "[a-z0-9\\u00a1-\\uffff]\\." +
                ")+" +
                "(?:[a-z\\u00a1-\\uffff]{2,}\\.?)" +
                ")" +
                "(?::\\d{2,5})?" +
                "(?:[/?#]\\S*)?" +
                "$", "i"
            );

            return re_weburl.test(url);

        }

        $('#show_full_form').click(function () {
            if ($(this).is(':checked')) {
                $('.hideBlock').show();
                $('#addFirm input[name="additional_url"]').removeClass("required");
                check_all_required();
            }
            else {
                $('.hideBlock').hide();
                $('#addFirm input[name="additional_url"]').addClass("required");
                check_all_required();
            }
        });

        $('.formMultiInputPlus').bind("click", function () {
            var item = $(this).closest('.formMultiInput');
            var clone_item = item.clone(true);
            clone_item.find("input").val('').removeClass('error');
            clone_item.find('.formInputEerrorMessage').remove();
            item.after(clone_item);
            check_all_required();
        });

        $('.formMultiInputMinus').bind("click", function () {
            var item = $(this).closest('.formMultiInput');
            var inp_name = item.find('.formInput').attr("name");

            var form = $(this).closest("form");
            var similar_items = form.find('.formInput[name="' + inp_name + '"]');

            if (similar_items.length == 1) {
                similar_items.eq(0).val('');
            }
            else {
                item.remove()
            }
            check_all_required();
        });

        $('#addFirm .required').keyup(function () {

            // Удаляем из телефона все лишнее
            $('#addFirm input[name="phone[]"]').each(function () {
                $(this).val($(this).val().replace(/[^\(\) \+0-9]/g, ""));
            });
            // Проверяем на заполнненость все обязательные поля для активации кнопки
            check_all_required();
        });

        // Добавление надписи под ошибочным полем
        function add_error_message(input) {
            input.addClass('error');
            var message = '<div class="formInputEerrorMessage">Поле "' + input.attr('placeholder') + '" заполнено неверно' + '</div>';
            if (input.closest('.formMultiInput').length != 0) {
                input.closest('.formMultiInput').append(message);
            }
            else {
                input.after(message);
            }
        }

        $('#addFirm').submit(function () {

            $('#addFirm input').removeClass('error');
            $('#addFirm .formInputEerrorMessage').remove();

            var has_error = false;

            // Проверяем каждое URL поле на корректность

            if ($('#show_full_form').is(':checked')) {
                $('#addFirm input[name="site[]"]').each(function () {
                    if (!isUrl($(this).val())) {
                        add_error_message($(this));
                        has_error = true;
                    }
                });
            } else {
                $('#addFirm input[name="additional_url"]').each(function () {
                    if (!isUrl($(this).val())) {
                        add_error_message($(this));
                        has_error = true;
                    }
                });
            }

            // Проверяем телефоны на корректность
            $('#addFirm input[name="phone[]"]').each(function () {
                var val_l = $(this).val().replace(/[^0-9]/g, "").length;
                if (val_l < 5 && val_l != 0) {
                    add_error_message($(this));
                    has_error = true;
                }
            });

            // Если нет ошибок проверяем капчу
            if (has_error == false) {

                grecaptcha.ready(function () {
                    grecaptcha.execute({action: 'addFirm'}).then(function (token) {

                        $.ajax({
                            url: '/firms?action=doAddFirm&ajax=1',
                            method: 'POST',
                            dataType: "json",
                            data: $('#addFirm').serialize()
                        }).done(function (data) {
                            if ('error' in data) {
                                alert(data.error);
                            }
                            else {
                                $('.firmAdditionContainer').hide();
                                $('.successMessage').show();
                                $('.requestNumber').text(data.message);

                                // Отчистка формы и закрытие окна через 15 секунд
                                setTimeout(function () {
                                    $('#addFirm').trigger('reset');
                                    $('.firmAdditionContainer').show();
                                    $('.successMessage').hide();
                                }, 15000);

                            }
                        }).fail(function (jqXHR, textStatus) {
                            alert("Request failed: " + textStatus);
                        });
                    });
                });
            }

            return false;
        });
    }
};

$(document).ready(function () {

    add_firm.formActions();

});
