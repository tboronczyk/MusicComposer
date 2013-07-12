<?php
$app->get(
    '/melody',
    function () use ($c) {
        require $c['config']['path.templates'] . 'melody.html';
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
            json_encode(['melody' => $melody])
        );
    }
);

$app->get(
    '/melody/midi/:data',
    function ($data) use ($c) {
        $data = explode('.', $data);

        $midi = $c['midi']->generate($data);

        $resp = $c['app']->response();
        $resp['Content-Type'] = 'application/x-midi';
        $resp['Content-Disposition'] = 'attachment; filename="melody.mid"';
        $resp->body($midi);
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
            file_put_contents($c['config']['path.datafile'], $mComposer->toJSON());
        }
    }
);
