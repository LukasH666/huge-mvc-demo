<?php

$publicPath = __DIR__ . '/public';

chdir($publicPath);

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$file = $publicPath . $requestPath;

if ($requestPath !== '/' && file_exists($file)) {
    return false;
}

$_GET['url'] = trim($requestPath, '/');

require $publicPath . '/index.php';