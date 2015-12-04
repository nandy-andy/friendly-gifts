<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../models/User.class.php');

$app = new Silex\Application();
$app['debug'] = true;

// Providers
$app->register(new Silex\Provider\TwigServiceProvider(), [
   'twig.path' => __DIR__ . '/../views',
]);
$app->register(new Silex\Provider\SessionServiceProvider());

use Symfony\Component\HttpFoundation\Request;
use FriendlyGifts\User;

$user = $app['session']->get('user');

// Controllers
$app->get('/', function () use ($app, $user) {
   if( null === $user ) {
      return $app->redirect('/login');
   }

   return $app['twig']->render('dashboard.twig', [
       'name' => $user->getName()
   ]);
});

$app->get('/login', function () use ($app) {
   return $app['twig']->render('login.twig', [ 'error' => '' ]);
});

$app->post('/login', function (Request $request) use ($app) {
   $user = new User\User($request->get('login'), $request->get('password'));
   $user->login();

   if( $user->isLoggedIn() ) {
      $app['session']->set('user', $user);
      return $app->redirect('/');
   }

   return $app['twig']->render('login.twig', [
      'error' => 'Nieprawidłowy login lub hasło.'
   ]);
});

$app->get('/logout', function () use ($app) {
   $app['session']->set('user', null);
   return $app->redirect('/');
});

$app->run();

