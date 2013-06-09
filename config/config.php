<?php
return array(
    'env.error_reporting' => E_ALL|E_STRICT,
    'env.display_errors'  => true,

    'db.driver'           => 'sqlite',
    'db.filename'         => dirname(__FILE__) . '/../db/data.db'
);
