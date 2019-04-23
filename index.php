<?php
/**
 * Created by PhpStorm.
 * User: Gaysin.R
 * Date: 23.04.2019
 * Time: 11:44
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("rest AmoCrm");

$ob = new bitrixAmoCRM();

$deal = $ob->addDeal();
$contact = $ob->addContact(
    "Гайсин Ришат Радисович",
    "89518950743",
    "xpan96@gmail.com"
);

if($deal and $contact)
    echo "Запись добавлена в AmoCRM";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>