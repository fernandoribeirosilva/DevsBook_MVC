<?php

namespace src\controllers;

use core\Controller;
use src\handlers\UserHandler;
use src\handlers\PostHandler;

class ProfileController extends Controller {

  private $loggedUser;

  public function __construct() {
    $this->loggedUser = UserHandler::checkLogin();
    if ($this->loggedUser === false) {
      $this->redirect('/login');
    }
  }

  public function index($args = []) {
    $page = intval(filter_input(INPUT_GET, 'page'));

    // Detectando o usuário acessado
    $id = $this->loggedUser->id; // id do user logado
    if (!empty($args['id'])) { // se tiver e não for vazil
      $id = $args['id']; // id de outra pessoa
    }

    // Pegando informações do usuário
    $user = UserHandler::getUser($id, true);
    if(!$user) {
      $this->redirect('/');
    }

    // calcular quantos anos tem 
    $dateFrom = new \DateTime($user->birthdate);// usa a data do banco de dados
    $dateTo = new \DateTime('today');// vai pegar a data de hoje
    $user->ageYears = $dateFrom->diff($dateTo)->y;// diff($dateTo) pegar a diferença; e com y no final pegar o ano

    // pegar informações expecificas dos post do usuario
    $feed = PostHandler::getUserFedd(
      $id,
      $page,
      $this->loggedUser->id
    );

    // verificar se Eu sigo o usuário
    $isFollowing = false;
    if($user->id != $this->loggedUser->id) {  // usuario logado,  usuário que estou acessado
      $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
    }

    $this->render('profile', [
      'loggedUser' => $this->loggedUser,
      'user' => $user,
      'feed' => $feed,
      'isFollowing' => $isFollowing
    ]);
  }

  public function follow($args) {
    $to = intval($args['id']);

    if(UserHandler::idExists($to)) {
      if(UserHandler::isFollowing($this->loggedUser->id, $to)) {
        // deichar de seguir
        UserHandler::unfollow($this->loggedUser->id, $to);
      } else {
        // seguir
        UserHandler::follow($this->loggedUser->id, $to);
      }
    }
    $this->redirect('/perfil/'.$to);
  }

  public function friends($args = []) {
    // Detectando o usuário acessado
    $id = $this->loggedUser->id; // id do user logado
    if (!empty($args['id'])) { // se tiver e não for vazil
      $id = $args['id']; // id de outra pessoa
    }

    // Pegando informações do usuário
    $user = UserHandler::getUser($id, true);
    if(!$user) {
      $this->redirect('/');
    }

    // calcular quantos anos tem 
    $dateFrom = new \DateTime($user->birthdate);// usa a data do banco de dados
    $dateTo = new \DateTime('today');// vai pegar a data de hoje
    $user->ageYears = $dateFrom->diff($dateTo)->y; // diff($dateTo) pegar a diferença; e com y no final pegar o ano

    // verificar se Eu sigo o usuário
    $isFollowing = false;
    if ($user->id != $this->loggedUser->id) {  // usuario logado,  usuário que estou acessado
      $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
    }

    $this->render('profile_friends', [
      'loggedUser' => $this->loggedUser,
      'user' => $user,
      'isFollowing' => $isFollowing
    ]);

  }

  public function photos($args = []) {
    // Detectando o usuário acessado
    $id = $this->loggedUser->id; // id do user logado
    if (!empty($args['id'])) { // se tiver e não for vazil
      $id = $args['id']; // id de outra pessoa
    }

    // Pegando informações do usuário
    $user = UserHandler::getUser($id, true);
    if(!$user) {
      $this->redirect('/');
    }

    // calcular quantos anos tem 
    $dateFrom = new \DateTime($user->birthdate);// usa a data do banco de dados
    $dateTo = new \DateTime('today');// vai pegar a data de hoje
    $user->ageYears = $dateFrom->diff($dateTo)->y; // diff($dateTo) pegar a diferença; e com y no final pegar o ano

    // verificar se Eu sigo o usuário
    $isFollowing = false;
    if ($user->id != $this->loggedUser->id) {  // usuario logado,  usuário que estou acessado
      $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
    }

    $this->render('profile_photos', [
      'loggedUser' => $this->loggedUser,
      'user' => $user,
      'isFollowing' => $isFollowing
    ]);

  }
}
