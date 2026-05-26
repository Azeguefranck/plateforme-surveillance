<?php

// Vercel entry point — route all requests through Laravel's public/index.php

$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Serve static assets directly if they exist in public/
$publicPath = __DIR__ . '/../public' . parse_url($uri, PHP_URL_PATH);
if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    return false; // serve the static file
}

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request  = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
