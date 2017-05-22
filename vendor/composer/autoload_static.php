<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit677cc324ff130472137839404f055e8d
{
    public static $files = array (
        '2c102faa651ef8ea5874edb585946bce' => __DIR__ . '/..' . '/swiftmailer/swiftmailer/lib/swift_required.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Phalcon\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Phalcon\\' => 
        array (
            0 => __DIR__ . '/..' . '/phalcon/incubator/Library/Phalcon',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit677cc324ff130472137839404f055e8d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit677cc324ff130472137839404f055e8d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}