<?php
use Zaemis\Composer\Melody;
use Zaemis\Composer\Midi;

$app->get(
    '/melody',
    function () use ($container) {
        $template = $container['template'];
        $template->fetch('melody.html');
    }
);

$app->post(
    '/melody',
    function () use ($container) {
        $db = $container['db'];
        $app  = $container['app'];
        $req  = $app->request();
        $resp = $app->response();

        $start = $req->post('start');
        $count = $req->post('count');

        $mComposer = $container['melody'];
        $melody = $mComposer->compose($start, $count);

        $resp['Content-Type'] = 'application/json';
        $resp->body(
            json_encode(array('melody' => $melody))
        );
    }
);

$app->get(
    '/melody/midi/:data',
    function ($data) use ($container) {
        $app  = $container['app'];
        $resp = $app->response();

        $data = explode('.', $data);

        $midi = new Midi();
        $midFile = $midi->generate($data);

        $resp['Content-Type'] = 'application/x-midi';
        $resp['Content-Disposition'] = 'attachment; filename="melody.mid"';
        $resp->body($midFile);
    }
);

$app->post(
    '/melody/vote/:data',
    function ($data) use ($container) {
        $db = $container['db'];
        $app  = $container['app'];
        $req  = $app->request();
        $resp = $app->response();

        $vote = $req->post('vote');
        $data = explode('.', $data);

        $mComposer = $container['melody'];
        if ($vote == 'Y') {
            $mComposer->train($data);
            $json = $mComposer->toJSON();
            $db->training_data('id', 1)->update(array('data' => $json));
        }
    }
);
