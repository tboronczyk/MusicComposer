<?php
use Slim\Slim;
use Zaemis\Template;
use Zaemis\Composer\Melody;
use Zaemis\Composer\Midi;

$c = new Pimple();

$c['config'] = require dirname(__FILE__) . '/../config/config.php';

$c['app'] = $c->share(function ($c) {
    return new Slim();
});

$c['db.pdo'] = function ($c) {
    $cfg = $c['config'];
    $pdo = new PDO($cfg['db.driver'] . ':' . $cfg['db.filename']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};
$c['db'] = function ($c) {
    return new NotORM($c['db.pdo']);
};

$c['melody'] = function ($c) {
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

$c['midi'] = function ($c) {
    return new Midi();
};

$c['template'] = function ($c) {
    return new Template($c['config']['path.templates']);
};

return $c;
