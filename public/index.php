<?php
require_once '../vendor/autoload.php';
$container = require '../include/services.php';

$config = $container['config'];
error_reporting($config['env.error_reporting']);
ini_set("display_errors", $config['env.display_errors']);

$app = $container['app'];

$app->get(
    '/',
    function () use ($container) {
        $app = $container['app'];
        $app->redirect('/melody');
    }
);

foreach (glob($container['config']['path.routes'] . '*php') as  $file) {
    require_once $file;
}

$app->run();
