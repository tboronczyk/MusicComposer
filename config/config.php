<?php
$basedir = dirname(__FILE__) . '/../';

return array(
    'env.error_reporting' => E_ALL|E_STRICT,
    'env.display_errors'  => true,

    'path.datafile'       => $basedir . 'db/data.txt',
    'path.training'       => $basedir . 'data/',
    'path.templates'      => $basedir . 'templates/',
    'path.routes'         => $basedir . 'routes/'
);
