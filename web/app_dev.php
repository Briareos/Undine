<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

$httpAuth = function () {
    $users = [
        'fox' => '$2y$05$Jh0v7.l8p07C.DsERRuf9uiNNGloZ.3mihf3dw7huPaHIajFsigjS',
    ];
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        goto auth;
    }
    if (!array_key_exists($_SERVER['PHP_AUTH_USER'], $users)) {
        goto auth;
    }
    if (!password_verify($_SERVER['PHP_AUTH_PW'], $users[$_SERVER['PHP_AUTH_USER']])) {
        goto auth;
    }
    return;
    auth:
    header('WWW-Authenticate: Basic realm="Undine Dev"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You are not allowed to access this file. Check ', basename(__FILE__), ' for more information.';
    exit;
};

//if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '10.0.2.2'])) {
    $httpAuth();
//}
unset($httpAuth);

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/../app/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
if (!isset($_REQUEST['XDEBUG_SESSION'])) {
    $kernel->loadClassCache();
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
