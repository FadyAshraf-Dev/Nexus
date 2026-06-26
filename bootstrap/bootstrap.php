<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Core Infrastructure
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Session.php';
require_once __DIR__ . '/../classes/Response.php';
require_once __DIR__.'/../classes/Config.php';

/*
|--------------------------------------------------------------------------
| Security
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../classes/CSRF.php';
require_once __DIR__ . '/../classes/Gatekeeper.php';
require_once __DIR__ . '/../classes/Role.php';

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../classes/Html.php';
require_once __DIR__ . '/../classes/Asset.php';

/*
|--------------------------------------------------------------------------
| Application Bootstrap
|--------------------------------------------------------------------------
*/

Config::load();
Session::start();