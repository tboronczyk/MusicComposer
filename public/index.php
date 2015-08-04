<?php
require '../vendor/autoload.php';
use Boronczyk\MusicComposer\Composer;
use Boronczyk\MusicComposer\MidiGenerator;

$app = new \Slim\Slim([
    'templates.path' => realpath('../templates')
]);
$app->view(new \Slim\Views\Twig());
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

$app->container->singleton('composer', function () {
    $c = new Composer();
    $file = '../data/training.json';
    if (is_readable($file)) {
        $c->fromJson(file_get_contents($file));
    } else {
        foreach (glob('../training/*.txt') as $f) {
            $data = trim(file_get_contents($f));
            $data = explode(' ', $data);
            $c->train($data);
        }
        file_put_contents($file, $c->toJson());
    }
    return $c;
});

$app->get('/', function () use ($app) {
$app->container['composer'];
    $app->render('index.html');
});

$app->post('/', function () use ($app) {
    $req = $app->request();
    $start = $req->post('start');
    $count = $req->post('count');

    $c = $app->container['composer'];
    $melody = $c->compose($start, $count);

    $resp = $app->response();
    $resp['Content-type'] = 'application/json';
    $resp->body(json_encode(['melody' => $melody]));
});

$app->get('/midi/:data', function ($data) use ($app) {
    $data = explode('.', $data);

    $mg = new MidiGenerator;
    $midi = $mg->generate($data);

    $resp = $app->response();
    $resp['Content-Type'] = 'application/x-midi';
    $resp['Content-Disposition'] = 'attachment; filename="melody.mid"';
    $resp->body($midi);
});

$app->post('/vote/:data', function ($data) use ($app) {
    $req = $app->request();
    $vote = $req->post('vote');
    $data = explode('.', $data);

    $c = $app->container['composer'];
    if ($vote == 'Y') {
        $c->train($data);
        file_put_contents('../data/training.json', $c->toJson());
    }
});

$app->run();
