<?php

// App entry point for all the requests used for WCGI

/* HACKS for PHP-CGI WASI */
require_once __DIR__ . '/hacks.php';
/* END HACKS */

define('SERVE_STATIC', true);

$path_info = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";
$requested_path_local = dirname(__FILE__) . $path_info;

if (SERVE_STATIC) {
    if (is_dir($requested_path_local)) {
        $requested_path_local_with_index = $requested_path_local . "index.php";
        if (is_file($requested_path_local_with_index)) {
            $requested_path_local = $requested_path_local_with_index;
        }
    }
    if (is_file($requested_path_local)) {
        // if requested file is'nt a php file
        if (!preg_match('/\.php$/', $requested_path_local)) {
            serve_static_file($requested_path_local);
        } else {
            // if requested file is php, include it
            include_once $requested_path_local;
            exit(0);
        }
    }
}

if (!preg_match('/\.(css|txt|js|png|jpg|jpeg|json|htm|html|csv)$/', $requested_path_local)) {
    require __DIR__ . '/index.php';
}

