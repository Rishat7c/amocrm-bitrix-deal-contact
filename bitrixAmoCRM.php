<?php
/**
 * Created by PhpStorm.
 * User: Gaysin.R
 * Date: 23.04.2019
 * Time: 11:18
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class bitrixAmoCRM
{

    // Данные
    public $responsible_user_id =   3435217; //id ответственного по сделке, контакту, компании

    public $lead_name           =   'Заявка с сайта'; //Название добавляемой сделки
    public $lead_status_id      =   '11331793'; //id этапа продаж, куда помещать сделку

    public $contact_name        =   "Ivanov Ivan Ivanovich"; //Название добавляемого контакта
    public $contact_phone       =   "89999999999"; //Телефон контакта
    public $contact_email       =   "admin@mail.ru"; //Емейл контакта

    public $user = array(
        'USER_LOGIN'            =>  '#email#', // Ваш email адрес от amocrm.ru
        'USER_HASH'             =>  '#token#' // // Ваш API токен от amocrm.ru
    );

    public $subdomain           =   '#subdomain#'; // Ваш subdomain от amocrm.ru (Пример: subdomain.amocrm.ru)
    public $sFields             =   array();
    public $lead_id             =   null;

    // TODO: function __construct($contact_name = "Ivanov Ivan Ivanovich", $contact_phone = "89999999999", $contact_email = "admin@mail.ru")
    function __construct()
    {

        // Формируем ссылку для запроса
        $link='https://'.$this->subdomain.'.amocrm.ru/private/api/auth.php?type=json';
        $curl=curl_init(); // Сохраняем дескриптор сеанса cURL

        // Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        curl_setopt($curl,CURLOPT_URL,$link);
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($this->user));
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__ .'/cookie.txt');
        curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__ .'/cookie.txt');
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

        $out=curl_exec($curl); // Инициируем запрос к API и сохраняем ответ в переменную
        $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); // Получим HTTP-код ответа сервера
        curl_close($curl);  // Завершаем сеанс cURL
        $Response=json_decode($out,true);

        // Данные аккаунта
        $link='https://'.$this->subdomain.'.amocrm.ru/private/api/v2/json/accounts/current'; // $subdomain уже объявляли выше
        $curl=curl_init(); // Сохраняем дескриптор сеанса cURL
        // Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        curl_setopt($curl,CURLOPT_URL,$link);
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__ .'/cookie.txt');
        curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__ .'/cookie.txt');
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
        $out=curl_exec($curl); // Инициируем запрос к API и сохраняем ответ в переменную
        $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);
        $Response=json_decode($out,true);
        $account=$Response['response']['account'];

        // Существующие поля
        $amoAllFields = $account['custom_fields']; //Все поля
        $amoConactsFields = $account['custom_fields']['contacts']; //Поля контактов

        //Стандартные поля амо:
        $this->sFields = array_flip(
            array(
                'PHONE',    // Телефон. Варианты: WORK, WORKDD, MOB, FAX, HOME, OTHER
                'EMAIL'     // Email. Варианты: WORK, PRIV, OTHER
            )
        );

        // Проставляем id этих полей из базы амо
        foreach($amoConactsFields as $afield) {

            if(isset($this->sFields[$afield['code']]))
                $this->sFields[$afield['code']] = $afield['id'];

        }

        $this->addDeal();
        $this->addContact();

    }

    function addDeal()
    {
        // Добавляем сделку
        $leads['request']['leads']['add']=array(
            array(
                'name'                  => $this->lead_name,
                'status_id'             => $this->lead_status_id, //id статуса
                'responsible_user_id'   => $this->responsible_user_id, //id ответственного по сделке
                //'date_create'=>1298904164, //optional
                //'price'=>300000,
                //'tags' => 'Important, USA', #Теги
                //'custom_fields'=>array()
            )
        );

        $link='https://'.$this->subdomain.'.amocrm.ru/private/api/v2/json/leads/set';
        $curl=curl_init(); // Сохраняем дескриптор сеанса cURL
        // Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        curl_setopt($curl,CURLOPT_URL,$link);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt');
        curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt');
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
        $out=curl_exec($curl); // Инициируем запрос к API и сохраняем ответ в переменную
        $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
        $Response=json_decode($out,true);

        if(is_array($Response['response']['leads']['add'])) {
            foreach ($Response['response']['leads']['add'] as $lead) {
                $this->lead_id = $lead["id"]; //id новой сделки
            };
        }
    }

    function addContact()
    {

        // Добавление контакта
        $contact = array(
            'name' => $this->contact_name,
            'linked_leads_id' => array($this->lead_id), //id сделки
            'responsible_user_id' => $this->responsible_user_id, //id ответственного
            'custom_fields'=>array(
                array(
                    'id' => $this->sFields['PHONE'],
                    'values' => array(
                        array(
                            'value' => $this->contact_phone,
                            'enum' => 'MOB'
                        )
                    )
                ),
                array(
                    'id' => $this->sFields['EMAIL'],
                    'values' => array(
                        array(
                            'value' => $this->contact_email,
                            'enum' => 'WORK'
                        )
                    )
                )
            )
        );
        $set['request']['contacts']['add'][]=$contact;

        // Формируем ссылку для запроса
        $link='https://'.$this->subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
        $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
        // Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        curl_setopt($curl,CURLOPT_URL,$link);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($set));
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt');
        curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt');
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
        $out=curl_exec($curl); // Инициируем запрос к API и сохраняем ответ в переменную
        $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

        $Response=json_decode($out,true);

    }

}