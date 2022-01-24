<?php

namespace src\controllers;

use core\Controller;
use src\handlers\UserHandler;
use src\handlers\PostHandler;

class HomeController extends Controller {

  private $loggedUser;

  public function __construct() {
    $this->loggedUser = UserHandler::checkLogin();
    if ($this->loggedUser === false) {
      $this->redirect('/login');
    }
  }

  public function index() {
    // pegando pagina atual 
    $page = intval(filter_input(INPUT_GET, 'page'));

    $feed = PostHandler::getHomeFeed(
      $this->loggedUser->id,
      $page
    ); // manda a pessoa que sesta logada, para pegar as postagem da pessoas que eu sigo

    $this->render('home', [
      'loggedUser' => $this->loggedUser, // passando informações do usuario logado
      'feed' => $feed
    ]);
  }
}
