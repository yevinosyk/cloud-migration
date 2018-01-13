<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get(
    '/',
    'Keywords\\Controllers\\NodesController::getAll'
);

$app->get(
    '/node/new',
    'Keywords\\Controllers\\NodesController::create'
);

$app->get(
    '/node/new',
    'Keywords\\Controllers\\NodesController::getNewForm'
);

$app->register(new Silex\Provider\DoctrineServiceProvider(), array (
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => '/vagrant/APP_ROOT/silex_web/database/cloud-migration.db'
    ),
));

$app->run();

