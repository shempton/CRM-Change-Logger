<?php
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Type\DateTime;

Loader::includeModule('highloadblock');

$hlblock = HL\HighloadBlockTable::getList(['filter' => ['=NAME' => 'CRMChangeLog']])->fetch();
if (!$hlblock) return;

$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityDataClass = $entity->getDataClass();

$date = new DateTime();
$date->add('-1 month');

$entityDataClass::getList([
    'filter' => ['<UF_DATE' => $date],
])->fetchAll();

return 'crm_log_cleaner.php';