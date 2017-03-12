<?php
if (PHP_SAPI == 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = realpath(__DIR__ . $path);
    if ($path !== false && is_file($path) && strpos($path, __DIR__) === 0) {
        return false;
    }
}

chdir(__DIR__);
require_once '../vendor/autoload.php';

use Slim\App;
use Slim\Container;
use Slim\Views\Twig;
use Zaemis\MusicComposer\Composer;
use Zaemis\MusicComposer\MidiWriter;

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

$app->get('/', function ($req, $resp, $args) {
    return $this->Twig->render($resp, 'index.html', []);
});

$app->post('/', function ($req, $resp, $args) {
    $data = $req->getParsedBody();
    $start = $data['start'];
    $count = $data['count'];

    $melody = $this->Composer->compose($start, $count);

    return $resp->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['melody' => $melody]));
});

$app->get('/midi/{data}', function ($req, $resp, $args) {
    $data = explode(',', $args['data']);

    $midi = $this->MidiWriter->write($data);

    return $resp->withHeader('Content-Type', 'application/x-mid')
        ->withHeader('Content-Disposition', 'attachment; filename="melody.mid"')
        ->write($midi);
});

$app->post('/vote/{data}', function ($req, $resp, $args) {
    $data = $req->getParsedBody();
    $vote = $data['vote'];
    $data = explode(',', $args['data']);

    if ($vote == 'Y') {
        $composer = $this->Composer;
        $composer->tally($data);
        file_put_contents($this->settings['path.data'], $composer->toJson());
    }
});

$app->run();
