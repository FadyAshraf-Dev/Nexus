<?php
// classes/config.php

// 1. Get the protocol (http or https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// 2. Get the host name (e.g., localhost)
$host = $_SERVER['HTTP_HOST'];

// 3. Define the project root directory URL dynamically
// Adjust 'nexus' if your root folder name changes
define('BASE_URL', $protocol . $host . '/nexus/');
define('ADMIN_URL', BASE_URL . 'admin/');