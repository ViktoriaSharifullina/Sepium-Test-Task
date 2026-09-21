<?php

require_once '/var/www/app/src/bootstrap.php';

$passed = 0;
$failed = 0;

function check($condition, $message)
{
    global $passed, $failed;

    if ($condition) {
        $passed++;
        echo '[PASS] ' . $message . PHP_EOL;
        return;
    }

    $failed++;
    echo '[FAIL] ' . $message . PHP_EOL;
}

function requestPage($path, $body)
{
    $options = array(
        'http' => array(
            'method' => $body === null ? 'GET' : 'POST',
            'ignore_errors' => true,
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body === null ? '' : $body,
        ),
    );

    return file_get_contents(
        'http://127.0.0.1' . $path,
        false,
        stream_context_create($options)
    );
}

function requestProperties($categories)
{
    return requestPage(
        '/admin/ajax/property/Refresh_Property_Good.php',
        http_build_query(array('category' => $categories))
    );
}

function hasProperty($html, $propertyId)
{
    return strpos($html, 'data-property="' . $propertyId . '"') !== false;
}

function testAjaxContract()
{
    $response = requestProperties(array(1));
    check(
        strpos($response, 'property-field') !== false
            && strpos(ltrim($response), '{') !== 0,
        'AJAX-обработчик сохраняет контракт с готовым HTML-ответом.'
    );
}

function testEmptyCategory()
{
    $response = requestProperties(array());
    check(
        hasProperty($response, 1)
            && hasProperty($response, 6)
            && !hasProperty($response, 2)
            && !hasProperty($response, 3)
            && !hasProperty($response, 4)
            && !hasProperty($response, 5)
            && !hasProperty($response, 7),
        'Без категории возвращаются только общие характеристики.'
    );
}

function testFurnitureCategory()
{
    $response = requestProperties(array(1));
    check(
        hasProperty($response, 1)
            && hasProperty($response, 2)
            && hasProperty($response, 3)
            && hasProperty($response, 4)
            && hasProperty($response, 6)
            && !hasProperty($response, 5)
            && !hasProperty($response, 7)
            && strpos($response, 'inputmode="decimal"') !== false,
        'Для категории Мебель возвращаются общие, связанные и числовые характеристики.'
    );
}

function testMultipleCategories()
{
    $response = requestProperties(array(1, 2));
    check(
        substr_count($response, 'data-property="3"') === 1,
        'Характеристика, общая для нескольких категорий, не дублируется.'
    );
}

function testInvalidCategory()
{
    $response = requestProperties(array('1 OR 1=1', array('11')));
    check(
        hasProperty($response, 1)
            && hasProperty($response, 6)
            && !hasProperty($response, 2)
            && strpos($response, '&lt;текст&gt;') !== false,
        'Некорректные категории игнорируются, а текст из БД экранируется.'
    );
}

try {
    $tables = db()->query("SHOW TABLES LIKE 'property_s'")->fetchAll();
    $categories = db()->query('SELECT ID_category FROM category_s')->fetchAll();
    $properties = db()->query('SELECT id FROM property_s')->fetchAll();
    check(
        count($tables) === 1 && count($categories) === 5 && count($properties) === 7,
        'Legacy-fixture доступен и использует таблицы админки.'
    );

    testAjaxContract();
    testEmptyCategory();
    testFurnitureCategory();
    testMultipleCategories();
    testInvalidCategory();
} catch (Exception $exception) {
    $failed++;
    echo '[FAIL] Проверки прерваны исключением: ' . $exception->getMessage() . PHP_EOL;
}

echo PHP_EOL . 'Результат: ' . $passed . ' успешно, ' . $failed . ' с ошибкой.' . PHP_EOL;

exit($failed > 0 ? 1 : 0);
