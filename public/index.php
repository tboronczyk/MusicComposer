<?php
require_once '../vendor/autoload.php';

$c = require '../include/services.php';

$config = $c['config'];
error_reporting($config['env.error_reporting']);
ini_set("display_errors", $config['env.display_errors']);

$app = $c['app'];

$app->get(
    '/',
    function () use ($c) {
        $app = $c['app'];
        $app->redirect('/melody');
    }
);

foreach (glob($config['path.routes'] . '*php') as  $file) {
    require_once $file;
}

$app->run();
