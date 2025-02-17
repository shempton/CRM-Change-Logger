<?php

use Bitrix\Main\Loader;
Loader::includeModule("highloadblock");
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;


AddEventHandler("crm", "OnAfterCrmLeadUpdate", "saveLeadEventToHlBlock");
AddEventHandler("crm", "OnAfterCrmDealUpdate", "saveDealEventToHlBlock");
AddEventHandler("crm", "OnAfterCrmContactUpdate", "saveContactEventToHlBlock");

function saveLeadEventToHlBlock(&$arFields){
    saveChangeCrmEntity('CRM_LEAD',  $arFields);
}

function saveDealEventToHlBlock(&$arFields){
    saveChangeCrmEntity('CRM_DEAL',  $arFields);
}

function saveContactEventToHlBlock(&$arFields){
    saveChangeCrmEntity('CRM_CONTACT',  $arFields);
}


function saveChangeCrmEntity(string $entityType,  array $fields)
{
    global $USER;
    $hlbl = 2; 
    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $data = array(
        "UF_USER_ID" => $USER->GetID(),
        "UF_ENTITY_ID" =>  $entityType,
        "UF_ELEMENT_ID" =>  $fields['ID'],
        "UF_CHANGE_LOG" => \Bitrix\Main\Web\Json::encode($fields),
        "UF_DATE" => date("d.m.Y H:i:s")
    );

    $result = $entity_data_class::add($data);
}