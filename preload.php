<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/.env.local.php';

$listClasses = include __DIR__ . '/vendor/composer/autoload_classmap.php';
foreach ($listClasses as $class) {
    opcache_compile_file($class);
}