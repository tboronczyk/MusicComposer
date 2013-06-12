<?php
use Slim\Slim;
use Zaemis\Template;
use Zaemis\Composer\Melody;

$container = new Pimple();

$container['config'] = require dirname(__FILE__) . '/../config/config.php';

$container['app'] = $container->share(function ($c) {
    return new Slim();
});

$container['db.pdo'] = function ($c) {
    $cfg = $c['config'];
    $pdo = new PDO($cfg['db.driver'] . ':' . $cfg['db.filename']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};
$container['db'] = function ($c) {
    return new NotORM($c['db.pdo']);
};

$container['melody'] = function ($c) {
    $db = $c['db'];
    $mComposer = new Melody();
    if ($row = $db->training_data('id', 1)->select('data')->fetch()) {
        $row = iterator_to_array($row);
        $json = $row['data'];
    }
    if (isset($json)) {
        $mComposer->fromJSON($json);
    }
    else {
        foreach (glob($c['config']['path.training'] . '*.txt') as $f) {
            $data = trim(file_get_contents($f));
            $data = explode(' ', $data);
            $mComposer->train($data);
        }

        $json = $mComposer->toJSON();
        $db->training_data('id', 1)->insert(
            array('id' => 1, 'data' => $json)
        );
    }
    return $mComposer;
};

$container['template'] = function ($c) {
    return new Template($c['config']['path.templates']);
};

return $container;
