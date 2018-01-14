<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;

$app = new Silex\Application();

$app->register(new Silex\Provider\DoctrineServiceProvider(), array (
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../database/cloud-migration.db'
    ),
));

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    ['twig.path' => __DIR__ . '/../views']
);

$app->get(
    '/',
    'Keywords\\Controllers\\NodesController::getAll'
);

$app->get(
    '/node/new',
    'Keywords\\Controllers\\NodesController::create'
);

$app->get(
    '/keywords',
    'Keywords\\Controllers\\NodesController::getNewForm'
);



$app->run();

