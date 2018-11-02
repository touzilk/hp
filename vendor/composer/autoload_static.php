<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1b241f4cc1285e669b8b1b771663bcbc
{
    public static $files = array (
        'd7f4f7522f962c095f835c50e6136087' => __DIR__ . '/..' . '/hprose/hprose/src/init.php',
    );

    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Medoo\\' => 6,
        ),
        'H' => 
        array (
            'Hprose\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Medoo\\' => 
        array (
            0 => __DIR__ . '/..' . '/catfan/medoo/src',
        ),
        'Hprose\\' => 
        array (
            0 => __DIR__ . '/..' . '/hprose/hprose/src/Hprose',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1b241f4cc1285e669b8b1b771663bcbc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1b241f4cc1285e669b8b1b771663bcbc::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
