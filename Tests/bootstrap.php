<?php

if (file_exists($file = __DIR__.'/autoload.php') or file_exists($file = __DIR__.'/autoload.php.dist')) {
    require_once $file;
}
