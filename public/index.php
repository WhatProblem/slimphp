<?php
// https://www.thatyou.cn/%E4%BD%BF%E7%94%A8slim-php-mysql%E6%9E%84%E5%BB%BArestful-api/
require '../src/common/Util.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

define('KEY', '1gHuiop975cdashyex9Ud23ldsvm2Xq');

header("Content-Type: text/html;application/json;application/x-www-form-urlencoded;image/jpeg;charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
// header("Access-Control-Allow-Headers: Origin,X-Requested-With,Content-Type,Accept");
header("Access-Control-Allow-Methods", "PUT,POST,GET,DELETE,OPTIONS");
header("X-Powered-By", ' 3.2.1');
header("Access-Control-Allow-Credentials", "true");
header("Access-Control-Expose-Headers", "*");

require '../vendor/autoload.php';
session_start();
$settings = require '../src/settings.php';
$app = new \Slim\App($settings);

// Route and Dependencies
require '../src/routes/homeRoutes.php';
require '../src/routes/routes.php';
require '../src/dependencies.php';

$app->run();

