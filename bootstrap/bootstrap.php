<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/bootstrap/autoload.php';

Config::load();
Session::start();