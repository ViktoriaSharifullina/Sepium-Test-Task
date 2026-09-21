(function ($) {
    'use strict';

    function collectPropertyValues() {
        var values = {};

        $('.name_select_rielt').each(function () {
            var $field = $(this);
            var propertyId = $field.attr('data-property');
            var $input = $field.find('input.ag_pole_good').first();
            var $select = $field.find('select.ag_pole_good').first();

            if ($select.length) {
                values[propertyId] = {
                    type: 'select',
                    value: $select.val()
                };
                return;
            }

            if ($input.length) {
                values[propertyId] = {
                    type: 'input',
                    value: $input.val()
                };
                return;
            }

            var checked = [];
            $field.find('input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')) {
                    checked[checked.length] = $(this).siblings('.ckeck_param').attr('data-val');
                }
            });

            values[propertyId] = {
                type: 'checkbox',
                value: checked
            };
        });

        return values;
    }

    function restorePropertyValues($properties, values) {
        $.each(values, function (propertyId, property) {
            var $field = $properties.find('.name_select_rielt').filter(function () {
                return $(this).attr('data-property') === propertyId;
            });

            if (!$field.length) {
                return;
            }

            if (property.type === 'select') {
                $field.find('select.ag_pole_good').val(property.value);
                return;
            }

            if (property.type === 'input') {
                $field.find('input.ag_pole_good').val(property.value);
                return;
            }

            $field.find('input[type="checkbox"]').each(function () {
                var value = $(this).siblings('.ckeck_param').attr('data-val');
                $(this).prop('checked', $.inArray(value, property.value) !== -1);
            });
        });
    }

    // Выбор категории в товаре и обновление блока характеристик.
    $('body').on('change', '.js-category', function () {
        var category = [];
        var $properties = $('.property_all');
        var propertyValues = collectPropertyValues();

        $(this).closest('.add_good_name_category')
            .toggleClass('category_checked is-selected', this.checked);

        $('.category_checked').each(function () {
            category[category.length] = $(this).attr('data-category-id');
        });

        $properties.addClass('is-loading').attr('aria-busy', 'true');

        $.ajax({
            type: 'POST',
            url: './admin/ajax/property/Refresh_Property_Good.php',
            dataType: 'html',
            data: { category: category },
        success: function (data) {
            if (data != 'no') {
                $properties.html(data);
                restorePropertyValues($properties, propertyValues);
            }
        },
            error: function () {
                $properties.html('<div class="error-state">Не удалось обновить характеристики.</div>');
            },
            complete: function () {
                $properties.removeClass('is-loading').attr('aria-busy', 'false');
            }
        });
    });
}(jQuery));
