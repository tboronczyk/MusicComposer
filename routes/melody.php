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

        $mComposer = new Melody();

        if ($row = $db->training_data('id', 1)->select('data')->fetch()) {
            $row = iterator_to_array($row);
            $json = $row['data'];
        }
        if (isset($json)) {
            $mComposer->fromJSON($json);
        }
        else {
            foreach (glob('../data/*.txt') as $f) {
                $data = trim(file_get_contents($f));
                $data = explode(' ', $data);
                $mComposer->train($data);
            }

            $json = $mComposer->toJSON();
            $db->training_data('id', 1)->insert(
                array('id' => 1, 'data' => $json)
            );
        }

        $melody = $mComposer->compose($start, $count);

        $resp['Content-Type'] = 'application/json';
        $resp->body(
            json_encode(array('melody' => $melody))
        );
    }
);

$app->get(
    '/midi',
    function () use ($container) {
        $app  = $container['app'];
        $req  = $app->request();
        $resp = $app->response();

        $data = $req->get('data');
        $data = explode('.', $data);

        $midi = new Midi();
        $midFile = $midi->generate($data);

        $resp['Content-Type'] = 'application/x-midi';
        $resp['Content-Disposition'] = 'attachment; filename="melody.mid"';
        $resp->body($midFile);
    }
);

