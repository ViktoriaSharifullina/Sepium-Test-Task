<?php

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/src/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

// Упрощённая обезличенная копия реального legacy-обработчика.
function property($property)
{
    $place = '';
    if ($property['place_prop'] != '') {
        $place = '<div class="field-help">' . h($property['place_prop']) . '</div>';
    }

    $idProp = (int) $property['id'];
    $allOption = '';

    if ($property['type_prop'] == '1') {
        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . h($property['name_prop']) . '</div>
            ' . $place . '
            <input type="text" class="text-input add-inp ag_pole_good" placeholder="' . h($property['name_prop']) . '">
        </div>';
    } elseif ($property['type_prop'] == '2') {
        $answers = db()->query(
            "SELECT * FROM property_answer_s WHERE id_prop = '" . $idProp . "' ORDER BY sort_answer"
        );

        while ($answer = $answers->fetch()) {
            $allOption .= '<option value="' . (int) $answer['id'] . '">' . h($answer['answer_prop']) . '</option>';
        }

        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . h($property['name_prop']) . '</div>
            ' . $place . '
            <select class="text-input ag_pole_good">
                <option value="">Не выбрано</option>' . $allOption . '
            </select>
        </div>';
    } elseif ($property['type_prop'] == '3') {
        $answers = db()->query(
            "SELECT * FROM property_answer_s WHERE id_prop = '" . $idProp . "' ORDER BY sort_answer"
        );
        $checkboxes = '';

        while ($answer = $answers->fetch()) {
            $checkboxes .= '<label class="choice line_chek">
                <input type="checkbox">
                <span class="ckeck_param" data-val="' . (int) $answer['id'] . '">' . h($answer['answer_prop']) . '</span>
            </label>';
        }

        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . h($property['name_prop']) . '</div>
            ' . $place . '
            <div class="choice-grid checkbox_property ag_pole_good">' . $checkboxes . '</div>
        </div>';
    } elseif ($property['type_prop'] == '4') {
        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . h($property['name_prop']) . '</div>
            ' . $place . '
            <input type="text" inputmode="decimal" class="text-input add-inp ag_pole_good" placeholder="Числовое значение">
        </div>';
    } else {
        $result = '';
    }

    return $result;
}

$category = isset($_POST['category']) && is_array($_POST['category']) ? $_POST['category'] : array();
$categoryIds = array();

foreach ($category as $categoryId) {
    if (is_scalar($categoryId)
        && filter_var($categoryId, FILTER_VALIDATE_INT) !== false
        && (int) $categoryId > 0
    ) {
        $categoryIds[] = (int) $categoryId;
    }
}
$categoryIds = array_values(array_unique($categoryIds));

$conditions = array("cat_prop = ''");
$parameters = array();
foreach ($categoryIds as $categoryId) {
    $conditions[] = 'FIND_IN_SET(?, cat_prop)';
    $parameters[] = $categoryId;
}

$properties = db()->prepare(
    'SELECT * FROM property_s WHERE ' . implode(' OR ', $conditions) . ' ORDER BY sort_prop, id'
);
$properties->execute($parameters);
$result = '';

while ($property = $properties->fetch()) {
    $result .= property($property);
}

echo $result === '' ? 'no' : $result;
