<?php
$basedir = dirname(__FILE__) . '/../';

return array(
    'env.error_reporting' => E_ALL|E_STRICT,
    'env.display_errors'  => true,

    'path.datafile'       => $basedir . 'data/data.txt',
    'path.training'       => $basedir . 'data/training/',
    'path.templates'      => $basedir . 'templates/',
    'path.routes'         => $basedir . 'routes/'
);
