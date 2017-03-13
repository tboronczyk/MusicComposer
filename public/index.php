<?php
declare(strict_types=1);

if (PHP_SAPI == 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = realpath(__DIR__ . $path);
    if ($path !== false && is_file($path) && strpos($path, __DIR__) === 0) {
        return false;
    }
}

use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Zaemis\MusicComposer\Composer;
use Zaemis\MusicComposer\MidiWriter;

chdir(__DIR__);
require_once '../vendor/autoload.php';

$settings = [
    // framework settings
    'displayErrorDetails' => true,

    // application paths
    'path.data' => '../data/data.json',
    'path.templates' => '../templates',
    'path.training' => '../training'
];

$c = new Container(['settings' => $settings]);

$c['Composer'] = function ($c) {
    $composer = new Composer;
    if (is_readable($c['settings']['path.data'])) {
        $pitches = file_get_contents($c['settings']['path.data']);
        $composer->fromJson($pitches);
    } else {
        foreach (glob($c['settings']['path.training'] . '/*.txt') as $f) {
            $pitches = file_get_contents($f);
            $pitches = array_filter(preg_split('|[\s]+|', $pitches));
            $composer->tally($pitches);
        }
        file_put_contents($c['settings']['path.data'], $composer->toJson());
    }
    return $composer;
};

$c['MidiWriter'] = function ($c) {
    return new MidiWriter;
};

$c['Twig'] = function ($c) {
    return new Twig($c['settings']['path.templates']);
};

$app = new App($c);

$app->get('/', function (Request $req, Response $resp, array $args) {
    return $this->Twig->render($resp, 'index.html', []);
});

$app->post('/', function (Request $req, Response $resp, array $args) {
    $data = $req->getParsedBody();
    settype($data['count'], 'int');
    $melody = $this->Composer->compose($data['start'], $data['count']);

    return $resp
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['melody' => $melody]));
});

$app->get('/midi/{melody}', function (Request $req, Response $resp, array $args) {
    $melody = explode(',', $args['melody']);
    $midi = $this->MidiWriter->write($melody);

    return $resp
        ->withHeader('Content-Type', 'application/x-mid')
        ->withHeader('Content-Disposition', 'attachment; filename="melody.mid"')
        ->write($midi);
});

$app->post('/vote/{melody}', function (Request $req, Response $resp, array $args) {
    $data = $req->getParsedBody();

    if ($data['vote'] == 'Y') {
        $melody = explode(',', $args['melody']);

        $composer = $this->Composer;
        $composer->tally($melody);

        file_put_contents($this->settings['path.data'], $composer->toJson());
    }
});

$app->run();
