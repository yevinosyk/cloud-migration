<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Form\FormRenderer;
use Keywords\Controllers\NodesController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->register(new DoctrineServiceProvider(), array (
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        // 'path' => __DIR__ . '/../database/cloud-migration.db'
        'path' => '/var/tmp/db'
    ),
));


$app->register(new ServiceControllerServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), array('translator.domains' => array()));

$app['debug'] = true;

$app['keywords.controller'] = function() use ($app) {
    return new NodesController();
};

// gets all nodes
$app->get(
    '/node',
    'keywords.controller:getAll'
);

// gets a single node by ID
$app->get(
    '/node/{id}',
    'keywords.controller:getNode'
);

// creates a new new node
$app->post(
    '/node',
    'keywords.controller:postNode'
);

// updates a node by id
$app->put(
    '/node/{id}',
    'keywords.controller:putNode'
);

// deletes a node by id
$app->delete(
    '/node/{id}',
    'keywords.controller:deleteNode'
);

// creates a new node link to given id
$app->post(
    '/node/{id}/create_link',
    'keywords.controller:createLink'
);


$app->run();

