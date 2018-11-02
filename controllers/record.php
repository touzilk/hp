<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 19:12
 */

namespace controllers;


use Core;

class record
{
    function lists(){

        $result =  Core::$app->db->select('user', '*');
        var_dump($result);die;

    }
}