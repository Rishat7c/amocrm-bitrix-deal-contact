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

if($dealID = $ob->addDeal()) {

    $contact = $ob->addContact(
        "Гайсин Ришат Радисович",
        "89518950743",
        "xpan96@gmail.com",
        $dealID
    );

    if($contactID = $contact) {
        echo "Запись добавлена в AmoCRM (contact id = $contactID, deal id = $dealID)";
    }

}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>