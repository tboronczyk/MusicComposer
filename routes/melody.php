<?php
use Zaemis\Composer\Melody;
use Zaemis\Composer\Midi;

$app->get(
    '/melody',
    function () use ($c) {
        $c['template']->fetch('melody.html');
    }
);

$app->post(
    '/melody',
    function () use ($c) {
        $app  = $c['app'];

        $req  = $app->request();
        $start = $req->post('start');
        $count = $req->post('count');

        $mComposer = $c['melody'];
        $melody = $mComposer->compose($start, $count);

        $resp = $app->response();
        $resp['Content-Type'] = 'application/json';
        $resp->body(
            json_encode(array('melody' => $melody))
        );
    }
);

$app->get(
    '/melody/midi/:data',
    function ($data) use ($c) {
        $data = explode('.', $data);

        $midi = new Midi();
        $midFile = $midi->generate($data);

        $resp = $c['app']->response();
        $resp['Content-Type'] = 'application/x-midi';
        $resp['Content-Disposition'] = 'attachment; filename="melody.mid"';
        $resp->body($midFile);
    }
);

$app->post(
    '/melody/vote/:data',
    function ($data) use ($c) {
        $data = explode('.', $data);
        $vote = $c['app']->request()->post('vote');

        $mComposer = $c['melody'];
        if ($vote == 'Y') {
            $mComposer->train($data);
            $json = $mComposer->toJSON();
            $db = $c['db'];
            $db->training_data('id', 1)->update(array('data' => $json));
        }
    }
);
