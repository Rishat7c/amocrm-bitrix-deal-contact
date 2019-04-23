<?php
/**
 * Created by PhpStorm.
 * User: Gaysin.R
 * Date: 23.04.2019
 * Time: 11:44
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("rest AmoCrm");

// TODO: $ob = new bitrixAmoCRM("Гайсин Ришат Радисович", "89518950743", "xpan96@gmail.com");
$ob = new bitrixAmoCRM();

if($ob) {
    echo "Запись добавлена в AmoCRM";
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>