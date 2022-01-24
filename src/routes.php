<?php
use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');

$router->get('/login', 'LoginController@signin');
$router->post('/login', 'LoginController@signinAction');

$router->get('/cadastro', 'LoginController@signup');
$router->post('/cadastro', 'LoginController@signupAction');

$router->post('/post/new', 'PostController@new');

$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');

//$router->get('/pesquisar');
//$route->get('/perfil');
//$route->get('/sair');
//$route->get('/amigos');
//$route->get('/fotos');
//$route->get('/config');