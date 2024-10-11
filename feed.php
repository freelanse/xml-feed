<?php
ob_clean(); // Очищаем буфер вывода
header("Content-Type: text/xml; charset=UTF-8");
//создаем сниппет и вставлеям весь этот код
// Генерируем XML-заголовок
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<yml_catalog date="' . date('Y-m-d\TH:i:sO') . '">';
echo '<shop>';
echo '<name>' . htmlspecialchars($modx->getOption('site_name'), ENT_XML1, 'UTF-8') . '</name>';
echo '<company>' . htmlspecialchars($modx->getOption('site_name'), ENT_XML1, 'UTF-8') . '</company>';
echo '<url>' . htmlspecialchars($modx->getOption('site_url'), ENT_XML1, 'UTF-8') . '</url>';
echo '<currencies>';
echo '<currency id="BYN" rate="1"/>';
echo '</currencies>';
echo '<categories>';

// Получаем все категории
$categories = $modx->getCollection('modResource', [
    'parent' => 0,
    'published' => 1,
]);

foreach ($categories as $category) {
    echo '<category id="' . $category->get('id') . '">' . htmlspecialchars($category->get('pagetitle'), ENT_XML1, 'UTF-8') . '</category>';
    
    $subcategories = $modx->getCollection('modResource', [
        'parent' => $category->get('id'),
        'published' => 1,
    ]);

    foreach ($subcategories as $subcategory) {
        echo '<category id="' . $subcategory->get('id') . '" parentId="' . $category->get('id') . '">' . htmlspecialchars($subcategory->get('pagetitle'), ENT_XML1, 'UTF-8') . '</category>';
    }
}

echo '</categories>';
echo '<delivery-options>';
echo '<option cost="200" days="1"/>';
echo '</delivery-options>';
echo '<offers>';

// Получаем товары
$products = $modx->getCollection('modResource', [
    'template' => 6,
    'published' => 1,
]);

foreach ($products as $product) {
    $categoryId = $product->get('parent');
    $category = $modx->getObject('modResource', $categoryId);
    if ($category) {
        $categoryId = $category->get('id');
    }

    // Получаем цену из TV-поля
    $price = $product->getTVValue('price');

    // Проверка, является ли цена числом
    if (is_numeric($price)) {
        echo '<offer id="' . $product->get('id') . '">';
        echo '<name>' . htmlspecialchars($product->get('pagetitle'), ENT_XML1, 'UTF-8') . '</name>';
        echo '<url>' . $modx->makeUrl($product->get('id'), '', '', 'full') . '</url>';
        echo '<price>' . $price . '</price>'; // Выводим только если цена числовая
        echo '<currencyId>BYN</currencyId>';
        echo '<categoryId>' . $categoryId . '</categoryId>';
        echo '<delivery>true</delivery>';
        echo '<param name="Цвет">' . htmlspecialchars($product->getTVValue('color') ?: 'отсутствует информация', ENT_XML1, 'UTF-8') . '</param>';
        echo '<weight>' . htmlspecialchars($product->getTVValue('weight') ?: 'отсутствует информация', ENT_XML1, 'UTF-8') . '</weight>';
        echo '<dimensions>' . htmlspecialchars($product->getTVValue('size_list') ?: 'отсутствует информация', ENT_XML1, 'UTF-8') . '</dimensions>';
        echo '</offer>';
    }
}

echo '</offers>';
echo '</shop>';
echo '</yml_catalog>';
