<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/{name}', function($name) use($app) {
    return 'Hello '.$app->escape($name);
});

$app->register(new Silex\Provider\DoctrineServiceProvider(), array (
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => '/vagrant/APP_ROOT/silex_web/database/cloud-migration.db'
    ),
));

$app->run();

