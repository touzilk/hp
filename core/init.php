<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 18:36
 */

require(__DIR__ . '/Application.php');

class init extends \core\Application
{
}

spl_autoload_register(['init','autoload'], true, true);
