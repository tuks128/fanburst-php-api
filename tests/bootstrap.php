<?php

/**
 * @author: Martin Liprt
 * @email: tuxxx128@protonmail.com
 */

spl_autoload_register(function ($class) {
    require __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
});
