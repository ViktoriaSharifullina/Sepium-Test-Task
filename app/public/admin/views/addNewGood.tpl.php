<?php

// Фрагмент повторяет исторический подход админки: SQL и сборка HTML находятся в view.
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

$properties = db()->query("SELECT * FROM property_s WHERE cat_prop = '' ORDER BY sort_prop, id");
?>
<section class="scenario-column app-window">
    <header class="app-window-bar">
        <div class="app-window-brand"><span>S</span><strong>Demo CMS</strong></div>
        <div class="app-window-caption">
            <b>Здесь нужно починить</b>
            <span>Интерфейс товара</span>
        </div>
    </header>

    <div class="product-header">
        <div>
            <small>Редактирование товара</small>
            <h2>Настольная лампа Nordic</h2>
        </div>
        <span>Тестовый стенд</span>
    </div>

    <div class="editor">
        <div class="editor-grid">
            <section class="categories-panel">
                <div class="section-heading">
                    <h2>Категории</h2>
                    <p>Можно выбрать несколько.</p>
                </div>

                <div class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <label class="category-row add_good_name_category" data-category-id="<?php echo h($category['ID_category']); ?>" data-category-chpu="<?php echo h($category['chpu_category']); ?>">
                            <input class="js-category" type="checkbox" value="<?php echo h($category['ID_category']); ?>">
                            <span><?php echo h($category['name_category']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="properties-panel">
                <div class="section-heading properties-heading">
                    <div>
                        <h2>Характеристики</h2>
                        <p>HTML возвращает AJAX-обработчик.</p>
                    </div>
                </div>

                <div class="properties property_all" aria-live="polite">
                    <?php while ($property = $properties->fetch()): ?>
                        <?php echo property($property); ?>
                    <?php endwhile; ?>
                </div>
            </section>
        </div>

        <footer class="editor-footer">
            <span>Выбор категорий должен сразу обновлять содержимое <code>.property_all</code>.</span>
            <button class="addgood_click" type="button">Проверить отправку</button>
        </footer>

        <section class="payload-preview" aria-live="polite">
            <div>
                <b>Данные, которые получит PHP</b>
                <span>Записи в базу не выполняются</span>
            </div>
            <pre class="js-payload-preview">Нажмите «Проверить отправку»</pre>
        </section>
    </div>
</section>
