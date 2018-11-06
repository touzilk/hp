<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 15:59
 */
namespace controllers;

use Hprose\InvokeSettings;
use Hprose\ResultMode;
use Hprose\Client;
require(__DIR__ . '/../vendor/autoload.php');

$client = Client::create('tcp://127.0.0.1:2628', false);
//$client->timeout = 2000;
$client->onError = 'throwError';

function throwError($name, $e)
{
    var_dump($name);
}

;

$result = $client->check_exam(1, new InvokeSettings(array('mode' => ResultMode::Normal)));
print_r($result);
//echo $result;

/* $client->subscribe('time', function ($date) {
     echo $date;
 });*/
