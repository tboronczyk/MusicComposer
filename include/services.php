<?php
use Slim\Slim;
use Zaemis\Composer\Melody;
use Zaemis\Composer\Midi;

$c = new Pimple();

$c['config'] = require dirname(__FILE__) . '/../config/config.php';

$c['app'] = $c->share(function ($c) {
    return new Slim();
});

$c['melody'] = function ($c) {
    $datafile = $c['config']['path.datafile'];

    $mComposer = new Melody();
    if (file_exists($datafile)) {
        $mComposer->fromJSON(file_get_contents($datafile));
    }
    else {
        foreach (glob($c['config']['path.training'] . '*.txt') as $f) {
            $data = trim(file_get_contents($f));
            $data = explode(' ', $data);
            $mComposer->train($data);
        }
        file_put_contents($datafile, $mComposer->toJSON());
    }
    return $mComposer;
};

$c['midi'] = function ($c) {
    return new Midi();
};

return $c;
