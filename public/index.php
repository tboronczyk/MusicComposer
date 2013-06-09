<?php
require_once '../vendor/autoload.php';
use Slim\Slim;
use Zaemis\Template;

$config = require '../config/config.php';
error_reporting($config['env.error_reporting']);
ini_set("display_errors", $config['env.display_errors']);

$container = new Pimple();

$container['config'] = $config;
$container['db.pdo'] = function ($c) {
    $cfg = $c['config'];
    $pdo = new PDO($cfg['db.driver'] . ':' . $cfg['db.filename']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};
$container['db'] = function ($c) {
    return new NotORM($c['db.pdo']);
};

$app = new Slim();
$container['app'] = $app;

$template = new Template(dirname(__FILE__) . '/../templates/');
$container['template'] = $template;

$app->get(
    '/',
    function () use ($container) {
        $app = $container['app'];
        $app->redirect('/melody');
    }
);

foreach (glob('../routes/*php') as  $file) {
    require_once $file;
}

$app->run();
