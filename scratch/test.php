<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
$router = $app->make('router');
print_r(array_keys($router->getMiddlewareGroups()));
print_r(array_keys($router->getMiddleware()));
