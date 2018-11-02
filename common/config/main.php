<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 10:26
 */

return [
    'id' => 'app-holter',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'controllers',
    'db'=>[
        'class' => 'Medoo\Medoo',
        'database_type' => 'mysql',
        'database_name' => 'holter_online',
        'server' => '106.3.133.117',
        'username' => 'root',
        'password' => 'cloud@2016',
        'charset' => 'utf8',
        'port' => 3699,
        'prefix' => 'ho_',
    ],
];

