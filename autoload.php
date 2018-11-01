<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 18:36
 */

$class_map = [];

spl_autoload_register(function ($className) {


    if (isset($class_map[$className])) {
        return;
    }

    $classFile = PATH . DIRECTORY_SEPARATOR . $className . '.php';
    $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $classFile);
    $classFile = str_replace('/', DIRECTORY_SEPARATOR, $classFile);

    if ($classFile === false || !is_file($classFile)) {
        return;
    }

    if (include($classFile)) {

        $class_map[$className] = $classFile;
    }

}, true, true);
