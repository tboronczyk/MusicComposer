<?php
$basedir = dirname(__FILE__) . '/../';

return array(
    'env.error_reporting' => E_ALL|E_STRICT,
    'env.display_errors'  => true,

    'db.driver'           => 'sqlite',
    'db.filename'         => $basedir . 'db/data.db',

    'path.training'       => $basedir . 'data/',
    'path.templates'      => $basedir . 'templates/',
    'path.routes'         => $basedir . 'routes/'
);
