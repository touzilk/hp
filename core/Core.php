<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 18:36
 */

require(__DIR__ . '/Application.php');

class Core extends \core\Application
{
    public static $app;
}

spl_autoload_register(['Core','autoload'], true, true);
