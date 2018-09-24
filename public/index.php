<?php
// https://www.thatyou.cn/%E4%BD%BF%E7%94%A8slim-php-mysql%E6%9E%84%E5%BB%BArestful-api/
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

header("Content-Type: text/html;application/json;image/jpeg;charset=utf-8");
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods", "PUT,POST,GET,DELETE,OPTIONS");
header("X-Powered-By", ' 3.2.1');
header("Access-Control-Allow-Credentials", "true");
header("Access-Control-Expose-Headers", "*");

require '../vendor/autoload.php';
session_start();
$settings = require '../src/settings.php';
$app = new \Slim\App($settings);

// Route and Dependencies
require '../src/homeRoutes.php';
require '../src/routes.php';
require '../src/dependencies.php';

$app->run();

