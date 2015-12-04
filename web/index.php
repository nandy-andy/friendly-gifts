<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$app = new Silex\Application();

$app['debug'] = true;
$loggedIn = true;

// Providers
$app->register(new Silex\Provider\TwigServiceProvider(), array(
   'twig.path' => __DIR__ . '/../views',
));

// Controllers
$app->get('/', function () use ($app, $loggedIn) {
   if( $loggedIn !== true ) {
      return $app->redirect('/login');
   }

   return $app['twig']->render('dashboard.twig', [
       'name' => 'nAndy'
   ]);
});

$app->get('/login', function () use ($app) {
   return $app['twig']->render('login.twig');
});

$app->run();

