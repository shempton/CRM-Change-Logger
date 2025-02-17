<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

// Подключаем модуль Highload-блоков
Loader::includeModule('highloadblock');

// Получаем ID Highload-блока
$hlblock = HL\HighloadBlockTable::getList(['filter' => ['=NAME' => 'CRMChangeLog']])->fetch();
if (!$hlblock) {
    echo 'Highload-блок не найден!';
    return;
}

// Получаем класс для работы с данными Highload-блока
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityDataClass = $entity->getDataClass();


// Фильтры (если переданы в запросе)
$filter = [];
if (!empty($_GET['USER_ID'])) {
    $filter['=UF_USER_ID'] = (int)$_GET['USER_ID'];
}
if (!empty($_GET['DATE_FROM'])) {
    $filter['>=UF_DATE'] = new Bitrix\Main\Type\DateTime(date('d.m.Y', strtotime($_GET['DATE_FROM'])));
}
if (!empty($_GET['DATE_TO'])) {
    $filter['<=UF_DATE'] = new Bitrix\Main\Type\DateTime(date('d.m.Y', strtotime($_GET['DATE_TO'])));
}

// Получаем данные из Highload-блока
$logs = $entityDataClass::getList([
    'select' => ['*'],
    'filter' => $filter,
    'order' => ['UF_DATE' => 'DESC'],
]);

// Выводим форму фильтрации
echo '<h1>Логи изменений CRM</h1>';
echo '<form method="GET" action="">
    <label>Пользователь:</label>
    <input type="text" name="USER_ID" value="' . htmlspecialchars($_GET['USER_ID'] ?? '') . '">

    <label>Дата от:</label>
    <input type="date" name="DATE_FROM" value="' . htmlspecialchars($_GET['DATE_FROM'] ?? '') . '">

    <label>Дата до:</label>
    <input type="date" name="DATE_TO" value="' . htmlspecialchars($_GET['DATE_TO'] ?? '') . '">

    <button type="submit">Фильтровать</button>
</form>';

// Выводим таблицу с логами
echo '<table border="1" cellpadding="10" cellspacing="0">';
echo '<tr>
        <th>Пользователь</th>
        <th>Сущность</th>
        <th>ID элемента</th>
        <th>Изменения</th>
        <th>Дата</th>
      </tr>';

while ($log = $logs->fetch()) {
    echo '<tr>';
    echo '<td>' . $log['UF_USER_ID'] . '</td>';
    echo '<td>' . $log['UF_ENTITY_ID'] . '</td>';
    echo '<td>' . $log['UF_ELEMENT_ID'] . '</td>';
    echo '<td><pre>' . print_r(json_decode($log['UF_CHANGE_LOG'], true), true) . '</pre></td>';
    echo '<td>' . $log['UF_DATE']->toString() . '</td>';
    echo '</tr>';
}
echo '</table>';

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>