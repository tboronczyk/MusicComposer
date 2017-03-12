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

$settings = require '../settings.php';
error_reporting($settings['php.error_reporting']);
ini_set('display_errors', $settings['php.display_errors']);
date_default_timezone_set($settings['php.default_timezone']);

$container = new Container(['settings' => $settings]);
require_once '../container.php';

$app = new App($container);
$app->view = new Twig($container['settings']['path.templates']);

$app->get('/', function ($req, $resp, $args) use ($app) {
    return $app->view->render($resp, 'index.html', []);
});

$app->post('/', function ($req, $resp, $args) use ($app) {
    $data = $req->getParsedBody();
    $start = $data['start'];
    $count = $data['count'];

    $c = $app->getContainer();
    $composer = $c['composer'];
    $melody = $composer->compose($start, $count);

    return $resp->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['melody' => $melody]));
});

$app->get('/midi/{data}', function ($req, $resp, $args) use ($app) {
    $data = explode(',', $args['data']);

    $c = $app->getContainer();
    $writer = $c['midiwriter'];
    $midi = $writer->write($data);

    return $resp->withHeader('Content-Type', 'application/x-mid')
        ->withHeader('Content-Disposition', 'attachment; filename="melody.mid"')
        ->write($midi);
});

$app->post('/vote/{data}', function ($req, $resp, $args) use ($app) {
    $data = $req->getParsedBody();
    $vote = $data['vote'];
    $data = explode(',', $args['data']);

    $c = $app->getContainer();
    $composer = $c['composer'];

    if ($vote == 'Y') {
        $composer->tally($data);
        file_put_contents($c['settings']['path.data'], $composer->toJson());
    }
});

$app->run();
