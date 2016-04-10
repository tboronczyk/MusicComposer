<?php
return [
    // PHP environment settings
    'php.error_reporting'  => E_ALL,
    'php.display_errors'   => true,
    'php.default_timezone' => 'America/New_York',

    // Slim framework settings
    'displayErrorDetails' => true,

    // Application paths
    'path.data' => __DIR__ . '/data/data.json',
    'path.templates' => __DIR__ . '/templates',
    'path.training'  => __DIR__ . '/training'
];
