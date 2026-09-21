(function ($) {
    'use strict';

    function collectCategories() {
        var cats = [];

        $('.category_checked').each(function () {
            cats[cats.length] = $(this).attr('data-category-chpu');
        });

        return cats;
    }

    // Фрагмент упрощён из legacy main.js. Сейчас он неверно собирает часть типов полей.
    function collectPropertyValues() {
        var propertyMas = {};

        $('.name_select_rielt').each(function () {
            var $field = $(this);
            var propertyId = $field.attr('data-property');
            var $input = $field.find('input.ag_pole_good').first();
            var $select = $field.find('select.ag_pole_good').first();
            var value;

            if ($select.length) {
                value = $select.val();
            } else if ($input.length) {
                value = $input.val();
            } else {
                var checked = [];

                $field.find('input[type="checkbox"]:checked').each(function () {
                    var checkboxValue = $(this).siblings('.ckeck_param').attr('data-val');

                    if (checkboxValue !== undefined && checkboxValue !== '') {
                        checked[checked.length] = checkboxValue;
                    }
                });

                value = checked.join(':::');
            }

            if (value !== undefined && value !== '') {
                propertyMas[propertyId] = value;
            }
        });

        return propertyMas;
    }

    $('body').on('click', '.addgood_click', function () {
        var $button = $(this);

        $button.prop('disabled', true).text('Проверяем…');

        $.ajax({
            type: 'POST',
            url: './admin/ajax/Preview_Good_Payload.php',
            dataType: 'json',
            data: {
                cats: collectCategories(),
                property_mas: collectPropertyValues()
            },
            success: function (data) {
                $('.js-payload-preview').text(JSON.stringify(data, null, 2));
            },
            error: function () {
                $('.js-payload-preview').text('Не удалось проверить отправку.');
            },
            complete: function () {
                $button.prop('disabled', false).text('Проверить отправку');
            }
        });
    });
}(jQuery));
